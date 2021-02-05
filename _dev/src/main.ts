import Vue from 'vue';
import {BootstrapVue} from 'bootstrap-vue';
import VueCollapse from 'vue2-collapse';
import psAccountsVueComponents from 'prestashop_accounts_vue_components';
import VueSegment from '@prestashopcorp/segment-vue';
import router from './router';
import store from './store';
import App from './app.vue';
import i18n from './lib/i18n';

Vue.config.productionTip = false;
Vue.use(BootstrapVue);
Vue.use(VueCollapse);
Vue.use(psAccountsVueComponents, {locale: i18n.locale});
Vue.use(VueSegment, {
  // @ts-ignore
  id: global.psFacebookSegmentId,
  router,
  debug: process.env.NODE_ENV !== 'production',
  pageCategory: 'ps_facebook',
});

new Vue({
  router,
  store,
  i18n,
  template: '<App :contextPsFacebook="contextPsFacebook" />',
  components: {App},
  data() {
    return {
      // @ts-ignore
      contextPsFacebook: global.contextPsFacebook,
      // @ts-ignore
      psFacebookExternalBusinessId: global.psFacebookExternalBusinessId,
    };
  },
  methods: {
    refreshContextPsFacebook(context) {
      this.contextPsFacebook = context;
      // @ts-ignore
      global.contextPsFacebook = context;
    },
    refreshExternalBusinessId(externalBusinessId) {
      this.psFacebookExternalBusinessId = externalBusinessId;
      // @ts-ignore
      global.psFacebookExternalBusinessId = externalBusinessId;
    },
  },
}).$mount('#psFacebookApp');
