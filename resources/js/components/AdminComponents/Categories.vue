<template>
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Categories</h3>
            </div>
            <div class="card-table scrollable-x-auto">
                <table class="table">
                    <thead>
                    <tr>
                        <th class="min-w-1">#</th>
                        <th>Name</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(obj, index) in propData" :key="obj.id">
                        <td>{{ ++index }}</td>
                        <td>{{ obj.name }}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "categories",
    props: ['propData'],
    data() {
        return {

        }
    },
}
</script>

<style scoped>

</style>
