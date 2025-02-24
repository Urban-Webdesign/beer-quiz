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
}
