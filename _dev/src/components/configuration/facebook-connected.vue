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
  <b-card
    no-body
  >
    <b-card-header>
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
      {{ $t('configuration.facebook.title') }}
    </b-card-header>

    <b-card-body
      class="d-flex align-items-start justify-content-between pl-3 pr-3"
    >
      <span v-if="!!contextPsFacebook">
        {{ $t('configuration.facebook.connected.description') }}
      </span>

      <b-dropdown
        variant="outline-primary"
        right
        class="ml-2"
        toggle-class="btn-max-width"
      >
        <template #button-content>
          {{ $t('configuration.facebook.connected.dropdownButton') }}
        </template>
        <b-dropdown-item
          @click="edit"
        >
          {{ $t('configuration.facebook.connected.editButton') }}
        </b-dropdown-item>
        <b-dropdown-item
          @click="openManageFbe"
        >
          {{ $t('configuration.facebook.connected.manageFbeButton') }}
        </b-dropdown-item>
        <b-dropdown-item
          @click="$emit('onFbeUnlinkRequest')"
        >
          {{ $t('configuration.facebook.connected.unlinkButton') }}
        </b-dropdown-item>
      </b-dropdown>
    </b-card-body>

    <b-card-body
      class="py-0 px-1"
    >
      <facebook-app
        :app-type="$t('configuration.facebook.connected.facebookBusinessManager')"
        :tooltip="$t('configuration.facebook.connected.facebookBusinessManagerTooltip')"
        :app-name="fbm.name"
        :email="fbm.email || ''"
        :created-at="fbm.createdAt"
        :display-warning="!fbm.email"
        :logo="require('@/assets/logo_highres.png')"
      />
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
      <facebook-app
        :app-type="$t('configuration.facebook.connected.facebookPixel')"
        :tooltip="$t('configuration.facebook.connected.facebookPixelTooltip')"
        :app-name="contextPsFacebook.pixel.name"
        :app-id="`Pixel ID: ${contextPsFacebook.pixel.id}`"
        :last-active="contextPsFacebook.pixel.lastActive"
        :url="pixelUrl"
        :activation-switch="contextPsFacebook.pixel.isActive"
        :frozen-switch="!isModuleEnabled"
        @onActivation="pixelActivation"
        :logo="require('@/assets/logo_highres.png')"
      />
      <facebook-app
        :app-type="$t('configuration.facebook.connected.facebookAds')"
        :tooltip="$t('configuration.facebook.connected.facebookAdsTooltip')"
        :app-name="contextPsFacebook.ads.name"
        :created-at="contextPsFacebook.ads.createdAt"
        :display-warning="
          !contextPsFacebook.ads.name ||
            !contextPsFacebook.ads.createdAt
        "
        :logo="require('@/assets/logo_highres.png')"
      />
    </b-card-body>
  </b-card>
</template>

<script lang="ts">
import {defineComponent} from 'vue';
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
    isModuleEnabled: {
      type: Boolean,
      required: false,
      default: true,
    },
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
  },
});
</script>
