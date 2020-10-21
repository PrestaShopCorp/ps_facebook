<template>
  <li>
    <b-card no-body>
      <b-card-body>
        <div class="d-flex">
          <div class="description align-self-center flex-grow-1 pl-3 pr-2">
            <h3>
              <img
                class="mr-1"
                :src=image
                width="50"
              >
              {{ $t(`integrate.features.${featureName}.name`) }}
            </h3>
          </div>
        </div>
      </b-card-body>
    </b-card>
  </li>
</template>

<script>

export default {
  name: 'EnabledFeature',
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
  .flex-grow-1 {
    flex-grow:1
  }
</style>
