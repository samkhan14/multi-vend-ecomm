<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Vendor;
use App\Models\Category;
use App\Models\Product;
use App\Models\Rating;

class VendorsController extends Controller
{
public function index(Request $request)
{
    $query = Vendor::with(['user', 'products' => function($q) {
            $q->where('status', 1)->latest()->take(5);
        }])
        ->withCount(['products' => function($q) {
            $q->where('status', 1);
        }])
        ->where('status', 1)
        ->where('is_block', 0);

    if ($request->filled('search')) {
        $search = $request->search;
        $query->where('store_name', 'LIKE', "%{$search}%");
    }

    $sort = $request->get('sort', 'latest');
    switch ($sort) {
        case 'latest':
            $query->orderBy('created_at', 'desc');
            break;
        case 'oldest':
            $query->orderBy('created_at', 'asc');
            break;
        case 'name_asc':
            $query->orderBy('store_name', 'asc');
            break;
        case 'name_desc':
            $query->orderBy('store_name', 'desc');
            break;
    }

    $vendors = $query->paginate(12)->withQueryString();

    if ($request->ajax()) {
        // Vendors HTML with row class
        $vendorsHtml = '';
        if ($vendors->count() > 0) {
            $vendorsHtml = '<div class="row gy-4">' . 
                view('frontend.component.vendor-card', compact('vendors'))->render() . 
                '</div>';
        } else {
            $vendorsHtml = '<div class="col-12 text-center py-5"><p>No vendors found</p></div>';
        }
        
        // Pagination HTML
        $paginationHtml = '';
        if ($vendors->hasPages()) {
            $paginationHtml = '<ul class="pagination flex-center flex-wrap gap-16 mt-48">';
            
            if ($vendors->onFirstPage()) {
                $paginationHtml .= '<li class="page-item disabled"><span class="page-link h-64 w-64 flex-center text-xxl rounded-circle fw-medium text-gray-400 border border-gray-100"><i class="ph-bold ph-arrow-left"></i></span></li>';
            } else {
                $paginationHtml .= '<li class="page-item"><a class="page-link h-64 w-64 flex-center text-xxl rounded-circle fw-medium text-neutral-600 border border-gray-100" href="'.$vendors->previousPageUrl().'"><i class="ph-bold ph-arrow-left"></i></a></li>';
            }
            
            for ($i = 1; $i <= $vendors->lastPage(); $i++) {
                $activeClass = $i == $vendors->currentPage() ? 'active bg-main-600 text-white border-main-600' : 'text-neutral-600 border-gray-100';
                $paginationHtml .= '<li class="page-item"><a class="page-link h-64 w-64 flex-center text-md rounded-circle fw-medium border '.$activeClass.'" href="'.$vendors->url($i).'">'.str_pad($i, 2, '0', STR_PAD_LEFT).'</a></li>';
            }
            
            if ($vendors->hasMorePages()) {
                $paginationHtml .= '<li class="page-item"><a class="page-link h-64 w-64 flex-center text-xxl rounded-circle fw-medium text-neutral-600 border border-gray-100" href="'.$vendors->nextPageUrl().'"><i class="ph-bold ph-arrow-right"></i></a></li>';
            } else {
                $paginationHtml .= '<li class="page-item disabled"><span class="page-link h-64 w-64 flex-center text-xxl rounded-circle fw-medium text-gray-400 border border-gray-100"><i class="ph-bold ph-arrow-right"></i></span></li>';
            }
            
            $paginationHtml .= '</ul>';
        }

        return response()->json([
            'success' => true,
            'html' => $vendorsHtml,
            'pagination' => $paginationHtml,
            'total' => $vendors->total(),
            'from' => $vendors->firstItem(),
            'to' => $vendors->lastItem()
        ]);
    }

    return view('frontend.vendor', compact('vendors'));
}

    public function show($slug, Request $request)
    {
        $vendor = Vendor::where('store_slug', $slug)
            ->where('status', 1)
            ->where('is_block', 0)
            ->with(['user'])
            ->firstOrFail();

        $categories = Category::whereHas('products', function($q) use ($vendor) {
                $q->where('vendor_id', $vendor->id)->where('status', 1);
            })
            ->withCount(['products' => function($q) use ($vendor) {
                $q->where('vendor_id', $vendor->id)->where('status', 1);
            }])
            ->orderBy('category_name')
            ->get();

        $productsQuery = Product::with(['category', 'brand', 'images', 'vendor'])
            ->where('vendor_id', $vendor->id)
            ->where('status', 1);

        if ($request->filled('category')) {
            $productsQuery->where('category_id', $request->category);
        }

        if ($request->filled('price')) {
            $priceRange = explode('-', $request->price);
            if (count($priceRange) == 2) {
                $minPrice = (float) preg_replace('/[^0-9.]/', '', $priceRange[0]);
                $maxPrice = (float) preg_replace('/[^0-9.]/', '', $priceRange[1]);
                $productsQuery->whereBetween('product_price', [$minPrice, $maxPrice]);
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $productsQuery->where('product_name', 'LIKE', "%{$search}%");
        }

        $sort = $request->get('sort', 'latest');
        switch ($sort) {
            case 'latest':
                $productsQuery->orderBy('created_at', 'desc');
                break;
            case 'oldest':
                $productsQuery->orderBy('created_at', 'asc');
                break;
            case 'price_low':
                $productsQuery->orderBy('product_price', 'asc');
                break;
            case 'price_high':
                $productsQuery->orderBy('product_price', 'desc');
                break;
            case 'name_asc':
                $productsQuery->orderBy('product_name', 'asc');
                break;
            case 'name_desc':
                $productsQuery->orderBy('product_name', 'desc');
                break;
        }

        $products = $productsQuery->paginate(8)->withQueryString();

        $productIds = $products->pluck('id');
        $ratings = Rating::whereIn('product_id', $productIds)
            ->where('status', 'Active')
            ->selectRaw('product_id, AVG(rating) as avg_rating, COUNT(*) as total_reviews')
            ->groupBy('product_id')
            ->get()
            ->keyBy('product_id');

if ($request->ajax()) {
    $productsHtml = '';
    if ($products->count() > 0) {
        $productsHtml = '<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-3 row-cols-xl-4 row-cols-xxl-5 g-12">' . 
            view('frontend.component.product-card', [
                'products' => $products,
                'productRatings' => $ratings,
                'genralsetting' => $genralsetting ?? (object)['currency' => '$']
            ])->render() . 
            '</div>';
    } else {
        $productsHtml = '<div class="col-12 text-center py-5"><p>No products found</p></div>';
    }

    // 🔥 IMPORTANT: Pagination HTML generate karo
    $paginationHtml = '';
    if ($products->hasPages()) {
        $current = $products->currentPage();
        $last = $products->lastPage();
        $start = max(1, $current - 2);
        $end = min($last, $current + 2);
        
        $paginationHtml = '<ul class="pagination flex-center flex-wrap gap-16">';
        
        if ($start > 1) {
            $paginationHtml .= '<li class="page-item"><a class="page-link h-64 w-64 flex-center text-md rounded-8 fw-medium text-neutral-600 border border-gray-100" href="'.$products->url(1).'">01</a></li>';
            if ($start > 2) {
                $paginationHtml .= '<li class="page-item disabled"><span class="page-link h-64 w-64 flex-center text-md rounded-8 fw-medium text-neutral-600 border border-gray-100">...</span></li>';
            }
        }
        
        for ($page = $start; $page <= $end; $page++) {
            if ($page == $current) {
                $paginationHtml .= '<li class="page-item active"><span class="page-link h-64 w-64 flex-center text-md rounded-8 fw-medium text-white border border-main-600 bg-main-600">'.str_pad($page, 2, '0', STR_PAD_LEFT).'</span></li>';
            } else {
                $paginationHtml .= '<li class="page-item"><a class="page-link h-64 w-64 flex-center text-md rounded-8 fw-medium text-neutral-600 border border-gray-100" href="'.$products->url($page).'">'.str_pad($page, 2, '0', STR_PAD_LEFT).'</a></li>';
            }
        }
        
        if ($end < $last) {
            if ($end < $last - 1) {
                $paginationHtml .= '<li class="page-item disabled"><span class="page-link h-64 w-64 flex-center text-md rounded-8 fw-medium text-neutral-600 border border-gray-100">...</span></li>';
            }
            $paginationHtml .= '<li class="page-item"><a class="page-link h-64 w-64 flex-center text-md rounded-8 fw-medium text-neutral-600 border border-gray-100" href="'.$products->url($last).'">'.str_pad($last, 2, '0', STR_PAD_LEFT).'</a></li>';
        }
        
        $paginationHtml .= '</ul>';
    }

    return response()->json([
        'success' => true,
        'html' => $productsHtml,
        'pagination' => $paginationHtml,
        'total' => $products->total(),
        'from' => $products->firstItem(),
        'to' => $products->lastItem()
    ]);
}
        return view('frontend.vendor-details', compact('vendor', 'categories', 'products', 'ratings'));
    }
}