  /** @type {import('tailwindcss').Config} */
  export default {
    content: [
      "./resources/**/*.blade.php",
      "./resources/**/*.js",
      "./resources/**/*.vue",
    ],
    theme: {
      extend: {
        colors: {
          'kraftdo': {
            'dark': '#1a1a2e',
            'navy': '#16213e',
            'blue': '#0f3460',
            'green': '#00ff88',
            'lime': '#7fff00',
          }
        },
        fontFamily: {
          'kraftdo': ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
        },
      },
    },
  }