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
      <h3 class="d-inline">
        {{ $t('configuration.facebook.title') }}
      </h3>
    </template>
    <b-card-body>
      {{ $t('configuration.facebook.notConnected.intro') }}
    </b-card-body>
    <b-card-body class="pt-0">
      <b-button
        variant="primary"
        class="float-right ml-4"
        @click="onFbeOnboardClick"
      >
        {{ $t('configuration.facebook.notConnected.connectButton') }}
      </b-button>

      <div class="logo mr-3">
        <img
          :src="facebookLogo"
          alt="colors"
        >
      </div>

      <div class="description pr-2">
        <div>
          {{ $t('configuration.facebook.notConnected.description') }}
          <br>
          <p
            class="facebook-not-connected-details small-text text-muted"
            v-html="$t('configuration.facebook.notConnected.details')"
          />
        </div>
      </div>
    </b-card-body>
  </b-card>
</template>

<script lang="ts">
import {defineComponent} from '@vue/composition-api';
import {BCard, BCardBody} from 'bootstrap-vue';
import facebookLogo from '../../assets/facebook_logo.svg';

export default defineComponent({
  name: 'FacebookNotConnected',
  components: {BCard, BCardBody},
  data() {
    return {
      facebookLogo,
    };
  },
  methods: {
    onFbeOnboardClick() {
      this.$emit('onFbeOnboardClick');
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
