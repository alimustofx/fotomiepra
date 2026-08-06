import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            colors: {
                // Token Palet "Kertas & Tinta" (Design.md 2.1)
                'paper': '#EFE7D6',
                'paper-card': '#F8F3E6',
                'ink-navy': '#1F3A52',
                'ink-faded': '#5B6B63',
                'stamp-red': '#AE3B2E',
                'ledger-line': '#C9B896',
            },
            fontFamily: {
                // Tipografi IBM Plex (Design.md 2.2)
                sans: ['"IBM Plex Sans"', ...defaultTheme.fontFamily.sans],
                mono: ['"IBM Plex Mono"', ...defaultTheme.fontFamily.mono],
            },
        },
    },

    plugins: [forms],
};
