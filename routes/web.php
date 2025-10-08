<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ManajemenUserController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StockReportController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\UserController;

// Middleware for guest users only
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'login_post'])->name('login.post');
    Route::get('/password-request', [AuthController::class, 'password_request'])->name('password.request');
    Route::post('/password-request-post', [AuthController::class, 'password_request_post'])->name('password.request.post');
    Route::get('/password-reset-form', [AuthController::class, 'password_reset'])->name('password.reset');
    Route::put('/password-reset-post', [AuthController::class, 'password_reset_post'])->name('password.reset.post');

    // Hanya boleh diakses kalau sedang proses verifikasi email
    Route::middleware('ensureEmailVerificationPending')->group(function () {
        Route::get('/verify-email', [AuthController::class, 'verify_email'])->name('verify.email');
        Route::post('/verify-email', [AuthController::class, 'verify_email_post'])->name('verify.email.post');
        Route::post('/verify-email/resend', [AuthController::class, 'resend_verification_code'])->name('verify.email.resend');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/two_factor_auth', [AuthController::class, 'two_factor_auth'])->name('two_factor_auth');
    Route::post('/two_factor_auth/login', [AuthController::class, 'two_factor_process'])->name('two_factor_auth.login');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/', [HomeController::class, 'index'])->name('home.index');

    Route::get('/file/{type}/{filename}', function ($type, $filename) {
        $path = storage_path('app/public/' . $type . '/' . $filename);

        if (!file_exists($path)) {
            abort(404);
        }

        $file = file_get_contents($path);
        $type = mime_content_type($path);

        return response($file, 200)->header("Content-Type", $type);
    })->name('image.get');

    // Routes for admin & owner only
    Route::middleware('role:admin,owner')->group(function () {
        // Routes for both admin and owner

        // Product Categories
        Route::get('/product-categories', [ProductCategoryController::class, 'index'])->name('product-categories.index');
        Route::get('/product-categories/create', [ProductCategoryController::class, 'create'])->name('product-categories.create');
        Route::post('/product-categories', [ProductCategoryController::class, 'store'])->name('product-categories.store');
        Route::get('/product-categories/{id}/edit', [ProductCategoryController::class, 'edit'])->name('product-categories.edit');
        Route::put('/product-categories/{id}', [ProductCategoryController::class, 'update'])->name('product-categories.update');
        Route::delete('/product-categories/{id}', [ProductCategoryController::class, 'destroy'])->name('product-categories.destroy');

        // Route Products Resource
        Route::resource('products', \App\Http\Controllers\ProductController::class)->except(['show'])->names([
            'index' => 'product.index',
            'create' => 'product.create',
            'store' => 'product.store',
            'edit' => 'product.edit',
            'update' => 'product.update',
            'destroy' => 'product.destroy',
        ]);

        // Route Suppliers Resource with custom names and except show
        Route::resource('suppliers', \App\Http\Controllers\SupplierController::class)->except(['show'])->names([
            'index' => 'supplier.index',
            'create' => 'supplier.create',
            'store' => 'supplier.store',
            'edit' => 'supplier.edit',
            'update' => 'supplier.update',
            'destroy' => 'supplier.destroy',
        ]);

        // Route Items In with prefix and custom names
        Route::prefix('items-in')->name('items-in.')->group(function () {
            Route::get('/', [\App\Http\Controllers\ItemInController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\ItemInController::class, 'create'])->name('create');
            Route::post('/create', [\App\Http\Controllers\ItemInController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [\App\Http\Controllers\ItemInController::class, 'edit'])->name('edit');
            Route::put('/{id}/edit', [\App\Http\Controllers\ItemInController::class, 'update'])->name('update');
            Route::delete('/{id}', [\App\Http\Controllers\ItemInController::class, 'destroy'])->name('destroy');
        });

        // Route Items In with prefix and custom names
        Route::prefix('items-in')->name('items-in.')->group(function () {
            Route::get('/', [\App\Http\Controllers\ItemInController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\ItemInController::class, 'create'])->name('create');
            Route::post('/create', [\App\Http\Controllers\ItemInController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [\App\Http\Controllers\ItemInController::class, 'edit'])->name('edit');
            Route::put('/{id}/edit', [\App\Http\Controllers\ItemInController::class, 'update'])->name('update');
            Route::delete('/{id}', [\App\Http\Controllers\ItemInController::class, 'destroy'])->name('destroy');
        });

        // Route Items Out with prefix and custom names
        Route::prefix('items-out')->name('items-out.')->group(function () {
            Route::get('/', [\App\Http\Controllers\ItemOutController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\ItemOutController::class, 'create'])->name('create');
            Route::post('/create', [\App\Http\Controllers\ItemOutController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [\App\Http\Controllers\ItemOutController::class, 'edit'])->name('edit');
            Route::put('/{id}/edit', [\App\Http\Controllers\ItemOutController::class, 'update'])->name('update');
            Route::delete('/{id}', [\App\Http\Controllers\ItemOutController::class, 'destroy'])->name('destroy');
        });

        // Route Transaction with prefix and custom names
        Route::prefix('transaction')->name('transaction.')->group(function () {
            Route::get('/', [\App\Http\Controllers\TransactionController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\TransactionController::class, 'create'])->name('create');
            Route::post('/create', [\App\Http\Controllers\TransactionController::class, 'store'])->name('store');
            Route::get('/{id}/detail', [\App\Http\Controllers\TransactionController::class, 'detail'])->name('detail');
            Route::get('/{id}/cetak', [\App\Http\Controllers\TransactionController::class, 'cetak'])->name('cetak');
            Route::get('/{id}/edit', [\App\Http\Controllers\TransactionController::class, 'edit'])->name('edit');
            Route::put('/{id}/edit', [\App\Http\Controllers\TransactionController::class, 'update'])->name('update');
            Route::delete('/{id}', [\App\Http\Controllers\TransactionController::class, 'destroy'])->name('destroy');
        });

        // Route Member with prefix and custom names
        Route::prefix('member')->name('member.')->group(function () {
            Route::get('/', [\App\Http\Controllers\MemberController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\MemberController::class, 'create'])->name('create');
            Route::post('/create', [\App\Http\Controllers\MemberController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [\App\Http\Controllers\MemberController::class, 'edit'])->name('edit');
            Route::put('/{id}/edit', [\App\Http\Controllers\MemberController::class, 'update'])->name('update');
            Route::delete('/{id}', [\App\Http\Controllers\MemberController::class, 'destroy'])->name('destroy');
        });

        // Route Laporan
        Route::prefix('report')->name('report.')->group(function () {
            Route::get('/', [ReportController::class, 'index'])->name('index');
            Route::post('/', [ReportController::class, 'index'])->name('proses');
            Route::get('/{report_key}/{month}/{year}/cetak', [ReportController::class, 'cetak'])->name('cetak');
        });

        // Route Stock Product
        Route::prefix('stock-report')->name('stock-report.')->group(function () {
            Route::get('/', [StockReportController::class, 'index'])->name('index');
            Route::post('/', [StockReportController::class, 'index'])->name('detail');
            Route::get('/{product}/{month}/{year}/cetak', [StockReportController::class, 'cetak'])->name('cetak');
        });

        // Route Kelola Admin
        Route::prefix('admin')->name('admin.')->group(function () {
            Route::get('/', [ManajemenUserController::class, 'index'])->name('index');
            Route::get('/create', [ManajemenUserController::class, 'create'])->name('create');
            Route::post('/create', [ManajemenUserController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [ManajemenUserController::class, 'edit'])->name('edit');
            Route::put('/{id}/edit', [ManajemenUserController::class, 'update'])->name('update');
            Route::delete('/{id}', [ManajemenUserController::class, 'destroy'])->name('destroy');
        });

        // Route Kelola Team
        Route::prefix('team')->name('team.')->group(function () {
            Route::get('/', [TeamController::class, 'index'])->name('index');
            Route::post('/create', [TeamController::class, 'store'])->name('create');
            Route::put('/{id}/edit', [TeamController::class, 'update'])->name('update');
            Route::delete('/{id}', [TeamController::class, 'destroy'])->name('delete');
        });

        // Route Kelola Profile
        Route::prefix('profile')->name('profile.')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('index');
            Route::put('/two_factor_secret', [UserController::class, 'two_factor_secret'])->name('two_factor_secret');
            Route::put('/two_factor_recovery', [UserController::class, 'two_factor_recovery'])->name('two_factor_recovery');
        });
    });

    // Routes for admin only
    Route::middleware('role:admin')->group(function () {
        // Routes for admin only
    });
});
