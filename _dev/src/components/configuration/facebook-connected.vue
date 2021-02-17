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
  <b-card no-body>
    <b-card-header @click="fold">
      <a
        href="javascript:void(0);"
        class="float-right tooltip-link"
      >
        <i
          v-if="folded"
          class="material-icons fixed-size-small float-right"
        >expand_more</i>
        <i
          v-else
          class="material-icons fixed-size-small float-right"
        >expand_less</i>
      </a>
      <b-iconstack
        font-scale="1.5"
        class="mr-2 align-bottom fixed-size"
        width="20"
        height="20"
      >
        <b-icon-circle-fill
          stacked
          variant="success"
        />
        <b-icon-check
          stacked
          variant="white"
        />
      </b-iconstack>
      <h3 class="d-inline">
        {{ $t('configuration.facebook.title') }}
      </h3>
    </b-card-header>

    <!-- Large screen -->
    <b-card-body
      v-if="!folded"
      class="description d-none d-sm-flex"
    >
      <img
        class="mr-3"
        :src="facebookLogo"
        alt="colors"
      >

      <div v-if="!!contextPsFacebook">
        {{ $t('configuration.facebook.connected.description') }}
        <br>
        <span
          class="font-weight-bold"
          v-if="!!contextPsFacebook.user.email"
        >
          {{ contextPsFacebook.user.email }}
        </span>
      </div>

      <b-dropdown
        variant="primary"
        split
        right
        @click="edit"
        class="ml-4 float-right"
      >
        <template #button-content>
          {{ $t('configuration.facebook.connected.editButton') }}
        </template>
        <b-dropdown-item @click="openManageFbe">
          {{ $t('configuration.facebook.connected.manageFbeButton') }}
          <i class="material-icons">open_in_new</i>
        </b-dropdown-item>
        <b-dropdown-item
          data-toggle="modal"
          data-target="#ps_facebook_modal_unlink"
        >
          {{ $t('configuration.facebook.connected.unlinkButton') }}
        </b-dropdown-item>
      </b-dropdown>
    </b-card-body>

    <!-- Small screen -->
    <b-card-body
      v-if="!folded"
      class="description d-block d-sm-none"
    >
      <img
        class="mr-3 mb-3"
        :src="facebookLogo"
        alt="colors"
      >

      <b-dropdown
        variant="primary"
        split
        right
        :text="$t('configuration.facebook.connected.manageFbeButton')"
        @click="openManageFbe"
        class="ml-4 float-right"
      >
        <b-dropdown-item @click="edit">
          {{ $t('configuration.facebook.connected.editButton') }}
        </b-dropdown-item>
        <b-dropdown-item
          data-toggle="modal"
          data-target="#ps_facebook_modal_unlink"
        >
          {{ $t('configuration.facebook.connected.unlinkButton') }}
        </b-dropdown-item>
      </b-dropdown>

      <div v-if="!!contextPsFacebook">
        {{ $t('configuration.facebook.connected.description') }}
        <br>
        <span
          class="font-weight-bold"
          v-if="!!contextPsFacebook.user.email"
        >
          {{ contextPsFacebook.user.email }}
        </span>
      </div>
    </b-card-body>

    <!-- Confirmation modal for FBE uninstallation -->
    <div
      id="ps_facebook_modal_unlink"
      class="modal"
    >
      <div
        class="modal-dialog"
        role="document"
      >
        <div class="modal-content tw-rounded-none">
          <div class="modal-header">
            <slot name="header">
              <div class="tw-flex tw-items-center">
                <h5 class="modal-title tw-pl-3">
                  {{ $t('configuration.facebook.connected.unlinkModalHeader') }}
                </h5>
              </div>
            </slot>
            <button
              type="button"
              class="close"
              data-dismiss="modal"
              aria-label="Close"
            >
              <span aria-hidden="true">×</span>
            </button>
          </div>
          <div class="modal-body">
            {{ $t('configuration.facebook.connected.unlinkModalText') }}
          </div>
          <div class="modal-footer">
            <b-button
              variant="primary"
              target="_blank"
              data-dismiss="modal"
              @click="uninstall"
            >
              {{ $t('integrate.buttons.modalConfirm') }}
            </b-button>
          </div>
        </div>
      </div>
    </div>

    <b-card-body
      v-if="!folded"
      class="py-0 px-1"
    >
      <b-container fluid>
        <b-row align-v="stretch">
          <b-col
            lg="6"
            md="6"
            sm="12"
            class="app pb-3 px-2"
          >
            <facebook-app
              :app-type="$t('configuration.facebook.connected.facebookBusinessManager')"
              :tooltip="$t('configuration.facebook.connected.facebookBusinessManagerTooltip')"
              :app-name="fbm.name"
              :email="fbm.email || ''"
              :created-at="fbm.createdAt"
              :display-warning="!fbm.email"
            />
          </b-col>
          <div class="w-100 d-block d-sm-none" />
          <div class="w-100 d-none d-sm-block d-md-none" />
          <b-col
            lg="6"
            md="6"
            sm="12"
            class="app pb-3 px-2"
          >
            <facebook-app
              :app-type="$t('configuration.facebook.connected.facebookPixel')"
              :tooltip="$t('configuration.facebook.connected.facebookPixelTooltip')"
              :app-name="contextPsFacebook.pixel.name"
              :app-id="`Pixel ID: ${contextPsFacebook.pixel.id}`"
              :last-active="contextPsFacebook.pixel.lastActive"
              :url="pixelUrl"
              :activation-switch="contextPsFacebook.pixel.isActive"
              @onActivation="pixelActivation"
            />
          </b-col>
          <div class="w-100" />
          <b-col
            lg="6"
            md="6"
            sm="12"
            class="app pb-3 px-2"
          >
            <facebook-app
              :app-type="$t('configuration.facebook.connected.facebookPage')"
              :tooltip="$t('configuration.facebook.connected.facebookPageTooltip')"
              :app-name="contextPsFacebook.page.page"
              :likes="contextPsFacebook.page.likes"
              :logo="contextPsFacebook.page.logo"
              :display-warning="
                !contextPsFacebook.page.page
              "
            />
          </b-col>
          <div class="w-100 d-block d-sm-none" />
          <div class="w-100 d-none d-sm-block d-md-none" />
          <b-col
            lg="6"
            md="6"
            sm="12"
            class="app pb-3 px-2"
          >
            <facebook-app
              :app-type="$t('configuration.facebook.connected.facebookAds')"
              :tooltip="$t('configuration.facebook.connected.facebookAdsTooltip')"
              :app-name="contextPsFacebook.ads.name"
              :created-at="contextPsFacebook.ads.createdAt"
              :display-warning="
                !contextPsFacebook.ads.name ||
                  !contextPsFacebook.ads.createdAt
              "
            />
          </b-col>
        </b-row>
      </b-container>
    </b-card-body>
  </b-card>
</template>

<script lang="ts">
import {defineComponent} from '@vue/composition-api';
import {
  BCard,
  BButton,
  BCardBody,
  BCardHeader,
  BIconstack,
  BIconCheck,
  BIconCircleFill,
  BContainer,
  BRow,
  BCol,
  BDropdown,
  BDropdownItem,
} from 'bootstrap-vue';
import FacebookApp from './facebook-app.vue';
import facebookLogo from '../../assets/facebook_logo.svg';

export default defineComponent({
  name: 'FacebookConnected',
  components: {
    BCard,
    BButton,
    BCardBody,
    BIconstack,
    BIconCheck,
    BIconCircleFill,
    FacebookApp,
    BContainer,
    BRow,
    BCol,
    BCardHeader,
    BDropdown,
    BDropdownItem,
  },
  props: {
    psFacebookAppId: {
      type: String,
      required: true,
    },
    externalBusinessId: {
      type: String,
      required: true,
    },
    contextPsFacebook: {
      type: Object,
      required: false,
      default: null,
    },
    startExpanded: {
      type: Boolean,
      required: false,
      default: true,
    },
  },
  data() {
    return {
      facebookLogo,
      folded: !this.startExpanded,
    };
  },
  computed: {
    fbm() {
      return {
        name: this.contextPsFacebook.facebookBusinessManager.name,
        email: this.contextPsFacebook.facebookBusinessManager.email
          || this.contextPsFacebook.user.email,
      };
    },
    fbeUrl() {
      const q = `?app_id=${this.psFacebookAppId}&external_business_id=${this.externalBusinessId}`;
      return `https://www.facebook.com/facebook_business_extension/management/${q}`;
    },
    pixelUrl() {
      if (!this.contextPsFacebook || !this.contextPsFacebook.pixel) {
        return '#';
      }
      const pixId = this.contextPsFacebook.pixel.id;
      return `https://business.facebook.com/events_manager2/list/pixel/${pixId}/overview`;
    },
  },
  methods: {
    fold() {
      this.folded = !this.folded;
    },
    edit() {
      this.$emit('onEditClick');
      this.$segment.track('Click on Restart onboarding', {
        module: 'ps_facebook',
      });
    },
    pixelActivation(activated: boolean) {
      this.$emit('onPixelActivation', activated);
    },
    openManageFbe() {
      window.open(this.fbeUrl, '_blank');
      this.$segment.track('Click on Open advanced Settings', {
        module: 'ps_facebook',
      });
    },
    uninstall() {
      this.$emit('onUninstallClick');
      this.$segment.track('Click on unlink', {
        module: 'ps_facebook',
      });
    },
  },
});
</script>

<style lang="scss" scoped>
  .description {
    display: flex;
    flex-direction: row;
    align-items: flex-start;

    > div:first-of-type {
      flex-grow: 1;
      flex-shrink: 1;

      > span {
        word-break: break-word;
      }
    }
  }
</style>
