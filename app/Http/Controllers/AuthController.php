<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Tenant;
use App\Services\TenantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'tenant_slug' => 'required|string'
        ]);

        // Find tenant
        $tenant = Tenant::where('slug', $request->tenant_slug)->first();
        
        if (!$tenant) {
            throw ValidationException::withMessages([
                'tenant_slug' => ['Invalid store.'],
            ]);
        }

        // Switch to tenant database
        TenantService::switchToTenant($tenant->id);

        // Find user in tenant database
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Store tenant ID in session
        session(['tenant_id' => $tenant->id]);

        return response()->json([
            'user' => $user,
            'tenant' => $tenant,
            'token' => $user->createToken('auth-token')->plainTextToken
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        session()->forget('tenant_id');
        
        return response()->json(['message' => 'Logged out successfully']);
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
            'tenant_id' => 'required|exists:tenants,id'
        ]);

        // Switch to tenant database
        TenantService::switchToTenant($request->tenant_id);

        // Check if email already exists in tenant database
        if (User::where('email', $request->email)->exists()) {
            throw ValidationException::withMessages([
                'email' => ['Email already exists in this store.'],
            ]);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'user' => $user,
            'message' => 'User created successfully'
        ], 201);
    }
}