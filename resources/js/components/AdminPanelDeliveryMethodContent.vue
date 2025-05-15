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
                    :value="deliveryMethod.id"
                    @input="$emit('update:modelValue', $event.target.value)"
                />
                {{ deliveryMethod.name }}
            </span>
            <span class="fs-4">{{ deliveryMethod.description }}</span>
            <span class="fs-3">{{ deliveryMethod.price }}</span>
        </div>
    </label>
</template>

<script>
export default {
    props: ['deliveryMethod', 'deliveryMethodChange', 'active', 'modelValue'],
    emits: ['update:modelValue'],
    mounted() {},
};
</script>
