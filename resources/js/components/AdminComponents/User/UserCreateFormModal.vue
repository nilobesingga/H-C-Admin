<template>
    <div class="modal-mask">
        <div class="modal-wrapper">
            <div class="w-full max-w-lg p-6 bg-white rounded shadow-lg modal-container">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold">Create User</h3>
                    <button @click="$emit('closeModal')" class="text-gray-400 hover:text-gray-700">&times;</button>
                </div>
                <form @submit.prevent="handleSubmit">
                    <div class="mb-4">
                        <label class="block mb-1 font-medium">Name</label>
                        <input v-model="form.name" type="text" class="w-full input input-bordered" required />
                    </div>
                    <div class="mb-4">
                        <label class="block mb-1 font-medium">Email</label>
                        <input v-model="form.email" type="email" class="w-full input input-bordered" required />
                    </div>
                    <div class="mb-4">
                        <label class="block mb-1 font-medium">User Name</label>
                        <input v-model="form.user_name" type="text" class="w-full input input-bordered" required />
                    </div>
                    <div class="mb-4">
                        <label class="block mb-1 font-medium">Password</label>
                        <input v-model="form.password" type="password" class="w-full input input-bordered" required />
                    </div>
                    <div class="mb-4">
                        <label class="block mb-1 font-medium">User Type</label>
                        <select v-model="form.user_type" class="w-full input input-bordered" required>
                            <option value="admin">Admin</option>
                            <option value="user">User</option>
                            <option value="client">Client</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="inline-flex items-center">
                            <input type="checkbox" v-model="form.is_admin" class="form-checkbox" />
                            <span class="ml-2">Admin</span>
                        </label>
                    </div>
                    <div class="flex justify-end gap-2">
                        <button type="button" @click="$emit('closeModal')" class="secondary-btn">Cancel</button>
                        <button type="submit" class="secondary-btn">Create</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'user-create-form-modal',
    props: ['obj_id'],
    data() {
        return {
            form: {
                name: '',
                email: '',
                user_name: '',
                password: '',
                is_admin: false,
                user_type: 'user'
            },
            loading: false
        };
    },
    methods: {
        handleSubmit() {
            this.loading = true;
            // Replace with your actual API endpoint
            axios.post('/admin/settings/users/create', this.form)
                .then(() => {
                    this.$emit('closeModal');
                })
                .catch(error => {
                    alert('Failed to create user.');
                    console.error(error);
                })
                .finally(() => {
                    this.loading = false;
                });
        }
    }
};
</script>

<style scoped>
.modal-mask {
    position: fixed;
    z-index: 9998;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background-color: rgba(0, 0, 0, 0.3);
    display: flex;
    align-items: center;
    justify-content: center;
}
.modal-wrapper {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100vw;
    height: 100vh;
}
</style>
