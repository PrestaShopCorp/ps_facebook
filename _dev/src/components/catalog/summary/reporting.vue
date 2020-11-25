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
  <div>
    <h1>{{ $t('catalogSummary.reportingTitle') }}</h1>

    <p>{{ $t('catalogSummary.reportingIntro') }}</p>

    <b-container
      fluid
      class="w-100"
    >
      <b-row align-v="stretch">
        <b-col class="counter m-1 pt-1 pb-2 px-3">
          -
        </b-col>
        <div class="w-100 d-block d-sm-none" />
        <b-col class="counter m-1 pt-1 pb-2 px-3">
          -
        </b-col>
        <div class="w-100 d-block d-md-none" />
        <b-col class="counter m-1 pt-1 pb-2 px-3">
          -
        </b-col>
        <div class="w-100 d-block d-sm-none" />
        <b-col class="counter m-1 pt-1 pb-2 px-3">
          -
        </b-col>
      </b-row>
    </b-container>
    <br>

    <b-link
      class="view-button float-right ml-3"
      @click="$parent.goto($parent.PAGES.reportDetails)"
    >
      {{ $t('catalogSummary.detailsButton') }}
    </b-link>
  </div>
</template>

<script>
import {defineComponent} from '@vue/composition-api';
import {
  BLink,
  BContainer,
  BRow,
  BCol,
} from 'bootstrap-vue';

export default defineComponent({
  name: 'Reporting',
  components: {
    BLink,
    BContainer,
    BRow,
    BCol,
  },
  props: {
    reporting: {
      type: Object,
      required: true,
    },
  },
  computed: {
  },
  data() {
    return {
    };
  },
  methods: {
  },
  watch: {
  },
});
</script>

<style lang="scss" scoped>
  .view-button {
    font-weight: 700;
    position: absolute;
    bottom: 0.8rem;
    right: 1rem;
  }

  .counter {
    border-radius: 3px;
    background-color: #FAFBFC;
  }
</style>
