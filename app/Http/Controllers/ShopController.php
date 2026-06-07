<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Rating;
use App\Models\Variant;
use App\Models\Brand;
use Carbon\Carbon;

class ShopController extends Controller
{
    /**
     * Handle hierarchical category URLs
     */
    public function categoryHierarchy(Request $request, $slug1 = null, $slug2 = null, $slug3 = null)
    {
        // Clear previous session
        session()->forget('current_category_hierarchy');
        
        // Determine which level we're at
        if ($slug3) {
            // Level 3: main-category/sub-category/sub-sub-category
            $mainCategory = Category::where('url', $slug1)->where('status', 1)->firstOrFail();
            $subCategory = Category::where('url', $slug2)
                ->where('parent_id', $mainCategory->id)
                ->where('status', 1)
                ->firstOrFail();
            $currentCategory = Category::where('url', $slug3)
                ->where('parent_id', $subCategory->id)
                ->where('status', 1)
                ->firstOrFail();
            
            // Store in session for breadcrumbs
            session(['current_category_hierarchy' => [
                'level1' => $mainCategory,
                'level2' => $subCategory,
                'level3' => $currentCategory,
                'url' => "$slug1/$slug2/$slug3"
            ]]);
            
        } elseif ($slug2) {
            // Level 2: main-category/sub-category
            $mainCategory = Category::where('url', $slug1)->where('status', 1)->firstOrFail();
            $currentCategory = Category::where('url', $slug2)
                ->where('parent_id', $mainCategory->id)
                ->where('status', 1)
                ->firstOrFail();
            
            session(['current_category_hierarchy' => [
                'level1' => $mainCategory,
                'level2' => $currentCategory,
                'url' => "$slug1/$slug2"
            ]]);
            
        } else {
            // Level 1: main-category
            $currentCategory = Category::where('url', $slug1)->where('status', 1)->firstOrFail();
            
            session(['current_category_hierarchy' => [
                'level1' => $currentCategory,
                'url' => $slug1
            ]]);
        }
        
        // Add category_url to request for breadcrumbs
        $request->merge(['category_url' => $currentCategory->url]);
        $request->merge(['from_category_hierarchy' => true]);
        
        // Call the existing shop index method
        return $this->index($request);
    }
    
    /**
     * Shop method - GLOBAL FILTERS with category URL only for breadcrumbs
     */
    public function index(Request $request)
    {
        $view = $request->get('view', 'grid');

        // ==========  FIX: Agar products page hai to session clear karo ==========
        if ($request->is('products') || $request->is('products/*')) {
            session()->forget('current_category_hierarchy');
        }

        // Get all active categories
        $mainCategories = Category::where('status', '1')
            ->where('level', '0')
            ->orderBy('category_name', 'asc')
            ->with(['children' => function($query) {
                $query->where('status', '1')
                    ->where('level', '1')
                    ->orderBy('category_name', 'asc');
            }])
            ->get();

        // Get all subcategories separately
        $subCategories = Category::where('status', '1')
            ->where('level', '1')
            ->orderBy('category_name', 'asc')
            ->get();

        // Start query
        $query = Product::with(['category', 'brand', 'images', 'vendor', 
                                'productVariants.variantValues.variant', 
                                'productVariants.variantValues.variantValue'])
            ->where('status', 1);

        // ========== CATEGORY HANDLING ==========
        $path = $request->path();
        $currentUrlCategory = null;
        $formCategorySelected = $request->filled('category');
        $selectedCategoryId = null; // 🔥 NEW: For radio button selection

        // AGAR FORM CATEGORY SELECTED HAI TO USKO PRIORITY DO
        if ($formCategorySelected) {
            $categoryId = $request->category;
            $selectedCategoryId = $categoryId; // 🔥 Store selected category ID
            $category = Category::find($categoryId);
            
            if ($category) {
                $currentUrlCategory = $category;
                
                if ($category->level == '0' || $category->level == 0) {
                    $allSubCategoryIds = $this->getAllSubCategoryIds($category->id);
                    $allCategoryIds = $allSubCategoryIds;
                    $allCategoryIds[] = $category->id;
                    $query->whereIn('category_id', array_unique($allCategoryIds));
                } elseif ($category->level == '1' || $category->level == 1) {
                    $allSubSubCategoryIds = $this->getAllSubCategoryIds($category->id);
                    $allCategoryIds = $allSubSubCategoryIds;
                    $allCategoryIds[] = $category->id;
                    $query->whereIn('category_id', array_unique($allCategoryIds));
                } else {
                    $query->where('category_id', $category->id);
                }
                
                // Build hierarchy for breadcrumbs
                $hierarchy = [];
                $tempCat = $category;
                $catStack = [];
                
                while ($tempCat) {
                    array_unshift($catStack, $tempCat);
                    $tempCat = $tempCat->parent;
                }
                
                $level = 1;
                foreach ($catStack as $cat) {
                    $hierarchy['level' . $level] = $cat;
                    $level++;
                }
                
                session(['current_category_hierarchy' => $hierarchy]);
            }
        } 
        // AGAR FORM CATEGORY NAHI HAI AUR PATH PRODUCTS NAHI HAI TO URL SE CATEGORY LO
        elseif ($path != 'products' && $path != 'shop' && $path != '/') {
            $slugs = explode('/', $path);
            $lastSlug = end($slugs);
            
            // Check karo ye category hai ya nahi
            $urlCategory = Category::where('url', $lastSlug)->where('status', 1)->first();
            
            if ($urlCategory) {
                $currentUrlCategory = $urlCategory;
                $selectedCategoryId = $urlCategory->id; // 🔥 Store selected category ID
                
                $allCategoryIds = $this->getAllSubCategoryIds($urlCategory->id);
                $allCategoryIds[] = $urlCategory->id;
                $query->whereIn('category_id', array_unique($allCategoryIds));
                
                // Build hierarchy for breadcrumbs
                $hierarchy = [];
                $tempCat = $urlCategory;
                $catStack = [];
                
                while ($tempCat) {
                    array_unshift($catStack, $tempCat);
                    $tempCat = $tempCat->parent;
                }
                
                $level = 1;
                foreach ($catStack as $cat) {
                    $hierarchy['level' . $level] = $cat;
                    $level++;
                }
                
                session(['current_category_hierarchy' => $hierarchy]);
            }
        }

        // ========== ALL FILTERS - GLOBAL ==========
        
        // Price filter - GLOBAL
        if ($request->filled('price')) {
            $priceRange = explode('-', $request->price);
            if (count($priceRange) == 2) {
                $minPrice = (float)$priceRange[0];
                $maxPrice = (float)$priceRange[1];
                
                $query->where(function($q) use ($minPrice, $maxPrice) {
                    $q->whereBetween('product_price', [$minPrice, $maxPrice])
                        ->orWhereHas('productVariants', function($variantQuery) use ($minPrice, $maxPrice) {
                            $variantQuery->whereBetween('price', [$minPrice, $maxPrice])
                                ->where('status', 1);
                        });
                });
            }
        }

        // Discount filter - GLOBAL
        if ($request->filled('discount')) {
            $discountValue = (int)$request->discount;
            $query->where('product_discount', '>=', $discountValue);
        }

        // Variant filters - GLOBAL
        if ($request->filled('variant_value_id')) {
            $variantValueIds = is_array($request->variant_value_id) 
                ? $request->variant_value_id 
                : [$request->variant_value_id];
            
            $query->whereHas('productVariants.variantValues', function($q) use ($variantValueIds) {
                $q->whereIn('variant_value_id', $variantValueIds);
            });
        }

        // Brand filter - GLOBAL
        if ($request->filled('brand')) {
            $brandId = $request->brand;
            $query->where('brand_id', $brandId);
        }

        // In Stock filter - GLOBAL
        if ($request->filled('stock')) {
            if ($request->stock == 'in_stock') {
                $query->where(function($q) {
                    $q->where('stock', '>', 0)
                        ->orWhere('stock_status', 'In Stock')
                        ->orWhereHas('productVariants', function($variantQuery) {
                            $variantQuery->where('stock', '>', 0);
                        });
                });
            }
        }

        // Search filter - GLOBAL
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('product_name', 'LIKE', "%{$search}%")
                    ->orWhere('short_description', 'LIKE', "%{$search}%")
                    ->orWhere('long_description', 'LIKE', "%{$search}%")
                    ->orWhere('product_code', 'LIKE', "%{$search}%");
            });
        }

        // Sort filter
        $sort = $request->get('sort', 'default');
        switch ($sort) {
            case 'popularity':
                $query->orderBy('is_featured', 'desc')
                    ->orderBy('created_at', 'desc');
                break;
            case 'rating':
                $query->select('products.*')
                    ->selectSub(function($subquery) {
                        $subquery->selectRaw('COALESCE(AVG(rating), 0)')
                            ->from('ratings')
                            ->whereColumn('ratings.product_id', 'products.id')
                            ->where('ratings.status', 'Active');
                    }, 'average_rating')
                    ->orderBy('average_rating', 'desc')
                    ->orderBy('products.created_at', 'desc');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'price_low':
                $query->select('products.*')
                    ->selectRaw('
                        CASE 
                            WHEN product_discount > 0 
                            THEN product_price * (1 - product_discount / 100)
                            ELSE product_price
                        END as calculated_price
                    ')
                    ->orderBy('calculated_price', 'asc')
                    ->orderBy('products.created_at', 'desc');
                break;
            case 'price_high':
                $query->select('products.*')
                    ->selectRaw('
                        CASE 
                            WHEN product_discount > 0 
                            THEN product_price * (1 - product_discount / 100)
                            ELSE product_price
                        END as calculated_price
                    ')
                    ->orderBy('calculated_price', 'desc')
                    ->orderBy('products.created_at', 'desc');
                break;
            case 'name_asc':
                $query->orderBy('product_name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('product_name', 'desc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        // Paginate results
        $products = $query->paginate($view === 'list' ? 6 : 8)->withQueryString();
        
        // Get ratings
        $productIds = $products->pluck('id');
        $ratings = Rating::whereIn('product_id', $productIds)
            ->where('status', 'Active')
            ->selectRaw('product_id, AVG(rating) as avg_rating, COUNT(*) as total_reviews')
            ->groupBy('product_id')
            ->get()
            ->keyBy('product_id');

        // Get all brands - GLOBAL
        $brands = Brand::where('status', 1)
            ->orderBy('name', 'asc')
            ->get();

        // Get all variants with their values - GLOBAL
        $allVariants = Variant::with(['variantValues' => function($query) {
            $query->where('status', 1)
                ->orderBy('value', 'asc');
        }])->where('status', 1)
            ->orderBy('name', 'asc')
            ->get();

        // Generate breadcrumbs
        $breadcrumbs = $this->generateBreadcrumbs($request);
        
        // Current category for display
        $currentCategory = $currentUrlCategory ?? null;

        if ($request->ajax()) {
            if ($view === 'grid') {
                $productsHtml = '<div class="row gy-4">' . 
                    view('frontend.component.product-card-shop', [
                        'products' => $products,
                        'productRatings' => $ratings,
                        'genralsetting' => $genralsetting ?? (object)['currency' => '$']
                    ])->render() . 
                    '</div>';
            } else {
                $productsHtml = '<div class="row gy-4">';
                foreach ($products as $product) {
                    $productsHtml .= '<div class="col-lg-6 col-md-12">' . 
                        view('frontend.component.product-card-list', [
                            'product' => $product,
                            'ratings' => $ratings
                        ])->render() . 
                        '</div>';
                }
                $productsHtml .= '</div>';
            }
            
            return response()->json([
                'success' => true,
                'html' => $productsHtml,
                'total' => $products->total(),
                'stats' => [
                    'from' => $products->firstItem(),
                    'to' => $products->lastItem(),
                    'total' => $products->total()
                ]
            ]);
        }
        
        // 🔥 IMPORTANT: Pass selectedCategoryId to blade
        return view('frontend.shop', compact(
            'products', 
            'mainCategories',
            'subCategories',
            'ratings',
            'allVariants',
            'brands',
            'view',
            'breadcrumbs',
            'currentCategory',
            'selectedCategoryId' 
        ));
    }

    /**
     * Generate breadcrumbs based on URL hierarchy
     */
    private function generateBreadcrumbs(Request $request)
    {
        $breadcrumbs = [
            ['name' => 'Home', 'url' => route('home'), 'active' => false]
        ];
        
        // Agar products page hai to sirf "Products" dikhao
        if ($request->is('products') || $request->is('products/*')) {
            // Check karo ki ye category page hai ya nahi
            $path = $request->path();
            $slugs = explode('/', $path);
            
            // Agar sirf /products hai to
            if (count($slugs) == 1 && $slugs[0] == 'products') {
                $breadcrumbs[] = [
                    'name' => 'Products',
                    'url' => route('products'),
                    'active' => true
                ];
                return $breadcrumbs;
            }
            
            // Agar category page hai to session se lo
            if (session()->has('current_category_hierarchy')) {
                $hierarchy = session('current_category_hierarchy');
                
                if (isset($hierarchy['level1'])) {
                    $url = '/products/' . $hierarchy['level1']->url;
                    $breadcrumbs[] = [
                        'name' => $hierarchy['level1']->category_name,
                        'url' => $url,
                        'active' => !isset($hierarchy['level2'])
                    ];
                }
                
                if (isset($hierarchy['level2'])) {
                    $url = '/products/' . $hierarchy['level1']->url . '/' . $hierarchy['level2']->url;
                    $breadcrumbs[] = [
                        'name' => $hierarchy['level2']->category_name,
                        'url' => $url,
                        'active' => !isset($hierarchy['level3'])
                    ];
                }
                
                if (isset($hierarchy['level3'])) {
                    $url = '/products/' . $hierarchy['level1']->url . '/' . $hierarchy['level2']->url . '/' . $hierarchy['level3']->url;
                    $breadcrumbs[] = [
                        'name' => $hierarchy['level3']->category_name,
                        'url' => $url,
                        'active' => true
                    ];
                }
            }
        } else {
            // Non-products pages ke liye
            if (session()->has('current_category_hierarchy')) {
                $hierarchy = session('current_category_hierarchy');
                
                if (isset($hierarchy['level1'])) {
                    $url = '/' . $hierarchy['level1']->url;
                    $breadcrumbs[] = [
                        'name' => $hierarchy['level1']->category_name,
                        'url' => $url,
                        'active' => !isset($hierarchy['level2'])
                    ];
                }
                
                if (isset($hierarchy['level2'])) {
                    $url = '/' . $hierarchy['level1']->url . '/' . $hierarchy['level2']->url;
                    $breadcrumbs[] = [
                        'name' => $hierarchy['level2']->category_name,
                        'url' => $url,
                        'active' => !isset($hierarchy['level3'])
                    ];
                }
                
                if (isset($hierarchy['level3'])) {
                    $url = '/' . $hierarchy['level1']->url . '/' . $hierarchy['level2']->url . '/' . $hierarchy['level3']->url;
                    $breadcrumbs[] = [
                        'name' => $hierarchy['level3']->category_name,
                        'url' => $url,
                        'active' => true
                    ];
                }
            }
        }
        
        return $breadcrumbs;
    }

    /**
     * Build category path for breadcrumbs
     */
    private function buildCategoryPath($category, &$breadcrumbs, $urlParts = [])
    {
        array_unshift($urlParts, $category->url);
        
        if ($category->parent) {
            $this->buildCategoryPath($category->parent, $breadcrumbs, $urlParts);
        }
        
        $breadcrumbs[] = [
            'name' => $category->category_name,
            'url' => '/' . implode('/', $urlParts),
            'active' => !$category->parent && !$category->children->count()
        ];
    }

    /**
     * Helper function to get all subcategory IDs recursively
     */
    private function getAllSubCategoryIds($categoryId)
    {
        $ids = [];
        $subCategories = Category::where('parent_id', $categoryId)
            ->where('status', 1)
            ->get();
        
        foreach ($subCategories as $subCat) {
            $ids[] = $subCat->id;
            // Recursively get sub-subcategories
            $childIds = $this->getAllSubCategoryIds($subCat->id);
            $ids = array_merge($ids, $childIds);
        }
        
        return $ids;
    }
}