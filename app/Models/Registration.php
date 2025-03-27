<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Registration extends Model
{
    protected $fillable = ['event_id', 'name', 'leader', 'phone'];

	public function event()
	{
		return $this->belongsTo(Event::class, 'event_id');
	}
}
