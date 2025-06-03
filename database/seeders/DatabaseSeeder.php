<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Services\TenantService;
use App\Models\User;
use App\Models\Product;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Create sample tenants
        $tenants = [
            ['name' => 'Electronics Store', 'description' => 'Latest gadgets and electronics'],
            ['name' => 'Fashion Boutique', 'description' => 'Trendy clothes and accessories'],
            ['name' => 'Book Haven', 'description' => 'Books for every reader']
        ];

        foreach ($tenants as $tenantData) {
            // Create tenant and database
            $tenant = TenantService::createTenant($tenantData['name'], $tenantData['description']);
            
            // Switch to tenant database
            TenantService::switchToTenant($tenant->id);
            
            // Create admin user for this tenant
            User::create([
                'name' => 'Admin',
                'email' => 'admin@' . $tenant->slug . '.com',
                'password' => Hash::make('password'),
            ]);
            
            // Create sample products
            $this->createSampleProducts($tenant->slug);
        }
    }

    private function createSampleProducts($tenantSlug)
    {
        $productsByStore = [
            'electronics-store' => [
                ['name' => 'Laptop Pro 2024', 'description' => 'High-performance laptop', 'price' => 1299.99, 'stock' => 10],
                ['name' => 'Smartphone X', 'description' => 'Latest smartphone model', 'price' => 899.99, 'stock' => 25],
                ['name' => 'Wireless Headphones', 'description' => 'Premium sound quality', 'price' => 199.99, 'stock' => 50],
            ],
            'fashion-boutique' => [
                ['name' => 'Designer T-Shirt', 'description' => 'Cotton comfort wear', 'price' => 49.99, 'stock' => 100],
                ['name' => 'Denim Jeans', 'description' => 'Classic fit jeans', 'price' => 79.99, 'stock' => 75],
                ['name' => 'Summer Dress', 'description' => 'Light and breezy', 'price' => 89.99, 'stock' => 40],
            ],
            'book-haven' => [
                ['name' => 'Programming Guide', 'description' => 'Learn to code', 'price' => 39.99, 'stock' => 30],
                ['name' => 'Mystery Novel', 'description' => 'Thrilling page-turner', 'price' => 24.99, 'stock' => 60],
                ['name' => 'Cookbook Deluxe', 'description' => '100 recipes', 'price' => 34.99, 'stock' => 45],
            ]
        ];

        $products = $productsByStore[$tenantSlug] ?? [];
        
        foreach ($products as $product) {
            Product::create($product);
        }
    }
}