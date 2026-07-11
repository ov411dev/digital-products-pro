import js from '@eslint/js';
import globals from 'globals';
export default [
  { ignores: ['node_modules/**','vendor/**','release/**','theme/digital-products-pro/dist/**'] },
  { files: ['theme/digital-products-pro/assets/js/**/*.js'], languageOptions: { ecmaVersion: 'latest', sourceType: 'module', globals: { ...globals.browser, DPPTheme: 'readonly' } }, rules: { ...js.configs.recommended.rules, 'no-console': 'warn' } }
];
