<template>
<div class="w-full min-h-screen p-6">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-extrabold text-gray-900">Data Update Request</h1>
    </div>

    <!-- Tabs as Grouped Button and Search/Filter -->
    <div class="mb-6">
        <div class="flex flex-col md:flex-row md:justify-between md:items-center">
            <!-- Search and Filter -->
            <div class="flex flex-col md:flex-row md:items-center md:space-x-4">
                <!-- Search -->
                <div class="relative flex-shrink-0 w-full mb-4 md:w-64 md:mb-0">
                    <input type="text" v-model="searchQuery" placeholder="Search..." class="w-full py-2 pl-10 pr-4 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" />
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>

                <!-- Filter Options -->
                <div class="flex flex-wrap flex-grow gap-3">
                    <div class="flex items-center justify-end w-full md:w-auto">
                        <select v-model="dateFilter" class="py-2 pl-3 pr-10 border border-gray-300 rounded-md appearance-none focus:outline-none focus:ring-2 focus:ring-blue-500">
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
    <div class="w-full overflow-x-auto bg-white border border-gray-300 rounded-lg shadow">
        <table class="min-w-full table-auto table-border">
            <thead>
                <tr class="text-sm font-medium text-gray-600 bg-gray-50">
                    <th class="px-6 py-3 text-left border-b border-gray-200">Request By</th>
                    <th class="px-6 py-3 text-left border-b border-gray-200">Field Name</th>
                    <th class="px-6 py-3 text-left border-b border-gray-200">Current Value</th>
                    <th class="px-6 py-3 text-left border-b border-gray-200">Change Value</th>
                    <th class="px-6 py-3 text-left border-b border-gray-200">Status</th>
                    <th class="px-6 py-3 text-left border-b border-gray-200">
                        <div class="flex items-center gap-1 cursor-pointer" @click="toggleSort('date')">
                            Created At
                            <span v-if="sortField === 'date'" class="ml-1">
                                {{ sortOrder === 'asc' ? '↑' : '↓' }}
                            </span>
                        </div>
                    </th>
                    <th class="px-6 py-3 text-left border-b border-gray-200">Actions</th>
                </tr>
            </thead>

            <!-- Table Body -->
            <tbody v-if="isLoading">
                <tr>
                    <td colspan="7" class="px-6 py-4">
                        <div class="flex items-center justify-center">
                            <div class="flex items-center gap-2 text-sm font-medium leading-none text-brand-active">
                                <svg class="w-5 h-5 -ml-1 animate-spin text-brand-active" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Loading...
                            </div>
                        </div>
                    </td>
                </tr>
            </tbody>
            <tbody v-else-if="groupedRequests.length > 0">
                <template v-for="(group, groupIndex) in paginatedGroupedRequests" :key="groupIndex">
                    <tr v-for="(req, reqIndex) in group.requests" :key="req.id" class="text-sm transition-colors duration-150 hover:bg-gray-50" :class="{ 'border-t border-gray-200': reqIndex === 0 }">
                        <!-- Only show requester name for first row in group -->
                        <td class="px-1 py-2" v-if="reqIndex === 0" :rowspan="group.requests.length">
                            <div class="flex items-center">
                                <div class="flex items-center justify-center flex-shrink-0 w-8 h-8 mr-2 text-sm font-medium text-white bg-blue-500 rounded-full">
                                    {{ getInitials(group.requested_by) }}
                                </div>
                                {{ group.requested_by }}
                            </div>
                        </td>
                        <td class="px-1 py-2">{{ req.field_name }}</td>
                        <td class="px-1 py-2">{{ req.current_value }}</td>
                        <td class="px-1 py-2">{{ req.proposed_value }}</td>
                        <td class="px-1 py-2 capitalize">
                            <span :class="{
                                    'px-2 py-1 text-xs font-medium rounded-full': true,
                                    'bg-amber-100 text-amber-800': req.status === 'pending',
                                    'bg-green-100 text-green-800': req.status === 'approved',
                                    'bg-red-100 text-red-800': req.status === 'rejected'
                                }">
                                {{ req.status }}
                            </span>
                        </td>
                        <td class="px-1 py-2 text-gray-600">{{ formatDate(req.created_at) }}</td>
                        <td class="px-1 py-2">
                            <div v-if="req.status === 'pending'" class="flex items-center space-x-2">
                                <button @click="approveRequest(req.id)" class="px-3 py-1 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700">
                                    Approve
                                </button>
                                <button @click="rejectRequest(req.id)" class="px-3 py-1 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700">
                                    Reject
                                </button>
                            </div>
                        </td>
                    </tr>
                </template>
            </tbody>
            <tbody v-else>
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center justify-center">
                            <svg class="w-16 h-16 mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <h3 class="mb-1 text-lg font-medium text-gray-900">No Requests Found</h3>
                            <p class="max-w-md mb-4 text-gray-500">There are no requests matching your current filters. Try adjusting your search or filters, or create a new request.</p>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Pagination -->
        <div class="flex items-center justify-between px-6 py-4 border-t border-gray-200">
            <div class="text-sm text-gray-500">
                Showing <span class="font-medium">{{ paginationInfo.from }}</span> to <span class="font-medium">{{ paginationInfo.to }}</span> of <span class="font-medium">{{ paginationInfo.total }}</span> requests
            </div>
            <div class="flex items-center space-x-1">
                <button @click="changePage(currentPage - 1)" :disabled="currentPage === 1" :class="[
                            'px-3 py-1 rounded border',
                            currentPage === 1
                                ? 'bg-gray-100 text-gray-400 border-gray-200 cursor-not-allowed'
                                : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'
                        ]">
                    Previous
                </button>

                <button v-for="page in visiblePageNumbers" :key="page" @click="changePage(page)" :class="[
                            'px-3 py-1 rounded border',
                            currentPage === page
                                ? 'bg-blue-50 text-blue-600 border-blue-300'
                                : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'
                        ]">
                    {{ page }}
                </button>

                <button @click="changePage(currentPage + 1)" :disabled="currentPage === totalPages" :class="[
                            'px-3 py-1 rounded border',
                            currentPage === totalPages
                                ? 'bg-gray-100 text-gray-400 border-gray-200 cursor-not-allowed'
                                : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'
                        ]">
                    Next
                </button>
            </div>
        </div>
    </div>
</div>
</template>

<script>
export default {
    name: 'data-request',
    components: {},
    props: {
        page_data: {
            type: Object,
            required: true
        },
        company_data: {
            type: Object,
            required: true
        },
        company_id: {
            type: Number
        },
        data: {
            type: [Array, Object],
            required: true,
            validator: function (value) {
                // Allow either an array or an object with data property
                return Array.isArray(value) || (typeof value === 'object' && value !== null);
            }
        }
    },
    data() {
        return {
            searchQuery: '',
            dateFilter: 'all',
            sortField: 'date',
            sortOrder: 'desc',
            currentPage: 1,
            itemsPerPage: 10,
            isLoading: false,
        }
    },
    methods: {
        async approveRequest(id) {
            try {
                const response = await axios.post(`/api/change-requests/${id}/approve`);
                if (response.data.success) {
                    // Update the request status in the local data
                    for (const group of this.groupedRequests) {
                        const request = group.requests.find(r => r.id === id);
                        if (request) {
                            request.status = 'approved';
                            break;
                        }
                    }
                    // Show success message
                    this.successToast('Request approved successfully');
                }
            } catch (error) {
                this.errorToast('Failed to approve request');
                console.error('Error approving request:', error);
            }
        },

        async rejectRequest(id) {
            try {
                const response = await axios.post(`/api/change-requests/${id}/reject`);
                if (response.data.success) {
                    // Update the request status in the local data
                    for (const group of this.groupedRequests) {
                        const request = group.requests.find(r => r.id === id);
                        if (request) {
                            request.status = 'rejected';
                            break;
                        }
                    }
                    // Show success message
                    this.successToast('Request rejected successfully');
                }
            } catch (error) {
                this.errorToast('Failed to reject request');
                console.error('Error rejecting request:', error);
            }
        },

        formatDate(dateString) {
            if (!dateString) return '';
            const date = new Date(dateString);
            return date.toLocaleString('en-US', {
                month: 'short',
                day: 'numeric',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
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

                switch (this.dateFilter) {
                    case 'today':
                        return diffDays <= 1;
                    case 'week':
                        return diffDays <= 7;
                    case 'month':
                        return diffDays <= 30;
                    case 'quarter':
                        return diffDays <= 90;
                    default:
                        return true;
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
        // Click outside to close dropdown
        handleClickOutside(event) {
            if (!event.target.closest('.relative')) {
                this.openDropdownId = null;
            }
        },
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
            result = this.applySearchFilter(result);
            result = this.applyDateFilter(result);
            // Apply sorting
            result = this.applySorting(result);

            return result;
        },

        groupedRequests() {
            // Group requests by model_id and model_type
            const groups = {};
            this.filteredRequests.forEach(req => {
                const key = `${req.model_type}_${req.model_id}`;
                if (!groups[key]) {
                    groups[key] = {
                        model_type: req.model_type,
                        model_id: req.model_id,
                        requested_by: req.requested_by,
                        requests: []
                    };
                }
                groups[key].requests.push({
                    ...req,
                    // Keep all original properties
                });
            });
            return Object.values(groups);
        },

        paginatedGroupedRequests() {
            const startIndex = (this.currentPage - 1) * this.itemsPerPage;
            const endIndex = startIndex + this.itemsPerPage;
            return this.groupedRequests.slice(startIndex, endIndex);
        },

        totalPages() {
            return Math.max(1, Math.ceil(this.groupedRequests.length / this.itemsPerPage));
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
            const from = this.groupedRequests.length === 0 ? 0 : (this.currentPage - 1) * this.itemsPerPage + 1;
            const to = Math.min(this.currentPage * this.itemsPerPage, this.groupedRequests.length);
            const total = this.groupedRequests.length;

            return {
                from,
                to,
                total
            };
        }
    },
    watch: {
        searchQuery() {
            this.isLoading = true;
            this.currentPage = 1;

            // Simulate loading time with debounce for typing
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
    color: #2563eb;
    /* Default to blue if custom brand color not defined */
}

/* Hover effects */
button:not([disabled]):hover {
    transform: translateY(-1px);
}
</style>
