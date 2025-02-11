/** @type {import('tailwindcss').Config} */
module.exports = {
  darkMode: 'selector',
  content: [
    "./src/**/*.{html,js,twig,tsx}",
    "./apps/**/*.{html,js,twig,tsx}",
    "./vendor/wai-blue/adios/**/*.{tsx,twig}",
    "./vendor/wai-blue/adios/node_modules/primereact/**/*.{js,ts,jsx,tsx}",
  ],
  safelist: [
    'adios-lookup__indicator',
    'adios-lookup__control',
    'adios-lookup__input-container',
    'adios-lookup__value-container',
    'adios-lookup__input',
  ],
  // theme: {
  //   fontFamily: {
  //     sans: ['Fredoka-Regular', 'sans-serif'],
  //     serif: ['Merriweather', 'serif'],
  //   },
  //   extend: {
  //     colors: {
  //       'primary': '#008000',
  //       'secondary': '#7FB562', // povodna oranzova
  //     },
  //     spacing: {
  //       '8xl': '96rem',
  //       '9xl': '128rem',
  //     },
  //     borderRadius: {
  //       '4xl': '2rem',
  //     }
  //   }
  // },
  plugins: [],
}

