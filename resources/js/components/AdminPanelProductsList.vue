<template>
    <ul class="app-list-ul">
        <li v-for="(category, index) in currentCategories" :key="category.id" class="d-flex">
            {{ category.name }}
            <div class="btn-group btn-group-sm float-end" role="group" aria-label="Small button group">
                <button class="btn btn-secondary" @click="goToSubCategory($event, category)"><i
                        class="fa-solid fa-arrow-right"></i></button>
            </div>
        </li>
    </ul>
</template>

<script>
export default {
    props: ['currentCategories', 'breadcrumb', 'categories'],
    data() {
        return {
        }
    },
    methods: {
        goToSubCategory: function (e, category) {
            this.breadcrumb.push(category);
            if (!this.categories[category.id]) {
                this.categories[category.id] = [];
            }
        },
    },
    created() {
    },
    mounted() {
    },
}
</script>
