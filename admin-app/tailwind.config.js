export default {
  content: ['./index.html', './src/**/*.{js,ts,jsx,tsx}'],
  theme: {
    extend: {
      colors: {
        primary: '#1e3a8a',
        secondary: '#7c3aed',
        success: '#059669',
        danger: '#dc2626',
        warning: '#f59e0b',
        info: '#0891b2',
      },
      spacing: {
        '128': '32rem',
      },
    },
  },
  plugins: [],
  important: true,
};
