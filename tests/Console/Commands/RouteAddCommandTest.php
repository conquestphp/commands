<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

use function Pest\Laravel\artisan;

function createController($name)
{
    if (!str($name)->endsWith('Controller')) {
        $name .= 'Controller';
    }

    $path = base_path('app/Http/Controllers/'.$name.'.php');
    $directory = dirname($path);

    if (!File::isDirectory($directory)) {
        File::makeDirectory($directory, 0755, true, true);
    }

    File::put($path, "<?php\n\nuse Illuminate\Support\Facades\Route;\n\nclass {$name}\n{\n\tpublic function __invoke(Request \$request)\n\t{\n\t\treturn 'Hello, world!';\n\t}\n}");
    return $path;
}

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
    $path = createController($c = 'TestIndex');

    $success = Artisan::call('add:route', [
        'controller' => $c,
    ]);

    expect($success)->toBeTruthy();

    $file = File::get($this->web);
    $base = last(explode('/', $c));
    expect($file)->toContain("use App\Http\Controllers\\{$c}Controller;");
    expect($file)->toContain("Route::get('/test', {$base}Controller::class)->name('test.index');");
    File::delete($path);
});

it('can add an update route to web.php', function () {
    $path = createController($c ='TestUpdate');

    $success = Artisan::call('add:route', [
        'controller' => $c,
    ]);

    expect($success)->toBeTruthy();

    $file = File::get($this->web);
    $base = last(explode('/', $c));
    expect($file)->toContain("use App\Http\Controllers\\{$c}Controller;");
    expect($file)->toContain("Route::patch('/test', {$base}Controller::class)->name('test.update');");
    File::delete($path);
});

it('can add an store route to web.php', function () {
    $path = createController($c = 'TestStore');

    $success = Artisan::call('add:route', [
        'controller' => $c,
    ]);

    expect($success)->toBeTruthy();

    $file = File::get($this->web);
    $base = last(explode('/', $c));
    expect($file)->toContain("use App\Http\Controllers\\{$c}Controller;");
    expect($file)->toContain("Route::post('/test', {$base}Controller::class)->name('test.store');");
    File::delete($path);
});

it('can add an destroy route to web.php', function () {
    $path = createController($c ='TestDestroy');

    $success = Artisan::call('add:route', [
        'controller' => $c,
    ]);

    expect($success)->toBeTruthy();

    $file = File::get($this->web);
    $base = last(explode('/', $c));
    expect($file)->toContain("use App\Http\Controllers\\{$c}Controller;");
    expect($file)->toContain("Route::delete('/test', {$base}Controller::class)->name('test.destroy');");
    File::delete($path);
});

it('supports directory structure', function () {
    $path = createController($c = 'Test/TestShow');

    $success = Artisan::call('add:route', [
        'controller' => $c,
    ]);

    expect($success)->toBeTruthy();

    $file = File::get($this->web);
    $base = last(explode('/', $c));
    $n = str_replace('/', '\\', $c);
    expect($file)->toContain("use App\Http\Controllers\\{$n}Controller;");
    expect($file)->toContain("Route::get('/test/test', {$base}Controller::class)->name('test.test.show');");
    File::delete($path);
});

it('can add to a custom route file', function () {
    $controller = createController($c = 'Test/TestEdit');

    $path = base_path('routes/test.php');
    File::put($path, "<?php\n\nuse Illuminate\Support\Facades\Route;\n");

    $success = Artisan::call('add:route', [
        'controller' => $c,
        '--file' => 'test.php',
    ]);

    expect($success)->toBeTruthy();

    $file = File::get($path);
    $base = last(explode('/', $c));
    $n = str_replace('/', '\\', $c);
    expect($file)->toContain("use App\Http\Controllers\\{$n}Controller;");
    expect($file)->toContain("Route::get('/test/test', {$base}Controller::class)->name('test.test.edit');");

    File::delete($path);
    File::delete($controller);
});

it('can use route model binding', function () {
    $path = createController($c = 'Test/TestItemCreate');

    $success = Artisan::call('add:route', [
        'controller' => $c,
        '--model' => true,
    ]);

    expect($success)->toBeTruthy();

    $file = File::get($this->web);
    $base = last(explode('/', $c));
    $n = str_replace('/', '\\', $c);
    expect($file)->toContain("use App\Http\Controllers\\{$n}Controller;");
    expect($file)->toContain("Route::get('/test/{testItem}', {$base}Controller::class)->name('test.test-item.create');");

    File::delete($path);
});


it('can limit to a class base name for the route name', function () {
    $path = createController($c = 'Test/TestItemDelete');

    $success = Artisan::call('add:route', [
        'controller' => $c,
        '--class' => true,
    ]);

    expect($success)->toBeTruthy();

    $file = File::get($this->web);
    $base = last(explode('/', $c));
    $n = str_replace('/', '\\', $c);
    expect($file)->toContain("use App\Http\Controllers\\{$n}Controller;");
    expect($file)->toContain("Route::get('/test/test-item', {$base}Controller::class)->name('test-item.delete');");

    File::delete($path);});
