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
        background: '#ffffff', // Clean white background for academic feel
        foreground: '#0f172a', // Slate-900
        primary: {
          DEFAULT: '#004a99', // LIET Blue
          foreground: '#ffffff',
          light: '#0056b3',
          dark: '#003366',
        },
        secondary: {
          DEFAULT: '#ed1c24', // LIET Red
          foreground: '#ffffff',
        },
        muted: {
          DEFAULT: '#f8fafc', // Slate-50
          foreground: '#64748b', // Slate-500
        },
        accent: {
          DEFAULT: '#f1f5f9', // Slate-100
          foreground: '#0f172a',
        },
        border: '#e2e8f0', // Slate-200
        input: '#f8fafc',
        ring: '#004a99',
      },
      fontFamily: {
        sans: ['Inter', 'system-ui', 'sans-serif'],
        heading: ['Plus Jakarta Sans', 'Inter', 'sans-serif'],
        mono: ['JetBrains Mono', 'monospace'],
      },
      borderRadius: {
        'lg': '0.5rem',
        'md': '0.375rem',
        'sm': '0.25rem',
        'xl': '0.75rem',
        '2xl': '1rem',
        '3xl': '1.5rem',
      },
      boxShadow: {
        'premium': '0 10px 15px -3px rgba(0, 74, 153, 0.05), 0 4px 6px -2px rgba(0, 74, 153, 0.02)',
        'subtle': '0 1px 3px 0 rgba(0, 0, 0, 0.05), 0 1px 2px 0 rgba(0, 0, 0, 0.03)',
        'glass': '0 8px 32px 0 rgba(31, 38, 135, 0.07)',
      },
    },
  },
  plugins: [],
}
