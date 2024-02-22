<template>
  <div class="mb-4 d-flex flex-row">
    <img
      class="mr-2"
      src="@/assets/facebook_logo.svg"
      width="44"
      height="44"
      alt="PrestaShop Social logo"
    >
    <div class="flex-grow-1">
      <div class="d-flex align-items-start mb-3">
        <div>
          <div class="d-flex">
            <h2 class="ps_gs-fz-16 font-weight-600 mb-0">
              {{ $t('catalog.summaryPage.productCatalog.productsSentToFacebook.stepTitle') }}
            </h2>
            <b-button
              id="tooltip-submitted-product"
              class="ml-1 p-0 d-flex"
              variant="text"
            >
              <span
                class="material-icons material-icons-round ps_gs-fz-20 mb-1 ml-0 text-secondary"
              >
                help_outline
              </span>
            </b-button>
            <b-tooltip
              target="tooltip-submitted-product"
              triggers="hover"
              container="#psFacebookApp"
              custom-class="tooltip-lg"
            >
              <span
                v-html="md2html(
                  $t('catalog.summaryPage.productCatalog.productsSentToFacebook.stepDetails')
                )"
              />
            </b-tooltip>
          </div>
          {{ lastSyncText }}
        </div>
        <b-form-checkbox
          v-if="pageIsActive"
          switch
          size="lg"
          class="ml-1 ps_gs-switch ml-auto"
          :checked="syncIsActive"
          @click.native.prevent="$emit('toggleSync')"
          :disabled="syncToggleRequestStatus === RequestState.PENDING"
          inline
        >
          <span class="small">
            {{ syncIsActive
              ? $t('catalog.summaryPage.productCatalog.catalogExportActivated')
              : $t('catalog.summaryPage.productCatalog.catalogExportPaused') }}
          </span>
        </b-form-checkbox>
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

      <full-sync-request />
    </div>
  </div>
</template>

<script lang="ts">
import {PropType, defineComponent} from 'vue';
import showdown from 'showdown';
import StatusCardComponent, {StatusCardParameters} from '@/components/catalog/summary/status-card.vue';
import {SyncReport} from '@/store/modules/catalog/state';
import CatalogTabPages from '@/components/catalog/pages';
import {RequestState} from '@/store/types';
import FullSyncRequest from '@/components/catalog/summary/panel/full-sync-request.vue';

export default defineComponent({
  components: {
    StatusCardComponent,
    FullSyncRequest,
  },
  data() {
    return {
      RequestState,
    };
  },
  props: {
    pageIsActive: {
      type: Boolean,
      required: true,
    },
    syncIsActive: {
      type: Boolean,
      required: true,
    },
    validationSummary: {
      type: Object as PropType<SyncReport>,
      required: true,
    },
    catalogId: {
      type: String as PropType<string|null>,
      required: false,
      default: null,
    },
    loading: {
      type: Boolean,
      required: true,
    },
  },
  computed: {
    syncToggleRequestStatus(): RequestState {
      return this.$store.state.catalog.requests.syncToggle;
    },
    lastSyncText(): string {
      if (!this.pageIsActive) {
        return this.$t('catalog.summaryPage.productCatalog.productsSentToFacebook.syncStatus.notSubscribed');
      }

      if (!this.syncIsActive) {
        return this.$t('catalog.summaryPage.productCatalog.productsSentToFacebook.syncStatus.paused');
      }

      if (!this.validationSummary.lastSyncDate) {
        return this.$t('catalog.summaryPage.productCatalog.productsSentToFacebook.syncStatus.firstSyncSoon');
      }

      return this.$t('catalog.summaryPage.productCatalog.productsSentToFacebook.syncStatus.lastSyncDate', {
        date: this.validationSummary.lastSyncDate.toLocaleDateString(
          window.i18nSettings.languageLocale.substring(0, 2),
          {dateStyle: 'long'},
        ),
        time: this.validationSummary.lastSyncDate.toLocaleTimeString(
          undefined,
          {hour: '2-digit', minute: '2-digit'},
        ),
      });
    },
    viewCatalogUrl(): string {
      return `https://www.facebook.com/products/catalogs/${this.catalogId}/products`;
    },
    statusCards(): StatusCardParameters[] {
      return [{
        title: this.$t('catalog.summaryPage.productCatalog.productsSentToFacebook.reportCards.approved'),
        description: this.$t('catalog.summaryPage.productCatalog.productsSentToFacebook.reportCards.approvedDescription'),
        value: this.validationSummary.catalog,
        variant: 'success',
        icon: 'check',
        reverseColors: false,
        ...((this.catalogId && this.validationSummary.catalog) && {
          link: {
            href: this.viewCatalogUrl,
            target: '_blank',
          },
        }),
      },
      {
        title: this.$t('catalog.summaryPage.productCatalog.productsSentToFacebook.reportCards.disapproved'),
        description: this.$t('catalog.summaryPage.productCatalog.productsSentToFacebook.reportCards.disapprovedDescription'),
        value: this.validationSummary.errored,
        variant: 'danger',
        icon: 'cancel',
        reverseColors: false,
        ...(!!this.validationSummary.errored && {
          link: {
            to: {name: CatalogTabPages.reportDetails},
          },
          reverseColors: !!this.validationSummary.errored,
        }),
      }];
    },
  },
  methods: {
    md2html: (md: string) => (new showdown.Converter()).makeHtml(md),
  },
  watch: {
    loading(newVal, oldVal) {
      if (oldVal === true && newVal === false) {
        this.$segment.identify(this.$store.state.accounts.shopIdPsAccounts, {
          fbk_user_has_approved_products_on_fb: !!this.validationSummary.catalog,
          fbk_user_has_disapproved_products_on_fb: !!this.validationSummary.errored,
        });
      }
    },
  },
});
</script>
