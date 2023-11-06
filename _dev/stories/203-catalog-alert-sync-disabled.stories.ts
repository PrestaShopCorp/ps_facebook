import AlertSyncDisabled from '@/components/catalog/summary/alert-sync-disabled.vue';

export default {
  title: "Catalog/Summary Page/Components/Alert",
  component: AlertSyncDisabled,
};

const Template = (args: any, { argTypes }: any) => ({
  props: Object.keys(argTypes),
  components: { AlertSyncDisabled },
  template: `
    <alert-sync-disabled />
  `,
});
export const SyncIsDisabled: any = Template.bind({});
SyncIsDisabled.args = {};