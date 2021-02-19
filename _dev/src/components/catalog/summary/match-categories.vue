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
  <div>
    <div class="illustration float-left mr-3 d-none d-md-block">
      <img
        src="@/assets/category_matching_illustration.png"
        width="120"
        height="120"
        alt="background illustration"
      >
    </div>

    <h1 class="title">
      {{ $t('catalogSummary.categoryMatching') }}
    </h1>
    <b-button
      class="float-right ml-3"
      :variant="isPrimaryAction ? 'primary' : 'outline-secondary'"
      @click="$parent.goto($parent.PAGES.categoryMatchingView)"
    >
      {{ $t('catalogSummary.matchCategoriesButton') }}
    </b-button>
    <p class="text">
      {{ $t('catalogSummary.categoryMatchingIntro') }}
    </p>
    <b-alert
      variant="info"
      class="small-text fix-alert"
      show
      v-html="md2html($t('catalogSummary.categoryMatchingNotice'))"
    />
  </div>
</template>

<script>
import {defineComponent} from '@vue/composition-api';
import {BButton} from 'bootstrap-vue';

import showdown from 'showdown';

export default defineComponent({
  name: 'MatchCategories',
  components: {
    BButton,
  },
  props: {
    isPrimaryAction: {
      type: Boolean,
      required: false,
      default: false,
    },
  },
  methods: {
    md2html: (md) => (new showdown.Converter()).makeHtml(md),
  },
});
</script>

<style lang="scss" scoped>
  .title {
    font-weight: 600;
  }
  .text {
    display: flow-root;
    margin-bottom: 3.5rem!important;

    & > div {
      margin-top: 1em;
      padding-left: 3.8rem !important;
      padding-right: 0.5rem !important;

      span {
        padding: 0 !important;
      }
    }
  }
  .fix-alert {
    margin-bottom: 0;
  }
</style>
<style lang="scss">
  .fix-alert.small-text > p {
    font-size: .75rem !important;
  }
</style>
