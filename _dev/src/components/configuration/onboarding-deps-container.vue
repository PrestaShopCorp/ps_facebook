<template>
  <div>
    <alert-module-upgrade-for-billing
      v-if="!billingContext"
    />
    <alert-subscribe-to-continue
      v-else-if="!billingRunning && !billingSubscription && facebookOnboarded"
      @startSubscription="startSubscription"
    />
    <alert-subscription-cancelled
      v-else-if="billingSubscription && billingSubscription.cancelled_at"
      :subscription="billingSubscription"
      @startSubscription="startSubscription"
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
      :description="$t('configuration.sectionDesc.psbilling')"
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
      :description="$t('configuration.sectionDesc.pscloudsync')"
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
import {billingUpdateCallback, initialize} from '@/lib/billing';
import AlertModuleUpgradeForBilling from '@/components/monetization/alert-module-upgrade-for-billing.vue';
import AlertSubscribeToContinue from '@/components/monetization/alert-subscribe-to-continue.vue';
import AlertSubscriptionCancelled from '@/components/configuration/alert-subscription-cancelled.vue';
import ModalModuleUpgradeForBilling from '@/components/monetization/modal-module-upgrade-for-billing.vue';

export default defineComponent({
  name: 'OnboardingDepsContainer',
  components: {
    AlertModuleUpgradeForBilling,
    AlertSubscribeToContinue,
    AlertSubscriptionCancelled,
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
  data() {
    return {
      openBillingModal: null as Function|null,
    };
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
    initAccountsComponent(): void {
      if (!window.psaccountsVue) {
        return;
      }
      window.psaccountsVue.init();
    },
    initBillingComponent(): void {
      if (!window.psBilling || !this.billingContext) {
        return;
      }
      const {openCheckout} = initialize(
        window.psBilling,
        this.billingContext.context,
        '#ps-billing-in-catalog-tab',
        '#ps-modal-in-catalog-tab',
        billingUpdateCallback(window.psBilling, this.$store.state.app),
      );
      this.openBillingModal = openCheckout;
    },
    startSubscription($event: string): void {
      if (this.openBillingModal) {
        this.openBillingModal($event);
      }
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
