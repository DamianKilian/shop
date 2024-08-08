/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

import './bootstrap';

import { __, toggleMenu, localeSwitcher } from './app-scripts.js';
window.toggleMenu = toggleMenu;
window.localeSwitcher = localeSwitcher;
window.__ = __;

import { createApp } from 'vue';

/**
 * Next, we will create a fresh Vue application instance. You may then begin
 * registering components with the application instance so they are ready
 * to use in your application's views. An example is included for you.
 */

import Root from './components/Root.vue';
const app = createApp(Root);
app.config.globalProperties.__ = window.__;

// import ExampleComponent from './components/ExampleComponent.vue';
// app.component('example-component', ExampleComponent);

import AdminPanelCategories from './components/AdminPanelCategories.vue';
app.component('admin-panel-categories', AdminPanelCategories);
import AdminPanelFilters from './components/AdminPanelFilters.vue';
app.component('admin-panel-filters', AdminPanelFilters);
import AdminPanelProducts from './components/AdminPanelProducts.vue';
app.component('admin-panel-products', AdminPanelProducts);
import Search from './components/Search.vue';
app.component('search', Search);
import SearchFilters from './components/SearchFilters.vue';
app.component('search-filters', SearchFilters);
import PaginationWidget from './components/PaginationWidget.vue';
app.component('pagination-widget', PaginationWidget);
import LoadingOverlay from './components/LoadingOverlay.vue';
app.component('loading-overlay', LoadingOverlay);



/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// Object.entries(import.meta.glob('./**/*.vue', { eager: true })).forEach(([path, definition]) => {
//     app.component(path.split('/').pop().replace(/\.\w+$/, ''), definition.default);
// });

/**
 * Finally, we will attach the application instance to a HTML element with
 * an "id" attribute of "app". This element is included with the "auth"
 * scaffolding. Otherwise, you will need to add an element yourself.
 */

app.mount('#app');
