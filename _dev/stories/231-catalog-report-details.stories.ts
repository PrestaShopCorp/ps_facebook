import ReportDetails from '../src/components/catalog/report-details.vue';

export default {
  title: 'Catalog/Report details page',
  component: ReportDetails,
};

const Template = (args: any, {argTypes}: any) => ({
  props: Object.keys(argTypes),
  components: {ReportDetails},
  template: '<report-details />',
});

export const Default: any = Template.bind({});
Default.args = {

};
// TODO
