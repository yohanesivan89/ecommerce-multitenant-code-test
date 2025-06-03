<template>
    <div class="container">
        <h1>Shopping Cart</h1>
        <div v-if="items.length === 0" class="empty-cart">
            Your cart is empty
        </div>
        <div v-else>
            <div class="cart-items">
                <div v-for="item in items" :key="`${item.tenant_id}-${item.id}`" class="cart-item">
                    <div class="item-info">
                        <h3>{{ item.name }}</h3>
                        <p>Store: {{ item.tenant_name }}</p>
                        <p>Price: ${{ item.price }}</p>
                    </div>
                    <div class="item-actions">
                        <input 
                            type="number" 
                            v-model.number="item.quantity" 
                            @change="updateQuantity(item)"
                            min="1"
                            :max="item.stock"
                        >
                        <button @click="removeItem(item)" class="btn-remove">Remove</button>
                    </div>
                </div>
            </div>
            <div class="cart-summary">
                <h2>Total: ${{ total.toFixed(2) }}</h2>
                <button @click="clearCart" class="btn btn-secondary">Clear Cart</button>
            </div>
        </div>
    </div>
</template>

<script>
import { computed } from 'vue';
import { useCartStore } from '../stores/cart';

export default {
    setup() {
        const cartStore = useCartStore();
        
        const items = computed(() => cartStore.items);
        const total = computed(() => cartStore.total);
        
        const updateQuantity = (item) => {
            cartStore.updateQuantity(item.tenant_id, item.id, item.quantity);
        };
        
        const removeItem = (item) => {
            cartStore.removeItem(item.tenant_id, item.id);
        };
        
        const clearCart = () => {
            cartStore.clearCart();
        };
        
        return {
            items,
            total,
            updateQuantity,
            removeItem,
            clearCart
        };
    }
}
</script>

<style scoped>
.empty-cart {
    text-align: center;
    padding: 3rem;
    font-size: 1.2rem;
    color: #666;
}

.cart-items {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.cart-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.item-actions {
    display: flex;
    gap: 1rem;
    align-items: center;
}

.item-actions input {
    width: 60px;
    padding: 0.25rem;
}

.btn-remove {
    background: #e74c3c;
    color: white;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 4px;
    cursor: pointer;
}

.cart-summary {
    margin-top: 2rem;
    text-align: right;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 4px;
}

.btn-secondary {
    background: #6c757d;
}

.btn-secondary:hover {
    background: #5a6268;
}
</style>