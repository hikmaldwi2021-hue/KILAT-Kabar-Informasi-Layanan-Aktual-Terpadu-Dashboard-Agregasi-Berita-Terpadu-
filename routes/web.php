<?php

use Illuminate\Support\Facades\Route;
use App\Models\Berita;
use Livewire\Volt\Volt;

Route::get('/berita/{berita}/kunjungi', function (Berita $berita) {
    $berita->increment('dilihat');
    return redirect()->away($berita->link_asal);
})->name('berita.kunjungi');

Volt::route('/', 'beranda')->name('beranda');

Route::livewire('/kontak', 'pages::kontak')->name('kontak');
Route::livewire('/berita', 'pages::berita')->name('berita');
Route::livewire('/berita/{berita}', 'pages::berita-detail')->name('berita.detail');