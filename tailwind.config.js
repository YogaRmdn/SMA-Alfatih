import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './app/**/*.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Amiri', 'Figtree', ...defaultTheme.fontFamily.sans],
            },
            fontSize: {
                xs: ['0.8125rem', { lineHeight: '1.1rem' }],
                sm: ['0.9375rem', { lineHeight: '1.35rem' }],
                base: ['1.0625rem', { lineHeight: '1.65rem' }],
                lg: ['1.1875rem', { lineHeight: '1.8rem' }],
                xl: ['1.3125rem', { lineHeight: '1.9rem' }],
                '2xl': ['1.625rem', { lineHeight: '2.15rem' }],
                '3xl': ['1.9375rem', { lineHeight: '2.4rem' }],
                '4xl': ['2.375rem', { lineHeight: '2.75rem' }],
                '5xl': ['3.125rem', { lineHeight: '1.1' }],
                '6xl': ['3.875rem', { lineHeight: '1.1' }],
                '7xl': ['4.75rem', { lineHeight: '1.05' }],
                '8xl': ['6.25rem', { lineHeight: '1' }],
                '9xl': ['8.25rem', { lineHeight: '1' }],
            },
        },
    },

    plugins: [forms, typography],
};
