<template>
  <div class="mb-4 d-flex flex-row">
    <img
      class="mr-2"
      src="@/assets/logo_prestashop.svg"
      width="44"
      height="44"
      alt="PrestaShop Social logo"
    >
    <div class="flex-grow-1">
      <div class="d-flex align-items-start mb-3">
        <div>
          <div class="d-flex">
            <h2 class="ps_gs-fz-16 font-weight-600 mb-0">
              {{ $t('catalog.summaryPage.productCatalog.productVerification.stepTitle') }}
            </h2>
            <b-button
              id="tooltip-verified-product"
              class="ml-1 p-0 d-flex"
              variant="text"
            >
              <span class="material-icons-round ps_gs-fz-20 mb-1 ml-0 text-secondary">
                info_outlined
              </span>
            </b-button>
            <b-tooltip
              target="tooltip-verified-product"
              triggers="hover"
              container="#psFacebookApp"
              custom-class="tooltip-lg"
            >
              <span
                v-html="md2html(
                  $t('catalog.summaryPage.productCatalog.productVerification.stepDetails')
                )"
              />
            </b-tooltip>
          </div>
          {{ lastScanText }}
        </div>
        <b-button
          variant="outline-primary"
          class="ml-auto"
          :disabled="!active || scanRequestStatus === RequestState.PENDING"
          @click="$emit('triggerScan')"
        >
          <i
            class="material-icons"
            :class="{'fa-spin': scanRequestStatus === RequestState.PENDING}"
          >autorenew</i>
          {{ $t('cta.rescan') }}
        </b-button>
      </div>

      <div class="p-0 container-fluid">
        <div class="row mx-n1 no-gutters mb-3">
          <status-card-component
            v-for="(status, index) in statusCards"
            :key="index"
            :status="status"
            :loading="loading"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import {PropType, defineComponent} from 'vue';
import showdown from 'showdown';
import StatusCardComponent, {StatusCardParameters} from '../status-card.vue';
import {ValidationReport} from '@/store/modules/catalog/state';
import CatalogTabPages from '@/components/catalog/pages';
import {RequestState} from '@/store/modules/catalog/types';

export default defineComponent({
  components: {
    StatusCardComponent,
  },
  data() {
    return {
      RequestState,
    };
  },
  props: {
    verificationsStats: {
      type: Object as PropType<ValidationReport>,
      required: true,
    },
    active: {
      type: Boolean,
      required: true,
    },
    loading: {
      type: Boolean,
      required: true,
    },
  },
  computed: {
    scanRequestStatus(): RequestState {
      return this.$store.state.catalog.requests.scan;
    },
    lastScanText(): string {
      if (this.scanRequestStatus === RequestState.PENDING) {
        return this.$t('catalog.summaryPage.productCatalog.productVerification.scanStatus.inProgress');
      }

      if (!this.verificationsStats.lastScanDate) {
        return this.$t('catalog.summaryPage.productCatalog.productVerification.scanStatus.notSubscribed');
      }

      return this.$t('catalog.summaryPage.productCatalog.productVerification.scanStatus.lastScanDate', {
        date: this.verificationsStats.lastScanDate.toLocaleDateString(
          undefined,
          {year: 'numeric', month: 'numeric', day: 'numeric'},
        ),
        time: this.verificationsStats.lastScanDate.toLocaleTimeString(
          undefined,
          {hour: '2-digit', minute: '2-digit'},
        ),
      });
    },
    productsInCatalog(): number|null {
      if (this.verificationsStats.notSyncable === null
        || this.verificationsStats.syncable === null) {
        return null;
      }
      return this.verificationsStats.notSyncable + this.verificationsStats.syncable;
    },
    statusCards(): StatusCardParameters[] {
      return [{
        title: this.$t('catalog.summaryPage.productCatalog.productVerification.reportCards.productsInCatalog'),
        description: this.$t('catalog.summaryPage.productCatalog.productVerification.reportCards.productsInCatalogDescription'),
        value: this.productsInCatalog,
        variant: 'info',
        icon: 'redeem',
        reverseColors: false,
        ...(+(this.productsInCatalog || 0) && {
          link: {
            href: this.$store.state.app.links.coreProductsPageUrl,
            target: '_blank',
          },
        }),
      },
      {
        title: this.$t('catalog.summaryPage.productCatalog.productVerification.reportCards.verified'),
        description: this.$t('catalog.summaryPage.productCatalog.productVerification.reportCards.verifiedDescription'),
        value: this.verificationsStats.syncable,
        variant: 'success',
        icon: 'send',
        reverseColors: false,
      },
      {
        title: this.$t('catalog.summaryPage.productCatalog.productVerification.reportCards.nonCompliant'),
        description: this.$t('catalog.summaryPage.productCatalog.productVerification.reportCards.nonCompliantDescription'),
        value: this.verificationsStats.notSyncable,
        variant: 'danger',
        icon: 'remove_shopping_cart',
        reverseColors: false,
        ...((this.verificationsStats.notSyncable !== null) && {
          link: {
            to: {name: CatalogTabPages.prevalidationDetails},
          },
          reverseColors: !!this.verificationsStats.notSyncable,
        }),
      },
      ];
    },
  },
  methods: {
    md2html: (md: string) => (new showdown.Converter()).makeHtml(md),
  },
});
</script>
