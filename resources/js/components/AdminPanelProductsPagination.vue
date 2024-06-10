<template>
    <div class="ap-products-pagination mt-4">
        <nav aria-label="Page navigation example">
            <ul v-if='pagination' class="pagination justify-content-center">
                <li v-for="(link, i) in pagination.links" :class='{ active: link.active, disabled: !link.url }'
                    class="page-item">
                    <a @click.prevent='getProducts(selectedCategory, link.url)' :href="link.url" class="page-link"
                        v-html='link.label'></a>
                </li>
            </ul>
        </nav>
    </div>
</template>

<script>

export default {
    props: ['pagination', 'selectedCategory', 'getProducts'],
    data() {
        return {
        }
    },
    methods: {
    },
    created() {
    },
    mounted() {
    }
}
</script>
