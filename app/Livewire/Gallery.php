<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class Gallery extends Component
{
	use WithFileUploads;

	public array $photos = [];

	public function upload()
	{
		$this->validate([
			'photos.*' => 'image|max:1024', // max 1MB na soubor
		]);

		foreach ($this->photos as $photo) {
			$photo->store('public/gallery');
		}

		$this->reset('photos');
	}

	public function delete(string $filename)
	{
		Storage::delete('public/gallery/' . $filename);
	}

	public function render()
	{
		$images = collect(Storage::files('public/gallery'))
			->filter(fn($path) => str_starts_with($path, 'public/gallery/'))
			->map(fn($path) => basename($path));

		return view('livewire.gallery', [
			'images' => $images,
		]);
	}
}
