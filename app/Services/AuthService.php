<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Namshi\JOSE\JWT;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\IdRole;
use App\Services\UserActivitys;
use App\Services\UserService;
// Use the UserActivitys service to log user activities

class AuthService
{
    // Login and return JWT token
    public function login($credentials)
    {
        $user = User::where('email', $credentials['email'])->first();
        if (!$user) {
            return [
                'success' => false,
                'message' => 'User not found',
                'status' => 404,
            ];
        }
        if ($user->email_verified_at === null) {
            return [
                'success' => false,
                'message' => 'Please Verify',
                'status' => 401, // or 403
            ];
        }
        if (!$token = Auth::attempt($credentials)) {
            return [
                'success' => false,
                'message' => 'Invalid credentials',
                'status' => 401,

            ];
        }
        if ($user->email_verified_at !== null) {
            $activity = new UserActivitys();
            $activity->logLogin();
            return [
                'success' => true,
                'message' => 'Please Login now',
                'token' => $token,
                'user_id' => $user->id,
                'status' => 200,
            ];
        }
    }

    // Logout method
    public function logout()
    {
        $acivity = new UserActivitys();
        $acivity->logLogout();
        Auth::logout();
        return [
            'success' => true,
            'message' => 'User logged out successfully',
            'status' => 200,
        ];
    }

    // Register a new user
    public function register($validdata)
    {
        $authuser = Auth::user();
        if ($authuser && !$authuser->roles->contains('role', 'admin')) {
            return [
                'success' => false,
                'message' => 'Only admin can create users',
                'status' => 403,
            ];
        }
        $validdata['password'] = Hash::make($validdata['password']);
        $user = User::create($validdata);
        //Generate a confirmation token
        $token = str::random(32);
        $user->confirmation_token = $token;
        $user->save();
        if ($authuser) {
            $user->created_by = $authuser->id;
        } else {
            $user->created_by = null;
        }
        $user->save();
        // Assign default role if needed
        $UserService = new UserService();
        $UserService->assignRole($user->id, 'user'); // Assuming 'user' is the default role
        $emailResult = $this->sendVerificationEmail($user);
        return [
            'success' => true,
            'user' => $user,
            'status' => 201,
        ];
    }


    // send verification email
    public function sendVerificationEmail($user)
    {
        $frontendUrl = 'http://localhost:3000/verify-email';
        $verification_link = $frontendUrl . '?token=' . urlencode($user['confirmation_token']) . '&email=' . urlencode($user['email']);
        $data = [
            'name' => $user['name'],
            'email' => $user['email'],
            'verification_link' => $verification_link,
        ];
        try {
            Mail::send('emails.confirm', $data, function ($message) use ($user) {
                $message->to($user['email'], $user['name'])
                    ->subject('Email Verification');
            });
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to send verification email: ' . $e->getMessage(),
                'status' => 500,
            ];
        }
        return [
            'success' => true,
            'message' => 'Verification email sent.',
            'status' => 450,
        ];
    }
    public function verifyEmailbytoken($token, $email)
    {
        $user = User::where('confirmation_token', $token)->
            where('email', $email)->first();
        if (!$user) {
            return [
                'success' => false,
                'message' => 'Invalid or expired token',
                'status' => 404,
            ];
        }
        // Update the user's email_verified_at timestamp
        $user->email_verified_at = Carbon::now();
        $user->confirmation_token = null; // Clear the token after verification
        $user->save();
        //user created by other loged user 
        return [
            'success' => true,
            'message' => 'Email verified successfully',
            'status' => 200,
        ];
    }

    //forget password
    public function sendPasswordResetEmail($email)
    {
        $user = User::Where('email', $email)->first();
        if (!$user) {
            return [
                'success' => false,
                'message' => 'User not found',
                'status' => 404,
            ];
        }
        // generate password reset token
        $token = Str::random(32);
        $user->confirmation_token = $token; // Reusing confirmation_token for password reset
        $user->save();
        // Send reset password email
        $frontendUrl = 'http://localhost:3000/reset-password';
        $reset_link = $frontendUrl . '?token=' . urlencode($token) . '&email=' . urlencode($user->email);
        $data = [
            'name' => $user->name,
            'email' => $user->email,
            'reset_link' => $reset_link,
        ];
        Mail::send('emails.reset', $data, function ($message) use ($user) {
            $message->to($user->email, $user->name)
                ->subject('Password Reset');
        });
        return [
            'success' => true,
            'message' => 'Password reset email sent.',
            'status' => 200,
        ];
    }
    // Reset password using token
    public function resetPassword($token, $email, $newPassword)
    {
        $user = User::where('confirmation_token', $token)->
            where('email', $email)->first();

        if (!$user) {
            return [
                'success' => false,
                'message' => 'Invalid or expired token',
                'status' => 404,
            ];
        }

        // Update the user's password
        $user->password = Hash::make($newPassword);
        $user->confirmation_token = null; // Clear the token after reset
        $user->save();
        return [
            'success' => true,
            'message' => 'Password reset successfully',
            'status' => 200,
        ];
    }
}