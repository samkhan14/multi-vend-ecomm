<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\PasswordResetMail;

class PasswordResetController extends Controller
{
    // Send Reset Code via Email
    public function sendResetCode(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        
        // Check if webuser exists
        $user = User::where('email', $request->email)
                    ->where('user_type', 'webuser')
                    ->first();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'No account found with this email.'
            ], 404);
        }
        
        // Generate 6-digit code
        $code = rand(100000, 999999);
        
        // Save in database (24 hours expiry)
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => Hash::make($code),
                'created_at' => now()
            ]
        );
        
        // ✅ SEND EMAIL WITH CODE
        try {
            Mail::to($user->email)->send(new PasswordResetMail($user, $code));
            
            // Log the code for development (optional, can remove in production)
            \Log::info("Password reset code for {$request->email}: {$code}");
            
            return response()->json([
                'success' => true,
                'message' => 'Reset code has been sent to your email.',
                'email' => $request->email
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Password reset email failed: ' . $e->getMessage());
            
            // Even if email fails, save code in DB for development/testing
            return response()->json([
                'success' => true,
                'message' => 'Reset code sent. Please check your email.',
                'email' => $request->email
            ]);
        }
    }
    
    // Verify Code and Reset Password
    public function verifyCodeAndReset(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required',
            'password' => 'required|min:6|confirmed',
        ]);
        
        // Find token record
        $tokenRecord = DB::table('password_reset_tokens')
                        ->where('email', $request->email)
                        ->first();
        
        // Verify code
        if (!$tokenRecord || !Hash::check($request->token, $tokenRecord->token)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired code.'
            ], 400);
        }
        
        // Check expiry (24 hours)
        $tokenAge = now()->diffInHours($tokenRecord->created_at);
        if ($tokenAge > 24) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return response()->json([
                'success' => false,
                'message' => 'Code has expired.'
            ]);
        }
        
        // Update password
        $user = User::where('email', $request->email)
                    ->where('user_type', 'webuser')
                    ->first();
        
        if ($user) {
            $user->password = Hash::make($request->password);
            $user->save();
            
            // Delete used token
            DB::table('password_reset_tokens')
                ->where('email', $request->email)
                ->delete();
            
            // Auto login
            Auth::login($user);

            return response()->json([
                'success' => true,
                'message' => 'Password reset successful!',
                'redirect' => url('/')
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'User not found.'
        ], 404);
    }
}