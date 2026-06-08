<?php

use App\Http\Controllers\Admin\AboutController;
use App\Http\Controllers\Admin\AnnoucementController;
use App\Http\Controllers\Admin\AttributeController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CategoriesController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EwalletController;
use App\Http\Controllers\Admin\GeneralSettingController;
use App\Http\Controllers\Admin\IntegrationController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PageContentController;
use App\Http\Controllers\Admin\PaymentGatewayController;
use App\Http\Controllers\Admin\PayoutController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\RatingController;
use App\Http\Controllers\Admin\SeoController;
use App\Http\Controllers\Admin\ShippingController;
use App\Http\Controllers\Admin\SiteSettingController;
use App\Http\Controllers\Admin\SocialLinksController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\VariantController;
use App\Http\Controllers\Frontend\AuthController;
use App\Http\Controllers\Frontend\CartController;
use App\Http\Controllers\Frontend\CheckoutController;
use App\Http\Controllers\Frontend\CompareController;
use App\Http\Controllers\Frontend\PasswordResetController;
use App\Http\Controllers\Frontend\VendorsController;
use App\Http\Controllers\Frontend\WishlistController;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\Vendor\VendorController;
use App\Http\Controllers\Webhooks\NowPaymentsWebhookController;
use App\Models\AboutContent;
use App\Models\frontend\Cart;
use App\Models\PageContent;
use App\Models\Product;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Route;

Route::get('/lw-test', function () {
    $path = storage_path('app/public/livewire-tmp');

    return [
        'readable' => is_readable($path),
        'writable' => is_writable($path),
    ];
});

// ========== 🔴 FIX: ADMIN ROUTES - PEHLE ==========
Route::prefix('admin')->name('admin.')->middleware(['auth', 'check.permission'])->group(function () {
    // Admin Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::controller(BannerController::class)->group(function () {
        Route::get('/banner', 'index')->name('banner');
        Route::get('/banner/create', 'create')->name('banner.create');
        Route::get('/banner/edit/{id}', 'edit')->name('banner.edit');
    });
    Route::controller(BrandController::class)->group(function () {
        Route::get('/brand', 'index')->name('brand');
        Route::get('/brand/create', 'create')->name('brand.create');
        Route::get('/brand/edit/{slug}', 'edit')->name('brand.edit');
    });
    Route::controller(CategoriesController::class)->group(function () {
        Route::get('/categories', 'index')->name('categories');
        Route::get('/categories/create', 'create')->name('categories.create');
        Route::get('/categories/edit/{url}', 'edit')->name('categories.edit');
    });
    Route::controller(AttributeController::class)->group(function () {
        Route::get('/attribute', 'index')->name('attribute');
        Route::get('/attribute/create', 'create')->name('attribute.create');
        Route::get('/attribute/edit/{slug}', 'edit')->name('attribute.edit');
    });
    Route::controller(VariantController::class)->group(function () {
        Route::get('/variant', 'index')->name('variant');
        Route::get('/variant/create', 'create')->name('variant.create');
        Route::get('/variant/edit/{slug}', 'edit')->name('variant.edit');
    });
    Route::controller(ProductController::class)->group(function () {
        Route::get('/product', 'index')->name('product');
        Route::get('/product/create', 'create')->name('product.create');
        Route::get('/product/edit/{id}-{slug}', 'edit')->name('product.edit');
    });
    Route::controller(CouponController::class)->group(function () {
        Route::get('/coupon', 'index')->name('coupon');
        Route::get('/coupon/create', 'create')->name('coupon.create');
        Route::get('/coupon/edit/{slug}', 'edit')->name('coupon.edit');
    });
    Route::controller(UserController::class)->group(function () {
        Route::get('/user', 'index')->name('user');
        Route::get('/user/create', 'create')->name('user.create');
        Route::get('/user/edit/{id}', 'edit')->name('user.edit');
        Route::get('/newsletter-subscriber', 'subIndex')->name('user.subscriber');
        Route::get('/inquiries', 'inquiriesIndex')->name('inquiries');
    });
    Route::controller(RatingController::class)->group(function () {
        Route::get('/ratings', 'index')->name('rating');
    });
    Route::controller(OrderController::class)->group(function () {
        Route::get('/orders', 'index')->name('orders');
        Route::get('/orders/{id}', 'indexdetail')->name('orders.detail');
    });
    Route::controller(AnnoucementController::class)->group(function () {
        Route::get('/annoucement', 'index')->name('annoucement');
        Route::get('/annoucement/create', 'create')->name('annoucement.create');
        Route::get('/annoucement/{id}', 'edit')->name('annoucement.edit');
    });
    Route::controller(PageContentController::class)->group(function () {
        Route::get('/page-content', 'index')->name('page-content');
        Route::get('/page-content/create', 'create')->name('page-content.create');
        Route::get('/page-content/{slug}', 'edit')->name('page-content.edit');
    });
    Route::controller(SeoController::class)->group(function () {
        Route::get('/seo', 'index')->name('seo');
        Route::get('/seo/create', 'create')->name('seo.create');
        Route::get('/seo/{id}', 'edit')->name('seo.edit');
    });
    Route::controller(SiteSettingController::class)->group(function () {
        Route::get('/site-setting', 'index')->name('site-setting');
    });
    Route::controller(GeneralSettingController::class)->group(function () {
        Route::get('/general-setting', 'index')->name('general-setting');
    });
    Route::controller(ShippingController::class)->group(function () {
        Route::get('/shipping', 'index')->name('shipping-setting');
    });
    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile-index', 'index')->name('profile-index');
    });
    Route::controller(PermissionController::class)->group(function () {
        Route::get('/role-index', 'index')->name('role-index');
        Route::get('/permission-index', 'permissionindex')->name('permission-index');
        Route::get('/permission-create', 'permissioncreate')->name('permission.create');
    });
    Route::controller(VendorController::class)->group(function () {
        Route::get('/vendor', 'index')->name('vendor');
        Route::get('/vendor/detail/{id}', 'detail')->name('vendor.detail');
        Route::get('/vendor/{id}/orders', 'orders')->name('vendor.orders');
    });
    Route::controller(EwalletController::class)->group(function () {
        Route::get('/e-wallet', 'index')->name('e-wallet');
    });
    Route::controller(PayoutController::class)->group(function () {
        Route::get('/payouts', 'index')->name('payouts');
    });

    Route::controller(SocialLinksController::class)->name('social-links.')->group(function () {
        Route::get('/social-links', 'index')->name('index');
        Route::get('/social-links/create', 'create')->name('create');
        Route::get('/social-links/edit/{id}', 'edit')->name('edit');
    });
    Route::controller(AboutController::class)->group(function () {
        Route::get('/about', 'index')->name('about');
    });

    Route::get('/integrations', [IntegrationController::class, 'index'])->name('integrations');
    Route::controller(PaymentGatewayController::class)->group(function () {
        Route::get('/payment-gateways', 'index')->name('payment-gateways');
    });
});

// ========== VENDOR ROUTES ==========
Route::prefix('vendor')->name('vendor.')->middleware(['vendor.blocked'])->group(function () {
    Route::get('/register', [VendorController::class, 'register'])->name('register');
    Route::post('/register', [VendorController::class, 'register'])->name('register.submit');
    Route::get('/login', [VendorController::class, 'login'])->name('login');
    Route::get('/reset-password/{token}', [VendorController::class, 'resetPassword'])->name('password.reset');
    Route::post('/reset-password', [VendorController::class, 'updatePassword'])->name('password.update');
});

// ========== EMAIL VERIFICATION ==========
Route::get('/email/verify/{id}/{hash}', function (Illuminate\Http\Request $request) {
    $user = User::findOrFail($request->id);
    if (! hash_equals(sha1($user->getEmailForVerification()), $request->hash)) {
        abort(403);
    }

    if (! $user->hasVerifiedEmail()) {
        $user->markEmailAsVerified();
        event(new Verified($user));
    }

    if ($user->hasRole('Vendor')) {
        return redirect()->route('vendor.login');
    }

    return redirect()->route('login');
})->middleware(['signed'])->name('verification.verify');

// ========== FRONTEND ROUTES ==========

Route::get('/quick-view/{id}', [FrontendController::class, 'quickView'])->name('quick.view');
Route::get('/', [FrontendController::class, 'index'])->name('home');
Route::get('contact', function () {
    return view('frontend.contact');
})->name('contact');

Route::post('/review/submit', [FrontendController::class, 'submitReview'])->name('review.submit');
Route::get('/product/{product:product_slug}', [FrontendController::class, 'show'])->name('product.details');

// ========== CART ROUTES ==========
Route::prefix('cart')->group(function () {
    Route::post('/add', [CartController::class, 'addToCart'])->name('cart.add');
    Route::post('/remove', [CartController::class, 'removeFromCart'])->name('cart.remove');
    Route::post('/update', [CartController::class, 'updateCart'])->name('cart.update');
    Route::get('/count', [CartController::class, 'getCartCount'])->name('cart.count');
    Route::get('/items', [CartController::class, 'getCartItems'])->name('cart.items');
    Route::post('/clear', [CartController::class, 'clearCart'])->name('cart.clear');
    Route::post('/apply-coupon', [CartController::class, 'applyCoupon'])->name('cart.apply-coupon');
    Route::post('/validate-coupon', [CartController::class, 'validateCoupon'])->name('cart.validate-coupon');
    Route::post('/remove-coupon-session', [CartController::class, 'removeCouponFromSession'])->name('cart.remove-coupon-session');
    Route::get('/applied-coupon-status', [CartController::class, 'getAppliedCouponStatus'])->name('cart.applied-coupon-status');
    Route::post('/calculate-shipping', [CartController::class, 'calculateShipping'])->name('cart.calculate-shipping');
    Route::get('/shipping-charges', [CartController::class, 'getShippingCharges'])->name('cart.shipping-charges');
    Route::get('/cart-total', [CartController::class, 'getCartTotalForCalculations'])->name('cart.total-for-calc');
});

// Checkout Routes
Route::prefix('checkout')->name('checkout.')->group(function () {
    Route::get('/', [CheckoutController::class, 'index'])->name('index');
    Route::post('/place-order', [CheckoutController::class, 'placeOrder'])->name('place.order');
    Route::get('/thank-you/{order_number}', [CheckoutController::class, 'thankyou'])->name('thankyou');
    Route::get('/payment/cancel/{order_number}', [CheckoutController::class, 'paymentCancel'])->name('payment.cancel');
    Route::get('/invoice/{order_number}', [CheckoutController::class, 'downloadInvoice'])->name('invoice');
    Route::get('/cart-data', [CheckoutController::class, 'getCheckoutCart'])->name('cart.data');
});

Route::post('/webhooks/nowpayments', NowPaymentsWebhookController::class)
    ->middleware('throttle:60,1')
    ->name('webhooks.nowpayments');
// Wishlist Routes
Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist');
Route::post('/wishlist/add', [WishlistController::class, 'addToWishlist'])->name('wishlist.add');
Route::post('/wishlist/remove', [WishlistController::class, 'removeFromWishlist'])->name('wishlist.remove');
Route::post('/wishlist/toggle', [WishlistController::class, 'toggleWishlist'])->name('wishlist.toggle');
Route::post('/wishlist/check', [WishlistController::class, 'checkWishlist'])->name('wishlist.check');
Route::get('/wishlist/count', [WishlistController::class, 'getWishlistCount'])->name('wishlist.count');
Route::get('/wishlist/items', [WishlistController::class, 'getWishlistItems'])->name('wishlist.items');
Route::get('/wishlist/immediate-count', [WishlistController::class, 'getImmediateWishlistCount'])->name('wishlist.immediate-count');

// routes/web.php mein ye routes add karo

// Compare Routes (Wishlist ki tarah exactly)
Route::get('/compare', [CompareController::class, 'index'])->name('compare');
Route::post('/compare/add', [CompareController::class, 'addToCompare'])->name('compare.add');
Route::post('/compare/remove', [CompareController::class, 'removeFromCompare'])->name('compare.remove');
Route::post('/compare/toggle', [CompareController::class, 'toggleCompare'])->name('compare.toggle');
Route::post('/compare/check', [CompareController::class, 'checkCompare'])->name('compare.check');
Route::get('/compare/count', [CompareController::class, 'getCompareCount'])->name('compare.count');
Route::get('/compare/items', [CompareController::class, 'getCompareItems'])->name('compare.items');

Route::get('/cart', [CartController::class, 'cartPage'])->name('cart.page');

// ========== SHOP ROUTE ==========
Route::get('/products', [ShopController::class, 'index'])->name('products');

Route::prefix('products')->name('category.')->group(function () {
    Route::get('/{slug1}/{slug2}/{slug3}', [ShopController::class, 'categoryHierarchy'])->name('level3');
    Route::get('/{slug1}/{slug2}', [ShopController::class, 'categoryHierarchy'])->name('level2');
    Route::get('/{slug}', [ShopController::class, 'categoryHierarchy'])->name('level1');
});
// User Auth Routes
Route::prefix('user')->name('user.')->group(function () {
    Route::get('/login', function () {
        return view('frontend.auth.login');
    })->name('login');

    Route::get('/register', function () {
        return view('frontend.auth.register');
    })->name('register');

    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/register', [AuthController::class, 'register'])->name('register');

    Route::middleware(['webuser'])->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
        Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
        Route::post('/profile/update', [AuthController::class, 'updateProfile'])->name('profile.update');
        Route::post('/password/change', [AuthController::class, 'changePassword'])->name('password.change');
        Route::get('/orders', [AuthController::class, 'orders'])->name('orders');

    });
});

// Password Reset Routes
Route::post('/send-reset-code', [PasswordResetController::class, 'sendResetCode'])->name('password.send-code');
Route::post('/reset-password', [PasswordResetController::class, 'verifyCodeAndReset'])->name('password.reset');

// Vendor Routes
Route::get('/vendors', [VendorsController::class, 'index'])->name('vendor.index');
Route::get('/vendor/{slug}', [VendorsController::class, 'show'])->name('vendor.show');

Route::post('/contact/submit', [FrontendController::class, 'submitInquiry'])->name('contact.submit');
Route::post('/newsletter/subscribe', [FrontendController::class, 'subscribe'])->name('newsletter.subscribe');
Route::post('/newsletter/unsubscribe', [FrontendController::class, 'unsubscribe'])->name('newsletter.unsubscribe');

Route::get('/trending-products', [FrontendController::class, 'getTrendingProducts'])->name('trending.products');

Route::get('/web-search', [FrontendController::class, 'webSearch'])->name('web.search');

Route::get('policy/{slug}', function ($slug) {
    $policy = PageContent::where('slug', $slug)
        ->where('status', '1')
        ->firstOrFail();

    return view('frontend.policy', compact('policy'));
})->name('policy.show');

Route::get('about', function () {
    $about = AboutContent::first();

    return view('frontend.about', compact('about'));
})->name('about');

// CSRF Token refresh endpoint (for auto-refresh)
Route::get('/refresh-csrf', function () {
    return response()->json([
        'token' => csrf_token(),
    ]);
})->name('csrf.refresh');

Route::post('/switch-currency', function () {
    $newCurrency = request('currency');
    $oldCurrency = session('user_currency', getGeneralSetting()->currency ?? 'PKR');

    // Save new currency in session
    session(['user_currency' => $newCurrency]);

    // Agar currency change hui hai
    if ($oldCurrency !== $newCurrency) {
        // Get all cart items
        if (auth()->check()) {
            $cartItems = Cart::where('user_id', auth()->id())->get();
        } else {
            $cartItems = Cart::where('session_id', session()->getId())->get();
        }

        foreach ($cartItems as $cartItem) {
            $product = Product::find($cartItem->product_id);
            if ($product) {
                // Direct convert from product price
                $cartItem->price = convertPrice($product->product_price);
                $cartItem->save();
            }
        }
    }

    return back();
})->name('switch.currency');

require __DIR__.'/auth.php';
