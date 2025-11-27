<?php

use App\Livewire\Hound\Add as HoundAdd;
use App\Livewire\Hound\Index as HoundIndex;
use App\Livewire\Price\Index as PriceIndex;
use App\Livewire\Price\Show as PriceShow;
use App\Livewire\Product\Add as ProductAdd;
use App\Livewire\Product\Index as ProductIndex;
use App\Livewire\Product\Show as ProductShow;
use App\Livewire\Shop\Create as ShopCreate;
use App\Livewire\Shop\Index as ShopIndex;
use App\Livewire\Shop\Show as ShopShow;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');

    Route::get('shops/create', ShopCreate::class)->name('shops.create');
    Route::get('shops', ShopIndex::class)->name('shops.index');
    Route::get('shops/show/{shop}', ShopShow::class)->name('shops.show');

    Route::get('products/add', ProductAdd::class)->name('products.add');
    Route::get('products', ProductIndex::class)->name('products.index');
    Route::get('products/show/{product}', ProductShow::class)->name('products.show');

    Route::get('hounds/add', HoundAdd::class)->name('hounds.add');
    Route::get('hounds', HoundIndex::class)->name('hounds.index');

    Route::get('prices', PriceIndex::class)->name('prices.index');
});

require __DIR__.'/auth.php';
