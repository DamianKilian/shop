<template>
    <form
        class="order-1 pt-2 pb-2 d-flex justify-content-center m-auto"
        role="search"
        id="search"
    >
        <div class="d-flex align-items-center search-icon">
            <i class="fa-solid fa-magnifying-glass"></i>
        </div>
        <div class="input-group search-container">
            <input
                @keypress.enter.prevent="$emit('search', searchValue)"
                @keydown.arrow-down.prevent="suggestionSelect(1)"
                @keydown.arrow-up.prevent="suggestionSelect(-1)"
                @input="getSuggestions"
                @click="getSuggestions"
                v-model="searchValue"
                class="form-control"
                type="search"
                :placeholder="__('Search') + categoryNameText() + ' ...'"
                aria-label="Search"
            />
            <button
                @click="$emit('search', searchValue)"
                class="btn btn-outline-secondary"
                type="button"
            >
                {{ __('Search') }}
            </button>
            <div v-if="suggestions.length" id="suggestions-container">
                <div id="suggestions" class="border">
                    <div
                        v-if='suggestions[0]'
                        v-for="suggestion in suggestions"
                        @click="suggestionSelectClick(suggestion)"
                        ref="suggestions"
                        :key="suggestion.id"
                        :data-value="suggestion.suggestion"
                        class="suggestion p-1"
                    >
                        <i class="fa-solid fa-magnifying-glass"></i>
                        {{ suggestion.suggestion }}
                    </div>
                    <div
                        v-else
                        class="suggestion p-1"
                    >
                        <i class='text-muted'>{{__('No suggestions')}}</i>
                    </div>
                </div>
            </div>
        </div>
        <button
            v-show="searchValue"
            @click="searchValue = ''"
            ref="clear"
            type="button"
            class="btn btn-danger ms-1 d-none"
        >
            <i class="fa-solid fa-xmark"></i>
        </button>
    </form>
</template>

<script>
export default {
    props: ['getSuggestionsUrl', 'categoryName'],
    data() {
        return {
            selectedSuggestion: -1,
            suggestions: [],
            searchValue: '',
            searchValueEntered: '',
        };
    },
    methods: {
        suggestionSelectClick(suggestion) {
            this.searchValue = suggestion.suggestion;
            this.$emit('search', this.searchValue);
            this.suggestions = [];
            this.searchValueEntered = this.searchValue;
        },
        suggestionSelect(change) {
            if (!this.$refs.suggestions) {
                return;
            }
            var suggestionsNum = this.$refs.suggestions.length - 1;
            var selected = this.selectedSuggestion + change;
            if (-2 === selected) {
                selected = suggestionsNum;
            } else if (suggestionsNum < selected) {
                selected = -1;
            }
            var selectedEl = this.$refs.suggestions[selected];
            if (-1 !== this.selectedSuggestion) {
                this.$refs.suggestions[
                    this.selectedSuggestion
                ].classList.remove('active');
            }
            if (-1 === selected) {
                this.searchValue = this.searchValueEntered;
            } else {
                selectedEl.classList.add('active');
                this.searchValue = selectedEl.dataset.value;
            }
            this.selectedSuggestion = selected;
        },
        categoryNameText: function () {
            return this.categoryName
                ? ' ' + __('in') + ' "' + this.categoryName + '"'
                : '';
        },
        getSuggestions: _.debounce(function () {
            if (!this.getSuggestionsUrl || !this.searchValue) {
                return;
            }
            if (
                this.suggestions.length &&
                this.searchValueEntered === this.searchValue
            ) {
                return;
            }
            this.searchValueEntered = this.searchValue;
            var that = this;
            axios
                .post(this.getSuggestionsUrl, {
                    searchValue: that.searchValue,
                })
                .then(function (response) {
                    that.suggestions = response.data.suggestions;
                    if(!that.suggestions.length){
                        that.suggestions.push('');
                    }
                });
        }, 500),
    },
    mounted() {
        var that = this;
        if (this.$refs.clear) {
            this.$refs.clear.classList.remove('d-none');
        }
        document.addEventListener('click', function (e) {
            if (!e.target.closest('.search-container') || e.target.classList.contains('btn')) {
                that.suggestions = [];
            }
        });
    },
};
</script>
