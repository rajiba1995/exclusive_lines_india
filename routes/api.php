<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\OrderController;

Route::prefix('customer')->group(function () {
    // User Registration Route
    Route::post('register', [AuthController::class, 'register']);

        // User Login Route
        Route::post('login', [AuthController::class, 'login']);
        Route::post('requestotp', [AuthController::class, 'requestotp']);
        Route::post('verifyOtp', [AuthController::class, 'verifyOtp']);
        Route::post('resetPassword', [AuthController::class, 'resetPassword']);

     // Protected routes for authenticated users
     Route::middleware(['auth.sanctum.custom'])->group(function () {
        // User Profile Route
        Route::get('profile', [AuthController::class, 'userProfile']);
        Route::get('home-page',[AuthController::class,'HomePage']);
        Route::get('banners', [AuthController::class, 'fetchBanners']);
        Route::get('faqs', [AuthController::class, 'fetchFaqs']);
        Route::get('product-list', [AuthController::class, 'ProductList']);
        Route::get('product-details/{id}', [AuthController::class, 'ProductDetails']);
        Route::get('product/filter', [AuthController::class, 'ProductFilter']);
        
        // Change Password Route
        Route::post('changePassword', [AuthController::class, 'changePassword']);
        Route::post('update-profile', [AuthController::class, 'updateProfile']);
        Route::get('document-status', [AuthController::class, 'DocumentStatus']);
        Route::post('update-document', [AuthController::class, 'updateDocument']);
        Route::get('revokeTokens', [AuthController::class, 'revokeTokens']);

        Route::get('offer-list', [AuthController::class,'OfferList']);
        Route::get('order-history', [AuthController::class,'OrderHistory']);
        Route::get('sell-order-history/{user_id}', [AuthController::class,'SellOrderHistory']);
        Route::get('rent-order-history/{user_id}', [AuthController::class,'RentOrderHistory']);
        Route::get('order-details/{order_id}', [AuthController::class, 'OrderDetails']);
        Route::get('company-policies', [AuthController::class, 'CompanyPolicy']);
        Route::get('company-policy/details/{id}', [AuthController::class, 'CompanyPolicyDetails']);
        Route::post('book-now', [AuthController::class, 'bookNow']);
        Route::post('booking-new-payment', [AuthController::class, 'bookingNewPayment']);
        Route::post('booking-renew-payment', [AuthController::class, 'bookingRenewPayment']);
        Route::get('booking-cancel/{order_id}', [AuthController::class, 'bookingCancel']);
        Route::get('my-active-subscription', [AuthController::class, 'myActiveSubscription']);

        Route::get('order/existing-payment-type', [OrderController::class, 'ExistingPaymentType']);
        Route::post('apply/coupon', [OrderController::class, 'ApplyCoupon']);
       
        Route::post('order/create', [OrderController::class, 'createOrder']);
        Route::get('payment/history', [AuthController::class, 'paymentHistory']);
    });
});
