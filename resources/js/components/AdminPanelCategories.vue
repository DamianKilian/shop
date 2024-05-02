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
        <div class="sortable-list">
            <div class="actions-global clearfix">
                <button type="button" class="btn btn-success float-end" @click="addCategory">
                    <i class="fa-solid fa-plus"></i>
                </button>
            </div>
            <AdminPanelCategoriesList :currentCategories="currentCategories" :breadcrumb="breadcrumb"
                :categories="categories" />
            <div class="clearfix">
                <button type="button" class="btn btn-success float-end" @click="saveCategories">Save</button>
            </div>
            <div v-if="globalError" class="text-bg-danger float-end mt-1">{{ globalError }}</div>
            <div v-if="globalSuccess" class="text-bg-success float-end mt-1">{{ globalSuccess }}</div>
        </div>

    </div>
</template>

<script>
import AdminPanelCategoriesList from './AdminPanelCategoriesList.vue'

export default {
    components: { AdminPanelCategoriesList },
    props: ['categoriesProp', 'adminPanelSaveCategoriesUrl'],
    data() {
        return {
            globalError: '',
            globalSuccess: '',
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
            this.breadcrumb = this.breadcrumb.slice(0, index + 1);
        },
        addCategory: function () {
            var id = Math.random().toString().replace('0.', 'new_');
            this.currentCategories.push({
                name: id,
                id: id,
                new: true,
            });
        },
        saveCategories: function () {
            var that = this;
            that.globalSuccess = '';
            that.globalError = '';
            axios.post(this.adminPanelSaveCategoriesUrl, { categories: this.categories })
                .then(function (response) {
                    that.arrangeCategories(response.data.categories);
                    that.globalSuccess = 'Saved!';
                })
                .catch(function (error) {
                    if (_.has(error, 'response.data.failedValidation')) {
                        _.forEach(error.response.data.failedValidation, function (value, key) {
                            let parentIds = key.split('.');
                            that.categories[parentIds[1]][parentIds[2]].failedValidation = value;
                        });
                    } else {
                        that.globalError = error.message;
                    }
                });
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
                    initialPosition: this.categories[item.parent_id].length,
                    initialName: item.name,
                });
            });
        },
        setBreadcrumb: function () {
            this.breadcrumb.push({
                name: 'Main menu',
                id: this.mainMenuId,
            });
        },
    },
    created() {
        this.setBreadcrumb();
        this.arrangeCategories();
    },
    mounted() {
    }
}
</script>
