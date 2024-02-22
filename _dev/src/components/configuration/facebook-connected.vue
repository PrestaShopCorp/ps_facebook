<template>
  <b-card
    no-body
  >
    <b-card-header
      class="d-flex"
    >
      <span class="step-is-done rounded-circle bg-success d-flex align-items-center mr-1">
        <i
          class="material-icons material-icons-round text-light ps_gs-fz-16 ml-auto mr-auto"
        >check</i>
      </span>
      {{ $t('configuration.facebook.title') }}
    </b-card-header>

    <b-card-body
      class="d-flex align-items-start justify-content-between"
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
      class="py-0"
    >
      <facebook-app
        :app-type="$t('configuration.facebook.connected.facebookBusinessManager')"
        :tooltip="$t('configuration.facebook.connected.facebookBusinessManagerTooltip')"
        :app-name="fbm.name"
        :email="fbm.email || ''"
        :created-at="fbm.createdAt"
        :display-warning="!fbm.email"
        :logo="require('@/assets/icon_meta_business_manager.png')"
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
        :activation-switch="true"
        :frozen-switch="!isModuleEnabled"
        @onActivation="pixelActivation"
        :logo="require('@/assets/icon_meta.png')"
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
        :logo="require('@/assets/local_offer_24px.svg')"
      />
    </b-card-body>
  </b-card>
</template>

<script lang="ts">
import {defineComponent} from 'vue';
import FacebookApp from './facebook-app.vue';

export default defineComponent({
  name: 'FacebookConnected',
  components: {
    FacebookApp,
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
      this.$segment.track('[FBK] Click on Restart onboarding', {
        module: 'ps_facebook',
      });
    },
    pixelActivation(activated: boolean) {
      this.$emit('onPixelActivation', activated);
    },
    openManageFbe() {
      window.open(this.fbeUrl, '_blank');
      this.$segment.track('[FBK] Click on Open advanced Settings', {
        module: 'ps_facebook',
      });
    },
  },
});
</script>
