<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class Gallery extends Component
{
	use WithFileUploads;

	public array $photos = [];

	// Only authenticated users should be able to reach this component's upload/delete actions.
	// Add auth middleware on the route/page that renders this component as well.
	public function upload()
	{
		$this->authorize('upload-gallery'); // requires Gate defined, or replace with: abort_unless(Auth::check(), 403);

		$this->validate([
			// image rule ensures MIME is image/*, max 2 MB, and no .php disguised as image
			'photos.*' => ['required', 'image', 'max:2048', 'mimes:jpg,jpeg,png,gif,webp'],
		]);

		foreach ($this->photos as $photo) {
			// Store in a NON-public disk (local) or in a folder protected by .htaccess
			$photo->store('gallery', 'local');
		}

		$this->reset('photos');
		session()->flash('message', 'Fotky byly úspěšně nahrány.');
	}

	public function delete(string $filename)
	{
		abort_unless(Auth::check(), 403);

		// Sanitize filename — prevent path traversal
		$filename = basename($filename);

		Storage::disk('local')->delete('gallery/' . $filename);
	}

	public function render()
	{
		$images = collect(Storage::disk('local')->files('gallery'))
			->map(fn($path) => basename($path));

		return view('livewire.gallery', [
			'images' => $images,
		]);
	}
}
