<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Event extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = ['name', 'date', 'shootout', 'register_from', 'capacity'];

	public function registerMediaConversions(Media $media = null): void
	{
		$this->addMediaConversion('info') // název konverze je libovolný
		->nonQueued(); // synchronní zpracování (užitečné při ladění)
	}

	public function registerMediaCollections(): void
	{
		$this->addMediaCollection('gallery')->useDisk('public');
	}

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'results', 'event_id', 'team_id');
    }

    public function results(): HasMany
    {
        return $this->hasMany(Result::class);
    }

	public function registrations(): HasMany
	{
		return $this->hasMany(Registration::class);
	}
}
