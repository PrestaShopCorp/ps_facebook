<!--**
 * 2007-2020 PrestaShop and Contributors
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
 * @copyright 2007-2020 PrestaShop SA and Contributors
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
        ></b-form-radio-group>
      </b-form-group>

      <p v-if="view === 'PREVALIDATION'">{{ $t('syncReport.prevalidationText') }}</p>
      <template v-else>
        <span v-if="lastSyncDate" class="text-muted text-italic">
          {{ $t('syncReport.lastSyncDate', [lastSyncDate]) }}
        </span>
        <br v-if="lastSyncDate">
        <p>{{ $t('syncReport.reportingText') }}</p>
      </template>

      <prevalidation-table v-if="view === 'PREVALIDATION' && prevalidationRows" :rows="prevalidationRows" :url="url" />
      <reporting-table v-if="view === 'REPORTING' && reportingRows" :rows="reportingRows" :url="url" />
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
      type: Array,
      required: false,
      default: () => [],
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
      this.prevalidationRows = [];
      if (!this.getProductsWithErrorsRoute) {
        console.error('No route to fetch prevalidation data.');
        return Promise.resolve();
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
        const {list, url, success} = res;
        if (success && list && list.length > 0) {
          this.prevalidationRows = list;
          this.url = url;
        }
      }).catch((error) => {
        console.error(error);
      });
    },
    fetchReporting(page = 0) {
      this.reportingRows = [];
      if (!this.getProductStatusesRoute) {
        console.error('No route to fetch reporting data.');
        return Promise.resolve();
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
        console.log('#####1234345', JSON.stringify(res.list));
        const {
          list, url, success, lastFinishedSyncStartedAt,
        } = res;
        if (!success) {
          this.reportingRows = null;
        } else {
          this.reportingRows = list;
          this.url = url;
          this.lastSyncDate = new Date(lastFinishedSyncStartedAt);
        }
      }).catch((error) => {
        console.error(error);
      });
    },
    fetchData() {
      const fetches = [];
      if (this.forcePrevalidationRows === null || this.forcePrevalidationRows.length > 0) {
        this.prevalidationRows = this.forcePrevalidationRows;
      } else {
        fetches.push(this.fetchPrevalidation(0));
      }
      if (this.forceReportingRows === null || this.forceReportingRows.length > 0) {
        this.reportingRows = this.forceReportingRows;
      } else {
        fetches.push(this.fetchReporting(0));
      }

      this.loading = true;
      Promise.all(fetches).then(() => {
        this.loading = false;
      });
    },
  },
  watch: {
    prevalidationRows(newValue) {
      // eslint-disable-next-line no-nested-ternary
      const count = newValue.length === 0 ? '' : (
        newValue.length === 1
          ? this.$t('syncReport.views.oneError')
          : this.$t('syncReport.views.manyErrors', [newValue.length])
      );
      this.views[0].text = this.$t('syncReport.views.prevalidation', [count]);
    },
    reportingRows(newValue) {
      // eslint-disable-next-line no-nested-ternary
      const count = (!newValue || Object.keys(newValue).length === 0) ? '' : (
        Object.keys(newValue).length === 1
          ? this.$t('syncReport.views.oneError')
          : this.$t('syncReport.views.manyErrors', [Object.keys(newValue).length])
      );
      this.views[1].text = this.$t('syncReport.views.reporting', [count]);
      this.views[1].disabled = (newValue === null);
    },
  },
});
</script>

<style lang="scss" scoped>
  .text-italic {
    font-style: italic;
  }
</style>
