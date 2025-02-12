<template>
    <div class="container-fluid px-3">
        <!-- Page Header -->
        <div class="py-7">
            <div class="flex items-center justify-between">
                <!-- Title -->
                <div class="flex flex-col justify-start w-full max-w-sm text-black font-bold text-3xl tracking-tight">
                    {{ page_data.title }}
                </div>
            </div>
        </div>
        <!-- Content -->
        <div class="relative flex-grow overflow-auto table-container shadow-md border border-tec">
            <!-- Table -->
            <table class="w-full table table-main table-border align-middle text-xs table-fixed">
                <thead>
                    <tr class="text-left tracking-tight">
                        <th class="sticky top-0 w-10">#</th>
                        <th class="sticky top-0 w-[150px]">Name</th>
                        <th class="sticky top-0 w-[50px] text-left">Sage Companies Count</th>
                        <th class="sticky top-0 w-[200px]">Sage Companies</th>
                    </tr>
                </thead>
                <tbody class="text-left text-xs tracking-tight">
                    <tr v-for="(obj, index) in page_data.data" :key="index" class="group transition-all duration-300 text-neutral-800">
                        <td>{{ ++index }}</td>
                        <td>{{ obj.name }}</td>
                        <td>{{ obj.sage_companies.length }}</td>
                        <td>
                            <div v-for="(item, index) in obj.sage_companies" :key="index">
                                <div class="group-hover:!bg-tec-active/20 mr-1 mb-1 !text-neutral-800 badge badge-sm bg-transparent transition-all duration-300">
                                    <span>{{ item.bitrix_sage_company_name }}</span>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr class="table-no-data-available" v-if="page_data.data.length === 0">
                        <td class="text-center text-md text-red-400">No data available</td>
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
