<template>
  <li>
    <b-card no-body>
      <b-card-body>
        <div class="d-flex">
          <img
            class="mr-2 align-self-center logo"
            :src="image"
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
          <b-button
            variant="disabled"
            class="ml-4 align-self-center"
          >
            {{ $t('integrate.add') }}
          </b-button>
        </div>
      </b-card-body>
    </b-card>
  </li>
</template>

<script>
export default {
  name: 'UnavailableFeature',
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
  },
  data() {
    return {
      featureName: this.name,
      featureImage: this.image,
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
