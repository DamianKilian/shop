<template>
    <ul id="sortable" class="app-list-ul" @dragstart="dragstart" @dragend="dragend($event, 'currentCategories')"
        @dragover="dragover" ref="sortable">
        <template v-for="(category, index) in currentCategories" :key="category.id">
            <li :set="removeStatus = category.remove || (category.deleted_at && !category.restore)"
                v-if="!category.deleted_at || category.restore || showDeletedCategories"
                :class="{ 'new-category': category.new, 'text-bg-danger': removeStatus, 'mb-4': category.failedValidation }"
                class="d-flex align-items-stretch draggableElement" draggable="true">
                <div class="cat-name flex-fill align-self-center"><span class="text-muted">{{ __('name') }}: </span>
                    <b>{{ category.name }}</b>
                </div>
                <div class="cat-slug flex-fill align-self-center"><span class="text-muted">{{ __('slug') }}: </span>
                    <b>{{ category.slug }}</b>
                </div>
                <div class="btn-group btn-group-sm float-end" role="group" aria-label="Small button group">
                    <button v-if='!removeStatus' class="btn btn-secondary" data-bs-toggle="modal"
                        data-bs-target="#editCategory" @click="bootEditedCategory(category)"><i
                            class="fa-solid fa-pen-to-square"></i></button>
                    <button class="btn btn-secondary" @click="removeCategory($event, index, category)"><i
                            class="fa-solid fa-xmark"></i></button>
                    <button v-if='!removeStatus' class="btn btn-secondary" @click="goToSubCategory($event, category)"><i
                            class="fa-solid fa-arrow-right"></i></button>
                </div>
                <div v-if="category.failedValidation" class="text-danger"
                    style="position: absolute;bottom: -19px;z-index: 5;">{{
                        category.failedValidation[0] }}</div>
            </li>
        </template>
    </ul>
    <div id="editCategory" class="modal fade" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="input-group input-group-sm mb-3">
                        <span class="input-group-text" id="inputGroup-sizing-sm" style="width: 60px;">{{ __('Name')
                            }}</span>
                        <input v-model='editedCategory.name' @input='editedCategoryName()' type="text"
                            class="form-control" aria-label="Sizing example input"
                            aria-describedby="inputGroup-sizing-sm">
                    </div>
                    <div class="input-group input-group-sm mb-3">
                        <span class="input-group-text" id="inputGroup-sizing-sm" style="width: 60px;">{{ __('Slug')
                            }}</span>
                        <input v-model='editedCategory.slug' @input='editedCategory.slugCustomized = true' type="text"
                            class="form-control" aria-label="Sizing example input"
                            aria-describedby="inputGroup-sizing-sm">
                        <button @click='editedCategory.slugCustomized = false; editedCategoryName()'
                            class="btn btn-outline-secondary" type="button" id="button-addon2">{{ __('Reset')
                            }}</button>
                    </div>
                    <fieldset ref='filters'>
                        <legend>{{ __('Filters') }}:</legend>
                        <div v-for="(filter) in filters" :key="filter.id" class="form-check form-check-inline">
                            <input @change='addFilter($event, filter, editedCategory)'
                                class="form-check-input filter-inp" type="checkbox" :id='"f-" + filter.id'
                                :data-id="filter.id">
                            <label class="form-check-label" :for='"f-" + filter.id'>{{ filter.name }}</label>
                        </div>
                    </fieldset>
                </div>
                <div class="modal-footer border-light">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">{{ __('Ok') }}</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { goToSubCategory, arraymove } from './commonFunctions.js'
import sortable from "./sortable.js";

export default {
    mixins: [sortable],
    props: ['currentCategories', 'breadcrumb', 'categories', 'filters', 'showDeletedCategories'],
    data() {
        return {
            editedCategory: {
                name: '',
                slug: '',
                filtersById: {}
            }
        }
    },
    methods: {
        goToSubCategory,
        arraymove,
        editedCategoryName: function (e) {
            if (!this.editedCategory.slugCustomized) {
                this.editedCategory.slug = this.generateSlug();
            }
        },
        generateSlug: function () {
            return this.editedCategory.name.trim().replace(/ /g, '-');
        },
        addFilter: function (e, filter, category) {
            if (e.target.checked) {
                category.filtersById[filter.id] = filter.name;
            } else {
                delete category.filtersById[filter.id];
            }
        },
        bootEditedCategory: function (category) {
            this.editedCategory = category;
            this.editedCategory.slugCustomized = this.generateSlug() !== this.editedCategory.slug;
            var that = this;
            if (!this.editedCategory.filtersById) {
                this.editedCategory.filtersById = {};
                _.forEach(category.filters, function (filter) {
                    that.editedCategory.filtersById[filter.id] = filter.name;
                });
            }
            _.forEach(this.$refs.filters.querySelectorAll('.filter-inp'), function (filter) {
                filter.checked = !!that.editedCategory.filtersById[filter.dataset.id];
            });
        },
        removeCategory: function (e, index, category) {
            if (category.new) {
                this.currentCategories.splice(index, 1);
            } else if (category.deleted_at) {
                if (!category.restore) {
                    category.restore = true;
                } else {
                    category.restore = false;
                }
            } else {
                if (!category.remove) {
                    category.remove = true;
                } else {
                    category.remove = false;
                }
            }
        },
    },
    created() {
    },
    mounted() {
    },
    updated() {
        console.debug('updated');
    },
}
</script>
