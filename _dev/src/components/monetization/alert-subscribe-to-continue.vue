<template>
  <b-alert
    variant="info"
    show
  >
    <div
      class="d-flex justify-content-between"
    >
      <p class="mb-0">
        <strong>
          {{ $t('configuration.alertBillingNotStarted.title') }}
        </strong>
        <span
          v-html="md2html(
            $t('configuration.alertBillingNotStarted.description',
               {date, price},
            ))"
        />
        <span class="text-muted ps_gs-fz-12">
          {{ $t('configuration.upgradeForBillingStep.IncludedInHosted') }}
        </span>
      </p>
      <div class="d-md-flex text-center align-items-center mt-2">
      </div>
    </div>
  </b-alert>
</template>

<script lang="ts">
import {defineComponent} from 'vue';
import Showdown from 'showdown';

export default defineComponent({
  name: 'AlertSubscribeToContinue',
  computed: {
    price(): string {
      return Intl.NumberFormat(window.i18nSettings.languageLocale, {
        style: 'currency',
        currency: 'EUR',
        currencyDisplay: 'narrowSymbol',
        trailingZeroDisplay: 'stripIfInteger',
      }).format(9.99);
    },
    date(): string {
      return new Date('2023-11-16').toLocaleDateString(undefined, {dateStyle: 'medium'});
    },
  },
  methods: {
    md2html: (md: string) => (new Showdown.Converter()).makeHtml(md),
  },
});
</script>
