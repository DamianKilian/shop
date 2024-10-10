export default {
    data() {
        return {
            slug: '',
            slugCustomized: false,
        };
    },
    methods: {
        editedStringForSlug: function (stringForSlug) {
            if (!this.slugCustomized) {
                this.slug = this.generateSlug(stringForSlug);
            }
        },
        generateSlug: function (stringForSlug) {
            return stringForSlug.trim().replace(/ /g, '-');
        },
    },
};
