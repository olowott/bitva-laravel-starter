import "./bootstrap";

import Alpine from "alpinejs";

window.Alpine = Alpine;

Alpine.data("appearance", () => ({
    mode: localStorage.getItem("appearance") || "system",

    init() {
        this.apply();

        this.media = window.matchMedia("(prefers-color-scheme: dark)");

        this.handleSystemChange = () => {
            if (this.mode === "system") {
                this.apply();
            }
        };

        this.media.addEventListener("change", this.handleSystemChange);
    },

    setMode(mode) {
        this.mode = mode;

        localStorage.setItem("appearance", mode);

        this.apply();
    },

    apply() {
        const prefersDark = window.matchMedia(
            "(prefers-color-scheme: dark)",
        ).matches;

        const dark =
            this.mode === "dark" || (this.mode === "system" && prefersDark);

        document.documentElement.classList.toggle("dark", dark);
    },
}));

Alpine.start();
