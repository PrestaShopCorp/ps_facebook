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
      <div
        id="enabled-features"
        v-if="dynamicEnabledFeatures"
      >
        <feature-list>
          <enabled-feature
            v-for="(properties, featureName) in dynamicEnabledFeatures"
            :name="featureName"
            :key="featureName"
            v-bind:active="properties.enabled"
          />
        </feature-list>
      </div>

      <div
        id="disabled-features"
        v-if="dynamicDisabledFeatures"
      >
        <h3 class="ml-3">
          {{ $t('integrate.headings.disabledFeatures') }}
        </h3>
        <feature-list>
          <disabled-feature
            v-for="(properties, featureName) in dynamicDisabledFeatures"
            :name="featureName"
            :key="featureName"
          />
        </feature-list>
      </div>

      <div
        id="unavailable-features"
        v-if="dynamicUnavailableFeatures"
      >
        <h3 class="ml-3">
          {{ $t('integrate.headings.unavailableFeatures') }}
        </h3>
        <div class="mr-3 ml-3">
          <warning
            :warning-text="$t('integrate.warning.productsNotSynced')"
          >
            <b-button
              variant="primary"
              class="m-2 p-2"
            >
              {{ $t('integrate.buttons.syncProducts') }}
            </b-button>
          </warning>
        </div>
        <feature-list>
          <unavailable-feature
            v-for="(properties, featureName) in dynamicUnavailableFeatures"
            :name="featureName"
            :key="featureName"
          />
        </feature-list>
      </div>
    </div>
  </div>
</template>

<script>
import {defineComponent} from '@vue/composition-api';
import {BButton} from 'bootstrap-vue';
import FeatureList from '../components/features/feature-list.vue';
import EnabledFeature from '../components/features/enabled-feature.vue';
import Spinner from '../components/spinner/spinner.vue';
import DisabledFeature from '../components/features/disabled-feature.vue';
import UnavailableFeature from '../components/features/unavailable-feature.vue';
import Warning from '../components/warning/warning.vue';

export default defineComponent({
  name: 'Integrate',
  components: {
    BButton,
    Spinner,
    EnabledFeature,
    FeatureList,
    UnavailableFeature,
    DisabledFeature,
    Warning,
  },
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
      fetch(global.psFacebookGetFeaturesRoute)
        .then((res) => {
          if (!res.ok) {
            throw new Error(res.statusText || res.status);
          }
          return res.json();
        })
        .then((json) => {
          this.dynamicEnabledFeatures = json.fbeFeatures.enabledFeatures;
          this.dynamicDisabledFeatures = json.fbeFeatures.disabledFeatures;
          this.dynamicUnavailableFeatures = json.fbeFeatures.unavailableFeatures;
          this.loading = false;
        }).catch((error) => {
          console.error(error);
          this.error = 'configuration.messages.unknownOnboardingError';
        });
    },
  },
});
</script>

<style lang="scss" scoped>
</style>
