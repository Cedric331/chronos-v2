const defaultTheme = require('tailwindcss/defaultTheme');

/** @type {import('tailwindcss').Config} */
module.exports = {
    mode: 'jit',
    darkMode: ['class'],
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
        './app/Filament/**/*.php',
        './resources/views/filament/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
    ],

    theme: {
            extend: {
            screens: {
                '2xl': '1645px',
            },
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            zIndex: {
                '55': '55',
                '60': '60',
                '70': '70',
                '75': '75',
                '80': '80',
                '90': '90',
            },
        },
    },

    plugins: [require('@tailwindcss/forms')],
};
