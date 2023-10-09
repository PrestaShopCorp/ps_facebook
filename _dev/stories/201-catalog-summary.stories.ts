import { cloneDeep } from "lodash";
import CatalogSummary from '@/components/catalog/catalog-summary.vue';
import {State as CatalogState} from '@/store/modules/catalog/state';
import {State as OnboardingState} from '@/store/modules/onboarding/state';
import { stateOnboarded } from "@/../.storybook/mock/onboarding";

export default {
  title: 'Catalog/Summary page',
  component: CatalogSummary,
};

const Template = (args: any, {argTypes}: any) => ({
  props: Object.keys(argTypes),
  components: {CatalogSummary},
  template: '<catalog-summary />',
  beforeMount: args.beforeMount,
});

export const FreshInstall: any = Template.bind({});
FreshInstall.args = {
  beforeMount: function(this: any) {
    (this.$store.state.onboarding as OnboardingState) = cloneDeep(stateOnboarded);
    (this.$store.state.catalog as CatalogState) = {
      warmedUp: true,
      enabledFeature: false,
      exportOn: false,
      categoryMatching: {
        matchingDone: false,
        matchingProgress: {total: 42, matched: 0},
      },
      report: {
        prevalidation: {
          lastScanDate: new Date('2023-09-22T11:59:59.568Z'),
          syncable: 134,
          notSyncable: 12,
        },
        reporting: {
          lastSyncDate: new Date('2023-09-23T02:23:52.123Z'),
          catalog: 42,
          errored: 42,
        },
      },
    };
  },
};

export const JustActivated: any = Template.bind({});
JustActivated.args = {
  beforeMount: function(this: any) {
    (this.$store.state.onboarding as OnboardingState) = cloneDeep(stateOnboarded);
    (this.$store.state.catalog as CatalogState) = {
      warmedUp: true,
      enabledFeature: true,
      exportOn: true,
      categoryMatching: {
        matchingDone: true,
        matchingProgress: {total: 42, matched: 23},
      },
      report: {
        prevalidation: {
          syncable: 134,
          notSyncable: 12,
          lastScanDate: new Date('2023-09-22T11:59:59.568Z'),
        },
        reporting: null,
      },
    };
  },
};

export const SyncOn: any = Template.bind({});
SyncOn.args = {
  beforeMount: function(this: any) {
    (this.$store.state.onboarding as OnboardingState) = cloneDeep(stateOnboarded);
    (this.$store.state.catalog as CatalogState) = {
      warmedUp: true,
      enabledFeature: true,
      exportOn: true,
      categoryMatching: {
        matchingDone: false,
        matchingProgress: {total: 42, matched: 23},
      },
      report: {
        prevalidation: {
          syncable: 134,
          notSyncable: 12,
          lastScanDate: new Date('2023-09-22T11:59:59.568Z'),
        },
        reporting: {
          lastSyncDate: new Date('2023-09-23T02:23:52.123Z'),
          catalog: 42,
          errored: 42,
        },
      },
    };
  },
};

export const SyncPaused: any = Template.bind({});
SyncPaused.args = {
  beforeMount: function(this: any) {
    (this.$store.state.onboarding as OnboardingState) = cloneDeep(stateOnboarded);
    (this.$store.state.catalog as CatalogState) = {
      warmedUp: true,
      enabledFeature: true,
      exportOn: false,
      categoryMatching: {
        matchingDone: false,
        matchingProgress: {total: 42, matched: 23},
      },
      report: {
        prevalidation: {
          syncable: 134,
          notSyncable: 12,
          lastScanDate: new Date('2023-09-22T11:59:59.568Z'),
        },
        reporting: {
          lastSyncDate: new Date('2023-09-23T02:23:52.123Z'),
          catalog: 42,
          errored: 42,
        },
      },
    };
  },
};

export const BothDone: any = Template.bind({});
BothDone.args = {
  beforeMount: function(this: any) {
    (this.$store.state.onboarding as OnboardingState) = cloneDeep(stateOnboarded);
    (this.$store.state.catalog as CatalogState) = {
      warmedUp: true,
      enabledFeature: true,
      exportOn: true,
      categoryMatching: {
        matchingDone: true,
        matchingProgress: {total: 42, matched: 42},
      },
      report: {
        prevalidation: {
          syncable: 134,
          notSyncable: 12,
          lastScanDate: new Date('2023-09-22T11:59:59.568Z'),
        },
        reporting: {
          lastSyncDate: new Date('2023-09-23T02:23:52.123Z'),
          catalog: 42,
          errored: 42,
        },
      },
    };
  },
};
