import BannerCatalogSharing from "@/components/configuration/banner-catalog-sharing.vue";

export default {
  title: "Configuration/Banner",
  component: BannerCatalogSharing,
};

const Template = (args: any, { argTypes }: any) => ({
  props: Object.keys(argTypes),
  components: { BannerCatalogSharing },
  template: `
    <banner-catalog-sharing />
  `,
});
export const GoToCatalogPage: any = Template.bind({});
GoToCatalogPage.args = {};
