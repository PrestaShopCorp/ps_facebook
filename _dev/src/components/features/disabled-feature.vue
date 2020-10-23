<template>
  <li>
    <b-card no-body>
      <b-card-body>
        <div class="d-flex">
          <img
            class="mr-2 align-self-center logo d-none d-md-block"
            :src="page_shop"
            :alt="$t(`integrate.features.${name}.name`)"
            width="80"
          >
          <div class="description align-self-center flex-grow-1 pl-3 pr-2">
            <h3>
              {{ $t(`integrate.features.${name}.name`) }}
            </h3>
            <p class="small-text text-muted">
              {{ $t(`integrate.features.${name}.description`) }}
            </p>
          </div>
          <a
            class="align-self-center"
            :href="manageRoute"
            target="_blank"
          >
            <b-button
              variant="outline-primary"
              class="ml-4 align-self-center"
            >
              {{ $t('integrate.buttons.add') }}
            </b-button>
          </a>
        </div>
      </b-card-body>
    </b-card>
  </li>
</template>

<script>
import {defineComponent} from '@vue/composition-api';
import {BCard, BCardBody} from 'bootstrap-vue';
// import ig_shopping from '../../assets/ig_shopping.png';
// import messenger_chat from '../../assets/messenger_chat.png';
// import page_cta from '../../assets/page_cta.png';
import page_shop from '../../assets/page_shop.png';

export default defineComponent({
  name: 'DisabledFeature',
  components: {
    BCard,
    BCardBody,
  },
  mixins: [],
  props: {
    name: {
      type: String,
      required: false,
      default: () => '',
    },
    manageRoute: {
      type: String,
      required: false,
      default: () => global.facebookManageFeaturesRoute,
    },
  },
  data() {
    // const images = {
    //   ig_shopping: ig_shopping,
    //   messenger_chat: messenger_chat,
    //   page_cta: page_cta,
    //   page_shop: page_shop,
    // };

    return {
      page_shop,
    };
  },
  methods: {
    getImgUrl(feature) {
      console.log(this.images);
      return this.images[feature];
    },
  },
});
</script>

<style lang="scss" scoped>
  .logo {
    float: left;
    display: block;
  }

  .description {
    display: table-cell;
  }

  .flex-grow-1 {
    flex-grow:1
  }
</style>
