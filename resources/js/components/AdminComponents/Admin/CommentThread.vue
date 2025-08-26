<template>
    <div class="space-y-3">
        <div :class="['flex space-x-3', {'ml-6': level > 0}]">
            <!-- Avatar -->
            <div :class="['flex items-center justify-center flex-shrink-0 overflow-hidden bg-gray-200 border border-gray-300 rounded-full',
                level === 0 ? 'w-8 h-8' : 'w-6 h-6']">
                <span v-if="!comment.avatar" :class="['font-medium text-gray-600', level === 0 ? 'text-sm' : 'text-xs']">
                    {{ getInitials(comment.author ? comment.author.author_name : '') }}
                </span>
                <img v-else :src="comment.avatar" class="object-cover w-full h-full" />
            </div>

            <!-- Comment Content -->
            <div class="flex-grow">
                <div :class="['p-3 border border-gray-200 rounded-lg shadow-sm', level === 0 ? 'bg-white' : 'bg-gray-50']">
                    <div class="flex items-start justify-between">
                        <span class="text-sm font-medium text-gray-900">{{ comment.author ? comment.author.author_name : "Unknown" }}</span>
                        <span class="text-xs text-gray-500">{{ formatDate(comment.created_at || (comment.author ? comment.author.created_at : '')) }}</span>
                    </div>
                    <p class="mt-1 text-sm text-gray-800">{{ comment.message }}</p>
                    <!-- Attached Documents -->
                    <div v-if="comment.files.length" class="mt-4 space-y-2">
                        <div v-for="(file, fileIndex) in comment.files" :key="fileIndex" class="flex items-center p-2 rounded-md bg-gray-50">
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
                    <!-- Comment Actions -->
                    <div class="flex items-center mt-2 space-x-4">
                        <button @click="toggleLike" class="flex items-center text-sm text-gray-500 hover:text-indigo-600">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" :class="['w-4 h-4 mr-1', comment.isLiked ? 'text-indigo-600 fill-current' : 'text-gray-400']">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.633 10.25c.806 0 1.533-.446 2.031-1.08a9.041 9.041 0 0 1 2.861-2.4c.723-.384 1.35-.956 1.653-1.715a4.498 4.498 0 0 0 .322-1.672V2.75a.75.75 0 0 1 .75-.75 2.25 2.25 0 0 1 2.25 2.25c0 1.152-.26 2.243-.723 3.218-.266.558.107 1.282.725 1.282m0 0h3.126c1.026 0 1.945.694 2.054 1.715.045.422.068.85.068 1.285a11.95 11.95 0 0 1-2.649 7.521c-.388.482-.987.729-1.605.729H13.48c-.483 0-.964-.078-1.423-.23l-3.114-1.04a4.501 4.501 0 0 0-1.423-.23H5.904m10.598-9.75H14.25M5.904 18.5c.083.205.173.405.27.602.197.4-.078.898-.523.898h-.908c-.889 0-1.713-.518-1.972-1.368a12 12 0 0 1-.521-3.507c0-1.553.295-3.036.831-4.398C3.387 9.953 4.167 9.5 5 9.5h1.053c.472 0 .745.556.5.96a8.958 8.958 0 0 0-1.302 4.665c0 1.194.232 2.333.654 3.375Z" />
                            </svg>

                            {{ comment.likes || 0 }} Likes
                        </button>
                        <button @click="toggleReplyForm" class="flex items-center text-sm text-gray-500 hover:text-indigo-600">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                            </svg>
                            Reply
                        </button>
                    </div>

                    <!-- Reply Form -->
                    <div v-if="showReplyForm" class="mt-3">
                        <div class="flex space-x-3">
                            <div class="flex items-center justify-center flex-shrink-0 w-6 h-6 bg-indigo-100 border border-indigo-200 rounded-full">
                                <span class="text-xs font-medium text-indigo-600">{{ getInitials(currentUser) }}</span>
                            </div>
                            <div class="flex-grow">
                                <div class="relative">
                                    <textarea
                                        v-model="replyText"
                                        class="w-full p-2 pb-8 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                        placeholder="Write a reply..."
                                        rows="2"
                                    ></textarea>
                                    <div class="absolute bottom-2 left-2">
                                        <input
                                            type="file"
                                            ref="fileInput"
                                            @change="handleFileChange"
                                            class="hidden"
                                            multiple
                                        />
                                        <button
                                            @click="$refs.fileInput.click()"
                                            class="flex items-center my-2 space-x-1 text-sm text-gray-400 hover:text-gray-600 focus:outline-none"
                                            type="button"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 01-6.364-6.364l10.94-10.94A3 3 0 1119.5 7.372L8.552 18.32m.009-.01l-.01.01m5.699-9.941l-7.81 7.81a1.5 1.5 0 002.112 2.13" />
                                            </svg>
                                            Attach Files
                                        </button>
                                    </div>
                                </div>
                                <div v-if="selectedFiles.length > 0" class="flex flex-wrap gap-2 mt-2">
                                    <div v-for="(file, index) in selectedFiles" :key="index"
                                        class="flex items-center px-2 py-1 bg-gray-100 rounded-md">
                                        <span class="text-xs text-gray-600 truncate max-w-[150px]">{{ file.name }}</span>
                                        <button @click="removeFile(index)" class="ml-1 text-gray-500 hover:text-red-500">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                                <div class="flex justify-end mt-2 space-x-2">
                                    <button @click="cancelReply" class="px-3 py-1 text-xs text-gray-600 bg-gray-100 rounded-md hover:bg-gray-200">
                                        Cancel
                                    </button>
                                    <button @click="submitReply" class="px-3 py-1 text-xs text-white bg-indigo-600 rounded-md hover:bg-indigo-700">
                                        Reply
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Nested Replies -->
                    <div v-if="comment.replies && comment.replies.length > 0" class="pt-2 mt-3 space-y-3">
                        <comment-thread
                            v-for="reply in comment.replies"
                            :key="reply.id"
                            :comment="reply"
                            :task="task"
                            :current-user="currentUser"
                            :level="level + 1"
                            @reply-added="handleReplyAdded"
                        />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    name: 'CommentThread',
    props: {
        comment: {
            type: Object,
            required: true
        },
        task: {
            type: Object,
            required: true
        },
        currentUser: {
            type: String,
            required: true
        },
        level: {
            type: Number,
            default: 0
        }
    },
    data() {
        return {
            showReplyForm: false,
            replyText: '',
            selectedFiles: []
        };
    },
    methods: {
        formatDate(dateString) {
            if (!dateString) return '';
            const date = new Date(dateString);
            return date.toLocaleString('en-US', {
                month: 'short',
                day: 'numeric',
                year: 'numeric'
            });
        },
        getInitials(name) {
            if (!name) return '';
            return name.split(' ')
                .map(word => word.charAt(0).toUpperCase())
                .join('')
                .substring(0, 2);
        },
        toggleReplyForm() {
            this.showReplyForm = !this.showReplyForm;
            if (!this.showReplyForm) {
                this.replyText = '';
            }
        },
        cancelReply() {
            this.showReplyForm = false;
            this.replyText = '';
            this.selectedFiles = [];
        },
        handleFileChange(event) {
            const files = Array.from(event.target.files);
            this.selectedFiles.push(...files);
        },
        removeFile(index) {
            this.selectedFiles.splice(index, 1);
        },
        async submitReply() {
            if (!this.replyText.trim() && this.selectedFiles.length === 0) return;

            try {
                const formData = new FormData();
                formData.append('message', this.replyText);
                formData.append('parent_id', this.comment.id);
                // Append files if any
                if (this.selectedFiles && this.selectedFiles.length > 0) {
                    this.selectedFiles.forEach((file, index) => {
                        formData.append(`files[${index}]`, file);
                    });
                }

                const response = await axios.post(`/api/tasks/comments/${this.task.id}`, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });

                if (response.data.status === 'success') {
                    // Initialize replies array if it doesn't exist
                    if (!this.comment.replies) {
                        this.comment.replies = [];
                    }

                    // Create new reply
                    const newReply = {
                        id: response.data.id,
                        parent_id: this.comment.id,
                        author: {
                            author_name: this.currentUser,
                            created_at: new Date().toISOString()
                        },
                        message: this.replyText,
                        created_at: new Date().toISOString(),
                        files: this.selectedFiles.map(file => ({
                            file_name: file.name,
                            file_path: URL.createObjectURL(file) // Use a temporary URL for display
                        })),
                        replies: [],
                        likes: 0,
                        isLiked: false
                    };

                    // Add to replies array
                    this.comment.replies.push(newReply);

                    // Clear form and attachments
                    this.replyText = '';
                    this.selectedFiles = [];
                    this.showReplyForm = false;

                    // Emit event
                    this.$emit('reply-added', newReply);

                    this.successToast('Reply added successfully!');
                } else {
                    this.errorToast('Failed to add reply: ' + response.data.message);
                }
            } catch (error) {
                console.error('Error adding reply:', error);
                this.errorToast('Error adding reply');
            }
        },
        async toggleLike() {
            try {
                // Initialize likes if not present
                if (!this.comment.hasOwnProperty('likes')) {
                    this.comment.likes = 0;
                    this.comment.isLiked = false;
                }

                const formData = new FormData();
                formData.append('comment_id', this.comment.id);
                formData.append('user_id', this.task.page_data.user.id);

                const response = await axios.post(`/api/tasks/comments/like/${this.comment.id}`, formData);

                if (response.data.status === 'success') {
                    this.comment.isLiked = !this.comment.isLiked;
                    this.comment.likes += this.comment.isLiked ? 1 : -1;
                    this.successToast(this.comment.isLiked ? 'Comment liked!' : 'Comment unliked!');
                } else {
                    this.errorToast('Failed to update like status: ' + response.data.message);
                }
            } catch (error) {
                console.error('Error updating like:', error);
                this.errorToast('Error updating like status');
            }
        },
        handleReplyAdded(reply) {
            this.$emit('reply-added', reply);
        }
    }
};
</script>
