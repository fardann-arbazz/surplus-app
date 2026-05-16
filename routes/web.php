<?php

use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Api\MidtransWebhookController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Seller\OrderController as SellerOrderController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\Seller\DashboardController as SellerDashboardController;
use App\Http\Controllers\StoresController;
use App\Http\Controllers\SurplusProductController;
use App\Http\Controllers\User\CartController;
use App\Http\Controllers\User\OrderController as UserOrderController;
use App\Http\Controllers\User\SurplusDetailController;
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

    Route::prefix('orders')->name('user.orders.')->group(function () {

        // Daftar order
        Route::get('/', [UserOrderController::class, 'index'])
            ->name('index');

        // Halaman checkout (review cart sebelum bayar)
        Route::get('/checkout', [UserOrderController::class, 'checkoutPage'])
            ->name('checkout');

        // Proses checkout → buat order + redirect ke payment
        Route::post('/checkout', [UserOrderController::class, 'checkout'])
            ->name('checkout.process');

        // Detail order
        Route::get('/{orderId}', [UserOrderController::class, 'show'])
            ->name('show');

        // Halaman pembayaran Midtrans Snap
        Route::get('/{orderId}/payment', [UserOrderController::class, 'paymentPage'])
            ->name('payment');

        // Halaman form input kode pickup
        Route::get('/{orderId}/confirm-pickup', [UserOrderController::class, 'confirmPickupPage'])
            ->name('confirm-pickup');

        // Proses konfirmasi pickup
        Route::post('/{orderId}/confirm-pickup', [UserOrderController::class, 'confirmPickup'])
            ->name('confirm-pickup.process');
    });

    Route::prefix('cart')->name('user.cart.')->group(function () {

        // Tampilkan cart
        Route::get('/', [CartController::class, 'index'])
            ->name('index');

        // Tambah item ke cart (dari product detail)
        Route::post('/', [CartController::class, 'store'])
            ->name('store');

        // Update quantity
        Route::patch('/{cartId}', [CartController::class, 'update'])
            ->name('update');

        // Hapus satu item
        Route::delete('/{cartId}', [CartController::class, 'destroy'])
            ->name('destroy');

        // Kosongkan semua cart
        Route::delete('/', [CartController::class, 'clear'])
            ->name('clear');

        // Lanjut ke checkout
        Route::post('/checkout', [CartController::class, 'proceedToCheckout'])
            ->name('checkout');
    });

    Route::get('/surplus/{id}', [SurplusDetailController::class, 'show'])
        ->name('user.surplus.show');

    // Seller form regist
    Route::get('/regist-seller', [StoresController::class, 'index'])->name('store.index');

    // Register Seller
    Route::post('/seller-regist', [StoresController::class, 'createStore'])->name('store.submit');

    // Notif
    Route::post('/notifications/read-all', [DashboardController::class, 'readNotif']);

    // Admin Dashboard
    Route::middleware(['role:admin'])->group(function () {

        Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
        Route::get('/admin/seller-management', [DashboardController::class, 'sellerManagement'])->name('admin.seller-management');
        Route::patch('/admin/update-status-seller', [DashboardController::class, 'updateStoresStatus'])->name('admin.update-seller-status');
        Route::get('/admin/sellers/{id}/detail', [DashboardController::class, 'getDetailStore'])->name('admin.seller.detail');

        // Admin Category Management
        Route::prefix('admin/category')->name('admin.category.')->controller(CategoryController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/', 'store')->name('store');
            Route::patch('/{id}', 'update')->name('update');
            Route::delete('/{id}', 'destroy')->name('destroy');
        });

        Route::prefix('admin/users')->name('admin.users.')->controller(AdminUserController::class)->group(function () {
            Route::get('/',              'index')->name('index');    // GET  /admin/users
            Route::get('/{id}',          'show')->name('show');     // GET  /admin/users/{id}
            Route::patch('/{id}/suspend', 'suspend')->name('suspend');  // PATCH /admin/users/{id}/suspend
            Route::patch('/{id}/activate', 'activate')->name('activate'); // PATCH /admin/users/{id}/activate
            Route::delete('/{id}',       'destroy')->name('destroy');  // DELETE /admin/users/{id}
        });
    });

    // Seller Dashboard
    Route::middleware(['role:seller'])->group(function () {
        Route::get('/seller/dashboard', [SellerDashboardController::class, 'showDashboard'])
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

        Route::put('/surplus-product/update/{id}', [SurplusProductController::class, 'update'])->name('seller.surplus-update');
        Route::delete('/surplus-delete/{id}', [SurplusProductController::class, 'delete'])->name('seller.surplus-delete');

        Route::get('/dashboard/chart-data', [SellerDashboardController::class, 'chartData'])->name('seller.dashboard.chart-data');

        Route::prefix('seller/orders')->name('seller.orders.')->group(function () {
            Route::get('/', [SellerOrderController::class, 'index'])
                ->name('index');

            // Detail order
            Route::get('/{orderId}', [SellerOrderController::class, 'show'])
                ->name('show');

            // Tandai siap diambil 
            Route::patch('/{orderId}/ready', [SellerOrderController::class, 'markReady'])
                ->name('ready');
        });
    });
});


Route::post('midtrans/webhook', [MidtransWebhookController::class, 'handle']);

// Testing mailer 
// Route::get('/test-mail', function () {
//     \Mail::raw('Test email', function ($message) {
//         $message->to('test@example.com')
//             ->subject('Test Mailtrap');
//     });

//     return 'sent';
// });
