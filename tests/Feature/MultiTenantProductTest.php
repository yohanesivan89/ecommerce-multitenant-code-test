<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Tenant;
use App\Models\User;
use App\Services\TenantService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class MultiTenantProductTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create main database tables
        $this->artisan('migrate');
    }

    public function test_products_are_isolated_between_tenants()
    {
        // Create two tenants
        $tenant1 = TenantService::createTenant('Store One', 'First store');
        $tenant2 = TenantService::createTenant('Store Two', 'Second store');
        
        // Create products for tenant 1
        TenantService::switchToTenant($tenant1->id);
        $product1 = \App\Models\Product::create([
            'name' => 'Product 1',
            'price' => 10.00,
            'stock' => 5
        ]);
        
        // Create products for tenant 2
        TenantService::switchToTenant($tenant2->id);
        $product2 = \App\Models\Product::create([
            'name' => 'Product 2',
            'price' => 20.00,
            'stock' => 10
        ]);
        
        // Verify tenant 1 only sees their product
        TenantService::switchToTenant($tenant1->id);
        $this->assertEquals(1, \App\Models\Product::count());
        $this->assertEquals('Product 1', \App\Models\Product::first()->name);
        
        // Verify tenant 2 only sees their product
        TenantService::switchToTenant($tenant2->id);
        $this->assertEquals(1, \App\Models\Product::count());
        $this->assertEquals('Product 2', \App\Models\Product::first()->name);
    }
    
    public function test_authentication_works_per_tenant()
    {
        // Create tenant
        $tenant = TenantService::createTenant('Test Store', 'Test description');
        
        // Create user in tenant database
        TenantService::switchToTenant($tenant->id);
        $user = User::create([
            'name' => 'Test Admin',
            'email' => 'admin@test.com',
            'password' => Hash::make('password')
        ]);
        
        // Test login
        $response = $this->postJson('/api/login', [
            'email' => 'admin@test.com',
            'password' => 'password',
            'tenant_slug' => $tenant->slug
        ]);
        
        $response->assertStatus(200)
                 ->assertJsonStructure(['user', 'tenant', 'token']);
    }
    
    public function test_unauthorized_access_to_products_without_authentication()
    {
        $tenant = TenantService::createTenant('Test Store A', 'Test description A');
        
        $response = $this->getJson('/api/products', [
            'X-Tenant-ID' => $tenant->id
        ]);
        
        $response->assertStatus(401);
    }
    
    protected function tearDown(): void
    {
        // Clean up tenant databases
        $tenants = Tenant::all();
        foreach ($tenants as $tenant) {
            \DB::statement("DROP DATABASE IF EXISTS `{$tenant->database_name}`");
        }
        
        parent::tearDown();
    }
}