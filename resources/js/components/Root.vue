<script>
export default {
    data() {
        return {
            searchValue: '',
        }
    },
    methods: {
    },
    updated() {
        console.log('updated-root')
    },
    created() { },
    mounted() {
        if (this.$refs.clear) {
            this.$refs.clear.classList.remove("d-none");
        }
    }
}
</script>