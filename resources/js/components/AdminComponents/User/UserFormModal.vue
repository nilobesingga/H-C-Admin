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
                <div class="flex flex-col items-center mb-5">
                    <img class="rounded-full border-2 max-h-[70px] max-w-full" data-modal-toggle="#modal_profile" :src="obj ? obj.profile.bitrix_profile_photo : null">
                    <div class="flex items-center gap-1.5">
                        <div class="text-lg leading-5 font-semibold text-gray-900">
                            {{ obj ? obj.profile.bitrix_name : null }} {{ obj ? obj.profile.bitrix_last_name : null }}
                        </div>
                    </div>
                    <div class="flex flex-wrap justify-center gap-1 lg:gap-4.5 text-sm">
                        <div class="flex gap-1.25 items-center">
                            <i class="ki-filled ki-sms text-gray-500 text-sm"></i>
                            <a class="text-gray-600 font-medium hover:text-primary" :href="obj ? obj.email : null">{{ obj ? obj.email : null }}</a>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-1 xl:grid-cols-3 gap-5 lg:gap-7.5">
                    <!-- Modules and Permissions Table -->
                    <div class="col-span-2">
                        <div class="flex flex-col gap-5 lg:gap-7.5">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Modules and Permissions</h3>
                                </div>
                                <div class="card-table scrollable-x-auto">
                                    <table class="table">
                                        <thead>
                                            <tr >
                                                <th class="text-left bg-cresco_blue text-white font-normal min-w-[300px]">Module</th>
                                                <th class="min-w-24 text-gray-700 font-normal text-center">View Only</th>
                                                <th class="min-w-24 text-gray-700 font-normal text-center">Full Access</th>
                                            </tr>
                                        </thead>
                                        <tbody class="text-gray-900 font-medium">
                                            <template v-for="module in form_data.modules" :key="module.id">
                                                <!-- Parent Modules  -->
                                                <tr class="bg-gray-200">
                                                    <td>
                                                        <div class="flex flex-col gap-2.5">
                                                            <label class="checkbox-group">
                                                                <input
                                                                    class="checkbox checkbox-sm"
                                                                    type="checkbox"
                                                                    :value="module.id"
                                                                    :checked="isParentChecked(module)"
                                                                    @change="toggleParentModule(module, $event)"
                                                                >
                                                                <span class="checkbox-label text-black font-bold">{{ module.name }}</span>
                                                            </label>
                                                        </div>
                                                    </td>
                                                    <td class="">
                                                        <label class="checkbox-group justify-center">
                                                            <input
                                                                type="checkbox"
                                                                class="checkbox checkbox-sm"
                                                                :checked="hasParentPermission(module, 'view_only')"
                                                                @change="setParentPermission(module, 'view_only', $event)"
                                                            >
                                                            <span class="checkbox-label">All</span>
                                                        </label>

                                                    </td>
                                                    <td>
                                                        <label class="checkbox-group justify-center">
                                                            <input
                                                                type="checkbox"
                                                                class="checkbox checkbox-sm"
                                                                :checked="hasParentPermission(module, 'full_access')"
                                                                @change="setParentPermission(module, 'full_access', $event)"
                                                            >
                                                            <span class="checkbox-label">All</span>
                                                        </label>
                                                    </td>
                                                </tr>
                                                <!-- Child Modules -->
                                                <template v-for="child in module.children" :key="child.id">
                                                    <tr>
                                                        <td>
                                                            <div class="pl-5">
                                                                <label class="checkbox-group">
                                                                    <input
                                                                        class="checkbox checkbox-sm"
                                                                        type="checkbox"
                                                                        :value="child.id"
                                                                        :checked="isModuleChecked(child.id)"
                                                                        @change="toggleChildModule(child, module, $event)"
                                                                    >
                                                                    <span class="checkbox-label">{{ child.name }}</span>
                                                                </label>
                                                            </div>
                                                        </td>
                                                        <td class="text-center">
                                                            <input
                                                                type="checkbox"
                                                                class="checkbox checkbox-sm"
                                                                :disabled="!isModuleChecked(child.id)"
                                                                :checked="hasPermission(child.id, 'view_only')"
                                                                @change="setPermission(child.id, 'view_only', $event)"
                                                            >
                                                        </td>
                                                        <td class="text-center">
                                                            <input
                                                                type="checkbox"
                                                                class="checkbox checkbox-sm"
                                                                :disabled="!isModuleChecked(child.id)"
                                                                :checked="hasPermission(child.id, 'full_access')"
                                                                @change="setPermission(child.id, 'full_access', $event)"
                                                            >
                                                        </td>
                                                    </tr>
                                                    <!-- Grand Child Modules -->
                                                    <tr v-for="grandChild in child.children" :key="grandChild.id">
                                                        <td>
                                                            <div class="pl-10">
                                                                <label class="checkbox-group">
                                                                    <input
                                                                        class="checkbox checkbox-sm"
                                                                        type="checkbox"
                                                                        :value="grandChild.id"
                                                                        :checked="isModuleChecked(grandChild.id)"
                                                                        @change="toggleChildModule(grandChild, child, $event)"
                                                                    >
                                                                    <span class="checkbox-label">{{ grandChild.name }}</span>
                                                                </label>
                                                            </div>
                                                        </td>
                                                        <td class="text-center">
                                                            <input
                                                                type="checkbox"
                                                                class="checkbox checkbox-sm"
                                                                :disabled="!isModuleChecked(grandChild.id)"
                                                                :checked="hasPermission(grandChild.id, 'view_only')"
                                                                @change="setPermission(grandChild.id, 'view_only', $event)"
                                                            >
                                                        </td>
                                                        <td class="text-center">
                                                            <input
                                                                type="checkbox"
                                                                class="checkbox checkbox-sm"
                                                                :disabled="!isModuleChecked(grandChild.id)"
                                                                :checked="hasPermission(grandChild.id, 'full_access')"
                                                                @change="setPermission(grandChild.id, 'full_access', $event)"
                                                            >
                                                        </td>
                                                    </tr>
                                                </template>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Categories -->
                    <div class="col-span-1">
                        <div class="flex flex-col gap-5 lg:gap-7.5">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Categories</h3>
                                </div>
                                <div class="card-body flex flex-col gap-2.5">
                                    <label class="checkbox-group">
                                        <input
                                            class="checkbox checkbox-sm"
                                            type="checkbox"
                                            :checked="areAllCategoriesSelected"
                                            @change="toggleSelectAllCategories($event)"
                                        >
                                        <span class="checkbox-label">All</span>
                                    </label>
                                    <div class="flex flex-col gap-2.5 ml-5" v-for="category in form_data.categories" :key="category.id">
                                        <label class="checkbox-group">
                                            <input
                                                class="checkbox checkbox-sm"
                                                type="checkbox"
                                                :name="category.name"
                                                :value="category.id"
                                                v-model="form.selected_category_ids"
                                            >
                                            <span class="checkbox-label">{{ category.name }}</span>
                                        </label>
                                    </div>
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
                        :disabled="crud_loading"
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
                selected_category_ids: [],
                selected_modules: [],
            },
            form_data: {
                modules: [],
                categories: [],
            },
            crud_loading: false,
        }
    },
    methods: {
        save(){
            this.crud_loading = true
            axios({
                url: `/admin/settings/user/save/${this.obj_id}`,
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
                this.form_data.modules = response.data.modules;
                this.form_data.categories = response.data.categories;
                this.form.selected_category_ids = response.data.selected_category_ids;
                this.form.selected_modules = response.data.selected_modules;
            }).catch(error => {
                console.log(error)
            }).finally(() => {
                this.loading = false;
            })
        },
        toggleParentModule(module, event) {
            const isChecked = event.target.checked;
            if (isChecked) {
                module.children.forEach((child) => {
                    if (!this.isModuleChecked(child.id)) {
                        this.form.selected_modules.push({ module_id: child.id, permission: 'view_only' });
                    }
                });
            } else {
                module.children.forEach((child) => {
                    this.form.selected_modules = this.form.selected_modules.filter(
                        (item) => item.module_id !== child.id
                    );
                });
            }

            // Clean up any redundant modules
            this.cleanUpModules();
        },
        toggleChildModule(child, parent, event) {
            const isChecked = event.target.checked;
            if (isChecked) {
                if (!this.isModuleChecked(child.id)) {
                    this.form.selected_modules.push({ module_id: child.id, permission: 'view_only' });
                }
            } else {
                this.form.selected_modules = this.form.selected_modules.filter((item) => item.module_id !== child.id);
            }
        },
        setPermission(moduleId, permission, event) {
            if (event.target.checked) {
                // Remove any existing permissions for this module
                this.form.selected_modules = this.form.selected_modules.filter((item) => item.module_id !== moduleId);
                this.form.selected_modules.push({ module_id: moduleId, permission });
            } else {
                // If no permissions are selected, remove the module from the list
                this.form.selected_modules = this.form.selected_modules.filter((item) => item.module_id !== moduleId);
            }

            // Automatically uncheck the module if no permissions remain
            this.cleanUpModules();
        },
        hasPermission(moduleId, permission) {
            return this.form.selected_modules.some(
                (item) => item.module_id === moduleId && item.permission === permission
            );
        },
        isModuleChecked(moduleId) {
            return this.form.selected_modules.some((item) => item.module_id === moduleId);
        },
        isParentChecked(module) {
            return module.children.every((child) => this.isModuleChecked(child.id));
        },
        cleanUpModules() {
            // Remove modules from selected_modules if they have no permissions
            this.form.selected_modules = this.form.selected_modules.filter((item) => item.permission);
        },
        setParentPermission(module, permission, event) {
            const isChecked = event.target.checked;
            if (isChecked) {
                module.children.forEach((child) => {
                    // Remove any existing permissions for this child
                    this.form.selected_modules = this.form.selected_modules.filter(
                        (item) => item.module_id !== child.id
                    );

                    // Add the selected permission
                    this.form.selected_modules.push({ module_id: child.id, permission });
                });
            } else {
                // Remove the selected permission from all children
                module.children.forEach((child) => {
                    this.form.selected_modules = this.form.selected_modules.filter(
                        (item) => item.module_id !== child.id || item.permission !== permission
                    );
                });
            }

            // Clean up modules if no permissions remain
            this.cleanUpModules();
        },
        hasParentPermission(module, permission) {
            return module.children.every((child) =>
                this.form.selected_modules.some(
                    (item) => item.module_id === child.id && item.permission === permission
                )
            );
        },
        toggleSelectAllCategories(event) {
            const isChecked = event.target.checked;
            if (isChecked) {
                // Add all category IDs to the selected list
                this.form.selected_category_ids = this.form_data.categories.map((category) => category.id);
            } else {
                // Clear the selected categories
                this.form.selected_category_ids = [];
            }
        },
    },
    computed: {
        areAllCategoriesSelected() {
            return (
                this.form.selected_category_ids.length === this.form_data.categories.length &&
                this.form_data.categories.length > 0
            );
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
