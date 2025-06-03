<template>
    <div class="container">
        <div v-if="loading">Loading...</div>
        <div v-else>
            <h1>{{ store.name }}</h1>
            <p>{{ store.description }}</p>
            
            <div class="products-grid">
                <div v-for="product in products" :key="product.id" class="product-card">
                    <img v-if="product.image_path" :src="`/storage/${product.image_path}`" :alt="product.name">
                    <div class="product-info">
                        <h3>{{ product.name }}</h3>
                        <p>{{ product.description }}</p>
                        <p class="price">${{ product.price }}</p>
                        <p class="stock">Stock: {{ product.stock }}</p>
                        <button 
                            @click="addToCart(product)" 
                            class="btn"
                            :disabled="product.stock === 0"
                        >
                            {{ product.stock === 0 ? 'Out of Stock' : 'Add to Cart' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import { useCartStore } from '../stores/cart';
import axios from 'axios';

export default {
    setup() {
        const route = useRoute();
        const cartStore = useCartStore();
        const store = ref({});
        const products = ref([]);
        const loading = ref(true);
        
        const fetchProducts = async () => {
            try {
                const response = await axios.get(`/stores/${route.params.slug}/products`);
                store.value = response.data.tenant;
                products.value = response.data.products;
            } catch (error) {
                console.error('Error fetching products:', error);
            } finally {
                loading.value = false;
            }
        };
        
        const addToCart = (product) => {
            cartStore.addItem({
                ...product,
                tenant_id: store.value.id,
                tenant_name: store.value.name
            });
            alert('Product added to cart!');
        };
        
        onMounted(fetchProducts);
        
        return {
            store,
            products,
            loading,
            addToCart
        };
    }
}
</script>

<style scoped>
.products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 2rem;
    margin-top: 2rem;
}

.product-card {
    border: 1px solid #ddd;
    border-radius: 8px;
    overflow: hidden;
}

.product-card img {
    width: 100%;
    height: 200px;
    object-fit: cover;
}

.product-info {
    padding: 1rem;
}

.price {
    font-size: 1.2rem;
    font-weight: bold;
    color: #27ae60;
}

.stock {
    color: #666;
    font-size: 0.9rem;
}

.btn:disabled {
    background: #95a5a6;
    cursor: not-allowed;
}
</style>