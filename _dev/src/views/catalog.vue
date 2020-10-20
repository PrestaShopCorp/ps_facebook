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
  <div
    v-if="loading"
    class="spinner"
  />
  <div
    v-else
    id="catalog"
  >
    TODO
  </div>
</template>

<script>
import {defineComponent} from '@vue/composition-api';

export default defineComponent({
  name: 'Catalog',
  components: {
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
  methods: {
  },
  watch: {
  },
});
</script>

<style lang="scss" scoped>
  .spinner {
    color: #fff;
    background-color: inherit !important;
    width: 8rem !important;
    height: 8rem !important;
    border-radius: 4rem !important;
    border-right-color: #25b9d7;
    border-bottom-color: #25b9d7;
    border-width: .1875rem;
    border-style: solid;
    font-size: 0;
    outline: none;
    display: inline-block;
    border-left-color: #bbcdd2;
    border-top-color: #bbcdd2;
    -webkit-animation: rotating 2s linear infinite;
    animation: rotating 2s linear infinite;
    position: relative;
    left: calc(50% - 4rem);
    top: 6rem;
  }
</style>
