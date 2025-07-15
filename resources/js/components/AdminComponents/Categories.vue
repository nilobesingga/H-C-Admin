<template>
    <div class="px-3 container-fluid">
        <!-- Page Header -->
        <div class="py-7">
            <div class="flex items-center justify-between">
                <!-- Title -->
                <div class="flex flex-col justify-start w-full max-w-sm text-3xl font-bold tracking-tight text-black">
                    {{ page_data.title }}
                </div>
            </div>
        </div>
        <!-- Content -->
        <div class="relative flex-grow overflow-auto border shadow-md table-container border-tec">
            <!-- Table -->
            <table class="table w-full text-xs align-middle table-fixed table-main table-border">
                <thead>
                    <tr class="tracking-tight text-left">
                        <th class="sticky top-0 w-10">#</th>
                        <th class="sticky top-0 w-[150px]">Name</th>
                    </tr>
                </thead>
                <tbody class="text-xs tracking-tight text-left">
                    <tr v-for="(obj, index) in page_data.data" :key="index" class="transition-all duration-300 group text-neutral-800">
                        <td>{{ ++index }}</td>
                        <td>{{ obj.name }}</td>
                    </tr>
                    <tr class="table-no-data-available" v-if="page_data.data.length === 0">
                        <td class="text-center text-red-400 text-md">No data available</td>
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
