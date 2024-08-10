<?php

use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Artisan;

it('can create a user', function () {
    Artisan::call('user:make', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'password123',
    ]);
    
    expect(User::where('email', 'john@example.com')->exists())->toBeTrue();
});