<!--**
 * 2007-2021 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2021 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *-->
<template>
  <spinner v-if="loading" />
  <div
    v-else
    id="catalogReportDetails"
  >
    <b-card class="card m-3">
      <b-button
        class="float-left mr-3"
        variant="outline-secondary"
        @click="$parent.back"
      >
        <i class="material-icons">keyboard_backspace</i>
        {{ $t('catalogSummary.backButton') }}
      </b-button>
      <h1>{{ $t('syncReport.title') }}</h1>

      <br><br>
      <b-form-group v-slot="{ ariaDescribedby }">
        <b-form-radio-group
          id="btn-radios-1"
          v-model="view"
          :options="views"
          :aria-describedby="ariaDescribedby"
          name="radios-btn-default"
          buttons
          button-variant="outline-primary"
        />
      </b-form-group>

      <p v-if="view === 'PREVALIDATION'">
        {{ $t('syncReport.prevalidationText') }}
      </p>
      <template v-else>
        <span
          v-if="lastSyncDate"
          class="text-muted text-italic"
        >
          {{ $t('syncReport.lastSyncDate', [
            lastSyncDate.toLocaleDateString(undefined, { dateStyle: 'medium' }),
            lastSyncDate.toLocaleTimeString(undefined),
          ]) }}
        </span>
        <br v-if="lastSyncDate">
        <p>{{ $t('syncReport.reportingText') }}</p>
      </template>

      <prevalidation-table
        v-if="view === 'PREVALIDATION' && prevalidationRows"
        :rows="prevalidationRows"
        :url="url"
      />
      <reporting-table
        v-if="view === 'REPORTING' && reportingRows"
        :rows="reportingRows"
        :url="url"
      />
    </b-card>
  </div>
</template>

<script>
import {defineComponent} from '@vue/composition-api';
import {
  BButton, BCard, BFormGroup, BFormRadioGroup,
} from 'bootstrap-vue';
import Spinner from '../spinner/spinner.vue';
import PrevalidationTable from './report-details/prevalidation-table.vue';
import ReportingTable from './report-details/reporting-table.vue';

export default defineComponent({
  name: 'CatalogReportDetails',
  components: {
    Spinner,
    BButton,
    BCard,
    PrevalidationTable,
    ReportingTable,
    BFormGroup,
    BFormRadioGroup,
  },
  props: {
    getProductsWithErrorsRoute: { // prevalidation scan reporting call
      type: String,
      required: false,
      default: () => global.psFacebookGetProductsWithErrors || null,
    },
    getProductStatusesRoute: { // reporting from FB
      type: String,
      required: false,
      default: () => global.psFacebookGetProductStatuses || null,
    },
    forceView: {
      type: String,
      required: false,
      default: () => 'PREVALIDATION',
    },
    forcePrevalidationRows: {
      type: Array,
      required: false,
      default: () => [],
    },
    forceReportingRows: {
      type: Object,
      required: false,
      default: () => ({}),
    },
  },
  data() {
    return {
      loading: true,
      views: [
        {
          text: this.$t('syncReport.views.prevalidation', ['']),
          value: 'PREVALIDATION',
        },
        {
          text: this.$t('syncReport.views.reporting', ['']),
          value: 'REPORTING',
          disabled: true,
        },
      ],
      view: this.forceView,
      prevalidationRows: [],
      reportingRows: [],
      url: '',
      lastSyncDate: null,
    };
  },
  mounted() {
    this.fetchData();
  },
  methods: {
    fetchPrevalidation(page = 0) {
      if (!this.getProductsWithErrorsRoute) {
        return Promise.reject(new Error('No route to fetch reporting data.'));
      }
      return fetch(`${this.getProductsWithErrorsRoute}&page=${page}`, {
        method: 'GET',
        headers: {'Content-Type': 'application/json', Accept: 'application/json'},
      }).then((res) => {
        if (!res.ok) {
          throw new Error(res.statusText || res.status);
        }
        return res.json();
      }).then((res) => {
        const {
          list, url, success, hasMoreProducts,
        } = res;
        if (success && list && list.length > 0) {
          const newPage = list.map((row) => ({...row, page}));
          this.prevalidationRows = this.prevalidationRows
            .filter((row) => row.page < page)
            .concat(newPage);
          this.url = url;
          return hasMoreProducts;
        }
        return false;
      });
    },
    fetchReporting(page = 0) {
      if (!this.getProductStatusesRoute) {
        return Promise.reject(new Error('No route to fetch reporting data.'));
      }
      return fetch(`${this.getProductStatusesRoute}&page=${page}`, {
        method: 'GET',
        headers: {'Content-Type': 'application/json', Accept: 'application/json'},
      }).then((res) => {
        if (!res.ok) {
          throw new Error(res.statusText || res.status);
        }
        return res.json();
      }).then((res) => {
        const {
          list, url, success, lastFinishedSyncStartedAt,
        } = res;

        if (!success) {
          if (page === 0) {
            this.reportingRows = null;
          }
          return 0;
        }

        const newPage = Object.values(list || {}).map((row) => ({...row, page}));
        this.reportingRows = this.reportingRows.filter((row) => row.page < page).concat(newPage);
        this.url = url;
        this.lastSyncDate = new Date(lastFinishedSyncStartedAt);
        return newPage.length;
      });
    },
    fetchData() {
      const fetches = [];
      if (this.forcePrevalidationRows === null || this.forcePrevalidationRows.length > 0) {
        this.prevalidationRows = this.forcePrevalidationRows;
      } else {
        fetches.push(this.fetchPrevalidation());
      }
      if (this.forceReportingRows === null || Object.values(this.forceReportingRows).length > 0) {
        this.reportingRows = this.forceReportingRows
          ? Object.values(this.forceReportingRows)
          : null;
      } else {
        fetches.push(this.fetchReporting());
      }

      this.loading = true;
      Promise.all(fetches).then(() => {
        this.loading = false;
      }).catch((error) => {
        console.error(error);
        this.loading = false;
      });
    },
  },
  watch: {
    reportingRows(newValue) {
      this.views[1].disabled = (newValue === null);
    },
    view(newView, oldView) {
      if (newView === oldView || !newView || !oldView) {
        return;
      }
      this.$segment.track(
        newView === 'PREVALIDATION' ? 'See prevalidation scan details' : 'See reporting details',
        {
          module: 'ps_facebook',
        },
      );
    },
  },
});
</script>

<style lang="scss" scoped>
  .text-italic {
    font-style: italic;
  }
</style>
