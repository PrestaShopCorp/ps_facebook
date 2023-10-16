<template>
  <b-card
    no-body
    class="category-matched"
  >
    <b-card-header class="d-flex">
      {{ $t('catalog.summaryPage.categoryMatching.title') }}

      <b-button
        :variant="matchingProgress && matchingProgress.matchingDone ? 'outline-primary': 'primary'"
        class="ml-auto"
        :disabled="!active"
        @click="goToCategoryMatchingPage"
      >
        {{ matchingProgress && matchingProgress.matchingDone
          ? $t('cta.manageCategoryMatching')
          : $t('cta.setupCategoryMatching') }}
      </b-button>
    </b-card-header>

    <b-card-body class="d-flex justify-content-start">
      <div class="text-category-mapping">
        {{ $t('catalog.summaryPage.categoryMatching.description') }}
        <div
          v-if="matchingProgress && matchingProgress.matchingDone"
          class="text-progress-bar-status"
        >
          {{ $t('catalog.summaryPage.categoryMatching.progress', {
            current: matchingProgress.matchingProgress.matched,
            total: matchingProgress.matchingProgress.total,
          }) }}
        </div>
      </div>
    </b-card-body>
  </b-card>
</template>

<script lang="ts">
import {PropType, defineComponent} from 'vue';
import {CategoryMatchingStatus} from '@/store/modules/catalog/state';
import CatalogTabPages from '@/components/catalog/pages';

export default defineComponent({
  name: 'CategoryMatching',
  props: {
    matchingProgress: {
      type: Object as PropType<CategoryMatchingStatus|undefined>,
      required: true,
    },
    active: {
      type: Boolean,
      required: true,
    },
  },
  methods: {
    goToCategoryMatchingPage() {
      this.$router.push({
        name: CatalogTabPages.categoryMatchingView,
      });
    },
  },
});
</script>
