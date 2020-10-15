import {BTableSimple, BTbody} from 'bootstrap-vue';
import EditingRow from '../src/components/category-matching/editing-row.vue';

export default {
  title: 'Category matching/EditingRow component',
  component: EditingRow,
};

const params = ':language="language" :shopCategoryId="shopCategoryId" '
  + ':initialCategoryName="initialCategoryName" :initialCategoryId="initialCategoryId" '
  + ':initialSubcategoryName="initialSubcategoryName" :initialSubcategoryId="initialSubcategoryId" ';
const Template = (args: any, {argTypes}: any) => ({
  props: Object.keys(argTypes),
  components: {EditingRow, BTableSimple, BTbody},
  template: `<b-table-simple><b-tbody><editing-row ${params}>Tree item goes here</editing-row></b-tbody></b-table-simple>`,
});
export const Default: any = Template.bind({});
Default.args = {
  language: 'en-US',
  shopCategoryId: '42',
  initialCategoryName: 'My initial category',
  initialCategoryId: 7,
  initialSubcategoryName: 'My initial subcategory',
  initialSubcategoryId: 68,
};
