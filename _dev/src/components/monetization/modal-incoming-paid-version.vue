<template>
  <ps-modal
    id="ps_facebook_modal_incoming_paid_version"
    ref="ps_facebook_modal_incoming_paid_version"
    :title="$t('incomingPaidVersion.modal.title')"
    @ok="ack"
    ok-only
    :visible="!isModalAlreadyAknowledged()"
  >
    <p
      v-html="md2html($t('incomingPaidVersion.modal.description', {price, date}))"
    />
    <p class="text-muted">
      {{ $t('incomingPaidVersion.IncludedInHosted') }}
    </p>
    <template slot="modal-ok">
      {{ $t('incomingPaidVersion.modal.cta') }}
    </template>
  </ps-modal>
</template>

<script lang="ts">
import {defineComponent} from 'vue';
import Showdown from 'showdown';
import PsModal from '@/components/commons/ps-modal.vue';

export default defineComponent({
  name: 'ModalIncomingPaidVersion',
  components: {
    PsModal,
  },
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
    shopId() {
      return window.psAccountShopId;
    },
  },
  methods: {
    ack(): void {
      localStorage.setItem(`incomingSubscriptionAck-${this.shopId}`, 'true');
      this.$segment.track('[FBK] User wants to be notified of monetization launch', {
        module: 'ps_facebook',
      });
    },
    isModalAlreadyAknowledged(): boolean {
      return !!JSON.parse(localStorage.getItem(`incomingSubscriptionAck-${this.shopId}`) || 'false');
    },
    md2html: (md: string) => (new Showdown.Converter()).makeHtml(md),
  },
});
</script>

<style scoped>
.text-muted {
  font-size: 12px;
}
</style>
