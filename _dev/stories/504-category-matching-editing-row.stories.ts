import {BTableSimple, BTbody} from 'bootstrap-vue';
import EditingRow from '../src/components/category-matching/editing-row.vue';

export default {
  title: 'Catalog/Category matching edit page/EditingRow component',
  component: EditingRow,
};

const params = ':language="language" :shopCategoryId="shopCategoryId" '
  + ':initialCategoryName="initialCategoryName" :initialCategoryId="initialCategoryId" '
  + ':initialSubcategoryName="initialSubcategoryName" :initialSubcategoryId="initialSubcategoryId" '
  + ':initialPropagation="initialPropagation" :saveMatchingCallback="saveMatchingCallback" '
  + ':autocompletionApi="autocompletionApi" @onCategoryMatched="onCategoryMatched" ';

const Template = (args: any, {argTypes}: any) => ({
  props: Object.keys(argTypes),
  components: {EditingRow, BTableSimple, BTbody},
  template: `<b-table-simple><b-tbody><editing-row ${params}>Tree item goes here</editing-row></b-tbody></b-table-simple>`,
});

export const Default: any = Template.bind({});
Default.args = {
  language: 'en-US',
  shopCategoryId: '42',
  initialCategoryName: 'Animals & Pet Supplies',
  initialCategoryId: 1,
  initialSubcategoryName: 'Pet Supplies > Bird Supplies > Bird Cage Accessories',
  initialSubcategoryId: 7385,
  initialPropagation: false,
  autocompletionApi: 'https://facebook-api.psessentials.net/taxonomy/',
};

export const Void: any = Template.bind({});
Void.args = {
  language: 'en-US',
  shopCategoryId: '42',
  initialCategoryName: null,
  initialCategoryId: null,
  initialSubcategoryName: null,
  initialSubcategoryId: null,
  initialPropagation: null,
  autocompletionApi: 'https://facebook-api.psessentials.net/taxonomy/',
};

export const Void2: any = Template.bind({});
Void2.args = {
  language: 'en-US',
  shopCategoryId: '42',
  initialCategoryName: 'Animals & Pet Supplies',
  initialCategoryId: 1,
  initialSubcategoryName: null,
  initialSubcategoryId: null,
  initialPropagation: true,
  autocompletionApi: 'https://facebook-api.psessentials.net/taxonomy/',
};

export const Void3: any = Template.bind({});
Void3.args = {
  language: 'en-US',
  shopCategoryId: '42',
  initialCategoryName: null,
  initialCategoryId: null,
  initialSubcategoryName: null,
  initialSubcategoryId: null,
  initialPropagation: true,
  autocompletionApi: 'https://facebook-api.psessentials.net/taxonomy/',
};
