<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Registration;
use Illuminate\Http\Request;

class RegistrationController extends Controller
{
	public function register(Request $request, int $id)
	{
		$event = Event::findOrFail($id);

		$validated = $request->validate([
			'team_name' => 'required|string|max:100',
			'captain_name' => 'required|string|max:100',
			'phone' => 'required|string|max:25',
		]);

		if ($event->status !== 'register') {
			return response()->json(['message' => 'Registrace není povolena.'], 403);
		}

		// Check if team name is already taken for this event
		$existingTeam = Registration::where('event_id', $event->id)
			->where('name', $validated['team_name'])
			->first();

		if ($existingTeam) {
			return response()->json(['message' => 'Tento název týmu je již obsazen.'], 422);
		}

		$registration = Registration::create([
			'event_id' => $event->id,
			'name' => $validated['team_name'],
			'leader' => $validated['captain_name'],
			'phone' => $validated['phone'],
		]);

		return response()->json([
			'message' => 'Registrace proběhla úspěšně!',
			'registration' => $registration
		]);
	}

	public function showRegistrations(int $id)
	{
		$registrations = Registration::where('event_id', $id)->get();

		return response()->json($registrations);
	}
}
