<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'date', 'shootout'];

    public function teams()
    {
        return $this->belongsToMany(Team::class, 'results', 'event_id', 'team_id');
    }

    public function results()
    {
        return $this->hasMany(Result::class);
    }
}
