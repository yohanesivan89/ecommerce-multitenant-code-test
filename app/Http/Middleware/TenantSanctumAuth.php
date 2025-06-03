<?php

namespace App\Http\Middleware;

use Closure;
use App\Services\TenantService;
use Illuminate\Http\Request;
use App\Models\TenantPersonalAccessToken as PersonalAccessToken;

class TenantSanctumAuth
{
    protected $tenantService;
    
    public function __construct(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
    }
    
    public function handle(Request $request, Closure $next)
    {
        // Get tenant ID
        $tenantId = $request->header('X-Tenant-ID');
        
        if (!$tenantId) {
            return response()->json(['message' => 'Tenant ID not provided'], 401);
        }
        
        // Set tenant connection
        $this->tenantService->switchToTenant($tenantId);

        // Extract bearer token
        $bearerToken = $request->bearerToken();
        if (!$bearerToken) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }
        
        // Find token manually in tenant database
        $token = PersonalAccessToken::findToken($bearerToken);
        if (!$token || !$token->tokenable) {
            return response()->json(['message' => 'Invalid token'], 401);
        }
        
        // Set authenticated user
        $user = $token->tokenable;
        $request->setUserResolver(function() use ($user) {
            return $user;
        });
        
        return $next($request);
    }
}