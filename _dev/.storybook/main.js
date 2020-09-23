module.exports = {
  stories: ['../stories/**/*.stories.[tj]s'],
  addons : [
    '@storybook/addon-knobs/register',
    '@storybook/addon-actions/register',
    '@storybook/addon-viewport',
    '@storybook/addon-links',
    '@storybook/addon-docs'
  ]
};
