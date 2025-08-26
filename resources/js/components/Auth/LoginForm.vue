<template>
<div class="flex items-center justify-end w-full h-full bg-top bg-no-repeat bg-cover grow page-bg">
    <div class="absolute top-10 right-20">
        <img class="w-50" :src="logo" alt="Logo" />
    </div>
    <div class="flex w-full h-full">
        <!-- Left side - can be used for an image or additional content -->
        <div class="items-center justify-center hidden md:flex md:w-1/2">
        </div>
        <!-- Right side - contains the login form -->
        <div class="relative flex items-center justify-center w-full md:w-1/2">
            <!-- The login card will be positioned here -->
            <div class="bg-white z-5 border rounded-md hover:border-black/20 ring-0 hover:ring-8 ring-white/70 transition-all duration-300 backdrop-blur shadow-sm hover:shadow-2xl hover:shadow-black/5 p-7 max-w-[500px] w-full">
                <form @submit.prevent="handleLogin" class="flex flex-col gap-5 p-10 card-body">
                    <div class="mb-10 text-center">
                        <h2 class="mb-3 text-2xl font-black">Welcome !</h2>
                        <span class="text-sm text-gray-700">Admin Portal: Secure Access</span>
                    </div>
                    <!-- General Error Message -->
                    <div v-if="errors.general" class="p-3 mb-4 text-sm text-red-500 bg-red-100 rounded">
                        {{ errors.general }}
                    </div>
                    <div class="flex flex-col gap-1 mb-2">
                        <label class="block mb-1 text-sm font-medium" for="login">Username</label>
                        <input
                            id="login"
                            v-model="form.login"
                            type="text"
                            class="input input-normal"
                            :class="{ 'border-red-500': errors.login }"
                            placeholder="Username"
                            autofocus
                        />
                        <div v-if="errors.login" class="mt-1 text-sm text-red-500">{{ errors.login }}</div>
                    </div>
                    <div class="flex flex-col gap-1 mb-2">
                        <label class="block mb-1 text-sm font-medium" for="password">Password</label>
                        <div data-toggle-password="true">
                            <input
                                id="password"
                                v-model="form.password"
                                type="password"
                                class="input input-normal"
                                :class="{ 'border-red-500': errors.password }"
                                placeholder="********"
                                required
                            />
                            <div v-if="errors.password" class="mt-1 text-sm text-red-500">{{ errors.password }}</div>
                        </div>
                    </div>
                    <div class="flex items-center justify-between mb-4">
                        <!-- Remember Me Toggle Switch -->
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" v-model="form.remember" class="hidden peer" id="remember">
                            <span class="relative w-10 h-5 transition-colors duration-200 bg-gray-300 rounded-full peer-checked:bg-blue-600">
                                <span class="absolute w-3 h-3 transition-all duration-200 bg-white rounded-full left-1 top-1 peer-checked:left-6"></span>
                            </span>
                            <span class="text-sm text-gray-700">Remember me</span>
                        </label>
                        <!-- Forgot Password Link -->
                        <a href="" class="text-sm text-blue-600 hover:underline">Forgot password?</a>
                    </div>

                    <button
                        type="submit"
                        :disabled="loading"
                        class="flex justify-center text-white main-btn text-md grow"
                        :class="{ 'opacity-75': loading }"
                        style="background-color: #313d4f; color: #fff;"
                    >
                        {{ loading ? 'Signing in...' : 'Sign in' }}
                    </button>
                </form>
            </div>
        </div>
    </div>
    <div class="absolute right-0 overflow-hidden opacity-15 -bottom-40">
        <img class="w-50" :src="seal" alt="Logo" />
    </div>
</div>
</template>

<script>
import axios from '../../plugins/axios'; // Fixed path from plugin to plugins

export default {
    name: 'login-form',
    data() {
        return {
            form: {
                login: '',
                password: '',
                remember: false
            },
            errors: {},
            loading: false,
            logo: '/img/Logo.svg',
            seal: '/img/HC-Seal_blue.png',
            errorTimeout: null
        };
    },
    methods: {
        clearErrors() {
            if (this.errorTimeout) {
                clearTimeout(this.errorTimeout);
            }
            this.errorTimeout = setTimeout(() => {
                this.errors = {};
            }, 5000); // Hide errors after 5 seconds
        },
        async handleLogin(){
            try {
                if (this.errorTimeout) {
                    clearTimeout(this.errorTimeout);
                }
                this.loading = true;
                this.errors = {};
                const response = await axios.post('/api/api-login', this.form);
                if (response.data.success) {
                    // Store the token and user data
                    localStorage.setItem('access_token', response.data.data.access_token);
                    localStorage.setItem('user', JSON.stringify(response.data.data.user));

                    // Set up axios for future requests
                    axios.defaults.headers.common['Authorization'] = `Bearer ${response.data.data.access_token}`;

                    // Check if user is admin before redirecting
                    const userData = response.data.data.user;
                    if (userData && userData.is_admin) {
                        // Use traditional form submission to establish session
                        const form = document.createElement('form');
                        form.method = 'GET';
                        form.action = '/login/' + response.data.data.access_token;

                        // Add CSRF token
                        const csrfToken = document.createElement('input');
                        csrfToken.type = 'hidden';
                        csrfToken.name = '_token';
                        csrfToken.value = response.data.data.access_token;
                        form.appendChild(csrfToken);

                        // Add credentials
                        const login = document.createElement('input');
                        login.type = 'hidden';
                        login.name = 'login';
                        login.value = this.form.login;
                        form.appendChild(login);

                        const password = document.createElement('input');
                        password.type = 'hidden';
                        password.name = 'password';
                        password.value = this.form.password;
                        form.appendChild(password);

                        document.body.appendChild(form);
                        form.submit();
                    } else {
                        this.errors = { general: 'You do not have admin access.' };
                        this.clearErrors();
                    }
                } else {
                    this.errors = { general: response.data.message || 'Login failed' };
                    this.clearErrors();
                }
            } catch (error) {
                console.error('Login error:', error);
                if (error.response?.status === 422) {
                    // Validation errors
                    this.errors = error.response.data.errors;
                } else {
                    // General error
                    this.errors = {
                        general: error.response?.data?.message || 'An error occurred during login'
                    };
                }
                this.clearErrors();
            } finally {
                this.loading = false;
            }
        }
    },
    beforeDestroy() {
        if (this.errorTimeout) {
            clearTimeout(this.errorTimeout);
        }
    },
};
</script>

<style scoped>
.input {
    width: 100%;
    padding: 0.75rem 1rem;
    font-size: 0.875rem;
    border: 1px solid #e2e8f0;
    border-radius: 0.375rem;
}

.input:focus {
    outline: none;
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.5);
}

.input-normal {
    border-color: #e2e8f0;
}

.main-btn {
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
    font-weight: 500;
    border-radius: 0.375rem;
    transition: all 0.2s;
}

.main-btn:disabled {
    cursor: not-allowed;
    opacity: 0.75;
}
</style>
