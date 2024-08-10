<?php

use Conquest\Assemble\Concerns\HasMethods;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class Helper
{
    use HasMethods;
}

function toPath(string $class, string $method, string $type): string
{
    $class = str($class)->replace('\\', '/')->ltrim('/')->toString();

    return match ($type) {
        'Controller', 'controller', 'c' => app_path('Http/Controllers/'.$class.$method.'Controller.php'),
        'Request', 'request', 'r' => app_path('Http/Requests/'.$class.$method.'Request.php'),
        'Page', 'page', 'P' => resource_path('js/Pages/'.$class.$method.'.vue'),
        'Modal', 'modal', 'M' => resource_path('js/Modals/'.$class.$method.'.vue'),
        'Model', 'model', 'm' => app_path('Models/'.(new Helper)->getBase($class).'.php'),
        default => $class.$method
    };
}

beforeEach(function () {
    $this->helper = new Helper;
    $this->web = app()->basePath('routes/web.php');
    File::put($this->web, "<?php\n\nuse Illuminate\Support\Facades\Route;\n");
});

afterEach(function () {
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
    $success = Artisan::call('make:conquest', [
        'name' => $name = 'Conquest/User',
        'method' => $method = 'Index',
    ]);

    expect($success)->toBeTruthy();

    expect(File::exists(toPath($name, $method, 'c')))->toBeTrue();
    expect(File::exists(toPath($name, $method, 'r')))->toBeTrue();
});

it('can create CRUD', function () {
    $success = Artisan::call('make:conquest', [
        'name' => $name = 'Conquest/User',
        '--crud' => true,
    ]);

    expect($success)->toBeTruthy();

    collect($this->helper->methods)->each(function ($method) use ($name) {
        if ($this->helper->hasPage($method)) {
            expect(File::exists(toPath($name, $method, 'P')))->toBeTrue();
        } elseif ($this->helper->hasModal($method)) {
            expect(File::exists(toPath($name, $method, 'M')))->toBeTrue();
        }

        expect(File::exists(toPath($name, $method, 'c')))->toBeTrue();
        expect(File::exists(toPath($name, $method, 'r')))->toBeTrue();
    });
});

it('can create model', function () {
    $success = Artisan::call('make:conquest', [
        'name' => $name = 'Conquest/User',
        'method' => $method = 'Index',
        '--model' => true,
    ]);

    expect($success)->toBeTruthy();

    expect(File::exists(toPath($name, $method, 'm')))->toBeTrue();
    expect(File::exists(toPath($name, $method, 'c')))->toBeTrue();
    expect(File::exists(toPath($name, $method, 'r')))->toBeTrue();
});

it('can do all', function () {
    $before = File::get($this->web);

    $success = Artisan::call('make:conquest', [
        'name' => $name = 'Conquest/User',
        '--all' => true,
    ]);

    expect($success)->toBeTruthy();

    collect($this->helper->methods)->each(function ($method) use ($name) {
        if ($this->helper->hasPage($method)) {
            expect(File::exists(toPath($name, $method, 'P')))->toBeTrue();
        } elseif ($this->helper->hasModal($method)) {
            expect(File::exists(toPath($name, $method, 'M')))->toBeTrue();
        }

        expect(File::exists(toPath($name, $method, 'c')))->toBeTrue();
        expect(File::exists(toPath($name, $method, 'r')))->toBeTrue();
    });

    expect(File::get($this->web))->not->toBe($before);
});

it('can change the generated javascript resource', function () {
    $success = Artisan::call('make:conquest', [
        'name' => $name = 'Conquest/User',
        'method' => $method = 'Create',
        '--modal' => true,
    ]);

    expect($success)->toBeTruthy();

    expect(File::exists(toPath($name, $method, 'c')))->toBeTrue();
    expect(File::exists(toPath($name, $method, 'r')))->toBeTrue();
    expect(File::exists(toPath($name, $method, 'M')))->toBeTrue();
    expect(File::exists(toPath($name, $method, 'P')))->toBeFalse();
});
