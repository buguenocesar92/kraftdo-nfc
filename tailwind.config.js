/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
    "./app/Filament/**/*.php",
    "./vendor/filament/**/*.blade.php"
  ],
  theme: {
    extend: {
      colors: {
        'kraftdo-dark': '#2A3441',
        'kraftdo-navy': '#3B4A6B',
        'kraftdo-blue': '#4A90E2',
        'kraftdo-green': '#00FF7F',
        'kraftdo-lime': '#32FF32',
      },
      fontFamily: {
        'kraftdo': ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
      },
    },
  },
  plugins: [],
}