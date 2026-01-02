<?php

declare(strict_types=1);

namespace Aaix\FilamentEditorJs;

use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Support\Facades\Route;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentEditorJsServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-editor-js')
            ->hasViews('aaix-editorjs')
            ->hasTranslations();
    }

    public function packageBooted(): void
    {
        FilamentAsset::register([
            Css::make('filament-editor-js', __DIR__ . '/../resources/dist/filament-editor-js.css'),
            Js::make('filament-editor-js', __DIR__ . '/../resources/dist/filament-editor-js.js'),
        ], 'aaix/filament-editor-js');

        $this->registerRoutes();
    }

    protected function registerRoutes(): void
    {
        Route::group([
            'prefix' => 'filament-editor-js',
            'as' => 'filament-editor-js.',
            'middleware' => ['web', 'auth', 'signed'],
        ], function () {
            Route::post('/upload', \Aaix\FilamentEditorJs\Http\Controllers\ImageUploadController::class)
                ->name('upload');
        });
    }
}
