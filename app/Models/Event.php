<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'date', 'shootout', 'register_from', 'status', 'capacity'];

    public function teams()
    {
        return $this->belongsToMany(Team::class, 'results', 'event_id', 'team_id');
    }

    public function results()
    {
        return $this->hasMany(Result::class);
    }

	public function registrations()
	{
		return $this->hasMany(Registration::class);
	}
}
