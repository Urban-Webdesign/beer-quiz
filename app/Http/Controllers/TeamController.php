<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Illuminate\Support\Facades\DB;

class TeamController extends Controller
{
    // Získání všech záznamů z tabulky 'teams'
    public function index()
    {
        $teams = Team::withCount([
            'results',
            'results as victories_count' => function ($query) {
                $query->where('position', 1);
            },
	        'results as shootouts_count' => function ($query) {
		        $query->whereHas('event', function ($eventQuery) {
			        $eventQuery->where('shootout', true); // Pouze eventy, kde proběhl shootout
		        })->whereRaw('score = (SELECT MAX(score) FROM results AS r2 WHERE r2.event_id = results.event_id)');
	        }

        ])->get();

        // Řazení v PHP podle českého locales
        $teams = $teams->sort(function ($a, $b) {
            return collator_create('cs')->compare($a->name, $b->name);
        })->values(); // Resetuje klíče po seřazení

        // Znovu seřadit podle results_count a zachovat české řazení jmen
        $teams = $teams->sortByDesc('results_count')->values();

        return response()->json($teams);
    }

    // Získání všech záznamů z tabulky 'teams' podle události
    public function showTeamsByEvent($id)
    {
        return response()->json(Team::where('event_id', $id)->get());
    }


	public function show($id)
	{
		$team = Team::findOrFail($id);

		// Získání všech účastí týmu s informacemi o událostech
		$participations = DB::table('results')
			->where('team_id', $id)
			->join('events', 'results.event_id', '=', 'events.id')
			->select(
				'events.id as event_id',
				'events.date as event_date',
				'events.name as event_name',
				'results.position',
				'results.score'
			)
			->orderBy('events.date', 'DESC')
			->get();

		// Získání všech event_id, kde tým participoval
		$eventIds = $participations->pluck('event_id')->unique();

		// Získání maximálních skóre pro všechny události najednou
		$maxScores = DB::table('results')
			->whereIn('event_id', $eventIds)
			->select('event_id', DB::raw('MAX(score) as max_score'))
			->groupBy('event_id')
			->pluck('max_score', 'event_id');

		// Získání počtu týmů s maximálním skóre pro každou událost
		$teamsWithMaxScoreCounts = DB::table('results')
			->whereIn('event_id', $eventIds)
			->whereIn('score', $maxScores->values())
			->select('event_id', 'score', DB::raw('COUNT(*) as count'))
			->groupBy('event_id', 'score')
			->get()
			->groupBy('event_id');

		// Počítadla
		$totalParticipations = $participations->count();
		$victories = $participations->where('position', 1)->count();
		$shootouts = 0;

		foreach ($participations as $participation) {
			$eventId = $participation->event_id;
			$maxScore = $maxScores[$eventId] ?? 0;

			if ($participation->score == $maxScore) {
				$count = $teamsWithMaxScoreCounts[$eventId]->firstWhere('score', $maxScore)->count ?? 0;
				if ($count >= 2) {
					$shootouts++;
				}
			}
		}

		return response()->json([
			'id' => $team->id,
			'name' => $team->name,
			'stats' => [
				'total_participations' => $totalParticipations,
				'victories' => $victories,
				'shootouts' => $shootouts
			],
			'participations' => $participations->map(function($participation) {
				return [
					'event' => [
						'date' => $participation->event_date,
						'name' => $participation->event_name
					],
					'position' => $participation->position,
					'score' => $participation->score
				];
			})
		]);
	}
}
