<template>
    <div id="pages">
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Page title</th>
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
                    <th scope="row">{{ index + 1 }}</th>
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
                    </td>
                </tr>
            </tbody>
        </table>
        <AdminPanelAddPage
            :pageId="pageId"
            :page="page"
            :getPages="getPages"
            :adminPanelAddPageUrl="adminPanelAddPageUrl"
            :adminPanelGetPageUrl="adminPanelGetPageUrl"
        />
    </div>
</template>

<script>
import AdminPanelAddPage from './AdminPanelAddPage.vue';

export default {
    components: { AdminPanelAddPage },
    props: [
        'adminPanelAddPageUrl',
        'adminPanelGetPagesUrl',
        'adminPanelGetPageUrl',
    ],
    data() {
        return {
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
