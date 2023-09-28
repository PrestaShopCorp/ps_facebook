<template>
  <div class="row mb-4">
    <div class="col-12 col-md-5">
      <div
        class="is-sticky pb-3"
      >
        <h2>{{ title }}</h2>
      </div>
    </div>
      <div class="col-12 col-md-7">
      <slot></slot>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent } from 'vue';

export default defineComponent({
  name: 'TwoPanelCols',
  props: {
    title: {
      required: true,
      type: String,
    },
  },
});
</script>