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
  <b-overlay
    :show="!active"
    opacity="0.7"
    no-fade
  >
    <b-card no-body>
      <template v-slot:header>
        <h3 class="d-inline">
          {{ $t('configuration.facebook.notConnected.title') }}
        </h3>
      </template>
      <b-card-body>
        {{ $t('configuration.facebook.notConnected.intro') }}
      </b-card-body>
      <b-card-body class="pt-0">
        <b-button
          :variant="canConnect ? 'primary' : 'outline-primary disabled'"
          class="float-right ml-4 btn-with-spinner"
          @click="onFbeOnboardClick"
          v-if="active"
          :disabled="!canConnect"
        >
          <span :class="!canConnect ? 'hidden' : ''">
            {{ $t('configuration.facebook.notConnected.connectButton') }}
          </span>
          <div
            v-if="!canConnect"
            class="spinner"
          />
        </b-button>

        <div class="logo mr-3">
          <img
            src="@/assets/facebook_logo.svg"
            alt="colors"
          >
        </div>

        <div class="description pr-2">
          <div>
            {{ $t('configuration.facebook.notConnected.description') }}
            <br>
            <p
              class="facebook-not-connected-details small-text text-muted"
              v-html="md2html($t('configuration.facebook.notConnected.details'))"
            />
          </div>
        </div>
      </b-card-body>
    </b-card>
  </b-overlay>
</template>

<script lang="ts">
import {defineComponent} from '@vue/composition-api';
import {BCard, BCardBody, BOverlay} from 'bootstrap-vue';
import showdown from 'showdown';

export default defineComponent({
  name: 'FacebookNotConnected',
  components: {BCard, BCardBody, BOverlay},
  props: {
    active: {
      type: Boolean,
      required: true,
    },
    canConnect: {
      type: Boolean,
      required: true,
    },
  },
  methods: {
    onFbeOnboardClick() {
      if (this.canConnect) {
        this.$emit('onFbeOnboardClick');
        this.$segment.track('Launch FB configuration', {
          module: 'ps_facebook',
        });
      }
    },
    md2html: (md) => (new showdown.Converter()).makeHtml(md),
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

  .btn-with-spinner {
    position: relative;

    & > .hidden {
      visibility: hidden;
    }

    & > .spinner {
      color: #fff;
      background-color: #fff;
      width: 1.3rem !important;
      height: 1.3rem !important;
      border-radius: 2.5rem;
      border-right-color: #25b9d7;
      border-bottom-color: #25b9d7;
      border-width: .1875rem;
      border-style: solid;
      font-size: 0;
      outline: none;
      display: inline-block;
      border-left-color: #bbcdd2;
      border-top-color: #bbcdd2;
      -webkit-animation: rotating 2s linear infinite;
      animation: rotating 2s linear infinite;
      position: absolute;
      left: calc(50% - 0.6rem);
    }
  }

</style>
<style lang="scss">
  .facebook-not-connected-details {
    margin-top: 0.8em;
    margin-bottom: 0;

    > ul {
      margin-bottom: 0;
      padding-inline-start: 1.6rem;

      > li {
        padding-left: 0.8rem;
      }
    }
  }
</style>
