<template>
    <ul id="sortable" class="app-list-ul" @dragstart="dragstart" @dragend="dragend" @dragover="dragover" ref="sortable">
        <div id="editCategoryName" class="input-group mb-3 edit-category d-none">
            <input type="text" class="form-control"
                :value="editCategoryData.category ? editCategoryData.category.name : ''"
                @input="if (editCategoryData.category) editCategoryData.category.name = $event.target.value;">
        </div>
        <li v-for="(category, index) in currentCategories" :key="category.id" draggable="true"
            :class="{ 'new-category': category.new, 'text-bg-danger': category.removed, 'mb-4': category.failedValidation }"
            class="d-flex">
            {{ category.name }}&nbsp;
            <div class="btn-group btn-group-sm float-end" role="group" aria-label="Small button group">
                <button class="btn btn-secondary" @click="showEditCategoryNameInput($event, category)"><i
                        class="fa-solid fa-pen-to-square"></i></button>
                <button class="btn btn-secondary" @click="removeCategory($event, index, category)"><i
                        class="fa-solid fa-xmark"></i></button>
                <button class="btn btn-secondary" @click="goToSubCategory($event, category)"><i
                        class="fa-solid fa-arrow-right"></i></button>
            </div>
            <div v-if="category.failedValidation" class="text-danger"
                style="position: absolute;bottom: -19px;z-index: 5;">{{
                    category.failedValidation[0] }}</div>
        </li>
    </ul>
</template>

<script>
export default {
    props: ['currentCategories', 'breadcrumb', 'categories'],
    data() {
        return {
            fromIndex: null,
            yOld: null,
            dragDirOld: 'top',
            draggedItem: null,
            editCategoryData: {}
        }
    },
    methods: {
        goToSubCategory: function (e, category) {
            this.breadcrumb.push(category);
            if (!this.categories[category.id]) {
                this.categories[category.id] = [];
            }
        },
        arraymove: function (arr, fromIndex, toIndex) {
            var element = arr[fromIndex];
            arr.splice(fromIndex, 1);
            arr.splice(toIndex, 0, element);
        },
        removeCategory: function (e, index, category) {
            if (category.new) {
                this.currentCategories.splice(index, 1);
            } else if (!category.removed) {
                category.removed = true;
            } else {
                category.removed = false;
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
            document.getElementById('sortable').prepend(editCategoryName);
            this.editCategoryData.li.setAttribute('draggable', true);
            editCategoryName.classList.add('d-none');
            this.editCategoryData = {};
        },
        dragstart: function (e) {
            this.draggedItem = e.target;
            this.fromIndex = [...this.$refs.sortable.children].indexOf(this.draggedItem) - 1;
            setTimeout(() => {
                e.target.style.color = "#ddd";
                e.target.style['background-color'] = "#ddd";
            }, 0);
        },
        dragend: function (e) {
            setTimeout(() => {
                e.target.style.color = "";
                e.target.style['background-color'] = "";
                this.draggedItem = null;
            }, 0);
            let toIndex = [...this.$refs.sortable.children].indexOf(this.draggedItem) - 1;
            if (null !== this.fromIndex && this.fromIndex !== toIndex) {
                this.arraymove(this.currentCategories, this.fromIndex, toIndex);
                this.fromIndex = null;
            }
        },
        dragover: _.debounce(function (e) {
            e.preventDefault();
            const afterElement = this.getDragAfterElement(e.clientY);
            this.yOld = e.clientY;
            if (afterElement == null) {
                this.$refs.sortable.appendChild(this.draggedItem);
            } else if (null !== this.draggedItem) {
                this.$refs.sortable.insertBefore(this.draggedItem, afterElement);
            }
        }, 15),
        getDragAfterElement: function (y) {
            const draggableElements = [
                ...this.$refs.sortable.querySelectorAll(
                    "li:not(.dragging)"
                ),
            ];
            return draggableElements.reduce((closest, child) => {
                const box = child.getBoundingClientRect();
                var dragDir = null !== this.yOld && (this.yOld - y) > 0 ? 'top' : 'bottom';
                if ((this.yOld - y) === 0) {
                    dragDir = this.dragDirOld;
                }
                this.dragDirOld = dragDir;
                const offset = y - box[dragDir] + box.height * ('top' === dragDir ? -1 : 1);
                // const offset = y - box.top - box.height / 2;
                if (offset < 0 && offset > closest.offset) {
                    return {
                        offset: offset,
                        element: child,
                    };
                } else {
                    return closest;
                }
            }, {
                offset: Number.NEGATIVE_INFINITY,
            }).element;
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
}
</script>
