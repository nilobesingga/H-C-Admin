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
                        <th class="sticky top-0 w-[100px]">Bitrix Id</th>
                        <th class="sticky top-0 w-[200px]">Name</th>
                    </tr>
                </thead>
                <tbody class="text-left text-xs tracking-tight">
                    <tr v-for="(obj, index) in page_data.data" :key="index" class="transition-all duration-300 text-neutral-800">
                        <td>{{ ++index }}</td>
                        <td>{{ obj.bitrix_id }}</td>
                        <td>{{ obj.bitrix_name }}</td>
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
    name: "countries",
    props: ['page_data'],
    data() {
        return {

        }
    },
}
</script>

<style scoped>

</style>
