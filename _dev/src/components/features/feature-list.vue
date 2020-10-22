<template>
  <ul class="p-3">
    <slot />
  </ul>
</template>

<script>
import {defineComponent} from '@vue/composition-api';

export default defineComponent({
  name: 'FeatureList',
});
</script>

<style lang="scss" scoped>
  ul {
    li {
      list-style: none;
      padding-bottom: 15px;
    }
  }
</style>
