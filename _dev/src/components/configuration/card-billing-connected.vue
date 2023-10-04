<template>
  <b-card no-body>
    <b-card-header>
      <b-iconstack
        font-scale="1.5"
        class="mr-2 align-bottom fixed-size"
        width="20"
        height="20"
      >
        <b-icon-circle-fill
          stacked
          variant="success"
        />
        <b-icon-check
          stacked
          variant="white"
        />
      </b-iconstack>
      {{ $t('configuration.billingFacade.title') }}
    </b-card-header>
    <b-card-body
      class="pb-3"
    >
      <i
        class="material-icons ps_gs-fz-48 mr-3"
      >credit_card</i>
      {{ $t('configuration.billingFacade.nextPayment', {
        date: nextBillingDate
      }) }}
    </b-card-body>
  </b-card>
</template>

<script lang="ts">
import { PropType, defineComponent } from 'vue';
import {
  BIconstack,
  BIconCheck,
  BIconCircleFill,
} from 'bootstrap-vue';
import {ISubscription} from '@prestashopcorp/billing-cdc';

export default defineComponent({
  name: 'CardBillingConnected',
  components: {
    BIconstack,
    BIconCheck,
    BIconCircleFill,
  },
  props: {
    subscription: {
      type: Object as PropType<ISubscription>,
      required: true,
    },
  },
  computed: {
    nextBillingDate(): string {
      return  new Date(this.subscription.next_billing_at*1000).toLocaleDateString(
        undefined,
        {year: 'numeric', month: 'numeric', day: 'numeric'},
      );
    },
  },
});
</script>