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
                            <div v-if="userId != user.id" class="btn-group">
                                <button
                                    type="button"
                                    class="btn btn-sm btn-secondary dropdown-toggle ms-1"
                                    data-bs-toggle="dropdown"
                                >
                                    {{ __('Permissions') }}
                                </button>
                                <div
                                    class="dropdown-menu dropdown-menu-end ps-2"
                                >
                                    <div
                                        v-for="p in permissions"
                                        :key="p.id"
                                        class="form-check"
                                    >
                                        <input
                                            v-if="
                                                !(
                                                    userId == user.id &&
                                                    'admin' == p.name
                                                )
                                            "
                                            :checked="hasPermission(p, user)"
                                            @change="
                                                setPermission($event, p, user)
                                            "
                                            class="form-check-input"
                                            type="checkbox"
                                            value=""
                                            :id="p.name"
                                        />
                                        <label
                                            class="form-check-label"
                                            :for="p.name"
                                        >
                                            {{ p.name }}
                                        </label>
                                    </div>
                                    <div
                                        v-if="user.permissionClicked"
                                        class="spinner-wrapper"
                                    >
                                        <div
                                            class="d-flex justify-content-center"
                                        >
                                            <div
                                                class="spinner-border"
                                                role="status"
                                            >
                                                <span class="visually-hidden"
                                                    >Loading...</span
                                                >
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
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
        'permissions',
        'userId',
        'adminPanelSetPermissionUrl',
        'adminPanelGetUsersUrl',
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
        setPermission: function (e, permission, user) {
            user.permissionClicked = true;
            var that = this;
            axios
                .post(this.adminPanelSetPermissionUrl, {
                    userId: user.id,
                    permission: {
                        id: permission.id,
                        checked: e.target.checked,
                    },
                })
                .then(function (response) {
                    user.permissionClicked = false;
                });
        },
        hasPermission: function (permission, user) {
            var hasPermission = false;
            _.forEach(user.permissions, function (upn) {
                if (upn.id === permission.id) {
                    hasPermission = true;
                    return false;
                }
            });
            return hasPermission;
        },
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
                    user.permissionClicked = false;
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
