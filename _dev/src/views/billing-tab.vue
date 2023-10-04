<template>
  <div
    class="m-3"
  >
    <b-card
      no-body
    >
      <b-card-header>
        {{ $t('billing.planTitle') }}
      </b-card-header>

      <b-card-body
        class="pl-3 pr-3"
      >
        <div id="ps-billing-in-billing-tab" />
        <div id="ps-modal-in-billing-tab" />
      </b-card-body>
    </b-card>

    <b-card
      no-body
      class="mt-3"
    >
      <b-card-header>
        {{ $t('billing.invoicesTitle') }}
      </b-card-header>

      <b-card-body
        class="pl-3 pr-3"
      >
        <div id="ps-billing-invoice" />
      </b-card-body>
    </b-card>
  </div>
</template>

<script lang="ts">
import {defineComponent} from 'vue';

export default defineComponent({
  name: 'BillingTab',
  mounted() {
    if (!window.psBilling) {
      return;
    }
    window.psBilling.initialize(
      window.psBillingContext.context,
      '#ps-billing-in-billing-tab',
      '#ps-modal-in-billing-tab',
      () => {},
    );

    window.psBilling.initializeInvoiceList(
      window.psBillingContext.context,
      '#ps-billing-invoice',
    );
  },
});
</script>
