<template>
  <div class="w-full min-h-screen p-6">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-extrabold text-gray-900">My Tasks</h1>
    </div>

    <!-- Tabs and Search Bar in a flex container -->
    <div class="flex flex-col items-start justify-between mb-6 space-y-4 md:flex-row md:items-center md:space-y-0">
      <!-- Tabs -->
      <div class="w-full md:w-auto">
        <div class="flex overflow-hidden bg-gray-100 border border-gray-300 rounded-lg">
          <button
            @click="activeTab = 'pending'"
            class="px-6 py-2 text-sm font-medium transition-colors focus:outline-none"
            :class="activeTab === 'pending'
              ? 'bg-white text-gray-900 shadow font-semibold'
              : 'bg-[#EAECF3] text-gray-500 hover:text-gray-700 border-1'"
            style="border-radius: 0.5rem 0 0 0.5rem;"
          >
            Open Tasks
          </button>
          <button
            @click="activeTab = 'in_progress'"
            class="px-6 py-2 text-sm font-medium transition-colors border-l border-gray-200 focus:outline-none"
            :class="activeTab === 'in_progress'
              ? 'bg-white text-gray-900 shadow font-semibold'
              : 'bg-[#EAECF3] text-gray-500 hover:text-gray-700 border-1'"
          >
            In-progress
          </button>
          <button
            @click="activeTab = 'completed'"
            class="px-6 py-2 text-sm font-medium transition-colors border-l border-gray-200 focus:outline-none"
            :class="activeTab === 'completed'
              ? 'bg-white text-gray-900 shadow font-semibold'
              : 'bg-[#EAECF3] text-gray-500 hover:text-gray-700 border-1'"
            style="border-radius: 0 0.5rem 0.5rem 0;"
          >
            Completed
          </button>
        </div>
      </div>

      <!-- Search Bar -->
      <div class="relative w-full md:w-64">
        <input
          type="text"
          v-model="searchQuery"
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
    <div class="space-y-4" v-if="activeTab === 'pending'">
      <!-- Task Item -->
      <div
        v-for="(task, index) in filteredTasks"
        :key="index"
        class="overflow-hidden transition-all duration-200 bg-white rounded-lg shadow-sm hover:shadow-md"
      >
        <!-- Task Header -->
        <div class="p-4 border-l-4" :class="getTaskBorderColor(task)">
          <div class="flex flex-col md:flex-row md:justify-between md:items-center">
            <div class="flex-grow">
              <h3 class="text-base font-semibold text-gray-900">{{ task.title }}</h3>
              <div class="mt-1 text-sm text-gray-600">Due Date: {{ task.dueDate }}</div>
            </div>

            <!-- Task Action Buttons -->
            <div class="flex items-center mt-3 space-x-2 md:mt-0">
              <!-- Document icon if task has documents -->
              <button
                v-if="task.hasDocument && !task.document"
                class="p-1.5 text-gray-500 bg-gray-100 rounded-md hover:bg-gray-200"
                title="Attach document"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                </svg>
              </button>

              <!-- Comment button if task has comments -->
              <button
                v-if="task.comments && task.comments.length > 0"
                class="p-1.5 text-gray-500 bg-gray-100 rounded-md hover:bg-gray-200"
                @click="toggleComments(task)"
                title="View comments"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                </svg>
              </button>

              <!-- Status Badge -->
              <div
                :class="getTaskStatusClass(task)"
                class="px-3 py-1.5 text-xs font-medium rounded-md cursor-pointer"
                @click="toggleTaskForm(task)"
              >
                {{ task.status }}
              </div>
            </div>
          </div>

          <!-- Attached Documents -->
          <div v-if="task.document" class="flex items-center p-2 mt-4 rounded-md bg-gray-50">
            <div class="flex items-center justify-center flex-shrink-0 w-8 h-8 mr-2 bg-gray-200 rounded">
              <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
              </svg>
            </div>
            <div class="flex-grow">
              <div class="text-sm font-medium">{{ task.document.name }}</div>
              <div class="text-xs text-gray-500">Click to view</div>
            </div>
            <button class="text-gray-400 hover:text-gray-600">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
        </div>

        <!-- Comments Section (Collapsible) -->
        <div v-if="task.showCommentsSection && task.comments && task.comments.length > 0" class="p-4 border-t border-gray-200 bg-gray-50">
          <h4 class="mb-3 text-sm font-medium text-gray-700">Comments</h4>

          <!-- Comment Threads -->
          <div class="space-y-3">
            <div v-for="(comment, i) in task.comments" :key="`comment-${i}`" class="flex space-x-3">
              <!-- Avatar -->
              <div class="flex items-center justify-center flex-shrink-0 w-8 h-8 overflow-hidden bg-gray-200 border border-gray-300 rounded-full">
                <span v-if="!comment.avatar" class="text-sm font-medium text-gray-600">{{ getInitials(comment.author) }}</span>
                <img v-else :src="comment.avatar" class="object-cover w-full h-full" />
              </div>

              <!-- Comment Content -->
              <div class="flex-grow">
                <div class="p-3 bg-white border border-gray-200 rounded-lg shadow-sm">
                  <div class="flex items-start justify-between">
                    <span class="text-sm font-medium text-gray-900">{{ comment.author }}</span>
                    <span class="text-xs text-gray-500">{{ comment.date || '2 days ago' }}</span>
                  </div>
                  <p class="mt-1 text-sm text-gray-800">{{ comment.text }}</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Add New Comment -->
          <div class="pt-3 mt-4 border-t border-gray-200">
            <div class="flex space-x-3">
              <div class="flex items-center justify-center flex-shrink-0 w-8 h-8 bg-indigo-100 border border-indigo-200 rounded-full">
                <span class="text-sm font-medium text-indigo-600">{{ getInitials(currentUser) }}</span>
              </div>
              <div class="flex-grow">
                <textarea
                  class="w-full p-3 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                  placeholder="Add your comment..."
                  rows="2"
                  v-model="task.newComment"
                ></textarea>
                <div class="flex justify-end mt-2">
                  <button
                    class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    @click="addComment(task)"
                  >
                    Post Comment
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Task Form (Accordion) -->
        <div v-if="task.showForm" class="p-4 border-t border-gray-200 bg-gray-50">
          <div class="max-w-3xl mx-auto">
            <h4 class="mb-4 font-medium text-gray-900">Update Task Status</h4>

            <!-- Document Upload Section -->
            <div class="mb-4">
              <label class="block mb-1 text-sm font-medium text-gray-700">Document Upload</label>
              <div class="flex flex-wrap items-center gap-2">
                <button
                  class="flex items-center px-3 py-2 text-sm text-gray-700 transition bg-white border border-gray-300 rounded hover:bg-gray-50"
                >
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                  </svg>
                  Upload Document
                </button>
                <div class="text-sm text-gray-500">
                  Supported formats: PDF, DOC, DOCX, JPEG, PNG (max 10MB)
                </div>
              </div>
            </div>

            <!-- Status Update -->
            <div class="mb-4">
              <label class="block mb-1 text-sm font-medium text-gray-700">Status Update</label>
              <select
                class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                v-model="task.updatedStatus"
              >
                <option value="">Select a status</option>
                <option value="completed">Mark as Completed</option>
                <option value="in_progress">In Progress</option>
                <option value="deferred">Defer Task</option>
              </select>
            </div>

            <!-- Comments -->
            <div class="mb-4">
              <label class="block mb-1 text-sm font-medium text-gray-700">Add Comments</label>
              <textarea
                class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                placeholder="Add any additional notes or comments about this task..."
                rows="3"
                v-model="task.statusComment"
              ></textarea>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end space-x-3">
              <button
                class="px-4 py-2 text-sm font-medium text-gray-800 bg-gray-200 rounded-md hover:bg-gray-300"
                @click="cancelTaskUpdate(task)"
              >
                Cancel
              </button>
              <button
                class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700"
                @click="submitTask(task)"
              >
                Update Task
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-if="filteredTasks.length === 0" class="flex flex-col items-center justify-center py-12 text-center bg-white rounded-lg shadow-sm">
        <svg class="w-16 h-16 mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
        </svg>
        <h3 class="mb-1 text-lg font-medium text-gray-900">No Tasks Found</h3>
        <p class="max-w-md mb-4 text-gray-500">
          {{ searchQuery ? 'No tasks match your search criteria.' : 'There are no pending tasks at the moment.' }}
        </p>
      </div>
    </div>

    <!-- In-progress Tasks Tab -->
    <div class="space-y-4" v-else-if="activeTab === 'in_progress'">
      <div
        v-for="(task, index) in filteredInProgressTasks"
        :key="`inprogress-${index}`"
        class="overflow-hidden transition-all duration-200 bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md"
      >
        <div class="p-4 border-l-4 border-blue-500">
          <div class="flex flex-col md:flex-row md:justify-between md:items-center">
            <div class="flex-grow">
              <h3 class="text-base font-semibold text-gray-900">{{ task.title }}</h3>
              <div class="mt-1 text-sm text-gray-600">Due Date: {{ task.dueDate }}</div>
            </div>
            <div class="flex items-center mt-3 md:mt-0">
              <div class="px-3 py-1.5 text-xs font-medium bg-blue-100 text-blue-800 rounded-md">
                In Progress
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- Empty State for In-progress Tasks -->
      <div v-if="filteredInProgressTasks.length === 0" class="flex flex-col items-center justify-center py-12 text-center bg-white rounded-lg shadow-sm">
        <svg class="w-16 h-16 mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
        </svg>
        <h3 class="mb-1 text-lg font-medium text-gray-900">No In-progress Tasks</h3>
        <p class="max-w-md mb-4 text-gray-500">
          {{ searchQuery ? 'No in-progress tasks match your search criteria.' : 'There are no in-progress tasks at the moment.' }}
        </p>
      </div>
    </div>

    <!-- Completed Tasks Tab -->
    <div class="space-y-4" v-else>
      <!-- Completed Task Items -->
      <div
        v-for="(task, index) in filteredCompletedTasks"
        :key="`completed-${index}`"
        class="overflow-hidden transition-all duration-200 bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md"
      >
        <div class="p-4 border-l-4 border-green-500">
          <div class="flex flex-col md:flex-row md:justify-between md:items-center">
            <div class="flex-grow">
              <h3 class="text-base font-semibold text-gray-900">{{ task.title }}</h3>
              <div class="mt-1 text-sm text-gray-600">Completed: {{ task.completedDate }}</div>
            </div>

            <div class="flex items-center mt-3 md:mt-0">
              <div class="px-3 py-1.5 text-xs font-medium bg-green-100 text-green-800 rounded-md">
                Completed
              </div>
            </div>
          </div>

          <!-- Additional info if available -->
          <div v-if="task.completedBy" class="mt-2 text-sm text-gray-500">
            Completed by: {{ task.completedBy }}
          </div>
        </div>
      </div>

      <!-- Empty State for Completed Tasks -->
      <div v-if="filteredCompletedTasks.length === 0" class="flex flex-col items-center justify-center py-12 text-center bg-white rounded-lg shadow-sm">
        <svg class="w-16 h-16 mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
        </svg>
        <h3 class="mb-1 text-lg font-medium text-gray-900">No Completed Tasks</h3>
        <p class="max-w-md mb-4 text-gray-500">
          {{ searchQuery ? 'No completed tasks match your search criteria.' : 'You haven\'t completed any tasks yet.' }}
        </p>
      </div>
    </div>
  </div>
</template>

<script>
export default {
    name: 'task',
    props: {
        page_data: {
            type: Object,
            default: () => ([])
        }
    },
    data() {
        return {
        activeTab: 'pending',
        searchQuery: '',
        currentUser: 'Current User',
        tasks: [
            {
            id: 1,
            title: 'Passport expired please update',
            dueDate: '05 Mar 2025',
            status: 'Task Overdue',
            statusType: 'overdue',
            hasDocument: false,
            document: null,
            showForm: false,
            showCommentsSection: false,
            showCommentField: false,
            newComment: '',
            statusComment: '',
            updatedStatus: '',
            comments: []
            },
            {
            id: 2,
            title: 'Invoice_63537 due for payment - pay now',
            dueDate: '01 April 2025',
            status: 'High Priority',
            statusType: 'high',
            hasDocument: true,
            document: null,
            showForm: false,
            showCommentsSection: false,
            showCommentField: false,
            newComment: '',
            statusComment: '',
            updatedStatus: '',
            comments: []
            },
            {
            id: 3,
            title: 'Upload your new utility bill - Feb 2025',
            dueDate: '05 April 2025',
            status: 'View Comments',
            statusType: 'comments',
            hasDocument: true,
            document: {
                name: 'Utility_Bill_Feb2025.pdf',
                type: 'pdf',
                size: '1.2 MB'
            },
            showForm: false,
            showCommentsSection: false,
            showCommentField: false,
            newComment: '',
            statusComment: '',
            updatedStatus: '',
            comments: [
                {
                author: 'H&C Administrator',
                text: 'Please upload a clearer copy of your utility bill. The one provided is too blurry to read the address details.',
                date: '2 days ago',
                avatar: null
                },
                {
                author: 'John Smith',
                text: 'I will upload a better scan of the document by tomorrow.',
                date: '1 day ago',
                avatar: null
                }
            ]
            },
            {
            id: 4,
            title: 'Sign Contract for Q2 Services',
            dueDate: '05 April 2025',
            status: 'View Comments',
            statusType: 'comments',
            hasDocument: true,
            document: {
                name: 'Service_Contract_Q2_2025.pdf',
                type: 'pdf',
                size: '3.5 MB'
            },
            showForm: false,
            showCommentsSection: false,
            showCommentField: false,
            newComment: '',
            statusComment: '',
            updatedStatus: '',
            comments: [
                {
                author: 'H&C Legal',
                text: 'The contract has been updated with the new terms as discussed. Please review and sign at your earliest convenience.',
                date: '3 days ago',
                avatar: null
                }
            ]
            },
            {
            id: 5,
            title: 'Utility bill expired please update',
            dueDate: '10 April 2025',
            status: 'Pending',
            statusType: 'normal',
            hasDocument: true,
            document: null,
            showForm: false,
            showCommentsSection: false,
            showCommentField: false,
            newComment: '',
            statusComment: '',
            updatedStatus: '',
            comments: []
            }
        ],
        completedTasks: [
            {
            id: 101,
            title: 'Annual tax filing',
            completedDate: '28 Feb 2025',
            completedBy: 'Sarah Johnson',
            document: {
                name: 'Tax_Filing_2024.pdf',
                type: 'pdf'
            }
            },
            {
            id: 102,
            title: 'Business license renewal',
            completedDate: '15 Jan 2025',
            completedBy: 'David Wilson',
            document: {
                name: 'Business_License_2025.pdf',
                type: 'pdf'
            }
            },
            {
            id: 103,
            title: 'Employee handbook updates',
            completedDate: '05 Jan 2025',
            completedBy: 'HR Department',
            document: {
                name: 'Employee_Handbook_2025.pdf',
                type: 'pdf'
            }
            }
        ]
        };
    },
  computed: {
    filteredTasks() {
      if (!this.searchQuery) {
        return this.tasks;
      }

      const query = this.searchQuery.toLowerCase().trim();
      return this.tasks.filter(task =>
        task.title.toLowerCase().includes(query) ||
        task.dueDate.toLowerCase().includes(query) ||
        task.status.toLowerCase().includes(query)
      );
    },
    filteredCompletedTasks() {
      if (!this.searchQuery) {
        return this.completedTasks;
      }

      const query = this.searchQuery.toLowerCase().trim();
      return this.completedTasks.filter(task =>
        task.title.toLowerCase().includes(query) ||
        task.completedDate.toLowerCase().includes(query) ||
        (task.completedBy && task.completedBy.toLowerCase().includes(query))
      );
    },
    filteredInProgressTasks() {
      if (!this.searchQuery) {
        return this.tasks.filter(task => task.status === 'In Progress');
      }
      const query = this.searchQuery.toLowerCase().trim();
      return this.tasks.filter(task =>
        task.status === 'In Progress' && (
          task.title.toLowerCase().includes(query) ||
          task.dueDate.toLowerCase().includes(query) ||
          task.status.toLowerCase().includes(query)
        )
      );
    }
  },
  methods: {
    getTaskBorderColor(task) {
      switch (task.statusType) {
        case 'overdue':
          return 'border-red-500';
        case 'high':
          return 'border-orange-500';
        case 'comments':
          return 'border-yellow-500';
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
    submitTask(task) {
      // Here you would handle the task submission logic to your backend

      // For demonstration, we'll simulate a successful submission
      if (task.updatedStatus === 'completed') {
        // Move task to completed
        const completedTask = {
          id: task.id,
          title: task.title,
          completedDate: new Date().toLocaleDateString('en-US', {
            day: '2-digit',
            month: 'short',
            year: 'numeric'
          }),
          completedBy: this.currentUser,
          document: task.document
        };

        this.completedTasks.unshift(completedTask);

        // Remove from pending tasks
        const taskIndex = this.tasks.findIndex(t => t.id === task.id);
        if (taskIndex !== -1) {
          this.tasks.splice(taskIndex, 1);
        }
      } else {
        // Update task status
        if (task.updatedStatus) {
          switch (task.updatedStatus) {
            case 'in_progress':
              task.status = 'In Progress';
              task.statusType = 'normal';
              break;
            case 'deferred':
              task.status = 'Deferred';
              task.statusType = 'comments';
              break;
          }
        }

        // Add comment if provided
        if (task.statusComment.trim()) {
          this.addSystemComment(task, task.statusComment);
        }
      }

      // Close the form
      task.showForm = false;
      task.statusComment = '';
      task.updatedStatus = '';

      // Show success message
      this.$nextTick(() => {
        // In a real implementation, you'd integrate with your notification system
        alert('Task has been updated successfully!');
      });
    },
    addComment(task) {
      if (!task.newComment.trim()) return;

      // Add the comment to the task
      task.comments.push({
        author: this.currentUser,
        text: task.newComment,
        date: 'Just now',
        avatar: null
      });

      // Clear the comment field
      task.newComment = '';
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
    }
  },
  mounted() {
    console.log('Admin Task Component Mounted', this.page_data, this.module);
    // Initialize any necessary data or fetch tasks from an API
    // For now, we are using hardcoded tasks in the data property
  }
};
</script>

<style scoped>
/* Task list animations */
.task-enter-active, .task-leave-active {
  transition: all 0.3s ease;
}
.task-enter, .task-leave-to {
  opacity: 0;
  transform: translateY(20px);
}

/* Comment section transitions */
.comment-section-enter-active, .comment-section-leave-active {
  transition: all 0.3s ease;
  max-height: 1000px;
  overflow: hidden;
}
.comment-section-enter, .comment-section-leave-to {
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

  .task-action-buttons > * {
    margin-top: 0.5rem;
  }
}
</style>
