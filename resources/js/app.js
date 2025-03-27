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
if ('production' === window.appEnv) {
  app.config.errorHandler = (err, vm, info) => {
      var logData = {
          framework: 'vue',
          message: err.message,
          info: info,
          stack: err.stack,
      };
      window.logJs(logData);
  };
}
app.config.globalProperties.__ = window.__;

// import ExampleComponent from './components/ExampleComponent.vue';
// app.component('example-component', ExampleComponent);

import AdminPanelCategories from './components/AdminPanelCategories.vue';
app.component('admin-panel-categories', AdminPanelCategories);
import AdminPanelFilters from './components/AdminPanelFilters.vue';
app.component('admin-panel-filters', AdminPanelFilters);
import AdminPanelPages from './components/AdminPanelPages.vue';
app.component('admin-panel-pages', AdminPanelPages);
import AdminPanelProducts from './components/AdminPanelProducts.vue';
app.component('admin-panel-products', AdminPanelProducts);
import PaginationWidget from './components/PaginationWidget.vue';
app.component('pagination-widget', PaginationWidget);
import LoadingOverlay from './components/LoadingOverlay.vue';
app.component('loading-overlay', LoadingOverlay);
import AdminPanelSettings from './components/AdminPanelSettings.vue'
app.component('admin-panel-settings', AdminPanelSettings);
import AdminPanelFooter from './components/AdminPanelFooter.vue'
app.component('admin-panel-footer', AdminPanelFooter);
import AdminPanelUsers from './components/AdminPanelUsers.vue';
app.component('admin-panel-users', AdminPanelUsers);

//basket
import NumBadge from './components/basket/NumBadge.vue';
app.component('num-badge', NumBadge);
import basketIndex from './components/basket/Index.vue';
app.component('basket-index', basketIndex);
import basketPayment from './components/basket/Payment.vue';
app.component('basket-payment', basketPayment);

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
