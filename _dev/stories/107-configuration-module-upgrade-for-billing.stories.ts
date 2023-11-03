import AlertModuleUpgradeForBilling from '@/components/monetization/alert-module-upgrade-for-billing.vue';
import ModalModuleUpgradeForBilling from '@/components/monetization/modal-module-upgrade-for-billing.vue';

export default {
  title: "Configuration/Billing",
};

const Template = (args: any, { argTypes }: any) => ({
  props: Object.keys(argTypes),
  components: {
    AlertModuleUpgradeForBilling,
    ModalModuleUpgradeForBilling,
  },
  template: `
  <div>
    <alert-module-upgrade-for-billing />
    <modal-module-upgrade-for-billing />
  </div>
  `,
});
export const UpgradeRequired: any = Template.bind({});
UpgradeRequired.args = {};
