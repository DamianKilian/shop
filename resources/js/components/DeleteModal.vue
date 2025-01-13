<template>
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5">
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
                        @click="$emit('delete')"
                    >
                        {{ __('Delete') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import AdminPanelAddPage from './AdminPanelAddPage.vue';

export default {
    components: { AdminPanelAddPage },
    props: ['deleteModal'],
};
</script>
