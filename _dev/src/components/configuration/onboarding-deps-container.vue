<template>
  <div>
    <prestashop-accounts />
    <div
      id="prestashop-cloudsync"
      class="p-0"
    />
  </div>
</template>

<script lang="ts">
import {defineComponent} from '@vue/composition-api';

export default defineComponent({
  name: 'OnboardingDepsContainer',
  props: {
    psAccountsOnboarded: {
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
  },
  mounted() {
    this.initAccountsComponent();
    this.initCloudSyncConsent();
  },
  watch: {
    psAccountsOnboarded: {
      handler(newValue) {
        if (newValue === true) {
          this.initCloudSyncConsent();
        }
      },
    },
  },
});
</script>
