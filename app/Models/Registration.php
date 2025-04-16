<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Registration extends Model
{
    protected $fillable = ['event_id', 'name', 'leader', 'phone'];

	public function event()
	{
		return $this->belongsTo(Event::class, 'event_id');
	}

	public function team(): HasOne
	{
		return $this->hasOne(Team::class, 'name', 'name');
	}

	public function getTeamExistsAttribute(): bool
	{
		return $this->team()->exists();
	}
}
