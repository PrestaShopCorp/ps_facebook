import { cloneDeep } from "lodash";
import CatalogSummary from '@/components/catalog/catalog-summary.vue';
import {State as CatalogState} from '@/store/modules/catalog/state';
import {State as OnboardingState} from '@/store/modules/onboarding/state';
import { stateOnboarded } from "@/../.storybook/mock/onboarding";
import { RequestState } from "@/store/modules/catalog/types";

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

export const CatalogNotShared: any = Template.bind({});
CatalogNotShared.args = {
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
          lastScanDate: null,
          syncable: null,
          notSyncable: null,
        },
        reporting: {
          lastSyncDate: null,
          catalog: null,
          errored: null,
        },
      },
      requests: {
        requestNextSyncFull: RequestState.IDLE,
        scan: RequestState.IDLE,
        syncToggle: RequestState.IDLE,
        catalogReport: RequestState.IDLE,
      },
    };
  },
};

export const CatalogRecentlyShared: any = Template.bind({});
CatalogRecentlyShared.args = {
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
        reporting: {
          lastSyncDate: null,
          catalog: null,
          errored: null,
        },
      },
      requests: {
        requestNextSyncFull: RequestState.IDLE,
        scan: RequestState.IDLE,
        syncToggle: RequestState.IDLE,
        catalogReport: RequestState.IDLE,
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
        matchingProgress: {total: 42, matched: 0},
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
      requests: {
        requestNextSyncFull: RequestState.IDLE,
        scan: RequestState.IDLE,
        syncToggle: RequestState.IDLE,
        catalogReport: RequestState.IDLE,
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
      requests: {
        requestNextSyncFull: RequestState.IDLE,
        scan: RequestState.IDLE,
        syncToggle: RequestState.IDLE,
        catalogReport: RequestState.IDLE,
      },
    };
  },
};

export const FullConfiguration: any = Template.bind({});
FullConfiguration.args = {
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
      requests: {
        requestNextSyncFull: RequestState.IDLE,
        scan: RequestState.IDLE,
        syncToggle: RequestState.IDLE,
        catalogReport: RequestState.IDLE,
      },
    };
  },
};
