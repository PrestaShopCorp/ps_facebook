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
    :disabled="disabled"
    @show="beforeDropdownShown"
    @shown="afterDropdownShown"
    @hidden="afterDropdownHidden"
  >
    <template v-slot:button-content>
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
        class="text-muted select-title"
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
      <i v-if="fetchError">{{ $t('categoryMatching.autocomplete.fetchError') }}</i>
    </li>
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
import {defineComponent} from '@vue/composition-api';
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
      debouncedFetchCategories: debounce(this.fetchCategories.bind(this), 2000, false),
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
    afterDropdownHidden() {
      if (this.currentCategoryId > 0) {
        this.$emit('onCategorySelected', this.currentCategoryId, this.currentCategoryName);
      }
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
<style lang="scss" scoped>
  .spinner {
    color: #fff;
    background-color: #fff;
    width: 1.4rem;
    height: 1.4rem;
    border-radius: 2.5rem;
    border-right-color: #25b9d7;
    border-bottom-color: #25b9d7;
    border-width: .1875rem;
    border-style: solid;
    font-size: 0;
    outline: none;
    display: inline-block;
    border-left-color: #bbcdd2;
    border-top-color: #bbcdd2;
    -webkit-animation: rotating 2s linear infinite;
    animation: rotating 2s linear infinite;
    position: absolute;
    top: 0.7rem;
    right: 0.7rem;
  }

  .select-title {
    font-weight: normal;
    font-style: italic;
  }
</style>
<style lang="scss">
  .category-matching-dropdown {
    & > button {
      text-overflow: ellipsis;
      overflow: hidden;
      direction: rtl;
      text-align: end;

      &::after {
        content: "";
      }
      &::before {
        text-align: start;
        margin-left: .625rem;
        margin-right: 0;
        font-family: Material Icons;
        font-weight: 400;
        font-style: normal;
        font-size: 1.5rem;
        text-transform: none;
        letter-spacing: normal;
        word-wrap: normal;
        white-space: nowrap;
        direction: ltr;
        -webkit-font-smoothing: antialiased;
        text-rendering: optimizeLegibility;
        -moz-osx-font-smoothing: grayscale;
        font-feature-settings: "liga";
        content: "expand_more";
        border: none;
        display: inline-block;
        vertical-align: middle;
        width: auto;
        line-height: 1;
        float: right;
        box-sizing: border-box;
      }
    }

    & > ul {
      top: -2.4rem !important;
      padding: 0.4rem;
      border: 2px solid #3ed2f0;
      box-shadow: 0 0.2rem 0.5rem rgba(0,0,0,.175);
      max-height: 25rem;
      overflow-y: auto;

      & > li:first-of-type {
        margin-top: calc(-0.4rem + 2px);
        margin-bottom: 0.6rem;

        & > a {
          font-weight: 600;
          display: inherit;
          width: inherit;
          white-space: inherit;
          clear: none;
          text-align: center;

          &:active {
            background: inherit;
            color: inherit;
          }
        }

        &:before {
          font-family: Material Icons;
          font-weight: 400;
          font-style: normal;
          font-size: 24px;
          font-size: 1.5rem;
          text-transform: none;
          letter-spacing: normal;
          word-wrap: normal;
          white-space: nowrap;
          direction: ltr;
          -webkit-font-smoothing: antialiased;
          text-rendering: optimizeLegibility;
          -moz-osx-font-smoothing: grayscale;
          font-feature-settings: "liga";
          content: "expand_less";
          border: none;
          display: inline-block;
          vertical-align: middle;
          width: auto;
          float: right;
        }
      }

      & > li:not(:first-of-type) {
        margin-top: 0.4rem;

        & > a {
          white-space: normal;

          &.disabled {
            font-style: italic;
            font-size: 0.85em;
            background-color: white;
          }

          &:not(.disabled):hover {
            background-color: #25b9d7;
            color: white;
          }
        }

        &:nth-of-type(even) {
          background-color: #eff1f2;
        }
      }

      & > li:nth-of-type(2) {
        margin: -0.4rem;
        padding: 0.4rem;
        position: sticky;
        top: -0.5rem;
        background-color: white !important;

        & > i {
          font-size: 0.85em;
          background-color: white;
        }
      }
    }
  }
</style>
