<template>
     <div class="px-6 py-6 container-fluid">
        <div class="grid gap-2">
            <div class="flex items-center justify-between gap-2">
                <div class="flex">
                    <select class="select select-sm select-input w-96" v-model="filters.sage_company_code">
                        <option value="" selected>Filter by Sage Company</option>
                        <option v-for="obj in page_data.bitrix_list_categories" :key="obj.bitrix_category_id" :value="obj.bitrix_category_id">
                            {{ obj.bitrix_category_name }}
                        </option>
                    </select>
                </div>
                <div class="flex grow">
                    <div class="relative w-full">
                        <i class="absolute leading-none text-black transform -translate-y-1/2 ki-outline ki-magnifier text-md top-1/2 left-3"></i>
                        <input
                            class="input input-sm text-input !ps-8"
                            placeholder="Search"
                            type="text"
                            v-model="filters.search"
                        />
                    </div>
                </div>

                <!-- Sort Controls -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <button data-modal-toggle="#file-manger" class="btn secondary-btn relative px-3 h-10  !rounded-none transition-all duration-300 hover:border-black btn-light text-black">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m3.75 9v6m3-3H9m1.5-12H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                            </svg>
                            New File
                        </button>
                    </div>
                </div>
            </div>
        <div class="relative flex-grow overflow-auto">
            <table class="table table-striped table-bordered table-hover">
                <thead>
                    <tr>
                        <th class="text-center">File Name</th>
                        <th class="text-center">File Path</th>
                        <th class="text-center">Created At</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(file, index) in files" :key="index">
                        <td class="text-center">{{ file.name }}</td>
                        <td class="text-center">{{ file.path }}</td>
                        <td class="text-center">{{ file.created_at }}</td>
                        <td class="text-center">
                            <!-- Add your action buttons here -->
                            <button @click="editFile(file)" class="btn btn-sm btn-primary">Edit</button>
                            <button @click="deleteFile(file)" class="btn btn-sm btn-danger">Delete</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="modal" data-modal="true" data-modal-backdrop-static="true" id="file-manger">
        <div class="modal-content top-[5%] lg:max-w-[1000px]">
            <div class="modal-header">
                <h3 class="text-xl font-bold tracking-tight capitalize modal-title">File Manager</h3>
                <button class="btn btn-xs btn-icon btn-light" data-modal-dismiss="true" @click="$emit('closeModal')">
                    <i class="ki-outline ki-cross" ></i>
                </button>
            </div>
            <div class="relative h-full overflow-auto modal-body">
                <!-- Loading Spinner -->
                <div v-if="loading" class="absolute inset-0 z-50 flex items-center justify-center h-40">
                    <div class="flex items-center gap-2 px-4 py-2 text-sm font-medium leading-none text-gray-500 bg-white border border-gray-200 rounded-md shadow-default">
                        <svg class="w-5 h-5 -ml-1 text-gray-600 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Loading...
                    </div>
                </div>
                <!-- Modal Content -->
                <div v-else>
                    <form @submit.prevent="submit">
                        <div class="grid grid-cols-6 gap-4">
                                <div class="col-start-1 col-end-7">
                                    <label class="flex items-center gap-1 mb-1 text-sm form-label" for="company">Company
                                        <span class="text-danger">* <span class="form-text-error" v-if="v$.form.company.$error">Please fill out this field</span></span>
                                    </label>
                                    <select class="select select-sm select-input" v-model="filters.company">
                                        <option value="" selected>Filter by Sage Company</option>
                                        <option v-for="obj in page_data.bitrix_list_categories" :key="obj.bitrix_category_id" :value="obj.bitrix_category_id">
                                            {{ obj.bitrix_category_name }}
                                        </option>
                                    </select>
                                </div>
                                <!-- Project -->
                                <div class="col-start-1 col-end-7">
                                    <label class="flex items-center gap-1 mb-1 text-sm form-label" for="project">Link Project
                                        <span class="text-danger">* <span class="form-text-error" v-if="v$.form.project.$error">Please fill out this field</span></span>
                                    </label>
                                    <v-select
                                        v-model="form.project"
                                        :options="form_data.projects"
                                        label="TITLE"
                                        :multiple="true"
                                        :filterable="false"
                                        :clearable="false"
                                        placeholder="Search Link Project"
                                        class="text-black"
                                        :class="{'has-error': v$.form.project.$error}"
                                        @search="debouncedSearch('projects', $event)"
                                        id="project"
                                    >
                                        <template #no-options>
                                            <div class="text-black">Search by project name</div>
                                        </template>
                                        <template #option="option">
                                            <div class="py-2 text-xs text-black">
                                                <span v-if="option.TYPE === '1'" class="px-1 bg-[#1759cd] text-white">L</span>
                                                <span v-if="option.TYPE === '2'" class="px-1 bg-[#149ac0] text-white">D</span>
                                                <span v-if="option.TYPE === '3'" class="px-1">C</span>
                                                <span class="ml-1">{{ option.TITLE }}</span>
                                            </div>
                                        </template>
                                        <template #selected-option="option">
                                            <div class="text-xs text-black" v-if="form.project">
                                                <span v-if="option.TYPE === '1'" class="px-1 bg-[#1759cd] text-white">L</span>
                                                <span v-if="option.TYPE === '2'" class="px-1 bg-[#149ac0] text-white">D</span>
                                                <span v-if="option.TYPE === '3'" class="px-1">C</span>
                                                <span class="ml-1"> {{ option.TITLE }}</span>
                                            </div>
                                        </template>
                                    </v-select>
                                </div>
                                <!-- File Title -->
                                <div class="col-start-1 col-end-7">
                                    <label class="flex items-center gap-1 mb-1 text-sm form-label" for="title">Title
                                        <span class="text-danger">* <span class="form-text-error" v-if="v$.form.title.$error">Please fill out this field</span></span>
                                    </label>
                                    <input class="text-black input bg-inherit" :class="v$.form.title.$error ? '!border-red-500' : ''" placeholder="Title" id="title" type="text" v-model="form.title">
                                </div>
                                <div class="col-start-1 col-end-7">
                                    <label class="flex items-center gap-1 mb-1 text-sm font-medium text-gray-700" for="supporting_document">
                                        Attach Document
                                    <span class="text-red-500">
                                        *
                                        <span v-if="v$.form.supporting_document.$error" class="text-xs italic text-red-500">
                                        Please upload a file
                                        </span>
                                    </span>
                                    </label>
                                    <div id="supporting_document_dropzone" class="flex flex-col gap-3 p-6 mt-4 transition-all duration-200 border-2 border-dashed rounded-lg dropzone dz-preview-wrapper"
                                        :class="{
                                            'border-red-500': v$.form.supporting_document.$error,
                                            'border-gray-300 hover:border-gray-900': !v$.form.supporting_document.$error,
                                            'bg-gray-50': true,
                                        }">
                                    <div class="dz-message" :class="{ 'hidden': form.supporting_document  }">
                                        <p class="text-gray-500">Drag & drop your file here or click to upload</p>
                                        <p class="text-xs text-gray-400">Supported formats: PDF, JPG, PNG</p>
                                    </div>
                                    </div>
                                </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="justify-end modal-footer">
                <div class="flex gap-4">
                    <button class="secondary-btn !text-md font-semibold !border-2 !px-10" data-modal-dismiss="true" @click="$emit('closeModal')">
                        Cancel
                    </button>
                    <button
                        class="main-btn"
                        type="submit"
                        @click="submit"
                        :disabled="loading || crud_loading"
                    >
                        <svg v-if="crud_loading" class="w-5 h-5 text-white animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span v-else>Upload</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
</template>

<script>
import axios from 'axios';
import {DateTime} from "luxon";
import { useVuelidate } from '@vuelidate/core';
import { required } from '@vuelidate/validators';
import vSelect from 'vue-select';
import 'vue-select/dist/vue-select.css';
import { debounce } from 'lodash';
import Dropzone from 'dropzone';
const bitrixUserId = "";
const bitrixWebhookToken = "";
import qs from 'qs';
export default {
    props: ['page_data'],
    name: 'FileManager',
    components: {
        vSelect,
    },
    data() {
        return {
            loading: false,
            filters: {
                sage_company_code: "",
                search: "",
            },
            companies: [],
            form: {
                company: null,
                project: [],
                title: null,
                supporting_document: [],
            },
            form_data: {
                banks: [],
                projects: [],
            },
            currencies: [],
            crud_loading: false,
            debouncedSearch: null,
            crud_loading: false,
            loading: false,
            bitrix_obj: {},
            dropzone: null,
        }
    },
    setup() {
        const v$ = useVuelidate();
        return { v$ };
    },
    validations () {
        return {
            form: {
                company: { required },
                project: { required },
                title: { required },
                supporting_document: { required }
            },
        }
    },
    computed: {
    },
    watch: {
    },
    async mounted() {
        this.form = [];
        this.debouncedSearch = debounce((type, query) => {
            this.searchData(type, query);
        }, 500);
        this.$nextTick(() => {
            this.dropzone = new Dropzone('#supporting_document_dropzone', {
                url: '/api/upload-supporting-document', // Replace with your upload endpoint
                autoProcessQueue: false, // Handle uploads manually
                maxFiles: 3, // Limit to one file
                acceptedFiles: '.pdf,.jpg,.png', // Supported formats
                addRemoveLinks: false, // Allow file removal
                dictDefaultMessage: '', // Custom message in template
                dictRemoveFile: 'Remove',
                previewTemplate: `
                    <div class="flex items-start p-3 space-x-4 bg-white border rounded-md shadow-sm dz-preview dz-file-preview">
                        <!-- Icon -->
                        <div class="flex-shrink-0">
                        <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 21h10a2 2 0 002-2V7a2 2 0 00-2-2h-5l-5 5v11a2 2 0 002 2z"/>
                        </svg>
                        </div>

                        <!-- File Info -->
                        <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-800 dz-filename"><span data-dz-name></span></p>
                        <p class="mt-1 text-xs text-red-500 dz-error-message" data-dz-errormessage></p>
                        </div>

                        <!-- Remove -->
                        <button data-dz-remove type="button"
                                class="text-xs text-red-500 hover:underline focus:outline-none">
                        Remove
                        </button>
                    </div>

                `,
                init: function () {
                // Store Vue component instance
                const vueComponent = this.options.vueComponent;

                // Handle file addition
                this.on('addedfile', (file) => {
                    vueComponent.form.supporting_document = file;
                    vueComponent.v$.form.supporting_document.$touch();
                    vueComponent.uploadDocument({ target: { files: [file] } }, 'supporting_document');
                });

                // Handle file removal
                this.on('removedfile', () => {
                    vueComponent.form.supporting_document = null;
                    vueComponent.v$.form.supporting_document.$touch();
                });

                // Handle dragover styling
                this.on('dragenter', () => {
                    document.querySelector('#supporting_document_dropzone').classList.add('border-blue-500', 'bg-blue-50');
                });

                this.on('dragleave', () => {
                    document.querySelector('#supporting_document_dropzone').classList.remove('border-blue-500', 'bg-blue-50');
                });
                },
                // Pass Vue component to init function
                vueComponent: this,
            });
        });
    },
    beforeDestroy() {
    },
    beforeUnmount() {
        // Clean up Dropzone instance
        if (this.dropzone) {
        this.dropzone.destroy();
        }
    },
    methods: {
        uploadDocument(event, field) {
            // Existing uploadDocument logic
            const file = event.target.files[0];
            if (file) {
                this.form[field] = file;
                this.v$.form[field].$touch();
                // Add your file upload logic here, e.g., send to server
                console.log('Uploading file:', file);
                // Example: Upload to server
                /*
                const formData = new FormData();
                formData.append('file', file);
                axios.post('/upload', formData).then(response => {
                console.log('File uploaded:', response.data);
                });
                */
            }
        },
        async searchData(type, query) {
            if (query) {
                const bitrixUserId = this.sharedState.bitrix_user_id ? this.sharedState.bitrix_user_id : this.page_data.user.bitrix_user_id;
                const bitrixWebhookToken = this.sharedState.bitrix_webhook_token ? this.sharedState.bitrix_webhook_token : this.page_data.user.bitrix_webhook_token;
                let endpoint, requestData;

                if (type === "projects") {
                    endpoint = "crm.contact.search";
                    requestData = { search: `%${query}%` };
                }

                try {
                    const response = await this.callBitrixAPI(endpoint, bitrixUserId, bitrixWebhookToken, qs.stringify(requestData));
                    if (response.result) {
                        console.log(response.result);
                        if (type === "projects"){
                            this.form_data.projects = [];
                            // this.form_data.projects = response.result.filter(obj => {
                            //     return obj.TYPE !== "3"
                            // });
                            this.form_data.projects = response.result
                        }
                    }
                } catch (error) {
                    console.error(`Error fetching ${type}:`, error);
                }
            }
        },
        async getProjectById(projectId){
            if (projectId){
                const bitrixUserId = this.sharedState.bitrix_user_id;
                const bitrixWebhookToken = this.sharedState.bitrix_webhook_token;
                const endpoint = "crm.contact.search";
                const requestData = {
                    id: `%${projectId.split("_")[1]}%`,
                };
                try {
                    const response = await this.callBitrixAPI(endpoint, bitrixUserId, bitrixWebhookToken, qs.stringify(requestData));
                    if (response.result) {
                        this.form.project = response.result[0]
                    }
                } catch (error) {
                    console.error("Error fetching project:", error);
                }
            }
        },
    },
    created(){
        this.debouncedSearch = debounce((type, query) => {
            this.searchData(type, query);
        }, 500);
    }
}
</script>
<style>
/* Ensure Dropzone styles work with Tailwind */
.dropzone {
  min-height: 120px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
}
.dropzone .dz-message {
  margin: 0;
}
.dropzone .dz-remove {
  color: #ef4444;
  font-size: 12px;
  text-decoration: none;
}
</style>
