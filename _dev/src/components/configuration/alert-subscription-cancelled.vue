<template>
  <b-alert
    show
    variant="warning"
  >
    <div
      class="d-flex flex-column flex-md-row justify-content-between"
    >
      <p class="mb-0">
        <strong class="ps_gs-fz-16">
          {{ $t('configuration.alertBillingCancelled.title') }}
        </strong>
        <br>
        <span>
          {{ $t('configuration.alertBillingCancelled.explanation') }}
        </span>
      </p>
      <div class="d-md-flex flex-grow-1 text-center align-items-end mt-2">
        <b-button
          size="sm"
          class="mx-1 mt-3 mt-md-0 mr-md-1 text-nowrap ml-auto"
          variant="outline-primary"
          @click="triggerSubscription"
        >
          {{ $t('cta.resubscribe') }}
        </b-button>
      </div>
    </div>
  </b-alert>
</template>

<script lang="ts">
import {defineComponent} from 'vue';

export default defineComponent({
  name: 'AlertSubscriptionCancelled',
  methods: {
    triggerSubscription() {
      // TODO: Open billing modal
    },
  },
});
</script>
