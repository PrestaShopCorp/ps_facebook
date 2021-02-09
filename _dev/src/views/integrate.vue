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
      <div class="mr-3 ml-3">
        <!-- Display a warning when no features are shown (token expired?) -->
        <b-alert
          v-if="!allFeaturesLength"
          variant="warning"
          show
        >
          {{ $t('integrate.warning.noFeatures') }}
        </b-alert>
        <!-- Display confirmation messages for freshly enabled features -->
        <success-alert
          v-for="(feature, index) in successfullyEnabledFeatures"
          :key="index"
          :name="feature"
          :shop-url="shopUrl"
          :show="true"
        />
      </div>

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
            :manage-route="manageRoute"
            @onToggleSwitch="onToggleSwitch"
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
            :manage-route="manageRoute"
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
                @click="onSyncCatalogClick"
              >
                {{ $t('catalogSummary.exportCatalogButton') }}
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

      <survey />
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
import SuccessAlert from '../components/features/success-alert.vue';
import Survey from '../components/survey/survey.vue';

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
    SuccessAlert,
    Survey,
  },
  mixins: [],
  props: {
    enabledFeatures: {
      type: Object,
      required: false,
      default: () => ({}),
    },
    availableFeatures: {
      type: Object,
      required: false,
      default: () => ({}),
    },
    unavailableFeatures: {
      type: Object,
      required: false,
      default: () => ({}),
    },
    shopUrl: {
      type: String,
      required: false,
      default: () => global.shopUrl || null,
    },
    manageRoute: {
      type: Object,
      required: false,
      default: () => ({
        default: `https://www.facebook.com/facebook_business_extension?app_id=${global.psFacebookAppId}&external_business_id=${global.psFacebookExternalBusinessId}`,
        page_cta: `https://www.facebook.com/${global.contextPsFacebook?.page?.id}`,
        view_message_url: `https://business.facebook.com/latest/inbox/all?asset_id=${global.contextPsFacebook?.page?.id}`,
      }),
    },
  },
  data() {
    return {
      dynamicEnabledFeatures: this.enabledFeatures,
      dynamicAvailableFeatures: this.availableFeatures,
      dynamicUnavailableFeatures: this.unavailableFeatures,
      successfullyEnabledFeatures: [],
      loading: true,
      hiddenProp: null,
      visibilityChangeEvent: null,
    };
  },
  created() {
    // Looking at the props (not data) to check if we can display the content immediately
    if (this.dynamicEnabledFeaturesLength === 0
      && this.dynamicAvailableFeaturesLength === 0
      && this.dynamicUnavailableFeaturesLength === 0
    ) {
      this.fetchData();
      this.registerToWindowVisibilityChangeEvent();
    } else {
      this.loading = false;
    }
  },
  beforeDestroy() {
    if (document.removeEventListener === 'undefined' || this.hiddenProp === null) {
      return;
    }
    document.removeEventListener(this.visibilityChangeEvent, this.onWindowVisibilityChange, false);
  },
  computed: {
    dynamicEnabledFeaturesLength() {
      return Object.keys(this.dynamicEnabledFeatures).length;
    },
    dynamicAvailableFeaturesLength() {
      return Object.keys(this.dynamicAvailableFeatures).length;
    },
    dynamicUnavailableFeaturesLength() {
      return Object.keys(this.dynamicUnavailableFeatures).length;
    },
    allFeaturesLength() {
      return this.dynamicEnabledFeaturesLength
        + this.dynamicAvailableFeaturesLength
        + this.dynamicUnavailableFeaturesLength;
    },
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
          this.displaySuccessMessages(json.fbeFeatures.enabledFeatures);
          this.dynamicEnabledFeatures = json.fbeFeatures.enabledFeatures;
          this.dynamicAvailableFeatures = json.fbeFeatures.disabledFeatures;
          this.dynamicUnavailableFeatures = json.fbeFeatures.unavailableFeatures;
          this.loading = false;
        }).catch((error) => {
          console.error(error);
          this.error = 'configuration.messages.unknownOnboardingError';
          this.loading = false;
        });
    },
    displaySuccessMessages(newEnabledFeatures) {
      if (!this.allFeaturesLength) {
        return;
      }
      Object.keys(newEnabledFeatures).forEach((feature) => {
        // If the feature was disabled in the previous state, display the confirmation message
        if ((!this.dynamicEnabledFeatures[feature]
          || this.dynamicEnabledFeatures[feature].enabled === false)
          && newEnabledFeatures[feature].enabled === true
        ) {
          this.displaySuccessMessage(feature);
        } else if (this.dynamicEnabledFeatures[feature]
          && this.dynamicEnabledFeatures[feature].enabled === true
          && newEnabledFeatures[feature].enabled === false
        ) {
          this.hideSuccessMessage(feature);
        }
      });
    },
    displaySuccessMessage(feature) {
      this.successfullyEnabledFeatures.push(feature);
    },
    hideSuccessMessage(acknowledgedFeature) {
      this.successfullyEnabledFeatures = this.successfullyEnabledFeatures
        .filter((feature) => feature !== acknowledgedFeature);
    },
    onSyncCatalogClick() {
      this.$router.push({name: 'Catalog', query: {page: 'summary'}});
    },
    onToggleSwitch(name, newStatus) {
      const newEnabledFeatures = {
        ...this.dynamicEnabledFeatures,
        [name]: {enabled: newStatus},
      };
      this.displaySuccessMessages(newEnabledFeatures);
      this.dynamicEnabledFeatures = newEnabledFeatures;

      this.$segment.track(`Feature ${name} ${newStatus ? 'enabled' : 'disabled'}`, {
        module: 'ps_facebook',
      });
    },
    onWindowVisibilityChange() {
      // Watch when the page gets the focus, for instance
      // when the merchant comes back from another tab.
      if (document[this.hiddenProp] === false) {
        this.fetchData();
      }
    },
    registerToWindowVisibilityChangeEvent() {
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
      document.addEventListener(this.visibilityChangeEvent, this.onWindowVisibilityChange, false);
    },
  },
});
</script>

<style lang="scss">
  #integrate {
    div.card:not(.survey) {
      border: none !important;
      border-radius: 3px;
    }
  }
</style>
