import Vue from 'vue';
import {BootstrapVue} from 'bootstrap-vue';
import VueCollapse from 'vue2-collapse';
import VueSegment from '@/lib/segment';
import router from './router';
import store from './store';
import App from './app.vue';
import i18n from './lib/i18n';
import './assets/scss/app.scss';

Vue.config.productionTip = false;
Vue.config.ignoredElements = ['prestashop-accounts'];
Vue.use(BootstrapVue);
Vue.use(VueCollapse);
Vue.use(VueSegment, {
  // @ts-ignore
  id: global.psFacebookSegmentId,
  router,
  debug: process.env.NODE_ENV !== 'production',
  pageCategory: '[FBK]',
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

      // @ts-ignore
      this.$segment.identify(this.$store.state.context?.appContext?.shopId, {
        psx_ps_facebook_account_connected: (context !== null),
        psx_ps_pixel_disabled: !(context?.pixel?.isActive),
      });
    },
    refreshExternalBusinessId(externalBusinessId) {
      this.psFacebookExternalBusinessId = externalBusinessId;
      // @ts-ignore
      global.psFacebookExternalBusinessId = externalBusinessId;
    },
    identifySegment() {
      // @ts-ignore
      if (!this.$segment) {
        return;
      }

      const userId = this.$store.state.context?.appContext?.shopId;
      if (userId) {
        // @ts-ignore
        this.$segment.identify(userId, {
          name: this.$store.state.context.appContext.shopUrl,
          email: this.$store.state.context.appContext.user?.email,
          language: this.$store.state.context.statei18nSettings?.isoCode,
          version_ps: this.$store.state.context.appContext.psVersion,
          fbk_module_version: this.$store.state.context.appContext.moduleVersion,
          external_business_id: this.psFacebookExternalBusinessId,
        });
      }
    },
  },
}).$mount('#psFacebookApp');
