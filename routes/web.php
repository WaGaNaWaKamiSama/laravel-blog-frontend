<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Home
Volt::route('/', 'home')->name('home');

// Posts (Alias)
Volt::route('/posts', 'post-list')->name('posts.index');
Volt::route('/posts/{slug}', 'post-detail')->name('posts.show');

// Categories
Volt::route('/categories', 'categories')->name('categories');

// Auth Routes
Volt::route('/login', 'auth.login')->name('login')->middleware('guest');
Volt::route('/register', 'auth.register')->name('register')->middleware('guest');

Route::post('/logout', function () {
    session()->forget('api_token');
    return redirect('/')->with('success', 'Başarıyla çıkış yaptınız.');
})->name('logout');

// Dashboard (Protected)
Volt::route('/dashboard', 'dashboard')->name('dashboard')->middleware('auth.token');

// Profile (Protected)
Volt::route('/profile', 'profile')->name('profile')->middleware('auth.token');
