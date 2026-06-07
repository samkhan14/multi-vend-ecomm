<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\frontend\Cart;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\Coupon;
use App\Models\ShippingCharges;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
// ✅ YEH LINE ADD KARO - Helper functions import karne ke liye
if (!function_exists('getUserCurrency')) {
    require_once app_path('Helpers/FrontendHelper.php');
}
class CartController extends Controller
{
    /**
     * Check if product requires prescription
     */
    private function productRequiresPrescription($product)
    {
        $glassesCategory = \App\Models\Category::whereRaw('LOWER(category_name) = ?', ['glasses'])->first();
        
        if (!$glassesCategory) {
            return false;
        }
        
        $category = $product->category;
        
        while ($category) {
            if ($category->id == $glassesCategory->id) {
                return ($product->product_type ?? 'normal') === 'normal';
            }
            $category = $category->parent;
        }
        
        return false;
    }

    /**
     * Validate prescription data
     */
 private function validatePrescriptionData($prescriptionJson, $hasImage = false)
{
    // Agar image hai aur text nahi hai to allow
    if ($hasImage && !$prescriptionJson) {
        return ['only_image' => true];
    }
    
    // Agar text hi nahi hai
    if (!$prescriptionJson) {
        return false;
    }
    
    $data = json_decode($prescriptionJson, true);
    
    if (!$data) {
        return $hasImage ? ['only_image' => true] : false;
    }
    
    // Check karo kitne text fields fill hain
    $requiredFields = [
        'right_axis', 'right_spherical', 'right_cylindrical',
        'left_axis', 'left_spherical', 'left_cylindrical'
    ];
    
    $filledFields = 0;
    foreach ($requiredFields as $field) {
        if (!empty($data[$field]) || $data[$field] === '0' || $data[$field] === 0) {
            $filledFields++;
        }
    }
    
    // Kuch fields fill hain but sab nahi -> Not allowed
    if ($filledFields > 0 && $filledFields < 6) {
        return false;
    }
    
    // Sab fields fill hain to validate karo
    if ($filledFields == 6) {
        $validator = Validator::make($data, [
            'right_axis' => 'required|numeric|min:0|max:180',
            'right_spherical' => 'required|numeric|between:-20,20',
            'right_cylindrical' => 'required|numeric|between:-10,10',
            'left_axis' => 'required|numeric|min:0|max:180',
            'left_spherical' => 'required|numeric|between:-20,20',
            'left_cylindrical' => 'required|numeric|between:-10,10',
        ]);
        
        return !$validator->fails() ? $data : false;
    }
    
    return false;
}

    /**
     * Add product to cart
     */
    public function addToCart(Request $request)
    {
        try {
            $request->validate([
                'product_id' => 'required|exists:products,id',
                'quantity' => 'nullable|integer|min:1',
                'variant_id' => 'nullable|exists:product_variants,id',
                'prescription' => 'nullable|string',
                 'prescription_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120'  // 👈 YEH LINE ADD
            ]);
            
            $productId = $request->product_id;
            $variantId = $request->variant_id;
            $quantity = $request->quantity ?? 1;
            $sessionId = Session::getId();
            
            // Check if product exists
            $product = Product::find($productId);
            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found'
                ]);
            }
            
               $product->increment('interaction_count');
            // Check if product requires prescription
            $requiresPrescription = $this->productRequiresPrescription($product);
            $prescriptionData = null;
            
          if ($requiresPrescription) {
    $hasImage = $request->hasFile('prescription_image');
    $prescriptionJson = $request->prescription;
    
    $prescriptionData = $this->validatePrescriptionData($prescriptionJson, $hasImage);
    
    if (!$prescriptionData) {
        return response()->json([
            'success' => false,
            'message' => 'Please either upload prescription image OR fill all prescription fields completely. Partial entries are not allowed.'
        ]);
    }
    
    // Agar sirf image hai to text data null rakho
    if (is_array($prescriptionData) && isset($prescriptionData['only_image'])) {
        $prescriptionData = null;
    }
}
            
            // GET EXISTING QUANTITY IN CART
            $existingQuantity = 0;
            
            $query = Cart::where('product_id', $productId);
            
            if ($variantId) {
                $query->where('product_variant_id', $variantId);
            } else {
                $query->whereNull('product_variant_id');
            }
            
            if (Auth::check()) {
                $query->where('user_id', Auth::id());
            } else {
                $query->where('session_id', $sessionId);
            }
            
            $existingCartItem = $query->first();
            if ($existingCartItem) {
                $existingQuantity = $existingCartItem->quantity;
            }
            
            // CALCULATE TOTAL QUANTITY
            $totalQuantity = $existingQuantity + $quantity;
            
            // VARIANT PEHLE CHECK KARO
            if ($variantId) {
                $variant = ProductVariant::find($variantId);
                
                if ($variant) {
                    if ($variant->stock < $totalQuantity) {
                        $availableVariantQuantity = $variant->stock - $existingQuantity;
                        
                        if ($availableVariantQuantity <= 0) {
                            return response()->json([
                                'success' => false,
                                'message' => 'This variant is already at maximum quantity in your cart'
                            ]);
                        } else {
                            return response()->json([
                                'success' => false,
                                'message' => 'Only ' . $availableVariantQuantity . ' more items available for this variant (' . $existingQuantity . ' already in cart)'
                            ]);
                        }
                    }
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Variant not found'
                    ]);
                }
                
            } else {
                // MAIN PRODUCT KA STOCK CHECK
                if ($product->stock < $totalQuantity) {
                    $availableQuantity = $product->stock - $existingQuantity;
                    
                    if ($availableQuantity <= 0) {
                        return response()->json([
                            'success' => false,
                            'message' => 'This product is already at maximum quantity in your cart'
                        ]);
                    } else {
                        return response()->json([
                            'success' => false,
                            'message' => 'Only ' . $availableQuantity . ' more items available (' . $existingQuantity . ' already in cart)'
                        ]);
                    }
                }
            }
            
            // Price calculate karo
            $price = $this->calculatePrice($product, $variantId);
            
            // Check if already in cart
            $query = Cart::where('product_id', $productId);

            if ($variantId) {
                $query->where('product_variant_id', $variantId);
            } else {
                $query->whereNull('product_variant_id');
            }
            
            if (Auth::check()) {
                $query->where('user_id', Auth::id());
            } else {
                $query->where('session_id', $sessionId);
            }
            
            $cartItem = $query->first();
            
if ($cartItem) {
    // Update quantity
    $cartItem->quantity += $quantity;
    $cartItem->save();
    $message = 'Product quantity updated in cart';
    
    // Update prescription if needed
    if ($requiresPrescription) {
        // Pehle existing prescription delete karo
        if ($cartItem->prescription) {
            $cartItem->prescription->delete();
        }
        
        // Agar text fields fill hain to save karo
        if ($prescriptionData && !isset($prescriptionData['only_image'])) {
            $prescriptionDataForDb = $prescriptionData;
            
            // Image upload handling
            if ($request->hasFile('prescription_image')) {
                $image = $request->file('prescription_image');
                $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $imagePath = $image->storeAs('prescriptions/' . date('Y/m'), $imageName, 'public');
                $prescriptionDataForDb['prescription_image'] = $imagePath;
            }
            
            // Naya prescription create karo
            $cartItem->prescription()->create($prescriptionDataForDb);
        } elseif ($request->hasFile('prescription_image')) {
            // Sirf image hai, text nahi
            $image = $request->file('prescription_image');
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('prescriptions/' . date('Y/m'), $imageName, 'public');
            
            $cartItem->prescription()->create([
                'prescription_image' => $imagePath,
            ]);
        }
    }
    
} else {
    // Add new item
    $cartItem = Cart::create([
        'product_id' => $productId,
        'product_variant_id' => $variantId,
        'user_id' => Auth::check() ? Auth::id() : null,
        'session_id' => Auth::check() ? null : $sessionId,
        'quantity' => $quantity,
        'price' => $price,
    ]);
    
    // Save prescription if needed
    if ($requiresPrescription) {
        // Agar text fields fill hain to save karo
        if ($prescriptionData && !isset($prescriptionData['only_image'])) {
            $prescriptionDataForDb = $prescriptionData;
            
            // Image upload handling
            if ($request->hasFile('prescription_image')) {
                $image = $request->file('prescription_image');
                $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $imagePath = $image->storeAs('prescriptions/' . date('Y/m'), $imageName, 'public');
                $prescriptionDataForDb['prescription_image'] = $imagePath;
            }
            
            $cartItem->prescription()->create($prescriptionDataForDb);
        } elseif ($request->hasFile('prescription_image')) {
            // Sirf image hai, text nahi
            $image = $request->file('prescription_image');
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('prescriptions/' . date('Y/m'), $imageName, 'public');
            
            $cartItem->prescription()->create([
                'prescription_image' => $imagePath,
            ]);
        }
    }
    
    $message = 'Product added to cart successfully';
}
            // Get cart count and total
            $cartData = $this->getCartData();
            
            return response()->json([
                'success' => true,
                'message' => $message,
                'count' => $cartData['count'],
                'total' => $cartData['total'],
                'items' => $cartData['items']
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Add to cart error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Remove from cart
     */
    public function removeFromCart(Request $request)
    {
        try {
            $request->validate([
                'product_id' => 'required|exists:products,id',
                'variant_id' => 'nullable|exists:product_variants,id'
            ]);
            
            $productId = $request->product_id;
            $variantId = $request->variant_id;
            $sessionId = Session::getId();
            
            $query = Cart::where('product_id', $productId);
            
            if ($variantId) {
                $query->where('product_variant_id', $variantId);
            } else {
                $query->whereNull('product_variant_id');
            }
            
            if (Auth::check()) {
                $query->where('user_id', Auth::id());
            } else {
                $query->where('session_id', $sessionId);
            }
            
            // Delete prescription first (if exists)
            $cartItem = $query->first();
            if ($cartItem && $cartItem->prescription) {
                $cartItem->prescription->delete();
            }
            
            $query->delete();
            
            // Get cart data
            $cartData = $this->getCartData();
            
            return response()->json([
                'success' => true,
                'message' => 'Product removed from cart',
                'count' => $cartData['count'],
                'total' => $cartData['total'],
                'items' => $cartData['items']
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Update cart quantity
     */
    public function updateCart(Request $request)
    {
        try {
            $request->validate([
                'product_id' => 'required|exists:products,id',
                'quantity' => 'required|integer|min:1',
                'variant_id' => 'nullable|exists:product_variants,id'
            ]);
            
            $productId = $request->product_id;
            $variantId = $request->variant_id;
            $quantity = $request->quantity;
            $sessionId = Session::getId();
            
            // CHECK PRODUCT EXISTS
            $product = Product::find($productId);
            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found'
                ]);
            }
            
            // VARIANT PEHLE CHECK KARO
            if ($variantId) {
                $variant = ProductVariant::find($variantId);
                
                if ($variant) {
                    if ($variant->stock < $quantity) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Only ' . $variant->stock . ' items available for this variant'
                        ]);
                    }
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Variant not found'
                    ]);
                }
                
            } else {
                // MAIN PRODUCT KA STOCK CHECK
                if ($product->stock < $quantity) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Only ' . $product->stock . ' items available in stock'
                    ]);
                }
            }
            
            // FIND CART ITEM
            $query = Cart::where('product_id', $productId);
            
            if ($variantId) {
                $query->where('product_variant_id', $variantId);
            } else {
                $query->whereNull('product_variant_id');
            }
            
            if (Auth::check()) {
                $query->where('user_id', Auth::id());
            } else {
                $query->where('session_id', $sessionId);
            }
            
            $cartItem = $query->first();
            
            if ($cartItem) {
                $cartItem->quantity = $quantity;
                $cartItem->save();
            }
            
            // Get cart data
            $cartData = $this->getCartData();
            
            return response()->json([
                'success' => true,
                'message' => 'Cart updated successfully',
                'count' => $cartData['count'],
                'total' => $cartData['total'],
                'items' => $cartData['items']
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Get cart count
     */
    public function getCartCount()
    {
        try {
            $cartData = $this->getCartData();
            
            return response()->json([
                'success' => true,
                'count' => $cartData['count']
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'count' => 0
            ]);
        }
    }
    
    /**
     * Get cart items
     */
    public function getCartItems()
    {
        try {
            $cartData = $this->getCartData();
            
            return response()->json([
                'success' => true,
                'count' => $cartData['count'],
                'total' => $cartData['total'],
                'items' => $cartData['items']
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Clear cart
     */
    public function clearCart()
    {
        try {
            $sessionId = Session::getId();
            
            if (Auth::check()) {
                // Delete prescriptions first
                $cartItems = Cart::where('user_id', Auth::id())->get();
                foreach ($cartItems as $item) {
                    if ($item->prescription) {
                        $item->prescription->delete();
                    }
                }
                Cart::where('user_id', Auth::id())->delete();
            } else {
                // Delete prescriptions first
                $cartItems = Cart::where('session_id', $sessionId)->get();
                foreach ($cartItems as $item) {
                    if ($item->prescription) {
                        $item->prescription->delete();
                    }
                }
                Cart::where('session_id', $sessionId)->delete();
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Cart cleared successfully',
                'count' => 0,
                'total' => 0,
                'items' => []
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Helper: Calculate price
     */
/**
 * Helper: Calculate price
 */
private function calculatePrice($product, $variantId = null)
{
    // Agar variant hai to variant price lo
    if ($variantId) {
        $variant = ProductVariant::with(['product'])->find($variantId);
        if ($variant) {
            // Pehle variant ki sale price check karo
            if ($variant->sale_price && $variant->sale_price > 0) {
                $currentDate = now();
                
                // Check if variant has its own sale dates
                if ($variant->sale_start_date || $variant->sale_end_date) {
                    $saleStart = $variant->sale_start_date ? Carbon::parse($variant->sale_start_date) : null;
                    $saleEnd = $variant->sale_end_date ? Carbon::parse($variant->sale_end_date) : null;
                    
                    $hasActiveSale = true;
                    
                    if ($saleStart && $currentDate < $saleStart) {
                        $hasActiveSale = false;
                    }
                    
                    if ($saleEnd && $currentDate > $saleEnd) {
                        $hasActiveSale = false;
                    }
                    
                    if ($hasActiveSale) {
                        $price = (float) $variant->sale_price;
                        return convertPrice($price); // ✅ CONVERT ADDED
                    }
                } else {
                    // No dates set, sale is active
                    $price = (float) $variant->sale_price;
                    return convertPrice($price); // ✅ CONVERT ADDED
                }
            }
            
            // Agar sale active nahi to regular price
            $price = (float) $variant->price;
            return convertPrice($price); // ✅ CONVERT ADDED
        }
    }
    
    // Main product price with sale check
    $currentDate = now();
    
    // Check product sale price first
    if ($product->sale_price && $product->sale_price > 0) {
        $saleStart = $product->sale_start_date ? Carbon::parse($product->sale_start_date) : null;
        $saleEnd = $product->sale_end_date ? Carbon::parse($product->sale_end_date) : null;
        
        $hasActiveSale = true;
        
        if ($saleStart && $currentDate < $saleStart) {
            $hasActiveSale = false;
        }
        
        if ($saleEnd && $currentDate > $saleEnd) {
            $hasActiveSale = false;
        }
        
        if ($hasActiveSale) {
            $price = (float) $product->sale_price;
            return convertPrice($price); // ✅ CONVERT ADDED
        }
    }
    
    // Agar sale nahi to discount check karo
    if ($product->product_discount && $product->product_discount > 0) {
        $discountedPrice = $product->product_price * (1 - $product->product_discount / 100);
        $price = (float) $discountedPrice;
        return convertPrice($price); // ✅ CONVERT ADDED
    }
    
    // Original price
    $price = (float) $product->product_price;
    return convertPrice($price); // ✅ CONVERT ADDED
}
    
    /**
     * Helper: Get cart data
     */
    private function getCartData()
    {
        $sessionId = Session::getId();
        
        if (Auth::check()) {
            $cartItems = Cart::with(['product', 'variant', 'prescription'])
                ->where('user_id', Auth::id())
                ->get();
        } else {
            $cartItems = Cart::with(['product', 'variant', 'prescription'])
                ->where('session_id', $sessionId)
                ->get();
        }
        
        $count = 0;
        $total = 0;
        $items = [];
        
        foreach ($cartItems as $item) {
            $count += $item->quantity;
            $itemTotal = $item->price * $item->quantity;
            $total += $itemTotal;
            
            $image = $item->product->thumbnail_image;
            
            $itemData = [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'variant_id' => $item->product_variant_id,
                'product_name' => $item->product->product_name,
                'product_slug' => $item->product->product_slug,
                'variant_name' => $item->variant ? $item->variant->sku : null,
                'image' => $image,
                'price' => (float) $item->price,
                'quantity' => (int) $item->quantity,
                'item_total' => $itemTotal,
                'currency' => getUserCurrency(), // ✅ YEH LINE ADD KARO
            ];
            
            // Add prescription info if exists
            if ($item->prescription) {
                $itemData['has_prescription'] = true;
                $itemData['prescription_text'] = "OD: {$item->prescription->right_spherical}/{$item->prescription->right_cylindrical}x{$item->prescription->right_axis}° | OS: {$item->prescription->left_spherical}/{$item->prescription->left_cylindrical}x{$item->prescription->left_axis}°";
            }
            
            $items[] = $itemData;
        }
        
        return [
            'count' => $count,
            'total' => $total,
            'items' => $items
        ];
    }
    
    /**
     * Cart page
     */
    public function cartPage()
    {
        $cartData = $this->getCartData();
        
        return view('frontend.cart', [
            'cartItems' => $cartData['items'],
            'cartCount' => $cartData['count'],
            'cartTotal' => $cartData['total']
        ]);
    }
    
    // ========== COUPON METHODS ==========
    
    /**
     * Apply coupon
     */
  /**
 * Apply coupon
 */
public function applyCoupon(Request $request)
{
    try {
        $request->validate([
            'coupon_code' => 'required|string',
            'cart_total' => 'required|numeric|min:0'
        ]);
        
        $coupon = Coupon::where('coupon_code', $request->coupon_code)
                        ->where('status', 1)
                        ->first();
        
        if (!$coupon) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid coupon code or coupon is inactive'
            ]);
        }
        
        // ✅ Convert cart total to base currency for checking
        $baseCurrency = getGeneralSetting()->currency ?? 'PKR';
        $userCurrency = getUserCurrency();
        $cartTotal = $request->cart_total;
        
        $cartTotalInBase = $cartTotal;
        if ($userCurrency !== $baseCurrency) {
            $rate = getCurrencyRate($userCurrency, $baseCurrency);
            $cartTotalInBase = $cartTotal * $rate;
        }
        
        // Check expiry
        $now = now()->format('Y-m-d');
        
        if ($coupon->start_date && $now < $coupon->start_date) {
            return response()->json([
                'success' => false,
                'message' => 'Coupon is not yet active. Starts from ' . $coupon->start_date
            ]);
        }
        
        if ($coupon->end_date && $now > $coupon->end_date) {
            return response()->json([
                'success' => false,
                'message' => 'Coupon has expired on ' . $coupon->end_date
            ]);
        }
        
        // ✅ Check minimum purchase in base currency
        if ($coupon->minimum_purchase_amount && $cartTotalInBase < $coupon->minimum_purchase_amount) {
            return response()->json([
                'success' => false,
                'message' => 'Minimum purchase amount of ' . $baseCurrency . ' ' . number_format($coupon->minimum_purchase_amount, 2) . ' not met'
            ]);
        }
        
        // ✅ Calculate discount in base currency first
        $discountAmountInBase = 0;
        $discountValue = (float) $coupon->discount_value;
        
        if ($coupon->discount_type == 'percentage') {
            $discountAmountInBase = ($cartTotalInBase * $discountValue) / 100;
            
            if ($coupon->maximum_discount_amount && $discountAmountInBase > $coupon->maximum_discount_amount) {
                $discountAmountInBase = (float) $coupon->maximum_discount_amount;
            }
        } else {
            // Fixed discount - already in base currency
            $discountAmountInBase = $discountValue;
        }
        
        if ($discountAmountInBase > $cartTotalInBase) {
            $discountAmountInBase = $cartTotalInBase;
        }
        
        // ✅ Convert discount back to user currency
        $discountAmount = $discountAmountInBase;
        if ($userCurrency !== $baseCurrency) {
            $rateBack = getCurrencyRate($baseCurrency, $userCurrency);
            $discountAmount = $discountAmountInBase * $rateBack;
        }
        
        $discountAmount = round($discountAmount, 2);
        
        // Save coupon in session
        session([
            'applied_coupon_code' => $coupon->coupon_code,
            'coupon_discount_amount' => $discountAmount,
            'coupon_discount_amount_base' => $discountAmountInBase,
            'coupon_discount_type' => $coupon->discount_type,
            'coupon_minimum_amount' => $coupon->minimum_purchase_amount,
            'coupon_expiry' => $coupon->end_date
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Coupon applied successfully! You saved ' . $userCurrency . ' ' . $discountAmount,
            'discount_amount' => $discountAmount,
            'discount_type' => $coupon->discount_type,
            'coupon_code' => $coupon->coupon_code
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Something went wrong: ' . $e->getMessage()
        ]);
    }
}
/**
 * Validate coupon
 */
public function validateCoupon(Request $request)
{
    try {
        $request->validate([
            'coupon_code' => 'required|string',
            'cart_total' => 'required|numeric|min:0'
        ]);
        
        $coupon = Coupon::where('coupon_code', $request->coupon_code)
                        ->where('status', 1)
                        ->first();
        
        if (!$coupon) {
            return response()->json([
                'success' => false,
                'valid' => false,
                'message' => 'Coupon no longer exists or is inactive'
            ]);
        }
        
        // ✅ Convert to base currency
        $baseCurrency = getGeneralSetting()->currency ?? 'PKR';
        $userCurrency = getUserCurrency();
        $cartTotal = $request->cart_total;
        
        $cartTotalInBase = $cartTotal;
        if ($userCurrency !== $baseCurrency) {
            $rate = getCurrencyRate($userCurrency, $baseCurrency);
            $cartTotalInBase = $cartTotal * $rate;
        }
        
        // Check expiry
        $now = now()->format('Y-m-d');
        if ($coupon->end_date && $now > $coupon->end_date) {
            return response()->json([
                'success' => false,
                'valid' => false,
                'message' => 'Coupon has expired'
            ]);
        }
        
        // Check minimum purchase in base currency
        if ($coupon->minimum_purchase_amount && $cartTotalInBase < $coupon->minimum_purchase_amount) {
            return response()->json([
                'success' => false,
                'valid' => false,
                'message' => 'Cart total is below minimum purchase amount of ' . $baseCurrency . ' ' . number_format($coupon->minimum_purchase_amount, 2)
            ]);
        }
        
        // Calculate discount in base currency
        $discountAmountInBase = 0;
        $discountValue = (float) $coupon->discount_value;
        
        if ($coupon->discount_type == 'percentage') {
            $discountAmountInBase = ($cartTotalInBase * $discountValue) / 100;
            
            if ($coupon->maximum_discount_amount && $discountAmountInBase > $coupon->maximum_discount_amount) {
                $discountAmountInBase = (float) $coupon->maximum_discount_amount;
            }
        } else {
            $discountAmountInBase = $discountValue;
        }
        
        if ($discountAmountInBase > $cartTotalInBase) {
            $discountAmountInBase = $cartTotalInBase;
        }
        
        // Convert back to user currency
        $discountAmount = $discountAmountInBase;
        if ($userCurrency !== $baseCurrency) {
            $rateBack = getCurrencyRate($baseCurrency, $userCurrency);
            $discountAmount = $discountAmountInBase * $rateBack;
        }
        
        $discountAmount = round($discountAmount, 2);
        
        return response()->json([
            'success' => true,
            'valid' => true,
            'discount_amount' => $discountAmount,
            'minimum_amount' => $coupon->minimum_purchase_amount ?? 0,
            'message' => 'Coupon is still valid'
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'valid' => false,
            'message' => 'Validation error: ' . $e->getMessage()
        ]);
    }
}
    /**
     * Remove coupon from session
     */
    public function removeCouponFromSession(Request $request)
    {
        try {
            session()->forget([
                'applied_coupon_code',
                'coupon_discount_amount',
                'coupon_discount_type',
                'coupon_minimum_amount',
                'coupon_expiry'
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Coupon removed from session'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Get applied coupon status
     */
    public function getAppliedCouponStatus()
    {
        try {
            $couponCode = session('applied_coupon_code');
            $discountAmount = session('coupon_discount_amount', 0);
            
            if ($couponCode && $discountAmount > 0) {
                return response()->json([
                    'success' => true,
                    'applied' => true,
                    'coupon_code' => $couponCode,
                    'discount_amount' => $discountAmount,
                    'message' => 'Coupon ' . $couponCode . ' is applied'
                ]);
            }
            
            return response()->json([
                'success' => true,
                'applied' => false,
                'discount_amount' => 0,
                'message' => 'No coupon applied'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong: ' . $e->getMessage()
            ]);
        }
    }
    
    // ========== SHIPPING METHODS ==========
    
    /**
     * Calculate shipping
     */
public function calculateShipping(Request $request)
{
    try {
        $request->validate([
            'cart_total' => 'required|numeric|min:0'
        ]);
        
        $shipping = ShippingCharges::where('status', 1)->first();
        
        if (!$shipping) {
            return response()->json([
                'success' => false,
                'message' => 'No shipping charges configured'
            ]);
        }
        
        // ✅ Convert cart total to base currency for threshold check
        $baseCurrency = getGeneralSetting()->currency ?? 'PKR';
        $userCurrency = getUserCurrency();
        $cartTotal = $request->cart_total;
        
        $cartTotalInBase = $cartTotal;
        if ($userCurrency !== $baseCurrency) {
            $rate = getCurrencyRate($userCurrency, $baseCurrency);
            $cartTotalInBase = $cartTotal * $rate;
        }
        
        $shippingFee = 0;
        $fee = (float) $shipping->fee;
        $maxOrderAmount = $shipping->max_order_amount ? (float) $shipping->max_order_amount : null;
        $isFreeShipping = false;
        
        // Check for free shipping threshold (in base currency)
        if ($maxOrderAmount && $cartTotalInBase >= $maxOrderAmount) {
            $shippingFee = 0;
            $isFreeShipping = true;
        } else {
            // Calculate shipping fee in base currency first
            if ($shipping->type == 'percentage') {
                $shippingFeeInBase = ($cartTotalInBase * $fee) / 100;
            } else {
                $shippingFeeInBase = $fee;
            }
            
            // Convert back to user currency
            if ($userCurrency !== $baseCurrency) {
                $rateBack = getCurrencyRate($baseCurrency, $userCurrency);
                $shippingFee = $shippingFeeInBase * $rateBack;
            } else {
                $shippingFee = $shippingFeeInBase;
            }
        }
        
        $shippingFee = round($shippingFee, 2);
        
        // Calculate remaining amount for free shipping in user currency
        $remainingAmount = 0;
        if ($maxOrderAmount && !$isFreeShipping) {
            $remainingInBase = $maxOrderAmount - $cartTotalInBase;
            if ($userCurrency !== $baseCurrency) {
                $rateRemaining = getCurrencyRate($baseCurrency, $userCurrency);
                $remainingAmount = $remainingInBase * $rateRemaining;
            } else {
                $remainingAmount = $remainingInBase;
            }
            $remainingAmount = round($remainingAmount, 2);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Shipping calculated successfully',
            'shipping_fee' => $shippingFee,
            'type' => $shipping->type,
            'fee' => $fee,
            'max_order_amount' => $maxOrderAmount,
            'is_free_shipping' => $isFreeShipping,
            'free_shipping_threshold' => $maxOrderAmount,
            'current_cart_total' => $cartTotal,
            'cart_total_in_base' => round($cartTotalInBase, 2),
            'remaining_amount' => $remainingAmount
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Something went wrong: ' . $e->getMessage()
        ]);
    }
}
    
    /**
     * Get shipping charges
     */
    public function getShippingCharges()
    {
        try {
            $shipping = ShippingCharges::where('status', 1)->first();
            
            if (!$shipping) {
                return response()->json([
                    'success' => false,
                    'message' => 'No shipping charges configured'
                ]);
            }
            
            return response()->json([
                'success' => true,
                'shipping' => $shipping
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Get cart total for calculations
     */
    public function getCartTotalForCalculations()
    {
        try {
            $cartData = $this->getCartData();
            
            return response()->json([
                'success' => true,
                'cart_total' => $cartData['total']
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'cart_total' => 0,
                'message' => $e->getMessage()
            ]);
        }
    }
}