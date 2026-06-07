<?php


namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Models\frontend\wishlist;
use App\Models\Order;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'name' => $request->first_name . ' ' . $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'user_type' => 'webuser',
            'user_status' => 1,
        ]);

        Auth::login($user);
        $request->session()->regenerate(); 

        return response()->json([
            'success' => true,
            'message' => 'Registration successful!',
            'redirect' => url('/') 
        ]);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        if (Auth::attempt([
            'email' => $request->email,
            'password' => $request->password,
            'user_type' => 'webuser', 
            'user_status' => 1
        ], $request->boolean('stay_signed_in') )) { 
            
            $request->session()->regenerate();
            
            return response()->json([
                'success' => true,
                'message' => 'Login successful!',
                'redirect' => url('/') 
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid email or password.'
        ], 401);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function dashboard()
    {
        $user = Auth::user();
        
 // Total orders count ke liye
    $totalOrders = Order::where('user_id', $user->id)->count();
    $wishlistCount = Wishlist::where('user_id', $user->id)->count();
    $cartCount = 0; // Fetch cart count

    return view('frontend.user.dashboard', compact('user', 'totalOrders', 'wishlistCount', 'cartCount'));
}

    public function profile()
    {
        return view('frontend.user.profile');
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'dob' => 'nullable|date',
            'address' => 'nullable|string|max:500',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'dob' => $request->dob,
            'address' => $request->address,
        ];

        // Handle Image Upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($user->image && Storage::disk('public')->exists($user->image)) {
                Storage::disk('public')->delete($user->image);
            }
            
            // Store new image
            $imagePath = $request->file('image')->store('profile_images', 'public');
            $data['image'] = $imagePath;
        }

        $user->update($data);

        return response()->json([
            'success' => true,
            'user' => $user->fresh()
        ]);
    }

    // NEW: Change Password Method
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Auth::user();

        // Check if current password matches
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect.'
            ], 401);
        }

        // Update password
        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully!'
        ]);
    }



public function orders()
{
    $user = Auth::user();
    $orders = Order::with('items')->where('user_id', $user->id)->orderBy('created_at', 'desc')->get();
    $wishlistCount = Wishlist::where('user_id', $user->id)->count();
    
    return view('frontend.user.order', compact('user', 'orders', 'wishlistCount'));
}
}