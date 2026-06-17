import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './app/Livewire/**/*.php',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
                display: ['Poppins', 'Inter', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                kraft: {
                    DEFAULT: '#B5793A',
                    50: '#FBF6F0',
                    100: '#F3E6D5',
                    600: '#B5793A',
                    700: '#945F2C',
                },
                coral: {
                    DEFAULT: '#FF5A3C',
                    600: '#FF5A3C',
                    700: '#E8431F',
                },
                ink: '#1E2530',
                paper: '#F7F3EC',
            },
        },
    },
    plugins: [],
};
