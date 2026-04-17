/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./public/*.php",
    "./src/**/*.php",
    "./src/**/*.js",
  ],
  theme: {
    extend: {
      colors: {
        background: '#0c1324',
        primary: {
          DEFAULT: '#4cd7f6',
          container: '#06b6d4',
          fixed: '#acedff',
          'fixed-dim': '#4cd7f6',
        },
        surface: {
          DEFAULT: '#0c1324',
          bright: '#33394c',
          container: {
            DEFAULT: '#191f31',
            lowest: '#070d1f',
            low: '#151b2d',
            high: '#23293c',
            highest: '#2e3447',
          },
          variant: '#2e3447',
        },
        accent: {
          DEFAULT: '#06b6d4',
          600: '#0891b2',
          700: '#0e7490',
        }
      },
      fontFamily: {
        sans: ['Inter', 'sans-serif'],
        heading: ['Plus Jakarta Sans', 'Inter', 'sans-serif'],
        mono: ['JetBrains Mono', 'monospace'],
      },
      spacing: {
        '20': '5rem',
        '24': '6rem',
      },
      borderRadius: {
        'xl': '0.75rem',
        '2xl': '1rem',
        '3xl': '1.5rem',
        '4xl': '2rem',
        '5xl': '2.5rem',
      },
    },
  },
  plugins: [],
}
