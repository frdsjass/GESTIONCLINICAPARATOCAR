import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';
import colors from 'tailwindcss/colors';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            
            colors: {
                // Paleta principal con colores de Tailwind
                'primary': colors.teal,
                
                // Colores personalizados específicos de la aplicacion
                'sidebar': '#0A2342',
                'content': '#ecfafaff',
                'clinica-gray': '#E0E0E0',
                'clinica-blue': '#b31818ff',
                'clinica-teal': '#1b4948ff',
                
                // Puedes agregar mas variantes si es necesario
                'clinica': {
                    'dark-blue': '#0A2342',
                    'blue': '#2E8BC0',
                    'teal': '#1b4948ff',
                    'light-gray': '#F5F5F5',
                    'gray': '#E0E0E0',
                }
            },
            
            // Opcional: Extender otros aspectos del tema
            spacing: {
                'sidebar': '16rem',
            },
            
            boxShadow: {
                'sidebar': '2px 0 10px rgba(0, 0, 0, 0.1)',
            }
        },
    },

    plugins: [forms, typography],
};