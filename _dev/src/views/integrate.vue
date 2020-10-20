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
  <div id="integrate">
    <spinner v-if="loading" />
    <div v-else>
      <app-list>
        <app-item
          v-for="(properties, featureName) in dynamicEnabledFeatures"
          :key="featureName"
        />
      </app-list>

      <app-list>
        <app-item
          v-for="(properties, featureName) in dynamicDisabledFeatures"
          :key="featureName"
        />
      </app-list>

      <app-list>
        <app-item
          v-for="(properties, featureName) in dynamicDisabledFeatures"
          :key="featureName"
        />
      </app-list>
    </div>
  </div>
</template>

<script>
import AppList from '../components/apps/app-list.vue';
import AppItem from '../components/apps/app-item.vue';
import Spinner from '../components/spinner/spinner.vue';

export default {
  name: 'Integrate',
  components: {Spinner, AppItem, AppList},
  mixins: [],
  props: {
    enabledFeatures: {
      type: Array,
      required: false,
      default: () => [],
    },
    disabledFeatures: {
      type: Array,
      required: false,
      default: () => [],
    },
    unavailableFeatures: {
      type: Array,
      required: false,
      default: () => [],
    },
  },
  data() {
    return {
      dynamicEnabledFeatures: this.enabledFeatures,
      dynamicDisabledFeatures: this.disabledFeatures,
      dynamicUnavailableFeatures: this.unavailableFeatures,
      loading: true,
    };
  },
  created() {
    this.fetchData();
  },
  methods: {
    fetchData() {
      this.loading = true;
      fetch(global.psFacebookGetFeatures)
        .then((res) => {
          if (!res.ok) {
            throw new Error(res.statusText || res.status);
          }
          return res.json();
        })
        .then((json) => {
          this.dynamicEnabledFeatures = json.fbeFeatures.enabledFeatures;
          this.dynamicDisabledFeatures = json.fbeFeatures.disabledFeatures;
          this.unavailableFeatures = json.fbeFeatures.unavailableFeatures;
          this.loading = false;
        }).catch((error) => {
          console.error(error);
          this.error = 'configuration.messages.unknownOnboardingError';
        });
    },
  },
};
</script>

<style lang="scss" scoped>
</style>
