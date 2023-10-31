<template>
  <b-alert
    variant="info"
    show
  >
    <p class="mb-0">
    <strong>
      {{ $t('configuration.upgradeForBillingStep.alert.title') }}
    </strong>
    <span
      v-html="md2html($t('configuration.upgradeForBillingStep.alert.description', {date, price}))"
    />
    <span class="text-muted ps_gs-fz-12">
      {{ $t('configuration.upgradeForBillingStep.IncludedInHosted') }}
    </span>
    </p>
  </b-alert>
</template>

<script lang="ts">
import {defineComponent} from 'vue';
import Showdown from 'showdown';

export default defineComponent({
  name: 'AlertModuleUpgradeForBilling',
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
