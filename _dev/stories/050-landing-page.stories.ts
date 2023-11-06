import LandingPage from "@/views/landing-page.vue";

export default {
  title: "Landing page/Landing page",
  component: LandingPage,
};


const Template = (args: any, { argTypes }: any) => ({
  props: Object.keys(argTypes),
  components: { LandingPage },
  template: `<landing-page />`,
  beforeMount: args.beforeMount,
});

export const Default: any = Template.bind({});
Default.args = {
};
