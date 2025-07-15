<template>
  <div class="w-full min-h-screen p-6">
    <!-- Header -->
    <div class="flex flex-col justify-between mb-6 md:flex-row md:items-center">
      <h1 class="mb-4 text-3xl font-extrabold text-gray-900 md:mb-0">{{ page_title || 'Calendar' }}</h1>
      <div class="flex flex-col space-y-3 sm:flex-row sm:space-y-0 sm:space-x-3">
        <div class="relative">
          <input
            type="text"
            v-model="searchQuery"
            placeholder="Search events..."
            class="px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
          />
          <svg class="absolute right-3 top-2.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
          </svg>
        </div>
        <button @click="openAddEventModal" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
          <div class="flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Add Event
          </div>
        </button>
      </div>
    </div>

    <!-- Calendar Controls -->
    <div class="flex flex-col justify-between p-4 mb-6 bg-white rounded-lg shadow sm:flex-row sm:items-center">
      <div class="flex items-center mb-4 space-x-2 sm:mb-0">
        <button @click="previousMonth" class="p-2 rounded-full hover:bg-gray-100">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
          </svg>
        </button>
        <h2 class="text-xl font-bold">{{ currentMonthName }} {{ currentYear }}</h2>
        <button @click="nextMonth" class="p-2 rounded-full hover:bg-gray-100">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
          </svg>
        </button>
        <button @click="goToToday" class="px-3 py-1 ml-2 text-sm bg-gray-100 rounded-md hover:bg-gray-200">Today</button>
      </div>
      <div class="flex items-center mt-4 space-x-2 sm:mt-0">
        <span class="mr-2 text-sm text-gray-500">View:</span>
        <button
          @click="changeView('month')"
          class="px-3 py-1 text-sm rounded-md"
          :class="currentView === 'month' ? 'bg-indigo-100 text-indigo-800' : 'bg-gray-100 hover:bg-gray-200'"
        >Month</button>
        <button
          @click="changeView('week')"
          class="px-3 py-1 text-sm rounded-md"
          :class="currentView === 'week' ? 'bg-indigo-100 text-indigo-800' : 'bg-gray-100 hover:bg-gray-200'"
        >Week</button>
        <button
          @click="changeView('day')"
          class="px-3 py-1 text-sm rounded-md"
          :class="currentView === 'day' ? 'bg-indigo-100 text-indigo-800' : 'bg-gray-100 hover:bg-gray-200'"
        >Day</button>
        <button
          @click="changeView('schedule')"
          class="px-3 py-1 text-sm rounded-md"
          :class="currentView === 'schedule' ? 'bg-indigo-100 text-indigo-800' : 'bg-gray-100 hover:bg-gray-200'"
        >Schedule</button>
      </div>
    </div>

    <!-- Legend -->
    <div class="flex flex-wrap gap-4 p-4 mb-6 bg-white rounded-lg shadow">
      <div class="flex items-center">
        <div class="w-3 h-3 mr-2 bg-blue-500 rounded-full"></div>
        <span class="text-sm">Meeting</span>
      </div>
      <div class="flex items-center">
        <div class="w-3 h-3 mr-2 bg-green-500 rounded-full"></div>
        <span class="text-sm">Appointment</span>
      </div>
      <div class="flex items-center">
        <div class="w-3 h-3 mr-2 bg-yellow-500 rounded-full"></div>
        <span class="text-sm">Reminder</span>
      </div>
      <div class="flex items-center">
        <div class="w-3 h-3 mr-2 bg-red-500 rounded-full"></div>
        <span class="text-sm">Holiday</span>
      </div>
      <div class="flex items-center">
        <div class="w-3 h-3 mr-2 bg-purple-500 rounded-full"></div>
        <span class="text-sm">Out of Office</span>
      </div>
    </div>

    <!-- Month View Calendar -->
    <div v-if="currentView === 'month'" class="bg-white border border-gray-200 rounded-lg shadow">
      <!-- Day Headers -->
      <div class="grid grid-cols-7 gap-px bg-gray-100">
        <div
          v-for="day in weekDays"
          :key="day"
          class="p-2 text-sm font-semibold text-center text-gray-600"
        >
          {{ day }}
        </div>
      </div>
      <!-- Calendar Grid -->
      <div class="grid grid-cols-7 gap-px bg-gray-200">
        <div
          v-for="day in calendarDays"
          :key="day.date"
          class="min-h-[100px] relative bg-white"
          :class="{
            'bg-gray-50': !day.isCurrentMonth,
            'bg-indigo-50': day.isToday
          }"
        >
          <!-- Day Number -->
          <div class="p-1 text-right">
            <span
              class="inline-block text-sm leading-7 text-center rounded-full w-7 h-7"
              :class="{
                'text-gray-400': !day.isCurrentMonth,
                'bg-indigo-600 text-white': day.isToday,
                'font-semibold': day.isCurrentMonth && !day.isToday
              }"
            >{{ day.day }}</span>
          </div>
          <!-- Events -->
          <div class="px-1 pb-1 space-y-1 overflow-y-auto max-h-[80px]">
            <div
              v-for="event in day.events.slice(0, 3)"
              :key="event.id"
              @click="viewEvent(event)"
              class="p-1 text-xs border-l-4 rounded cursor-pointer calendar-event"
              :class="[getEventClass(event), getEventBorderClass(event)]"
            >
              {{ truncateText(event.title, 18) }}
            </div>
            <div
              v-if="day.eventsCount > 3"
              class="p-1 text-xs text-center text-gray-500 bg-gray-100 rounded cursor-pointer"
              @click="changeView('day'); currentDate = DateTime.fromISO(day.date)"
            >
              {{ day.eventsCount - 3 }} more
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Week View -->
    <div v-if="currentView === 'week'" class="bg-white border border-gray-200 rounded-lg shadow">
      <!-- Day Headers -->
      <div class="grid grid-cols-8 gap-px bg-gray-100">
        <div class="p-2 text-sm font-semibold text-center text-gray-600">
          Hour
        </div>
        <div
          v-for="day in currentWeek"
          :key="day.date"
          class="p-2 text-center"
          :class="day.isToday ? 'bg-indigo-50' : ''"
        >
          <div class="text-sm font-semibold" :class="day.isToday ? 'text-indigo-600' : 'text-gray-600'">
            {{ day.dayName }}
          </div>
          <div class="text-sm" :class="day.isToday ? 'text-indigo-600' : 'text-gray-600'">
            {{ day.day }}
          </div>
        </div>
      </div>
      <!-- Hours -->
      <div class="grid grid-cols-8 gap-px bg-gray-200">
        <div v-for="hour in 24" :key="hour - 1" class="bg-white">
          <div class="h-12 px-2 py-1 text-xs text-gray-500 border-b border-gray-100">
            {{ formatHour(hour - 1) }}
          </div>
        </div>

        <!-- Week days hours -->
        <template v-for="day in currentWeek" :key="day.date">
          <div v-for="hour in 24" :key="`${day.date}-${hour-1}`" class="relative h-12 bg-white border-b border-gray-100">
            <!-- Events -->
            <div
              v-for="event in getDayEvents(day.date).filter(e => {
                const eventStart = DateTime.fromISO(e.start);
                return eventStart.hour === (hour - 1);
              })"
              :key="event.id"
              @click="viewEvent(event)"
              class="absolute left-0 right-0 z-10 p-1 mx-1 text-xs truncate border-l-4 rounded cursor-pointer calendar-event"
              :class="[getEventClass(event), getEventBorderClass(event)]"
              :style="{
                top: `${getEventMinutePosition(event, hour - 1)}px`,
                height: `${getEventMinuteHeight(event, hour - 1)}px`
              }"
            >
              {{ truncateText(event.title, 15) }}
            </div>
          </div>
        </template>
      </div>
    </div>

    <!-- Day View -->
    <div v-if="currentView === 'day'" class="bg-white border border-gray-200 rounded-lg shadow">
      <div class="p-4 text-center border-b border-gray-200">
        <h3 class="text-lg font-semibold" :class="isToday(currentDate) ? 'text-indigo-600' : 'text-gray-800'">
          {{ currentDate.toFormat('EEEE, MMMM d, yyyy') }}
        </h3>
      </div>
      <div class="grid grid-cols-[80px_1fr] gap-px bg-gray-200">
        <!-- Hours -->
        <div v-for="hour in 24" :key="hour - 1" class="h-12 px-2 py-1 text-xs text-gray-500 bg-white border-b border-gray-100">
          {{ formatHour(hour - 1) }}
        </div>

        <!-- Day events -->
        <div v-for="hour in 24" :key="`day-${hour-1}`" class="relative h-12 bg-white border-b border-gray-100">
          <!-- Events -->
          <div
            v-for="event in getDayHourEvents(hour - 1)"
            :key="event.id"
            @click="viewEvent(event)"
            class="absolute left-0 right-0 z-10 p-1 mx-1 text-xs border-l-4 rounded cursor-pointer calendar-event"
            :class="[getEventClass(event), getEventBorderClass(event)]"
            :style="{
              top: `${getEventMinutePosition(event, hour - 1)}px`,
              height: `${getEventMinuteHeight(event, hour - 1)}px`
            }"
          >
            {{ truncateText(event.title, 25) }}
          </div>
        </div>
      </div>
    </div>

    <!-- Schedule View -->
    <div v-if="currentView === 'schedule'" class="bg-white border border-gray-200 rounded-lg shadow">
      <div class="p-4">
        <div v-if="Object.keys(groupedEvents).length === 0" class="py-8 text-center text-gray-500">
          No events found
        </div>
        <div v-for="(events, date) in groupedEvents" :key="date" class="mb-6">
          <h3 class="pb-2 mb-3 text-lg font-semibold border-b">{{ formatScheduleDate(date) }}</h3>
          <div v-for="event in events" :key="event.id" class="p-3 mb-3 border-l-4 rounded-lg cursor-pointer hover:bg-gray-50"
            :class="getEventBorderClass(event)"
            @click="viewEvent(event)">
            <div class="flex justify-between">
              <div class="font-medium">{{ event.title }}</div>
              <div class="text-xs text-gray-500">{{ formatEventTime(event) }}</div>
            </div>
            <div v-if="event.location" class="mt-1 text-sm text-gray-600">
              <span class="inline-block mr-1">
                <svg class="inline-block w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
              </span>
              {{ event.location }}
            </div>
            <div v-if="event.description" class="mt-1 text-sm text-gray-600">
              {{ truncateText(event.description, 100) }}
            </div>
            <div class="mt-2">
              <span class="inline-block px-2 py-1 text-xs rounded-full" :class="getEventClass(event)">
                {{ formatEventType(event.type) }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Add/Edit Event Modal -->
    <div v-if="showEventModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
      <div class="w-full max-w-md mx-4 overflow-hidden bg-white rounded-lg shadow-xl">
        <div class="p-4 border-b border-gray-200">
          <h3 class="text-lg font-semibold text-gray-900">{{ editingEvent ? 'Edit Event' : 'Add Event' }}</h3>
        </div>
        <div class="p-4">
          <form @submit.prevent="saveEvent">
            <div class="space-y-4">
              <!-- Title -->
              <div>
                <label class="block mb-1 text-sm font-medium text-gray-700" for="event-title">Title</label>
                <input
                  type="text"
                  id="event-title"
                  v-model="eventForm.title"
                  required
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                />
              </div>

              <!-- Event Type -->
              <div>
                <label class="block mb-1 text-sm font-medium text-gray-700" for="event-type">Event Type</label>
                <select
                  id="event-type"
                  v-model="eventForm.type"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                >
                  <option value="meeting">Meeting</option>
                  <option value="appointment">Appointment</option>
                  <option value="reminder">Reminder</option>
                  <option value="holiday">Holiday</option>
                  <option value="outOfOffice">Out of Office</option>
                </select>
              </div>

              <!-- Start Date/Time -->
              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label class="block mb-1 text-sm font-medium text-gray-700" for="start-date">Start Date</label>
                  <input
                    type="date"
                    id="start-date"
                    v-model="eventForm.startDate"
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                  />
                </div>
                <div>
                  <label class="block mb-1 text-sm font-medium text-gray-700" for="start-time">Start Time</label>
                  <input
                    type="time"
                    id="start-time"
                    v-model="eventForm.startTime"
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                  />
                </div>
              </div>

              <!-- End Date/Time -->
              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label class="block mb-1 text-sm font-medium text-gray-700" for="end-date">End Date</label>
                  <input
                    type="date"
                    id="end-date"
                    v-model="eventForm.endDate"
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                  />
                </div>
                <div>
                  <label class="block mb-1 text-sm font-medium text-gray-700" for="end-time">End Time</label>
                  <input
                    type="time"
                    id="end-time"
                    v-model="eventForm.endTime"
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                  />
                </div>
              </div>

              <!-- Location -->
              <div>
                <label class="block mb-1 text-sm font-medium text-gray-700" for="event-location">Location (optional)</label>
                <input
                  type="text"
                  id="event-location"
                  v-model="eventForm.location"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                />
              </div>

              <!-- Description -->
              <div>
                <label class="block mb-1 text-sm font-medium text-gray-700" for="event-description">Description (optional)</label>
                <textarea
                  id="event-description"
                  v-model="eventForm.description"
                  rows="3"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                ></textarea>
              </div>
            </div>

            <div class="flex justify-end mt-6 space-x-3">
              <button
                type="button"
                @click="closeEventModal"
                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
              >
                Cancel
              </button>
              <button
                type="submit"
                class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
              >
                {{ editingEvent ? 'Update' : 'Save' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- View Event Modal -->
    <div v-if="showViewEventModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
      <div class="w-full max-w-md mx-4 overflow-hidden bg-white rounded-lg shadow-xl">
        <div class="flex items-center justify-between p-4 border-b border-gray-200" :class="getEventClass(selectedEvent)">
          <h3 class="text-lg font-semibold">{{ selectedEvent.title }}</h3>
          <div>
            <span class="inline-block px-2 py-1 text-xs bg-white rounded-full">
              {{ formatEventType(selectedEvent.type) }}
            </span>
          </div>
        </div>
        <div class="p-4 space-y-4">
          <div class="flex items-start">
            <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            <div>
              <div class="text-sm font-medium text-gray-700">Start</div>
              <div class="text-sm text-gray-600">{{ formatEventDateTime(selectedEvent.start) }}</div>
            </div>
          </div>
          <div class="flex items-start">
            <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div>
              <div class="text-sm font-medium text-gray-700">End</div>
              <div class="text-sm text-gray-600">{{ formatEventDateTime(selectedEvent.end) }}</div>
            </div>
          </div>
          <div v-if="selectedEvent.location" class="flex items-start">
            <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
            <div>
              <div class="text-sm font-medium text-gray-700">Location</div>
              <div class="text-sm text-gray-600">{{ selectedEvent.location }}</div>
            </div>
          </div>
          <div v-if="selectedEvent.description" class="flex items-start">
            <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path>
            </svg>
            <div>
              <div class="text-sm font-medium text-gray-700">Description</div>
              <div class="text-sm text-gray-600">{{ selectedEvent.description }}</div>
            </div>
          </div>
        </div>
        <div class="flex justify-end p-4 space-x-3 border-t border-gray-200">
          <button
            @click="deleteEvent(selectedEvent)"
            class="px-4 py-2 text-sm font-medium text-red-700 bg-red-100 rounded-md hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
          >
            Delete
          </button>
          <button
            @click="editEvent(selectedEvent)"
            class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            Edit
          </button>
          <button
            @click="closeViewEventModal"
            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
          >
            Close
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { DateTime } from 'luxon';

export default {
    name: 'Calendar',
    props: ['page_data', 'page_title'],
    data() {
        return {
            currentView: 'month', // 'month', 'week', 'day', 'schedule'
            currentDate: DateTime.now(),
            weekDays: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
            events: [], // Will be populated with sample events
            searchQuery: '',
            showEventModal: false,
            showViewEventModal: false,
            editingEvent: null,
            selectedEvent: {},
            eventForm: {
                title: '',
                startDate: '',
                startTime: '',
                endDate: '',
                endTime: '',
                location: '',
                description: '',
                type: 'meeting'
            }
        }
    },
    computed: {
        currentMonthName() {
            return this.currentDate.toFormat('MMMM');
        },
        currentYear() {
            return this.currentDate.year;
        },
        calendarDays() {
            const result = [];
            // Start from first day of month
            const firstDayOfMonth = this.currentDate.startOf('month');
            // Calculate the start and end of the month view
            const startCalendar = firstDayOfMonth.minus({ days: firstDayOfMonth.weekday % 7 });
            const endCalendar = startCalendar.plus({ days: 42 }); // 6 weeks to display

            let currentDay = startCalendar;
            while (currentDay < endCalendar) {
                // Get events for this day
                const dayEvents = this.filteredEvents.filter(event => {
                    const eventStart = DateTime.fromISO(event.start);
                    return eventStart.hasSame(currentDay, 'day');
                });

                result.push({
                    date: currentDay.toISODate(),
                    day: currentDay.day,
                    isCurrentMonth: currentDay.month === this.currentDate.month,
                    isToday: currentDay.hasSame(DateTime.now(), 'day'),
                    events: dayEvents,
                    eventsCount: dayEvents.length
                });

                currentDay = currentDay.plus({ days: 1 });
            }
            return result;
        },
        currentWeek() {
            const startOfWeek = this.currentDate.startOf('week');
            const days = [];

            for (let i = 0; i < 7; i++) {
                const day = startOfWeek.plus({ days: i });
                days.push({
                    date: day.toISODate(),
                    day: day.day,
                    dayName: day.toFormat('ccc'),
                    isToday: day.hasSame(DateTime.now(), 'day')
                });
            }

            return days;
        },
        filteredEvents() {
            if (!this.searchQuery) return this.events;

            return this.events.filter(event => {
                return event.title.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                       (event.description && event.description.toLowerCase().includes(this.searchQuery.toLowerCase())) ||
                       (event.location && event.location.toLowerCase().includes(this.searchQuery.toLowerCase()));
            });
        },
        groupedEvents() {
            // Group events by date for schedule view
            const grouped = {};

            // Sort events by start time
            const sortedEvents = [...this.filteredEvents].sort((a, b) => {
                return a.start.localeCompare(b.start);
            });

            sortedEvents.forEach(event => {
                const date = event.start.split('T')[0]; // Extract date part
                if (!grouped[date]) {
                    grouped[date] = [];
                }
                grouped[date].push(event);
            });

            return grouped;
        }
    },
    methods: {
        previousMonth() {
            this.currentDate = this.currentDate.minus({ months: 1 });
        },
        nextMonth() {
            this.currentDate = this.currentDate.plus({ months: 1 });
        },
        goToToday() {
            this.currentDate = DateTime.now();
        },
        changeView(view) {
            this.currentView = view;
        },
        formatDate(date, format) {
            return date.toFormat(format || 'yyyy-MM-dd');
        },
        formatHour(hour) {
            const formatted = hour % 12 || 12;
            const amPm = hour < 12 ? 'AM' : 'PM';
            return `${formatted} ${amPm}`;
        },
        getEventClass(event) {
            switch(event.type) {
                case 'meeting':
                    return 'bg-blue-100 text-blue-800';
                case 'appointment':
                    return 'bg-green-100 text-green-800';
                case 'reminder':
                    return 'bg-yellow-100 text-yellow-800';
                case 'holiday':
                    return 'bg-red-100 text-red-800';
                case 'outOfOffice':
                    return 'bg-purple-100 text-purple-800';
                default:
                    return 'bg-gray-100 text-gray-800';
            }
        },
        getEventBorderClass(event) {
            switch(event.type) {
                case 'meeting':
                    return 'border-blue-400';
                case 'appointment':
                    return 'border-green-400';
                case 'reminder':
                    return 'border-yellow-400';
                case 'holiday':
                    return 'border-red-400';
                case 'outOfOffice':
                    return 'border-purple-400';
                default:
                    return 'border-gray-400';
            }
        },
        getEventColorClass(event) {
            switch(event.type) {
                case 'meeting':
                    return 'bg-blue-500';
                case 'appointment':
                    return 'bg-green-500';
                case 'reminder':
                    return 'bg-yellow-500';
                case 'holiday':
                    return 'bg-red-500';
                case 'outOfOffice':
                    return 'bg-purple-500';
                default:
                    return 'bg-gray-500';
            }
        },
        formatEventType(type) {
            switch(type) {
                case 'meeting':
                    return 'Meeting';
                case 'appointment':
                    return 'Appointment';
                case 'reminder':
                    return 'Reminder';
                case 'holiday':
                    return 'Holiday';
                case 'outOfOffice':
                    return 'Out of Office';
                default:
                    return type;
            }
        },
        getDayEvents(date) {
            return this.filteredEvents.filter(event => {
                const eventStart = DateTime.fromISO(event.start);
                return eventStart.toISODate() === date;
            });
        },
        getDayHourEvents(hour) {
            return this.filteredEvents.filter(event => {
                const eventStart = DateTime.fromISO(event.start);
                const eventEnd = DateTime.fromISO(event.end);

                // Check if the event is happening during this hour
                if (eventStart.hasSame(this.currentDate, 'day') || eventEnd.hasSame(this.currentDate, 'day')) {
                    const eventStartHour = eventStart.hour;
                    const eventEndHour = eventEnd.hour;

                    // Event spans this hour
                    return (eventStartHour <= hour && eventEndHour >= hour) ||
                           (eventStartHour === hour);
                }
                return false;
            });
        },
        getEventTopPosition(event) {
            const eventStart = DateTime.fromISO(event.start);
            const hour = eventStart.hour;
            const minute = eventStart.minute;

            return hour * 48 + (minute / 60) * 48;
        },
        getEventHeight(event) {
            const eventStart = DateTime.fromISO(event.start);
            const eventEnd = DateTime.fromISO(event.end);

            const diffInMinutes = eventEnd.diff(eventStart, 'minutes').minutes;
            return Math.max(25, (diffInMinutes / 60) * 48); // Minimum height of 25px
        },
        getEventMinutePosition(event, hour) {
            const eventStart = DateTime.fromISO(event.start);
            if (eventStart.hour !== hour) return 0;

            return (eventStart.minute / 60) * 48;
        },
        getEventMinuteHeight(event, hour) {
            const eventStart = DateTime.fromISO(event.start);
            const eventEnd = DateTime.fromISO(event.end);

            // If the event spans multiple hours
            if (eventStart.hour !== eventEnd.hour) {
                if (eventStart.hour === hour) {
                    // For the first hour, calculate from start to end of hour
                    return ((60 - eventStart.minute) / 60) * 48;
                } else if (eventEnd.hour === hour) {
                    // For the last hour, calculate from start of hour to end
                    return (eventEnd.minute / 60) * 48;
                } else if (eventStart.hour < hour && eventEnd.hour > hour) {
                    // For hours in the middle, use the full hour
                    return 48;
                }
            } else {
                // Event within the same hour
                const diffInMinutes = eventEnd.diff(eventStart, 'minutes').minutes;
                return Math.max(25, (diffInMinutes / 60) * 48); // Minimum height of 25px
            }

            return 0;
        },
        formatScheduleDate(dateStr) {
            const date = DateTime.fromISO(dateStr);
            const today = DateTime.now();
            const tomorrow = today.plus({ days: 1 });

            if (date.hasSame(today, 'day')) return 'Today';
            if (date.hasSame(tomorrow, 'day')) return 'Tomorrow';

            return date.toFormat('EEEE, MMMM d, yyyy');
        },
        formatEventTime(event) {
            const start = DateTime.fromISO(event.start);
            const end = DateTime.fromISO(event.end);

            return `${start.toFormat('h:mm a')} - ${end.toFormat('h:mm a')}`;
        },
        formatEventDateTime(dateTime) {
            if (!dateTime) return '';
            const dt = DateTime.fromISO(dateTime);
            return dt.toFormat('EEE, MMM d, yyyy h:mm a');
        },
        truncateText(text, length) {
            if (!text) return '';
            if (text.length <= length) return text;
            return text.substring(0, length) + '...';
        },
        isToday(date) {
            return date.hasSame(DateTime.now(), 'day');
        },
        openAddEventModal() {
            this.editingEvent = null;
            const now = DateTime.now();
            const later = now.plus({ hours: 1 });

            this.eventForm = {
                title: '',
                startDate: now.toFormat('yyyy-MM-dd'),
                startTime: now.toFormat('HH:mm'),
                endDate: now.toFormat('yyyy-MM-dd'),
                endTime: later.toFormat('HH:mm'),
                location: '',
                description: '',
                type: 'meeting'
            };

            this.showEventModal = true;
        },
        closeEventModal() {
            this.showEventModal = false;
            this.editingEvent = null;
        },
        saveEvent() {
            const startDateTime = `${this.eventForm.startDate}T${this.eventForm.startTime}:00`;
            const endDateTime = `${this.eventForm.endDate}T${this.eventForm.endTime}:00`;

            const event = {
                id: this.editingEvent ? this.editingEvent.id : Date.now().toString(),
                title: this.eventForm.title,
                start: startDateTime,
                end: endDateTime,
                location: this.eventForm.location,
                description: this.eventForm.description,
                type: this.eventForm.type
            };

            if (this.editingEvent) {
                // Update existing event
                const index = this.events.findIndex(e => e.id === this.editingEvent.id);
                if (index !== -1) {
                    this.events.splice(index, 1, event);
                }
            } else {
                // Add new event
                this.events.push(event);
            }

            this.closeEventModal();

            // In a real application, you would save this to your backend
            // axios.post('/api/events', event)...
        },
        viewEvent(event) {
            this.selectedEvent = event;
            this.showViewEventModal = true;
        },
        closeViewEventModal() {
            this.showViewEventModal = false;
        },
        editEvent(event) {
            this.closeViewEventModal();

            const startDT = DateTime.fromISO(event.start);
            const endDT = DateTime.fromISO(event.end);

            this.eventForm = {
                title: event.title,
                startDate: startDT.toFormat('yyyy-MM-dd'),
                startTime: startDT.toFormat('HH:mm'),
                endDate: endDT.toFormat('yyyy-MM-dd'),
                endTime: endDT.toFormat('HH:mm'),
                location: event.location || '',
                description: event.description || '',
                type: event.type || 'meeting'
            };

            this.editingEvent = event;
            this.showEventModal = true;
        },
        deleteEvent(event) {
            if (confirm('Are you sure you want to delete this event?')) {
                const index = this.events.findIndex(e => e.id === event.id);
                if (index !== -1) {
                    this.events.splice(index, 1);
                }
                this.closeViewEventModal();

                // In a real application, you would delete this from your backend
                // axios.delete(`/api/events/${event.id}`)...
            }
        },
        initializeEvents() {
            // Populate with sample events
            const now = DateTime.now();

            this.events = [
                {
                    id: '1',
                    title: 'Team Meeting',
                    start: now.toFormat('yyyy-MM-dd') + 'T09:00:00',
                    end: now.toFormat('yyyy-MM-dd') + 'T10:00:00',
                    location: 'Conference Room A',
                    description: 'Weekly team meeting to discuss project status',
                    type: 'meeting'
                },
                {
                    id: '2',
                    title: 'Client Call',
                    start: now.toFormat('yyyy-MM-dd') + 'T11:00:00',
                    end: now.toFormat('yyyy-MM-dd') + 'T12:00:00',
                    location: 'Zoom',
                    description: 'Project status update with client',
                    type: 'meeting'
                },
                {
                    id: '3',
                    title: 'Lunch Break',
                    start: now.toFormat('yyyy-MM-dd') + 'T12:30:00',
                    end: now.toFormat('yyyy-MM-dd') + 'T13:30:00',
                    type: 'reminder'
                },
                {
                    id: '4',
                    title: 'Eid Holiday',
                    start: now.plus({ days: 2 }).toFormat('yyyy-MM-dd') + 'T00:00:00',
                    end: now.plus({ days: 2 }).toFormat('yyyy-MM-dd') + 'T23:59:00',
                    type: 'holiday'
                },
                {
                    id: '5',
                    title: 'Project Deadline',
                    start: now.plus({ days: 5 }).toFormat('yyyy-MM-dd') + 'T17:00:00',
                    end: now.plus({ days: 5 }).toFormat('yyyy-MM-dd') + 'T18:00:00',
                    description: 'Final submission for client project',
                    type: 'reminder'
                },
                {
                    id: '6',
                    title: 'Dentist Appointment',
                    start: now.plus({ days: 3 }).toFormat('yyyy-MM-dd') + 'T14:00:00',
                    end: now.plus({ days: 3 }).toFormat('yyyy-MM-dd') + 'T15:00:00',
                    location: 'Clinic',
                    type: 'appointment'
                },
                {
                    id: '7',
                    title: 'Business Trip',
                    start: now.plus({ days: 10 }).toFormat('yyyy-MM-dd') + 'T08:00:00',
                    end: now.plus({ days: 12 }).toFormat('yyyy-MM-dd') + 'T18:00:00',
                    location: 'Dubai',
                    description: 'Client meetings and conference',
                    type: 'outOfOffice'
                }
            ];
        }
    },
    mounted() {
        this.$nextTick(() => {
            this.initializeEvents();
        });
    }
}
</script>

<style scoped>
.calendar-event {
    position: relative;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    transition: all 0.2s ease;
}

.calendar-event:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    z-index: 20;
}

/* Responsive adjustments */
@media (max-width: 640px) {
    .calendar-event {
        font-size: 0.65rem;
    }
}
</style>
