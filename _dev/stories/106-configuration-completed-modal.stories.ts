import ModalConfigurationCompleted from '@/components/configuration/modal-configuration-completed.vue';

export default {
  title: 'Configuration/Modals',
  component: ModalConfigurationCompleted,
};

const Template = (args: any, { argTypes }: any) => ({
  props: Object.keys(argTypes),
  components: { ModalConfigurationCompleted },
  template: `
    <modal-configuration-completed
      @onHide="onHide"
    />
  `,
});
export const ConfigurationCompleted: any = Template.bind({});
ConfigurationCompleted.args = { };
