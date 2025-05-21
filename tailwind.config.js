// tailwind.config.js
import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class', // Habilitar modo oscuro basado en clase
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};

module.exports = {
    theme: {
        extend: {

            fontFamily: {
                sans: ['Open Sans', 'sans-serif'],
            },
            fontSize: {
                'title': ['2rem', { lineHeight: '1.2' }],       // 32px
                'subtitle': ['1.5rem', { lineHeight: '1.3' }],  // 24px
                'body': ['1rem', { lineHeight: '1.5' }],        // 16px
                'small': ['0.875rem', { lineHeight: '1.4' }],   // 14px
            },
            colors: {
                'primary': 'var(--primary)',
                'primary-light': 'var(--primary-light)',
                'primary-dark': 'var(--primary-dark)',
                'secondary': 'var(--secondary)',
                'secondary-light': 'var(--secondary-light)',
                'secondary-dark': 'var(--secondary-dark)',
            },
            backgroundColor: {
                'light': 'var(--bg-light)',
            },
            textColor: {
                'primary': 'var(--primary)',
                'secondary': 'var(--secondary)',
                'default': 'var(--text-dark)',
            }
        },
    },
}
