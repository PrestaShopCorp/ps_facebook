import ModalIncomingPaidVersion from '@/components/monetization/modal-incoming-paid-version.vue';

export default {
  title: 'Others/Modal',
  component: ModalIncomingPaidVersion,
};

const Template = (args: any, {argTypes}: any) => ({
  props: Object.keys(argTypes),
  components: {ModalIncomingPaidVersion},
  template: `
    <div>
      <span>If the modal does not appear, empty your local storage.</span>
      <modal-incoming-paid-version />
    </div>
    `,
});

export const IncomingPaidVersion: any = Template.bind({});
IncomingPaidVersion.args = {
};
