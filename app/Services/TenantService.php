<?php

namespace App\Services;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use App\Models\Tenant;

class TenantService
{
    public static function switchToTenant($tenantId)
    {
        $tenant = Tenant::find($tenantId);
        
        if (!$tenant) {
            return false;
        }

        return self::setTenantConnection($tenant->database_name);
    }

    public static function switchToTenantBySlug($slug)
    {
        $tenant = Tenant::where('slug', $slug)->first();
        
        if (!$tenant) {
            return false;
        }

        return self::setTenantConnection($tenant->database_name);
    }

    private static function setTenantConnection($databaseName)
    {
        Config::set('database.connections.tenant.database', $databaseName);
        
        DB::purge('tenant');
        DB::reconnect('tenant');
        
        return true;
    }

    public static function createTenant($name, $description = null)
    {
        $slug = \Str::slug($name);
        $databaseName = 'tenant_' . $slug;

        // Create tenant record
        $tenant = Tenant::create([
            'name' => $name,
            'slug' => $slug,
            'description' => $description,
            'database_name' => $databaseName
        ]);

        // Create tenant database
        DB::statement("CREATE DATABASE IF NOT EXISTS `{$databaseName}`");

        // Switch to tenant database
        self::setTenantConnection($databaseName);

        // Run tenant migrations
        Artisan::call('migrate', [
            '--database' => 'tenant',
            '--path' => 'database/migrations/tenant',
            '--force' => true
        ]);

        return $tenant;
    }
}