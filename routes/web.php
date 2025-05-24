<?php

use Illuminate\Support\Facades\Route;
// Livewire Components
use App\Livewire\AdminLogin;
use App\Http\Controllers\Admin\AuthController;
use App\Livewire\Admin\{Dashboard};
use App\Livewire\Admin\{CommonPageIndex,CommonPageEdit,StoreLocationIndex,StoreLocationCreate,StoreLocationEdit};
use App\Livewire\Master\{BannerIndex, FaqIndex, WhyEwentIndex,BrandIndex,CollectionIndex,ProductIndex,ProductCreate};

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
        Route::get('/products',ProductIndex::class)->name('admin.product.index');
        Route::get('/product/create',ProductCreate::class)->name('admin.product.create');
    });
    Route::group(['prefix' => 'common-pages'], function () {
        Route::get('/index', CommonPageIndex::class)->name('admin.common_pages.index');
        Route::get('/edit/{id}', CommonPageEdit::class)->name('admin.common_pages.edit');
    });

    Route::group(['prefix' => 'store-location'], function () {
        Route::get('/index', StoreLocationIndex::class)->name('admin.store_location.index');
        Route::get('/create', StoreLocationCreate::class)->name('admin.store_location.create');
        Route::get('/edit/{id}', StoreLocationEdit::class)->name('admin.store_location.edit');

    });

    



});