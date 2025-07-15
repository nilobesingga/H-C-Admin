<template>
  <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40">
    <div class="relative w-full max-w-2xl mx-4 bg-white rounded-lg shadow-lg animate-fadeIn">
      <!-- Header -->
      <div class="flex items-center justify-between px-6 py-4 border-b">
        <h2 class="text-xl font-bold text-gray-900">Add Task</h2>
        <button @click="$emit('close')" class="text-gray-400 hover:text-gray-600">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
      <!-- Body -->
      <div class="px-6 py-4 space-y-6">
        <!-- Task Title -->
        <input type="text" v-model="task.title" placeholder="Things to do" class="w-full px-3 py-2 text-sm font-semibold border rounded focus:outline-none focus:ring-2 focus:ring-blue-400" />
        <!-- Description -->
        <textarea v-model="task.description" rows="3" placeholder="Description..." class="w-full px-3 py-2 text-sm border rounded focus:outline-none focus:ring-2 focus:ring-blue-400"></textarea>
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
        <!-- Responsible Person Multi-select -->
        <div class="form-group">
          <div class="flex items-center justify-between mb-1">
            <label class="block text-xs text-black">Responsible person</label>
            <div class="flex gap-2">
              <button type="button" class="px-2 py-1 text-xs font-semibold text-blue-700 rounded bg-blue-50 hover:bg-blue-100" @click="showParticipants = !showParticipants">
                Participants
              </button>
              <button type="button" class="px-2 py-1 text-xs font-semibold text-blue-700 rounded bg-blue-50 hover:bg-blue-100" @click="showObservers = !showObservers">
                Observers
              </button>
            </div>
          </div>
          <div class="flex flex-wrap gap-2 p-2 mb-2 bg-white border rounded">
            <span v-for="user in selectedResponsible" :key="user.id" class="inline-flex items-center px-3 py-1 text-sm font-semibold text-white bg-blue-500 rounded">
              {{ user.name }}
              <button type="button" class="ml-2 text-white hover:text-gray-200 focus:outline-none" @click="removeResponsible(user)">
                <span aria-hidden="true">&times;</span>
              </button>
            </span>
            <div class="relative flex-1 min-w-[150px]">
              <input
                v-model="responsibleSearch"
                @focus="responsibleDropdownOpen = true"
                @input="responsibleDropdownOpen = true"
                @blur="() => setTimeout(() => responsibleDropdownOpen = false, 150)"
                type="text"
                class="w-full px-2 py-1 text-sm border-none focus:ring-0"
                placeholder="Add responsible person..."
              />
              <ul v-if="responsibleDropdownOpen && filteredResponsible.length" class="absolute left-0 right-0 z-10 mt-1 overflow-auto bg-white border rounded shadow max-h-40">
                <li v-for="user in filteredResponsible" :key="user.id" class="px-3 py-2 cursor-pointer hover:bg-blue-100" @mousedown.prevent="addResponsible(user)">
                  {{ user.name }}
                </li>
              </ul>
            </div>
          </div>
        </div>
        <!-- Participants Multi-select -->
        <div v-if="showParticipants" class="mt-2">
          <div class="form-group">
            <label class="block mb-1 text-xs text-black">Participants</label>
            <div class="flex flex-wrap gap-2 p-2 mb-2 bg-white border rounded">
              <span v-for="user in selectedParticipants" :key="user.id" class="inline-flex items-center px-3 py-1 text-sm font-semibold text-white bg-blue-500 rounded">
                {{ user.name }}
                <button type="button" class="ml-2 text-white hover:text-gray-200 focus:outline-none" @click="removeParticipant(user)">
                  <span aria-hidden="true">&times;</span>
                </button>
              </span>
              <div class="relative flex-1 min-w-[150px]">
                <input
                  v-model="participantSearch"
                  @focus="participantDropdownOpen = true"
                  @input="participantDropdownOpen = true"
                  @blur="() => setTimeout(() => participantDropdownOpen = false, 150)"
                  type="text"
                  class="w-full px-2 py-1 text-sm border-none focus:ring-0"
                  placeholder="Add participant..."
                />
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
        <div v-if="showObservers" class="mt-2">
          <div class="mt-4 form-group">
            <label class="block mb-1 text-xs text-black">Observers</label>
            <div class="flex flex-wrap gap-2 p-2 mb-2 bg-white border rounded">
              <span v-for="user in selectedObservers" :key="user.id" class="inline-flex items-center px-3 py-1 text-sm font-semibold text-white bg-blue-500 rounded">
                {{ user.name }}
                <button type="button" class="ml-2 text-white hover:text-gray-200 focus:outline-none" @click="removeObserver(user)">
                  <span aria-hidden="true">&times;</span>
                </button>
              </span>
              <div class="relative flex-1 min-w-[150px]">
                <input
                  v-model="observerSearch"
                  @focus="observerDropdownOpen = true"
                  @input="observerDropdownOpen = true"
                  @blur="() => setTimeout(() => observerDropdownOpen = false, 150)"
                  type="text"
                  class="w-full px-2 py-1 text-sm border-none focus:ring-0"
                  placeholder="Add observer..."
                />
                <ul v-if="observerDropdownOpen && filteredObservers.length" class="absolute left-0 right-0 z-10 mt-1 overflow-auto bg-white border rounded shadow max-h-40">
                  <li v-for="user in filteredObservers" :key="user.id" class="px-3 py-2 cursor-pointer hover:bg-blue-100" @mousedown.prevent="addObserver(user)">
                    {{ user.name }}
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </div>
        <!-- Deadline and Planning -->
        <div class="w-full">
          <div>
            <label class="block mb-1 text-xs text-black">Deadline</label>
            <input type="date" v-model="task.deadline" class="w-full px-2 py-1 border rounded form-input focus:outline-none focus:ring-2 focus:ring-blue-400" />
          </div>
        </div>
      </div>
      <!-- Footer -->
      <div class="flex items-center justify-end gap-3 px-6 py-4 border-t">
        <button @click="$emit('close')" class="px-5 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded hover:bg-gray-200">Cancel</button>
        <button @click="submitTask" class="px-5 py-2 text-sm font-medium text-white bg-blue-600 rounded hover:bg-blue-700">Send</button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'TaskModal',
  props: {
    show: { type: Boolean, default: false }
  },
  data() {
    return {
      task: {
        title: '',
        description: '',
        checklist: false,
        deadline: '',
        timePlanning: false,
        timeTracking: false,
        repeat: false,
        files: [],
        participants: [],
        observers: []
      },
      showParticipants: false,
      showObservers: false,
      selectedParticipant: '',
      selectedObserver: '',
      users: [
        { id: 1, name: 'Nilo Besingga' },
        { id: 2, name: 'Jane Doe' },
        { id: 3, name: 'John Smith' },
        { id: 4, name: 'Sarah Johnson' },
        { id: 5, name: 'Christopher Rebayla' },
      ],
      selectedParticipants: [],
      selectedObservers: [],
      selectedResponsible: [],
      participantToAdd: '',
      observerToAdd: '',
      participantSearch: '',
      observerSearch: '',
      responsibleSearch: '',
      participantDropdownOpen: false,
      observerDropdownOpen: false,
      responsibleDropdownOpen: false,
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
    availableParticipants() {
      return this.users.filter(u => !this.selectedParticipants.some(s => s.id === u.id));
    },
    availableObservers() {
      return this.users.filter(u => !this.selectedObservers.some(s => s.id === u.id));
    },
  },
  methods: {
    handleFileUpload(e) {
      const files = Array.from(e.target.files);
      if (files.length) {
        this.task.files = files;
      }
    },
    removeFile(idx) {
      this.task.files.splice(idx, 1);
    },
    submitTask() {
      // Emit the task data to parent
      this.$emit('submit', this.task);
      this.$emit('close');
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
