<template>
  <div>
    <two-panel-cols
      :title="$t('configuration.sectionTitle.psaccounts')"
    >
      <prestashop-accounts />
    </two-panel-cols>
    <two-panel-cols
      :title="$t('configuration.sectionTitle.psbilling')"
    >
      <div
        v-show="!billingRunning"
        id="ps-billing-in-catalog-tab"
      />
      <div id="ps-modal-in-catalog-tab" />
      <card-billing-connected
        v-if="billingRunning"
        :subscription="billingSubscription"
      />
    </two-panel-cols>
    <two-panel-cols
      :title="$t('configuration.sectionTitle.pscloudsync')"
      v-show="billingRunning"
    >
      <div
        id="prestashop-cloudsync"
        class="p-0"
      />
    </two-panel-cols>
  </div>
</template>

<script lang="ts">
import {defineComponent} from '@vue/composition-api';
import {EVENT_HOOK_TYPE} from '@prestashopcorp/billing-cdc';
import TwoPanelCols from './two-panel-cols.vue';
import CardBillingConnected from './card-billing-connected.vue';

export default defineComponent({
  name: 'OnboardingDepsContainer',
  components: {
    TwoPanelCols,
    CardBillingConnected,
  },
  props: {
    psAccountsOnboarded: {
      type: Boolean,
      required: true,
    },
    billingRunning: {
      type: Boolean,
      required: true,
    },
  },
  computed: {
    billingSubscription(): boolean {
      return this.$store.state.app.billing.subscription;
    },
  },
  methods: {
    initCloudSyncConsent() {
      // If data related to CloudSync consent screen is available
      if (!window.cloudSyncSharingConsent && this.psAccountsOnboarded) {
        return;
      }

      const msc = window.cloudSyncSharingConsent;
      msc.init();
      msc.on('OnboardingCompleted', (isCompleted) => {
        if (isCompleted) {
          this.$segment.track('[FBK] Consent to share data of CloudSync', {
            module: 'ps_facebook',
          });
          this.$emit('onCloudsyncConsentUpdated', isCompleted);
        }
      });
      msc.isOnboardingCompleted((isCompleted) => {
        // Identify only when we get a valid boolean value
        if (!!isCompleted === isCompleted) {
          this.$segment.identify(this.$store.state.context?.appContext?.shopId, {
            fbk_user_has_given_consent_to_use_cloudsync: isCompleted,
          });
          this.$emit('onCloudsyncConsentUpdated', isCompleted);
        }
      });
    },
    initAccountsComponent() {
      if (!window.psaccountsVue) {
        return;
      }
      window.psaccountsVue.init();
    },
    initBillingComponent() {
      if (!window.psBilling) {
        return;
      }
      window.psBilling.initialize(window.psBillingContext.context, '#ps-billing-in-catalog-tab', '#ps-modal-in-catalog-tab', (type: EVENT_HOOK_TYPE, data: any) => {
        switch (type) {
          // Hook triggered when the subscription is created
          case window.psBilling.EVENT_HOOK_TYPE.SUBSCRIPTION_CREATED:
            // CHECKME: Do we actually receive data about the subscription?
            this.$store.state.app.billing.subscription = data;
            break;
          default:
            break;
        }
      });
    },
  },
  mounted() {
    this.initAccountsComponent();
    this.initBillingComponent();
    this.initCloudSyncConsent();
  },
  watch: {
    psAccountsOnboarded: {
      handler(newValue: boolean) {
        if (newValue === true) {
          this.initBillingComponent();
        }
      },
    },
    billingRunning: {
      handler(newValue: boolean) {
        if (newValue === true) {
          this.initCloudSyncConsent();
        }
      },
    },
  },
});
</script>
