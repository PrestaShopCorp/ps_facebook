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
  <b-card class="introductionCard">
    <div class="illustration float-right d-none d-md-block">
      <img
        src="@/assets/illustration_header.png"
        width="271"
        height="194"
        alt="background illustration"
      >
    </div>

    <div class="p-3">
      <div>
        <img
          class="mb-3"
          src="@/assets/logo-wordmark.png"
          height="56"
          alt="PS Facebook logo"
        >
      </div>
      <h2>{{ $t('configuration.introduction.subTitle') }}</h2>

      <b-button
        class="mb-2 mt-1"
        variant="primary"
        @click="onHide"
      >
        {{ $t('configuration.introduction.getStarted') }}
      </b-button>

      <div
        class="text-muted small-text proPoints"
        v-html="md2html($t('configuration.introduction.proPoints'))"
      />
      <hr>

      <div class="potatoe">
        <img
          class="mx-4 mt-2 mb-4 d-none d-md-block"
          src="@/assets/manage_business_illustration.png"
          width="192"
          height="128"
          alt="A potatoe"
        >
        <div class="mb-4 mb-md-0">
          <h1>{{ $t('configuration.introduction.proPoint1Title') }}</h1>
          <p>{{ $t('configuration.introduction.proPoint1Description') }}</p>
        </div>
      </div>

      <div class="potatoe">
        <img
          class="mx-4 mt-2 mb-4 d-none d-md-block"
          src="@/assets/understand_traffic_illustration.png"
          width="192"
          height="128"
          alt="A potatoe"
        >
        <div class="mb-4 mb-md-0">
          <h1>{{ $t('configuration.introduction.proPoint2Title') }}</h1>
          <p>{{ $t('configuration.introduction.proPoint2Description') }}</p>
        </div>
      </div>

      <div class="potatoe">
        <img
          class="mx-4 mt-2 mb-4 d-none d-md-block"
          src="@/assets/boost_your_sales_illustration.png"
          width="192"
          height="128"
          alt="A potatoe"
        >
        <div class="mb-4 mb-md-0">
          <h1>{{ $t('configuration.introduction.proPoint3Title') }}</h1>
          <p>{{ $t('configuration.introduction.proPoint3Description') }}</p>
        </div>
      </div>

      <div class="potatoe">
        <img
          class="mx-4 mt-2 mb-4 d-none d-md-block"
          src="@/assets/reach_people_illustration.png"
          width="192"
          height="128"
          alt="A potatoe"
        >
        <div class="mb-4 mb-md-0">
          <h1>{{ $t('configuration.introduction.proPoint4Title') }}</h1>
          <p>{{ $t('configuration.introduction.proPoint4Description') }}</p>
        </div>
      </div>

      <!--
      {{ $t('configuration.introduction.needMoreFeatures') }}
      <a
        href="javascript:void(0);"
        class="font-weight-bold"
      >
        {{ $t('configuration.introduction.seeDetailedPlans') }}
      </a>
      -->

      <hr>
      <div class="text-center">
        <p>{{ $t('configuration.introduction.resume') }}</p>
        <b-button
          variant="primary"
          @click="onHide"
        >
          {{ $t('configuration.introduction.getStarted') }}
        </b-button>
      </div>
    </div>
  </b-card>
</template>

<script lang="ts">
import {defineComponent} from '@vue/composition-api';
import {BCard, BButton} from 'bootstrap-vue';
import showdown from 'showdown';

export default defineComponent({
  name: 'Introduction',
  components: {BCard, BButton},
  methods: {
    onHide() {
      this.$emit('onHide');
      if (this.$segment) {
        this.$segment.track('PS Facebook installed', {
          module: 'ps_facebook',
        });
      }
    },
    md2html: (md) => (new showdown.Converter()).makeHtml(md),
  },
});
</script>

<style lang="scss" scoped>
  .introductionCard {
    border: none;
    border-radius: 3px;
    overflow: hidden;
  }

  .illustration > img {
    position: relative;
    right: calc(-1.25rem - 2px);
    top: calc(-1.25rem - 2px);
  }
</style>
<style lang="scss">
  .proPoints > ul {
    padding-inline-start: 0;
    > li {
      display: inline;
      padding-left: 0.8em;
      padding-right: 1em;
    }
    > li:before {
      content: "✓";
      position: relative;
      left: -0.5em;
    }
  }

  .potatoe {
    display: flex;
    align-items: center;

    > div {
      display: flex;
      flex-direction: column;
      justify-items: center;

      > p {
        font-size: medium;
      }
    }
  }
</style>
