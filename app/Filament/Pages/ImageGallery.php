<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class ImageGallery extends Page
{
	protected static ?string $navigationIcon = 'heroicon-o-photo';
	protected static ?string $navigationLabel = 'Galerie';
	protected static ?int $navigationSort = 4;

    protected static string $view = 'filament.pages.image-gallery';
}
