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
    WIDEN_MEDIA_OBJ: 'readonly',
    jQuery_3_4_0: 'readonly',
  },
}
