<template>
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="pb-6">
            <div class="flex items-center justify-between">
                <!-- Title -->
                <div class="flex flex-col justify-start w-full max-w-sm text-black font-black">
                    <div>{{ page_data.title }}</div>
                </div>
            </div>
        </div>
        <!-- Content -->
        <div class="relative flex-grow overflow-auto table-container">
            <!-- Table -->
            <table class="w-full table table-border align-middle text-xs table-fixed">
                <thead>
                    <tr class="bg-black text-gray-900 font-medium text-center">
                        <th class="sticky top-0 w-10">#</th>
                        <th class="sticky top-0 w-[150px]">Name</th>
                        <th class="sticky top-0 w-[50px]">Sage Companies Count</th>
                        <th class="sticky top-0 w-[200px]">Sage Companies</th>
                    </tr>
                </thead>
                <tbody class="text-center text-xs text-gray-700">
                    <tr v-for="(obj, index) in page_data.data" :key="index" class="odd:bg-white even:bg-slate-100 hover:bg-gray-300">
                        <td>{{ ++index }}</td>
                        <td>{{ obj.name }}</td>
                        <td><span class="badge badge-outline badge-primary badge-sm">{{ obj.sage_companies.length }}</span></td>
                        <td>
                            <div v-for="(item, index) in obj.sage_companies" :key="index" class="mb-2" >
                                <div class="badge badge-outline badge-primary badge-sm ml-3">
                                    <span>{{ item.bitrix_sage_company_name }}</span>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr class="table-no-data-available" v-if="page_data.data.length === 0">
                        <td class="text-center text-red-400">No data available</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <!-- footer -->
        <div class="flex items-center justify-between">
            <div class="text-xs">
                <span>Showing {{ page_data.data.length }} records</span>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "categories",
    props: ['page_data'],
    data() {
        return {
        }
    },
}
</script>

<style scoped>

</style>
