<template>
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div class="flex flex-col gap-1">
            <select class="select select-sm min-w-[20rem] max-w-full truncate" v-model="selected_category_id" @change="setItemToLocalStorage('selected_category_id', selected_category_id)">
                <option v-for="category in categories" :key="category.id" :value="category.id">
                    {{ category.name }}
                </option>
            </select>
        </div>
    </div>
</template>

<script>
export default {
    name: "user-categories-dropdown",
    props: ['categories'],
    data() {
        return {
            selected_category_id: null
        }
    },
    methods: {

    },
    mounted() {
        if(this.categories.length > 0 ){
            const categoryId = this.categories[0].id;
            this.selected_category_id = categoryId
            this.setItemToLocalStorage('selected_category_id', categoryId)
        }
    }
}
</script>

<style scoped>

</style>
