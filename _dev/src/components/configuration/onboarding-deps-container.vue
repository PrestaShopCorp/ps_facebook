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
      <div id="ps-billing" />
      <div id="ps-modal" />
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
import TwoPanelCols from './two-panel-cols.vue';

export default defineComponent({
  name: 'OnboardingDepsContainer',
  components: {
    TwoPanelCols,
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
      window.psBilling.initialize(window.psBillingContext.context, '#ps-billing', '#ps-modal', (type) => {
        switch (type) {
          // Hook triggered when the subscription is created
          case window.psBilling.EVENT_HOOK_TYPE.SUBSCRIPTION_CREATED:
            this.$emit('onRunningBilling', true);
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
