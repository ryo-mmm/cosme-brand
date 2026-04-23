import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
        './resources/js/**/*.js',
    ],

    theme: {
        extend: {
            colors: {
                sand: '#F5F5F0',
                slate: {
                    brand: '#4A5859',
                    light: '#6B7B7C',
                    dark: '#2E3A3B',
                },
                accent: {
                    DEFAULT: '#C4A882',
                    light: '#D4BC9A',
                },
            },
            fontFamily: {
                sans: ['Noto Sans JP', ...defaultTheme.fontFamily.sans],
                serif: ['Noto Serif JP', ...defaultTheme.fontFamily.serif],
            },
        },
    },

    plugins: [forms],
};
