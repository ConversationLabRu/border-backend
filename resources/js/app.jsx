import './bootstrap';
// import '../css/app.css';

import { createRoot } from 'react-dom/client';
import { createInertiaApp } from '@inertiajs/react';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import React from 'react';
import {AppRouting} from "@/Pages/AppRouting.jsx";

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => require(`./Pages/${name}.jsx`),
    setup({ el, props }) {
        const root = createRoot(el);

        root.render(
            <React.StrictMode>
                <AppRouting />
            </React.StrictMode>
        );
    },
    progress: {
        color: '#4B5563',
    },
});
