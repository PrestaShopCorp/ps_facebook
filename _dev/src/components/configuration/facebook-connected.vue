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
        href="javascript:void();"
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
        variant=""
        class="float-right ml-4"
      >
        TODO
      </b-button>

      <div class="logo mr-3">
        <img
          :src="facebookLogo"
          alt="colors"
        >
      </div>

      <div class="description pr-2">
        <div>
          {{ $t('configuration.facebook.connected.description') }}
          <br>
          <strong>
            EMAIL TODO
          </strong>
        </div>
      </div>

      TODO
    </b-card-body>

  </b-card>
</template>

<script lang="ts">
import {defineComponent} from '@vue/composition-api';
import {
  BCard, BButton, BCardBody, BIconstack, BIconCheck, BIconCircleFill,
} from 'bootstrap-vue';
import facebookLogo from '../../assets/facebook_logo.svg';

export default defineComponent({
  name: 'FacebookConnected',
  components: {
    BCard, BButton, BCardBody, BIconstack, BIconCheck, BIconCircleFill,
  },
  mixins: [],
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
  computed: { },
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
  },
  watch: { },
  created() {
  },
  mounted() {
  },
  updated() {
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
  }
</style>
