<?php

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;

beforeEach(function () {
    $this->web = app()->basePath('routes/web.php');
    File::put($this->web, "<?php\n\nuse Illuminate\Support\Facades\Route;\n");
});

afterEach(function () {
    // Clean up created files after each test
    File::deleteDirectory(app()->basePath('app/Http/Controllers/Conquest'));
    File::deleteDirectory(app()->basePath('app/Http/Requests/Conquest'));
    File::deleteDirectory(app()->resourcePath('js/Pages/Conquest'));
    File::deleteDirectory(app()->basePath('Models'));
    File::deleteDirectory(app()->basePath('Models'));
    if (File::exists($this->web)) {
        File::delete($this->web);
    }
});

it('can create a conquest endpoint which uses a valid method', function () {

    Artisan::call('make:conquest', [
        'name' => 'Conquest/User',
        'method' => 'Index',
        '--force' => true,
    ]);

    $controller = base_path('app/Http/Controllers/Conquest/UserIndexController.php');
    $request = base_path('app/Http/Requests/Conquest/UserIndexRequest.php');
    expect(File::exists($controller))->toBeTrue();
    expect(File::exists($request))->toBeTrue();
});
