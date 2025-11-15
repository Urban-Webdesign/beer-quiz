<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Image\Enums\Fit;

class Event extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = ['name', 'date', 'shootout', 'register_from', 'capacity'];

	public function registerMediaCollections(): void
	{
		$this->addMediaCollection('gallery')
            ->useDisk('public')
            ->withResponsiveImages();
	}

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->format('webp')
            ->fit(Fit::Max, 400, 300)
            ->optimize()
            ->performOnCollections('gallery');

        $this
            ->addMediaConversion('original')
            ->format('webp')
            ->fit(Fit::Max, 1600, 1200)
            ->optimize()
            ->performOnCollections('gallery');
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
