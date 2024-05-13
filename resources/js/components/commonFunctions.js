export function goToCategory(e, index) {
    if (1 === this.breadcrumb.length) {
        return;
    }
    this.breadcrumb = this.breadcrumb.slice(0, index + 1);
}

export function arrangeCategories(categoriesDb = null) {
    categoriesDb = categoriesDb || this.categoriesProp;
    this.categories = {
        [this.mainMenuId]: [],
    };
    if (!categoriesDb) {
        return;
    }
    categoriesDb.forEach((item) => {
        item.parent_id = item.parent_id || this.mainMenuId;
        if (!this.categories[item.parent_id]) {
            this.categories[item.parent_id] = [];
        }
        this.categories[item.parent_id].push({
            name: item.name,
            id: item.id,
            deleted_at: item.deleted_at,
        });
    });
}

export function setBreadcrumb() {
    this.breadcrumb.push({
        name: __('Main menu'),
        id: this.mainMenuId,
    });
}

export function goToSubCategory(e, category) {
    this.breadcrumb.push(category);
    if (!this.categories[category.id]) {
        this.categories[category.id] = [];
    }
}
