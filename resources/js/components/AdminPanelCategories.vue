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
        <div class="app-list clearfix">
            <div class="actions-global clearfix">
                <button type="button" class="btn btn-success float-end" @click="addCategory">
                    <i class="fa-solid fa-plus"></i>
                </button>
                <button type="button" class="btn btn-secondary float-end me-1"
                    @click="goToCategory($event, breadcrumb.length - 2)">
                    <i class="fa-solid fa-arrow-left"></i>
                </button>
            </div>
            <AdminPanelCategoriesList :filters='filters' :currentCategories="currentCategories" :breadcrumb="breadcrumb"
                :categories="categories" :showDeletedCategories='showDeletedCategories' />
            <div class="clearfix">
                <button class="btn btn-success float-end" @click="saveCategories">{{ __('Save') }}</button>
            </div>
            <div v-if="globalError" class="text-bg-danger float-end mt-1">{{ globalError }}</div>
            <div v-if="globalSuccess" class="text-bg-success float-end mt-1">{{ globalSuccess }}</div>
        </div>
        <div class="text-center mt-3">
            <button class="btn btn-sm btn-outline-danger" :class='{ "btn-outline-dark": !showDeletedCategories }'
                @click='showDeletedCategories = !showDeletedCategories'>
                {{ __('Show deleted categories') }}
            </button>
        </div>
    </div>
</template>

<script>
import AdminPanelCategoriesList from './AdminPanelCategoriesList.vue'
import { goToCategory, arrangeCategories, setBreadcrumb } from './commonFunctions.js'

export default {
    components: { AdminPanelCategoriesList },
    props: ['categoriesProp', 'filters', 'adminPanelSaveCategoriesUrl'],
    data() {
        return {
            showDeletedCategories: false,
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
        goToCategory,
        arrangeCategories,
        setBreadcrumb,
        addCategory: function () {
            var id = Math.random().toString().replace('0.', 'new_');
            this.currentCategories.push({
                name: id,
                slug: id,
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
                    that.globalSuccess = __('Saved!');
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
    },
    created() {
        this.setBreadcrumb();
        this.arrangeCategories();
    },
    updated() {
        console.debug('updated');
    },
    mounted() {
    }
}
</script>
