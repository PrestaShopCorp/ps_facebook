<template>
  <ul class="p-3">
    <slot />
  </ul>
</template>

<script lang="ts">
import {defineComponent} from 'vue';

export default defineComponent({
  name: 'FeatureList',
});
</script>
