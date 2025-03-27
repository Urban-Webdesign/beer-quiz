<?php

namespace App\Filament\Resources\EventResource\Widgets;

use App\Models\Event;
use Filament\Widgets\Widget;

class EventRegistrationsOverview extends Widget
{
	public Event $record;

	protected static string $view = 'filament.resources.event-resource.widgets.event-registrations-overview';

	public function getTeamsCountProperty()
	{
		return $this->record->registrations()->count();
	}
}