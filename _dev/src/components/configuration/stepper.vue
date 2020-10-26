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
  <b-card class="card">
    <div class="illustration float-left d-none d-md-block">
      <img
        :src="illustration2"
        width="271"
        height="194"
        alt="background illustration"
      >
    </div>

    <div class="title px-3">
      <h3>{{ $t('configuration.messages.syncCatalogAdvice') }}</h3>

      <div class="mb-2">
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
        {{ $t('configuration.messages.stepPsAccount') }}
      </div>

      <div class="mb-2">
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
        {{ $t('configuration.messages.stepPsFacebook') }}
      </div>

      <div class="mb-2">
        <b-iconstack v-if="categoryMatchingStarted"
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
        <b-icon-circle-fill
          v-else
          font-scale="1.5"
          class="mr-2 align-bottom fixed-size"
          width="20"
          height="20"
          variant="secondary"
        />
        <span :class="!categoryMatchingStarted && 'bold'">
          {{ $t('configuration.messages.stepCategoryMatching') }}
        </span>
      </div>

      <div class="mb-2">
        <b-iconstack v-if="productSyncStarted"
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
        <b-icon-circle-fill
          v-else
          font-scale="1.5"
          class="mr-2 align-bottom fixed-size"
          width="20"
          height="20"
          variant="secondary"
        />
        <span :class="!productSyncStarted && 'bold'">
          {{ $t('configuration.messages.stepProductSync') }}
        </span>
      </div>

      <b-button
        variant="primary"
        class="mt-2"
        @click="onSyncCatalogAdviceClick"
      >
        {{ $t('configuration.messages.syncCatalogButton') }}
      </b-button>
    </div>
  </b-card>
</template>

<script lang="ts">
import {defineComponent} from '@vue/composition-api';
import {
  BButton,
  BCard,
  BIconstack,
  BIconCheck,
  BIconCircleFill,
} from 'bootstrap-vue';
import illustration2 from '../../assets/illustration2.png';

export default defineComponent({
  name: 'Stepper',
  components: {
    BButton,
    BCard,
    BIconstack,
    BIconCheck,
    BIconCircleFill,
  },
  props: {
    categoryMatchingStarted: {
      type: Boolean,
      required: false,
      default: false,
    },
    productSyncStarted: {
      type: Boolean,
      required: false,
      default: false,
    },
  },
  data() {
    return {
      illustration2,
    };
  },
  methods: {
    onSyncCatalogAdviceClick() {
      this.$emit('onSyncCatalogAdviceClick');
    },
  },
});
</script>

<style lang="scss" scoped>
  .card {
    border: none;
    border-radius: 3px;
    overflow: hidden;
    box-shadow: 0 5px 10px 0 rgba(0,0,0,0.1);

    & .title {
      display: flow-root;

      & > h3 {
        font-weight: 600;
        line-height: 1.5em;
      }
    }

    & .bold {
      font-weight: 600;
    }
  }

  .illustration > img {
    position: relative;
    left: calc(-1.25rem - 2px);
    top: calc(-1.25rem - 2px);
  }
</style>
