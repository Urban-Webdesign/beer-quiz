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
			'team_name' => 'required|string|max:50',
			'captain_name' => 'required|string|max:50',
			'phone' => [
				'required',
				'string',
				'max:25',
				function ($attribute, $value, $fail) {
					$cleaned = preg_replace('/\s+/', '', $value);
					if (!preg_match('/^(\+[1-9][0-9][0-9])?[1-9][0-9]{8}$/', $cleaned)) {
						$fail('Zadejte platné telefonní číslo');
					}
				},
			],
		], [
			'team_name.required' => 'Název týmu je povinný.',
			'team_name.max' => 'Název týmu může mít maximálně 50 znaků.',
			'captain_name.required' => 'Jméno kapitána je povinné.',
			'captain_name.max' => 'Jméno kapitána může mít maximálně 50 znaků.',
			'phone.required' => 'Telefonní číslo je povinné.',
		]);

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
			'phone' => preg_replace('/\s+/', '', $validated['phone']),
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
