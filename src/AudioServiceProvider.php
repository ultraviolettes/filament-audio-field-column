<?php

declare(strict_types=1);

namespace Croustibat\FilamentAudio;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class AudioServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-audio';

    public function configurePackage(Package $package): void
    {
        $package
            ->name(static::$name)
            ->hasViews();
    }
}
