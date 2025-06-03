<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Services\TenantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class TenantController extends Controller
{
    public function index()
    {
        $tenants = Tenant::all();
        return response()->json($tenants);
    }

    public function show($id)
    {
        $tenant = Tenant::findOrFail($id);
        return response()->json($tenant);
    }

    public function create(Request $request)
    {
        $request->validate([
            'tenant_name' => 'required|string|max:255|unique:tenants,name',
            'tenant_desc' => 'nullable|string'
        ]);

        $tenant = TenantService::createTenant(
            $request->tenant_name,
            $request->tenant_desc
        );

        User::create([
            'name' => 'Admin',
            'email' => 'admin@' . $tenant->slug . '.com',
            'password' => Hash::make('password'),
        ]);

        return response()->json($tenant, 201);
    }
}