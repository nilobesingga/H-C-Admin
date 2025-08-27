<template>
<div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40">
    <div class="relative w-full max-w-5xl mx-4 bg-white rounded-lg shadow-lg animate-fadeIn">
        <!-- Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b">
            <h2 class="text-xl font-semibold text-gray-900">Create a new task</h2>
            <button @click="$emit('close')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <!-- Body -->
        <div class="px-6 py-4 space-y-6">
            <!-- Task Title with Priority Checkbox -->
            <div class="flex items-center gap-4">
                <div class="flex-grow">
                    <input
                        type="text"
                        v-model="task.title"
                        placeholder="Things to do *"
                        class="w-full px-3 py-2 text-sm font-semibold border border-gray-600 rounded focus:outline-none focus:ring-2 focus:ring-gray-100"
                        :class="{ 'border-red-500': !task.title && showErrors }"
                    />
                </div>
                <div class="flex items-center">
                    <input
                        type="checkbox"
                        id="highPriority"
                        v-model="task.is_priority"
                        class="w-4 h-4 text-red-600 border-gray-600 rounded focus:ring-red-500"
                    >
                    <label
                        for="highPriority"
                        class="ml-2 text-sm font-medium text-gray-700 cursor-pointer hover:text-red-600"
                    >
                        <span class="flex items-center">
                        High Priority <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 hover:text-red-600">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.362 5.214A8.252 8.252 0 0 1 12 21 8.25 8.25 0 0 1 6.038 7.047 8.287 8.287 0 0 0 9 9.601a8.983 8.983 0 0 1 3.361-6.867 8.21 8.21 0 0 0 3 2.48Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 18a3.75 3.75 0 0 0 .495-7.468 5.99 5.99 0 0 0-1.925 3.547 5.975 5.975 0 0 1-2.133-1.001A3.75 3.75 0 0 0 12 18Z" />
                        </svg>
                        </span>
                    </label>
                </div>
            </div>
            <!-- Description -->
            <textarea
                v-model="task.description"
                rows="5"
                placeholder="Tell me more about the task..."
                class="w-full px-3 py-2 text-sm border border-gray-600 rounded focus:outline-none focus:ring-2 focus:ring-gray-100"
                :class="{ 'border-red-500': !task.description && showErrors }"
            ></textarea>
            <!-- Attach File -->
            <div class="flex items-center mt-2">
                <label class="flex items-center cursor-pointer">
                    <input type="file" class="hidden" @change="handleFileUpload" multiple />
                    <span class="inline-flex items-center px-3 py-1 text-sm font-medium text-blue-700 bg-blue-100 rounded hover:bg-blue-200">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Attach Files
                    </span>
                </label>
                <div v-if="task.files && task.files.length" class="flex flex-wrap gap-2 ml-3">
                    <span v-for="(file, idx) in task.files" :key="idx" class="flex items-center max-w-xs px-2 py-1 text-xs text-gray-600 truncate bg-gray-100 rounded">
                        {{ file.name }}
                        <button @click.prevent="removeFile(idx)" class="ml-1 text-gray-400 hover:text-red-500 focus:outline-none" title="Remove">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 20 20">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 6l8 8M6 14L14 6" />
                            </svg>
                        </button>
                    </span>
                </div>
            </div>

             <div class="w-full">
                <div>
                    <label class="block mb-1 text-xs text-black">Category</label>
                   <select v-model="task.category" class="w-full px-2 py-3 border border-gray-600 rounded form-select focus:outline-none focus:ring-2 focus:ring-gray-100">
                        <option value="">Select Category</option>
                        <option value="individual">Individual</option>
                        <option value="company">Company</option>
                   </select>
                </div>
            </div>
            <!-- Company Person Multi-select -->
            <div class="form-group" v-if="task.category === 'company'">
                <div class="flex items-center justify-between mb-1">
                    <label class="block text-xs text-black">Company</label>
                </div>
                <div class="flex flex-wrap gap-2 p-2 mb-2 bg-white border border-gray-600 rounded">
                    <span v-for="company in selectedCompany" :key="company.id" class="inline-flex items-center px-3 py-1 text-sm font-semibold text-white bg-blue-500 rounded">
                        {{ company.name }}
                        <button type="button" class="ml-2 text-white hover:text-gray-200 focus:outline-none" @click="removeCompany(company)">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </span>
                    <div class="relative flex-1 min-w-[150px]">
                        <input v-model="companySearch" @focus="companyDropdownOpen = true" @input="companyDropdownOpen = true" @blur="handleDropdownBlur('company')" type="text" class="w-full px-2 py-1 text-sm border-none focus:ring-0" placeholder="Add company..." />
                        <ul v-if="companyDropdownOpen && filteredCompany.length" class="absolute left-0 right-0 z-10 mt-1 overflow-auto bg-white border rounded shadow max-h-40">
                            <li v-for="company in filteredCompany" :key="company.id" class="px-3 py-2 cursor-pointer hover:bg-blue-100" @mousedown.prevent="addCompany(company)">
                                {{ company.name }}
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- Responsible Person Multi-select -->
            <div class="grid grid-cols-2 gap-6">
                <div class="w-full form-group">
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-xs text-black">Responsible person</label>
                        <!-- <div class="flex gap-2">
                            <button type="button" class="px-2 py-1 text-xs font-semibold text-blue-700 rounded bg-blue-50 hover:bg-blue-100" @click="showParticipants = !showParticipants">
                                Participants
                            </button>
                            <button type="button" class="px-2 py-1 text-xs font-semibold text-blue-700 rounded bg-blue-50 hover:bg-blue-100" @click="showObservers = !showObservers">
                                Observers
                            </button>
                        </div> -->
                    </div>
                    <div class="flex flex-wrap gap-2 p-2 mb-2 bg-white border border-gray-600 rounded">
                        <span v-for="user in selectedResponsible" :key="user.id" class="inline-flex items-center px-3 py-1 text-sm font-semibold text-white bg-blue-500 rounded">
                            {{ user.name }}
                            <button type="button" class="ml-2 text-white hover:text-gray-200 focus:outline-none" @click="removeResponsible(user)">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </span>
                        <div class="relative flex-1 min-w-[150px]">
                            <input v-model="responsibleSearch" @focus="responsibleDropdownOpen = true" @input="responsibleDropdownOpen = true" @blur="handleDropdownBlur('responsible')" type="text" class="w-full px-2 py-1 text-sm border-none focus:ring-0" placeholder="Add responsible person..." />
                            <ul v-if="responsibleDropdownOpen && filteredResponsible.length" class="absolute left-0 right-0 z-10 mt-1 overflow-auto bg-white border rounded shadow max-h-40">
                                <li v-for="user in filteredResponsible" :key="user.id" class="px-3 py-2 cursor-pointer hover:bg-blue-100" @mousedown.prevent="addResponsible(user)">
                                    {{ user.name }}
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            <!-- Participants Multi-select -->
                <div class="w-full form-group">
                    <label class="block mb-1 text-xs text-black">Participants</label>
                    <div class="flex flex-wrap gap-2 p-2 mb-2 bg-white border border-gray-600 rounded">
                        <span v-for="user in selectedParticipants" :key="user.id" class="inline-flex items-center px-3 py-1 text-sm font-semibold text-white bg-blue-500 rounded">
                            {{ user.name }}
                            <button type="button" class="ml-2 text-white hover:text-gray-200 focus:outline-none" @click="removeParticipant(user)">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </span>
                        <div class="relative flex-1 min-w-[150px]">
                            <input v-model="participantSearch" @focus="participantDropdownOpen = true" @input="participantDropdownOpen = true" @blur="handleDropdownBlur('participant')" type="text" class="w-full px-2 py-1 text-sm border-none focus:ring-0" placeholder="Add participant..." />
                            <ul v-if="participantDropdownOpen && filteredParticipants.length" class="absolute left-0 right-0 z-10 mt-1 overflow-auto bg-white border rounded shadow max-h-40">
                                <li v-for="user in filteredParticipants" :key="user.id" class="px-3 py-2 cursor-pointer hover:bg-blue-100" @mousedown.prevent="addParticipant(user)">
                                    {{ user.name }}
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Observers Multi-select -->
            <div class="grid grid-cols-2 gap-6">
                <div class="w-full form-group">
                        <label class="block mb-1 text-xs text-black">H&C Observers</label>
                        <div class="flex flex-wrap gap-2 p-2 mb-2 bg-white border border-gray-600 rounded">
                            <span v-for="user in selectedObservers" :key="user.id" class="inline-flex items-center px-3 py-1 text-sm font-semibold text-white bg-blue-500 rounded">
                                {{ user.name }}
                                <button type="button" class="ml-2 text-white hover:text-gray-200 focus:outline-none" @click="removeObserver(user)">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </span>
                            <div class="relative flex-1 min-w-[150px]">
                                <input v-model="observerSearch" @focus="observerDropdownOpen = true" @input="observerDropdownOpen = true" @blur="handleDropdownBlur('observer')" type="text" class="w-full px-2 py-1 text-sm border-none focus:ring-0" placeholder="Add observer..." />
                                <ul v-if="observerDropdownOpen && filteredObservers.length" class="absolute left-0 right-0 z-10 mt-1 overflow-auto bg-white border rounded shadow max-h-40">
                                    <li v-for="user in filteredObservers" :key="user.id" class="px-3 py-2 cursor-pointer hover:bg-blue-100" @mousedown.prevent="addObserver(user)">
                                        {{ user.name }}
                                    </li>
                                </ul>
                            </div>
                        </div>
                </div>

                <!-- Deadline and Planning -->
                <div class="w-full">
                    <div>
                        <label class="block mb-1 text-xs text-black">Due Date *</label>
                        <input
                            type="date"
                            v-model="task.deadline"
                            class="w-full px-2 py-1 border border-gray-600 rounded form-input focus:outline-none focus:ring-2 focus:ring-gray-100"
                            :class="{ 'border-red-500': !task.deadline && showErrors }"
                        />
                    </div>
                </div>
            </div>

        </div>
        <!-- Footer -->
        <div class="flex items-center justify-end gap-3 px-6 py-4 border-t">
            <button @click="$emit('close')" class="px-5 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded hover:bg-gray-200">Cancel</button>
            <button @click="submitTask" class="px-5 py-2 text-sm font-medium text-white bg-[#D57B3F] rounded hover:bg-orange-300">Create Task</button>
        </div>
    </div>
</div>
</template>

<script>
export default {
    name: 'TaskModal',
    props: {
        show: {
            type: Boolean,
            default: false
        },
        currentUser: {
            type: Object,
            default: () => ({})
        }
    },
    data() {
        return {
            showErrors: false,
            filters: {
                search: null,
            },
            task: {
                category: '',
                title: '',
                description: '',
                is_priority: false,
                deadline: null,
                timePlanning: false,
                timeTracking: false,
                repeat: false,
                files: [],
                participants: [],
                observers: [],
                companies: [],
            },
            showParticipants: false,
            showObservers: false,
            selectedParticipant: '',
            selectedObserver: '',
            users: [],
            company: [],
            selectedParticipants: [],
            selectedObservers: [],
            selectedResponsible: [],
            selectedCompany: [],
            participantToAdd: '',
            observerToAdd: '',
            companyToAdd: '',
            participantSearch: '',
            observerSearch: '',
            responsibleSearch: '',
            companySearch: '',
            participantDropdownOpen: false,
            observerDropdownOpen: false,
            responsibleDropdownOpen: false,
            companyDropdownOpen: false,
        };
    },
    computed: {
        filteredParticipants() {
            const search = this.participantSearch.toLowerCase();
            return this.users.filter(u =>
                !this.selectedParticipants.some(s => s.id === u.id) &&
                u.name.toLowerCase().includes(search)
            );
        },
        filteredObservers() {
            const search = this.observerSearch.toLowerCase();
            return this.users.filter(u =>
                !this.selectedObservers.some(s => s.id === u.id) &&
                u.name.toLowerCase().includes(search)
            );
        },
        filteredResponsible() {
            const search = this.responsibleSearch.toLowerCase();
            return this.users.filter(u =>
                !this.selectedResponsible.some(s => s.id === u.id) &&
                u.name.toLowerCase().includes(search)
            );
        },
        filteredCompany() {
            const search = this.companySearch.toLowerCase();
            return this.company.filter(u =>
                !this.selectedCompany.some(s => s.company_id === u.company_id) &&
                u.name.toLowerCase().includes(search)
            );
        },
        availableParticipants() {
            return this.users.filter(u => !this.selectedParticipants.some(s => s.id === u.id));
        },
        availableObservers() {
            return this.users.filter(u => !this.selectedObservers.some(s => s.id === u.id));
        },
    },
    async mounted() {
        await this.usersData();
        await this.companyData();
        let selectedUser = {
            id: this.currentUser.id,
            name: this.currentUser.userprofile ?
                this.currentUser.userprofile.name || this.currentUser.email : this.currentUser.email
        };
        this.selectedResponsible = [selectedUser];
    },
    methods: {
        handleDropdownBlur(type) {
            setTimeout(() => {
                switch(type) {
                    case 'company':
                        this.companyDropdownOpen = false;
                        break;
                    case 'responsible':
                        this.responsibleDropdownOpen = false;
                        break;
                    case 'participant':
                        this.participantDropdownOpen = false;
                        break;
                    case 'observer':
                        this.observerDropdownOpen = false;
                        break;
                }
            }, 150);
        },
        async usersData() {
            try {
                const response = await axios.get('/api/users/list', {
                    data: {
                        filters: this.filters,
                    }
                });
                this.users = Object.values(response.data).map(user => ({
                    id: user.id,
                    name: (user.userprofile) ? user.userprofile.name || user.user_name : user.email,
                }));
            } catch (error) {
                console.error('Error fetching users:', error);
                this.$toast.error('Failed to load users');
            }
        },
        async companyData() {
            try {
                const response = await axios.get('/api/company-list', {
                    data: {
                        filters: this.filters,
                    }
                });
                this.company = Object.values(response.data).map(company => ({
                    company_id: company.company_id,
                    name: company.name,
                }));
            } catch (error) {
                console.error('Error fetching company:', error);
                this.$toast.error('Failed to load company');
            }
        },
        handleFileUpload(e) {
            const files = Array.from(e.target.files);
            if (files.length) {
                this.task.files = files;
            }
        },
        removeFile(idx) {
            this.task.files.splice(idx, 1);
        },
        async submitTask() {
            try {
                this.showErrors = true;

                // Validate required fields
                const errors = [];
                if (!this.task.title) {
                    errors.push('Title is required');
                }
                if (!this.task.description) {
                    errors.push('Description is required');
                }
                if (!this.task.deadline) {
                    errors.push('Deadline is required');
                }

                if (!this.task.category) {
                    errors.push('Category is required');
                }

                if (errors.length > 0) {
                    errors.forEach(error => this.errorToast(error));
                    return;
                }

                const formData = new FormData();
                formData.append('title', this.task.title);
                formData.append('description', this.task.description);
                formData.append('deadline', this.task.deadline);
                formData.append('is_priority', this.task.is_priority);
                formData.append('category', this.task.category);

                // Append company if any
                 this.selectedCompany.forEach((company, index) => {
                    formData.append(`companies[${index}][id]`, company.company_id);
                });

                // Append files if any
                if (this.task.files.length > 0) {
                    this.task.files.forEach(file => {
                        formData.append('files[]', file);
                    });
                }

                // Add responsible contacts
                this.selectedResponsible.forEach((user, index) => {
                    formData.append(`responsible[${index}][id]`, user.id);
                });

                // Add participants if any
                if (this.selectedParticipants.length > 0) {
                    this.selectedParticipants.forEach((user, index) => {
                        formData.append(`participants[${index}][id]`, user.id);
                    });
                }

                // Add observers if any
                if (this.selectedObservers.length > 0) {
                    this.selectedObservers.forEach((user, index) => {
                        formData.append(`observers[${index}][id]`, user.id);
                    });
                }

                const response = await axios.post('/api/tasks', formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });

                // Log the broadcast event details
                console.log('Event being broadcast:', {
                    channel: 'tasks',
                    event: '.task.created',
                    data: response.data.task
                });

                console.log('Setting up WebSocket listeners...');

                // Listen for new tasks
                const channel = window.Echo.private('tasks');

                // Debug channel subscription
                channel.listen('.subscription_succeeded', () => {
                    console.log('Successfully subscribed to tasks channel');
                });

                channel.listen('.subscription_error', (error) => {
                    console.error('Failed to subscribe to tasks channel:', error);
                });

                // Listen for task events
                channel.listen('.task.created', (e) => {
                    console.log('New task event received:', e);
                    // Add the new task to your tasks array
                    const newTask = {
                        id: e.id,
                        title: e.title,
                        description: e.description,
                        status: e.status,
                        created_at: e.created_at,
                        showForm: false,
                        statusType: 'normal',
                        showCommentsSection: false,
                        newComment: '',
                        statusComment: '',
                        updatedStatus: '',
                        comments: []
                    };
                    console.log('Adding new task to list:', newTask);
                    this.tasks.unshift(newTask);
                })
                .listen('error', (error) => {
                    console.error('Error in tasks channel:', error);
                });

                this.successToast('Task created successfully');
                this.task = {
                    category: '',
                    title: '',
                    description: '',
                    is_priority: false,
                    deadline: '',
                    files: [],
                    participants: [],
                    observers: [],
                    companies: [],
                };
                this.selectedParticipants = [];
                this.selectedObservers = [];
                this.selectedResponsible = [];
                this.$emit('submit', response.data.task);
                this.$emit('close');
            } catch (error) {
                console.error('Error creating task:', error);
                if (error.response?.data?.errors) {
                    // Handle validation errors from the server
                    Object.values(error.response.data.errors).forEach(errorMessages => {
                        errorMessages.forEach(message => this.$toast.error(message));
                    });
                } else {
                    this.errorToast(error.response?.data?.message || 'Error creating task');
                }
            }
        },
        getUserName(id) {
            const user = this.users.find(u => u.id === id);
            return user ? user.name : id;
        },
        addParticipant(user) {
            if (!this.selectedParticipants.some(s => s.id === user.id)) {
                this.selectedParticipants.push(user);
            }
            this.participantSearch = '';
            this.participantDropdownOpen = false;
        },
        removeParticipant(user) {
            this.selectedParticipants = this.selectedParticipants.filter(s => s.id !== user.id);
        },
        addObserver(user) {
            if (!this.selectedObservers.some(s => s.id === user.id)) {
                this.selectedObservers.push(user);
            }
            this.observerSearch = '';
            this.observerDropdownOpen = false;
        },
        removeObserver(user) {
            this.selectedObservers = this.selectedObservers.filter(s => s.id !== user.id);
        },
        addResponsible(user) {
            if (!this.selectedResponsible.some(s => s.id === user.id)) {
                this.selectedResponsible.push(user);
            }
            this.responsibleSearch = '';
            this.responsibleDropdownOpen = false;
        },
        removeResponsible(user) {
            this.selectedResponsible = this.selectedResponsible.filter(s => s.id !== user.id);
        },

        addCompany(company) {
            if (!this.selectedCompany.some(s => s.company_id === company.company_id)) {
                this.selectedCompany.push(company);
            }
            this.companySearch = '';
            this.companyDropdownOpen = false;
        },
        removeCompany(company) {
            this.selectedCompany = this.selectedCompany.filter(s => s.company_id !== company.company_id);
        },
    },
    watch: {
        selectedParticipant(val) {
            if (val && !this.task.participants.includes(val)) {
                this.task.participants.push(val);
                this.selectedParticipant = '';
            }
        },
        selectedObserver(val) {
            if (val && !this.task.observers.includes(val)) {
                this.task.observers.push(val);
                this.selectedObserver = '';
            }
        }
    }
}
</script>

<style scoped>
input[type="text"]:focus {
    animation: searchPulse 1.5s infinite;
}

.form-field {
    position: relative;
    margin-bottom: 8px;
}

.form-label {
    display: block;
    font-size: 0.75rem;
    color: #6b7280;
    margin-bottom: 4px;
    font-weight: 500;
}

.form-input {
    width: 100%;
    padding: 8px 8px 8px 8px;
    font-size: 0.875rem;
    font-weight: 500;
    color: #1f2937;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    background-color: #f9fafb;
    transition: all 0.2s ease;
}

.form-input:hover {
    border-color: #9ca3af;
}

.form-input:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05), 0 0 0 1px rgba(59, 130, 246, 0.5);
    background-color: #fff;
}
</style>
