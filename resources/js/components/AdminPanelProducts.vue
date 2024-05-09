<template>
    <div id="admin-panel-categories">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb justify-content-center">
                <li v-for="(b, index) in breadcrumb" :key="b.id" class="breadcrumb-item">
                    <button @click="goToCategory($event, index)" class="btn btn-sm btn-link"
                        :class="{ active: index == breadcrumb.length - 1 }">
                        {{ b.name }}
                    </button>
                </li>
            </ol>
        </nav>
        <div class="app-list">
            <div class="actions-global clearfix">
                <button type="button" class="btn btn-secondary float-end"
                    @click="goToCategory($event, breadcrumb.length - 2)">
                    <i class="fa-solid fa-arrow-left"></i>
                </button>
            </div>
            <AdminPanelProductsList :currentCategories="currentCategories" :breadcrumb="breadcrumb"
                :categories="categories" />
        </div>
    </div>
</template>

<script>
import AdminPanelProductsList from './AdminPanelProductsList.vue'

export default {
    components: { AdminPanelProductsList },
    props: ['categoriesProp'],
    data() {
        return {
            mainMenuId: 'main-menu',
            breadcrumb: [],
            categories: {},
        }
    },
    computed: {
        currentCategories() {
            return this.categories[_.last(this.breadcrumb).id];
        },
    },
    methods: {
        goToCategory: function (e, index) {
            if (1 === this.breadcrumb.length) {
                return;
            }
            this.breadcrumb = this.breadcrumb.slice(0, index + 1);
        },
        arrangeCategories: function (categoriesDb = null) {
            categoriesDb = categoriesDb || this.categoriesProp;
            this.categories = {
                [this.mainMenuId]: []
            };
            if (!categoriesDb) {
                return;
            }
            categoriesDb.forEach((item) => {
                item.parent_id = item.parent_id || this.mainMenuId;
                if (!this.categories[item.parent_id]) {
                    this.categories[item.parent_id] = [];
                }
                this.categories[item.parent_id].push({
                    name: item.name,
                    id: item.id,
                });
            });
        },
        setBreadcrumb: function () {
            this.breadcrumb.push({
                name: __('Main menu'),
                id: this.mainMenuId,
            });
        },
    },
    created() {
        this.setBreadcrumb();
        this.arrangeCategories();
    },
    mounted() {
        console.debug(this.categoriesProp);//mmmyyy
    }
}
</script>
