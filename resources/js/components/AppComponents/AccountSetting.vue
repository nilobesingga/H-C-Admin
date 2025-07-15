<template>
<div class="relative max-w-xl p-8 px-6 mx-auto overflow-hidden bg-white shadow-2xl sm:px-10 rounded-2xl account-setting card">
    <div class="absolute pointer-events-none select-none -top-8 -right-8 opacity-10">
        <svg width="160" height="160" fill="none" viewBox="0 0 160 160">
            <circle cx="80" cy="80" r="80" fill="#3B82F6" />
        </svg>
    </div>
    <form @submit.prevent="submitForm" class="relative z-10 px-4 space-y-8">
        <div class="flex items-center gap-3 mb-2">
            <a href="/dashboard" class="flex items-center justify-center w-8 h-8 mr-2 text-blue-600 bg-blue-100 rounded-full hover:bg-blue-200 focus:outline-none" aria-label="Back to Dashboard">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <h1 class="text-3xl font-extrabold text-gray-800">Account Settings</h1>
        </div>
        <p class="mb-6 text-gray-500">Update your username and password. <span class="font-medium text-blue-600">Leave password fields blank to keep your current password.</span></p>
        <div>
            <label class="block mb-1 font-semibold text-gray-700" for="username">Username</label>
            <input id="username" v-model="form.username" type="text" class="w-full px-4 py-2 transition border border-gray-200 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-400 focus:outline-none bg-gray-50" required />
        </div>
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <div>
                <label class="block mb-1 font-semibold text-gray-700" for="password">New Password</label>
                <input id="password" v-model="form.password" type="password" class="w-full px-4 py-2 transition border border-gray-200 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-400 focus:outline-none bg-gray-50" minlength="6" autocomplete="new-password" placeholder="••••••••" />
            </div>
            <div>
                <label class="block mb-1 font-semibold text-gray-700" for="password_confirmation">Confirm Password</label>
                <input id="password_confirmation" v-model="form.password_confirmation" type="password" class="w-full px-4 py-2 transition border border-gray-200 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-400 focus:outline-none bg-gray-50" :class="{'border-red-500': passwordMismatch}" autocomplete="new-password" placeholder="••••••••" />
                <transition name="fade">
                    <p v-if="passwordMismatch" class="mt-1 text-xs text-red-500">Passwords do not match.</p>
                </transition>
            </div>
        </div>
        <button type="submit" class="flex items-center justify-center w-full gap-2 py-3 mt-2 text-lg font-semibold text-white transition rounded-lg shadow-lg bg-gradient-to-r from-orange-500 to-orange-700 hover:from-orange-600 hover:to-orange-800 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-2" :disabled="loading">
            <svg v-if="loading" class="inline w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
            </svg>
            <span>{{ loading ? 'Saving...' : 'Save Changes' }}</span>
        </button>
        <transition name="fade">
            <p v-if="success" class="p-2 mt-4 font-medium text-center text-green-700 border border-green-200 rounded shadow bg-green-50">Account updated successfully!</p>
        </transition>
        <transition name="fade">
            <p v-if="error" class="p-2 mt-4 font-medium text-center text-red-700 border border-red-200 rounded shadow bg-red-50">{{ error }}</p>
        </transition>
    </form>
</div>
</template>

<script>
export default {
    name: 'account-setting',
    props: {
        page_data: {
            type: Object,
            required: true,
        },
        user: {
            type: Object,
            required: true,
        },
        contact: {
            type: Object,
            required: true,
        },
    },
    data() {
        return {
            form: {
                username: this.user.user_name || '',
                password: '',
                password_confirmation: '',
            },
            loading: false,
            success: false,
            error: '',
        };
    },
    computed: {
        passwordMismatch() {
            return (
                this.form.password &&
                this.form.password_confirmation &&
                this.form.password !== this.form.password_confirmation
            );
        },
    },
    methods: {
        async submitForm() {
            this.loading = true;
            this.success = false;
            this.error = '';
            if (this.passwordMismatch) {
                this.loading = false;
                return;
            }
            try {
                const response = await fetch('/api/account/update', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${this.user.access_token}`,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify(this.form),
                });
                if (!response.ok) {
                    const data = await response.json();
                    throw new Error(data.message || 'Failed to update account.');
                }
                this.success = true;
                this.form.password = '';
                this.form.password_confirmation = '';
            } catch (err) {
                this.error = err.message;
            } finally {
                this.loading = false;
            }
        },
    },
    mounted() {
        // var token = localStorage.setItem('access_token', this.user);
        // console.log('AccountSetting component mounted', this.user);
        this.form.username = this.user.user_name;
        // Optionally fetch current username
        // fetch('/api/account/info').then(...)
    },
};
</script>

<style scoped>
.account-setting {
    padding: 2rem 0;
    background: linear-gradient(135deg, #f8fafc 0%, #e0e7ef 100%);
}

.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.4s;
}

.fade-enter,
.fade-leave-to {
    opacity: 0;
}
</style>
