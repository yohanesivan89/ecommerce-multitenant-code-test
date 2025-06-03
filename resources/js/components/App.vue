<template>
    <div id="app">
        <nav class="navbar">
            <div class="container">
                <router-link to="/" class="logo">Multi-Store Marketplace</router-link>
                <div class="nav-links">
                    <router-link to="/">Home</router-link>
                    <router-link to="/cart">Cart ({{ cartCount }})</router-link>
                    <router-link to="/admin/login">Admin</router-link>
                    <router-link to="/add-tenant">Add Tenant</router-link>
                </div>
            </div>
        </nav>
        <main>
            <router-view></router-view>
        </main>
    </div>
</template>

<script>
import { computed } from 'vue';
import { useCartStore } from '../stores/cart';

export default {
    setup() {
        const cartStore = useCartStore();
        const cartCount = computed(() => cartStore.itemCount);
        
        return { cartCount };
    }
}
</script>

<style>
.navbar {
    background: #2c3e50;
    color: white;
    padding: 1rem 0;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.logo {
    font-size: 1.5rem;
    font-weight: bold;
    color: white;
    text-decoration: none;
}

.nav-links {
    display: flex;
    gap: 1rem;
}

.nav-links a {
    color: white;
    text-decoration: none;
}

.nav-links a:hover {
    text-decoration: underline;
}

main {
    min-height: calc(100vh - 60px);
    padding: 2rem 0;
}
</style>