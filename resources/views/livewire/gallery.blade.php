<style>
    .container {
        padding: 1rem;
    }

    form {
        margin-bottom: 1.5rem;
        display: flex;
        gap: 1rem;
        flex-direction: row;
        align-items: center;
    }

    input {
        padding: 0.5rem;
        width: auto;
        border: 1px solid #ccc;
        border-radius: .5rem;
    }

    .submit {
        background-color: #4CAF50; /* Green */
        border: none;
        border-radius: .5rem;
        color: white;
        padding: 0.5rem 1rem;
        text-align: center;
        text-decoration: none;
        display: inline-block;
        font-size: 1rem;
        margin-left: 0.5rem;
        cursor: pointer;
    }

</style>


@dd($images)
<div class="container">
    <form wire:submit.prevent="upload">
        <input type="file" wire:model="photos" multiple>
        @error('photos.*') <p class="message">{{ $message }}</p> @enderror
        <button type="submit" class="submit">Nahrát</button>
    </form>

    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
        @foreach ($images as $image)
            <div class="relative group">
                <img src="{{ Storage::url('gallery/' . $image) }}" alt="Uploaded image" class="w-full h-40 object-cover rounded">

                <button wire:click="delete('{{ $image }}')" class="absolute top-2 right-2 bg-red-600 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity">
                    Smazat
                </button>
            </div>
        @endforeach
    </div>
</div>
