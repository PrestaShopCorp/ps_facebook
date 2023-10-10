import BannerCatalogSharing from '@/components/catalog/summary/banner-catalog-sharing.vue';

export default {
  title: "Catalog/Summary Page/Components/Banner",
  component: BannerCatalogSharing,
};

const Template = (args: any, { argTypes }: any) => ({
  props: Object.keys(argTypes),
  components: { BannerCatalogSharing },
  template: `
    <banner-catalog-sharing :onCatalogPage="onCatalogPage" />
  `,
});
export const GoToCatalogPage: any = Template.bind({});
GoToCatalogPage.args = {
  onCatalogPage: false,
};

export const OnCatalogPage: any = Template.bind({});
OnCatalogPage.args = {
  onCatalogPage: true,
};
