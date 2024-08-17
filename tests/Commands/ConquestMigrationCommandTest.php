<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

beforeEach(function () {
    // $this->js = resource_path('js/Modals');
});

afterEach(function () {
    // if (File::isDirectory($this->js)) {
    //     File::deleteDirectory($this->js);
    // }
});

it('can create a conquest migration', function () {
    Artisan::call('make:conquest-migration', [
        'name' => $m = 'User',
        '--attributes' => 'name, user_id'
    ]);

});