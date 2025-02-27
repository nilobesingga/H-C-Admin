<template>
    <div class="modal" data-modal="true" data-modal-backdrop-static="true" id="user_acl_modal">
        <div class="modal-content top-[5%] lg:max-w-[1500px]">
            <div class="modal-header">
                <h3 class="modal-title capitalize text-xl font-bold tracking-tight">ACL</h3>
                <button class="btn btn-xs btn-icon btn-light focus:!border-tec-active" data-modal-dismiss="true" @click="$emit('closeModal')">
                    <i class="ki-outline ki-cross" ></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="flex items-center gap-5">
                    <img class="rounded-full border-2 max-h-[70px] max-w-full ring-2 ring-tec-active border-white shadow-lg shadow-tec-active/30" data-modal-toggle="#modal_profile" :src="obj ? obj.profile.bitrix_profile_photo : null">
                    <div class="flex flex-col justify-center">
                        <div class="text-lg font-bold text-neutral-900 tracking-tight">
                            {{ obj ? obj.profile.bitrix_name : null }} {{ obj ? obj.profile.bitrix_last_name : null }}
                        </div>
                        <a class="text-neutral-500 text-sm hover:text-tec-active transition-all duration-300" :href="obj ? obj.email : null">{{ obj ? obj.email : null }}</a>
                    </div>
                </div>

                <div class="flex gap-3 pt-3 pb-3">
                    <!-- Modules and Permissions Table -->
                    <div class="w-3/5 flex">
                        <div class="flex flex-col gap-5 grow">
                            <div class="card rounded-none !shadow-sm bg-neutral-100 !border-neutral-200 hover:!border-tec-active transition-all duration-300 grow">
                                <div class="px-5 py-3 bg-white">
                                    <h3 class="card-title !text-neutral-800">Modules and Permissions</h3>
                                </div>
                                <div class="scrollable-x-auto">
                                    <table class="w-full">
                                        <thead>
                                            <tr class="bg-neutral-50 text-neutral-800 text-xs text-left tracking-tight border-b border-neutral-200/50">
                                                <th class="font-semibold py-2 pl-5">Module</th>
                                                <th class="font-semibold">View Only</th>
                                                <th class="font-semibold">Full Access</th>
                                            </tr>
                                        </thead>
                                        <tbody class="text-left text-xs tracking-tight">
                                            <template v-for="module in form_data.modules" :key="module.id">
                                                <!-- Parent Modules  -->
                                                <tr class="transition-all duration-300 text-neutral-800 bg-neutral-100 hover:!bg-white">
                                                    <td class="pl-5 py-2">
                                                        <div class="flex flex-col gap-2.5">
                                                            <label class="checkbox-group">
                                                                <input
                                                                    class="checkbox checkbox-sm"
                                                                    type="checkbox"
                                                                    :value="module.id"
                                                                    :checked="isParentChecked(module)"
                                                                    @change="toggleParentModule(module, $event)"
                                                                >
                                                                <span class="checkbox-label text-black font-semibold hover:text-tec-active transition-all duration-300">{{ module.name }}</span>
                                                            </label>
                                                        </div>
                                                    </td>

                                                    <!-- Check if Parent Module has children -->
                                                    <template v-if="module.children.length">
                                                        <td>
                                                            <label class="checkbox-group">
                                                                <input
                                                                    type="checkbox"
                                                                    class="checkbox checkbox-sm"
                                                                    :checked="hasParentPermission(module, 'view_only')"
                                                                    @change="setParentPermission(module, 'view_only', $event)"
                                                                >
                                                                <span class="checkbox-label text-black font-semibold hover:text-tec-active transition-all duration-300">All</span>
                                                            </label>
                                                        </td>
                                                        <td>
                                                            <label class="checkbox-group">
                                                                <input
                                                                    type="checkbox"
                                                                    class="checkbox checkbox-sm"
                                                                    :checked="hasParentPermission(module, 'full_access')"
                                                                    @change="setParentPermission(module, 'full_access', $event)"
                                                                >
                                                                <span class="checkbox-label text-black font-semibold hover:text-tec-active transition-all duration-300">All</span>
                                                            </label>
                                                        </td>
                                                    </template>

                                                    <!-- If Parent has NO children, treat as module itself -->
                                                    <template v-else>
                                                        <td class="text-left pl-2">
                                                            <input
                                                                type="checkbox"
                                                                class="checkbox checkbox-sm"
                                                                :checked="hasPermission(module.id, 'view_only')"
                                                                @change="setPermission(module.id, 'view_only', $event)"
                                                            >
                                                        </td>
                                                        <td class="text-left pl-2">
                                                            <input
                                                                type="checkbox"
                                                                class="checkbox checkbox-sm"
                                                                :checked="hasPermission(module.id, 'full_access')"
                                                                @change="setPermission(module.id, 'full_access', $event)"
                                                            >
                                                        </td>
                                                    </template>
                                                </tr>
                                                <!-- Add separator after each parent module -->
                                                <tr>
                                                    <td colspan="3">
                                                        <hr class="border">
                                                    </td>
                                                </tr>
                                                <!-- Child Modules -->
                                                <template v-for="child in module.children" :key="child.id">
                                                    <tr class="transition-all duration-300 text-neutral-800 bg-neutral-100 hover:!bg-white">
                                                        <td class="pl-6 py-2">
                                                            <div class="pl-5">
                                                                <label class="checkbox-group">
                                                                    <input
                                                                        class="checkbox checkbox-sm"
                                                                        type="checkbox"
                                                                        :value="child.id"
                                                                        :checked="isModuleChecked(child.id)"
                                                                        @change="toggleChildModule(child, module, $event)"
                                                                    >
                                                                    <span class="checkbox-label hover:text-tec-active transition-all duration-300">{{ child.name }}</span>
                                                                </label>
                                                            </div>
                                                        </td>
                                                        <td class="text-left pl-2">
                                                            <input
                                                                type="checkbox"
                                                                class="checkbox checkbox-sm"
                                                                :disabled="!isModuleChecked(child.id)"
                                                                :checked="hasPermission(child.id, 'view_only')"
                                                                @change="setPermission(child.id, 'view_only', $event)"
                                                            >
                                                        </td>
                                                        <td class="text-left pl-2">
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
                                                            <div class="pl-5">
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
                    <div class="w-2/5">
                        <div class="flex flex-col gap-5">
                            <div class="card rounded-none !shadow-sm !border-neutral-200 hover:!border-tec-active transition-all duration-300">
                                <div class="px-5 py-3">
                                    <h3 class="card-title !text-neutral-800">Categories</h3>
                                </div>
                                <div class="flex flex-col gap-2.5 pl-5 pb-2 bg-neutral-100 pt-2">
                                    <label class="checkbox-group">
                                        <input
                                            class="checkbox checkbox-sm"
                                            type="checkbox"
                                            :checked="areAllCategoriesSelected"
                                            @change="toggleSelectAllCategories($event)"
                                        >
                                        <span class="checkbox-label">All</span>
                                    </label>
                                    <div class="flex flex-col gap-0 ml-2" v-for="category in form_data.categories" :key="category.id">
                                        <label class="checkbox-group">
                                            <input
                                                class="checkbox checkbox-sm"
                                                type="checkbox"
                                                :name="category.name"
                                                :value="category.id"
                                                v-model="form.selected_category_ids"
                                            >
                                            <span class="checkbox-label hover:text-tec-active transition-all duration-300">{{ category.name }}</span>
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
                    <button class="secondary-btn !text-md font-semibold !border-2 focus:!border-tec-active !px-10" data-modal-dismiss="true" @click="$emit('closeModal')">
                        Cancel
                    </button>
                    <button
                        class="main-btn focus:!border-tec-active focus:!shadow-tec-active/30"
                        @click="save"
                        :disabled="crud_loading"
                    >
                        Save
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "user-acl-modal",
    props: ['obj_id'],
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

            if (module.children.length) {
                // If it has children, toggle all children
                module.children.forEach((child) => {
                    if (isChecked) {
                        if (!this.isModuleChecked(child.id)) {
                            this.form.selected_modules.push({ module_id: child.id, permission: 'view_only' });
                        }
                    } else {
                        this.form.selected_modules = this.form.selected_modules.filter(
                            (item) => item.module_id !== child.id
                        );
                    }
                });
            } else {
                // If it has no children, toggle the module itself
                if (isChecked) {
                    if (!this.isModuleChecked(module.id)) {
                        this.form.selected_modules.push({ module_id: module.id, permission: 'view_only' });
                    }
                } else {
                    this.form.selected_modules = this.form.selected_modules.filter(
                        (item) => item.module_id !== module.id
                    );
                }
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
            // return this.form.selected_modules.some((item) => item.module_id === moduleId);
            if (!this.form.selected_modules || this.form.selected_modules.length === 0) {
                return false;
            }
            return this.form.selected_modules.some((item) => item.module_id === moduleId);
        },
        isParentChecked(module) {
            // return module.children.every((child) => this.isModuleChecked(child.id));
            if (!this.form.selected_modules || this.form.selected_modules.length === 0) {
                return false;
            }

            if (module.children.length) {
                return module.children.every((child) => this.isModuleChecked(child.id));
            } else {
                return this.isModuleChecked(module.id);
            }
        },
        cleanUpModules() {
            // Remove modules from selected_modules if they have no permissions
            this.form.selected_modules = this.form.selected_modules.filter((item) => item.permission);
        },
        setParentPermission(module, permission, event) {
            const isChecked = event.target.checked;

            if (module.children.length) {
                // If Parent has children, apply permission to all children
                module.children.forEach((child) => {
                    this.form.selected_modules = this.form.selected_modules.filter(
                        (item) => item.module_id !== child.id
                    );

                    if (isChecked) {
                        this.form.selected_modules.push({ module_id: child.id, permission });
                    }
                });
            } else {
                // If Parent has no children, apply permission to itself
                this.form.selected_modules = this.form.selected_modules.filter(
                    (item) => item.module_id !== module.id
                );

                if (isChecked) {
                    this.form.selected_modules.push({ module_id: module.id, permission });
                }
            }

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
                // this.form.selected_category_ids = this.form_data.categories.map((category) => category.id);

                // Select all categories except "Test" (ID: 16)
                this.form.selected_category_ids = this.form_data.categories
                    .filter(category => category.id !== 16) // Exclude "Test"
                    .map(category => category.id);
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
