import AlertSyncEnabled from '@/components/catalog/summary/alert-sync-enabled.vue';

export default {
  title: "Catalog/Summary Page/Components/Alert",
  component: AlertSyncEnabled,
};

const Template = (args: any, { argTypes }: any) => ({
  props: Object.keys(argTypes),
  components: { AlertSyncEnabled },
  template: `
    <alert-sync-enabled />
  `,
});
export const SyncJustEnabled: any = Template.bind({});
SyncJustEnabled.args = {};