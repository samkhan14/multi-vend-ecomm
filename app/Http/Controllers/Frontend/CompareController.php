<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\frontend\Compare;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CompareController extends Controller
{
    /**
     * Show compare page
     */

public function index()
{
    $sessionId = Session::getId();
    
    if (Auth::check()) {
        $compares = Compare::with(['product.category', 'product.brand', 'product.vendor'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();
    } else {
        $compares = Compare::with(['product.category', 'product.brand', 'product.vendor']) 
            ->where('session_id', $sessionId)
            ->orderBy('created_at', 'desc')
            ->get();
    }
    
    $productIds = $compares->pluck('product_id')->toArray();
    $productRatings = [];
    
    if (!empty($productIds)) {
        $reviews = \App\Models\Rating::whereIn('product_id', $productIds)
            ->select('product_id', 'rating')
            ->get();
            
        foreach ($reviews as $review) {
            $productRatings[$review->product_id][] = ['rating' => $review->rating];
        }
    }
    
    return view('frontend.compare', compact('compares', 'productRatings'));
}
    
    /**
     * Add product to compare
     */
    public function addToCompare(Request $request)
    {
        try {
            $request->validate([
                'product_id' => 'required|exists:products,id'
            ]);
            
            $productId = $request->product_id;
            $sessionId = Session::getId();
            
            // Check if product exists
            $product = Product::find($productId);
            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found'
                ]);
            }
            
            $query = Compare::where('product_id', $productId);
            
            if (Auth::check()) {
                $query->where('user_id', Auth::id());
            } else {
                $query->where('session_id', $sessionId);
            }
            
            if ($query->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product is already in your compare list'
                ]);
            }
            
            $countQuery = Compare::query();
            if (Auth::check()) {
                $countQuery->where('user_id', Auth::id());
            } else {
                $countQuery->where('session_id', $sessionId);
            }
            
            if ($countQuery->count() >= 4) {
                return response()->json([
                    'success' => false,
                    'message' => 'You can compare up to 4 products only'
                ]);
            }
            
            Compare::create([
                'product_id' => $productId,
                'user_id' => Auth::check() ? Auth::id() : null,
                'session_id' => Auth::check() ? null : $sessionId
            ]);
            
            $count = $this->getCompareCountData();
            
            return response()->json([
                'success' => true,
                'message' => 'Product added to compare list successfully',
                'count' => $count
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Remove from compare
     */
    public function removeFromCompare(Request $request)
    {
        try {
            $request->validate([
                'product_id' => 'required|exists:products,id'
            ]);
            
            $productId = $request->product_id;
            $sessionId = Session::getId();
            
            $query = Compare::where('product_id', $productId);
            
            if (Auth::check()) {
                $query->where('user_id', Auth::id());
            } else {
                $query->where('session_id', $sessionId);
            }
            
            $query->delete();
            
            // Get compare count
            $count = $this->getCompareCountData();
            
            return response()->json([
                'success' => true,
                'message' => 'Product removed from compare list',
                'count' => $count
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Toggle compare (add/remove)
     */
    public function toggleCompare(Request $request)
    {
        try {
            $request->validate([
                'product_id' => 'required|exists:products,id'
            ]);
            
            $productId = $request->product_id;
            $sessionId = Session::getId();
            
            // Check if exists
            $query = Compare::where('product_id', $productId);
            
            if (Auth::check()) {
                $query->where('user_id', Auth::id());
            } else {
                $query->where('session_id', $sessionId);
            }
            
            $compareItem = $query->first();
            
            if ($compareItem) {
                // Remove
                $compareItem->delete();
                $inCompare = false;
                $message = 'Product removed from compare list';
            } else {
                // Check maximum compare limit
                $countQuery = Compare::query();
                if (Auth::check()) {
                    $countQuery->where('user_id', Auth::id());
                } else {
                    $countQuery->where('session_id', $sessionId);
                }
                
                if ($countQuery->count() >= 4) {
                    return response()->json([
                        'success' => false,
                        'message' => 'You can compare up to 4 products only'
                    ]);
                }
                
                // Add
                Compare::create([
                    'product_id' => $productId,
                    'user_id' => Auth::check() ? Auth::id() : null,
                    'session_id' => Auth::check() ? null : $sessionId
                ]);
                $inCompare = true;
                $message = 'Product added to compare list';
            }
            
            // Get count
            $count = $this->getCompareCountData();
            
            return response()->json([
                'success' => true,
                'message' => $message,
                'count' => $count,
                'in_compare' => $inCompare
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Check if product is in compare
     */
    public function checkCompare(Request $request)
    {
        try {
            $request->validate([
                'product_id' => 'required|exists:products,id'
            ]);
            
            $productId = $request->product_id;
            $sessionId = Session::getId();
            
            $query = Compare::where('product_id', $productId);
            
            if (Auth::check()) {
                $query->where('user_id', Auth::id());
            } else {
                $query->where('session_id', $sessionId);
            }
            
            $inCompare = $query->exists();
            
            return response()->json([
                'success' => true,
                'in_compare' => $inCompare
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Get compare count for AJAX
     */
    public function getCompareCount(Request $request = null)
    {
        try {
            $count = $this->getCompareCountData();
            
            return response()->json([
                'success' => true,
                'count' => $count
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'count' => 0,
                'message' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Get compare count data
     */
    private function getCompareCountData()
    {
        $sessionId = Session::getId();
        
        if (Auth::check()) {
            return Compare::where('user_id', Auth::id())->count();
        } else {
            return Compare::where('session_id', $sessionId)->count();
        }
    }
    
    /**
     * Get compare items for AJAX
     */
    public function getCompareItems()
    {
        $sessionId = Session::getId();
        
        if (Auth::check()) {
            $compares = Compare::with('product')
                ->where('user_id', Auth::id())
                ->get();
        } else {
            $compares = Compare::with('product')
                ->where('session_id', $sessionId)
                ->get();
        }
        
        return response()->json([
            'success' => true,
            'compares' => $compares
        ]);
    }
}