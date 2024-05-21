<template>
    <ul id="sortable" class="app-list-ul" @dragstart="dragstart" @dragend="dragend($event, 'currentCategories')"
        @dragover="dragover" ref="sortable">
        <template v-for="(category, index) in currentCategories" :key="category.id">
            <li :set="removeStatus = category.remove || (category.deleted_at && !category.restore)"
                v-if="!category.deleted_at || category.restore || showDeletedCategories"
                :class="{ 'new-category': category.new, 'text-bg-danger': removeStatus, 'mb-4': category.failedValidation }"
                class="d-flex draggableElement" draggable="true">
                {{ category.name }}&nbsp;
                <div class="btn-group btn-group-sm float-end" role="group" aria-label="Small button group">
                    <button v-if='!removeStatus' class="btn btn-secondary"
                        @click="showEditCategoryNameInput($event, category)"><i
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
    <div id="editCategoryName" class="input-group mb-3 edit-category d-none">
        <input type="text" class="form-control" :value="editCategoryData.category ? editCategoryData.category.name : ''"
            @input="if (editCategoryData.category) editCategoryData.category.name = $event.target.value;">
    </div>
</template>

<script>
import { goToSubCategory, arraymove } from './commonFunctions.js'
import sortable from "./sortable.js";

export default {
    mixins: [sortable],
    props: ['currentCategories', 'breadcrumb', 'categories', 'showDeletedCategories'],
    data() {
        return {
            editCategoryData: {}
        }
    },
    methods: {
        goToSubCategory,
        arraymove,
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
        showEditCategoryNameInput: function (e, category) {
            let editCategoryName = this.editCategoryData.editCategoryName = document.getElementById('editCategoryName');
            let li = this.editCategoryData.li = e.target.closest('li');
            this.editCategoryData.category = category;
            li.prepend(editCategoryName);
            li.setAttribute('draggable', false);
            editCategoryName.classList.remove('d-none');
        },
        editCategoryName: function () {
            let editCategoryName = this.editCategoryData.editCategoryName;
            document.getElementById('sortable').after(editCategoryName);
            this.editCategoryData.li.setAttribute('draggable', true);
            editCategoryName.classList.add('d-none');
            this.editCategoryData = {};
        },
    },
    created() {
        document.addEventListener('click', (e) => {
            if (_.isEmpty(this.editCategoryData)) {
                return;
            }
            let sortableLi = e.target.closest('#sortable li');
            if (!sortableLi || !sortableLi.querySelector("#editCategoryName")) {
                this.editCategoryName();
            }
        });
    },
    mounted() {
        document.querySelector("#editCategoryName > input").addEventListener('keypress', (e) => {
            if (e.keyCode !== 13) {
                return;
            }
            this.editCategoryName();
        });
    },
    updated() {
        console.debug('updated');//mmmyyy
    },
}
</script>
