const defaultTheme = require('tailwindcss/defaultTheme');

module.exports = {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans, 'Open Sans', 'ui-sans-serif', 'system-ui', 'sans-serif'],
            },
            colors: {
                primary: {
                    DEFAULT: 'var(--color-primary)',
                    light: 'var(--color-primary-light)',
                    dark: 'var(--color-primary-dark)',
                },
                secondary: {
                    DEFAULT: 'var(--color-secondary)',
                    light: 'var(--color-secondary-light)',
                    dark: 'var(--color-secondary-dark)',
                },
                tocototi: {
                    verde: '#00913F',
                    'verde-hover': '#007833',
                    'verde-dark': '#006b2d',
                    tierra: '#825028',
                    'tierra-hover': '#6b3f1f',
                    crema: '#FAF6F0',
                    'crema-dark': '#F5EFE6',
                    'gris-texto': '#333333',
                    'gris-claro': '#EEEEEE',
                },
            },
            fontSize: {
                h1: ['40px', { lineHeight: '1.2', fontWeight: '700' }],
                h2: ['32px', { lineHeight: '1.3', fontWeight: '600' }],
                h3: ['28px', { lineHeight: '1.4', fontWeight: '600' }],
                h4: ['24px', { lineHeight: '1.4', fontWeight: '500' }],
                body: ['16px', { lineHeight: '1.5' }],
                small: ['14px', { lineHeight: '1.5' }],
            },
        },
    },
    plugins: [
        require('@tailwindcss/forms'),
    ],
};
