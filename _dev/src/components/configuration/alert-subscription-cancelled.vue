<template>
  <b-alert
    show
    variant="info"
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
          {{ $t('configuration.alertBillingCancelled.explanation', {date: endOfSubscriptionDate}) }}
        </span>
      </p>
      <div class="d-md-flex flex-grow-1 text-center align-items-end mt-2">
        <b-button
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
import {ISubscription} from '@prestashopcorp/billing-cdc/dist/@types/Subscription';
import {PropType, defineComponent} from 'vue';

export default defineComponent({
  name: 'AlertSubscriptionCancelled',
  props: {
    subscription: {
      type: Object as PropType<ISubscription>,
      required: true,
    },
  },
  computed: {
    endOfSubscriptionDate(): string {
      return new Date(this.subscription.cancelled_at * 1000).toLocaleDateString(
        window.i18nSettings.languageLocale.substring(0, 2),
        {dateStyle: 'long'},
      );
    },
  },
  methods: {
    triggerSubscription() {
      // TODO: Open billing modal
    },
  },
});
</script>
