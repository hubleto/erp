/** @type {import('tailwindcss').Config} */
module.exports = {
  darkMode: 'selector',
  content: [
    "./src/**/*.{html,js,twig,tsx}",
    "./apps/**/*.{php,html,js,twig,tsx}",
    "./vendor/wai-blue/adios/**/*.{tsx,twig}",
    "./vendor/wai-blue/adios/node_modules/primereact/**/*.{js,ts,jsx,tsx}",
    "../premium/**/*.{php,html,js,twig,tsx}",
  ],
  safelist: [
    'adios-lookup__indicator',
    'adios-lookup__control',
    'adios-lookup__input-container',
    'adios-lookup__value-container',
    'adios-lookup__input',
  ],
  plugins: [],
}

