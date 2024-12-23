<template>
    <div id="admin-panel-footer" class="ms-3">
        <h3>{{ __('Footer HTML') }}</h3>
        <div
            id="editorjs"
            class="border color bg-white padding-form-control"
            @click="success = ''"
        ></div>
        <div v-if="success" class="text-bg-success float-end mt-1">
            {{ __(success) }}
        </div>
        <button
            v-else
            @click="saveFooter"
            class="btn btn-primary mt-1 float-end"
        >
            {{ __('Save') }}
        </button>
    </div>
</template>

<script>
import RawTool from '@editorjs/raw';
import EditorJS from '@editorjs/editorjs';

export default {
    props: ['adminPanelGetFooterUrl', 'adminPanelSaveFooterUrl'],
    data() {
        return {
            editor: null,
            success: '',
        };
    },
    methods: {
        saveFooter: function () {
            var that = this;
            this.editor
                .save()
                .then((data) => {
                    let footerHtml = '';
                    if (data.blocks.length) {
                        footerHtml = JSON.stringify(data);
                    }
                    axios
                        .post(this.adminPanelSaveFooterUrl, {
                            footerHtml: footerHtml,
                        })
                        .then(function (response) {
                            that.success = __('Saved!');
                        })
                        .catch(function (error) {});
                })
                .catch((error) => {
                    console.log('Saving failed: ', error);
                });
        },
        getFooter: function () {
            var that = this;
            axios.post(this.adminPanelGetFooterUrl).then(function (response) {
                if (response.data.footerHtml) {
                    that.editor.blocks.render(
                        JSON.parse(response.data.footerHtml)
                    );
                }
            });
        },
    },
    mounted() {
        this.editor = new EditorJS({
            minHeight: 50,
            tools: {
                raw: RawTool,
            },
        });
        this.getFooter();
    },
};
</script>
