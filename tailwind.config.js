/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './resources/views/layouts/landing.blade.php',
    './resources/views/landing/**/*.blade.php',
  ],
  theme: {
    container: {
      center: true,
      padding: '1rem',
    },
    extend: {
      keyframes: {
        leaves: {
          '0%': { transform: 'scale(1)' },
          '100%': { transform: 'scale(1.1)' },
        },
        jumping: {
          '0%': { transform: 'translateY(0)' },
          '100%': { transform: 'translateY(-20px)' },
        }
      },
      animation: {
        leaves: 'leaves 2s ease-in-out infinite alternate',
        jumping: 'jumping 2s ease-in-out infinite alternate',
      }
    },
  },
  plugins: [],
};
