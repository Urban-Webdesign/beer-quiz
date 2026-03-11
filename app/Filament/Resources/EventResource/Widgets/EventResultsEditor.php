<?php

namespace App\Filament\Resources\EventResource\Widgets;

use App\Models\Event;
use App\Models\Result;
use Filament\Notifications\Notification;
use Filament\Widgets\Widget;

class EventResultsEditor extends Widget
{
    public Event $record;

    public array $rows = [];

    public bool $showShootout = false;

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
        $existingResults = $this->record->results()->get()->keyBy('team_id');

        $rows = [];
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

        usort($rows, fn ($a, $b) =>
            ($a['position'] !== '' ? (int) $a['position'] : 999) <=> ($b['position'] !== '' ? (int) $b['position'] : 999)
        );

        $this->rows = $rows;
        $this->refreshTiedGroups();
        $this->refreshShootoutParticipants();
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

        $topScore = $firstRow['score'];
        $this->shootoutParticipantIds = collect($this->rows)
            ->filter(fn ($r) => (string) $r['score'] === (string) $topScore)
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
            $this->recalculatePositions();
        }
    }

    /**
     * Compute positions from scores in-place (no reorder, preserves wire:model indices).
     * Teams with equal scores share the same position; the next position skips accordingly.
     */
    public function recalculatePositions(): void
    {
        $scores = array_unique(array_filter(
            array_column($this->rows, 'score'),
            fn ($s) => $s !== ''
        ));
        usort($scores, fn ($a, $b) => (int) $b <=> (int) $a);

        // Map each unique score to its shared position
        $scoreToPos = [];
        $pos = 1;
        foreach ($scores as $score) {
            $scoreToPos[(string) $score] = $pos;
            $pos += count(array_filter($this->rows, fn ($r) => $r['score'] === $score));
        }

        foreach ($this->rows as &$row) {
            $row['position'] = $row['score'] !== '' ? $scoreToPos[(string) $row['score']] : '';
        }
        unset($row);

        $this->refreshTiedGroups();
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

            $offset = 0;
            foreach ($this->rows as &$row) {
                if (! in_array($row['team_id'], $tiedIds)) {
                    continue;
                }
                if ($row['team_id'] == $winnerTeamId) {
                    $row['position'] = $groupPos;
                } else {
                    $row['position'] = $groupPos + 1 + $offset;
                    $offset++;
                }
            }
            unset($row);
        }
    }

    public function save(): void
    {
        $this->recalculatePositions();

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

        $this->shootoutWinners = [];
        $this->loadRows();

        Notification::make()
            ->title('Výsledky uloženy')
            ->success()
            ->send();
    }
}
