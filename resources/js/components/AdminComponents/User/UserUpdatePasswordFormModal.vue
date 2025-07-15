<template>
    <div class="modal" data-modal="true" data-modal-backdrop-static="true" id="user_update_password_form_modal">
        <div class="modal-content top-[5%] lg:max-w-[800px]">
            <div class="modal-header">
                <h3 class="text-xl font-bold tracking-tight capitalize modal-title">Update Password</h3>
                <button class="btn btn-xs btn-icon btn-light focus:!border-tec-active" data-modal-dismiss="true" @click="$emit('closeModal')">
                    <i class="ki-outline ki-cross" ></i>
                </button>
            </div>
            <form @submit.prevent="save">
                <div class="modal-body">
                    <div class="flex items-center">
                        <div class="w-5/6">
                            <div class="flex items-center gap-5">
                                <img class="rounded-full border-2 max-h-[60px] max-w-full ring-2 ring-tec-active border-white shadow-lg shadow-tec-active/30" data-modal-toggle="#modal_profile" :src="obj ? '/storage/'+obj.profile.photo : null">
                                <div class="flex flex-col justify-center">
                                    <div class="text-lg font-bold tracking-tight text-neutral-900">
                                        {{ obj ? obj.profile.name : null }}
                                    </div>
                                    <a class="text-sm transition-all duration-300 text-neutral-500 hover:text-tec-active" :href="obj ? obj.email : null">{{ obj ? obj.email : null }}</a>
                                </div>
                            </div>
                        </div>
                        <div class="w-1/6">
                            <span
                                :class="obj?.is_default_password ? 'bg-red-100 text-red-800 border-red-400' : 'bg-green-100 text-green-800 border-green-400'"
                                class="text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm dark:bg-gray-700 dark:text-green-400 border"
                            >
                            {{ obj?.is_default_password ? 'Not Updated' : 'Updated' }}
                        </span>
                        </div>
                    </div>
                    <div class="flex mt-10">
                        <div class="mb-4 w-full gap-2.5">
                            <label class="flex items-center gap-1 mb-1 text-sm form-label" for="password">Password</label>
                            <input class="input input-sm" id="password" type="text" v-model="form.password" required autofocus="autofocus">
                        </div>
                    </div>
                </div>
                <div class="justify-end modal-footer">
                    <div class="flex gap-4">
                        <button
                            type="button"
                            class="secondary-btn !text-md font-semibold !border-2 focus:!border-tec-active !px-10"
                            data-modal-dismiss="true"
                            @click="$emit('closeModal')">
                            Cancel
                        </button>
                        <button
                            class="main-btn focus:!border-tec-active focus:!shadow-tec-active/30"
                            type="submit"
                            :disabled="crud_loading"
                        >
                            <span v-if="!crud_loading">Save</span>
                            <span v-else>Saving...</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
export default {
    name: "user-update-password-form-modal",
    props: ['obj_id', 'modal_type'],
    data(){
        return {
            obj: null,
            form: {
                password: ""
            },
            crud_loading: false,
        }
    },
    methods: {
        save(){
            this.crud_loading = true
            axios({
                url: `/admin/settings/user/${this.obj_id}/update-password`,
                method: 'POST',
                data: this.form
            }).then(response => {
                this.successToast(response.data.message);
                this.$emit('closeModal');
            }).catch(error => {
                if (error.status === 500){
                    this.errorToast(error.response.data.message)
                }
            }).finally(() => {
                this.crud_loading = false;
            })
        },
        getObjById(){
            this.loading = true
            axios({
                url: `/admin/settings/user/${this.obj_id}`,
                method: 'GET',
            }).then(response => {
                this.obj = response.data.obj;
            }).catch(error => {
                console.log(error)
            }).finally(() => {
                this.loading = false;
            })
        },
    },
    mounted() {
        if(this.obj_id){
            this.getObjById()
        }
    }
}
</script>

<style scoped>
</style>
