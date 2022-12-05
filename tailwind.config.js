/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './native/views/**/*.php',
    './app/views/**/*.php',
    './app/modules/**/*.php',
  ],
  theme: {
    fontFamily: {
      sans: ['Poppins', 'Inter'],
    },
    extend: {
      boxShadow: {
        lg: '0 4px 5px rgba(0, 0, 0, 0.05)',
      },
      colors: {
        'teal-50': '#ebf8f9',
        'teal-100': '#c4eaed',
        'teal-200': '#9ddde2',
        'teal-300': '#76cfd6',
        'teal-400': '#4ec1ca',
        'teal-500': '#35a8b1',
        'teal-600': '#298289',
        'teal-700': '#19494D',
        'teal-800': '#1d5d62',
        'teal-900': '#12383b',
      },
    },
  },
  plugins: ['form'],
};
