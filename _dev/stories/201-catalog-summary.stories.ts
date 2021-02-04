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
    exportOn: false,
    matchingDone: false,
    matchingProgress: {total: 42, matched: 0},
    validation: {
      prevalidation: {
        syncable: 134,
        notSyncable: 12,
      },
      reporting: {
        lastSyncDate: new Date(),
        catalog: 42,
        errored: 42,
      },
    },
    catalogId: '34567890',
  },
};

export const JustActivated: any = Template.bind({});
JustActivated.args = {
  data: {
    exportDone: true,
    exportOn: true,
    matchingDone: true,
    matchingProgress: {total: 42, matched: 23},
    validation: {
      prevalidation: {
        syncable: 134,
        notSyncable: 12,
      },
      reporting: { },
    },
    catalogId: '34567890',
  },
};

export const SyncOn: any = Template.bind({});
SyncOn.args = {
  data: {
    exportDone: true,
    exportOn: true,
    matchingDone: false,
    matchingProgress: {total: 42, matched: 23},
    validation: {
      prevalidation: {
        syncable: 134,
        notSyncable: 12,
      },
      reporting: {
        lastSyncDate: new Date(),
        catalog: 42,
        errored: 42,
      },
    },
    catalogId: '34567890',
  },
};

export const SyncPaused: any = Template.bind({});
SyncPaused.args = {
  data: {
    exportDone: true,
    exportOn: false,
    matchingDone: false,
    matchingProgress: {total: 42, matched: 23},
    validation: {
      prevalidation: {
        syncable: 134,
        notSyncable: 12,
      },
      reporting: {
        lastSyncDate: new Date(),
        catalog: 42,
        errored: 42,
      },
    },
    catalogId: '34567890',
  },
};

export const BothDone: any = Template.bind({});
BothDone.args = {
  data: {
    exportDone: true,
    exportOn: true,
    matchingDone: true,
    matchingProgress: {total: 42, matched: 42},
    validation: {
      prevalidation: {
        syncable: 134,
        notSyncable: 12,
      },
      reporting: {
        lastSyncDate: new Date(),
        catalog: 42,
        errored: 42,
      },
    },
  },
};
