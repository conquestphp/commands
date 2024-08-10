<?php

use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Artisan;

it('can create a user', function () {
    $result = Artisan::call('user:make', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'password123',
    ]);
    
    expect(User::where('email', 'john@example.com')->exists())->toBeTrue();
    expect($result)->toBe(1); // Successful
});

it('validates email', function () {
    $result = Artisan::call('user:make', [
        'name' => 'John Doe',
        'email' => 'johnexample.com',
        'password' => 'password123',
    ]);
    
    expect(User::where('email', 'johnexample.com')->exists())->toBeFalse();
    expect($result)->toBe(0); // Fail
});