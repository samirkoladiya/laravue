const cache = new Map();

/**
 * Loads an external <script> once and caches the promise, so multiple
 * components asking for the same URL all await the same load instead of
 * inserting duplicate tags.
 */
export function loadScript(src) {
    if (cache.has(src)) {
        return cache.get(src);
    }

    const promise = new Promise((resolve, reject) => {
        const script = document.createElement('script');
        script.src = src;
        script.async = true;
        script.onload = () => resolve();
        script.onerror = () => reject(new Error(`Failed to load script: ${src}`));
        document.body.appendChild(script);
    });

    cache.set(src, promise);

    return promise;
}
