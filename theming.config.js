import { definePreset } from '@primevue/themes';
import Aura from "@primevue/themes/aura";

const customTheme = definePreset(Aura, {
    font: {
        family: 'Open Sans'
    },
    semantic: {
        primary: {
            50: '{orange.50}',
            100: '{orange.100}',
            200: '{orange.200}',
            300: '{orange.300}',
            400: '{orange.400}',
            500: '{orange.500}',
            600: '{orange.600}',
            700: '{orange.700}',
            800: '{orange.800}',
            900: '{orange.900}',
            950: '{orange.950}'
        }
    },
    colorScheme: {
        dark: {
            surface: '#374151', // Tailwind gray-700
            background: '#374151', // Tailwind gray-700
        }
    }
});

export default customTheme;
