<template>
  <li>
    <b-card no-body>
      <b-card-body>
        <div class="d-flex">
          <img
            class="mr-2 align-self-center logo"
            :src=image
            width="50"
          >
          <div class="description align-self-center flex-grow-1 pl-3 pr-2">
            <h3>
              {{ $t(`integrate.features.${featureName}.name`) }}
            </h3>
            <p class="small-text text-muted">
              {{ $t(`integrate.features.${featureName}.description`) }}
            </p>
          </div>
          <a
            class="align-self-center"
            :href=featureManageRoute
            target="_blank"
          >
            <b-button
              variant="primary"
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
export default {
  name: 'DisabledFeature',
  mixins: [],
  props: {
    name: {
      type: String,
      required: false,
      default: () => '',
    },
    image: {
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
    return {
      featureName: this.name,
      featureImage: this.image,
      featureManageRoute: this.manageRoute,
    };
  },
};
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
