<template>
  <ps-modal
    id="ps_facebook_modal_module_upgrade_for_billing"
    ref="ps_facebook_modal_module_upgrade_for_billing"
    :title="$t('configuration.upgradeForBillingStep.modal.title')"
    hide-footer
    visible
  >
    <p class="ps_gs-fz-18 font-weight-600">
      {{ $t('configuration.upgradeForBillingStep.modal.subtitle') }}
    </p>
    <p
      v-html="md2html($t('configuration.upgradeForBillingStep.modal.description', {
        price,
        date,
      }))"
    />
    <i18n
      path="configuration.upgradeForBillingStep.modal.needHelp"
      tag="p"
    >
      <b-link
        :to="{ name: 'help' }"
        class="text-decoration-underline"
      >
        {{ $t("help.help.contactUs") }}
      </b-link>
    </i18n>
    <p class="text-muted ps_gs-fz-12">
      {{ $t('configuration.upgradeForBillingStep.IncludedInHosted') }}
    </p>
    <div
      class="my-4 d-flex justify-content-end"
    >
      <button-module-upgrade />
    </div>
  </ps-modal>
</template>

<script lang="ts">
import {defineComponent} from 'vue';
import Showdown from 'showdown';
import PsModal from '@/components/commons/ps-modal.vue';
import ButtonModuleUpgrade from './button-module-upgrade.vue';

export default defineComponent({
  name: 'ModalModuleUpgradeRequiredForBilling',
  components: {
    PsModal,
    ButtonModuleUpgrade,
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
    md2html: (md: string) => (new Showdown.Converter()).makeHtml(md),
  },
});
</script>
