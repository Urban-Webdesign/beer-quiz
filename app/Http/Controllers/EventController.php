<?php

namespace App\Http\Controllers;

use App\Models\Event;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::with('results.team')
            ->withCount('results as teams_count')
	        ->withCount('registrations as registrations_count')
            ->orderBy('date', 'desc')
            ->get()
            ->map(function ($event) {
                $totalPoints = $event->results->sum('score');

                $averagePoints = $event->teams_count > 0
                    ? round($totalPoints / $event->teams_count, 2)
                    : 0;

                $winningResult = $event->results->sortByDesc('score')->first();
                $winningTeamName = $winningResult ? $winningResult->team->name : null;

                $event->average_points = $averagePoints;
                $event->winning_team = $winningTeamName;

                return $event;
            });

        return response()->json($events);
    }

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
        $events = Event::query()
            ->whereHas('media', function ($query) {
                $query->where('collection_name', 'gallery');
            })
            ->with('media')
            ->orderBy('date', 'desc')
            ->get()
            ->map(function ($event) {
                $gallery = $event->getMedia('gallery');

                if ($gallery->isEmpty()) {
                    return null;
                }

                return [
                    'id' => $event->id,
                    'name' => $event->name,
                    'date' => date('j. n. Y', strtotime($event->date)),
                    'gallery' => $event->getMedia('gallery')
                        ->map(function ($media) {
                            $path = $media->getPath('original') ?? $media->getPath();

                            $width = null;
                            $height = null;

                            if ($path && @is_file($path)) {
                                [$width, $height] = @getimagesize($path) ?: [null, null];
                            }

                            return [
                                'thumb_url'     => $media->getUrl('thumb'),
                                'thumb_srcset'  => $media->getSrcset('thumb'),
                                'large_url'     => $media->getUrl('original'),
                                'large_srcset'  => $media->getSrcset('original'),
                                'width'         => $width,
                                'height'        => $height,
                            ];
                        })
                        ->values()
                        ->toArray(),
                ];
            })
            ->values();

        return response()->json($events);
    }

}
