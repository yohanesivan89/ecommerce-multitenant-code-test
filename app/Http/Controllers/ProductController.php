<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Tenant;
use App\Services\TenantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all();
        return response()->json($products);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|max:2048'
        ]);

        $data = $request->only(['name', 'description', 'price', 'stock']);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            $data['image_path'] = $path;
        }

        $product = Product::create($data);

        return response()->json($product, 201);
    }

    public function show($id)
    {
        $product = Product::findOrFail($id);
        return response()->json($product);
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'name' => 'string|max:255',
            'description' => 'nullable|string',
            'price' => 'numeric|min:0',
            'stock' => 'integer|min:0',
            'image' => 'nullable|image|max:2048'
        ]);

        $data = $request->only(['name', 'description', 'price', 'stock']);

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($product->image_path) {
                Storage::disk('public')->delete($product->image_path);
            }
            
            $path = $request->file('image')->store('products', 'public');
            $data['image_path'] = $path;
        }

        $product->update($data);

        return response()->json($product);
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        
        if ($product->image_path) {
            Storage::disk('public')->delete($product->image_path);
        }
        
        $product->delete();

        return response()->json(['message' => 'Product deleted successfully']);
    }

    // Public endpoint to get all products from all stores
    public function getAllProducts()
    {
        $tenants = Tenant::all();
        $allProducts = [];

        foreach ($tenants as $tenant) {
            TenantService::switchToTenant($tenant->id);
            
            $products = Product::all();
            
            foreach ($products as $product) {
                $productData = $product->toArray();
                $productData['tenant_id'] = $tenant->id;
                $productData['tenant_name'] = $tenant->name;
                $productData['tenant_slug'] = $tenant->slug;
                $allProducts[] = $productData;
            }
        }

        return response()->json($allProducts);
    }

    // Public endpoint to get products from a specific store
    public function getStoreProducts($tenantSlug)
    {
        $tenant = Tenant::where('slug', $tenantSlug)->firstOrFail();
        
        TenantService::switchToTenant($tenant->id);
        
        $products = Product::all();

        return response()->json([
            'tenant' => $tenant,
            'products' => $products
        ]);
    }
}