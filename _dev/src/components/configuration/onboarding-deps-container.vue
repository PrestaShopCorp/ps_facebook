<template>
  <div>
    <alert-module-upgrade-for-billing
      v-if="!billingContext"
    />
    <modal-module-upgrade-for-billing
      v-if="!billingContext && facebookOnboarded"
    />
    <two-panel-cols
      :title="$t('configuration.sectionTitle.psaccounts')"
    >
      <prestashop-accounts />
    </two-panel-cols>
    <two-panel-cols
      v-if="billingContext"
      :title="$t('configuration.sectionTitle.psbilling')"
    >
      <div
        v-show="!billingRunning"
        id="ps-billing-in-catalog-tab"
      />
      <div id="ps-modal-in-catalog-tab" />
      <card-billing-connected
        v-if="billingRunning && billingSubscription"
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
import {defineComponent} from 'vue';
import {ISubscription} from '@prestashopcorp/billing-cdc/dist/@types/Subscription';
import {IContextAuthentication, IContextBase} from '@prestashopcorp/billing-cdc/dist/@types/context/ContextRoot';
import TwoPanelCols from './two-panel-cols.vue';
import CardBillingConnected from './card-billing-connected.vue';
import {State as AppState} from '@/store/modules/app/state';
import {billingUpdateCallback} from '@/lib/billing';
import AlertModuleUpgradeForBilling from '@/components/monetization/alert-module-upgrade-for-billing.vue';
import ModalModuleUpgradeForBilling from '@/components/monetization/modal-module-upgrade-for-billing.vue';

export default defineComponent({
  name: 'OnboardingDepsContainer',
  components: {
    AlertModuleUpgradeForBilling,
    ModalModuleUpgradeForBilling,
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
    facebookOnboarded: {
      type: Boolean,
      required: true,
    },
  },
  computed: {
    billingContext(): IContextBase<IContextAuthentication>|undefined {
      return window.psBillingContext;
    },
    billingSubscription(): ISubscription|undefined {
      return (this.$store.state.app as AppState).billing.subscription;
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
      if (!window.psBilling || !this.billingContext) {
        return;
      }
      window.psBilling.initialize(
        this.billingContext.context,
        '#ps-billing-in-catalog-tab',
        '#ps-modal-in-catalog-tab',
        billingUpdateCallback(window.psBilling, this.$store.state.app),
      );
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
