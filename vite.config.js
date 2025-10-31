// import { defineConfig } from 'vite';
// import laravel from 'laravel-vite-plugin';
// import i18n from 'laravel-vue-i18n/vite';
// import VueI18nPlugin from '@intlify/unplugin-vue-i18n/vite';
// import vue from '@vitejs/plugin-vue';
// import path from 'path';

// export default defineConfig({
//     plugins: [
//         vue(),
//         VueI18nPlugin({
//             // Define the path to your locale files
//             include: path.resolve(__dirname, 'lang/**')
//         }),
//         laravel({
//             input: ['resources/css/app.css', 'resources/js/app.js'],
//             refresh: true,
//         }),
//         i18n(),
//     ],
//     resolve: {
//         alias: {
//             '@': path.resolve(__dirname, 'resources/js'),
//             '@modules': path.resolve(__dirname, 'modules'),
//         },
//     },
// });



import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import i18n from 'laravel-vue-i18n/vite';
import VueI18nPlugin from '@intlify/unplugin-vue-i18n/vite';
import vue from '@vitejs/plugin-vue';
import path from 'path';

export default defineConfig({
    plugins: [
        vue(),
        VueI18nPlugin({
            // Define the path to your locale files
            include: path.resolve(__dirname, 'lang/**')
        }),
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        i18n(),
    ],
    resolve: {
        alias: {
            '@': path.resolve(__dirname, 'resources/js'),
            '@modules': path.resolve(__dirname, 'modules'),
        },
    },

    // <--- watcher settings to avoid ENOSPC errors
    server: {
        watch: {
            // ignore large folders so Vite doesn't try to watch them
            ignored: [
                '**/vendor/**',
                '**/node_modules/**',
                '**/.git/**',
                '**/storage/**',
                '**/public/**',
                '**/dist/**'
            ],
            // Uncomment only if you can't raise inotify limits and must use polling:
            // usePolling: true,
            // interval: 1000
        }
    }
});
