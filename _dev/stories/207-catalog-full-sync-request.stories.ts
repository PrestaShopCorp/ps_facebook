import cloneDeep from 'lodash.clonedeep';
import FullSyncRequest from "@/components/catalog/summary/panel/full-sync-request.vue";
import {RequestState} from '@/store/types';
import {State as CatalogState, state} from '@/store/modules/catalog/state';

export default {
  title: "Catalog/Summary Page/Components/Full Sync Request",
  component: FullSyncRequest,
};

const Template = (args: any, { argTypes }: any) => ({
  props: Object.keys(argTypes),
  components: { FullSyncRequest },
  template: `
    <full-sync-request />
  `,
  mounted: args.mounted,
});
export const Default: any = Template.bind({});
Default.args = {
  mounted: function(this: any) {
    (this.$store.state.catalog as CatalogState) = cloneDeep(state);
  },
};

export const RequestFailed: any = Template.bind({});
RequestFailed.args = {
  mounted: function(this: any) {
    (this.$store.state.catalog as CatalogState) = {
      ...cloneDeep(state),
      requests: {
        ...state.requests,
        requestNextSyncFull: RequestState.FAILED,
      },
    };
  },
};

export const RequestSucceeded: any = Template.bind({});
RequestSucceeded.args = {
  mounted: function(this: any) {
    (this.$store.state.catalog as CatalogState) = {
      ...cloneDeep(state),
      requests: {
        ...state.requests,
        requestNextSyncFull: RequestState.SUCCESS,
      },
    };
  },
};
