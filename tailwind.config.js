import defaultTheme from "tailwindcss/defaultTheme";
import colors from "tailwindcss/colors";
import forms from "@tailwindcss/forms";

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
        "./resources/js/**/*.js",
    ],

    darkMode: "class",

    theme: {
        extend: {
            fontFamily: {
                // Layout nào chưa nạp Be Vietnam Pro (VD admin) sẽ rơi về Figtree
                sans: ["Be Vietnam Pro", "Figtree", ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // Màu nhấn tông teal (brand-700 = #0f766e)
                brand: colors.teal,
            },
        },
    },

    plugins: [forms],
};
