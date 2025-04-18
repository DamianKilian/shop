export function addInputToForm(form, name, value, type = 'hidden') {
    let input = document.createElement('input');
    input.name = name;
    input.value = value;
    input.type = type;
    form.appendChild(input);
}

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
            slug: item.slug,
            id: item.id,
            deleted_at: item.deleted_at,
            filters: item.filters
        });
    });
}

export function setBreadcrumb() {
    this.breadcrumb.push({
        name: __('Main menu'),
        id: this.mainMenuId,
    });
}

export function arraymove(arr, fromIndex, toIndex) {
    var element = arr[fromIndex];
    arr.splice(fromIndex, 1);
    arr.splice(toIndex, 0, element);
}

export function goToSubCategory(e, category) {
    this.breadcrumb.push(category);
    if (!this.categories[category.id]) {
        this.categories[category.id] = [];
    }
}

export function generateUniqueNumber() {
    window.appGeneratedUniqueNumbers = {};
    var un = '';
    do {
        un = Math.random().toString().replace('0.', '');
    } while (window.appGeneratedUniqueNumbers[un]);
    window.appGeneratedUniqueNumbers[un] = un;
    return un;
}
