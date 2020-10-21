<template>
  <ul class="p-3">
    <slot />
  </ul>
</template>

<script>
export default {
  name: 'FeatureList',
};
</script>

<style lang="scss" scoped>
  ul {
    li {
      list-style: none;
      padding-bottom: 15px;
    }
  }
</style>
