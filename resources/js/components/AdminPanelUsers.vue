<template>
    <div id="admin-panel-users">
        <div v-for="(usersGroup, index) in allUsers" class="usersGroup">
            <h4>{{ index }}</h4>
            <div v-if="index === 'users'" id="users-search">
                <div class="input-group">
                    <input
                        v-model="searchUsersVal"
                        class="form-control"
                        type="search"
                        placeholder="Search ..."
                        aria-label="Search"
                    />
                    <button
                        @click="searchUsers(adminPanelSearchUsersUrl)"
                        class="btn btn-outline-secondary"
                        type="button"
                    >
                        Search
                    </button>
                </div>
            </div>
            <table class="table table-sm table-bordered table-hover fs-5">
                <colgroup>
                    <col style="width: 40px" />
                </colgroup>
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">{{ __('Username') }}</th>
                        <th scope="col">{{ __('Email') }}</th>
                        <th scope="col"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(user, index) in usersGroup" :key="user.id">
                        <td scope="row">{{ index + 1 }}</td>
                        <td>{{ user.name }}</td>
                        <td>{{ user.email }}</td>
                        <td>
                            <button
                                v-if="userId != user.id"
                                class="btn btn-sm ms-1"
                                :class="{
                                    'btn-outline-warning': user.admin,
                                    'btn-warning': !user.admin,
                                }"
                                :disabled="user.setAdminClicked"
                                @click="setAdmin(user)"
                            >
                                <template v-if="user.setAdminClicked">
                                    <span
                                        class="spinner-border spinner-border-sm"
                                        role="status"
                                        aria-hidden="true"
                                    ></span>
                                    Loading...
                                </template>
                                <template v-else>
                                    {{
                                        user.admin
                                            ? __('Unset as Admin')
                                            : __('Set as Admin')
                                    }}
                                </template>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <AdminPanelProductsPagination
            :pagination="pagination"
            :getItems="searchUsers"
            v-if="allUsers.users.length"
        />
    </div>
</template>

<script>
import AdminPanelProductsPagination from './AdminPanelProductsPagination.vue';

export default {
    components: { AdminPanelProductsPagination },
    props: [
        'userId',
        'adminPanelGetUsersUrl',
        'adminPanelSetAdminUrl',
        'adminPanelSearchUsersUrl',
    ],
    data() {
        return {
            searchUsersVal: '',
            pagination: [],
            allUsers: {
                admins: [],
                users: [],
            },
        };
    },
    methods: {
        searchUsers: function (url) {
            this.users = [];
            var that = this;
            axios
                .post(url, {
                    searchUsersVal: this.searchUsersVal,
                })
                .then(function (response) {
                    that.allUsers.users = response.data.users.data;
                    that.pagination = response.data.users;
                    that.arrangeUsers();
                });
        },
        setAdmin: function (user) {
            var admin = !user.admin;
            user.setAminClicked = true;
            axios
                .post(this.adminPanelSetAdminUrl, {
                    userId: user.id,
                    admin: admin,
                })
                .then(function (response) {
                    user.admin = admin;
                    user.setAminClicked = false;
                });
        },
        getUsers: function () {
            var that = this;
            axios
                .post(this.adminPanelGetUsersUrl, {})
                .then(function (response) {
                    that.allUsers.admins = response.data.allUsers.admins;
                    that.allUsers.users = response.data.allUsers.users.data;
                    that.pagination = response.data.allUsers.users;
                    that.arrangeUsers();
                });
        },
        arrangeUsers: function () {
            var that = this;
            _.forEach(this.allUsers, function (usersGroup, groupIndex) {
                _.forEach(usersGroup, function (user) {
                    if ('admins' === groupIndex) {
                        user.admin = true;
                    } else {
                        user.admin = false;
                    }
                    user.setAdminClicked = false;
                });
            });
        },
    },
    created() {
        this.getUsers();
    },
    updated() {},
    mounted() {},
};
</script>
