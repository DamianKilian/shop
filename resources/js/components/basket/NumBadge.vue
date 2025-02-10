<template>
    <span
        v-if="Object.keys(productsInBasket).length"
        class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
    >
        {{ Object.keys(productsInBasket).length }}
    </span>
</template>

<script>
export default {
    props: ['productsInBasket'],
    mounted() {},
};
</script>
