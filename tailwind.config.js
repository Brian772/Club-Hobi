import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
    ],

    theme: {
        extend: {
            colors: {
                // Primary & Secondary
                primary: {
                    DEFAULT: "#0075de",
                    active: "#005bb0",
                },
                secondary: "#213183",

                // Ink (Typography)
                ink: {
                    DEFAULT: "#000000",
                    secondary: "#31302e",
                    muted: "#615d59",
                    faint: "#a39e98",
                },

                // Canvas & Surface
                canvas: {
                    DEFAULT: "#ffffff",
                    soft: "#f6f5f4",
                },
                outline: "#e6e6e6",

                // Accents
                accent: {
                    sky: "#62aef0",
                    purple: "#d6b6f6",
                    pink: "#ff64c8",
                    orange: "#dd5b00",
                    teal: "#2a9d99",
                    green: "#1aae39",
                    brown: "#523410",
                    red: "#ff383c",
                    yellow: "#ffcc00",
                },
            },

            // 02. TYPOGRAPHY
            fontSize: {
                // [fontSize, { lineHeight, letterSpacing, fontWeight }]
                "display-1": [
                    "64px",
                    {
                        lineHeight: "1.1",
                        letterSpacing: "-0.05em",
                        fontWeight: "700",
                    },
                ],
                "display-2": [
                    "54px",
                    {
                        lineHeight: "1.1",
                        letterSpacing: "-0.025em",
                        fontWeight: "700",
                    },
                ],
                "heading-1": [
                    "40px",
                    {
                        lineHeight: "1.1",
                        letterSpacing: "-0.01em",
                        fontWeight: "700",
                    },
                ],
                "heading-2": [
                    "26px",
                    {
                        lineHeight: "1.15",
                        letterSpacing: "-0.0075em",
                        fontWeight: "700",
                    },
                ],
                "heading-3": [
                    "22px",
                    {
                        lineHeight: "1.2",
                        letterSpacing: "-0.005em",
                        fontWeight: "700",
                    },
                ],
                title: [
                    "20px",
                    {
                        lineHeight: "1.2",
                        letterSpacing: "-0.0025em",
                        fontWeight: "600",
                    },
                ],
                "body-mid": [
                    "16px",
                    {
                        lineHeight: "1.5",
                        letterSpacing: "0em",
                        fontWeight: "400",
                    },
                ],
                "body-sm": [
                    "15px",
                    {
                        lineHeight: "1.33",
                        letterSpacing: "0em",
                        fontWeight: "400",
                    },
                ],
                "btn-text": [
                    "16px",
                    {
                        lineHeight: "1.5",
                        letterSpacing: "0em",
                        fontWeight: "600",
                    },
                ],
                caption: [
                    "14px",
                    {
                        lineHeight: "1.43",
                        letterSpacing: "0em",
                        fontWeight: "400",
                    },
                ],
                overline: [
                    "12px",
                    {
                        lineHeight: "1.33",
                        letterSpacing: "0.05em",
                        fontWeight: "600",
                    },
                ],
            },

            // 04. BORDER RADIUS
            borderRadius: {
                xs: "4px",
                sm: "5px",
                md: "8px",
                lg: "12px",
                xl: "16px",
                full: "200px",
            },
        },
    },

    plugins: [forms],
};
