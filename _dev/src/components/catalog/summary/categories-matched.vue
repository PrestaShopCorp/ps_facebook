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
    <div class="illustration float-left mr-3 d-none d-md-block">
      <img
        :src="illustration"
        width="48"
        height="44"
        alt="background illustration"
      >
    </div>

    <h1 class="title">
      {{ $t('categoryMatched.title') }}
    </h1>

    <b-link
      class="view-button float-right ml-3"
      @click="$parent.goto($parent.PAGES.categoryMatchingEdit)"
    >
      {{ $t('categoryMatched.btn') }}
    </b-link>

    <p>
      {{ $t('categoryMatched.description') }}
    </p>

    <div class="w-75 p-3 mb-1">
      <div class="h-25">
        <span class="psfb-progress-txt">
          {{ $t('categoryMatched.progressBarMatched')}}
        </span>
        <span class="float-right psfb-progress-txt">
          {{ $t('categoryMatched.progressBarTotal')}}
        </span>
      </div>
      <b-progress
        class="psfb-progress-bar"
        :value="progressBarData.matched"
        :max="progressBarData.total"
      />
      <span class="psfb-number matched"> {{ progressBarData.matched }}</span>
      <span class="float-right psfb-number total"> {{ progressBarData.total }}</span>
    </div>
  </div>
</template>

<script>
import {defineComponent} from '@vue/composition-api';
import {BLink} from 'bootstrap-vue';
import illustration from '../../../assets/category_matching_illustration.png';

export default defineComponent({
  name: 'CategoriesMatched',
  components: {
    BLink,
  },
  mixins: [],
  props: {
    matchingProgress: {
      type: Object,
      required: true,
    },
  },
  computed: {
  },
  data() {
    return {
      illustration,
      progressBarData: this.matchingProgress,
    };
  },
  methods: {
  },
  watch: {
  },
});
</script>

<style lang="scss" scoped>
  .title {
    font-weight: 600;
  }
  .view-button {
    font-weight: 700;
    position: absolute;
    bottom: 0.8rem;
    right: 1rem;
  }
  .psfb-number {
    font-size: 18px;
    font-weight: 400;
  }
  .psfb-number.matched {
    color: #24B9D7;
  }
  .psfb-progress-bar {
    margin-bottom: 5px;
    height: 20px;
    margin-top: 5px;
  }
  .psfb-progress-txt {
    color:#6C868E;
    font-size: 12px;
  }
</style>
