<!--**
 * 2007-2021 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2021 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *-->
<template>
  <div id="psFacebookApp">
    <div
      id="head_tabs"
      class="ps_gs-sticky-head page-head-tabs"
    >
      <Menu :context-ps-facebook="contextPsFacebook">
        <MenuItem
          @click="onProduct"
          :onboarding-required="true"
          route="/catalog"
        >
          {{ $t('general.tabs.catalog') }}
        </MenuItem>
        <MenuItem
          @click="onSales"
          :onboarding-required="true"
          route="/integrate"
        >
          {{ $t('general.tabs.integrate') }}
        </MenuItem>
        <MenuItem route="/configuration">
          {{ $t('general.tabs.configuration') }}
        </MenuItem>
        <MenuItem
          @click="onHelp"
          route="/help"
        >
          {{ $t('general.tabs.help') }}
        </MenuItem>
      </Menu>
    </div>

    <router-view :context-ps-facebook="contextPsFacebook" />
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
import {initShopClient} from '@/lib/api/shopClient';
import Menu from '@/components/menu/menu.vue';
import MenuItem from '@/components/menu/menu-item.vue';

let resizeEventTimer;
const root = document.documentElement;
const header = document.querySelector('#content .page-head');
const headerFull = document.querySelector('#header_infos');

const getGenericRouteFromSpecificOne = (route: string): string => {
  const url = new URL(route);
  const genericSearchParams = new URLSearchParams();
  url.searchParams.forEach((value, param) => {
    if (['token', 'controller'].includes(param)) {
      genericSearchParams.set(param, value);
    }
  });
  url.search = `?${genericSearchParams.toString()}`;
  return url.toString();
};

export default defineComponent({
  name: 'Home',
  components: {
    Menu,
    MenuItem,
  },
  props: {
    contextPsFacebook: {
      type: Object,
      required: false,
      default: () => global.contextPsFacebook || null, // avoid undefined
    },
    psFacebookGetFbContextRoute: {
      type: String,
      required: false,
      default: () => global.psFacebookGetFbContextRoute,
    },
  },
  created() {
    initShopClient({
      shopUrl: window.psFacebookRouteToShopApi || getGenericRouteFromSpecificOne(
        window.psFacebookEnsureTokensExchanged,
      ),
    });
    this.getFbContext();
    this.$root.identifySegment();

    this.setCustomProperties();
    window.addEventListener('resize', this.resizeEventHandler);
  },
  destroyed() {
    window.removeEventListener('resize', this.resizeEventHandler);
  },
  computed: {
    shopId() {
      return window.psAccountShopId;
    },
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
    getFbContext() {
      fetch(this.psFacebookGetFbContextRoute)
        .then((res) => {
          if (!res.ok) {
            throw new Error(res.statusText || res.status);
          }
          return res.json();
        })
        .then((json) => {
          this.$root.refreshContextPsFacebook(json.contextPsFacebook);
        })
        .catch((error) => {
          console.error(error);
        });
    },
    onHelp() {
      this.$segment.track('Click on Help tab', {
        module: 'ps_facebook',
      });
    },
    onProduct() {
      this.$segment.track('Click on Product catalog tab', {
        module: 'ps_facebook',
      });
    },
    onSales() {
      this.$segment.track('Click on Sales channels tab', {
        module: 'ps_facebook',
      });
    },
  },
  watch: {
    $route() {
      this.$root.identifySegment();
    },
  },
});
</script>
