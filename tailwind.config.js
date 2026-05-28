import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', 'Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                chat: {
                    primary: '#075E54',
                    secondary: '#128C7E',
                    light: '#25D366',
                    bg: '#ECE5DD',
                    sent: '#DCF8C6',
                    received: '#FFFFFF',
                },
            },
            transitionTimingFunction: {
                'out-custom': 'cubic-bezier(0.2, 0, 0, 1)',
                'in-out-custom': 'cubic-bezier(0.77, 0, 0.175, 1)',
            },
        },
    },
    plugins: [forms],
};
