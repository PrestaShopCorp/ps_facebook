import Vue from 'vue';
import {BootstrapVue} from 'bootstrap-vue';
import psAccountsVueComponents from 'prestashop_accounts_vue_components';
import router from './router';
import store from './store';
import App from './app.vue';

Vue.config.productionTip = false;
Vue.use(BootstrapVue);
Vue.use(psAccountsVueComponents);

new Vue({
  router,
  store,
  render: (h) => h(App),
}).$mount('#app');
