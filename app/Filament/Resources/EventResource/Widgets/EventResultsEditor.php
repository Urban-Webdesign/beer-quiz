<?php

namespace App\Filament\Resources\EventResource\Widgets;

use App\Models\Event;
use App\Models\Result;
use App\Models\Team;
use Filament\Notifications\Notification;
use Filament\Widgets\Widget;

class EventResultsEditor extends Widget
{
    public Event $record;

    public array $rows = [];

    public bool $showShootout = false;

    public bool $showAddTeamRow = false;

    public ?int $addTeamId = null;

    public bool $isDirty = false;

    /** @var array<array{position: int, teams: array<array{team_id: int, team_name: string}>}> */
    public array $tiedGroups = [];

    /** keyed by position string => team_id */
    public array $shootoutWinners = [];

    /** team IDs that took part in a resolved shootout (for persistent 🎯 display) */
    public array $shootoutParticipantIds = [];

    protected static string $view = 'filament.resources.event-resource.widgets.event-results-editor';

    protected int | string | array $columnSpan = 'full';

    public function mount(): void
    {
        $this->loadRows();
    }

    protected function loadRows(): void
    {
        $existingResults = $this->record->results()->with('team')->get()->keyBy('team_id');

        $registeredTeamIds = $this->record->registrations()->with('team')->get()
            ->map(fn ($r) => $r->team?->id)
            ->filter()
            ->unique()
            ->all();

        $rows = [];

        // Teams from registrations
        foreach ($this->record->registrations()->with('team')->get() as $registration) {
            $team = $registration->team;
            if (! $team) {
                continue;
            }

            $result = $existingResults->get($team->id);
            $rows[] = [
                'team_id'   => $team->id,
                'team_name' => $team->name,
                'position'  => $result?->position ?? '',
                'score'     => $result?->score ?? '',
            ];
        }

        // Teams added manually (have a saved result but no registration)
        foreach ($existingResults as $result) {
            if (! $result->team || in_array($result->team_id, $registeredTeamIds)) {
                continue;
            }
            $rows[] = [
                'team_id'   => $result->team_id,
                'team_name' => $result->team->name,
                'position'  => $result->position ?? '',
                'score'     => $result->score ?? '',
            ];
        }

        usort($rows, fn ($a, $b) =>
            ($a['position'] !== '' ? (int) $a['position'] : 999) <=> ($b['position'] !== '' ? (int) $b['position'] : 999)
        );

        $this->rows = $rows;
        $this->refreshTiedGroups();
        $this->refreshShootoutParticipants();
        $this->restoreShootoutState();
    }

    /**
     * If the event has shootout=true, mark all teams that share the top score
     * as participants so the 🎯 persists after saving.
     */
    protected function refreshShootoutParticipants(): void
    {
        if (! $this->record->shootout) {
            $this->shootoutParticipantIds = [];
            return;
        }

        $firstRow = collect($this->rows)->firstWhere('position', 1);
        if (! $firstRow || $firstRow['score'] === '') {
            $this->shootoutParticipantIds = [];
            return;
        }

        $topScore = (int) $firstRow['score'];
        $this->shootoutParticipantIds = collect($this->rows)
            ->filter(fn ($r) => $r['score'] !== '' && (int) $r['score'] === $topScore)
            ->pluck('team_id')
            ->toArray();
    }

    /**
     * Livewire 3 hook — fires for every property update with the full dotted key.
     * Recalculate positions whenever any score field changes.
     */
    public function updated(string $key): void
    {
        if (str_ends_with($key, '.score')) {
            $this->isDirty = true;
            $this->recalculatePositions();
        }
    }

    /**
     * Compute positions from scores in-place (no reorder, preserves wire:model indices).
     * Teams with equal scores share the same position; the next position skips accordingly.
     */
    public function recalculatePositions(): void
    {
        // Normalize to int to avoid mixed string/int comparison issues
        // (DB returns int, wire:model returns string for the same value)
        $uniqueScores = array_values(array_unique(array_map(
            'intval',
            array_filter(array_column($this->rows, 'score'), fn ($s) => $s !== '')
        )));
        usort($uniqueScores, fn ($a, $b) => $b <=> $a);

        // Map each unique score to its shared position
        $scoreToPos = [];
        $pos = 1;
        foreach ($uniqueScores as $score) {
            $scoreToPos[$score] = $pos;
            $pos += count(array_filter($this->rows, fn ($r) => $r['score'] !== '' && (int) $r['score'] === $score));
        }

        foreach ($this->rows as &$row) {
            $row['position'] = $row['score'] !== '' ? $scoreToPos[(int) $row['score']] : '';
        }
        unset($row);

        $this->refreshTiedGroups();
    }

    /**
     * When the event already has a resolved shootout, keep the panel visible
     * with the winner pre-selected so the user can change it if needed.
     */
    protected function restoreShootoutState(): void
    {
        if (! $this->record->shootout || count($this->shootoutParticipantIds) < 2) {
            return;
        }

        // Only restore if there is no live tie already shown
        if ($this->showShootout) {
            return;
        }

        $participants = collect($this->rows)
            ->filter(fn ($r) => in_array($r['team_id'], $this->shootoutParticipantIds))
            ->values()
            ->map(fn ($r) => ['team_id' => $r['team_id'], 'team_name' => $r['team_name']])
            ->toArray();

        if (count($participants) < 2) {
            return;
        }

        $this->tiedGroups = [['position' => 1, 'teams' => $participants]];
        $this->showShootout = true;

        // Pre-select the saved winner (position 1 among participants)
        $winner = collect($this->rows)
            ->first(fn ($r) => in_array($r['team_id'], $this->shootoutParticipantIds) && (int) $r['position'] === 1);

        if ($winner) {
            $this->shootoutWinners[1] = $winner['team_id'];
        }
    }

    protected function refreshTiedGroups(): void
    {
        $byPosition = [];
        foreach ($this->rows as $row) {
            if ($row['score'] !== '' && $row['position'] !== '') {
                $byPosition[$row['position']][] = ['team_id' => $row['team_id'], 'team_name' => $row['team_name']];
            }
        }

        // Only 1st-place ties trigger Rozstřel
        $this->tiedGroups = array_values(array_filter(
            array_map(fn ($pos, $teams) => ['position' => $pos, 'teams' => $teams], array_keys($byPosition), $byPosition),
            fn ($g) => count($g['teams']) >= 2 && (int) $g['position'] === 1
        ));

        $this->showShootout = count($this->tiedGroups) > 0;

        // Clear winners for any group no longer tied
        if (! $this->showShootout) {
            $this->shootoutWinners = [];
        }
    }

    /**
     * Apply chosen winners: winner keeps the group position,
     * remaining tied teams get consecutive positions after it.
     */
    protected function applyShootoutWinners(): void
    {
        foreach ($this->shootoutWinners as $posKey => $winnerTeamId) {
            if (! $winnerTeamId) {
                continue;
            }

            $groupPos = (int) $posKey;

            $tiedIds = array_column(
                array_filter($this->rows, fn ($r) => (int) $r['position'] === $groupPos),
                'team_id'
            );

            foreach ($this->rows as &$row) {
                if (! in_array($row['team_id'], $tiedIds)) {
                    continue;
                }
                $row['position'] = $row['team_id'] == $winnerTeamId ? $groupPos : $groupPos + 1;
            }
            unset($row);
        }
    }

    public function getAvailableTeams(): array
    {
        $usedIds = array_column($this->rows, 'team_id');
        return Team::whereNotIn('id', $usedIds)->orderBy('name')->get()
            ->map(fn ($t) => ['id' => $t->id, 'name' => $t->name])
            ->toArray();
    }

    public function addTeam(): void
    {
        if (! $this->addTeamId) {
            return;
        }
        $team = Team::find($this->addTeamId);
        if (! $team) {
            return;
        }

        $this->rows[] = [
            'team_id'   => $team->id,
            'team_name' => $team->name,
            'position'  => '',
            'score'     => '',
        ];
        $this->addTeamId = null;
        $this->showAddTeamRow = false;
        $this->isDirty = true;
    }

    public function removeRow(int $index): void
    {
        $row = $this->rows[$index] ?? null;
        if (! $row) {
            return;
        }

        Result::where('event_id', $this->record->id)
            ->where('team_id', $row['team_id'])
            ->delete();

        array_splice($this->rows, $index, 1);
        $this->isDirty = true;
        $this->recalculatePositions();
    }

    public function save(): void
    {
        $this->recalculatePositions();

        // Validate that a winner is selected for every active shootout group
        foreach ($this->tiedGroups as $group) {
            if (empty($this->shootoutWinners[$group['position']])) {
                Notification::make()
                    ->title('Vyber vítěze rozstřelu')
                    ->body('Před uložením musíš vybrat vítěze rozstřelu.')
                    ->danger()
                    ->send();
                return;
            }
        }

        // Determine shootout state from current scores (before applying winners)
        $topScoreCount = count(array_filter(
            $this->rows,
            fn ($r) => $r['position'] !== '' && (int) $r['position'] === 1
        ));
        $hasShootout = $topScoreCount >= 2;

        $this->applyShootoutWinners();

        foreach ($this->rows as $row) {
            if ($row['score'] === '') {
                continue;
            }

            Result::updateOrCreate(
                ['event_id' => $this->record->id, 'team_id' => $row['team_id']],
                [
                    'position' => $row['position'] !== '' ? (int) $row['position'] : null,
                    'score'    => (int) $row['score'],
                    'order'    => $row['position'] !== '' ? (int) $row['position'] : null,
                ]
            );
        }

        // Sync shootout flag on the event
        $this->record->update(['shootout' => $hasShootout]);

        $this->isDirty = false;
        $this->loadRows();

        Notification::make()
            ->title('Výsledky uloženy')
            ->success()
            ->send();
    }
}
