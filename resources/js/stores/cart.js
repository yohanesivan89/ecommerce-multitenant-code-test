import { defineStore } from 'pinia';

export const useCartStore = defineStore('cart', {
    state: () => ({
        items: JSON.parse(localStorage.getItem('cart') || '[]')
    }),
    
    getters: {
        itemCount: (state) => state.items.reduce((sum, item) => sum + item.quantity, 0),
        total: (state) => state.items.reduce((sum, item) => sum + (item.price * item.quantity), 0)
    },
    
    actions: {
        addItem(product) {
            const existingItem = this.items.find(
                item => item.id === product.id && item.tenant_id === product.tenant_id
            );
            
            if (existingItem) {
                existingItem.quantity++;
            } else {
                this.items.push({
                    ...product,
                    quantity: 1
                });
            }
            
            this.saveCart();
        },
        
        updateQuantity(tenantId, productId, quantity) {
            const item = this.items.find(
                item => item.id === productId && item.tenant_id === tenantId
            );
            
            if (item) {
                item.quantity = Math.max(1, Math.min(quantity, item.stock));
                this.saveCart();
            }
        },
        
        removeItem(tenantId, productId) {
            const index = this.items.findIndex(
                item => item.id === productId && item.tenant_id === tenantId
            );
            
            if (index > -1) {
                this.items.splice(index, 1);
                this.saveCart();
            }
        },
        
        clearCart() {
            this.items = [];
            this.saveCart();
        },
        
        saveCart() {
            localStorage.setItem('cart', JSON.stringify(this.items));
        }
    }
});