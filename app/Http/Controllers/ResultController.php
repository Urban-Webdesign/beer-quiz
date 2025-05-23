<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Result;

class ResultController extends Controller
{
    // Získání všech záznamů z tabulky 'results' podle události
    public function showResultsByEvent($id = null)
    {

        if ($id === null) {
            $event = Event::has('results')->orderBy('date', 'desc')->first();
        } else {
            $event = Event::where('id', $id)->first();
        }

        if (! $event) {
            return response()->json(['message' => 'No events found'], 404);
        }

        // Get results for the latest event
        $results = Result::where('event_id', $event->id)
            ->join('teams', 'results.team_id', '=', 'teams.id') // Join the teams table
            ->select('results.*') // Ensure only Result fields are selected
            ->orderBy('position', 'asc') // Secondary ordering
            ->orderBy('teams.name', 'asc') // Order by team name
            ->with('team') // Keep the relationship loaded
            ->get();

        // Get previous and next event IDs
        $previousEvent = Event::has('results')->where('date', '<', $event->date)->orderBy('date', 'desc')->first();
        $nextEvent = Event::has('results')->where('date', '>', $event->date)->orderBy('date', 'asc')->first();

        return response()->json([
            'event' => $event,
            'results' => $results,
            'previous_event_id' => $previousEvent ? $previousEvent->id : null,
            'next_event_id' => $nextEvent ? $nextEvent->id : null,
        ]);
    }
}
