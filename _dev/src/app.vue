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
    <div class="ps_gs-sticky-head">
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
  </div>
</template>

<script>
import Menu from '@/components/menu/menu.vue';
import MenuItem from '@/components/menu/menu-item.vue';

let resizeEventTimer;
const root = document.documentElement;
const header = document.querySelector('#content .page-head');
const headerFull = document.querySelector('#header_infos');

export default {
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
    this.getFbContext();
    this.$root.identifySegment();

    this.setCustomProperties();
    window.addEventListener('resize', this.resizeEventHandler);
  },
  destroyed() {
    window.removeEventListener('resize', this.resizeEventHandler);
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
};
</script>

<style lang="scss">
  #psFacebookApp {
    @import '~bootstrap-vue/dist/bootstrap-vue';
    @import '~prestakit/dist/css/bootstrap-prestashop-ui-kit';
  }
  #psFacebookApp {
    margin: 0px;
    font-family: Open Sans,Helvetica,Arial,sans-serif;
    font-size: 14px;
    font-size: .875rem;
    font-weight: 400;
    line-height: 1.5;
    color: #363a41;
    text-align: left;
  }
  #psFacebookApp .card-header, .card-header .card-header-title {
    font-weight: 600;
    line-height: 24px;
    line-height: 1.5rem;
  }
  #psFacebookApp .card-header .main-header #header-search-container .input-group:before,
  .card-header .material-icons, .card-header .ps-tree-items .tree-name button:before,
  .main-header #header-search-container .card-header .input-group:before,
  .ps-tree-items .tree-name .card-header button:before {
    color: #6c868e;
    margin-right: 5px;
  }
  #psFacebookApp .form-group.has-danger:after, #psFacebookApp .form-group.has-success:after,
  #psFacebookApp .form-group.has-warning:after {
    right: 10px;
  }
  :root {
    // has to be a css custom propertie to be modified via JS
    // because the height differs with PS versions
    --header-height: 100px;
    --header-height-full: 136px;
  }

  .nobootstrap {
    background-color: unset !important;
    padding: 100px 10px 100px; // fallback for IE11
    padding: var(--header-height) 10px 100px;
    min-width: unset !important;
  }
  .nobootstrap .form-group>div {
    float: unset;
  }
  .nobootstrap fieldset {
    background-color: unset;
    border: unset;
    color: unset;
    margin: unset;
    padding: unset;
  }
  .nobootstrap label {
    color: unset;
    float: unset;
    font-weight: unset;
    padding: unset;
    text-align: unset;
    text-shadow: unset;
    width: unset;
  }
  .nobootstrap .table tr th {
    background-color: unset;
    color: unset;
    font-size: unset;
  }
  .nobootstrap .table.table-hover tbody tr:hover {
      color: #fff;
  }
  .nobootstrap .table.table-hover tbody tr:hover a {
      color: #fff !important;
  }
  .nobootstrap .table tr td {
      border-bottom: unset;
      color: unset;
  }
  .nobootstrap .table {
    background-color: unset;
    border: unset;
    border-radius: unset;
    padding: unset;
  }
  .page-sidebar.mobile #content.nobootstrap {
    margin-left: unset;
  }
  .page-sidebar-closed:not(.mobile) #content.nobootstrap {
    padding-left: 50px;
  }
  .material-icons.js-mobile-menu {
    display: none !important
  }
  @import url('https://fonts.googleapis.com/icon?family=Material+Icons');

  // Fix for alerts
  .alert {
    padding-left: 3.8rem !important;
    padding-right: 0.5rem !important;
  }

  .ps_gs-sticky-head {
    padding: 0;
    position: sticky;
    top: 136px; // fallback for IE11
    top: var(--header-height-full);
    margin-bottom: 20px;
    z-index: 499;
    padding-left: 0;
    padding-right: 0;
    background-color: #fff;
  }

  .ps_gs-sticky-head .nav-link {
    padding: rem(7) 1.25rem;
  }

</style>
