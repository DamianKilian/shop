export default {
    data() {
        return {
            fromIndex: null,
            yOld: null,
            xOld: null,
            dragDirOld: 'top',
            draggedItem: null,
        };
    },
    methods: {
        dragstart: function (e) {
            this.draggedItem = e.target;
            this.fromIndex = [...this.$refs.sortable.children].indexOf(this.draggedItem);
            setTimeout(() => {
                e.target.style.color = "#ddd";
                e.target.style['background-color'] = "#ddd";
            }, 0);
        },
        dragend: function (e, itemsArr = 'itemsArr') {
            setTimeout(() => {
                e.target.style.color = "";
                e.target.style['background-color'] = "";
                this.draggedItem = null;
            }, 0);
            let toIndex = [...this.$refs.sortable.children].indexOf(this.draggedItem);
            if (null !== this.fromIndex && this.fromIndex !== toIndex) {
                this.arraymove(this[itemsArr], this.fromIndex, toIndex);
                this.fromIndex = null;
            }
        },
        dragover: _.debounce(function (e, horizontally = false) {
            if (!this.draggedItem) {
                return;
            }
            e.preventDefault();
            if (horizontally) {
                var afterElement = this.getDragAfterElementHorizontally(e.clientX);
                this.xOld = e.clientX;
            } else {
                var afterElement = this.getDragAfterElement(e.clientY);
                this.yOld = e.clientY;
            }
            if (afterElement == null) {
                this.$refs.sortable.appendChild(this.draggedItem);
            } else if (null !== this.draggedItem) {
                this.$refs.sortable.insertBefore(
                    this.draggedItem,
                    afterElement
                );
            }
        }, 15),
        getDragAfterElement: function (y) {
            const draggableElements = [
                ...this.$refs.sortable.querySelectorAll(
                    ".draggableElement:not(.dragging)"
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
        getDragAfterElementHorizontally: function (x) {
            const draggableElements = [
                ...this.$refs.sortable.querySelectorAll(
                    ".draggableElement:not(.dragging)"
                ),
            ];
            return draggableElements.reduce((closest, child) => {
                const box = child.getBoundingClientRect();
                var dragDir = null !== this.xOld && (this.xOld - x) > 0 ? 'left' : 'right';
                if ((this.xOld - x) === 0) {
                    dragDir = this.dragDirOld;
                }
                this.dragDirOld = dragDir;
                const offset = x - box[dragDir] + box.width * ('left' === dragDir ? -1 : 1);
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
};
