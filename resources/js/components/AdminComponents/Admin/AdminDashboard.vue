<template>
    <div class="container-fluid">
        <!-- Grid Section -->
        <div class="mt-6">
            <div class="grid grid-cols-12 gap-4">
                <!-- First column (size 8) -->
                <div class="col-span-9">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-2xl font-semibold text-gray-800">Admin Dashboard</h2>
                        <button class="btn bg-[#D57B3F] text-white px-6 py-6 text-xl hover:bg-orange-300" @click="showTaskModal = true">Create New Task <img :src="icon"></button>
                    </div>
                    <hr class="mb-4 border-gray-500 border-1">
                    <div class="overflow-x-auto">
                        <div class="space-y-4">
                            <!-- Task/Request Cards -->
                            <div v-for="(task, index) in pendingTasks" :key="index"
                                class="relative p-4 bg-white border-l-4 rounded-lg shadow-sm"
                                :class="[getTaskBorderColor(task.type)]">

                                <!-- Left border color indicator based on task type -->

                                <!-- Task Content -->
                                <div class="flex flex-col w-full">
                                    <!-- Top Row with Avatar and Info -->
                                    <div class="flex items-center justify-between w-full">
                                        <div class="flex items-center space-x-4">
                                            <!-- Avatar Circle with Initials -->
                                            <div class="flex-shrink-0">
                                                <div class="flex items-center justify-center w-12 h-12 text-white rounded-full"
                                                    :style="{ backgroundColor: task.avatarColor }">
                                                    <span class="text-lg font-bold">{{ task.initials }}</span>
                                                </div>
                                            </div>

                                            <!-- Task Info -->
                                            <div>
                                                <div class="flex items-center mb-1 space-x-2">
                                                    <span class="font-medium text-gray-900">{{ task.name }}</span>
                                                     <template v-if="task.requestType">
                                                        <span>›</span>
                                                        <a :href="task.route_link" class="text-gray-700 hover:underline">{{ task.requestType }}</a>
                                                    </template>
                                                    <template v-if="task.company">
                                                        <span>›</span>
                                                        <span class="font-medium text-gray-700">{{ task.company }}</span>
                                                    </template>
                                                </div>
                                                <!-- Request Type and Assignee Navigation -->
                                                <!-- <div class="flex items-center space-x-1 text-sm text-gray-500">
                                                    <template v-if="task.requestType">
                                                        <a :href="task.route_link" class="text-blue-500 hover:underline">{{ task.requestType }}</a>
                                                        <span>›</span>
                                                    </template>
                                                    <a href="#" class="text-blue-500 hover:underline">{{ task.assignee }}</a>
                                                </div> -->
                                            </div>
                                        </div>

                                        <!-- Right Side - Date and Action Button -->
                                        <div class="flex flex-col items-end ml-auto space-y-3">
                                            <button v-if="task.status === 'pending'"
                                                class="px-5 py-1 font-semibold text-white transition-colors bg-blue-500 rounded btn-sm hover:bg-blue-600">
                                                Confirm
                                            </button>
                                            <span v-else-if="task.status === 'open'"
                                                class="px-5 py-1 font-semibold text-orange-400 bg-orange-100 rounded btn-sm">
                                                Pending
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Full width divider -->
                                    <hr class="my-3 ml-16 border-gray-200">

                                    <!-- Task Title -->
                                    <div class="flex items-center justify-between w-full">
                                        <h3 class="ml-16 text-lg font-semibold text-gray-800"><a href="/task" target="_blank" rel="noopener noreferrer" class="text-gray-900 hover:underline hover:text-blue-500">{{ task.title }}</a></h3>
                                        <div class="text-gray-500">{{ formatDate(task.deadline) }}</div>
                                    </div>
                                </div>
                            </div>

                            <!-- If no tasks -->
                            <div v-if="pendingTasks.length === 0" class="p-6 text-center text-gray-500 bg-white rounded-lg">
                                No pending tasks or requests
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Second column (size 4) -->
                <div class="col-span-3">
                    <div class="p-8 mb-4 transition-shadow bg-white rounded-lg shadow hover:shadow-md">
                        <h1 class="mb-2 text-xl font-semibold text-gray-700">Overview</h1>
                        <hr class="mb-8 border-gray-500 border-1">
                        <div class="grid grid-cols-2 gap-3">
                            <!-- Created Tasks Card -->
                            <a href="/task" class="flex flex-col overflow-hidden transition-shadow bg-gray-700 rounded-lg shadow hover:shadow-md">
                                <div class="flex items-center justify-center flex-1 p-4 text-4xl font-bold text-white">
                                    {{ taskStats.created || '0' }}
                                </div>
                                <div class="px-4 py-2 text-sm text-center text-white bg-gray-500">
                                    Created Tasks
                                </div>
                            </a>

                            <!-- Document Requests Card -->
                            <a href="/request" class="flex flex-col overflow-hidden transition-shadow bg-gray-700 rounded-lg shadow hover:shadow-md">
                                <div class="flex items-center justify-center flex-1 p-4 text-4xl font-bold text-white">
                                    {{ taskStats.documents || '0' }}
                                </div>
                                <div class="px-4 py-2 text-sm text-center text-white bg-gray-500">
                                    Document Requests
                                </div>
                            </a>

                            <!-- Change Requests Card -->
                            <a href="/request" class="flex flex-col overflow-hidden transition-shadow bg-gray-700 rounded-lg shadow hover:shadow-md">
                                <div class="flex items-center justify-center flex-1 p-4 text-4xl font-bold text-white">
                                    {{ taskStats.changes || '0' }}
                                </div>
                                <div class="px-4 py-2 text-sm text-center text-white bg-gray-500">
                                    Change Requests
                                </div>
                            </a>

                            <!-- Data Updates Card -->
                            <a href="/change-requests" class="flex flex-col overflow-hidden transition-shadow bg-gray-700 rounded-lg shadow hover:shadow-md">
                                <div class="flex items-center justify-center flex-1 p-4 text-4xl font-bold text-white">
                                    {{ taskStats.updates || '0' }}
                                </div>
                                <div class="px-4 py-2 text-sm text-center text-white bg-gray-500">
                                    Data Updates
                                </div>
                            </a>
                        </div>
                    </div>
                    <div class="p-8 transition-shadow bg-white rounded-lg shadow hover:shadow-md">
                        <h1 class="mb-2 text-xl font-semibold text-gray-700">Upcoming Renewals</h1>
                        <hr class="mb-4 border-gray-500 border-1">

                        <div class="space-y-4">
                            <div v-for="(renewal, index) in upcomingRenewals" :key="index" class="flex items-center p-3 transition-colors rounded-lg hover:bg-gray-50">
                                <div class="flex items-center justify-center w-12 h-12 mr-4 text-lg font-bold text-white rounded-full" :style="{ backgroundColor: renewal.color }">
                                    {{ renewal.initials }}
                                </div>
                                <div>
                                    <div class="font-medium">{{ renewal.company }}</div>
                                    <div class="text-sm text-gray-500">{{ renewal.date }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Task Modal -->
        <TaskModal :show="showTaskModal" :currentUser="page_data.user" @close="showTaskModal = false" @submit="fetchTasks" />
    </div>
</template>
<script>
import TaskModal from './Modal/TaskModal.vue';
export default {
    components: {
        TaskModal
    },
    name: 'admin-dashboard',
    props: {
        page_data: {
            type: Object,
            required: true
        },
        module: {
            type: Object,
            required: true
        }
    },
    data() {
        return {
            icon : '/icon/task.svg',
            search: '',
            loading: false,
            tasks: [],
            taskStats: {
                created: 0,
                documents: 0,
                changes: 0,
                updates: 0
            },
            currentUser: this.page_data.user.userprofile.name || 'Current User',
            showTaskModal: false,
            pendingTasks: [
                // {
                //     name: 'Jo Ann Pinuela',
                //     title: 'Upload your new utility bill - June 2025 Upload your new utility bill - June',
                //     requestType: 'Client\'s Task',
                //     company: 'CRESCO Management and Consultancies',
                //     assignee: '',
                //     status: 'pending',
                //     date: '20 Aug 2025',
                //     type: 'document',
                //     initials: 'JA',
                //     avatarColor: '#F4A261'
                // },
                // {
                //     name: 'Adam Guardacini',
                //     company: 'Silvergate Dynamics',
                //     title: 'Notarized my business license',
                //     requestType: 'Document Request',
                //     assignee: 'Jo Ann Pinuela',
                //     status: 'pending',
                //     date: '20 Aug 2025',
                //     type: 'document',
                //     initials: 'AG',
                //     avatarColor: '#7B68EE'
                // },
                // {
                //     name: 'Sean Rivers',
                //     company: 'Paramount Management Systems',
                //     title: 'Update my Bank Account Details',
                //     requestType: 'Change Request',
                //     assignee: 'Cesar Igarta',
                //     status: 'pending',
                //     date: '20 Aug 2025',
                //     type: 'change',
                //     initials: 'SR',
                //     avatarColor: '#7B68EE'
                // },
                // {
                //     name: 'Jonathan Saunderson',
                //     company: 'Evercrest Capital Partners',
                //     title: 'Another Branch in Oman',
                //     requestType: 'Set-up Company',
                //     assignee: 'Ramazan Galyamov',
                //     status: 'pending',
                //     date: '20 Aug 2025',
                //     type: 'setup',
                //     initials: 'JS',
                //     avatarColor: '#7B68EE'
                // }
            ],
            upcomingRenewals: [
                {
                    company: 'Silvergate Dynamics',
                    date: '20 Aug 2025',
                    initials: 'SD',
                    color: '#4CD9C0'
                },
                {
                    company: 'Evercrest Capital Partners',
                    date: '24 Aug 2025',
                    initials: 'EC',
                    color: '#4285F4'
                },
                {
                    company: 'Company Name',
                    date: '24 Aug 2025',
                    initials: 'DT',
                    color: '#B076F9'
                },
                {
                    company: 'Luminaris Health Group',
                    date: '07 Sep 2025',
                    initials: 'LH',
                    color: '#53C268'
                },
                {
                    company: 'Luminaris Health Group',
                    date: '07 Sep 2025',
                    initials: 'LH',
                    color: '#53C268'
                }
            ]
        };
    },
    computed: {
        filteredTasks() {
            if (!this.search) return this.tasks;
            return this.tasks.filter(task =>
                task.title.toLowerCase().includes(this.search.toLowerCase())
            );
        }
    },
    async mounted() {
        await this.fetchTasks();
        await this.fetchRequest();
    },
    created() {
        console.log('Admin Dashboard created');
    },
    methods: {
        getInitials(name) {
            if (!name) return '';
            return name.split(' ')
                .map(word => word.charAt(0).toUpperCase())
                .join('')
                .substring(0, 2);
        },
         formatDate(dateString) {
            if (!dateString) return '';
            const date = new Date(dateString);
            return date.toLocaleString('en-US', {
                month: 'short',
                day: 'numeric',
                year: 'numeric',
                // hour: 'numeric',
                // minute: '2-digit',
                // hour12: true
            });
        },
        async fetchTasks() {
            this.loading = true;
            this.tasks = [];
            await axios.get('/api/tasks/list', {
                params : {
                    status: 'open',
                    limit : 3
                }
            })
            .then(response => {
                const allTasks = Object.values(response.data);

                // Update tasks list for the table
                this.pendingTasks = allTasks.map(task => {
                    var name = (task.participants) ? task.participants.map(p => (p.userprofile) ? p.userprofile.name : 'Unknown').join(', ') : 'Unknown';
                    return {
                        ...task,
                        title: task.title,
                        created_at: this.formatDate(task.created_at),
                        name: name,
                        requestType: 'Client\'s Task',
                        company: (task.companies) ? task.companies.map(c => c.name).join(', ') : 'Unknown',
                        assignee: '',
                        status: task.status,
                        date: this.formatDate(task.date),
                        type: 'document',
                        initials: this.getInitials(name),
                        avatarColor: '#F4A261',
                        route_link : '/task?search=' + task.title
                    };
                });

                // Calculate task statistics
                this.calculateTaskStats(allTasks);

                this.loading = false;
            })
            .catch(error => {
                this.loading = false;
                console.error('Error fetching tasks:', error);
            });
        },

        async fetchRequest() {
            this.loading = true;
            await axios.get('/api/requests', {
                params : {
                    status: 'pending',
                    limit : 6
                }
            })
            .then(response => {
                const allTasks = Object.values(response.data);
                // Update tasks list for the table
                let task = allTasks.map(req => {
                    return {
                        ...req,
                        title: req.category,
                        created_at: this.formatDate(req.created_at),
                        name: req.company_name ? req.company_name : 'Unknown',
                        requestType: (req.type === 'document_request') ? 'Document Request' : 'Change Request',
                        company: (req.contact_name) ? req.contact_name : 'Unknown',
                        assignee: '',
                        status: req.status,
                        date: this.formatDate(req.date),
                        type: (req.type === 'document_request') ? 'document' : 'change',
                        initials: this.getInitials(req.contact_name),
                        avatarColor: '#7B68EE',
                        route_link : (req.type === 'document_request') ? 'document-request' : 'change-request',
                    };
                });
                this.pendingTasks= [...this.pendingTasks,...task];
                // Calculate task statistics
                this.calculateTaskStats(allTasks);

                this.loading = false;
            })
            .catch(error => {
                this.loading = false;
                console.error('Error fetching tasks:', error);
            });
        },


        getTaskBorderColor(type) {
            const colors = {
                'document': 'border-green-500',
                'change': 'border-purple-500',
                'setup': 'border-blue-500',
                'data': 'border-orange-500',
                'default': 'border-gray-500'
            };

            return colors[type] || colors.default;
        },

        calculateTaskStats(allTasks) {
            // Reset stats
            this.taskStats = {
                created: 0,
                documents: 0,
                changes: 0,
                updates: 0
            };

            // Count all tasks
            this.taskStats.created = allTasks.length;

            // Count by category
            allTasks.forEach(task => {
                const category = (task.category || '').toLowerCase();

                if (category.includes('document') || category.includes('doc')) {
                    this.taskStats.documents++;
                } else if (category.includes('change')) {
                    this.taskStats.changes++;
                } else if (category.includes('update') || category.includes('data')) {
                    this.taskStats.updates++;
                }
            });

            // Set some default values for demo if empty
            if (this.taskStats.created === 0) this.taskStats.created = 77;
            if (this.taskStats.documents === 0) this.taskStats.documents = 21;
            if (this.taskStats.changes === 0) this.taskStats.changes = 32;
            if (this.taskStats.updates === 0) this.taskStats.updates = 55;
        },
    }

};
</script>
