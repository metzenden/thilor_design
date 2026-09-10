import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

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
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
                serif: ['"Playfair Display"', ...defaultTheme.fontFamily.serif],
            },
            colors: {
                cream: '#FAF6EF',
                gold: {
                    DEFAULT: '#C89B3C',
                    50: '#FBF4E4',
                    100: '#F6E8C9',
                    400: '#D4AE5C',
                    500: '#C89B3C',
                    600: '#A87F2C',
                    700: '#8A6822',
                },
                bordeaux: {
                    DEFAULT: '#7A2129',
                    600: '#7A2129',
                    700: '#5E1820',
                },
                ink: '#241C15',
            },
        },
    },

    plugins: [forms, typography],
};
