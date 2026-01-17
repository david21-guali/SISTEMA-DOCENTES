import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/pages/tasks-form.js',
                'resources/js/pages/projects-form.js',
                'resources/js/pages/calendar-index.js',
                'resources/js/pages/innovations-form.js',
                'resources/js/pages/meetings-form.js',
                'resources/js/pages/reports-comparative.js',
                'resources/js/pages/dashboard-index.js',
                'resources/js/pages/chat-index.js'
            ],
            refresh: true,
        }),
    ],
});
