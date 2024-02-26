<template>
  <b-card
    no-body
    class="category-matched catalogSummary__card"
  >
    <b-card-header class="d-flex align-items-start">
      <div>
        {{ $t('catalog.summaryPage.categoryMatching.title') }}
      </div>
      <b-button
        :variant="hasCategoryMappingStarted ? 'outline-primary': 'primary'"
        class="ml-auto text-nowrap"
        :disabled="!active"
        @click="goToCategoryMatchingPage"
      >
        {{ hasCategoryMappingStarted
          ? $t('cta.manageCategoryMatching')
          : $t('cta.setupCategoryMatching') }}
      </b-button>
    </b-card-header>

    <b-card-body class="d-flex justify-content-start">
      <div class="text-category-mapping ps_gs-fz-16">
        {{ $t('catalog.summaryPage.categoryMatching.description') }}
        <div
          v-if="hasCategoryMappingStarted"
          class="text-progress-bar-status mt-2"
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
      type: Object as PropType<CategoryMatchingStatus>,
      required: true,
    },
    active: {
      type: Boolean,
      required: true,
    },
  },
  computed: {
    hasCategoryMappingStarted(): boolean {
      return !!this.matchingProgress.matchingProgress.matched;
    },
  },
  methods: {
    goToCategoryMatchingPage() {
      if (this.hasCategoryMappingStarted) {
        this.$router.push({
          name: CatalogTabPages.categoryMatchingView,
        });
        return;
      }
      this.$router.push({
        name: CatalogTabPages.categoryMatchingEdit,
      });
    },
  },
});
</script>
