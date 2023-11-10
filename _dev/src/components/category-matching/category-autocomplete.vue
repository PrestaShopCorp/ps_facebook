<!--**
 * 2007-2021 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2021 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *-->
<template>
  <b-dropdown
    lazy
    block
    :id="`dropdown-category-${shopCategoryId}`"
    ref="dropdown"
    variant="outline-secondary"
    class="m-2 category-matching-dropdown"
    boundary="scrollParent"
    menu-class="w-100"
    no-flip
    :disabled="disabled"
    @show="beforeDropdownShown"
    @shown="afterDropdownShown"
    @hidden="afterDropdownHidden"
  >
    <template #button-content>
      <template v-if="currentCategoryName">
        {{ currentCategoryName }}
      </template>
      <span
        v-else
        class="text-muted select-title"
      >
        {{ $t('categoryMatching.autocomplete.select') }}
      </span>
    </template>
    <b-dropdown-item tabindex="-1">
      <template v-if="currentCategoryName">
        {{ currentCategoryName }}
      </template>
      <span
        v-else
        class="text-muted select-title title-on-select"
      >
        {{ $t('categoryMatching.autocomplete.select') }}
      </span>
    </b-dropdown-item>
    <li>
      <b-form-input
        autocomplete="off"
        size="sm"
        :id="`dropdown-category-input-${shopCategoryId}`"
        :placeholder="$t('categoryMatching.autocomplete.typeToFilter')"
        @input="filterChange"
      />
      <div
        v-if="loading"
        class="spinner"
      />

      <i v-if="tooManyProposals">{{ $t('categoryMatching.autocomplete.tooManyResults') }}</i>
      <i v-if="fetchError" />
    </li>
    <b-dropdown-item
      variant="inverse"
      v-if="currentCategoryId"
      @click="() => categoryRemoved()"
    >
      <i>{{ $t('categoryMatching.autocomplete.unassign') }}</i>
    </b-dropdown-item>
    <b-dropdown-item
      v-for="category in categories || [{id: -1, name: '...'}]"
      :key="category.id"
      :disabled="category.id === -1"
      variant="inverse"
      @click="() => categoryChosen(category.id, category.name)"
    >
      {{ category.name }}
    </b-dropdown-item>
  </b-dropdown>
</template>

<script lang="ts">
import {defineComponent} from 'vue';
import {debounce} from 'debounce';
import {
  BDropdown,
  BDropdownItem,
  BDropdownForm,
  BFormInput,
  BFormCheckbox,
  BSpinner,
} from 'bootstrap-vue';

export default defineComponent({
  name: 'CategoryAutocomplete',
  components: {
    BDropdown,
    BDropdownItem,
    BDropdownForm,
    BFormInput,
    BFormCheckbox,
    BSpinner,
  },
  mixins: [],
  props: {
    language: {
      type: String,
      required: false,
      default: 'en-US',
    },
    shopCategoryId: {
      type: String,
      required: true,
    },
    initialCategoryName: {
      type: String,
      required: false,
      default: null,
    },
    initialCategoryId: {
      type: Number,
      required: false,
      default: null,
    },
    parentCategoryId: {
      type: Number,
      required: false,
      default: null,
    },
    autocompletionApi: {
      type: String,
      required: true,
    },
    disabled: {
      type: Boolean,
      required: false,
      default: false,
    },
  },
  data() {
    return {
      currentCategoryName: this.initialCategoryName,
      currentCategoryId: this.initialCategoryId,
      currentFilter: null as String | null, // init null: forces to load full list first time opened
      categories: null, // init value null, to display loader before full list is loaded
      loading: false,
      tooManyProposals: false,
      fetchError: null,
      debouncedFetchCategories: debounce(this.fetchCategories.bind(this), 1000, false),
    };
  },
  methods: {
    beforeDropdownShown() {
      this.currentFilter = '';
    },
    afterDropdownShown() {
      const input = document.getElementById(`dropdown-category-input-${this.shopCategoryId}`);

      if (input) {
        input.focus();
      }
    },
    categoryChosen(categoryId, categoryName) {
      this.currentCategoryName = categoryName;
      this.currentCategoryId = categoryId;
    },
    categoryRemoved() {
      this.categoryChosen(0, '');
    },
    afterDropdownHidden() {
      this.$emit('onCategorySelected', this.currentCategoryId, this.currentCategoryName);
    },
    filterChange(value) {
      this.currentFilter = (value.length >= 3 || value.length === 0) ? value : this.currentFilter;
    },
    fetchCategories() {
      this.loading = true;
      this.fetchError = null;
      const url = new URL(
        `/taxonomy/${(this.parentCategoryId && (`${this.parentCategoryId}/subcategories`)) || ''}`,
        this.autocompletionApi,
      );
      url.search = `s=${encodeURIComponent(this.currentFilter)}&l=${this.language}`;

      fetch(url.toString())
        .then((result) => {
          if (!result.ok) {
            throw new Error(result.statusText || result.status);
          }
          return result.json();
        })
        .then((result) => {
          if (result.length === 0) {
            this.categories = [{id: -1, name: this.$t('categoryMatching.autocomplete.noResult')}];
            this.tooManyProposals = false;
          } else {
            this.categories = result.map(({id, name}) => ({id, name})); // keep only id and name
            this.tooManyProposals = (this.categories.length >= 50);
          }
          this.loading = false;
        })
        .catch((error) => {
          this.loading = false;
          console.error(error);
          this.fetchError = error;
        });
    },
  },
  watch: {
    currentFilter(newValue, oldValue) {
      if (newValue !== oldValue) {
        this.loading = true;
        this.debouncedFetchCategories();
      }
    },
    initialCategoryId(newValue, oldValue) {
      if (newValue !== oldValue) {
        this.currentCategoryId = newValue;
      }
    },
    initialCategoryName(newValue, oldValue) {
      if (newValue !== oldValue) {
        this.currentCategoryName = newValue;
      }
    },
  },
});
</script>
