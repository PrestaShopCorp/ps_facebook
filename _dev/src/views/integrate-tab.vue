<template>
  <div id="integrate">
    <loading-page-spinner v-if="loading" />
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

      <MessengerChatDeprecated
        v-if="messengerIsDeactivated"
        class="mr-3 ml-3"
      />
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
            :allow-display-of-switch="disallowSwitch.indexOf(featureName) === -1"
            :frozen-switch="!isModuleEnabled"
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
            @onToggleSwitch="onToggleSwitch"
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

      <card-survey />
    </div>
  </div>
</template>

<script>
import {defineComponent} from 'vue';
import {BAlert, BButton} from 'bootstrap-vue';
import {mapGetters} from 'vuex';
import GettersTypesOnboarding from '@/store/modules/onboarding/getters-types';
import FeatureList from '../components/features/feature-list.vue';
import EnabledFeature from '../components/features/enabled-feature.vue';
import LoadingPageSpinner from '@/components/spinner/loading-page-spinner.vue';
import AvailableFeature from '../components/features/available-feature.vue';
import UnavailableFeature from '../components/features/unavailable-feature.vue';
import SuccessAlert from '../components/features/success-alert.vue';
import CardSurvey from '@/components/survey/card-survey.vue';
import MessengerChatDeprecated from '@/components/configuration/messenger-chat-deprecated.vue';

export default defineComponent({
  name: 'IntegrateTab',
  components: {
    BAlert,
    BButton,
    CardSurvey,
    EnabledFeature,
    FeatureList,
    LoadingPageSpinner,
    UnavailableFeature,
    AvailableFeature,
    SuccessAlert,
    MessengerChatDeprecated,
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
      default: () => window.shopUrl || null,
    },
    isModuleEnabled: {
      type: Boolean,
      required: false,
      default: () => window.psFacebookModuleEnabled ?? true,
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
      disallowSwitch: [
        'page_shop',
      ],
      messengerIsDeactivated: true,
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
      this.isMessengerIsActive();
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
    ...mapGetters('onboarding', [
      GettersTypesOnboarding.GET_EXTERNAL_BUSINESS_ID,
      GettersTypesOnboarding.GET_ONBOARDING_STATE,
    ]),
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
    manageRoute() {
      return {
        default: `https://www.facebook.com/facebook_business_extension?app_id=${window.psFacebookAppId}&external_business_id=${this.GET_EXTERNAL_BUSINESS_ID}`,
        messenger_chat: `https://business.facebook.com/latest/inbox/settings/chat_plugin?asset_id=${this.GET_ONBOARDING_STATE?.page?.id}`,
        page_cta: `https://www.facebook.com/${this.GET_ONBOARDING_STATE?.page?.id}`,
        view_message_url: `https://business.facebook.com/latest/inbox/all?asset_id=${this.GET_ONBOARDING_STATE?.page?.id}`,
      };
    },
  },
  methods: {
    fetchData() {
      this.loading = true;
      fetch(window.psFacebookGetFeaturesRoute)
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
          this.identifyFeatures(json.fbeFeatures.enabledFeatures);
        }).catch((error) => {
          console.error(error);
          this.error = 'configuration.messages.unknownOnboardingError';
          this.loading = false;
        });
    },
    isMessengerIsActive() {
      fetch(window.psFacebookGetChatStatus)
        .then((res) => {
          if (!res.ok) {
            throw new Error(res.statusText || res.status);
          }
          return res.json();
        })
        .then((json) => {
          this.messengerIsDeactivated = json.messengerChatStatus;
        }).catch((error) => {
          console.error(error);
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

      this.$segment.track(`[FBK] Feature ${name} ${newStatus ? 'enabled' : 'disabled'}`, {
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
    identifyFeatures(enabledFeatures) {
      // @ts-ignore
      this.$segment.identify(this.$store.state.context?.appContext?.shopId, {
        psx_ps_messenger_disabled: !(enabledFeatures?.messenger_chat?.enabled),
      });
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
