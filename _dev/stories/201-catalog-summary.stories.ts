import CatalogSummary from '../src/components/catalog/summary.vue';

export default {
  title: 'Catalog/Summary page',
  component: CatalogSummary,
};

const Template = (args: any, {argTypes}: any) => ({
  props: Object.keys(argTypes),
  components: {CatalogSummary},
  template: '<catalog-summary :data="data" />',
});

export const FreshInstall: any = Template.bind({});
FreshInstall.args = {
  data: {
    exportDone: false,
    matchingDone: false,
    matchingProgress: {total: 42, matched: 0},
    reporting: {
      total: 0,
      pending: 0,
      approved: 0,
      disapproved: 0,
    },
  },
};

export const Progressing: any = Template.bind({});
Progressing.args = {
  data: {
    exportDone: false,
    matchingDone: true,
    matchingProgress: {total: 42, matched: 23},
    reporting: {
      total: 42,
      pending: 2,
      approved: 39,
      disapproved: 1,
    },
  },
};

export const Finished: any = Template.bind({});
Finished.args = {
  data: {
    exportDone: true,
    matchingDone: true,
    matchingProgress: {total: 42, matched: 42},
    reporting: {
      total: 42,
      pending: 0,
      approved: 41,
      disapproved: 1,
    },
  },
};
