export default {
    data() {
        return {
            slugCustomized: false,
        };
    },
    methods: {
        editedStringForSlug: function (stringForSlug) {
            if (!this.slugCustomized) {
                this.setSlug(this.generateSlug(stringForSlug));
            }
        },
        generateSlug: function (stringForSlug) {
            return stringForSlug.trim().replace(/ /g, '-');
        },
    },
};
