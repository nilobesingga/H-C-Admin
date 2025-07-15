<template>
    <div class="px-3 container-fluid">
        <!-- Activity Stream Section -->
        <div class="p-6 mt-6 rounded-lg shadow bg-gray-50">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold text-gray-800">Activity Stream</h2>
                <input type="text" v-model="search" placeholder="Filter and search" class="w-64 px-3 py-1 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-200" />
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white rounded-lg">
                    <thead>
                        <tr class="border-b">
                            <th class="w-10 px-4 py-2"></th>
                            <th class="px-4 py-2 text-left text-gray-600">You have {{ filteredTasks.length }} pending tasks</th>
                            <th class="px-4 py-2 text-gray-600">Created</th>
                            <th class="px-4 py-2 text-gray-600">Deadline</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="task in filteredTasks" :key="task.id" class="hover:bg-gray-100">
                            <td class="px-4 py-2"><input type="checkbox" /></td>
                            <td class="px-4 py-2">
                                <a :href="task.link" class="text-blue-700 hover:underline">{{ task.title }}</a>
                            </td>
                            <td class="px-4 py-2 text-gray-500">{{ task.created }}</td>
                            <td class="px-4 py-2 text-gray-500">{{ task.deadline }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>
<script>
export default {
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
            search: '',
            tasks: [
                {
                    id: 1,
                    title: 'BI_ Tagging / Quick Notes in contact / Company',
                    link: '#',
                    created: 'May 13 2025',
                    deadline: ''
                },
                {
                    id: 2,
                    title: 'Posted to Sage: — X_25-005 Sample DEAL for Nilo - 0225-0225, 100% at time of signing (one time)',
                    link: '#',
                    created: 'May 22 2025',
                    deadline: ''
                },
                {
                    id: 3,
                    title: 'OT_ adjust admin page in CLP portal',
                    link: '#',
                    created: 'May 23 2025',
                    deadline: ''
                },
                {
                    id: 4,
                    title: 'Proforma Invoice Add Sending of invoice Reminder',
                    link: '#',
                    created: 'Jun 16 2025',
                    deadline: ''
                },
                {
                    id: 5,
                    title: 'BI_ Upgrading Cetrix to latest version',
                    link: '#',
                    created: 'Jun 18 2025',
                    deadline: ''
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
    mounted() {
        console.log('Admin Dashboard mounted');
        console.log('Page Data:', this.page_data);
    },
    created() {
        console.log('Admin Dashboard created');
    },
    methods: {
    }

};
</script>
