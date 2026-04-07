<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Volt::route('/', 'home')->name('home');

Volt::route('/posts', 'post-list')->name('posts.index');
Volt::route('/posts/{slug}', 'post-detail')->name('posts.show');

Volt::route('/categories', 'categories')->name('categories');

Volt::route('/login', 'auth.login')->name('login')->middleware('guest');
Volt::route('/register', 'auth.register')->name('register')->middleware('guest');

Route::post('/logout', function () {
    app(\App\Services\ApiService::class)->logout();
    return redirect('/')->with('success', 'Logged out successfully.');
})->name('logout');

Volt::route('/dashboard', 'dashboard')->name('dashboard')->middleware('auth.token');

Volt::route('/profile', 'profile')->name('profile')->middleware('auth.token');

Volt::route('/post-pending', 'post-pending')->name('post.pending')->middleware('auth.token');
Volt::route('/contracts/{slug}', 'contract-detail')->name('contracts.show');
