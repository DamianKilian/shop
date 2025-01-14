<template>
    <div id="admin-panel-footer" class="ms-3">
        <h3>{{ __('Footer HTML') }}</h3>
        <div
            id="editorjs"
            class="border color bg-white padding-form-control"
            @click="success = ''"
        ></div>
        <div class="btn-group mt-1 float-end">
            <button
                type="button"
                class="btn btn-outline-primary"
                @click="saveFooter($event, 'html_preview')"
            >
                {{ __('Refresh') }}
            </button>
            <button
                type="button"
                class="btn btn-outline-primary"
                @click="previewPage"
                :disabled="!previewPageUrl"
            >
                <i class="fa-solid fa-eye"></i>
                {{ __('Preview') }}
            </button>
        </div>
        <div v-if="success" class="text-bg-success float-end mt-1 me-1">
            {{ __(success) }}
        </div>
        <button
            v-else
            @click="saveFooter($event)"
            class="btn btn-primary float-end mt-1 me-1"
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
            previewPageUrl: '',
        };
    },
    methods: {
        previewPage: function () {
            window.open(this.previewPageUrl, '_blank').focus();
        },
        saveFooter: function (e, dataKey = 'html') {
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
                            dataKey: dataKey,
                            footerHtml: footerHtml,
                        })
                        .then(function (response) {
                            that.success = __('Saved!');
                            that.previewPageUrl = response.data.previewUrl;
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
