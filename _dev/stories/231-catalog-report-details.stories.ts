import ReportDetails from '../src/components/catalog/report-details.vue';

export default {
  title: 'Catalog/Report details page',
  component: ReportDetails,
};

const prevalidation1 = {
  id_product: '15',
  id_product_attribute: null,
  name: 'Pack Mug + Affiche encadrée',
  id_lang: '1',
  language: 'fr',
  has_manufacturer_or_ean_or_upc_or_isbn: '0',
  has_cover: '1',
  has_link: '1',
  has_price_tax_excl: '1',
  has_description_or_short_description: '1',
};

const reporting = {
  '1-1': {
    id_product: '1',
    id_product_attribute: '1',
    name: 'T-shirt imprimé colibri',
    date_upd: '2021-02-12 08:42:43',
    status: 'Pending',
    messages: {
      base: 'Ongeldige waarde: Value passed at position 12 (id=1-1) is invalid: \'Invalid Date Range: The date range specified in property sale_price_effective_date is invalid.\'',
    },
  },
  '1-2': {
    id_product: '1',
    id_product_attribute: '2',
    name: 'T-shirt imprimé colibri',
    date_upd: '2021-02-12 08:42:43',
    status: 'Pending',
    messages: {
      base: 'Ongeldige waarde: Value passed at position 19 (id=1-2) is invalid: \'Invalid Date Range: The date range specified in property sale_price_effective_date is invalid.\''
    },
  },
};

const Template = (args: any, {argTypes}: any) => ({
  props: Object.keys(argTypes),
  components: {ReportDetails},
  template: '<report-details :forceView="forceView" :forcePrevalidationRows="forcePrevalidationRows" :forceReportingRows="forceReportingRows" />',
});

export const PrevalidationErrors: any = Template.bind({});
PrevalidationErrors.args = {
  forceView: 'PREVALIDATION',
  forcePrevalidationRows: [prevalidation1],
  forceReportingRows: reporting,
};

export const NoPrevalidationError: any = Template.bind({});
NoPrevalidationError.args = {
  forceView: 'PREVALIDATION',
  forcePrevalidationRows: [],
  forceReportingRows: reporting,
};

export const Reporting: any = Template.bind({});
Reporting.args = {
  forceView: 'REPORTING',
  forcePrevalidationRows: [prevalidation1],
  forceReportingRows: reporting,
};

export const EmptyReporting: any = Template.bind({});
EmptyReporting.args = {
  forceView: 'REPORTING',
  forcePrevalidationRows: [prevalidation1],
  forceReportingRows: {},
};

export const NoReportingAvailable: any = Template.bind({});
NoReportingAvailable.args = {
  forceView: 'PREVALIDATION',
  forcePrevalidationRows: [prevalidation1],
  forceReportingRows: null,
};
