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

export function getSelectedCategory() {
    var selectedCategory = null;
    _.forEach(this.currentCategories, function (category, key) {
        if (category.selected) {
            selectedCategory = category;
            return;
        }
    });
    return selectedCategory;
}

export function getCategoryProducts(e, category = null) {
    if (!e.target.classList.contains('show-products')) {
        return;
    }
    if (this.selectedCategory) {
        if (category.id === this.selectedCategory.id) {
            category.selected = false;
        } else {
            _.forEach(this.currentCategories, function (val, key) {
                if (val.selected) {
                    val.selected = false;
                    return;
                }
            });
            category.selected = true;
        }
    } else {
        category.selected = true;
    }
    this.getProducts(category);
}

export function getProducts(category) {
    var that = this;
    axios
        .post(this.adminPanelGetProductsUrl, { category: category })
        .then(function (response) {
            arrangeProducts.call(that, response.data);
        })
        .catch(function (error) {
        });
}

function arrangeProducts(data) {
    this.products = data.products;
}
