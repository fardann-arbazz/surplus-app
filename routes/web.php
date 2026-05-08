<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StoresController;
use App\Http\Controllers\SurplusProductController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\RedirectIfAuthenticatedByRole;
use Illuminate\Support\Facades\Route;

// Guest Routes (hanya untuk user yang belum login)
Route::middleware(RedirectIfAuthenticatedByRole::class)->group(function () {
    // Login
    Route::get('/', [AuthController::class, 'index'])->name('login');
    Route::get('/login', [AuthController::class, 'index'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

    // Register
    Route::get('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/register', [AuthController::class, 'store'])->name('register.store');

    // Forgot Password
    Route::get('/forgot-password', [ForgotPasswordController::class, 'showForgotForm'])
        ->name('password.forgot');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetOtp'])
        ->name('password.send-otp');

    // Reset Password
    Route::get('/reset-password', [ResetPasswordController::class, 'showResetForm'])
        ->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'reset'])
        ->name('password.reset.submit');
    Route::post('/reset-password/resend', [ResetPasswordController::class, 'resendOtp'])
        ->name('password.reset.resend');
});

// Auth Routes (harus login)
Route::middleware('auth')->group(function () {
    // OTP Verification
    Route::get('/otp-verify-email', [AuthController::class, 'showOtpVerification'])
        ->name('otp.verify-email');
    Route::post('/otp-verify-email', [AuthController::class, 'verifyOtp'])
        ->name('otp.verify');
    Route::post('/otp-resend', [AuthController::class, 'resendOtp'])
        ->name('otp.resend');

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// Protected Routes (harus login DAN email terverifikasi)
Route::middleware(['auth', 'verified'])->group(function () {
    // Home
    Route::get('/home', [UserController::class, 'index'])->name('user.home');

    // Surplus
    Route::get('/surplus-menu', [SurplusProductController::class, 'index'])->name('user.surplus-menu');
    Route::get('/surplus-products/nearby', [SurplusProductController::class, 'getNearby'])->name('surplus.nearby');

    // Lokasi 
    Route::patch('/user/location', [LocationController::class, 'update'])->name('user.location.update');
    Route::get('/user/location/status', [LocationController::class, 'status'])->name('user.location.status');

    // Stores route for user
    Route::get('/user/nearby-stores', [StoresController::class, 'getNearbyStores'])->name('user.stores-nearby');

    // Seller form regist
    Route::get('/regist-seller', [StoresController::class, 'index'])->name('store.index');

    // Register Seller
    Route::post('/seller-regist', [StoresController::class, 'createStore'])->name('store.submit');

    // Notif
    Route::post('/notifications/read-all', [DashboardController::class, 'readNotif']);

    // Admin Dashboard
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard')->middleware('role:admin');
    Route::get('/admin/seller-management', [DashboardController::class, 'sellerManagement'])->name('admin.seller-management')->middleware('role:admin');
    Route::patch('/admin/update-status-seller', [DashboardController::class, 'updateStoresStatus'])->name('admin.update-seller-status')->middleware('role:admin');
    Route::get('/admin/sellers/{id}/detail', [DashboardController::class, 'getDetailStore'])->name('admin.seller.detail');

    // Admin Category Management
    Route::get('/admin/category-management', [CategoryController::class, 'index'])->name('admin.category-management')->middleware('role:admin');

    // Seller Dashboard
    Route::middleware(['role:seller'])->group(function () {
        Route::get('/seller/dashboard', [StoresController::class, 'showDashboard'])
            ->name('seller.dashboard');

        Route::get('/seller/menu-management', [ProductController::class, 'index'])
            ->name('seller.menu-management');

        Route::post('/seller/menu/create', [ProductController::class, 'createProduct'])
            ->name('seller.menu-create');

        Route::put('/seller/menu/update/{productId}', [ProductController::class, 'updateProduct'])
            ->name('seller.menu-update');

        Route::delete('/seller/menu/delete/{productId}', [ProductController::class, 'deleteProduct'])
            ->name('seller.menu-delete');

        Route::post('/seller/surplus-food', [SurplusProductController::class, 'create'])
            ->name('seller.surplus-create');
    });
});



// Testing mailer 
// Route::get('/test-mail', function () {
//     \Mail::raw('Test email', function ($message) {
//         $message->to('test@example.com')
//             ->subject('Test Mailtrap');
//     });

//     return 'sent';
// });
