module.exports = {
  root: true,
  env: {
    node: true,
  },
  extends: [
    'prestashop',
    'plugin:vue/strongly-recommended',
    '@vue/typescript',
  ],
  parserOptions: {
    ecmaVersion: 2020,
    parser: '@typescript-eslint/parser',
  },
  rules: {
    'no-console': process.env.NODE_ENV === 'production' ? 'warn' : 'off',
    'no-debugger': process.env.NODE_ENV === 'production' ? 'warn' : 'off',
    'no-nested-ternary': 'off',
  },
};
