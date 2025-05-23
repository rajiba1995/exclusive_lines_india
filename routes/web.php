<?php

use Illuminate\Support\Facades\Route;
// Livewire Components
use App\Livewire\AdminLogin;
use App\Http\Controllers\Admin\AuthController;
use App\Livewire\Admin\{Dashboard};
use App\Livewire\Master\{BannerIndex, FaqIndex, WhyEwentIndex,BrandIndex,CollectionIndex};
use App\Livewire\Admin\CommonPageIndex;

// Public Route for Login

Route::get('admin/login', AdminLogin::class)->name('login');

Route::post('/admin/logout', [AuthController::class, 'logout'])->name('admin.logout');

// Default Root Route
Route::get('/', function () { return redirect()->route('login');});
// Admin Routes - Authenticated and Authorized
Route::middleware(['auth:admin'])->prefix('admin')->group(function () {
    // Dashboard and Customer Routes
    Route::get('dashboard', Dashboard::class)->name('admin.dashboard');

    // Master Routes
    Route::group(['prefix' => 'master'], function () {
        Route::get('/banner', BannerIndex::class)->name('admin.banner.index');
        Route::get('/why-ewent',WhyEwentIndex::class)->name('admin.why-ewent');
        Route::get('/brands',BrandIndex::class)->name('admin.brands.index');
        Route::get('/collections',CollectionIndex::class)->name('admin.collection.index');
    });
    Route::group(['prefix' => 'common_pages'], function () {
        Route::get('/index', CommonPageIndex::class)->name('admin.common_pages.index');
        Route::get('/edit/{id}', CommonPageIndex::class)->name('admin.common_pages.edit');

    });



});