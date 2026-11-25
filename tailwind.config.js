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
                // Police de texte officielle de la charte THILOR DESIGN.
                sans: ['"Merriweather Sans"', ...defaultTheme.fontFamily.sans],
                // Clé "serif" conservée (reprise par toutes les vues) mais chargée
                // avec Montserrat, la police de titres officielle de la charte —
                // Montserrat n'est pas une police serif, seul le nom de la clé l'est.
                serif: ['Montserrat', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // Palette officielle — charte de marque THILOR DESIGN.
                cream: '#F4F0E8', // Crème
                sand: '#D8C7AD', // Beige sable
                // Couleur d'accent principale (boutons, liens actifs) : Chocolat.
                gold: {
                    DEFAULT: '#3A2A22',
                    50: '#F1E9E3',
                    100: '#E3D4C8',
                    400: '#6B5245',
                    500: '#3A2A22',
                    600: '#2C1F19',
                    700: '#20150F',
                },
                // Accent secondaire (promotions, actions de suppression) : une
                // teinte terracotta sourde choisie pour rester dans l'esprit de la
                // palette officielle — la charte ne définit pas de couleur d'alerte.
                bordeaux: {
                    DEFAULT: '#8C3B2E',
                    600: '#8C3B2E',
                    700: '#6B2C22',
                },
                ink: '#101C2C', // Bleu nuit
            },
        },
    },

    plugins: [forms, typography],
};
