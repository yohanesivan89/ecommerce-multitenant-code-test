<template>
    <div class="container">
        <div class="dashboard-header">
            <h1>Admin Dashboard - {{ tenantName }}</h1>
            <button @click="logout" class="btn btn-secondary">Logout</button>
        </div>
        
        <div class="dashboard-content">
            <div class="section">
                <h2>Add New Product</h2>
                <form @submit.prevent="addProduct" class="product-form">
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" v-model="newProduct.name" required>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea v-model="newProduct.description"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Price</label>
                        <input type="number" v-model.number="newProduct.price" step="0.01" min="0" required>
                    </div>
                    <div class="form-group">
                        <label>Stock</label>
                        <input type="number" v-model.number="newProduct.stock" min="0" required>
                    </div>
                    <div class="form-group">
                        <label>Image</label>
                        <input type="file" @change="handleImageUpload" accept="image/*">
                    </div>
                    <button type="submit" class="btn btn-primary">Add Product</button>
                </form>
            </div>
            
            <div class="section">
                <h2>Manage Products</h2>
                <div class="products-table">
                    <table>
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="product in products" :key="product.id">
                                <td>{{ product.name }}</td>
                                <td>${{ product.price }}</td>
                                <td>{{ product.stock }}</td>
                                <td>
                                    <button @click="editProduct(product)" class="btn btn-sm">Edit</button>
                                    <button @click="deleteProduct(product.id)" class="btn btn-sm btn-danger">Delete</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Edit Modal -->
        <div v-if="editingProduct" class="modal">
            <div class="modal-content">
                <h2>Edit Product</h2>
                <form @submit.prevent="updateProduct">
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" v-model="editingProduct.name" required>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea v-model="editingProduct.description"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Price</label>
                        <input type="number" v-model.number="editingProduct.price" step="0.01" min="0" required>
                    </div>
                    <div class="form-group">
                        <label>Stock</label>
                        <input type="number" v-model.number="editingProduct.stock" min="0" required>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Update</button>
                        <button type="button" @click="editingProduct = null" class="btn btn-secondary">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

export default {
    setup() {
        const router = useRouter();
        const products = ref([]);
        const tenantName = ref(localStorage.getItem('tenant_name') || '');
        const editingProduct = ref(null);
        const newProduct = ref({
            name: '',
            description: '',
            price: 0,
            stock: 0,
            image: null
        });
        
        // Set auth headers
        const token = localStorage.getItem('auth_token');
        const tenantId = localStorage.getItem('tenant_id');
        
        if (token && tenantId) {
            axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
            axios.defaults.headers.common['X-Tenant-ID'] = tenantId;
        }
        
        const fetchProducts = async () => {
            try {
                const response = await axios.get('/products');
                products.value = response.data;
            } catch (error) {
                console.error('Error fetching products:', error);
                if (error.response?.status === 401) {
                    router.push('/admin/login');
                }
            }
        };
        
        const handleImageUpload = (event) => {
            newProduct.value.image = event.target.files[0];
        };
        
        const addProduct = async () => {
            try {
                const formData = new FormData();
                Object.keys(newProduct.value).forEach(key => {
                    if (newProduct.value[key] !== null) {
                        formData.append(key, newProduct.value[key]);
                    }
                });
                
                await axios.post('/products', formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });
                
                // Reset form
                newProduct.value = {
                    name: '',
                    description: '',
                    price: 0,
                    stock: 0,
                    image: null
                };
                
                fetchProducts();
                alert('Product added successfully!');
            } catch (error) {
                console.error('Error adding product:', error);
                alert('Error adding product');
            }
        };
        
        const editProduct = (product) => {
            editingProduct.value = { ...product };
        };
        
        const updateProduct = async () => {
            try {
                await axios.put(`/products/${editingProduct.value.id}`, editingProduct.value);
                editingProduct.value = null;
                fetchProducts();
                alert('Product updated successfully!');
            } catch (error) {
                console.error('Error updating product:', error);
                alert('Error updating product');
            }
        };
        
        const deleteProduct = async (id) => {
            if (confirm('Are you sure you want to delete this product?')) {
                try {
                    await axios.delete(`/products/${id}`);
                    fetchProducts();
                    alert('Product deleted successfully!');
                } catch (error) {
                    console.error('Error deleting product:', error);
                    alert('Error deleting product');
                }
            }
        };
        
        const logout = () => {
            localStorage.removeItem('auth_token');
            localStorage.removeItem('tenant_id');
            localStorage.removeItem('tenant_name');
            delete axios.defaults.headers.common['Authorization'];
            delete axios.defaults.headers.common['X-Tenant-ID'];
            router.push('/admin/login');
        };
        
        onMounted(fetchProducts);
        
        return {
            products,
            tenantName,
            newProduct,
            editingProduct,
            handleImageUpload,
            addProduct,
            editProduct,
            updateProduct,
            deleteProduct,
            logout,
            fetchProducts
        };
    }
}
</script>

<style scoped>
.dashboard-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.section {
    margin-bottom: 3rem;
}

.product-form {
    max-width: 500px;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 8px;
}

.products-table {
    overflow-x: auto;
}

table {
    width: 100%;
    border-collapse: collapse;
}

th, td {
    padding: 0.75rem;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

th {
    background: #f8f9fa;
    font-weight: bold;
}

.btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
    margin-right: 0.5rem;
}

.btn-danger {
    background: #e74c3c;
}

.btn-danger:hover {
    background: #c0392b;
}

.modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
}

.modal-content {
    background: white;
    padding: 2rem;
    border-radius: 8px;
    max-width: 500px;
    width: 100%;
}

.form-actions {
    display: flex;
    gap: 1rem;
    margin-top: 1rem;
}
</style>