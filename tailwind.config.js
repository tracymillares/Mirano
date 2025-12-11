/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./assets/**/*.js",
    "./templates/**/*.html.twig",
  ],
  theme: {
    extend: {
      colors: {
        cream: '#FFFFD1',
        coffee: '#5D2913',
      },
    },
  },
  plugins: [],
}
