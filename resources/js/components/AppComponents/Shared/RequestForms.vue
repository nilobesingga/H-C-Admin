<template>
    <div :class="containerClass">
        <template v-if="showTitle">
            <h2 class="mb-2 text-xl font-bold text-gray-800">{{ title }}</h2>
            <hr class="mb-8 border-gray-300" />
        </template>
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <!-- Document Request -->
            <div class="p-6 bg-white border border-gray-300 rounded-lg shadow">
                <h3 class="mb-4 text-lg font-semibold text-gray-800">Document Request</h3>
                <div class="relative p-4 mb-4 rounded-lg bg-gray-50">
                    <select v-model="documentRequest.category" class="w-full p-3 pr-8 bg-white border border-gray-300 rounded-md appearance-none focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="" disabled selected>Select Category</option>
                        <option v-for="category in documentCategories" :key="category" :value="category">
                            {{ category }}
                        </option>
                    </select>
                    <div class="absolute inset-y-0 flex items-center px-2 pointer-events-none right-4">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </div>
                </div>

                <div class="p-4 mb-4 border border-gray-300 rounded-lg">
                    <textarea v-model="documentRequest.details" class="w-full h-24 px-3 py-2 text-gray-700 border-0 resize-none focus:outline-none" placeholder="Tell us more in details."></textarea>
                    <div class="flex items-center mt-4 text-gray-500">
                        <label class="flex items-center gap-2 px-2 py-1 text-sm cursor-pointer hover:text-gray-700">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                            </svg>
                            Attach File
                            <input type="file" class="hidden" @change="handleFileAttachment('document', $event)">
                        </label>
                        <span v-if="documentRequest.file" class="ml-2 text-sm text-gray-600">
                            {{ documentRequest.file.name }}
                        </span>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button @click="sendDocumentRequest" :disabled="!documentRequest.category || !documentRequest.details" :class="[
                                'flex items-center px-4 py-2 text-sm font-medium text-white rounded',
                                (!documentRequest.category || !documentRequest.details)
                                    ? 'bg-gray-400 cursor-not-allowed'
                                    : 'bg-gray-800 hover:bg-gray-700'
                            ]">
                        Send Request
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Change Request -->
            <div class="p-6 bg-white border border-gray-300 rounded-lg shadow">
                <h3 class="mb-4 text-lg font-semibold text-gray-800">Change Request</h3>
                <div class="relative p-4 mb-4 rounded-lg bg-gray-50">
                    <select v-model="changeRequest.category" class="w-full p-3 pr-8 bg-white border border-gray-300 rounded-md appearance-none focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="" disabled selected>Select Category</option>
                        <option v-for="category in changeCategories" :key="category" :value="category">
                            {{ category }}
                        </option>
                    </select>
                    <div class="absolute inset-y-0 flex items-center px-2 pointer-events-none right-4">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </div>
                </div>

                <div class="p-4 mb-4 border border-gray-300 rounded-lg">
                    <textarea v-model="changeRequest.details" class="w-full h-24 px-3 py-2 text-gray-700 border-0 resize-none focus:outline-none" placeholder="Tell us more in details."></textarea>
                    <div class="flex items-center mt-4 text-gray-500">
                        <label class="flex items-center gap-2 px-2 py-1 text-sm cursor-pointer hover:text-gray-700">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                            </svg>
                            Attach File
                            <input type="file" class="hidden" @change="handleFileAttachment('change', $event)">
                        </label>
                        <span v-if="changeRequest.file" class="ml-2 text-sm text-gray-600">
                            {{ changeRequest.file.name }}
                        </span>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button @click="sendChangeRequest" :disabled="!changeRequest.category || !changeRequest.details" :class="[
                                'flex items-center px-4 py-2 text-sm font-medium text-white rounded',
                                (!changeRequest.category || !changeRequest.details)
                                    ? 'bg-gray-400 cursor-not-allowed'
                                    : 'bg-gray-800 hover:bg-gray-700'
                            ]">
                        Send Request
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    name: 'RequestForms',
    props: {
        // Allow customization of title and layout
        title: {
            type: String,
            default: 'New Requests'
        },
        showTitle: {
            type: Boolean,
            default: true
        },
        // Additional CSS classes for the container
        containerClass: {
            type: String,
            default: 'mt-6'
        },
        // Allow customization of category options
        customDocumentCategories: {
            type: Array,
            default: null
        },
        customChangeCategories: {
            type: Array,
            default: null
        },
        companyId: {
            type: Number
        },
        contactId: {
            type: Number
        },
    },
    data() {
        return {
            documentRequest: {
                category: '',
                details: '',
                file: null
            },
            changeRequest: {
                category: '',
                details: '',
                file: null
            },
            defaultDocumentCategories: [
                'Certified Documents',
                'Notarised Documents',
                'Apostilled Documents',
                'Legalized Documents',
                'Others',
            ],
            defaultChangeCategories: [
                "Change Owner",
                "Change Capital",
                "Change Director / Manager / Secretary",
                "Change UBO",
                "Change Registered Agent",
                "Bank Account",
                "Name Change",
                "Others",
            ]
        }
    },
    computed: {
        // Use custom categories if provided, otherwise use defaults
        documentCategories() {
            return this.customDocumentCategories || this.defaultDocumentCategories;
        },
        changeCategories() {
            return this.customChangeCategories || this.defaultChangeCategories;
        }
    },
    methods: {
        async sendDocumentRequest() {
            try {
                // Prepare form data for file upload
                const formData = new FormData();
                formData.append('category', this.documentRequest.category);
                formData.append('details', this.documentRequest.details);
                formData.append('type', 'document_request');
                formData.append('company_id', this.companyId);
                formData.append('contact_id', this.contactId);

                if (this.documentRequest.file) {
                    formData.append('file', this.documentRequest.file);
                }

                // Make API request
                const response = await axios.post('/api/request/document', formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });

                // Reset form after successful submission
                this.documentRequest = {
                    category: '',
                    details: '',
                    file: null
                };

                // Show success message (or we can emit an event for the parent to handle)
                this.$emit('request-success', 'document_request');

            } catch (error) {
                console.error('Error submitting document request:', error);
                this.$emit('request-error', {
                    type: 'document',
                    error: error.response?.data?.message || 'Failed to submit request'
                });
            }
        },

        async sendChangeRequest() {
            try {
                // Prepare form data for file upload
                const formData = new FormData();
                formData.append('category', this.changeRequest.category);
                formData.append('details', this.changeRequest.details);
                formData.append('type', 'change_request');
                formData.append('company_id', this.companyId);
                formData.append('contact_id', this.contactId);

                if (this.changeRequest.file) {
                    formData.append('file', this.changeRequest.file);
                }

                // Make API request
                const response = await axios.post('/api/request/document', formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });

                // Reset form after successful submission
                this.changeRequest = {
                    category: '',
                    details: '',
                    file: null
                };
                // Show success message (or we can emit an event for the parent to handle)
                this.$emit('request-success', 'change_request');

            } catch (error) {
                console.error('Error submitting change request:', error);
                this.$emit('request-error', {
                    type: 'change',
                    error: error.response?.data?.message || 'Failed to submit request'
                });
            }
        },

        handleFileAttachment(requestType, event) {
            const file = event.target.files[0];
            if (file) {
                if (requestType === 'document') {
                    this.documentRequest.file = file;
                } else {
                    this.changeRequest.file = file;
                }
            }
        }
    }
}
</script>
