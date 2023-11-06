<template>
  <b-card
    class="ps-banner"
    :class="`ps-banner-${variant}`"
  >
    <slot />
  </b-card>
</template>

<script lang="ts">
import {defineComponent} from 'vue';

export default defineComponent({
  name: 'PsBanner',
  props: {
    variant: {
      type: String,
      required: false,
      default: 'primary',
    },
  },
});
</script>
