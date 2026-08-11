/**
 * Tailwind configuration for TackPad.
 *
 * The theme is seeded strictly from docs/STYLEGUIDE.md so the rebuilt UI
 * reproduces the current palette exactly. Colours and typography are a client
 * requirement — do not add new brand colours here; add a token to the styleguide
 * first, then mirror it below.
 */
/** @type {import('tailwindcss').Config} */
module.exports = {
  // Every surface that can carry a Tailwind class. Purge scans these so the
  // committed output only ships classes actually used.
  content: [
    './app/Views/**/*.php',
    './public/js/**/*.js',
  ],
  theme: {
    extend: {
      colors: {
        tp: {
          black: '#000000',
          white: '#FFFFFF',
          surface: '#FEFEFE',
          navy: '#06145B',
          edit: '#4CAF50',
          danger: '#FF0000',
          'danger-ink': '#B00020',
          sidenav: '#333333',
          'sidenav-hover': '#555555',
          tab: '#1C1C1C',
          'tab-border': '#444444',
          muted: '#CCCCCC',
          hint: '#CFD6FF',
          'auth-border': '#005475',
        },
        task: {
          'on-time': '#90EE90',
          overdue: '#F08080',
          completed: '#D3D3D3',
        },
        // Signature gradient stops.
        grad: {
          cyan: '#0FA2D6',
          blue: '#090979',
          orange: '#F18522',
        },
      },
      fontFamily: {
        mono: ['Inconsolata', 'ui-monospace', 'SFMono-Regular', 'Menlo', 'monospace'],
      },
      // Off-canvas drawer width — single source shared by CSS (.sidenav open
      // state) so no JS ever hard-codes the pixel value.
      spacing: {
        sidenav: '250px',
      },
      borderRadius: {
        btn: '10px',
        modal: '25px',
        input: '4px',
      },
      boxShadow: {
        soft: 'rgba(0,0,0,0.35) 0 5px 15px',
        'white-glow': '0 2px 18px 0 rgba(255,255,255,0.5)',
        modal: '0 4px 8px rgba(0,0,0,0.2), 0 6px 20px rgba(0,0,0,0.19)',
        navy: '0 2px 18px 0 rgba(0,0,0,0.5)',
        card: '0 10px 30px rgba(0,0,0,0.35)',
      },
      backgroundImage: {
        // The one signature accent — auth title underline, strength meter fill.
        'tp-gradient': 'linear-gradient(90deg, #0FA2D6 0%, #090979 50%, #F18522 100%)',
      },
      keyframes: {
        animatetop: {
          from: { transform: 'translateY(-40px)', opacity: '0' },
          to: { transform: 'translateY(0)', opacity: '1' },
        },
        'toast-in': {
          from: { transform: 'translateX(120%)', opacity: '0' },
          to: { transform: 'translateX(0)', opacity: '1' },
        },
        'bg-drift': {
          '0%': { backgroundPosition: '0% 50%' },
          '100%': { backgroundPosition: '100% 50%' },
        },
      },
      animation: {
        animatetop: 'animatetop 0.4s ease',
        'toast-in': 'toast-in 0.35s cubic-bezier(0.22, 1, 0.36, 1)',
        'bg-drift': 'bg-drift 12s ease-in-out infinite alternate',
      },
    },
  },
  plugins: [],
}
