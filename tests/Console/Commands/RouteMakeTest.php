<?php

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;

beforeEach(function () {
    $this->web = base_path('routes/web.php');
    File::put($this->web, "<?php\n\nuse Illuminate\Support\Facades\Route;\n");
});

afterEach(function () {
    if (File::exists($this->web)) {
        File::delete($this->web);
    }
});

it('can add an index route to web.php', function () {
    Artisan::call('make:route', [
        'controller' => 'TestIndex',
    ]);

    $file = File::get($this->web);

    expect($file)->toContain("use App\Http\Controllers\TestIndexController;");
    expect($file)->toContain("Route::get('/test', TestIndexController::class)->name('test.index');");
});

it('can add a update route to web.php', function () {
    Artisan::call('make:route', [
        'controller' => 'Test/TestUpdateController',
    ]);

    $file = File::get($this->web);

    expect($file)->toContain("use App\Http\Controllers\Test\TestUpdateController;");
    expect($file)->toContain("Route::patch('/test/test', TestUpdateController::class)->name('test.test.update');");
});

it('can add a destroy route to web.php', function () {
    Artisan::call('make:route', [
        'controller' => 'Test/TestDestroyController',
    ]);

    $file = File::get($this->web);

    expect($file)->toContain("use App\Http\Controllers\Test\TestDestroyController;");
    expect($file)->toContain("Route::delete('/test/test', TestDestroyController::class)->name('test.test.destroy');");
});

it('can add a store route to web.php', function () {
    Artisan::call('make:route', [
        'controller' => 'Test/TestStoreController',
    ]);

    $file = File::get($this->web);

    expect($file)->toContain("use App\Http\Controllers\Test\TestStoreController;");
    expect($file)->toContain("Route::post('/test/test', TestStoreController::class)->name('test.test.store');");
});

it('can add to a custom route file', function () {
    $path = base_path('routes/test.php');
    File::put($path, "<?php\n\nuse Illuminate\Support\Facades\Route;\n");

    Artisan::call('make:route', [
        'controller' => 'TestIndexController',
        '--file' => 'test.php',
    ]);

    $file = File::get($path);
    
    expect($file)->toContain("use App\Http\Controllers\TestIndexController;");
    expect($file)->toContain("Route::get('/test', TestIndexController::class)->name('test.index');");

    if (File::exists($path)) {
        File::delete($path);
    }
});

it('can use a model for the route', function () {
    Artisan::call('make:route', [
        'controller' => 'Test/TestDestroyController',
        '--model' => true,
    ]);

    $file = File::get($this->web);

    expect($file)->toContain("use App\Http\Controllers\Test\TestDestroyController;");
    expect($file)->toContain("Route::delete('/test/{test}', TestDestroyController::class)->name('test.test.destroy');");
});

it('can limit to a class base name for the route name', function () {
    Artisan::call('make:route', [
        'controller' => 'Test/TestDestroyController',
        '--class' => true,
    ]);

    $file = File::get($this->web);

    expect($file)->toContain("use App\Http\Controllers\Test\TestDestroyController;");
    expect($file)->toContain("Route::delete('/test/test', TestDestroyController::class)->name('test.destroy');");
});