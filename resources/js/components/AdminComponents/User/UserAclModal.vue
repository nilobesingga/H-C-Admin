<template>
    <div class="modal" data-modal="true" data-modal-backdrop-static="true" id="user_acl_modal">
        <div class="modal-content top-[5%] lg:max-w-[1500px]">
            <div class="modal-header">
                <h3 class="modal-title capitalize text-xl font-bold tracking-tight">ACL</h3>
                <button class="btn btn-xs btn-icon btn-light focus:!border-tec-active" data-modal-dismiss="true" @click="$emit('closeModal')">
                    <i class="ki-outline ki-cross"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="flex items-center gap-5">
                    <img class="rounded-full border-2 max-h-[70px] max-w-full ring-2 ring-tec-active border-white shadow-lg shadow-tec-active/30" data-modal-toggle="#modal_profile" :src="obj ? obj.profile.bitrix_profile_photo : null">
                    <div class="flex flex-col justify-center">
                        <div class="text-lg font-bold text-neutral-900 tracking-tight">
                            {{ obj ? obj.profile.bitrix_name : null }} {{ obj ? obj.profile.bitrix_last_name : null }}
                        </div>
                        <a class="text-neutral-500 text-sm hover:text-tec-active transition-all duration-300" :href="obj ? 'mailto:' + obj.email : null">{{ obj ? obj.email : null }}</a>
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
                                            <!-- Parent Modules -->
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
                        <span v-if="!crud_loading">Save</span>
                        <span v-else>Saving...</span>
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
    data() {
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
            moduleParentMap: {},
            moduleChildrenMap: {}, // New map to store module ID to child IDs
        }
    },
    methods: {
        getObjById() {
            this.crud_loading = true;
            axios({
                url: `/admin/settings/user/${this.obj_id}`,
                method: 'GET',
            }).then(response => {
                this.obj = response.data.obj;
                this.form_data.modules = response.data.modules;
                this.form_data.categories = response.data.categories;
                this.form.selected_category_ids = response.data.selected_category_ids || [];
                this.form.selected_modules = response.data.selected_modules || [];
                this.buildModuleMaps();
            }).catch(error => {
                console.error('Error fetching user data:', error);
                this.errorToast('Failed to load user data');
            }).finally(() => {
                this.crud_loading = false;
            });
        },
        buildModuleMaps() {
            this.moduleParentMap = {};
            this.moduleChildrenMap = {};
            const buildMaps = (modules, parentId = null) => {
                modules.forEach(module => {
                    this.moduleParentMap[module.id] = parentId;
                    if (parentId) {
                        if (!this.moduleChildrenMap[parentId]) {
                            this.moduleChildrenMap[parentId] = [];
                        }
                        this.moduleChildrenMap[parentId].push(module.id);
                    }
                    if (module.children) {
                        buildMaps(module.children, module.id);
                    }
                });
            };
            buildMaps(this.form_data.modules);
        },
        ensureModuleIncluded(moduleId, permission = 'view_only') {
            const existing = this.form.selected_modules.find(item => item.module_id === moduleId);
            if (!existing) {
                this.form.selected_modules.push({ module_id: moduleId, permission });
            }
        },
        ensureAncestorsIncluded(moduleId) {
            let currentId = this.moduleParentMap[moduleId];
            while (currentId) {
                this.ensureModuleIncluded(currentId, 'view_only');
                currentId = this.moduleParentMap[currentId];
            }
        },
        hasSelectedDescendants(moduleId) {
            const children = this.moduleChildrenMap[moduleId] || [];
            return children.some(childId => {
                if (this.isModuleChecked(childId)) {
                    return true;
                }
                return this.hasSelectedDescendants(childId);
            });
        },
        syncParentModules() {
            const parentsToRemove = [];
            for (const moduleId in this.moduleParentMap) {
                const parentId = this.moduleParentMap[moduleId];
                if (parentId && !this.hasSelectedDescendants(parentId) && !this.isExplicitlySelected(parentId)) {
                    parentsToRemove.push(parentId);
                }
            }
            this.form.selected_modules = this.form.selected_modules.filter(
                item => !parentsToRemove.includes(item.module_id)
            );
        },
        isExplicitlySelected(moduleId) {
            const module = this.form_data.modules.find(m => m.id === moduleId) ||
                this.form_data.modules.flatMap(m => m.children).find(m => m.id === moduleId) ||
                this.form_data.modules.flatMap(m => m.children).flatMap(c => c.children || []).find(m => m.id === moduleId);
            return this.isModuleChecked(moduleId) && (!module || !module.children || module.children.length === 0);
        },
        toggleParentModule(module, event) {
            const isChecked = event.target.checked;
            if (isChecked) {
                this.ensureModuleIncluded(module.id, 'view_only');
                if (module.children.length) {
                    module.children.forEach(child => {
                        if (!this.isModuleChecked(child.id)) {
                            this.form.selected_modules.push({ module_id: child.id, permission: 'view_only' });
                        }
                    });
                }
                this.ensureAncestorsIncluded(module.id);
            } else {
                this.form.selected_modules = this.form.selected_modules.filter(
                    item => item.module_id !== module.id
                );
                if (module.children.length) {
                    module.children.forEach(child => {
                        this.form.selected_modules = this.form.selected_modules.filter(
                            item => item.module_id !== child.id
                        );
                    });
                }
            }
            this.syncParentModules();
            this.cleanUpModules();
        },
        toggleChildModule(child, parent, event) {
            const isChecked = event.target.checked;
            if (isChecked) {
                if (!this.isModuleChecked(child.id)) {
                    this.form.selected_modules.push({ module_id: child.id, permission: 'view_only' });
                }
                this.ensureAncestorsIncluded(child.id);
            } else {
                this.form.selected_modules = this.form.selected_modules.filter(
                    item => item.module_id !== child.id
                );
                this.syncParentModules();
            }
            this.cleanUpModules();
        },
        setPermission(moduleId, permission, event) {
            const isChecked = event.target.checked;
            if (isChecked) {
                const existing = this.form.selected_modules.find(item => item.module_id === moduleId);
                if (existing) {
                    existing.permission = permission;
                } else {
                    this.form.selected_modules.push({ module_id: moduleId, permission });
                    this.ensureAncestorsIncluded(moduleId);
                }
            } else {
                this.form.selected_modules = this.form.selected_modules.filter(
                    item => item.module_id === moduleId || item.permission !== permission
                );
                if (!this.isModuleChecked(moduleId)) {
                    this.form.selected_modules = this.form.selected_modules.filter(
                        item => item.module_id !== moduleId
                    );
                }
                this.syncParentModules();
            }
            this.cleanUpModules();
        },
        setParentPermission(module, permission, event) {
            const isChecked = event.target.checked;
            if (isChecked) {
                if (module.children.length) {
                    module.children.forEach(child => {
                        const existing = this.form.selected_modules.find(item => item.module_id === child.id);
                        if (existing) {
                            existing.permission = permission;
                        } else {
                            this.form.selected_modules.push({ module_id: child.id, permission });
                        }
                    });
                    this.ensureModuleIncluded(module.id, 'view_only');
                    this.ensureAncestorsIncluded(module.id);
                } else {
                    this.ensureModuleIncluded(module.id, permission);
                    this.ensureAncestorsIncluded(module.id);
                }
            } else {
                if (module.children.length) {
                    module.children.forEach(child => {
                        this.form.selected_modules = this.form.selected_modules.filter(
                            item => item.module_id !== child.id || item.permission !== permission
                        );
                    });
                }
                this.form.selected_modules = this.form.selected_modules.filter(
                    item => item.module_id !== module.id || item.permission !== permission
                );
                this.syncParentModules();
            }
            this.cleanUpModules();
        },
        hasPermission(moduleId, permission) {
            return this.form.selected_modules.some(
                item => item.module_id === moduleId && item.permission === permission
            );
        },
        isModuleChecked(moduleId) {
            return this.form.selected_modules.some(item => item.module_id === moduleId);
        },
        isParentChecked(module) {
            return this.isModuleChecked(module.id) && (
                !module.children.length || module.children.every(child => this.isModuleChecked(child.id))
            );
        },
        hasParentPermission(module, permission) {
            return module.children.length && module.children.every(child =>
                this.form.selected_modules.some(
                    item => item.module_id === child.id && item.permission === permission
                )
            );
        },
        cleanUpModules() {
            this.form.selected_modules = this.form.selected_modules.filter(
                item => item.module_id && item.permission
            );
        },
        toggleSelectAllCategories(event) {
            const isChecked = event.target.checked;
            if (isChecked) {
                this.form.selected_category_ids = this.form_data.categories
                    .filter(category => category.id !== 16)
                    .map(category => category.id);
            } else {
                this.form.selected_category_ids = [];
            }
        },
        save() {
            this.crud_loading = true;
            axios({
                url: `/admin/settings/user/save/${this.obj_id}`,
                method: 'POST',
                data: this.form
            }).then(response => {
                this.successToast(response.data.message);
                this.$emit('closeModal');
            }).catch(error => {
                console.error('Error saving permissions:', error);
                this.errorToast(error.response?.data?.message || 'Failed to save permissions');
            }).finally(() => {
                this.crud_loading = false;
            });
        },
        successToast(message) {
            console.log('Success:', message); // Replace with actual toast implementation
        },
        errorToast(message) {
            console.log('Error:', message); // Replace with actual toast implementation
        },
    },
    computed: {
        areAllCategoriesSelected() {
            return this.form_data.categories.length > 0 &&
                this.form.selected_category_ids.length === this.form_data.categories.length;
        },
    },
    mounted() {
        if (this.obj_id) {
            this.getObjById();
        }
    }
}
</script>

<style scoped>
.modal {
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>
