<template>
  <li>
    <b-card no-body>
      <b-card-body
        class="p-3"
      >
        <div class="d-flex">
          <img
            class="mr-2 align-self-center logo d-none d-md-block"
            :src="imageUrl"
            width="80"
          >
          <div class="description align-self-top flex-grow-1 pl-3 pr-2">
            <h3>
              {{ $t(`integrate.features.${name}.name`) }}
            </h3>
            <p>
              {{ $t(`integrate.features.${name}.description`) }}
            </p>
          </div>
          <b-button
            variant="outline-primary disabled"
            class="ml-4 align-self-center"
          >
            {{ $t(`integrate.features.${name}.addButton`) }}
          </b-button>
        </div>
      </b-card-body>
    </b-card>
  </li>
</template>

<script lang="ts">
import {defineComponent} from 'vue';
import {BCard, BCardBody} from 'bootstrap-vue';

export default defineComponent({
  name: 'UnavailableFeature',
  components: {
    BCard,
    BCardBody,
  },
  computed: {
    imageUrl(): string {
      return new URL(`/src/assets/${this.name}_disabled.png`, import.meta.url).href;
    },
  },
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
  height: 80px;
  width: 80px;
}

.description {
  display: table-cell;
}

.flex-grow-1 {
  flex-grow:1
}
</style>
