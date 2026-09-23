import pluginVue from 'eslint-plugin-vue';
import globals from 'globals';
import prettierConfig from 'eslint-config-prettier/flat';

export default [
    {
        ignores: ['node_modules/**', 'vendor/**', 'public/build/**', 'storage/**', 'bootstrap/cache/**'],
    },
    ...pluginVue.configs['flat/recommended'],
    {
        files: ['**/*.{js,mjs,vue}'],
        languageOptions: {
            ecmaVersion: 'latest',
            sourceType: 'module',
            globals: {
                ...globals.browser,
                ...globals.node,
            },
        },
        rules: {
            'vue/multi-word-component-names': 'off',
        },
    },
    prettierConfig,
];
