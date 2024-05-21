<template>
    <div id="drop-area" @dragenter='dragenterHandler' @dragover='dragoverHandler' @dragleave='dragleaveHandler'
        @drop='dropHandler' :class="{ highlight: dropAreaHighlight }" class='border border-2 mb-3 padding-form-control'>
        <div class="content">
            <div class="mb-3">
                <label for="files" class="form-label"><b>{{ __('Photos') }}</b></label>
                <input @input='dropHandler' ref="fileInput" type="file" id="files" name='files[]' multiple
                    accept="image/*" class="form-control">
            </div>
        </div>
        <div class='files-preview d-flex flex-row' @dragstart="dragstart" @dragend="dragend($event, 'filesArr')"
            @dragover="dragover($event, true)" ref="sortable">
            <div v-if="newFiles" v-for="(file, index) in filesArr" :key="file.src"
                class="file-preview me-1 draggableElement" draggable="true">
                <img class="preview-img mw-100 mh-100 new-element" :src="file.src" style='pointer-events: none;'
                    :data-position-in-input="file.positionInInput" />
            </div>
        </div>
    </div>
</template>

<script>
import sortable from "./sortable.js";
import { arraymove } from './commonFunctions.js'

export default {
    mixins: [sortable],
    props: ['validationError', 'filesArr'],
    data() {
        return {
            dropAreaHighlight: false,
            currFiles: this.currFilesProp,
            newFiles: null,
        }
    },
    methods: {
        arraymove,
        dragenterHandler: function (e) {
            this.preventDefaults(e);
            this.dropAreaHighlight = true;
        },
        dragoverHandler: function (e) {
            this.preventDefaults(e);
            this.dropAreaHighlight = true;
        },
        dragleaveHandler: function (e) {
            this.preventDefaults(e);
            this.dropAreaHighlight = false;
        },
        dropHandler: function (e) {
            this.preventDefaults(e);
            this.dropAreaHighlight = false;
            if (!this.newFiles) {
                this.newFiles = new DataTransfer();
            }
            if (e.dataTransfer) {
                [...e.dataTransfer.files].forEach((file) => {
                    this.newFiles.items.add(file);
                    this.generateURL(file);
                    this.filesArr.push({
                        src: file.src,
                        positionInInput: this.newFiles.items.length - 1
                    });
                });
            } else {
                [...this.$refs.fileInput.files].forEach((file) => {
                    this.newFiles.items.add(file);
                    this.generateURL(file);
                    this.filesArr.push({
                        src: file.src,
                        positionInInput: this.newFiles.items.length - 1
                    });
                });
            }
            this.$refs.fileInput.files = this.newFiles.files;
        },
        generateURL: function (file) {
            file.src = URL.createObjectURL(file);
            setTimeout(() => {
                URL.revokeObjectURL(file.src);
            }, 1000);
        },
        preventDefaults: function (e) {
            e.preventDefault();
            e.stopPropagation();
        },
    },
    updated() { },
    created() { },
    mounted() { }
}
</script>
