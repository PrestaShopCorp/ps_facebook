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
  <spinner v-if="loading" />
  <div
    v-else
    id="catalogCategoryMatchingView"
  >
    TODO: CatalogCategoryMatchingView
    <br>
    <b-button @click="$parent.goto($parent.PAGES.categoryMatchingEdit)">
      EDIT
    </b-button>
    <b-button @click="$parent.back">
      BACK
    </b-button>
  </div>
</template>

<script>
import {defineComponent} from '@vue/composition-api';
import {BButton} from 'bootstrap-vue';
import Spinner from '../spinner/spinner.vue';

export default defineComponent({
  name: 'CatalogCategoryMatchingView',
  components: {
    Spinner,
    BButton,
  },
  mixins: [],
  props: {
  },
  computed: {
  },
  data() {
    return {
      loading: true,
    };
  },
  created() {
    this.fetchData();
  },
  methods: {
    fetchData() {
      // TODO !0
      this.loading = false;
    },
  },
  watch: {
  },
});
</script>

<style lang="scss" scoped>
</style>
