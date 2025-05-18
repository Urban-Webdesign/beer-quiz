<?php

namespace App\Http\Controllers;

use App\Models\Event; // Model pro tabulku 'events'

class EventController extends Controller
{
    // Získání všech záznamů z tabulky 'events'
    public function index()
    {
        $events = Event::with('results.team') // Nahráváme výsledky pro práci s body
            ->withCount('results as teams_count') // Počet týmů na základě výsledků
	        ->withCount('registrations as registrations_count')
            ->orderBy('date', 'desc')
            ->get()
            ->map(function ($event) {
                // Vypočítáme součet bodů všech výsledků události
                $totalPoints = $event->results->sum('score');

                // Vypočítáme průměrné body (pokud nejsou žádné výsledky, vrátíme 0)
                $averagePoints = $event->teams_count > 0
                    ? round($totalPoints / $event->teams_count, 2)
                    : 0;

                // Najdeme vítězný tým (nejvyšší body)
                $winningResult = $event->results->sortByDesc('score')->first();
                $winningTeamName = $winningResult ? $winningResult->team->name : null;

                // Přidáme průměrné body do každé události
                $event->average_points = $averagePoints;
                $event->winning_team = $winningTeamName;

                return $event;
            });

        return response()->json($events);
    }

    // Získání konkrétního záznamu podle ID
    public function show($id)
    {
        $event = Event::find($id);

        if (! $event) {
            return response()->json(['message' => 'Event not found'], 404);
        }

        return response()->json($event);
    }

	public function upcoming()
	{
		$events = Event::where('status', '!=', 'finished')->orderBy('date')->get();

		if (!$events) {
			return response()->json(['message' => 'Events not found'], 404);
		}

		return response()->json($events);
	}

	public function next()
	{
		$event = Event::where('status', '!=', 'finished')
			->where('date', '>=', now()) // Volitelné: pouze budoucí události
			->withCount('registrations as registrations_count')
			->orderBy('date')
			->first();

		if (! $event) {
			return response()->json(['message' => 'No upcoming event found'], 404);
		}

		return response()->json($event);
	}

	public function gallery()
	{
		return Event::with(['media'])->orderBy('date', 'desc')->get()->map(function ($event) {
			return [
				'id' => $event->id,
				'name' => $event->name,
				'date' => date('j. n. Y', strtotime($event->date)),
				'gallery' => $event->getMedia('gallery')->map(function ($media) {
					[$width, $height] = getimagesize($media->getPath());

					return [
						'url' => $media->getUrl(),
						'width' => $width ?? null,
						'height' => $height ?? null,
					];
				}),
			];
		});
	}

}
