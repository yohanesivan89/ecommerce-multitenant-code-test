<template>
    <div class="container">
        <div class="login-form">
            <h1>Admin Login</h1>
            <form @submit.prevent="login">
                <div class="form-group">
                    <label>Store Slug</label>
                    <input type="text" v-model="form.tenant_slug" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" v-model="form.email" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" v-model="form.password" required>
                </div>
                <button type="submit" class="btn btn-primary">Login</button>
                <div v-if="error" class="error">{{ error }}</div>
            </form>
        </div>
    </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

export default {
    setup() {
        const token = localStorage.getItem('auth_token');
        const tenantId = localStorage.getItem('tenant_id');
        if (token && tenantId) {
            axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
            axios.defaults.headers.common['X-Tenant-ID'] = tenantId;
        }
        if (token && tenantId) {
            const router = useRouter();
            router.push('/admin/dashboard');
        }
        const router = useRouter();
        const stores = ref([]);
        const error = ref('');
        const form = ref({
            email: '',
            password: '',
            tenant_slug: ''
        });
        
        const fetchStores = async () => {
            try {
                const response = await axios.get('/tenants');
                stores.value = response.data;
            } catch (err) {
                console.error('Error fetching stores:', err);
            }
        };
        
        const login = async () => {
            try {
                error.value = '';
                // await axios.get('/sanctum/csrf-cookie', { baseURL: '' });
                const response = await axios.post('/login', form.value);
                
                console.log('Login response:', response.data);
                
                localStorage.setItem('auth_token', response.data.token);
                localStorage.setItem('tenant_id', response.data.tenant.id);
                localStorage.setItem('tenant_name', response.data.tenant.name);
                
                axios.defaults.headers.common['Authorization'] = `Bearer ${response.data.token}`;
                axios.defaults.headers.common['X-Tenant-ID'] = response.data.tenant.id;
                
                router.push('/admin/dashboard');
            } catch (err) {
                console.error('Login error details:', err);
                error.value = err.response?.data?.message || 'Login failed';
            }
        };
        
        onMounted(fetchStores);
        
        return {
            stores,
            form,
            error,
            login
        };
    }
}
</script>

<style scoped>
.login-form {
    max-width: 400px;
    margin: 0 auto;
    padding: 2rem;
    border: 1px solid #ddd;
    border-radius: 8px;
}

.form-group {
    margin-bottom: 1rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: bold;
}

.form-group input,
.form-group select {
    width: 100%;
    padding: 0.5rem;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.btn-primary {
    background: #3498db;
    width: 100%;
}

.error {
    color: #e74c3c;
    margin-top: 1rem;
    text-align: center;
}
</style>