module.exports = {
  extends: '@masonite/eslint-config-base',
  env: {
    browser: true,
    jquery: true,
  },
  rules: {
    'func-names': 0,
    'no-console': 0,
  },
  globals: {
    widen_media: 'readonly',
    jQuery: 'readonly',
  },
}
