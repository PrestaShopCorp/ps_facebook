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
          {{ isSubscriptionRunning
            ? $t(
              'configuration.alertBillingCancelled.explanationBeforeCancellationDate',
              {date: endOfSubscriptionDate},
            )
            : $t(
              'configuration.alertBillingCancelled.explanationFromCancellationDate',
              {date: endOfSubscriptionDate},
            )
          }}
        </span>
      </p>
      <div
        class="d-md-flex flex-grow-1 text-center align-items-end mt-2"
        v-if="isResubscriptionPossible"
      >
        <b-button
          class="mx-1 mt-3 mt-md-0 mr-md-1 text-nowrap ml-auto"
          variant="outline-primary"
          @click="$emit(
            'startSubscription',
            isSubscriptionRunning ? 'subscription_reactivation' : 'subscription_funnel'
          )"
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
    isSubscriptionRunning(): boolean {
      return this.subscription.status !== 'cancelled';
    },
    endOfSubscriptionDate(): string {
      return new Date(this.subscription.cancelled_at * 1000).toLocaleDateString(
        this.$i18n.locale,
        {dateStyle: 'long'},
      );
    },
    isResubscriptionPossible(): boolean {
      return !this.subscription.parent_plan_name;
    },
  },
});
</script>
