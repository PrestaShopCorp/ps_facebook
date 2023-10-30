<template>
  <b-alert
    variant="info"
    class="m-3"
    show
  >
    <p class="mb-0">
      <strong>
        {{ $t('incomingPaidVersion.alert.title', {date, price}) }}
      </strong>
      <span
        v-html="md2html($t('incomingPaidVersion.alert.description'))"
      />
      <span class="text-muted">
        {{ $t('incomingPaidVersion.IncludedInHosted') }}
      </span>
    </p>
  </b-alert>
</template>

<script lang="ts">
import {defineComponent} from 'vue';
import Showdown from 'showdown';

export default defineComponent({
  name: 'AlertIncomingPaidVersion',
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

<style scoped>
.text-muted {
  font-size: 12px;
}
</style>
