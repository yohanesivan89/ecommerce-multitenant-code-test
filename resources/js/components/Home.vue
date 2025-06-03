<template>
    <div class="container">
        <h1>Welcome to Multi-Store Marketplace</h1>
        <div class="stores-grid">
            <div v-for="store in stores" :key="store.id" class="store-card">
                <h2>{{ store.name }}</h2>
                <p>{{ store.description }}</p>
                <router-link :to="`/store/${store.slug}`" class="btn">
                    Visit Store
                </router-link>
            </div>
        </div>
    </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import axios from 'axios';

export default {
    setup() {
        const stores = ref([]);
        
        const fetchStores = async () => {
            try {
                const response = await axios.get('/tenants');
                stores.value = response.data;
            } catch (error) {
                console.error('Error fetching stores:', error);
            }
        };
        
        onMounted(fetchStores);
        
        return { stores };
    }
}
</script>

<style scoped>
.stores-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 2rem;
    margin-top: 2rem;
}

.store-card {
    border: 1px solid #ddd;
    padding: 1.5rem;
    border-radius: 8px;
    text-align: center;
}

.btn {
    display: inline-block;
    background: #3498db;
    color: white;
    padding: 0.5rem 1rem;
    text-decoration: none;
    border-radius: 4px;
    margin-top: 1rem;
}

.btn:hover {
    background: #2980b9;
}
</style>