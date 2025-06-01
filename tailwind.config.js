import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: "#f97316", // Orange
                primaryDark: "#c2410c", // Darker orange
                secondary: "#ffedd5", // Light peach
                secondaryDark: "#fdba74", // Soft apricot
                background: "#fef9c3", // Warm cream
                accent: "#facc15", // Bright yellow for highlights
            },
        },
    },

    plugins: [forms, typography],
};
