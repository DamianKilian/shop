<template>
    <div id="pages">
        <table class="table table-bordered table-hover fs-4">
            <colgroup>
                <col style="width: 40px;">
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
                            class="btn btn-primary"
                        >
                            {{ __('Edit') }}
                        </button>
                        <button
                            class="btn btn-danger ms-1"
                            data-bs-toggle="modal"
                            data-bs-target="#exampleModal"
                            @click="
                                deleteModal.title = `${__(
                                    'Do you want to delete'
                                )}: &quot;${page.title}&quot;`;
                                deleteModal.pageId = page.id;
                            "
                        >
                            {{ __('Delete') }}
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
        <div
            class="modal fade"
            id="exampleModal"
            tabindex="-1"
            aria-labelledby="exampleModalLabel"
            aria-hidden="true"
        >
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">
                            {{ deleteModal.title }}
                        </h1>
                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="Close"
                        ></button>
                    </div>
                    <div class="modal-footer">
                        <button
                            ref="deleteCloseModal"
                            type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal"
                        >
                            {{ __('Close') }}
                        </button>
                        <button
                            type="button"
                            class="btn btn-danger"
                            @click="deletePage(deleteModal.pageId)"
                        >
                            {{ __('Delete') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <AdminPanelAddPage
            :pageId="pageId"
            :page="page"
            :getPages="getPages"
            :adminPanelAddPageUrl="adminPanelAddPageUrl"
            :adminPanelGetPageUrl="adminPanelGetPageUrl"
            :adminPanelDeletePageUrl="adminPanelDeletePageUrl"
        />
    </div>
</template>

<script>
import AdminPanelAddPage from './AdminPanelAddPage.vue';

export default {
    components: { AdminPanelAddPage },
    props: [
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
        deletePage: function (pageId) {
            var that = this;
            axios
                .post(this.adminPanelDeletePageUrl, { pageId: pageId })
                .then(function (response) {
                    that.getPages();
                    that.$refs.deleteCloseModal.click();
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
