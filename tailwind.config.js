import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
        "./resources/js/**/*.{js,ts,jsx,tsx}",
    ],

    darkMode: "class",

    theme: {
        extend: {
            fontFamily: {
                sans: [
                    '"Be Vietnam Pro"',
                    "-apple-system",
                    "BlinkMacSystemFont",
                    '"SF Pro Display"',
                    '"SF Pro Text"',
                    '"Segoe UI"',
                    "Roboto",
                    "Helvetica",
                    "Arial",
                    ...defaultTheme.fontFamily.sans,
                ],
                anton: ['"Anton"', "sans-serif"],
            },
            colors: {
                // Apple Retail Design System Tokens with Teal Brand Theme
                apple: {
                    bg: "#F5F5F7",
                    surface: "#FFFFFF",
                    dark: "#000000",
                    text: "#1D1D1F",
                    subtext: "#86868B",
                    blue: "#019D91",
                    "blue-hover": "#008b80",
                    link: "#018076",
                    secondary: "#02b3a5",
                    border: "#D2D2D7",
                },
                // Harmonized brand palette mapped to Teal #019D91
                brand: {
                    50: "#F0FAF9",
                    100: "#D6F2EF",
                    200: "#ADE4DE",
                    300: "#75CFCA",
                    400: "#38B4B0",
                    500: "#019D91",
                    600: "#019D91",
                    700: "#018076",
                    800: "#01665e",
                    900: "#014c47",
                    950: "#002b28",
                },
            },
            borderRadius: {
                pill: "9999px",
                control: "980px",
            },
        },
    },

    plugins: [forms],
};
