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
        :src="illustration"
        width="48"
        height="44"
        alt="background illustration"
      >
    </div>

    <h1 class="title">
      {{ $t('catalogSummary.categoryMatching') }}
    </h1>

    <b-link
      class="view-button float-right ml-3"
      @click="$parent.goto($parent.PAGES.categoryMatchingView)"
    >
      {{ $t('catalogSummary.viewButton') }}
    </b-link>

    [TODO : when matching done or in progress]
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
</style>
