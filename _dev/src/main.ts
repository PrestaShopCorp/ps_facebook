import Vue from 'vue';
import {BootstrapVue} from 'bootstrap-vue';
import psAccountsVueComponents from 'prestashop_accounts_vue_components';
import router from './router';
import store from './store';
import App from './App.vue';
import i18n from './lib/i18n';

Vue.config.productionTip = false;
Vue.use(BootstrapVue);
Vue.use(psAccountsVueComponents);

new Vue({
  router,
  store,
  i18n,
  render: (h) => h(App),
}).$mount('#app');
