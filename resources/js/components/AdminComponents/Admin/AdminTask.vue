<template>
<div class="w-full min-h-screen p-6">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-extrabold text-gray-900">Client's Tasks</h1>
        <div class="flex space-x-3">
            <button class="btn bg-[#D57B3F] text-white px-4 py-6 text-lg" @click="showTaskModal = true">Create New Task <img :src="icon"></button>
        </div>
    </div>
    <!-- Tabs and Search Bar in a flex container -->
    <div class="flex flex-col items-start justify-between mb-6 space-y-4 md:flex-row md:items-center md:space-y-0">
        <!-- Tabs -->
        <div class="w-full md:w-auto">
            <div class="flex overflow-hidden bg-gray-100 border border-gray-300 rounded-lg">
                <button @click="changeTab('open')" class="px-6 py-2 text-sm font-medium transition-colors focus:outline-none" :class="activeTab === 'open'
              ? 'bg-white text-gray-900 shadow font-semibold'
              : 'bg-[#EAECF3] text-gray-500 hover:text-gray-700 border-0'" style="border-radius: 0.5rem 0 0 0.5rem;">
                    Open Tasks
                </button>
                <button @click="changeTab('pending')" class="px-6 py-2 text-sm font-medium transition-colors border-l border-gray-200 focus:outline-none" :class="activeTab === 'pending'
              ? 'bg-white text-gray-900 shadow font-semibold'
              : 'bg-[#EAECF3] text-gray-500 hover:text-gray-700 border-0'">
                    In-progress
                </button>
                <button @click="changeTab('completed')" class="px-6 py-2 text-sm font-medium transition-colors border-l border-gray-200 focus:outline-none" :class="activeTab === 'completed'
              ? 'bg-white text-gray-900 shadow font-semibold'
              : 'bg-[#EAECF3] text-gray-500 hover:text-gray-700 border-0'" style="border-radius: 0 0.5rem 0.5rem 0;">
                    Completed
                </button>
            </div>
        </div>

        <!-- Search Bar -->
        <div class="relative w-full md:w-64">
            <input
                type="text"
                v-model="searchQuery"
                @input="debounceSearch"
                placeholder="Search"
                class="w-full py-2 pl-10 pr-4 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
            />
            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
        </div>
    </div>
    <!-- Pending Tasks -->
    <!-- <div class="space-y-4" v-if="activeTab === 'open'"> -->
    <!-- <div class="space-y-4"> -->
        <!-- Task Item -->

        <div class="overflow-hidden bg-white border rounded-lg shadow-sm">
            <!-- Table Header -->
            <div class="grid grid-cols-12 px-6 py-3 text-sm font-medium text-gray-600 bg-gray-100">
                <div class="col-span-3">Task Name</div>
                <div class="col-span-3">Company</div>
                <div class="col-span-2">
                    Created Date
                    <button class="ml-1 focus:outline-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="inline-block w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                </div>
                <div class="col-span-2">
                    Due Date
                    <button class="ml-1 focus:outline-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="inline-block w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                </div>
                <div class="col-span-1">Status</div>
                <div class="col-span-1">Remind</div>
            </div>
             <div v-if="loading" class="flex flex-col items-center justify-center py-12 text-center bg-white rounded-lg shadow-sm">
                <div class="flex items-center gap-2 px-4 py-2 text-sm font-medium leading-none text-brand-active">
                    <svg class="w-5 h-5 -ml-1 animate-spin text-brand-active" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Loading...
                </div>
            </div>
            <!-- Table Rows -->
              <div v-else-if="filteredTasks.length > 0">
                <div v-for="(task, index) in filteredTasks" :key="index"
                    class="grid grid-cols-12 px-6 py-4 text-sm transition-colors border-b border-gray-200 cursor-pointer hover:bg-gray-50"
                    @click="openTaskDetailedModal(task.id)">
                    <div class="col-span-3 font-medium text-gray-800">{{ task.title }}</div>
                    <div class="col-span-3 text-gray-700">
                        <div class="flex items-center" v-for="(company, userIndex) in task.companies" :key="userIndex">
                            {{ (company) ? company.name : 'NA' }}
                        </div>
                    </div>
                    <div class="col-span-2 text-gray-600">{{ formatDate(task.created_at) }}</div>
                    <div class="col-span-2 text-gray-600">{{ formatDate(task.deadline) }}</div>

                    <!-- Status Badge -->
                    <div class="col-span-1">
                        <button v-if="task.status === 'open'"
                            class="font-semibold text-[#FFA247] px-4 bg-[#FFF2DE] btn-sm">
                            Pending
                        </button>
                        <button v-else-if="task.status === 'pending'"
                            class="px-4 font-semibold text-blue-800 bg-blue-100 btn-sm">
                            In-progress
                        </button>
                        <button v-else-if="task.status === 'completed'"
                            class="px-4 font-semibold text-green-800 bg-green-100 btn-sm">
                            Completed
                        </button>
                    </div>

                    <!-- Reminder Bell Icon -->
                    <div class="col-span-1 p-0">
                        <button class="px-1 bg-gray-300 btn btn-sm hover:bg-red-800 hover:text-white focus:outline-none">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Empty State -->
        <div v-if="filteredTasks.length === 0 && !loading" class="flex flex-col items-center justify-center py-12 text-center bg-white rounded-lg shadow-sm">
            <svg class="w-16 h-16 mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
            </svg>
            <h3 class="mb-1 text-lg font-medium text-gray-900">No Tasks Found</h3>
            <p class="max-w-md mb-4 text-gray-500">
                {{ searchQuery ? 'No tasks match your search criteria.' : `There are no ${activeTab} tasks at the moment.` }}
            </p>
        </div>

        <!-- Pagination -->
        <div v-if="pagination.totalPages > 1" class="flex items-center justify-between px-6 py-4 bg-white border-t border-gray-200">
            <div class="text-sm text-gray-500">
                Showing <span class="font-medium">{{ pagination.totalItems > 0 ? ((pagination.currentPage - 1) * pagination.perPage) + 1 : 0 }}</span> to <span class="font-medium">{{ Math.min(pagination.currentPage * pagination.perPage, pagination.totalItems) }}</span> of <span class="font-medium">{{ pagination.totalItems }}</span> tasks
            </div>
            <div class="flex items-center space-x-1">
                <button
                    @click="goToPage(pagination.currentPage - 1)"
                    :disabled="pagination.currentPage === 1"
                    :class="[
                        'px-3 py-1 rounded border',
                        pagination.currentPage === 1
                            ? 'bg-gray-100 text-gray-400 border-gray-200 cursor-not-allowed'
                            : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'
                    ]"
                >
                    Previous
                </button>

                <button
                    v-for="page in pageRange"
                    :key="page"
                    @click="goToPage(page)"
                    :class="[
                        'px-3 py-1 rounded border',
                        pagination.currentPage === page
                            ? 'bg-blue-50 text-blue-600 border-blue-300'
                            : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'
                    ]"
                >
                    {{ page }}
                </button>

                <button
                    @click="goToPage(pagination.currentPage + 1)"
                    :disabled="pagination.currentPage === pagination.totalPages"
                    :class="[
                        'px-3 py-1 rounded border',
                        pagination.currentPage === pagination.totalPages
                            ? 'bg-gray-100 text-gray-400 border-gray-200 cursor-not-allowed'
                            : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'
                    ]"
                >
                    Next
                </button>
            </div>
        </div>
    <!-- </div> -->

    <!-- Task Modal -->
    <TaskModal :show="showTaskModal" :currentUser="page_data.user" @close="showTaskModal = false" @submit="fetchTasks" />

    <!-- Task Detailed Modal -->
    <TaskDetailedModal :show="showTaskDetailedModal" :page_data="page_data" :taskId="selectedTaskId" @close="showTaskDetailedModal = false" @update="handleTaskUpdate" />
</div>
</template>

<script>
import TaskModal from './Modal/TaskModal.vue';
import TaskDetailedModal from './Modal/TaskDetailedModal.vue';
import CommentThread from './CommentThread.vue';

export default {
    name: 'admin-task',
    components: {
        TaskModal,
        TaskDetailedModal,
        CommentThread
    },
    props: {
        page_data: {
            type: Object,
            default: () => ([])
        }
    },
    data() {
        return {
            icon : '/icon/task.svg',
            activeTab: 'open',
            searchQuery: '',
            searchTimeout: null,
            currentUser: this.page_data.user.userprofile.name || 'Current User',
            showTaskModal: false,
            showTaskDetailedModal: false,
            selectedTaskId: null,
            tasks: [],
            completedTasks: [],
            loading: false,
            pagination: {
                currentPage: 1,
                perPage: 10,
                totalItems: 0,
                totalPages: 0
            },
        };
    },
    computed: {
        filteredTasks() {
            const query = (this.searchQuery) ? this.searchQuery.toLowerCase().trim() : '';
            return this.tasks.filter(task => task.status == this.activeTab && (task.title.toLowerCase().includes(query) ||
                    task.description.toLowerCase().includes(query) ||
                    task.status.toLowerCase().includes(query))
                );
        },
        filteredCompletedTasks() {
            const query = (this.searchQuery) ? this.searchQuery.toLowerCase().trim() : '';
            let res = this.tasks.filter(task => task.status == 'completed' && (
                        task.title.toLowerCase().includes(query) ||
                        task.description.toLowerCase().includes(query)
                    ));
            console.log(res, "completed tasks");
            return res;
        },
        filteredInProgressTasks() {
            const query = (this.searchQuery) ? this.searchQuery.toLowerCase().trim() : '';
            return this.tasks.filter(task =>
                task.status === 'pending' && (
                    task.title.toLowerCase().includes(query) ||
                    task.description.toLowerCase().includes(query) ||
                    task.status.toLowerCase().includes(query)
                )
            );
        },
        pageRange() {
            const pages = [];
            const maxVisiblePages = 5;

            if (this.pagination.totalPages <= maxVisiblePages) {
                // Show all pages if there are few
                for (let i = 1; i <= this.pagination.totalPages; i++) {
                    pages.push(i);
                }
            } else {
                // Show a window of pages centered on current page
                let start = Math.max(1, this.pagination.currentPage - Math.floor(maxVisiblePages / 2));
                let end = start + maxVisiblePages - 1;

                if (end > this.pagination.totalPages) {
                    end = this.pagination.totalPages;
                    start = Math.max(1, end - maxVisiblePages + 1);
                }

                for (let i = start; i <= end; i++) {
                    pages.push(i);
                }
            }

            return pages;
        }
    },
    methods: {
        goToPage(page) {
            if (page < 1 || page > this.pagination.totalPages) return;
            this.loading = true;
            this.pagination.currentPage = page;

            // Fetch tasks with updated page
            this.fetchTasks();

            // Scroll to top
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        },
        changeTab(tabName) {
            // Only fetch if changing to a different tab
            if (this.activeTab !== tabName) {
                this.activeTab = tabName;
                this.searchQuery = '';
                this.pagination.currentPage = 1;
                this.fetchTasks();
            }
        },
        // Debounce search to prevent too many API calls while typing
        debounceSearch() {
            // Clear any existing timeout
            if (this.searchTimeout) {
                clearTimeout(this.searchTimeout);
            }

            // Set a new timeout
            this.searchTimeout = setTimeout(() => {
                this.pagination.currentPage = 1;
                this.fetchTasks();
            }, 500); // Wait for 500ms after the user stops typing
        },
        getTaskBorderColor(task) {
            switch (task.status) {
                case 'pending':
                    return 'border-red-500';
                case 'high':
                    return 'border-orange-500';
                case 'comments':
                    return 'border-yellow-500';
                case 'completed':
                    return 'border-green-500';
                default:
                    return 'border-blue-500';
            }
        },
        getTaskStatusClass(task) {
            switch (task.statusType) {
                case 'overdue':
                    return 'bg-red-100 text-red-800 cursor-pointer';
                case 'high':
                    return 'bg-orange-100 text-orange-800 cursor-pointer';
                case 'comments':
                    return 'bg-yellow-100 text-yellow-800 cursor-pointer';
                default:
                    return 'bg-blue-100 text-blue-800 cursor-pointer';
            }
        },
        toggleTaskForm(task) {
            // Close all other task forms and comment sections first
            this.tasks.forEach(t => {
                if (t.id !== task.id) {
                    t.showForm = false;
                    t.showCommentsSection = false;
                }
            });

            // Toggle this task's form
            task.showForm = !task.showForm;

            // Reset form values if closing
            if (!task.showForm) {
                task.statusComment = '';
                task.updatedStatus = '';
            }
        },
        openTaskDetailedModal(taskId) {
            this.selectedTaskId = taskId;
            this.showTaskDetailedModal = true;
        },

        handleTaskUpdate(updatedTask) {
            // Find and update the task in the tasks array
            const index = this.tasks.findIndex(task => task.id === updatedTask.id);
            if (index !== -1) {
                this.tasks[index] = { ...this.tasks[index], ...updatedTask };
            }

            // Reset to first page and refresh the task list
            this.pagination.currentPage = 1;
            this.fetchTasks();
        },

        toggleComments(task) {
            // Close all other forms and comment sections
            this.tasks.forEach(t => {
                if (t.id !== task.id) {
                    t.showCommentsSection = false;
                    t.showForm = false;
                }
            });

            // Toggle this task's comments section
            task.showCommentsSection = !task.showCommentsSection;
        },
        cancelTaskUpdate(task) {
            task.showForm = false;
            task.statusComment = '';
            task.updatedStatus = '';
        },
        handleCommentFileChange(event, task) {
            const files = Array.from(event.target.files);
            if (!task.selectedFiles) {
                task.selectedFiles = [];
            }
            task.selectedFiles.push(...files);
        },
        removeCommentFile(task, index) {
            task.selectedFiles.splice(index, 1);
        },
        async addComment(task) {
            if (!task.newComment.trim() && !task.updateComment) return this.errorToast('Comment cannot be empty.');

            const formData = new FormData();
            formData.append('message', task.updateComment || task.newComment);
            formData.append('user_id', this.page_data.user.id);
            formData.append('parent_id', 0); // Ensure this is a parent comment
            formData.append('status', task.updateStatus || task.status);

            // Append files if any
            if (task.selectedFiles && task.selectedFiles.length > 0) {
                task.selectedFiles.forEach((file, index) => {
                    formData.append(`files[${index}]`, file);
                });
            }

            await axios.post(`/api/tasks/comments/${task.id}`, formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            })
            .then(response => {
                // console.log('Comment added successfully:', response.data);
                if(response.data.status === 'success') {
                    // Create new comment with empty replies array
                    const newComment = {
                        id: response.data.comment.id,
                        author: {
                            author_name: this.currentUser,
                            created_at: new Date().toISOString()
                        },
                        message: task.newComment,
                        replies: [], // Initialize empty replies array
                        files: (task.selectedFiles && task.selectedFiles.length > 0) ? task.selectedFiles.map(file => ({
                            file_name: file.name,
                            file_path: URL.createObjectURL(file) // Use a temporary URL for display
                        })) : [],
                        parent_id: response.data.comment.id // Mark as parent comment
                    };

                    // console.log('New comment:', newComment);

                    // Add to the beginning of the comments array
                    task.comments.push(newComment);
                    // Clear the comment field and attachments after adding
                    task.status = task.updateStatus || task.status;
                    task.newComment = '';
                    task.selectedFiles = [];
                    this.successToast('Comment added successfully!');
                    // this.fetchTasks();
                } else {
                    console.error('Failed to add comment:', response.data.message);
                    this.errorToast('Failed to add comment: ' + response.data.message);
                }
            })
            .catch(error => {
                console.error('Error adding comment:', error);
            });
            // console.log('Comment added:', task.newComment);
            // Clear the comment field
        },
        addSystemComment(task, text) {
            task.comments.push({
                author: 'System',
                text: text,
                date: 'Just now',
                avatar: null
            });
        },
        getInitials(name) {
            if (!name) return '';
            return name.split(' ')
                .map(word => word.charAt(0).toUpperCase())
                .join('')
                .substring(0, 2);
        },
        async fetchTasks() {
            this.loading = true;
            this.tasks = [];
            await axios.get('/api/tasks/list', {
                params: {
                    status: this.activeTab,
                    page: this.pagination.currentPage,
                    perPage: this.pagination.perPage,
                    search: this.searchQuery
                }
            })
                .then(response => {
                    // Check if the response is paginated
                    if (response.data.data) {
                        // It's a Laravel paginator response
                        this.pagination.totalItems = response.data.total;
                        this.pagination.currentPage = response.data.current_page;
                        this.pagination.totalPages = response.data.last_page;

                        this.tasks = response.data.data.map(task => this.formatTaskData(task));
                    } else {
                        // It's the old response format, we need to handle it manually
                        this.tasks = Object.values(response.data).map(task => this.formatTaskData(task));
                        this.pagination.totalItems = this.tasks.length;
                        this.pagination.totalPages = Math.ceil(this.tasks.length / this.pagination.perPage);
                    }
                    this.loading = false;
                })
                .catch(error => {
                    this.loading = false;
                    console.error('Error fetching tasks:', error);
                });
        },

        // Helper method to format task data consistently
        formatTaskData(task) {
            // Structure comments into parent-child hierarchy
            const comments = task.comments || [];
            const parentComments = [];
            const commentReplies = {};

            // First pass: Separate parent comments and replies
            comments.forEach(comment => {
                if (!comment.parent_id) {
                    // This is a parent comment
                    comment.replies = [];
                    parentComments.push(comment);
                } else {
                    // This is a reply
                    if (!commentReplies[comment.parent_id]) {
                        commentReplies[comment.parent_id] = [];
                    }
                    commentReplies[comment.parent_id].push(comment);
                }
            });

            // Second pass: Attach replies to their parent comments
            parentComments.forEach(comment => {
                if (commentReplies[comment.id]) {
                    comment.replies = commentReplies[comment.id];
                }
            });

            return {
                ...task,
                dueDate: this.formatDate(task.deadline),
                status: task.status,
                showForm: false,
                statusType: task.is_priority ? 'high' : 'normal',
                showCommentsSection: false,
                newComment: '',
                statusComment: '',
                updatedStatus: '',
                comments: parentComments // Use the structured comments
            };
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
        removeTaskFile(task, fileIndex) {
            // Remove the file from the task's files array
            task.files.splice(fileIndex, 1);

            // You might want to make an API call here to update the backend
            // axios.delete(`/api/tasks/${task.id}/files/${fileIndex}`)
            //     .then(response => {
            //         console.log('File removed successfully');
            //     })
            //     .catch(error => {
            //         console.error('Error removing file:', error);
            //     });
        },
        toggleLike(task, comment) {
            // If likes property doesn't exist, initialize it
            if (!comment.hasOwnProperty('likes')) {
                comment.likes = 0;
                comment.isLiked = false;
            }

            const formData = new FormData();
            formData.append('comment_id', comment.id);
            formData.append('user_id', this.page_data.user.id);

            axios.post(`/api/tasks/comments/like/${comment.id}`, formData)
                .then(response => {
                    if(response.data.status === 'success') {
                        comment.isLiked = !comment.isLiked;
                        comment.likes += comment.isLiked ? 1 : -1;
                        this.successToast(comment.isLiked ? 'Comment liked!' : 'Comment unliked!');
                    } else {
                        this.errorToast('Failed to update like status: ' + response.data.message);
                    }
                })
                .catch(error => {
                    console.error('Error updating like:', error);
                    this.errorToast('Error updating like status');
                });
        },

        toggleReply(task, comment) {
            // Close all other reply forms first
            task.comments.forEach(c => {
                if (c !== comment) {
                    c.showReplyForm = false;
                    c.replyText = '';
                }
            });

            // Toggle this comment's reply form
            comment.showReplyForm = !comment.showReplyForm;
            if (!comment.showReplyForm) {
                comment.replyText = '';
            }
        },

        cancelReply(comment) {
            comment.showReplyForm = false;
            comment.replyText = '';
        },

        async submitReply(task, comment) {
            if (!comment.replyText || !comment.replyText.trim()) return;

            const formData = new FormData();
            formData.append('message', comment.replyText);
            formData.append('user_id', this.page_data.user.id);
            formData.append('parent_id', comment.id); // Ensure we're setting the parent_id

            await axios.post(`/api/tasks/comments/${task.id}`, formData)
                .then(response => {
                    if(response.data.status === 'success') {
                        // Initialize replies array if it doesn't exist
                        if (!comment.replies) {
                            comment.replies = [];
                        }

                        // Add the new reply with proper structure
                        const newReply = {
                            id: response.data.id,
                            parent_id: comment.id, // Set the parent_id
                            author: {
                                author_name: this.currentUser,
                                created_at: new Date().toISOString()
                            },
                            message: comment.replyText,
                            created_at: new Date().toISOString()
                        };

                        // Add reply to the parent comment's replies array
                        comment.replies.push(newReply);

                        // Clear the reply form
                        comment.replyText = '';
                        comment.showReplyForm = false;
                        this.successToast('Reply added successfully!');
                    } else {
                        this.errorToast('Failed to add reply: ' + response.data.message);
                    }
                })
                .catch(error => {
                    console.error('Error adding reply:', error);
                    this.errorToast('Error adding reply');
                });
        },
        handleCommentReplyAdded(reply) {
            // This method will be triggered whenever a reply is added at any level
            // You can use this to update UI, show notifications, or trigger other actions
            this.successToast('Reply added successfully!');
        },
    },
    async mounted() {
        this.loading = true;
        // Simulate loading data
        setTimeout(() => {
            this.fetchTasks();
        }, 1000);

        console.log('Setting up WebSocket listeners...');
        const channel = window.Echo.private('tasks');

        // Debug channel subscription
        channel.listenForWhisper('subscription_succeeded', () => {
            console.log('Successfully subscribed to tasks channel');
        });

        channel.listenForWhisper('subscription_error', (error) => {
            console.error('Failed to subscribe to tasks channel:', error);
        });

        // Listen for task events
        channel.listen('.TaskCreated', (e) => {
            console.log('New task event received:', e);
            // Add the new task to your tasks array
            const newTask = {
                id: e.id,
                title: e.title,
                description: e.description || 'No description provided',
                status: e.status,
                deadline: e.deadline,
                dueDate: this.formatDate(e.deadline),
                showForm: false,
                statusType: e.is_priority ? 'high' : 'normal',
                showCommentsSection: false,
                newComment: '',
                statusComment: '',
                updatedStatus: '',
                comments: [],
                files: [],
                participants: [],
                observers: []
            };
            console.log('Adding new task to list:', newTask);
            this.tasks.unshift(newTask);
        });
    },
    watch: {
        // We'll remove the searchQuery watcher since we're using debounce
    },
    created() {
        this.searchQuery = this.$route && this.$route.query ? this.$route.query.search || '' : '';
    }
};
</script>

<style scoped>
/* Task list animations */
.task-enter-active,
.task-leave-active {
    transition: all 0.3s ease;
}

.task-enter,
.task-leave-to {
    opacity: 0;
    transform: translateY(20px);
}

/* Comment section transitions */
.comment-section-enter-active,
.comment-section-leave-active {
    transition: all 0.3s ease;
    max-height: 1000px;
    overflow: hidden;
}

.comment-section-enter,
.comment-section-leave-to {
    max-height: 0;
    opacity: 0;
    overflow: hidden;
}

/* Responsive adjustments for mobile */
@media (max-width: 640px) {
    .task-action-buttons {
        flex-direction: column;
        align-items: flex-start;
    }

    .task-action-buttons>* {
        margin-top: 0.5rem;
    }
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

/* Hover effects */
button:not([disabled]):hover {
    transform: translateY(-1px);
}
</style>
