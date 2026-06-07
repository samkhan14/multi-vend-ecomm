<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Category;
use App\Models\Product;
use App\Models\Rating;
use Illuminate\Http\Request;
use App\Models\GeneralSetting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use App\Models\Inquiries;
use Illuminate\Support\Facades\Log;
use App\Models\NewsletterSubscriber;
use App\Models\Brand;

class FrontendController extends Controller
{
public function index()
{
    $herobaner = Banner::where('type', 'Main Hero Banner')
        ->where('status', true)
        ->orderBy('created_at', 'desc')
        ->get();

    $bannerCategories = Category::where('level', 0)
        ->where('status', 1)
        ->where('banner_status', 1)
        ->whereNotNull('category_banner')
        ->orderBy('category_name')
        ->limit(2)
        ->get();


    $allProducts = Product::with('vendor')
        ->where('status', 1)
        ->where('stock', '>', 0)
        ->limit(50)   
        ->get();
    
    // Calculate sales count for each product from OrderItem table
    $productsWithSales = $allProducts->map(function($product) {
        $product->sales_count = \App\Models\OrderItem::where('product_id', $product->id)
            ->whereHas('order', function($query) {
                $query->where('status', 'completed'); 
            })
            ->count();
        
        return $product;
    });
    
    // Calculate trending and oldest logic
    $soldProducts = $productsWithSales->filter(function($product) {
        return $product->sales_count > 0;
    });
    
    $soldProductsCount = $soldProducts->count();
    $trendingCount = min($soldProductsCount, 8);
    $oldestCount = 8 - $trendingCount;
    
    // Get trending products (highest sales_count)
    $trendingProducts = collect();
    if ($trendingCount > 0) {
        $trendingProducts = $soldProducts
            ->sortByDesc('sales_count')
            ->take($trendingCount);
    }
    
    $oldestProducts = collect();
    if ($oldestCount > 0) {
        $trendingIds = $trendingProducts->pluck('id')->toArray();
        $oldestProducts = Product::with('vendor')
            ->where('status', 1)
            ->where('stock', '>', 0)
            ->when(!empty($trendingIds), function($query) use ($trendingIds) {
                $query->whereNotIn('id', $trendingIds);
            })
            ->orderBy('created_at', 'asc')
            ->take($oldestCount)
            ->get();
    }
    
    // Combine trending and oldest products
    $products = $trendingProducts->concat($oldestProducts);
    
    // Check if there are more trending products for "View More" button
    $totalTrendingProducts = $soldProducts->count();
    $hasMoreTrending = $totalTrendingProducts > 8;
    
    // Featured products (limit 8)
    $featuredProducts = Product::with('vendor')
        ->where('status', 1)
        ->where('is_featured', 1)
        ->latest()
        ->limit(8)  
        ->get();
    
    // On sale products (limit 8)
    $currentDate = Carbon::now();
    $onSaleProducts = Product::with('vendor')
        ->where('status', 1)
        ->whereNotNull('sale_price')
        ->where(function($query) use ($currentDate) {
            $query->where(function($q) use ($currentDate) {
                $q->where('sale_start_date', '<=', $currentDate)
                  ->orWhereNull('sale_start_date');
            })->where(function($q) use ($currentDate) {
                $q->where('sale_end_date', '>=', $currentDate)
                  ->orWhereNull('sale_end_date');
            });
        })
        ->latest()
        ->limit(8)   
        ->get();

    $middlebaner = Banner::where('type', 'Middle Banner')
        ->where('status', true)
        ->orderBy('created_at', 'desc')
        ->limit(3)
        ->get();
    

    $offerBanner = Banner::activeDatedOffer()->first();
    
 
    if (!$offerBanner) {
        $offerBanner = Banner::defaultOffer()->first();
    }

    $allProductsForPopular = Product::with('vendor')
        ->where('status', 1)
        ->where('stock', '>', 0)
        ->limit(50)   
        ->get();
    
    // Calculate popularity score for each product
    $productsWithScore = $allProductsForPopular->map(function($product) {
        $salesCount = \App\Models\OrderItem::where('product_id', $product->id)
            ->whereHas('order', function($query) {
                $query->where('status', 'completed');
            })
            ->count();
        
        $salesWeight = 10;
        $viewWeight = 1;
        $interactionWeight = 2;
        
        $baseScore = ($salesCount * $salesWeight) + 
                     ($product->view_count * $viewWeight) + 
                     ($product->interaction_count * $interactionWeight);
        
        $recentActivityBoost = 0;
        
        $recentOrder = \App\Models\OrderItem::where('product_id', $product->id)
            ->whereHas('order', function($query) {
                $query->where('status', 'completed');
            })
            ->where('created_at', '>=', now()->subDays(7))
            ->exists();
        
        if ($recentOrder) {
            $recentActivityBoost += 20;
        }
        
        if ($product->updated_at && $product->updated_at >= now()->subDays(7)) {
            $recentActivityBoost += 5;
        }
        
        $product->popularity_score = $baseScore + $recentActivityBoost;
        
        return $product;
    });
    
    // Sort by popularity score and take top 8
    $popularProducts = $productsWithScore
        ->sortByDesc('popularity_score')
        ->take(8)
        ->values();

    $brands = Brand::where('status', 1)
                   ->orderBy('name', 'asc')
                   ->limit(20)
                   ->get();

    return view('frontend.index', compact(
        'herobaner',
        'bannerCategories',
        'products',
        'featuredProducts',
        'onSaleProducts',
        'hasMoreTrending',
        'middlebaner',
        'offerBanner',
        'popularProducts',
        'brands'
    ));
}




public function getTrendingProducts(Request $request)
{
    $page = $request->get('page', 1);
    $perPage = 8;
    
    // Get all products with their sales count
    $products = Product::with('vendor')
        ->where('status', 1)
        ->where('stock', '>', 0)
        ->get();
    
    // Calculate sales count for each product
    $productsWithSales = $products->map(function($product) {
        $product->sales_count = \App\Models\OrderItem::where('product_id', $product->id)
            ->whereHas('order', function($query) {
                $query->where('status', 'completed');
            })
            ->count();
        return $product;
    });
    
    $trendingProducts = $productsWithSales
        ->filter(function($product) {
            return $product->sales_count > 0;
        })
        ->sortByDesc('sales_count')
        ->values();
    
    $total = $trendingProducts->count();
    $offset = ($page - 1) * $perPage;
    $items = $trendingProducts->slice($offset, $perPage)->values();
    
    $lastPage = ceil($total / $perPage);
    
    $html = view('frontend.component.product-card', [
        'products' => $items
    ])->render();
    
    return response()->json([
        'html' => $html,
        'current_page' => $page,
        'last_page' => $lastPage,
        'has_more' => $page < $lastPage
    ]);
}

    private function hasActiveSale($product, $currentDate)
    {
        if (!$product->sale_price) {
            return false;
        }
        
        $saleStart = $product->sale_start_date ? Carbon::parse($product->sale_start_date) : null;
        $saleEnd = $product->sale_end_date ? Carbon::parse($product->sale_end_date) : null;
        
        return (!$saleStart || $currentDate >= $saleStart) && 
               (!$saleEnd || $currentDate <= $saleEnd);
    }

public function quickView($id)
{
    $product = Product::with([
        'productVariants.images', 
        'productVariants.variantValues.variant',
        'productVariants.variantValues.variantValue',
        'images',
        'category',
        'brand',
        'vendor'
    ])->findOrFail($id);
    
    $colorVariants = collect();
    $otherVariants = collect();
    $variantGroups = [];
    
    foreach ($product->productVariants as $variant) {
        $firstVariantValue = $variant->variantValues->first();
        $variantType = $firstVariantValue && $firstVariantValue->variant 
            ? $firstVariantValue->variant->name 
            : 'Unknown';
        
        if (!isset($variantGroups[$variantType])) {
            $variantGroups[$variantType] = collect();
        }
        $variantGroups[$variantType]->push($variant);
        
        if (strtolower($variantType) == 'color') {
            $colorVariants->push($variant);
        } else {
            $otherVariants->push($variant);
        }
    }
    
    $averageRating = Rating::where('product_id', $product->id)
        ->where('status', 1)
        ->avg('rating') ?? 0;
    
    $totalReviews = Rating::where('product_id', $product->id)
        ->where('status', 1)
        ->count();
    
    return response()->json([
        'success' => true,
        'html' => view('frontend.component.quick-view-content', compact(
            'product', 
            'averageRating', 
            'totalReviews',
            'colorVariants',
            'otherVariants',
            'variantGroups'
        ))->render(),
    ]);
}

public function show($slug)
{
    $product = Product::where('product_slug', $slug)
        ->where('status', 1)
        ->with([
            'productVariants.images', 
            'productVariants.variantValues.variant',
            'productVariants.variantValues.variantValue',
            'category', 
            'brand', 
            'images',
            'vendor'
        ])
        ->first();
    
    if (!$product) {
        abort(404, 'Product not found');
    }
    $product->increment('view_count');

    
    $colorVariants = collect();
    $otherVariants = collect();
    $variantGroups = [];
    
    foreach ($product->productVariants as $variant) {
        $firstVariantValue = $variant->variantValues->first();
        $variantType = $firstVariantValue && $firstVariantValue->variant 
            ? $firstVariantValue->variant->name 
            : 'Unknown';
        
        if (!isset($variantGroups[$variantType])) {
            $variantGroups[$variantType] = collect();
        }
        $variantGroups[$variantType]->push($variant);
        
        if (strtolower($variantType) == 'color') {
            $colorVariants->push($variant);
        } else {
            $otherVariants->push($variant);
        }
    }
    
$relatedProducts = Product::with(['images', 'vendor'])
    ->where('category_id', $product->category_id)
    ->where('id', '!=', $product->id)
    ->where('status', 1)
    ->limit(4)
    ->get();


    
$ratingStats = Rating::where('product_id', $product->id)
    ->where('status', 1)
    ->selectRaw('COUNT(*) as total, AVG(rating) as average')
    ->first();

$reviewCount = $ratingStats->total;
$averageRating = round($ratingStats->average ?? 0, 1);

$reviews = Rating::where('product_id', $product->id)
    ->where('status', 1)
    ->orderBy('created_at', 'desc')
    ->paginate(5);

$totalReviews = $reviews->total();

    return view('frontend.products-details', compact(
        'product', 
        'relatedProducts',
        'reviewCount',
        'reviews',
        'averageRating',
        'totalReviews',
        'colorVariants',
        'otherVariants',
        'variantGroups'
    ));
}

    public function submitReview(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'required|string|min:5',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $existing = Rating::where('product_id', $request->product_id)
                ->where('email', $request->email)
                ->first();
                
            if ($existing) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have already reviewed this product.'
                ]);
            }

            $review = new Rating();
            $review->user_id = auth()->check() ? auth()->id() : null;
            $review->product_id = $request->product_id;
            $review->rating = $request->rating;
            $review->review = $request->review;
            $review->name = $request->name;
            $review->email = $request->email;
            $review->status = 1; 
            $review->save();

            return response()->json([
                'success' => true,
                'message' => 'Thank you! For your review .'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
   public function submitInquiry(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'phone' => 'required|string|max:20',
        'subject' => 'required|string|max:255',
        'message' => 'required|string|min:10',
    ]);

    try {
        Inquiries::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'company_name' => $request->company_name ?? null,
            'status' => 'pending' 
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Your message has been sent successfully!'
        ]);

    } catch (\Exception $e) {
        Log::error('Inquiry submission failed: ' . $e->getMessage());

        return response()->json([
            'success' => false,
            'message' => 'Something went wrong. Please try again.'
        ], 500);
    }
}

public function subscribe(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Please enter a valid email address.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $email = $request->email;
            
            $existingSubscriber = NewsletterSubscriber::findByEmail($email);
            
            if ($existingSubscriber) {
                if ($existingSubscriber->status === NewsletterSubscriber::STATUS_SUBSCRIBED) {
                    return response()->json([
                        'success' => false,
                        'message' => 'This email is already subscribed to our newsletter.'
                    ], 409);
                } else {
                    $existingSubscriber->update([
                        'status' => NewsletterSubscriber::STATUS_SUBSCRIBED,
                        'ip_address' => $request->ip(),
                        'user_agent' => $request->userAgent()
                    ]);
                    
                    return response()->json([
                        'success' => true,
                        'message' => 'Welcome back! You have been resubscribed to our newsletter.'
                    ]);
                }
            }

            NewsletterSubscriber::create([
                'email' => $email,
                'status' => NewsletterSubscriber::STATUS_SUBSCRIBED,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Thank you for subscribing to our newsletter!'
            ]);

        } catch (\Exception $e) {
            Log::error('Newsletter subscription failed: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. Please try again later.'
            ], 500);
        }
    }

    /**
     * Unsubscribe from newsletter
     */
    public function unsubscribe(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Please enter a valid email address.'
            ], 422);
        }

        try {
            $subscriber = NewsletterSubscriber::findByEmail($request->email);

            if (!$subscriber) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email not found in our subscription list.'
                ], 404);
            }

            if ($subscriber->status === NewsletterSubscriber::STATUS_UNSUBSCRIBED) {
                return response()->json([
                    'success' => false,
                    'message' => 'This email is already unsubscribed.'
                ], 409);
            }

            $subscriber->update([
                'status' => NewsletterSubscriber::STATUS_UNSUBSCRIBED
            ]);

            return response()->json([
                'success' => true,
                'message' => 'You have been unsubscribed successfully.'
            ]);

        } catch (\Exception $e) {
            Log::error('Newsletter unsubscribe failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. Please try again later.'
            ], 500);
        }
    }

    public function webSearch(Request $request)
{
    $query = trim($request->get('q', ''));
    
    if (strlen($query) < 2) {
        return response()->json(['products' => []]);
    }
    
    // Search products only
    $products = Product::with('vendor')
        ->where('status', 1)
        ->where('stock', '>', 0)
        ->where('product_name', 'LIKE', "%{$query}%")
        ->limit(8)
        ->get();
    
    return response()->json([
        'products' => $products
    ]);
}
}