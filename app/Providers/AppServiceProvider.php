<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Rating;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

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
            'frontend.component.product-card-list'
        ], function ($view) {
            $data = $view->getData();
            
            // Products ko identify karo — different variable names handle
            $products = $data['products'] ?? $data['product'] ?? $data['prd'] ?? [];
            
            if (!empty($products)) {
                $productIds = [];
                foreach ($products as $product) {
                    if (is_object($product) && isset($product->id)) {
                        $productIds[] = $product->id;
                    }
                }
                
                if (!empty($productIds)) {
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