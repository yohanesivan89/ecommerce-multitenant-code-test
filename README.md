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

Architecture Overview
This multi-tenant eCommerce platform implements a multi-database architecture where each tenant (store) has its own isolated database. This approach provides the highest level of data isolation and security.
System Architecture Diagram
┌─────────────────┐     ┌─────────────────┐     ┌─────────────────┐
│   Vue.js SPA    │────▶│  Laravel API    │────▶│   MySQL DBs     │
│                 │     │                 │     │                 │
│ - Product List  │     │ - Controllers   │     │ - Main DB       │
│ - Shopping Cart │     │ - Middleware    │     │ - Tenant DB 1   │
│ - Admin Panel   │     │ - Services      │     │ - Tenant DB 2   │
│                 │     │ - Models        │     │ - Tenant DB N   │
└─────────────────┘     └─────────────────┘     └─────────────────┘
Technology Stack

Frontend: Vue.js 3, Vue Router, Pinia (State Management)
Backend: Laravel 10.x, Laravel Sanctum (Authentication)
Database: MySQL (Multiple databases)
Storage: Local filesystem for product images

Multi-Database Tenancy Design
Core Concept
Each tenant (store) operates in complete isolation with:

Dedicated database instance
Separate user authentication
Isolated product catalog
Independent data management

Database Naming Convention
Main Database: ecommerce_main
Tenant Databases: tenant_{store_slug}

Examples:
- tenant_electronics-store
- tenant_fashion-boutique
- tenant_book-haven