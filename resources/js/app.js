import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp, router } from '@inertiajs/vue3';
import { Analytics } from './lib/analytics';

// Public-site-only (Analytics no-ops on /admin/* internally). `navigate`
// fires on the very first page load as well as every subsequent Inertia
// visit, so this one hook covers all page-view tracking - no separate
// call needed at init.
Analytics.init();
router.on('navigate', () => Analytics.page());

createInertiaApp({
    resolve: name => {
        const pages = import.meta.glob('./Pages/**/*.vue', {
            eager: true,
        });

        return pages[`./Pages/${name}.vue`];
    },

    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .mount(el);
    },
});