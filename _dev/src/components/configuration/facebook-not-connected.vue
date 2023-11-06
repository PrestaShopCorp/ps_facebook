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
      {{ $t('configuration.facebook.notConnected.title') }}
    </template>
    <b-card-body
      v-if="encourageToRetry"
      class="pt-0"
    >
      <b-alert
        show
        variant="warning"
        class="mb-0 mt-3"
      >
        <div
          class="d-flex justify-content-between"
        >
          <p class="mb-0">
            <strong class="ps_gs-fz-16">
              {{ $t('configuration.facebook.notConnected.incompleteOnboarding.title') }}
            </strong>
            <br>
            <span>
              {{ $t('configuration.facebook.notConnected.incompleteOnboarding.explanation') }}
            </span>
          </p>
          <div class="d-md-flex text-center align-items-center mt-2">
            <b-button
              class="mx-1 mt-3 mt-md-0 ml-md-0 mr-md-1 text-nowrap"
              variant="outline-primary"
              @click="onFbeOnboardClick"
            >
              {{ $t('configuration.facebook.notConnected.incompleteOnboarding.cta') }}
            </b-button>
          </div>
        </div>
      </b-alert>
    </b-card-body>
    <b-card-body
      v-else
      class="pt-2 pl-3 pb-4 pr-3 d-flex align-items-center"
    >
      <img
        src="@/assets/logo_highres.png"
        alt="colors"
        class="logo mr-3"
      >

      <div class="description pr-2">
        {{ $t('configuration.facebook.notConnected.description') }}
      </div>

      <b-button
        :variant="active && canConnect ? 'primary' : 'outline-primary disabled'"
        class="ml-4 btn-with-spinner text-nowrap ml-auto"
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
import {defineComponent} from 'vue';
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
    encourageToRetry: {
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
    md2html: (md: string) => (new showdown.Converter()).makeHtml(md),
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
