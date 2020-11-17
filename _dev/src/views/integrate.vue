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
        v-if="dynamicEnabledFeaturesLength"
      >
        <feature-list>
          <enabled-feature
            v-for="(properties, featureName) in dynamicEnabledFeatures"
            :name="featureName"
            :key="featureName"
            :active="properties.enabled"
          />
        </feature-list>
      </div>

      <div
        id="available-features"
        v-if="dynamicAvailableFeaturesLength"
      >
        <h3 class="ml-3">
          {{ $t('integrate.headings.availableFeatures') }}
        </h3>
        <feature-list>
          <available-feature
            v-for="(properties, featureName) in dynamicAvailableFeatures"
            :name="featureName"
            :key="featureName"
          />
        </feature-list>
      </div>

      <div
        id="unavailable-features"
        v-if="dynamicUnavailableFeaturesLength"
      >
        <h3 class="ml-3">
          {{ $t('integrate.headings.unavailableFeatures') }}
        </h3>
        <div class="mr-3 ml-3">
          <b-alert
            show
            variant="warning"
          >
            <div>
              <p>{{ $t('integrate.warning.productsNotSynced') }}</p>
              <b-button
                variant="primary"
                class="mt-2"
              >
                {{ $t('integrate.buttons.syncProducts') }}
              </b-button>
            </div>
          </b-alert>
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
import {BAlert, BButton} from 'bootstrap-vue';
import FeatureList from '../components/features/feature-list.vue';
import EnabledFeature from '../components/features/enabled-feature.vue';
import Spinner from '../components/spinner/spinner.vue';
import AvailableFeature from '../components/features/available-feature.vue';
import UnavailableFeature from '../components/features/unavailable-feature.vue';

export default defineComponent({
  name: 'Integrate',
  components: {
    BAlert,
    BButton,
    Spinner,
    EnabledFeature,
    FeatureList,
    UnavailableFeature,
    AvailableFeature,
  },
  mixins: [],
  props: {
    enabledFeatures: {
      type: Object,
      required: true,
      default: () => {},
    },
    availableFeatures: {
      type: Object,
      required: true,
      default: () => {},
    },
    unavailableFeatures: {
      type: Object,
      required: true,
      default: () => {},
    },
  },
  data() {
    return {
      dynamicEnabledFeatures: this.enabledFeatures,
      dynamicAvailableFeatures: this.availableFeatures,
      dynamicUnavailableFeatures: this.unavailableFeatures,
      dynamicEnabledFeaturesLength: Object.keys(this.enabledFeatures).length,
      dynamicAvailableFeaturesLength: Object.keys(this.availableFeatures).length,
      dynamicUnavailableFeaturesLength: Object.keys(this.unavailableFeatures).length,
      loading: true,
      hiddenProp: null,
      visibilityChangeEvent: null,
    };
  },
  created() {
    if (this.dynamicEnabledFeaturesLength === 0
      && this.dynamicAvailableFeaturesLength === 0
      && this.dynamicUnavailableFeaturesLength === 0
    ) {
      this.fetchData();
      this.registerToVisibilityChangeEvent();
    } else {
      this.loading = false;
    }
  },
  beforeDestroy() {
    if (document.removeEventListener === 'undefined' || this.hiddenProp === null) {
      return;
    }
    document.removeEventListener(this.visibilityChangeEvent, this.handleVisibilityChange, false);
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
          this.dynamicAvailableFeatures = json.fbeFeatures.disabledFeatures;
          this.dynamicUnavailableFeatures = json.fbeFeatures.unavailableFeatures;
          this.loading = false;
        }).catch((error) => {
          console.error(error);
          this.error = 'configuration.messages.unknownOnboardingError';
        });
    },
    handleVisibilityChange() {
      // Watch when the page gets the focus, for instance
      // when the merchant comes back from another tab.
      if (document[this.hiddenProp] === false) {
        this.fetchData();
      }
    },
    registerToVisibilityChangeEvent() {
      // https://developer.mozilla.org/en-US/docs/Web/API/Page_Visibility_API
      if (typeof document.hidden !== 'undefined') { // Opera 12.10 and Firefox 18 and later support
        this.hiddenProp = 'hidden';
        this.visibilityChangeEvent = 'visibilitychange';
      } else if (typeof document.msHidden !== 'undefined') {
        this.hiddenProp = 'msHidden';
        this.visibilityChangeEvent = 'msvisibilitychange';
      } else if (typeof document.webkitHidden !== 'undefined') {
        this.hiddenProp = 'webkitHidden';
        this.visibilityChangeEvent = 'webkitvisibilitychange';
      }

      if (document.addEventListener === 'undefined' || this.hiddenProp === null) {
        return;
      }
      document.addEventListener(this.visibilityChangeEvent, this.handleVisibilityChange, false);
    },
  },
});
</script>

<style lang="scss" scoped>
</style>
