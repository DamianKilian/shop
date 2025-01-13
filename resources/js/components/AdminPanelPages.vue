<template>
    <div id="admin-panel-pages">
        <table class="table table-bordered table-hover fs-5">
            <colgroup>
                <col style="width: 40px" />
            </colgroup>
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">{{ __('Page title') }}</th>
                    <th scope="col">
                        <button
                            @click="pageId = null"
                            data-bs-toggle="modal"
                            data-bs-target="#addPage"
                            class="btn btn-success float-end mt-1 mt-sm-0"
                        >
                            <i class="fa-solid fa-plus"></i>
                            {{ __('Add page') }}
                        </button>
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(page, index) in pages" :key="page.id">
                    <td scope="row">{{ index + 1 }}</td>
                    <td>{{ page.title }}</td>
                    <td>
                        <button
                            @click="pageId = page.id"
                            data-bs-toggle="modal"
                            data-bs-target="#addPage"
                            class="btn btn-primary btn-sm"
                        >
                            {{ __('Edit') }}
                        </button>
                        <button
                            class="btn btn-success btn-sm ms-1"
                            :disabled="page.applyChangesClicked"
                            @click="applyChanges(page)"
                        >
                            <template v-if="page.applyChangesClicked">
                                <span
                                    class="spinner-border spinner-border-sm"
                                    role="status"
                                    aria-hidden="true"
                                ></span>
                                Loading...
                            </template>
                            <template v-else>
                                {{ __('Apply changes') }}
                            </template>
                        </button>
                        <button
                            class="btn btn-sm ms-1"
                            :class="{
                                'btn-outline-warning': page.active,
                                'btn-warning': !page.active,
                            }"
                            :disabled="page.toggleActiveClicked"
                            @click="toggleActive(page)"
                        >
                            <template v-if="page.toggleActiveClicked">
                                <span
                                    class="spinner-border spinner-border-sm"
                                    role="status"
                                    aria-hidden="true"
                                ></span>
                                Loading...
                            </template>
                            <template v-else>
                                {{
                                    page.active
                                        ? __('Disactivate')
                                        : __('Activate')
                                }}
                            </template>
                        </button>
                        <button
                            class="btn btn-danger btn-sm ms-1"
                            data-bs-toggle="modal"
                            data-bs-target="#deleteModal"
                            @click="
                                deleteModal.title = `${__(
                                    'Do you want to delete'
                                )}: &quot;${page.title}&quot;`;
                                deleteModal.pageId = page.id;
                            "
                        >
                            {{ __('Delete') }}
                        </button>
                        <a
                            :href="'/' + (page.slug || '')"
                            class="btn btn-light btn-sm float-end"
                            target="_blank"
                        >
                            {{ __('Show') }}
                            <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </td>
                </tr>
            </tbody>
        </table>
        <DeleteModal
            :deleteModal="deleteModal"
            @delete="deletePage"
        />
        <AdminPanelAddPage
            :pageId="pageId"
            :page="page"
            :getPages="getPages"
            :adminPanelAddPageUrl="adminPanelAddPageUrl"
            :adminPanelGetPageUrl="adminPanelGetPageUrl"
            :adminPanelDeletePageUrl="adminPanelDeletePageUrl"
            :adminPanelFetchUrlUrl="adminPanelFetchUrlUrl"
            :adminPanelUploadFileUrl="adminPanelUploadFileUrl"
            :adminPanelUploadAttachmentUrl="adminPanelUploadAttachmentUrl"
        />
    </div>
</template>

<script>
import AdminPanelAddPage from './AdminPanelAddPage.vue';
import DeleteModal from './DeleteModal.vue';

export default {
    components: { AdminPanelAddPage, DeleteModal },
    props: [
        'adminPanelFetchUrlUrl',
        'adminPanelUploadFileUrl',
        'adminPanelUploadAttachmentUrl',
        'adminPanelToggleActiveUrl',
        'adminPanelApplyChangesUrl',
        'adminPanelDeletePageUrl',
        'adminPanelAddPageUrl',
        'adminPanelGetPagesUrl',
        'adminPanelGetPageUrl',
    ],
    data() {
        return {
            deleteModal: {
                title: '',
                pageId: null,
            },
            pageId: null,
            pages: [],
            page: {
                title: '',
                slug: '',
                slugCustomized: false,
            },
        };
    },
    methods: {
        toggleActive: function (page) {
            var active = !page.active;
            var that = this;
            page.toggleActiveClicked = true;
            axios
                .post(this.adminPanelToggleActiveUrl, {
                    pageId: page.id,
                    active: active,
                })
                .then(function (response) {
                    page.active = active;
                    page.toggleActiveClicked = false;
                });
        },
        applyChanges: function (page) {
            var that = this;
            page.applyChangesClicked = true;
            axios
                .post(this.adminPanelApplyChangesUrl, { pageId: page.id })
                .then(function (response) {
                    page.applyChangesClicked = false;
                });
        },
        deletePage: function () {
            var that = this;
            axios
                .post(this.adminPanelDeletePageUrl, { pageId: this.deleteModal.pageId })
                .then(function (response) {
                    that.getPages();
                    that.$refs.deleteModal.$refs.deleteCloseModal.click();
                });
        },
        getPages: function (url = this.adminPanelGetPagesUrl) {
            this.pages = [];
            var that = this;
            axios.post(url, {}).then(function (response) {
                that.arrangePages(response.data.pages);
            });
        },
        arrangePages: function (pages) {
            var that = this;
            _.forEach(pages, function (page) {
                page.applyChangesClicked = false;
                page.toggleActiveClicked = false;
                that.pages.push(page);
            });
        },
    },
    created() {
        this.getPages();
    },
    updated() {},
    mounted() {},
};
</script>
