<template>
    <div class="container">
        <div class="login-form">
            <h1>Add Tenant</h1>
            <form @submit.prevent="addtenant">
                <div class="form-group">
                    <label>Store Name</label>
                    <input type="text" v-model="form.tenant_name" required>
                </div>
                <div class="form-group">
                    <label>Store Desc</label>
                    <input type="text" v-model="form.tenant_desc" required>
                </div>
                <button type="submit" class="btn btn-primary">Add</button>
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
        const router = useRouter();
        const error = ref('');
        const form = ref({
            tenant_name: '',
            tenant_desc: ''
        });
        
        const addtenant = async () => {
            try {
                error.value = '';
                const response = await axios.post('/tenants/add', form.value);
                
                alert('Successfully added tenant with username: admin@' + response.data.slug + '.com and password: password');

            } catch (err) {
                error.value = err.response?.data?.message || 'Add Tenant Failed';
            }
        };
                
        return {
            form,
            addtenant
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