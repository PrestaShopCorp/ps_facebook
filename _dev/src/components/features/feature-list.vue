<template>
  <ul>
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
    padding: 15px;
    li {
      list-style: none;
      padding-bottom: 15px;
    }
  }
</style>
