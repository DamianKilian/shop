<template>
    <label
        class="list-group-item list-group-item-action"
        :class="{ active: active }"
    >
        <div class="d-flex w-100 justify-content-between">
            <span class="fs-3">
                <input
                    v-if="deliveryMethodChange"
                    @change="deliveryMethodChange"
                    class="form-check-input"
                    type="radio"
                    name="deliveryMethod"
                />
                {{ deliveryMethod.name }}
            </span>
            <span class="fs-4">{{ deliveryMethod.description }}</span>
            <span class="fs-3">{{ deliveryMethod.price }}</span>
        </div>
    </label>
</template>

<script>
// v-for="(method, id) in deliveryMethods"
export default {
    props: ['deliveryMethod', 'deliveryMethodChange', 'active'],
    mounted() {},
};
</script>
