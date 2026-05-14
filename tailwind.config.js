import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
                heading: ['Outfit', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: {
                    50: '#fffce6',
                    100: '#fef7cc',
                    200: '#fded99',
                    300: '#fce366',
                    400: '#fbda33',
                    500: '#FCD827',
                    600: '#e5c320',
                    700: '#cca019',
                    800: '#a38014',
                    900: '#7b6911',
                },
                secondary: {
                    50: '#ebf2ff',
                    100: '#d6e5ff',
                    500: '#1A5EDB',
                    600: '#154cb3',
                    900: '#0b285e',
                },
                'dark-bg': '#2B2B2B',
                'dark-card': '#333333',
            }
        },
    },

    plugins: [forms],
};
