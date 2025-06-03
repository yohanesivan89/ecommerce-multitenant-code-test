# Multi-Tenant eCommerce Platform

A Laravel + Vue.js multi-tenant eCommerce platform with separate databases per store.

## Features
- Multi-database tenancy (one database per store)
- Admin authentication per store
- Product CRUD operations
- Shopping cart with localStorage
- Vue.js frontend

## Installation

1. Clone the repository
2. Copy `.env.example` to `.env` and configure database
3. Run `composer install`
4. Run `npm install`
5. Run `php artisan migrate`
6. Run `php artisan db:seed`
7. Run `php artisan storage:link`
8. Run `npm run build`
9. Start the server with `php artisan serve`

## Default Credentials

- Electronics Store: admin@electronics-store.com / password
- Fashion Boutique: admin@fashion-boutique.com / password
- Book Haven: admin@book-haven.com / password

## Testing

Run tests with: `php artisan test`

## Architecture

This platform uses a multi-database tenancy approach where each store (tenant) has its own isolated database. This ensures complete data separation and enhanced security.

┌─────────────────────────────────────────────────────────────┐
│                        Main Database                         │
│                     (ecommerce_main)                        │
│                                                             │
│  ┌─────────────────────────────────────────────────────┐  │
│  │                   Tenants Table                      │  │
│  │  ┌────┬─────────────┬───────────┬────────────────┐ │  │
│  │  │ ID │ Store Name  │   Slug    │ Database Name  │ │  │
│  │  ├────┼─────────────┼───────────┼────────────────┤ │  │
│  │  │ 1  │ Electronics │ electronics│ tenant_        │ │  │
│  │  │    │ Store       │ -store    │ electronics... │ │  │
│  │  │ 2  │ Fashion     │ fashion-  │ tenant_        │ │  │
│  │  │    │ Boutique    │ boutique  │ fashion...     │ │  │
│  │  └────┴─────────────┴───────────┴────────────────┘ │  │
│  └─────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
       ┌──────────────────────┴───────────────────────┐
       │                                              │
       ▼                                              ▼
┌──────────────────┐                      ┌──────────────────┐
│ Tenant Database 1│                      │ Tenant Database 2│
│(tenant_electronics-store)               │(tenant_fashion-boutique)
│                  │                      │                  │
│ ┌──────────────┐ │                      │ ┌──────────────┐ │
│ │    Users     │ │                      │ │    Users     │ │
│ │ (Store Admins)│ │                      │ │ (Store Admins)│ │
│ └──────────────┘ │                      │ └──────────────┘ │
│ ┌──────────────┐ │                      │ ┌──────────────┐ │
│ │   Products   │ │                      │ │   Products   │ │
│ │              │ │                      │ │              │ │
│ └──────────────┘ │                      │ └──────────────┘ │
│ ┌──────────────┐ │                      │ ┌──────────────┐ │
│ │Auth Tokens   │ │                      │ │Auth Tokens   │ │
│ └──────────────┘ │                      │ └──────────────┘ │
└──────────────────┘                      └──────────────────┘

How It Works

Main Database (ecommerce_main)

Stores tenant (store) metadata
Maps store slugs to database names
Central registry of all stores


Tenant Databases (tenant_*)

Each store has its own database
Contains: Users, Products, Auth Tokens
Complete isolation from other stores


Dynamic Database Switching
php// When a request comes in:
TenantService::switchToTenant($tenantId);
// All subsequent queries run on tenant's database


Data Flow Example
User Login Flow:
1. Admin selects store: "Electronics Store"
2. System looks up database: "tenant_electronics-store"
3. Switches connection to tenant database
4. Validates credentials against tenant's users table
5. Returns auth token stored in tenant database

Product Management:
1. Admin authenticated with tenant context
2. All product operations occur in tenant database
3. No possibility of accessing other store's data