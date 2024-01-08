import Vue from 'vue';
import {mapGetters} from 'vuex';
import {BootstrapVue} from 'bootstrap-vue';
import VueSegment from '@/lib/segment';
import {initShopClient, getGenericRouteFromSpecificOne} from '@/lib/api/shopClient';
import GettersTypesOnboarding from '@/store/modules/onboarding/getters-types';
import router from './router';
import store from './store';
import App from './app.vue';
import i18n from './lib/i18n';
import './assets/scss/app.scss';

// Prevent rebranding on PrestaShop Edition to alter our interface
document.body.classList.add('no-smb-reskin');

Vue.config.productionTip = false;
Vue.config.ignoredElements = ['prestashop-accounts'];
Vue.use(BootstrapVue);
Vue.use(VueSegment, {
  // @ts-ignore
  id: window.psFacebookSegmentId,
  router,
  debug: process.env.NODE_ENV !== 'production',
  pageCategory: '[FBK]',
});

initShopClient({
  shopUrl: window.psFacebookRouteToShopApi || getGenericRouteFromSpecificOne(
    window.psFacebookEnsureTokensExchanged,
  ),
});

new Vue({
  router,
  store,
  i18n,
  template: '<App />',
  components: {App},
  computed: {
    ...mapGetters('onboarding', [
      GettersTypesOnboarding.GET_EXTERNAL_BUSINESS_ID,
      GettersTypesOnboarding.GET_ONBOARDING_STATE,
    ]),
  },
  methods: {
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
          external_business_id: this.GET_EXTERNAL_BUSINESS_ID,
        });
      }
    },
  },
  watch: {
    GET_ONBOARDING_STATE(newValue) {
      this.$segment.identify(this.$store.state.context?.appContext?.shopId, {
        psx_ps_facebook_account_connected: (newValue !== null),
        psx_ps_pixel_disabled: !(newValue?.pixel?.isActive),
      });
    },
  },
}).$mount('#psFacebookApp');
