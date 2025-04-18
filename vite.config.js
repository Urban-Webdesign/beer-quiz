import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import vue from "@vitejs/plugin-vue";
import { visualizer } from "rollup-plugin-visualizer";

export default defineConfig({
    plugins: [
        vue(),
        laravel({
            input: ["resources/css/app.css", "resources/js/app.js"],
            refresh: true,
        }),
        visualizer({
           filename: './dist/stats.html',
           open: true,
           gzipSize: true,
           brotliSize: true,
        })],
    resolve: {
        alias: {
            vue: "vue/dist/vue.esm-bundler.js",

        },
    },
    build: {
        target: 'es2018',
        minify: 'esbuild',
        sourcemap: false,
        cssCodeSplit: true,
        rollupOptions: {
            output: {
                manualChunks(id) {
                    if (id.includes("node_modules")) {
                        if (id.includes("vue")) return "vue";
                        if (id.includes("vue-router")) return "vue-router";
                        return "vendor";
                    }
                },
            },
        },
    },
});