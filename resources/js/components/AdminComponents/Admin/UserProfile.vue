<template>
  <div class="min-h-screen bg-gray-100">
    <!-- Profile Header -->
    <div class="relative flex items-end h-56">
      <!-- Background image with opacity -->
      <div class="absolute inset-0 z-0">
        <div class="w-full h-full bg-center bg-cover" style="background-image: url('/storage/images/logos/profile-bg.jpg'); opacity: 0.5;"></div>
        <!-- Overlay dark blue with opacity -->
        <div class="absolute inset-0 bg-[#FFA348] opacity-60"></div>
      </div>
      <!-- Foreground content -->
      <div class="absolute z-10 flex items-center gap-6 opacity-100 bottom-16 left-12">
        <img :src="user.avatar || 'https://randomuser.me/api/portraits/women/44.jpg'" class="object-cover w-24 h-24 border-4 border-white rounded-full shadow-lg" />
        <div class="flex flex-col justify-center">
          <h2 class="text-2xl font-bold leading-tight text-white">{{ user.name }}</h2>
          <p class="text-base text-white">{{ user.designation || 'Owner & Founder' }}</p>
          <div class="flex items-center gap-4 mt-2 text-sm text-white/80">
            <span class="flex items-center gap-1"><i class="fas fa-map-marker-alt"></i> {{ user.location || 'California, United States' }}</span>
            <span class="flex items-center gap-1"><i class="fas fa-building"></i> {{ user.company || 'Themesbrand' }}</span>
          </div>
        </div>
      </div>
      <button class="absolute z-20 px-4 py-2 text-white bg-green-500 rounded right-8 top-8 hover:bg-green-600">Edit Profile</button>
    </div>

    <div class="w-full px-4 mx-auto mt-4">
      <!-- Tabs -->
      <div class="flex gap-4 mb-6 border-b">
        <button class="px-4 py-2 font-semibold text-blue-600 border-b-2 border-blue-600">Overview</button>
        <button class="px-4 py-2 text-gray-500">Activities</button>
        <button class="px-4 py-2 text-gray-500">Projects</button>
        <button class="px-4 py-2 text-gray-500">Documents</button>
      </div>

      <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Left Column -->
        <div class="col-span-1 space-y-6">
          <!-- Profile Completion -->
          <div class="p-4 bg-white rounded-lg shadow">
            <div class="flex items-center justify-between mb-2">
              <span class="font-semibold text-gray-700">Complete Your Profile</span>
              <span class="text-xs text-gray-500">30%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2.5">
              <div class="bg-red-500 h-2.5 rounded-full" style="width: 30%"></div>
            </div>
          </div>

          <!-- Info -->
          <div class="p-4 bg-white rounded-lg shadow">
            <h3 class="mb-4 font-semibold text-gray-700">Info</h3>
            <div class="space-y-2 text-sm">
              <div class="flex justify-between"><span class="font-medium">Full Name :</span><span>{{ user.name }}</span></div>
              <div class="flex justify-between"><span class="font-medium">Mobile :</span><span>{{ user.mobile || '+1 (987) 6543' }}</span></div>
              <div class="flex justify-between"><span class="font-medium">E-mail :</span><span>{{ user.email }}</span></div>
              <div class="flex justify-between"><span class="font-medium">Location :</span><span>{{ user.location || 'California, United States' }}</span></div>
              <div class="flex justify-between"><span class="font-medium">Joining Date :</span><span>{{ user.joined || '24 Nov 2021' }}</span></div>
            </div>
          </div>

          <!-- Portfolio -->
          <div class="p-4 bg-white rounded-lg shadow">
            <h3 class="mb-4 font-semibold text-gray-700">Portfolio</h3>
            <div class="flex gap-3">
              <a href="#" class="text-blue-500"><i class="fab fa-facebook-f"></i></a>
              <a href="#" class="text-blue-400"><i class="fab fa-twitter"></i></a>
              <a href="#" class="text-pink-500"><i class="fab fa-dribbble"></i></a>
              <a href="#" class="text-green-500"><i class="fab fa-behance"></i></a>
              <a href="#" class="text-red-500"><i class="fab fa-pinterest"></i></a>
            </div>
          </div>

          <!-- Skills -->
          <div class="p-4 bg-white rounded-lg shadow">
            <h3 class="mb-4 font-semibold text-gray-700">Skills</h3>
            <div class="flex flex-wrap gap-2">
              <span v-for="skill in user.skills || ['Photoshop','Illustrator','HTML','CSS','Javascript','Php','Python']" :key="skill" class="px-2 py-1 text-xs text-blue-700 bg-blue-100 rounded">{{ skill }}</span>
            </div>
          </div>

          <!-- Suggestions -->
          <div class="p-4 bg-white rounded-lg shadow">
            <h3 class="mb-4 font-semibold text-gray-700">Suggestions</h3>
            <div class="space-y-2">
              <div v-for="suggestion in suggestions" :key="suggestion.name" class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                  <img :src="suggestion.avatar" class="w-8 h-8 rounded-full" />
                  <div>
                    <div class="text-sm font-medium">{{ suggestion.name }}</div>
                    <div class="text-xs text-gray-400">{{ suggestion.role }}</div>
                  </div>
                </div>
                <button class="px-2 py-1 text-xs text-blue-600 bg-blue-100 rounded">Follow</button>
              </div>
            </div>
          </div>
        </div>

        <!-- Main Column -->
        <div class="col-span-2 space-y-6">
          <!-- About -->
          <div class="p-6 bg-white rounded-lg shadow">
            <h3 class="mb-2 font-semibold text-gray-700">About</h3>
            <p class="mb-2 text-sm text-gray-600">{{ user.about || aboutText }}</p>
            <div class="flex gap-6 mt-2 text-sm text-gray-500">
              <div><span class="font-medium">Designation :</span> {{ user.designation || 'Lead Designer / Developer' }}</div>
              <div><span class="font-medium">Website :</span> <a href="https://www.veizon.com" class="text-blue-500" target="_blank">www.veizon.com</a></div>
            </div>
          </div>

          <!-- Recent Activity -->
          <div class="p-6 bg-white rounded-lg shadow">
            <div class="flex items-center justify-between mb-4">
              <h3 class="font-semibold text-gray-700">Recent Activity</h3>
              <div class="flex gap-2 text-sm">
                <button class="pb-1 font-semibold text-blue-600 border-b-2 border-blue-600">Today</button>
                <button class="text-gray-400">Weekly</button>
                <button class="text-gray-400">Monthly</button>
              </div>
            </div>
            <div class="space-y-4">
              <div v-for="activity in activities" :key="activity.id" class="flex items-start gap-3">
                <img :src="activity.avatar" class="w-10 h-10 rounded-full" />
                <div>
                  <div class="font-medium text-gray-800">{{ activity.user }}</div>
                  <div class="mb-1 text-xs text-gray-400">{{ activity.time }}</div>
                  <div class="text-sm text-gray-600">{{ activity.text }}</div>
                  <div v-if="activity.attachments" class="flex gap-2 mt-2">
                    <div v-for="file in activity.attachments" :key="file.name" class="flex items-center gap-1 px-2 py-1 text-xs bg-gray-100 rounded">
                      <i class="fas fa-file-alt"></i> {{ file.name }} <span class="text-gray-400">({{ file.size }})</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'user-profile',
  props: {
    page_data: { type: Object, required: true },
    user: { type: Object, required: true },
    module: { type: Object, required: true }
  },
  data() {
    return {
      aboutText: `Hi I'm Anna Adame, it will be as simple as Occidental; in fact, it will be Occidental. To an English person, it will seem like simplified English, as a skeptical Cambridge friend of mine told me what Occidental is European languages are members of the same family.\nYou always want to make sure that your fonts work well together and try to limit the number of fonts you use to three or less. Experiment and play around with the fonts that you already have in the software you're working with reputable font websites. This may be the most commonly encountered tip I received from the designers I spoke with. They highly encourage that you use different fonts in one design, but do not over-exaggerate and go overboard.`,
      suggestions: [
        { name: 'Esther James', role: 'Frontend Developer', avatar: 'https://randomuser.me/api/portraits/women/65.jpg' },
        { name: 'Jacqueline Steve', role: 'UI/UX Designer', avatar: 'https://randomuser.me/api/portraits/women/66.jpg' },
        { name: 'George Whalen', role: 'Backend Developer', avatar: 'https://randomuser.me/api/portraits/men/67.jpg' }
      ],
      activities: [
        {
          id: 1,
          user: 'Jacqueline Steve',
          avatar: 'https://randomuser.me/api/portraits/women/66.jpg',
          time: '06:15PM',
          text: 'We has changed 2 attributes on 06:15PM.',
          attachments: null
        },
        {
          id: 2,
          user: 'Megan Elmore',
          avatar: 'https://randomuser.me/api/portraits/women/67.jpg',
          time: '04:45PM',
          text: 'Adding a new event with attachments',
          attachments: [
            { name: 'Business Template - UI/UX design', size: '685 KB' },
            { name: 'Bank Management System - PSD', size: '8.78 MB' }
          ]
        },
        {
          id: 3,
          user: 'Eric245',
          avatar: 'https://randomuser.me/api/portraits/men/68.jpg',
          time: '02:33PM',
          text: 'New ticket received',
          attachments: null
        },
        {
          id: 4,
          user: 'Nancy Martino',
          avatar: 'https://randomuser.me/api/portraits/women/69.jpg',
          time: '12:57PM',
          text: 'A wonderful serenity has taken possession of my entire soul, like these sweet mornings of spring which I enjoy with my whole heart.',
          attachments: null
        },
        {
          id: 5,
          user: 'Lewis Arnold',
          avatar: 'https://randomuser.me/api/portraits/men/70.jpg',
          time: '10:05AM',
          text: 'Create new project building product',
          attachments: null
        }
      ]
    };
  }
};
</script>

<style scoped>
/* Add any additional custom styles here if needed */
</style>
