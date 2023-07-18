import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import colors from 'tailwindcss/colors';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
        'node_modules/flowbite-vue/**/*.{js,jsx,ts,tsx}',
        "./node_modules/flowbite/**/*.js"
    ],

    theme: {
        screens: {
            'sm': '850px',
            // => @media (min-width: 640px) { ... }
            'md': '1024px',
            // => @media (min-width: 768px) { ... }
            'lg': '1024px',
            // => @media (min-width: 1024px) { ... }
            'xl': '1280px',
            // => @media (min-width: 1280px) { ... }
            '2xl': '1536px',
            // => @media (min-width: 1536px) { ... }
        },
        extend: {
            fontFamily: {
                sans: ['Satoshi', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                transparent: 'transparent',
                current: 'currentColor',
                black: colors.black,
                white: colors.white,
                gray: colors.slate,
                green: colors.emerald,
                purple: colors.violet,
                yellow: colors.amber,
                pink: colors.fuchsia,
                primary: '#008083',
                "primary-dark": '#004b4b',
                "primary-dark-dark": '#001816',
                background: '#d6e9ed'
            },
            animation: {
                marquee: 'marquee 25s linear infinite',
                marquee2: 'marquee2 25s linear infinite',
            },
            keyframes: {
                marquee: {
                    '0%': { transform: 'translateX(0%)' },
                    '100%': { transform: 'translateX(-100%)' },
                },
                marquee2: {
                    '0%': { transform: 'translateX(100%)' },
                    '100%': { transform: 'translateX(0%)' },
                },
            },
        },


    },

    plugins: [
        forms,
        require('flowbite/plugin'),
        require("daisyui")
    ],
};
