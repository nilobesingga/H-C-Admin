<template>
    <div class="modal" data-modal="true" data-modal-backdrop-static="true" id="user_form_modal">
        <div class="modal-content top-[5%] lg:max-w-[1500px]">
            <div class="modal-header">
                <h3 class="modal-title capitalize">{{ modal_type }} User</h3>
                <button class="btn btn-xs btn-icon btn-light" data-modal-dismiss="true" @click="$emit('closeModal')">
                    <i class="ki-outline ki-cross" ></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="flex flex-col items-center gap-2 lg:gap-3.5 py-4 lg:pt-5 lg:pb-10">
                    <img class="rounded-full border-2 max-h-[70px] max-w-full" data-modal-toggle="#modal_profile" :src="obj ? obj.profile.bitrix_profile_photo : null">
                    <div class="flex items-center gap-1.5">
                        <div class="text-lg leading-5 font-semibold text-gray-900">
                            {{ obj ? obj.profile.bitrix_name : null }}
                        </div>
                    </div>
<!--                    <div class="flex flex-wrap justify-center gap-1 lg:gap-4.5 text-sm">-->
<!--                        <div class="flex gap-1.25 items-center">-->
<!--                            <i class="ki-filled ki-sms text-gray-500 text-sm"></i>-->
<!--                            <a class="text-gray-600 font-medium hover:text-primary" :href="obj ? obj.email : null">-->
<!--                                {{ obj ? obj.email : null }}-->
<!--                            </a>-->
<!--                        </div>-->
<!--                    </div>-->
                </div>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 lg:gap-7.5">
                    <!-- Modules -->
                    <div class="col-span-2 lg:col-span-1 flex">
                        <div class="card grow">
                            <div class="card-header">
                                <h3 class="card-title">Modules</h3>
                            </div>
                            <div class="card-body pt-4 pb-3">
                                <div class="flex flex-col items-start gap-4">
                                    <label class="form-label flex items-center gap-2.5">
<!--                                        <input class="checkbox checkbox-sm" name="check" type="checkbox" :value="{module}" v-model="form.module" />-->
                                        <span>Company Dashboard</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Categories -->
                    <div class="col-span-2 lg:col-span-1 flex">
                        <div class="card grow">
                            <div class="card-header">
                                <h3 class="card-title">Categories</h3>
                            </div>
                            <div class="card-body pt-4 pb-3">
                                <div class="flex flex-col items-start gap-4">
                                    <label class="form-label flex items-center gap-2.5" v-for="category in form_data.categories" :key="category.id">
                                        <input class="checkbox checkbox-sm" name="check" type="checkbox" :value="category.id" v-model="form.category_ids" />
                                        <span>{{ category.name }}</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer justify-end">
                <div class="flex gap-4">
                    <button class="btn btn-light" data-modal-dismiss="true" @click="$emit('closeModal')">
                        Cancel
                    </button>
                    <button
                        class="btn btn-primary"
                        @click="save"
                        :disabled="form.category_ids.length === 0 || crud_loading"
                    >
                        Submit
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "user-form-modal",
    props: ['obj_id', 'modal_type'],
    data(){
        return {
            obj: null,
            form: {
                category_ids: []
            },
            form_data: {
                modules: [],
                categories: [],
            },
            crud_loading: false
        }
    },
    methods: {
        save(){
            this.crud_loading = true
            axios({
                url: `/admin/acl/save/${this.obj_id}`,
                method: 'POST',
                data: this.form
            }).then(response => {
                this.$emit('closeModal')
            }).catch(error => {
                console.log(error)
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
                this.form_data.modules = response.data.modules;
                this.form_data.categories = response.data.categories;
                this.form.category_ids = response.data.user_category_ids
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
