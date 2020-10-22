<template>
  <li>
    <b-card no-body>
      <b-card-body>
        <div class="d-flex">
          <img
            class="mr-2 align-self-center logo d-none d-md-block"
            :src="require(`@/assets/${name}_disabled.png`)"
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
          <b-button
            variant="disabled"
            class="ml-4 align-self-center"
          >
            {{ $t('integrate.buttons.add') }}
          </b-button>
        </div>
      </b-card-body>
    </b-card>
  </li>
</template>

<script>
import {defineComponent} from '@vue/composition-api';

export default defineComponent({
  mixins: [],
  name: 'UnavailableFeature',
  props: {
    name: {
      type: String,
      required: false,
      default: () => '',
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
