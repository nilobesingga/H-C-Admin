<template>
    <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40">
        <div class="relative w-full mx-4 bg-white rounded-lg shadow-lg max-w-7xl animate-fadeIn">
            <!-- Body -->
            <div class="flex flex-col md:flex-row">
                <!-- Left side: Task details -->
                <div class="flex-1 p-6 border-r">
                    <div class="mb-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="mb-2 text-lg font-semibold">{{ task.title }}</h3>
                            <button class="flex items-center px-4 py-2 space-x-1 text-sm font-normal text-gray-700 bg-gray-200 rounded-md hover:bg-red-800 hover:text-white">
                                <span>Set Reminder</span>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                                </svg>
                            </button>
                        </div>
                        <div>
                            <p>{{ task.description }}</p>
                        </div>
                        <!-- Attached Documents -->
                        <div v-if="task.files" class="mt-4 space-y-2">
                            <div v-for="(file, fileIndex) in task.files" :key="fileIndex" class="flex items-center p-2 rounded-md bg-gray-50">
                                <div class="flex items-center justify-center flex-shrink-0 w-8 h-8 mr-2 bg-gray-200 rounded">
                                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <div class="flex-grow">
                                    <div class="text-sm font-medium">{{ file.file_name }}</div>
                                    <div class="text-xs text-gray-500">Click to view</div>
                                </div>
                                <button @click="removeTaskFile(task, fileIndex)" class="text-gray-400 hover:text-gray-600" title="Remove file">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                    <hr class="my-6 border-gray-200"/>
                    <!-- Comments Section -->
                    <div class="mt-6">
                        <h3 class="mb-4 font-semibold text-gray-700">Comments</h3>
                        <div class="space-y-4 max-h-[300px] overflow-y-auto pr-2 custom-scrollbar">
                            <!-- Comment 1 -->
                            <comment-thread
                                v-for="(comment, i) in task.comments"
                                :key="`comment-${i}`"
                                :comment="comment"
                                :task="task"
                                :current-user="currentUser"
                                :level="0"
                                @reply-added="handleCommentReplyAdded"
                            />
                        </div>
                        <div class="pt-3 mt-4 border-t border-gray-200" v-if="task.status !== 'completed'">
                            <div class="flex space-x-3">
                                <div class="flex items-center justify-center flex-shrink-0 w-8 h-8 text-white bg-blue-600 border border-indigo-200 rounded-full">
                                    <span class="text-sm font-medium">{{ getInitials(currentUser) }}</span>
                                </div>
                                <div class="flex-grow">
                                    <div class="relative">
                                        <textarea
                                            class="w-full p-3 pb-10 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                            placeholder="Add your comment..."
                                            rows="2"
                                            v-model="task.newComment"
                                        ></textarea>
                                        <div class="absolute bottom-2 left-2">
                                            <input
                                                type="file"
                                                :ref="'fileInput_' + task.id"
                                                @change="handleCommentFileChange($event, task)"
                                                class="hidden"
                                                multiple
                                            />
                                            <button
                                                @click="$refs['fileInput_' + task.id].click()"
                                                class="flex items-center space-x-1 text-sm text-gray-400 hover:text-gray-600 focus:outline-none"
                                                type="button"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 01-6.364-6.364l10.94-10.94A3 3 0 1119.5 7.372L8.552 18.32m.009-.01l-.01.01m5.699-9.941l-7.81 7.81a1.5 1.5 0 002.112 2.13" />
                                                </svg>
                                                <span>Attach Files</span>
                                            </button>
                                        </div>
                                    </div>
                                    <div v-if="task.selectedFiles && task.selectedFiles.length > 0" class="flex flex-wrap gap-2 mt-2">
                                        <div v-for="(file, index) in task.selectedFiles" :key="index"
                                            class="flex items-center px-2 py-1 bg-gray-100 rounded-md">
                                            <span class="text-xs text-gray-600 truncate max-w-[150px]">{{ file.name }}</span>
                                            <button @click="removeCommentFile(task, index)" class="ml-1 text-gray-500 hover:text-red-500">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="flex justify-end mt-2">
                                        <button class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" @click="addComment(task)">
                                            Post Comment
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right side: Task information -->
                <div class="w-full border-r rounded md:w-1/3 bg-gray-50">
                        <div class="flex items-center justify-end mt-2 mr-2">
                            <button @click="$emit('close')" class="p-1 text-gray-400 bg-orange-800 rounded-full hover:text-gray-600">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    <div class="p-6 mb-6">
                        <div class="px-3 py-2 mb-4 text-center rounded-md"
                        :class="{
                                    'bg-[#FFF2DE] text-orange-400': task.status === 'open',
                                    'bg-blue-700 text-white': task.status === 'pending',
                                    'bg-green-700 text-white': task.status === 'completed'
                                }"
                        >
                            <span class="font-medium capitalize"

                            >{{ task.status }}</span>
                        </div>

                        <div class="space-y-4">
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-500">Task No.:</span>
                                <span class="text-sm font-medium">{{ task.id }}</span>
                            </div>

                            <div class="flex justify-between">
                                <span class="text-sm text-gray-500">Created on:</span>
                                <span class="text-sm font-medium">{{ formatDate(task.created_at) }}</span>
                            </div>

                            <div class="flex justify-between">
                                <span class="text-sm text-gray-500">Last Modified:</span>
                                <span class="text-sm font-medium">{{ formatDate(task.updated_at) }}</span>
                            </div>

                            <div class="flex justify-between">
                                <span class="text-sm text-gray-500">Due date:</span>
                                <span class="text-sm font-medium">{{ formatDate(task.deadline) }}</span>
                            </div>

                            <div class="pt-4 border-t">
                                <h4 class="mb-2 text-sm text-gray-500">Company</h4>
                                <div class="flex items-center space-x-2" v-for="(company, userIndex) in task.companies" :key="userIndex">
                                    <div class="flex items-center justify-center flex-shrink-0 w-8 h-8 text-white bg-orange-300 rounded-full">
                                        <span class="text-xs font-medium">{{ getInitials((company) ? company.name : 'NA') }}</span>
                                    </div>
                                    <span class="text-sm font-medium">{{ (company) ? company.name : 'NA'}}</span>
                                </div>
                            </div>

                            <div class="pt-4 border-t">
                                <h4 class="mb-2 text-sm text-gray-500">Responsible Person</h4>
                                <div class="flex items-center space-x-2" v-for="(user, userIndex) in task.responsible" :key="userIndex">
                                    <div v-if="user.pivot.type == 'responsible'" class="flex items-center justify-center flex-shrink-0 w-8 h-8 text-white bg-blue-600 rounded-full">
                                        <span class="text-xs font-medium">{{ getInitials((user.userprofile) ? user.userprofile.name : 'NA') }}</span>
                                    </div>
                                    <span class="text-sm font-medium">{{ (user.userprofile) ? user.userprofile.name : 'NA' }}</span>
                                </div>
                            </div>

                            <div class="pt-4 border-t">
                                <h4 class="mb-2 text-sm text-gray-500">Paticipants</h4>
                                <div class="flex items-center space-x-2" v-for="(user, userIndex) in task.participants" :key="userIndex">
                                    <div v-if="user.pivot.type == 'participant'" class="flex items-center justify-center flex-shrink-0 w-8 h-8 text-white bg-green-600 rounded-full">
                                        <span class="text-xs font-medium">{{ getInitials((user.userprofile) ? user.userprofile.name : 'NA') }}</span>
                                    </div>
                                    <span class="text-sm font-medium">{{ (user.userprofile) ? user.userprofile.name : 'NA' }}</span>
                                </div>
                            </div>

                            <div class="pt-4 border-t">
                                <h4 class="mb-2 text-sm text-gray-500">H&C Observers</h4>
                                <div v-if="task.observers && task.observers.length" class="space-y-2">
                                    <div v-for="(user, userIndex) in task.observers" :key="userIndex" class="flex items-center space-x-2">
                                        <div class="flex items-center justify-center flex-shrink-0 w-8 h-8 text-white bg-purple-600 rounded-full">
                                            <span class="text-xs font-medium">{{ getInitials((user.userprofile) ? user.userprofile.name : 'NA') }}</span>
                                        </div>
                                        <span class="text-sm font-medium">{{ (user.userprofile) ? user.userprofile.name : 'NA' }}</span>
                                    </div>
                                </div>
                                <div v-else class="text-sm text-gray-500">No observers</div>
                            </div>
                        </div>

                         <button v-if="task.status !== 'completed'"
                        @click="markAsComplete(task)"
                        class="w-full px-4 py-2 mt-6 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                        Mark as complete
                    </button>
                     <!-- <button
                       @click="$emit('close')"
                        class="w-full px-4 py-2 mt-6 text-sm font-medium text-white bg-orange-600 rounded-md hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-500"
                    >
                        Close
                    </button> -->
                    </div>


                </div>
            </div>
        </div>
    </div>
</template>

<script>
import CommentThread from '../CommentThread.vue';
export default {
    name: 'TaskDetailedModal',
    components: {
        CommentThread
    },
    props: {
        show: {
            type: Boolean,
            default: false
        },
        taskId: {
            type: [Number, String],
            default: null
        },
        page_data: {
            type: Object,
            default: () => ([])
        }
    },
    data() {
        return {
            currentUser: this.page_data.user.userprofile.name || 'Current User',
            task: {},
            newComment: ''
        };
    },
    methods: {
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
        fetchTaskDetails() {
            // This would make an API call to get the task details based on taskId
            // For now, we're using the default data
            if (this.taskId) {
                // Example API call:
                axios.get(`/api/tasks/${this.taskId}`)
                   .then(response => {
                       this.task = response.data;
                       console.log('Fetching task details for ID:', this.task);
                   })
                   .catch(error => {
                       console.error('Error fetching task details:', error);
                   });
            }
        },
        markAsComplete(task) {
            // Mark the task as complete
            task.status = 'completed';
            task.newComment = 'Completed';
            this.addComment(task);
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
        completeTask() {
            // In a real implementation, you would send this to your backend
            // Example API call:
            // axios.patch(`/api/tasks/${this.task.id}`, { status: 'completed' })
            //    .then(response => {
            //        this.$emit('update', response.data);
            //        this.$emit('close');
            //    })
            //    .catch(error => {
            //        console.error('Error completing task:', error);
            //    });

            // For now, just emit events
            this.$emit('update', { ...this.task, status: 'completed' });
            this.$emit('close');
        },
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
    watch: {
        show(newVal) {
            if (newVal && this.taskId) {
                this.fetchTaskDetails();
            }
        }
    },
    mounted() {
        if (this.show && this.taskId) {
            this.fetchTaskDetails();
        }
    }
};
</script>

<style scoped>
.animate-fadeIn {
    animation: fadeIn 0.3s ease-out forwards;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
