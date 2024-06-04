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
        this.$refs.clear.classList.remove("d-none");

    }
}
</script>