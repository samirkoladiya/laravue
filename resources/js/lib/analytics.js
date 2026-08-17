/**
 * Lightweight, framework-agnostic analytics client - no Vue import by
 * design, so this file can be copied as-is into a different Laravel +
 * Vue + Inertia project later.
 *
 * Public API:
 *   Analytics.init()             - one-time setup, call once at app boot
 *   Analytics.page()             - track a page view, call on every navigation
 *   Analytics.track(name, data)  - track a custom event
 *   Analytics.getSessionId()     - current session UUID, no network call
 *
 * Deliberately inert on the admin panel (/admin/*) - see isAdminPath() -
 * so the site owner's own admin usage never pollutes visitor data.
 *
 * Visitor/session identity is client-generated but server-authoritative:
 * every call sends the locally-stored ids, and whatever the server
 * returns (echoed, or rotated if a session went stale) overwrites local
 * storage. See VisitorIdentityService on the backend for why.
 */

const ENDPOINT = '/analytics/track';
const VISITOR_COOKIE = 'av_id';
const SESSION_COOKIE = 'av_sid';
const VISITOR_MAX_AGE = 60 * 60 * 24 * 365 * 2; // 2 years
const SESSION_MAX_AGE = 60 * 30; // 30 minutes, sliding on every call

let initialized = false;
let visitorId = null;
let sessionId = null;

function isAdminPath() {
    return window.location.pathname.startsWith('/admin');
}

function generateUuid() {
    if (window.crypto && typeof window.crypto.randomUUID === 'function') {
        return window.crypto.randomUUID();
    }

    // Fallback for browsers without crypto.randomUUID - not
    // cryptographically strong, doesn't need to be for this purpose.
    return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, (c) => {
        const r = (Math.random() * 16) | 0;
        const v = c === 'x' ? r : (r & 0x3) | 0x8;

        return v.toString(16);
    });
}

function getCookie(name) {
    const escaped = name.replace(/[.$?*|{}()[\]\\/+^]/g, '\\$&');
    const match = document.cookie.match(new RegExp(`(?:^|; )${escaped}=([^;]*)`));

    return match ? decodeURIComponent(match[1]) : null;
}

function setCookie(name, value, maxAgeSeconds) {
    document.cookie = `${name}=${encodeURIComponent(value)}; path=/; max-age=${maxAgeSeconds}; SameSite=Lax`;
}

function readStorage(storage, key) {
    try {
        return storage.getItem(key);
    } catch {
        return null;
    }
}

function writeStorage(storage, key, value) {
    try {
        storage.setItem(key, value);
    } catch {
        // Storage disabled/full/private mode - silently degrade to
        // cookie-only persistence.
    }
}

function persistVisitorId(id) {
    setCookie(VISITOR_COOKIE, id, VISITOR_MAX_AGE);
    writeStorage(window.localStorage, VISITOR_COOKIE, id);
}

function persistSessionId(id) {
    setCookie(SESSION_COOKIE, id, SESSION_MAX_AGE);
    writeStorage(window.sessionStorage, SESSION_COOKIE, id);
}

function loadVisitorId() {
    const existing = getCookie(VISITOR_COOKIE) || readStorage(window.localStorage, VISITOR_COOKIE);
    const id = existing || generateUuid();

    persistVisitorId(id);

    return id;
}

function loadSessionId() {
    // The cookie is the real 30-minute sliding timeout; sessionStorage is
    // just a fast local read of the same value within one tab.
    const existing = getCookie(SESSION_COOKIE) || readStorage(window.sessionStorage, SESSION_COOKIE);
    const id = existing || generateUuid();

    persistSessionId(id);

    return id;
}

function applyServerIdentity(response) {
    if (response.visitor_id) {
        visitorId = response.visitor_id;
        persistVisitorId(visitorId);
    }

    if (response.session_id) {
        sessionId = response.session_id;
        persistSessionId(sessionId); // also slides the 30-minute window
    }
}

function send(payload) {
    return fetch(ENDPOINT, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
        body: JSON.stringify({
            visitor_id: visitorId,
            session_id: sessionId,
            referrer: document.referrer || null,
            query_string: window.location.search || null,
            screen: { width: window.screen.width, height: window.screen.height },
            ...payload,
        }),
    })
        .then((res) => (res.ok ? res.json() : null))
        .then((data) => {
            if (data) applyServerIdentity(data);
        })
        .catch(() => {
            // Analytics must never break the site - swallow all failures.
        });
}

function onDelegatedClick(event) {
    const el = event.target.closest('[data-analytics-event]');
    if (!el) return;

    const eventName = el.getAttribute('data-analytics-event');
    const category = el.getAttribute('data-analytics-category');

    track(eventName, category ? { category } : {});
}

function init() {
    if (initialized || isAdminPath()) return;

    visitorId = loadVisitorId();
    sessionId = loadSessionId();

    // Delegated so components just add a data-analytics-event attribute
    // to an existing element (e.g. a `tel:`/`wa.me`/`mailto:` link)
    // instead of importing this module themselves.
    document.addEventListener('click', onDelegatedClick);

    initialized = true;
}

function page() {
    if (isAdminPath()) return;
    if (!initialized) init();

    send({
        type: 'page_view',
        path: window.location.pathname,
        title: document.title,
    });
}

function track(eventName, data = {}) {
    if (isAdminPath()) return;
    if (!initialized) init();

    send({
        type: 'event',
        event_name: eventName,
        event_data: data,
        path: window.location.pathname,
    });
}

function getSessionId() {
    return sessionId;
}

export const Analytics = { init, page, track, getSessionId };
