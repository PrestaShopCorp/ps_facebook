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
    <template v-slot:header>
      <h3 class="d-inline">
        {{ $t('configuration.facebook.notConnected.title') }}
      </h3>
    </template>
    <b-card-body class="pt-2 pl-3 pb-4 pr-3 d-flex align-items-center">
      <img
        src="@/assets/logo_highres.png"
        alt="colors"
        class="logo mr-3"
      />

      <div class="description pr-2">
        {{ $t('configuration.facebook.notConnected.description') }}
      </div>

      <b-button
        :variant="active && canConnect ? 'primary' : 'outline-primary disabled'"
        class="ml-4 btn-with-spinner text-nowrap"
        @click="onFbeOnboardClick"
        :disabled="!active || !canConnect"
      >
        <span :class="active && !canConnect ? 'hidden' : ''">
          {{ $t('configuration.facebook.notConnected.connectButton') }}
        </span>
        <div
          v-if="active && !canConnect"
          class="spinner"
        />
      </b-button>
    </b-card-body>
  </b-card>
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
      width: 1.3rem !important;
      height: 1.3rem !important;
      position: absolute;
      left: calc(50% - 0.6rem);
      top: calc(50% - 0.6rem);
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
