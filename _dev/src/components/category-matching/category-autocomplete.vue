<!--**
 * 2007-2020 PrestaShop and Contributors
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
 * @copyright 2007-2020 PrestaShop SA and Contributors
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
    background-color: #fff!important;
    width: 1.4rem!important;
    height: 1.4rem!important;
    border-radius: 2.5rem!important;
    border-right-color: #25b9d7!important;
    border-bottom-color: #25b9d7;
    border-width: .1875rem!important;
    border-style: solid!important;
    font-size: 0!important;
    outline: none!important;
    display: inline-block!important;
    border-left-color: #bbcdd2!important;
    border-top-color: #bbcdd2!important;
    -webkit-animation: rotating 2s linear infinite!important;
    animation: rotating 2s linear infinite!important;
    position: absolute!important;
    top: 0.7rem!important;
    right: 0.7rem!important;
  }

  .select-title {
    font-weight: normal!important;
    font-style: italic!important;
  }
</style>
<style lang="scss">
  .category-matching-dropdown {
    & > button {
      text-overflow: ellipsis!important;
      overflow: hidden!important;
      direction: rtl!important;
      text-align: end!important;

      &::after {
        content: "" !important;
      }
      &::before {
        text-align: start!important;
        margin-left: .625rem!important;
        margin-right: 0!important;
        font-family: Material Icons!important;
        font-weight: 400!important;
        font-style: normal;
        font-size: 1.5rem!important;
        text-transform: none!important;
        letter-spacing: normal!important;
        word-wrap: normal!important;
        white-space: nowrap!important;
        direction: ltr!important;
        -webkit-font-smoothing: antialiased!important;
        text-rendering: optimizeLegibility!important;
        -moz-osx-font-smoothing: grayscale!important;
        font-feature-settings: "liga"!important;
        content: "expand_more"!important;
        border: none!important;
        display: inline-block!important;
        vertical-align: middle!important;
        width: auto!important;
        line-height: 1!important;
        float: right!important;
        box-sizing: border-box!important;
      }
    }

    & > ul {
      top: -2.4rem!important;
      padding: 0.4rem!important;
      border: 2px solid #3ed2f0!important;
      box-shadow: 0 0.2rem 0.5rem rgba(0,0,0,.175)!important;
      max-height: 25rem!important;
      overflow-y: auto!important;

      & > li:first-of-type {
        margin-top: calc(-0.4rem + 2px)!important;
        margin-bottom: 0.6rem!important;

        & > a {
          font-weight: 600!important;
          display: inherit!important;
          width: inherit!important;
          white-space: inherit!important;
          clear: none!important;
          text-align: center!important;

          &:active {
            background: inherit!important;
            color: inherit!important;
          }
        }

        &:before {
          font-family: Material Icons;
          font-weight: 400!important;
          font-style: normal;
          font-size: 24px!important;
          font-size: 1.5rem!important;
          text-transform: none!important;
          letter-spacing: normal!important;
          word-wrap: normal!important;
          white-space: nowrap!important;
          direction: ltr!important;
          -webkit-font-smoothing: antialiased!important;
          text-rendering: optimizeLegibility!important;
          -moz-osx-font-smoothing: grayscale!important;
          font-feature-settings: "liga"!important;
          content: "expand_less"!important;
          border: none!important;
          display: inline-block!important;
          vertical-align: middle!important;
          width: auto!important;
          float: right!important;
        }
      }

      & > li:not(:first-of-type) {
        margin-top: 0.4rem!important;

        & > a {
          white-space: normal!important;

          &.disabled {
            font-style: italic;
            font-size: 0.85em!important;
            background-color: white!important;
          }

          &:not(.disabled):hover {
            background-color: #25b9d7!important;
            color: white!important;
          }
        }

        &:nth-of-type(even) {
          background-color: #eff1f2!important;
        }
      }

      & > li:nth-of-type(2) {
        margin: -0.4rem!important;
        padding: 0.4rem!important;
        position: sticky!important;
        top: -0.5rem!important;
        background-color: white !important;

        & > i {
          font-size: 0.85em!important;
          background-color: white!important;
        }
      }
    }
  }
</style>
