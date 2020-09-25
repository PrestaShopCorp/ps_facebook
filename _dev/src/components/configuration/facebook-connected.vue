<!--**
 * 2007-2020 PrestaShop and Contributors
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
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *-->
<template>
  <b-card no-body>

    <template v-slot:header>
      <a
        @click="fold"
        href="javascript:void(0);"
        class="float-right tooltip-link"
      >
        <i v-if="folded" class="material-icons fixed-size-small float-right">expand_more</i>
        <i v-else class="material-icons fixed-size-small float-right">expand_less</i>
      </a>
      <b-iconstack
        @click="fold"
        font-scale="1.5"
        class="mr-2 align-bottom fixed-size"
        width="20"
        height="20"
      >
        <b-icon-circle-fill stacked variant="success" />
        <b-icon-check stacked variant="white" />
      </b-iconstack>
      <h3 @click="fold" class="d-inline">
        {{ $t('configuration.facebook.title') }}
      </h3>
    </template>

    <b-card-body v-if="!folded">
      <b-button
        variant="outline-secondary"
        @click="edit"
        class="float-right ml-4"
      >
        {{ $t('configuration.facebook.connected.editButton') }}
      </b-button>

      <div class="logo mr-3">
        <img :src="facebookLogo" alt="colors" />
      </div>

      <div v-if="!!contextPsFacebook" class="description pr-2">
        <div>
          {{ $t('configuration.facebook.connected.description') }}
          <br>
          <div class="font-weight-bold text-break text-truncate" v-if="!!contextPsFacebook.email">
            {{ contextPsFacebook.email }}
          </div>
        </div>
      </div>
    </b-card-body>

    <b-card-body v-if="!folded" class="py-0 px-1">
      <b-container fluid>
        <b-row align-v="stretch">
          <b-col lg="6" md="6" sm="12" class="app pb-3 px-2">
            <facebook-app
              :app-type="$t('configuration.facebook.connected.facebookBusinessManager')"
              :tooltip="$t('configuration.facebook.connected.facebookBusinessManagerTooltip')"
              :app-name="contextPsFacebook.facebookBusinessManager.name"
              :email="contextPsFacebook.facebookBusinessManager.email"
              :created-at="contextPsFacebook.facebookBusinessManager.createdAt"
            />
          </b-col>
          <div class="w-100 d-block d-sm-none" />
          <div class="w-100 d-none d-sm-block d-md-none" />
          <b-col lg="6" md="6" sm="12" class="app pb-3 px-2">
            <facebook-app
              :app-type="$t('configuration.facebook.connected.facebookPixel')"
              :tooltip="$t('configuration.facebook.connected.facebookPixelTooltip')"
              :app-name="contextPsFacebook.pixel.name"
              :app-id="`Pixel ID: ${contextPsFacebook.pixel.id}`"
              :last-active="Date.now()"
              :activation-switch="contextPsFacebook.pixel.activated"
              @onActivation="pixelActivation"
            />
          </b-col>
          <div class="w-100" />
          <b-col lg="6" md="6" sm="12" class="app pb-3 px-2">
            <facebook-app
              :app-type="$t('configuration.facebook.connected.facebookPage')"
              :tooltip="$t('configuration.facebook.connected.facebookPageTooltip')"
              :app-name="contextPsFacebook.page.name"
              :likes="contextPsFacebook.page.likes"
              :logo="contextPsFacebook.page.logo"
            />
          </b-col>
          <div class="w-100 d-block d-sm-none" />
          <div class="w-100 d-none d-sm-block d-md-none" />
          <b-col lg="6" md="6" sm="12" class="app pb-3 px-2">
            <facebook-app
              :app-type="$t('configuration.facebook.connected.facebookAds')"
              :tooltip="$t('configuration.facebook.connected.facebookAdsTooltip')"
              :app-name="contextPsFacebook.ads.name"
              :email="contextPsFacebook.ads.email"
              :created-at="contextPsFacebook.ads.createdAt"
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
  BCard, BButton, BCardBody, BIconstack, BIconCheck, BIconCircleFill, BContainer, BRow, BCol,
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
  },
  props: {
    contextPsFacebook: {
      type: Object,
      required: false,
      default: null,
    },
    startExpanded: {
      type: Boolean,
      required: false,
      default: false,
    },
  },
  data() {
    return {
      facebookLogo,
      folded: !this.startExpanded,
    };
  },
  methods: {
    fold() {
      this.folded = !this.folded;
    },
    edit() {
      this.$emit('onEditClick');
    },
    pixelActivation(activated: boolean) {
      this.$emit('onPixelActivation', activated);
    },
  },
});
</script>

<style lang="scss" scoped>
  .logo {
    float: left;
    display: block;
  }

  .description {
    display: table-cell;

    div.font-weight-bold {
      max-width: 30em;
    }
  }
</style>
