<?php

namespace App\Providers;

use App\Contracts\Payment\PaymentGatewayInterface;
use App\Models\Rating;
use App\Services\Payment\Gateways\CodGateway;
use App\Services\Payment\Gateways\NowPaymentsGateway;
use App\Services\Payment\PaymentGatewayManager;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(PaymentGatewayManager::class, function ($app) {
            return new PaymentGatewayManager([
                $app->make(CodGateway::class),
                $app->make(NowPaymentsGateway::class),
            ]);
        });

        $this->app->tag([
            CodGateway::class,
            NowPaymentsGateway::class,
        ], PaymentGatewayInterface::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::before(function ($user, $ability) {
            if ($user && method_exists($user, 'hasRole') && $user->hasRole('Super Admin')) {
                return true;
            }

            return null;
        });

        View::composer('frontend.*', function ($view) {
            $view->with('siteSetting', getSiteSetting());
            $view->with('genralsetting', getGeneralSetting());
            $view->with('announcements', getAnnouncements());
            $view->with('navbarCategories', getNavbarCategories());
            $view->with('footerName', getFooterName());
            $view->with('dynamicSeo', getSeoMetadata());
            $view->with('aboutExcerpt', getAboutExcerpt(10));
        });

        View::composer([
            'frontend.component.product-card',
            'frontend.component.product-card-shop',
            'frontend.component.product-card-list',
        ], function ($view) {
            $data = $view->getData();

            // Products ko identify karo — different variable names handle
            $products = $data['products'] ?? $data['product'] ?? $data['prd'] ?? [];

            if (! empty($products)) {
                $productIds = [];
                foreach ($products as $product) {
                    if (is_object($product) && isset($product->id)) {
                        $productIds[] = $product->id;
                    }
                }

                if (! empty($productIds)) {
                    $ratings = Rating::whereIn('product_id', $productIds)
                        ->where('status', 1)
                        ->get()
                        ->groupBy('product_id')
                        ->toArray();

                    $view->with('productRatings', $ratings);
                }
            }
        });
    }
}
