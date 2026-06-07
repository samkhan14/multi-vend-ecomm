<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\frontend\wishlist;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class WishlistController extends Controller
{
    /**
     * Add product to wishlist
     */
    public function addToWishlist(Request $request)
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
            
            // Check if already in wishlist
            $query = Wishlist::where('product_id', $productId);
            
            if (Auth::check()) {
                $query->where('user_id', Auth::id());
            } else {
                $query->where('session_id', $sessionId);
            }
            
            if ($query->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product is already in your wishlist'
                ]);
            }
            
            // Add to wishlist
            Wishlist::create([
                'product_id' => $productId,
                'user_id' => Auth::check() ? Auth::id() : null,
                'session_id' => Auth::check() ? null : $sessionId
            ]);
            
            // Get wishlist count
            $count = $this->getWishlistCount();
            
            return response()->json([
                'success' => true,
                'message' => 'Product added to wishlist successfully',
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
     * Remove from wishlist
     */
    public function removeFromWishlist(Request $request)
    {
        try {
            $request->validate([
                'product_id' => 'required|exists:products,id'
            ]);
            
            $productId = $request->product_id;
            $sessionId = Session::getId();
            
            $query = Wishlist::where('product_id', $productId);
            
            if (Auth::check()) {
                $query->where('user_id', Auth::id());
            } else {
                $query->where('session_id', $sessionId);
            }
            
            $query->delete();
            
            // Get wishlist count
            $count = $this->getWishlistCount();
            
            return response()->json([
                'success' => true,
                'message' => 'Product removed from wishlist',
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
     * Toggle wishlist (add/remove)
     */
    public function toggleWishlist(Request $request)
    {
        try {
            $request->validate([
                'product_id' => 'required|exists:products,id'
            ]);
            
            $productId = $request->product_id;
            $sessionId = Session::getId();
            
            // Check if exists
            $query = Wishlist::where('product_id', $productId);
            
            if (Auth::check()) {
                $query->where('user_id', Auth::id());
            } else {
                $query->where('session_id', $sessionId);
            }
            
            $wishlistItem = $query->first();
            
            if ($wishlistItem) {
                // Remove
                $wishlistItem->delete();
                $inWishlist = false;
                $message = 'Product removed from wishlist';
            } else {
                // Add
                Wishlist::create([
                    'product_id' => $productId,
                    'user_id' => Auth::check() ? Auth::id() : null,
                    'session_id' => Auth::check() ? null : $sessionId
                ]);
                $inWishlist = true;
                $message = 'Product added to wishlist';
            }
            
            // Get count
            $count = $this->getWishlistCount();
            
            return response()->json([
                'success' => true,
                'message' => $message,
                'count' => $count,
                'in_wishlist' => $inWishlist
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Check if product is in wishlist
     */
    public function checkWishlist(Request $request)
    {
        try {
            $request->validate([
                'product_id' => 'required|exists:products,id'
            ]);
            
            $productId = $request->product_id;
            $sessionId = Session::getId();
            
            $query = Wishlist::where('product_id', $productId);
            
            if (Auth::check()) {
                $query->where('user_id', Auth::id());
            } else {
                $query->where('session_id', $sessionId);
            }
            
            $inWishlist = $query->exists();
            
            return response()->json([
                'success' => true,
                'in_wishlist' => $inWishlist
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Get wishlist count for AJAX
     */
   public function getWishlistCount(Request $request = null)
    {
        try {
            $sessionId = Session::getId();
            $count = 0;
            
            if (Auth::check()) {
                $count = Wishlist::where('user_id', Auth::id())->count();
            } else {
                $count = Wishlist::where('session_id', $sessionId)->count();
            }
            
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
     * Show wishlist page
     */
      public function index()
    {
        $sessionId = Session::getId();
        
        if (Auth::check()) {
            $wishlists = Wishlist::with('product')
                ->where('user_id', Auth::id())
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            $wishlists = Wishlist::with('product')
                ->where('session_id', $sessionId)
                ->orderBy('created_at', 'desc')
                ->get();
        }
        
        return view('frontend.whishlist', compact('wishlists'));
    }
    
    /**
     * Get wishlist items for AJAX
     */
    public function getWishlistItems()
    {
        $sessionId = Session::getId();
        
        if (Auth::check()) {
            $wishlists = Wishlist::with('product')
                ->where('user_id', Auth::id())
                ->get();
        } else {
            $wishlists = Wishlist::with('product')
                ->where('session_id', $sessionId)
                ->get();
        }
        
        return response()->json([
            'success' => true,
            'wishlists' => $wishlists
        ]);
    }

    
}