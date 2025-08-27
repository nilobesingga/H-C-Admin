<template>
    <div class="w-full min-h-screen p-6">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-3xl font-extrabold text-gray-900">Change Request</h1>
            <div class="flex space-x-3">
                <button class="btn bg-[#D57B3F] text-white px-4 py-6 text-lg" @click="showTaskModal = true">Create New Task <img :src="icon"></button>
            </div>
        </div>

        <!-- Tabs as Grouped Button and Search/Filter -->
        <div class="mb-6">
            <div class="flex flex-col md:flex-row md:justify-between md:items-center">
                <!-- Tabs as Grouped Button -->
                <div class="mb-4 md:mb-0">
                    <div class="inline-flex overflow-hidden bg-gray-100 border border-gray-300 rounded-lg shadow-sm" role="group">
                        <button
                            v-for="(tab, index) in tabs"
                            :key="index"
                            @click="activeTab = tab.value"
                            :class="[
                                'px-4 py-2 text-sm font-medium transition-colors focus:outline-none',
                                activeTab === tab.value
                                    ? 'bg-white text-gray-800 shadow'
                                    : 'bg-[#EAECF3] text-gray-700 hover:bg-gray-200',
                            ]"
                        >
                            {{ tab.label }}
                        </button>
                    </div>
                </div>

                <!-- Search and Filter -->
                <div class="flex flex-col md:flex-row md:items-center md:space-x-4">
                    <!-- Search -->
                    <div class="relative flex-shrink-0 w-full mb-4 md:w-64 md:mb-0">
                        <input
                            type="text"
                            v-model="searchQuery"
                            placeholder="Search..."
                            class="w-full py-2 pl-10 pr-4 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        />
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </div>

                <!-- Filter Options -->
                <div class="flex flex-wrap flex-grow gap-3">
                    <div class="relative">
                        <select
                            v-model="categoryFilter"
                            class="py-2 pl-3 pr-10 border border-gray-300 rounded-md appearance-none focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                            <option value="">All Categories</option>
                            <option v-for="category in categories" :key="category" :value="category">{{ category }}</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="relative">
                        <select
                            v-model="dateFilter"
                            class="py-2 pl-3 pr-10 border border-gray-300 rounded-md appearance-none focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                            <option value="all">All Dates</option>
                            <option value="today">Today</option>
                            <option value="week">Last 7 Days</option>
                            <option value="month">Last 30 Days</option>
                            <option value="quarter">Last 90 Days</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </div>
                </div>
                </div>
            </div>
        </div>

        <!-- Requests Table -->
        <div class="w-full overflow-hidden bg-white border border-gray-300 rounded-lg shadow">
            <!-- Table Header -->
            <div class="grid grid-cols-12 px-6 py-4 text-sm font-medium text-gray-600 border-b border-gray-200 bg-gray-50">
                <div class="col-span-2">Company</div>
                <div class="col-span-2">Category</div>
                <div class="col-span-2">Description</div>
                <div class="col-span-2">
                    <div class="flex items-center gap-1 cursor-pointer" @click="toggleSort('date')">
                        Created Date
                        <svg :class="['w-4 h-4', sortField === 'date' ? 'text-gray-800' : 'text-gray-400']" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path v-if="sortOrder === 'asc' && sortField === 'date'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                            <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </div>
                </div>
                <div class="col-span-2">
                    <div class="flex items-center gap-1 cursor-pointer" @click="toggleSort('modified')">
                        Last Modified
                        <svg :class="['w-4 h-4', sortField === 'modified' ? 'text-gray-800' : 'text-gray-400']" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path v-if="sortOrder === 'asc' && sortField === 'modified'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                            <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </div>
                </div>
                <div class="col-span-2 text-center" v-if="activeTab !== 'approved'">Action</div>
            </div>

            <!-- Table Body -->
            <div v-if="isLoading" class="py-12">
                <div class="flex items-center justify-center">
                    <div class="flex items-center gap-2 px-4 py-2 text-sm font-medium leading-none text-brand-active">
                        <svg class="w-5 h-5 -ml-1 animate-spin text-brand-active" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Loading...
                    </div>
                </div>
            </div>
            <div v-else-if="filteredRequests.length > 0">
                <div
                    v-for="(req, index) in paginatedRequests"
                    :key="index"
                    class="grid grid-cols-12 px-6 py-4 transition-colors duration-150 border-b border-gray-200 hover:bg-gray-50" @click="openRequestDetailedModal(req.id)"
                >
                    <!-- <div class="flex items-center col-span-2 font-medium">
                        <div v-if="req.contact_photo == null" class="flex-shrink-0">
                            <img
                                :src="req.contact_photo"
                                alt="Contact Photo"
                                class="w-8 h-8 mr-2 rounded-full"
                            />
                        </div>
                        <div v-else class="flex items-center justify-center flex-shrink-0 w-8 h-8 mr-2 text-sm font-medium text-white bg-blue-500 rounded-full">
                            {{ getInitials(req.contact_name) }}
                        </div>
                        {{ req.contact_name }}
                    </div>
                    <div class="col-span-2 font-medium">
                        <span class="font-semibold">{{ req.request_no }}</span><br/>
                        <span v-for="file in req.files" :key="file.id" class="text-xs text-gray-600">
                            File : <a :href="file.path" class="text-xs text-gray-600 hover:underline">{{ file.file_name }}</a>
                        </span>
                    </div> -->
                    <div class="col-span-2">{{ req.company_name }}</div>
                    <div class="col-span-2">{{ req.category }}</div>
                    <div class="col-span-2">{{ req.description }}</div>
                    <div class="col-span-2 text-gray-600">{{ formatDate(req.created_at) }}</div>
                    <div class="col-span-2 text-gray-600">{{ formatDate(req.updated_at) }}</div>
                    <!-- <div class="col-span-1">
                        <span
                            :class="[
                                'px-3 py-2 text-xs font-semibold rounded-lg capitalize',
                                req.status === 'pending' ? 'bg-[#FFF2DE] text-[#FFA247]' :
                                req.status === 'approved' ? 'bg-green-100 text-green-800' :
                                req.status === 'declined' ? 'bg-[#FFE2DE] text-[#F15642]' :
                                req.status === 'cancelled' ? 'bg-[#E7E7E7] text-black' :
                                'bg-blue-100 text-blue-800'
                            ]"
                        >
                            {{ req.status }}
                        </span>
                    </div> -->
                    <div class="col-span-2">
                        <div v-if="req.status == 'pending'" class="relative">
                            <button
                                class="w-full px-2 py-2 text-sm text-white bg-blue-600 border border-gray-300 rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            >
                                Acknowledge
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div
                v-else
                class="flex flex-col items-center justify-center py-12 text-center"
            >
                <svg class="w-16 h-16 mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="mb-1 text-lg font-medium text-gray-900">No Requests Found</h3>
                <p class="max-w-md mb-4 text-gray-500">There are no requests matching your current filters. Try adjusting your search or filters, or create a new request.</p>
            </div>

            <!-- Pagination -->
            <div class="flex items-center justify-between px-6 py-4 border-t border-gray-200">
                <div class="text-sm text-gray-500">
                    Showing <span class="font-medium">{{ paginationInfo.from }}</span> to <span class="font-medium">{{ paginationInfo.to }}</span> of <span class="font-medium">{{ paginationInfo.total }}</span> requests
                </div>
                <div class="flex items-center space-x-1">
                    <button
                        @click="changePage(currentPage - 1)"
                        :disabled="currentPage === 1"
                        :class="[
                            'px-3 py-1 rounded border',
                            currentPage === 1
                                ? 'bg-gray-100 text-gray-400 border-gray-200 cursor-not-allowed'
                                : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'
                        ]"
                    >
                        Previous
                    </button>

                    <button
                        v-for="page in visiblePageNumbers"
                        :key="page"
                        @click="changePage(page)"
                        :class="[
                            'px-3 py-1 rounded border',
                            currentPage === page
                                ? 'bg-blue-50 text-blue-600 border-blue-300'
                                : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'
                        ]"
                    >
                        {{ page }}
                    </button>

                    <button
                        @click="changePage(currentPage + 1)"
                        :disabled="currentPage === totalPages"
                        :class="[
                            'px-3 py-1 rounded border',
                            currentPage === totalPages
                                ? 'bg-gray-100 text-gray-400 border-gray-200 cursor-not-allowed'
                                : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'
                        ]"
                    >
                        Next
                    </button>
                </div>
            </div>
        </div>
        <TaskModal :show="showTaskModal" :currentUser="page_data.user" @close="showTaskModal = false" @submit="fetchTasks" />
        <RequestDetailedModal :show="showRequestDetailedModal" :page_data="page_data" :requestId="selectedRequestId" :request="selectedRequest" @close="showRequestDetailedModal = false" />
    </div>
</template>
<script>
import TaskModal from './Modal/TaskModal.vue';
import RequestDetailedModal from './Modal/RequestDetailedModal.vue';
import RequestForms from '../../AppComponents/Shared/RequestForms.vue';


    export default {
        name: 'admin-change-request',
        components: {
            RequestForms,
            TaskModal,
            RequestDetailedModal
        },
        props: {
            page_data: { type: Object, required: true },
            company_data: { type: Object, required: true },
            company_id: { type: Number },
            data: {
                type: [Array, Object],
                required: true,
                validator: function(value) {
                    // Allow either an array or an object with data property
                    return Array.isArray(value) || (typeof value === 'object' && value !== null);
                }
            }
        },
        data() {
            return {
                icon : '/icon/task.svg',
                showTaskModal: false,
                activeTab: 'pending',
                searchQuery: '',
                categoryFilter: '',
                dateFilter: 'all',
                sortField: 'date',
                sortOrder: 'desc',
                currentPage: 1,
                itemsPerPage: 10,
                selectAll: false,
                selectedRequests: [],
                isLoading: false,
                openDropdownId: null, // Track which dropdown is currently open
                showRequestModal: false,
                showRequestDetailedModal: false,
                selectedRequestId: null,

                // Tab options
                tabs: [
                    { label: 'Pending', value: 'pending', count: 1 },
                    { label: 'Declined', value: 'declined', count: 1 },
                    { label: 'Cancelled', value: 'cancelled', count: 1 },
                    { label: 'Approved', value: 'approved', count: 1 },
                    { label: 'All', value: 'all', count: null },
                ],

                // Filter options
                categories: [
                    "Change Owner",
                    "Change Capital",
                    "Change Director / Manager / Secretary",
                    "Change UBO",
                    "Change Registered Agent",
                    "Bank Account",
                    "Name Change",
                ],
                changeCategories: [
                    "Change Owner",
                    "Change Capital",
                    "Change Director / Manager / Secretary",
                    "Change UBO",
                    "Change Registered Agent",
                    "Bank Account",
                    "Name Change",
                    "Others",
                ],
            }
        },
        methods: {
            openRequestDetailedModal(requestId) {
                this.selectedRequestId = requestId;
                this.showRequestDetailedModal = true;
            },
            getInitials(name) {
                if (!name) return '';
                return name.split(' ')
                    .map(word => word.charAt(0).toUpperCase())
                    .join('')
                    .substring(0, 2);
            },
            toggleSort(field) {
                this.isLoading = true;
                if (this.sortField === field) {
                    // Toggle sort order if clicking the same field
                    this.sortOrder = this.sortOrder === 'asc' ? 'desc' : 'asc';
                } else {
                    // Default to descending order when changing fields
                    this.sortField = field;
                    this.sortOrder = 'desc';
                }
                // Reset to first page when sorting changes
                this.currentPage = 1;

                // Simulate loading time
                setTimeout(() => {
                    this.isLoading = false;
                }, 500);
            },

            changePage(page) {
                if (page < 1 || page > this.totalPages) return;
                this.isLoading = true;
                this.currentPage = page;

                // Simulate loading time
                setTimeout(() => {
                    this.isLoading = false;
                }, 500);
            },

            toggleSelectAll() {
                if (this.selectAll) {
                    // Select all visible items (only current page)
                    this.selectedRequests = this.paginatedRequests.map(req => req.id);
                } else {
                    // Deselect all
                    this.selectedRequests = [];
                }
            },

            applyStatusFilter(requests) {
                if (this.activeTab === 'all') return requests;

                // Map the tab values to the actual status values in the data
                const statusMap = {
                    'pending': 'Pending',
                    'approved': 'Approved',
                    'ongoing': 'Ongoing',
                    'declined': 'Declined',
                    'cancelled': 'Cancelled'
                };

                // Get the mapped status or use the activeTab value as a fallback
                const statusToFilter = statusMap[this.activeTab];

                // Check if we have a valid status to filter by
                if (!statusToFilter) {
                    console.warn(`No status mapping for tab value: ${this.activeTab}`);
                    return requests;
                }

                // Debug logging to help troubleshoot
                console.log(`Filtering by status: ${statusToFilter} (from tab: ${this.activeTab})`);
                console.log(`Available statuses in data:`, [...new Set(requests.map(req => req.status))]);
                console.log(`Requests before filtering:`, requests, "statusToFilter" ,statusToFilter);
                return requests.filter(req => (req.status == statusToFilter.toLowerCase()));
            },

            applySearchFilter(requests) {
                if (!this.searchQuery) return requests;

                const query = this.searchQuery.toLowerCase();
                return requests.filter(req =>
                    (req.request_no && req.request_no.toLowerCase().includes(query)) ||
                    (req.description && req.description.toLowerCase().includes(query))
                );
            },

            applyCategoryFilter(requests) {
                if (!this.categoryFilter) return requests;

                return requests.filter(req => req.category === this.categoryFilter);
            },

            applyDateFilter(requests) {
                if (this.dateFilter === 'all') return requests;

                const today = new Date();

                return requests.filter(req => {
                    // Parse the ISO date format (assuming created_at is in ISO format)
                    const reqDate = new Date(req.created_at);
                    if (isNaN(reqDate.getTime())) {
                        return true; // Skip invalid dates
                    }

                    const diffTime = Math.abs(today - reqDate);
                    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

                    switch(this.dateFilter) {
                        case 'today': return diffDays <= 1;
                        case 'week': return diffDays <= 7;
                        case 'month': return diffDays <= 30;
                        case 'quarter': return diffDays <= 90;
                        default: return true;
                    }
                });
            },

            applySorting(requests) {
                return [...requests].sort((a, b) => {
                    let valueA, valueB;

                    if (this.sortField === 'date') {
                        valueA = new Date(a.created_at);
                        valueB = new Date(b.created_at);
                    } else if (this.sortField === 'modified') {
                        valueA = new Date(a.updated_at);
                        valueB = new Date(b.updated_at);
                    } else {
                        return 0;
                    }

                    // Handle invalid dates
                    if (isNaN(valueA) || isNaN(valueB)) {
                        return 0;
                    }

                    if (this.sortOrder === 'asc') {
                        return valueA - valueB;
                    } else {
                        return valueB - valueA;
                    }
                });
            },
            handleRequestSuccess(requestType) {
                // Display a success message based on the request type
                const message = requestType === 'document_request'
                    ? 'Document request submitted successfully!'
                    : 'Change request submitted successfully!';

                this.successToast(message);
                setTimeout(() => {
                    window.location.reload();
                }, 500);
                // Scroll to the top to see the newly added request
                window.scrollTo({ top: 0, behavior: 'smooth' });
            },

            toggleStatusDropdown(request) {
                // If clicking the same dropdown that's already open, close it
                if (this.openDropdownId === request.id) {
                    this.openDropdownId = null;
                } else {
                    // Otherwise, open this dropdown (and close any other)
                    this.openDropdownId = request.id;
                }
            },

            updateStatus(request, newStatus) {
                request.status = newStatus;
                this.openDropdownId = null; // Close the dropdown
                this.handleStatusChange(request);
            },

            // Click outside to close dropdown
            handleClickOutside(event) {
                if (!event.target.closest('.relative')) {
                    this.openDropdownId = null;
                }
            },

            async handleStatusChange(request) {
                this.isLoading = true;
                // Here you would typically make an API call to update the status
                // For example:
                await axios.post(`/api/requests/${request.id}`, { status: request.status })
                    .then(response => {
                        this.successToast('Request status updated successfully');
                    })
                    .catch(error => {
                        this.errorToast('Failed to update request status');
                        // Revert the status change
                        request.status = originalStatus;
                    })
                    .finally(() => {
                        this.isLoading = false;
                    });

                // For now, we'll just simulate the API call
                setTimeout(() => {
                    this.successToast('Request status updated successfully');
                    this.isLoading = false;
                }, 500);
            }
        },
        computed: {
            // Normalize data to always work with an array
            requestsData() {
                if (Array.isArray(this.data)) {
                    return this.data;
                } else if (this.data && typeof this.data === 'object') {
                    // Check if data has a property that might contain the array
                    const possibleArrayProps = ['data', 'items', 'requests'];
                    for (const prop of possibleArrayProps) {
                        if (Array.isArray(this.data[prop])) {
                            return this.data[prop];
                        }
                    }
                }
                return []; // Return empty array as fallback
            },

            filteredRequests() {
                let result = this.requestsData;

                // Apply filters in sequence
                result = this.applyStatusFilter(result);
                result = this.applySearchFilter(result);
                result = this.applyCategoryFilter(result);
                result = this.applyDateFilter(result);

                // Apply sorting
                result = this.applySorting(result);

                return result;
            },

            paginatedRequests() {
                const startIndex = (this.currentPage - 1) * this.itemsPerPage;
                const endIndex = startIndex + this.itemsPerPage;
                return this.filteredRequests.slice(startIndex, endIndex);
            },

            totalPages() {
                return Math.max(1, Math.ceil(this.filteredRequests.length / this.itemsPerPage));
            },

            visiblePageNumbers() {
                const pages = [];
                const maxVisiblePages = 5;

                if (this.totalPages <= maxVisiblePages) {
                    // Show all pages if there are few
                    for (let i = 1; i <= this.totalPages; i++) {
                        pages.push(i);
                    }
                } else {
                    // Show a window of pages centered on current page
                    let start = Math.max(1, this.currentPage - Math.floor(maxVisiblePages / 2));
                    let end = start + maxVisiblePages - 1;

                    if (end > this.totalPages) {
                        end = this.totalPages;
                        start = Math.max(1, end - maxVisiblePages + 1);
                    }

                    for (let i = start; i <= end; i++) {
                        pages.push(i);
                    }
                }

                return pages;
            },

            paginationInfo() {
                const from = this.filteredRequests.length === 0 ? 0 : (this.currentPage - 1) * this.itemsPerPage + 1;
                const to = Math.min(this.currentPage * this.itemsPerPage, this.filteredRequests.length);
                const total = this.filteredRequests.length;

                return { from, to, total };
            }
        },
        watch: {
            // Reset to first page when filters change
            activeTab() {
                this.isLoading = true;
                this.currentPage = 1;
                this.selectedRequests = [];
                this.selectAll = false;

                // Simulate loading time
                setTimeout(() => {
                    this.isLoading = false;
                }, 500);
            },
            searchQuery() {
                this.isLoading = true;
                this.currentPage = 1;

                // Simulate loading time with debounce for typing
                setTimeout(() => {
                    this.isLoading = false;
                }, 500);
            },
            categoryFilter() {
                this.isLoading = true;
                this.currentPage = 1;

                // Simulate loading time
                setTimeout(() => {
                    this.isLoading = false;
                }, 500);
            },
            dateFilter() {
                this.isLoading = true;
                this.currentPage = 1;

                // Simulate loading time
                setTimeout(() => {
                    this.isLoading = false;
                }, 500);
            },
            sortField() {
                this.currentPage = 1;
            },
            sortOrder() {
                this.currentPage = 1;
            }
        },
        mounted() {
            // Add click outside event listener
            document.addEventListener('click', this.handleClickOutside);

            // Simulate initial loading
            this.isLoading = true;
            setTimeout(() => {
                this.isLoading = false;

                // Debug logging to see what data we're working with
                const requests = this.data;
                if (requests.length > 0) {
                    const statuses = [...new Set(requests.map(req => req.status))];
                    console.log('Available statuses in data:', statuses);
                    console.log('First few request objects:', requests.slice(0, 3));
                } else {
                    console.warn('No request data found or empty array');
                    console.log('Original data prop:', this.data);
                }
            }, 1000);
        }
    }
</script>

<style scoped>
.border-orange-500 {
    border-color: #f97316;
}

input[type="checkbox"] {
    cursor: pointer;
}

/* Status badge styling */
.bg-amber-100 {
    background-color: #fef3c7;
}
.text-amber-800 {
    color: #92400e;
}
.bg-green-100 {
    background-color: #dcfce7;
}
.text-green-800 {
    color: #166534;
}
.bg-red-100 {
    background-color: #fee2e2;
}
.text-red-800 {
    color: #991b1b;
}
.bg-blue-100 {
    background-color: #dbeafe;
}
.text-blue-800 {
    color: #1e40af;
}

/* Transitions */
.transition-colors {
    transition-property: background-color, border-color, color, fill, stroke;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 150ms;
}

/* Pagination active style */
.bg-blue-50 {
    background-color: #eff6ff;
}
.text-blue-600 {
    color: #2563eb;
}
.border-blue-300 {
    border-color: #93c5fd;
}

/* Loading animation */
.animate-spin {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}

.text-brand-active {
    color: #2563eb; /* Default to blue if custom brand color not defined */
}

/* Hover effects */
button:not([disabled]):hover {
    transform: translateY(-1px);
}
</style>


