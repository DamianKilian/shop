<template>
    <div id="admin-panel-settings">
        <div
            v-for="(settingCategory, indexC) in settingCategories"
            :key="settingCategory.id"
            class="settingCategories"
        >
            <h4 class="text-muted text-decoration-underline">
                {{ __(settingCategory.name) }}
            </h4>
            <table
                class="table table-bordered table-hover fs-5"
                id="settings-table"
            >
                <colgroup>
                    <col span="3" />
                    <col span="1" style="min-width: 100px" />
                    <col span="1" style="min-width: 165px" />
                </colgroup>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ __('Setting name') }}</th>
                        <th>{{ __('Setting description') }}</th>
                        <th colspan="2">
                            <button
                                type="button"
                                class="btn btn-warning btn-sm"
                                data-bs-toggle="modal"
                                data-bs-target="#restoreModal"
                                @click="
                                    restoreModal.title = `${__(
                                        'Do you want to restore to default values in'
                                    )}: &quot;${settingCategory.name}&quot;?`;
                                    restoreModal.indexes = {
                                        indexC: indexC,
                                    };
                                "
                            >
                                <i class="fa-solid fa-rotate-left"></i>
                                {{ __('Restore All') }}
                            </button>

                            <button
                                type="button"
                                class="btn btn-secondary btn-sm ms-1"
                                @click="reset(indexC)"
                                data-bs-container="body"
                                data-bs-toggle="popover"
                                data-bs-placement="bottom"
                                data-bs-content="Bottom popover"
                                data-bs-trigger="hover"
                            >
                                <i class="fa-solid fa-broom"></i>
                                {{ __('Reset') }}
                            </button>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="(setting, index) in settingCategory.settings"
                        :key="setting.id"
                        :class="{ 'text-bg-warning': setting.modified }"
                    >
                        <td scope="row">{{ index + 1 }}</td>
                        <td>{{ setting.name }}</td>
                        <td>{{ setting.desc }}</td>
                        <td>
                            <input
                                v-if="'text' === setting.input_type"
                                v-model="setting.value"
                                @input="
                                    settingChange(
                                        setting,
                                        settingCategoriesOrginal[indexC]
                                            .settings[index]
                                    )
                                "
                                class="form-control"
                            />
                            <p v-else-if="'select' === setting.input_type">
                                <select
                                    v-model="setting.value"
                                    @change="
                                        settingChange(
                                            setting,
                                            settingCategoriesOrginal[indexC]
                                                .settings[index]
                                        )
                                    "
                                    class="form-select"
                                >
                                    <option
                                        v-for="option in setting.setting_values"
                                        :value="option.value"
                                    >
                                        {{ option.name }}
                                    </option>
                                </select>
                            </p>
                        </td>
                        <td>
                            <button
                                class="btn btn-sm btn-success"
                                @click="
                                    saveSetting(
                                        setting,
                                        settingCategoriesOrginal[indexC]
                                            .settings[index]
                                    )
                                "
                            >
                                <i class="fa-regular fa-floppy-disk"></i>
                                {{ __('Save') }}
                            </button>
                            <button
                                class="btn btn-sm btn-warning ms-1"
                                data-bs-toggle="modal"
                                data-bs-target="#restoreModal"
                                @click="
                                    restoreModal.title = `${__(
                                        'Do you want to restore to default value'
                                    )}: &quot;${setting.default_value}&quot;?`;
                                    restoreModal.indexes = {
                                        indexC: indexC,
                                        index: index,
                                    };
                                "
                            >
                                <i class="fa-solid fa-rotate-left"></i>
                                {{ __('Restore') }}
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="modal fade" id="restoreModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5">
                        {{ restoreModal.title }}
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
                        ref="restoreCloseModal"
                        type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal"
                    >
                        {{ __('Close') }}
                    </button>
                    <button
                        type="button"
                        class="btn btn-danger"
                        @click="restoreSettings()"
                    >
                        {{ __('Restore') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { forEach } from 'lodash';

export default {
    props: [
        'adminPanelGetSettingsUrl',
        'adminPanelRestoreSettingsUrl',
        'adminPanelSaveSettingUrl',
    ],
    data() {
        return {
            restoreModal: {
                title: '',
                indexes: {},
            },
            settingCategories: [],
            settingCategoriesOrginal: [],
        };
    },
    methods: {
        settingChange: function (setting, settingOrginal) {
            setting.modified = settingOrginal.value !== setting.value;
        },
        reset: function (indexC) {
            console.debug(indexC); //mmmyyy
            var settingsOrginal =
                this.settingCategoriesOrginal[indexC].settings;
            _.forEach(
                this.settingCategories[indexC].settings,
                function (setting, i) {
                    if (setting.modified) {
                        setting.value = settingsOrginal[i].value;
                        setting.modified = false;
                    }
                }
            );
        },
        saveSetting: function (setting, settingOrginal) {
            if (!setting.modified) {
                return;
            }
            axios
                .post(this.adminPanelSaveSettingUrl, {
                    settingId: setting.id,
                    value: setting.value,
                })
                .then(function (response) {
                    setting.modified = false;
                    settingOrginal.value = setting.value;
                });
        },
        restoreSettings: function () {
            var that = this;
            var i = this.restoreModal.indexes;
            var settings = this.settingCategories[i.indexC].settings;
            var settingsOrginal =
                this.settingCategoriesOrginal[i.indexC].settings;
            if (undefined !== i.index) {
                settings = [settings[i.index]];
                settingsOrginal = [settingsOrginal[i.index]];
            }
            axios
                .post(this.adminPanelRestoreSettingsUrl, {
                    settings: settings,
                })
                .then(function (response) {
                    _.forEach(settings, function (setting) {
                        setting.value = setting.default_value;
                    });
                    _.forEach(settingsOrginal, function (settingOrginal) {
                        settingOrginal.value = settingOrginal.default_value;
                    });
                    that.$refs.restoreCloseModal.click();
                });
        },
        getSettings: function () {
            this.settings = [];
            var that = this;
            axios
                .post(this.adminPanelGetSettingsUrl, {})
                .then(function (response) {
                    that.settingCategories = response.data.settingCategories;
                    that.settingCategoriesOrginal = JSON.parse(
                        JSON.stringify(response.data.settingCategories)
                    );
                });
        },
    },
    created() {
        this.getSettings();
    },
};
</script>
