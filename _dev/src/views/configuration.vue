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
  <div id="configuration">
    <introduction v-if="showIntroduction" @onHide="showIntroduction = false" class="m-4" />
    <template v-else>
      <messages :showOnboardSucceeded="true" :showSyncCatalogAdvice="true" class="m-4" />
      <ps-accounts class="m-4" />
      <no-config class="m-4" />
    </template>
  </div>
</template>

<script>
  import { defineComponent } from '@vue/composition-api';
  import { PsAccounts } from 'prestashop_accounts_vue_components';
  import Introduction from '../components/configuration/introduction.vue';
  import Messages from '../components/configuration/messages.vue';
  import NoConfig from '../components/configuration/no-config.vue';

  export default defineComponent({
    name: 'Configuration',
    components: {
      Introduction,
      Messages,
      PsAccounts,
      NoConfig,
    },
    mixins: [],
    props: { },
    computed: { },
    data() {
      return {
        showIntroduction: true,
      };
    },
    methods: {
    },
    watch: { },
    created() {
    },
    mounted() {
    },
    updated() {
    },
  });
</script>

<style lang="scss" scoped>

</style>
