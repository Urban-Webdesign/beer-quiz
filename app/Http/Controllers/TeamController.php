<?php

namespace App\Http\Controllers;

use App\Models\Team;

class TeamController extends Controller
{
    // Získání všech záznamů z tabulky 'teams'
    public function index()
    {
        $teams = Team::withCount('results')->get();

        // Řazení v PHP podle českého locale
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
