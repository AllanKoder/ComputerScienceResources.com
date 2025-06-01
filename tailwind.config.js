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
                primary: "#fa6600", // Orange
                primaryDark: "#c2410c", // Darker orange
                secondary: "#fff3e3", // Light peach
                secondaryDark: "#fcdcbb", // Soft apricot
                background: "#fef9c3", // Warm cream
                accent: "#facc15", // Bright yellow for highlights
            },
        },
    },

    plugins: [forms, typography],
};
