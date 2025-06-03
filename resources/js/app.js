import { createApp } from 'vue';
import { createRouter, createWebHistory } from 'vue-router';
import { createPinia } from 'pinia';
import axios from 'axios';

// Configure axios
axios.defaults.baseURL = '/api';
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.withCredentials = true;

// Import components
import App from './components/App.vue';
import Home from './components/Home.vue';
import StoreProducts from './components/StoreProducts.vue';
import Cart from './components/Cart.vue';
import AdminLogin from './components/AdminLogin.vue';
import AdminDashboard from './components/AdminDashboard.vue';
import AddTenant from './components/AddTenant.vue';

// Configure routes
const router = createRouter({
    history: createWebHistory(),
    routes: [
        { path: '/', component: Home, name: 'home' },
        { path: '/store/:slug', component: StoreProducts, name: 'store' },
        { path: '/cart', component: Cart, name: 'cart' },
        { path: '/admin/login', component: AdminLogin, name: 'admin.login' },
        { path: '/add-tenant', component: AddTenant, name: 'addtenant' },
        { path: '/admin/dashboard', component: AdminDashboard, name: 'admin.dashboard', meta: { requiresAuth: true } },
    ]
});

// Auth guard
router.beforeEach((to, from, next) => {
    if (to.meta.requiresAuth && !localStorage.getItem('auth_token')) {
        next('/admin/login');
    } else {
        next();
    }
});

// Create app
const app = createApp(App);
app.use(router);
app.use(createPinia());
app.mount('#app');