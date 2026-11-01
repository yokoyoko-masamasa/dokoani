import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './app/Http/Controllers/**/*.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['"Noto Sans JP"', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                accent: '#C970E4',
                primary: {
                    DEFAULT: '#5293FF',
                    dark: '#336DCC',
                },
                secondary: '#BEC5FF',
            },
        },
    },

    plugins: [forms],
};
