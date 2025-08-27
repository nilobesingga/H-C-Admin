<template>
  <div class="w-full h-full">
    <div class="flex flex-col h-screen max-h-[calc(100vh-120px)]">
      <!-- Chat Header -->
      <div class="flex items-center p-4 mb-2">
        <h1 class="text-2xl font-bold">Quick Chat</h1>
      </div>

      <div class="flex flex-col h-full gap-4 md:flex-row">
        <!-- Previous Chats Section -->
        <div class="flex flex-col h-full p-4 mb-4 overflow-hidden bg-white rounded-lg shadow md:mb-0 md:w-1/4">
          <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-medium text-gray-700">Previous Chats</h2>
            <button class="flex items-center px-3 py-1 text-sm text-gray-600 bg-gray-100 rounded-md">
              <i class="mr-1 ki-duotone ki-archive"></i> Archived
            </button>
          </div>

          <div class="flex-grow overflow-y-auto">
            <!-- Today Section -->
            <div class="mb-4">
              <h3 class="mb-2 text-sm text-gray-500">Today</h3>
              <ul class="space-y-2">
                <li
                  v-for="(chat, index) in todayChats"
                  :key="'today-' + index"
                  class="p-2 rounded cursor-pointer hover:bg-gray-50"
                  @click="selectChat(chat)"
                >
                  {{ chat.title }}
                </li>
              </ul>
            </div>

            <!-- Yesterday Section -->
            <div class="mb-4">
              <h3 class="mb-2 text-sm text-gray-500">Yesterday</h3>
              <ul class="space-y-2">
                <li
                  v-for="(chat, index) in yesterdayChats"
                  :key="'yesterday-' + index"
                  class="p-2 rounded cursor-pointer hover:bg-gray-50"
                  @click="selectChat(chat)"
                >
                  {{ chat.title }}
                </li>
              </ul>
            </div>

            <!-- Previous 7 days Section -->
            <div>
              <h3 class="mb-2 text-sm text-gray-500">Previous 7 days</h3>
              <ul class="space-y-2">
                <li
                  v-for="(chat, index) in previousChats"
                  :key="'prev-' + index"
                  class="p-2 rounded cursor-pointer hover:bg-gray-50"
                  @click="selectChat(chat)"
                >
                  {{ chat.title }}
                </li>
              </ul>
            </div>
          </div>
        </div>

        <!-- Main Chat Window -->
        <div class="flex flex-col flex-1 h-full overflow-hidden bg-white rounded-lg shadow md:w-3/4">
          <!-- Chat Messages Area -->
          <div ref="messagesContainer" class="flex-1 p-4 overflow-y-auto">
            <div v-if="!selectedChat" class="flex flex-col items-center justify-center h-full">
              <h2 class="mb-8 text-xl font-semibold text-gray-700">How can we assist you today?</h2>

              <!-- Quick Action Buttons -->
              <div class="grid w-full max-w-5xl grid-cols-2 gap-4 mx-auto md:grid-cols-4">
                <a
                  v-for="(action, index) in quickActions"
                  :key="index"
                  :href="action.link"
                  class="flex items-center p-3 border border-gray-200 rounded-md hover:bg-gray-50"
                >
                  <i :class="[action.icon, 'text-gray-500 mr-2']"></i>
                  <span>{{ action.title }}</span>
                </a>
              </div>
            </div>

            <div v-else class="flex flex-col space-y-4">
              <div class="flex justify-center mb-4">
                <div class="px-3 py-1 text-xs text-gray-600 bg-gray-200 rounded-full">
                  {{ formatDate(selectedChat.date) }}
                </div>
              </div>

              <div v-for="(message, index) in selectedChat.messages" :key="index"
                   :class="['flex', message.isUser ? 'justify-end' : 'justify-start']">
                <div :class="['max-w-[80%] rounded-lg p-3',
                             message.isUser ? 'bg-blue-500 text-white rounded-br-none' : 'bg-gray-200 text-gray-800 rounded-bl-none']">
                  {{ message.content }}
                  <div :class="['text-xs mt-1', message.isUser ? 'text-blue-100' : 'text-gray-500']">
                    {{ formatTime(message.time) }}
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Chat Input -->
          <div class="p-4 border-t border-gray-200">
            <form @submit.prevent="sendMessage" class="flex items-end">
              <div class="flex-1 mr-3">
                <textarea
                  v-model="newMessage"
                  class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-300"
                  rows="2"
                  placeholder="Send a Quickchat..."
                  @keyup.enter.exact="sendMessage"
                ></textarea>
              </div>
              <div class="flex">
                <button type="button" @click="openFileSelector" class="p-3 mr-2 text-gray-500 rounded-md hover:bg-gray-100">
                  <i class="text-lg ki-duotone ki-paperclip"></i>
                  <span class="sr-only">Attach File</span>
                  <input ref="fileInput" type="file" class="hidden" @change="handleFileUpload" />
                </button>
                <button
                  type="submit"
                  class="flex items-center px-5 py-3 text-white bg-gray-800 rounded-md hover:bg-gray-700"
                  :disabled="!newMessage.trim()"
                >
                  Send
                  <i class="ml-2 ki-duotone ki-arrow-right"></i>
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'QuickChat',
  props: {
    page_data: Object,
    page_title: String
  },
  data() {
    return {
      newMessage: '',
      selectedChat: null,
      todayChats: [
        {
          id: 1,
          title: 'How to update my profile photo',
          date: new Date(),
          messages: [
            { content: 'How do I update my profile photo?', isUser: true, time: new Date(new Date().setHours(9, 15)) },
            { content: 'To update your profile photo, go to your account settings and click on the profile picture icon to upload a new image.', isUser: false, time: new Date(new Date().setHours(9, 17)) }
          ]
        },
        {
          id: 2,
          title: 'I can\'t find my Bank Statement',
          date: new Date(),
          messages: [
            { content: 'I can\'t find my bank statement from last month', isUser: true, time: new Date(new Date().setHours(11, 32)) },
            { content: 'Bank statements are available in the Wallet section. You can filter by date to find previous statements.', isUser: false, time: new Date(new Date().setHours(11, 35)) }
          ]
        },
        {
          id: 3,
          title: 'How to open a company?',
          date: new Date(),
          messages: [
            { content: 'I would like to know the procedure for opening a new company', isUser: true, time: new Date(new Date().setHours(14, 22)) },
            { content: 'To open a new company, you\'ll need to submit documentation including business plan, company structure, and owner identification. Would you like me to prepare a detailed guide?', isUser: false, time: new Date(new Date().setHours(14, 25)) }
          ]
        }
      ],
      yesterdayChats: [
        {
          id: 4,
          title: 'Why am I paying extra?',
          date: new Date(new Date().setDate(new Date().getDate() - 1)),
          messages: [
            { content: 'I noticed an extra charge on my latest invoice. Why am I paying more?', isUser: true, time: new Date(new Date().setDate(new Date().getDate() - 1)).setHours(10, 5) },
            { content: 'The additional charge is for the premium service tier you upgraded to last month. I can provide a detailed breakdown if you\'d like.', isUser: false, time: new Date(new Date().setDate(new Date().getDate() - 1)).setHours(10, 8) }
          ]
        },
        {
          id: 5,
          title: 'I cannot delete my task',
          date: new Date(new Date().setDate(new Date().getDate() - 1)),
          messages: [
            { content: 'I\'m trying to delete a completed task but the system won\'t let me', isUser: true, time: new Date(new Date().setDate(new Date().getDate() - 1)).setHours(13, 40) },
            { content: 'For compliance reasons, completed tasks cannot be deleted for 30 days. They can be archived instead from the task options menu.', isUser: false, time: new Date(new Date().setDate(new Date().getDate() - 1)).setHours(13, 45) }
          ]
        },
        {
          id: 6,
          title: 'How to update calendar?',
          date: new Date(new Date().setDate(new Date().getDate() - 1)),
          messages: [
            { content: 'Need help updating my calendar with recurring events', isUser: true, time: new Date(new Date().setDate(new Date().getDate() - 1)).setHours(16, 20) },
            { content: 'To set up recurring events, open the calendar, create a new event and check the "Repeat" option. You can then select the frequency and duration.', isUser: false, time: new Date(new Date().setDate(new Date().getDate() - 1)).setHours(16, 23) }
          ]
        }
      ],
      previousChats: [
        {
          id: 7,
          title: 'How to subscribe to new services',
          date: new Date(new Date().setDate(new Date().getDate() - 3)),
          messages: [
            { content: 'I want to subscribe to additional accounting services', isUser: true, time: new Date(new Date().setDate(new Date().getDate() - 3)).setHours(9, 30) },
            { content: 'You can subscribe to additional services from the "Other Services" section. Select the service package you need and follow the subscription prompts.', isUser: false, time: new Date(new Date().setDate(new Date().getDate() - 3)).setHours(9, 33) }
          ]
        },
        {
          id: 8,
          title: 'My account details are wrong',
          date: new Date(new Date().setDate(new Date().getDate() - 4)),
          messages: [
            { content: 'Some of my account details are incorrect and need updating', isUser: true, time: new Date(new Date().setDate(new Date().getDate() - 4)).setHours(11, 15) },
            { content: 'You can update your account details in the profile settings section. For some changes like legal name or registered address, you might need to provide supporting documentation.', isUser: false, time: new Date(new Date().setDate(new Date().getDate() - 4)).setHours(11, 18) }
          ]
        },
        {
          id: 9,
          title: 'I need to update my password',
          date: new Date(new Date().setDate(new Date().getDate() - 5)),
          messages: [
            { content: 'How do I change my password?', isUser: true, time: new Date(new Date().setDate(new Date().getDate() - 5)).setHours(14, 45) },
            { content: 'To update your password, go to your profile settings and select "Security". You\'ll need to enter your current password before setting a new one.', isUser: false, time: new Date(new Date().setDate(new Date().getDate() - 5)).setHours(14, 48) }
          ]
        },
        {
          id: 10,
          title: 'Why can\'t I open a new account',
          date: new Date(new Date().setDate(new Date().getDate() - 6)),
          messages: [
            { content: 'I\'m trying to open a new account but keep getting an error', isUser: true, time: new Date(new Date().setDate(new Date().getDate() - 6)).setHours(10, 20) },
            { content: 'There may be some requirements that need to be met first. Common issues include incomplete profile information or pending verification steps. Let me check your specific case.', isUser: false, time: new Date(new Date().setDate(new Date().getDate() - 6)).setHours(10, 23) }
          ]
        },
        {
          id: 11,
          title: 'I can\'t retrieve my old files',
          date: new Date(new Date().setDate(new Date().getDate() - 6)),
          messages: [
            { content: 'I need access to files from 2 years ago but can\'t find them', isUser: true, time: new Date(new Date().setDate(new Date().getDate() - 6)).setHours(16, 10) },
            { content: 'Documents older than 18 months are moved to our archive system. I can help you retrieve these files - please provide the approximate date range and file types you\'re looking for.', isUser: false, time: new Date(new Date().setDate(new Date().getDate() - 6)).setHours(16, 14) }
          ]
        },
        {
          id: 12,
          title: 'Where can I find the minutes...',
          date: new Date(new Date().setDate(new Date().getDate() - 7)),
          messages: [
            { content: 'Where can I find the minutes from last quarter\'s board meeting?', isUser: true, time: new Date(new Date().setDate(new Date().getDate() - 7)).setHours(9, 5) },
            { content: 'Board meeting minutes are stored in the Governance section under "Corporate Documents". You\'ll need appropriate permissions to access these. I can check your current access level.', isUser: false, time: new Date(new Date().setDate(new Date().getDate() - 7)).setHours(9, 9) }
          ]
        }
      ],
      quickActions: []
    };
  },
  created() {
    // Set up quick actions based on the current company
    const selectedCompany = this.page_data?.selected_company || '';
    this.quickActions = [
      {
        title: 'Manage Company',
        icon: 'ki-duotone ki-chart-simple',
        link: this.getRoute('company.index', { company: selectedCompany })
      },
      {
        title: 'Dashboard',
        icon: 'ki-duotone ki-element-11',
        link: this.getRoute('dashboard')
      },
      {
        title: 'Inbox',
        icon: 'ki-duotone ki-sms',
        link: this.getRoute('company.inbox', { company: selectedCompany })
      },
      {
        title: 'Payments',
        icon: 'ki-duotone ki-credit-cart',
        link: this.getRoute('company.payment', { company: selectedCompany })
      },
      {
        title: 'Wallet',
        icon: 'ki-duotone ki-wallet',
        link: this.getRoute('company.wallet', { company: selectedCompany })
      },
      {
        title: 'Calendar',
        icon: 'ki-duotone ki-calendar',
        link: this.getRoute('company.calendar', { company: selectedCompany })
      },
      {
        title: 'Accounting Portal',
        icon: 'ki-duotone ki-file-up',
        link: 'https://cresco.accountants/',
        external: true
      },
      {
        title: 'Other Services',
        icon: 'ki-duotone ki-info',
        link: '#'
      }
    ];
  },
  methods: {
    goBack() {
      window.history.back();
    },
    selectChat(chat) {
      this.selectedChat = chat;
      this.$nextTick(() => {
        this.scrollToBottom();
      });
    },
    formatDate(date) {
      if (!date) return '';
      const now = new Date();
      const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
      const yesterday = new Date(today);
      yesterday.setDate(yesterday.getDate() - 1);

      if (date.setHours(0,0,0,0) === today.setHours(0,0,0,0)) {
        return 'Today';
      } else if (date.setHours(0,0,0,0) === yesterday.setHours(0,0,0,0)) {
        return 'Yesterday';
      } else {
        return date.toLocaleDateString('en-US', { weekday: 'long', month: 'short', day: 'numeric' });
      }
    },
    formatTime(time) {
      return time.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
    },
    sendMessage() {
      if (!this.newMessage.trim()) return;

      // Create a new chat or add to existing one
      const now = new Date();

      if (!this.selectedChat) {
        // Create a new chat
        const newChat = {
          id: this.todayChats.length + this.yesterdayChats.length + this.previousChats.length + 1,
          title: this.newMessage.length > 30 ? this.newMessage.substring(0, 27) + '...' : this.newMessage,
          date: now,
          messages: [
            { content: this.newMessage, isUser: true, time: now }
          ]
        };

        // Add to today's chats
        this.todayChats.unshift(newChat);
        this.selectedChat = newChat;

        // In a real app, we would wait for a response from the server
        // Simulate a response from the support staff
        setTimeout(() => {
          this.selectedChat.messages.push({
            content: "Thank you for your message. Our team will respond shortly.",
            isUser: false,
            time: new Date()
          });
          this.scrollToBottom();
        }, 1000);
      } else {
        // Add to existing chat
        this.selectedChat.messages.push({
          content: this.newMessage,
          isUser: true,
          time: now
        });

        // Simulate a response
        setTimeout(() => {
          this.selectedChat.messages.push({
            content: "I've noted your follow-up. Our team will address this as soon as possible.",
            isUser: false,
            time: new Date()
          });
          this.scrollToBottom();
        }, 1000);
      }

      this.newMessage = '';
      this.$nextTick(() => {
        this.scrollToBottom();
      });
    },
    scrollToBottom() {
      if (this.$refs.messagesContainer) {
        this.$refs.messagesContainer.scrollTop = this.$refs.messagesContainer.scrollHeight;
      }
    },
    openFileSelector() {
      this.$refs.fileInput.click();
    },
    handleFileUpload(event) {
      const file = event.target.files[0];
      if (!file) return;

      // In a real application, you would upload this file to your server
      // For now, we'll just show a message with the file name

      if (!this.selectedChat) {
        // Create a new chat for the file
        const newChat = {
          id: this.todayChats.length + this.yesterdayChats.length + this.previousChats.length + 1,
          title: `File: ${file.name}`,
          date: new Date(),
          messages: [
            { content: `I'm sending a file: ${file.name}`, isUser: true, time: new Date() }
          ]
        };

        this.todayChats.unshift(newChat);
        this.selectedChat = newChat;
      } else {
        // Add file message to existing chat
        this.selectedChat.messages.push({
          content: `I'm sending a file: ${file.name}`,
          isUser: true,
          time: new Date()
        });
      }

      // Reset the file input
      this.$refs.fileInput.value = '';

      // Simulate response
      setTimeout(() => {
        this.selectedChat.messages.push({
          content: `Thank you for uploading ${file.name}. We'll process this file shortly.`,
          isUser: false,
          time: new Date()
        });
        this.scrollToBottom();
      }, 1000);

      this.$nextTick(() => {
        this.scrollToBottom();
      });
    },
    getRoute(name, params = {}) {
      // This is a simple implementation - in a real app you might use Laravel's route() helper
      if (name === 'dashboard') {
        return '/dashboard';
      }

      if (name.startsWith('company.')) {
        const route = name.split('.')[1];
        const company = params.company || this.page_data?.selected_company || '';
        return `/company/${route}/${company}`;
      }

      return '#';
    }
  }
}
</script>

<style scoped>
.max-h-\[calc\(100vh-120px\)\] {
  max-height: calc(100vh - 120px);
}
</style>
