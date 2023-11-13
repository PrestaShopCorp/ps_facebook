<template>
  <div id="psFacebookApp">
    <div
      id="head_tabs"
      class="ps_gs-sticky-head page-head-tabs"
    >
      <AppMenu>
        <MenuItem
          v-if="GET_BILLING_SUBSCRIPTION_ACTIVE && GET_ONBOARDING_STATE"
          route="/catalog"
        >
          {{ $t('general.tabs.catalog') }}
        </MenuItem>
        <MenuItem
          v-if="GET_BILLING_SUBSCRIPTION_ACTIVE && GET_ONBOARDING_STATE"
          route="/integrate"
        >
          {{ $t('general.tabs.integrate') }}
        </MenuItem>
        <MenuItem route="/configuration">
          {{ $t('general.tabs.configuration') }}
        </MenuItem>
        <MenuItem
          route="/billing"
          v-if="GET_BILLING_SUBSCRIPTION_ACTIVE"
        >
          {{ $t('general.tabs.billing') }}
        </MenuItem>
        <MenuItem
          route="/help"
        >
          {{ $t('general.tabs.help') }}
        </MenuItem>
      </AppMenu>
    </div>

    <router-view />
    <div
      v-if="shopId"
      id="helper-shopid"
    >
      {{ shopId }}
    </div>
  </div>
</template>

<script lang="ts">
import {defineComponent} from 'vue';
import {mapGetters} from 'vuex';
import AppMenu from '@/components/menu/app-menu.vue';
import MenuItem from '@/components/menu/menu-item.vue';
import GettersTypesOnboarding from '@/store/modules/onboarding/getters-types';
import GettersTypesApp from '@/store/modules/app/getters-types';

let resizeEventTimer;
const root = document.documentElement;
const header = document.querySelector('#content .page-head');
const headerFull = document.querySelector('#header_infos');

export default defineComponent({
  components: {
    AppMenu,
    MenuItem,
  },
  created() {
    this.setCustomProperties();
    window.addEventListener('resize', this.resizeEventHandler);
  },
  mounted() {
    this.getFbContext();
  },
  destroyed() {
    window.removeEventListener('resize', this.resizeEventHandler);
  },
  computed: {
    shopId(): string|null {
      return window.psAccountShopId;
    },
    ...mapGetters('onboarding', [
      GettersTypesOnboarding.GET_ONBOARDING_STATE,
    ]),
    ...mapGetters('app', [
      GettersTypesApp.GET_BILLING_SUBSCRIPTION_ACTIVE,
    ]),
  },
  methods: {
    resizeEventHandler() {
      clearTimeout(resizeEventTimer);
      resizeEventTimer = setTimeout(() => {
        this.setCustomProperties();
      }, 250);
    },
    setCustomProperties() {
      root.style.setProperty('--header-height', `${header.clientHeight}px`);
      root.style.setProperty('--header-height-full', `${header.clientHeight + headerFull.clientHeight}px`);
    },
    async getFbContext() {
      await this.$store.dispatch('onboarding/WARMUP_STORE');
    },
  },
  watch: {
    $route() {
      this.$root.identifySegment();
    },
  },
});
</script>
