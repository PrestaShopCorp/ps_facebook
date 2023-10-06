import PsBanner from '@/components/commons/ps-banner.vue';

export default {
  title: 'Others/Components',
  component: PsBanner,
};

const Template = (args: any, {argTypes}: any) => ({
  props: Object.keys(argTypes),
  components: {PsBanner},
  template: `
    <div>
      <ps-banner class="mb-2">Primary</ps-banner>
      <ps-banner variant="secondary" class="mb-2">Secondary</ps-banner>
      <ps-banner variant="info" class="mb-2">Info</ps-banner>
      <ps-banner variant="danger" class="mb-2">Danger</ps-banner>
      <ps-banner variant="warning" class="mb-2">Warning</ps-banner>
      <ps-banner variant="success" class="mb-2">Success</ps-banner>
    </div>
  `,
});

export const Banner: any = Template.bind({});
Banner.args = {
};
