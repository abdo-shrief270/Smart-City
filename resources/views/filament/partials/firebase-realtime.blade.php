@php($fbSettings = app(\App\Settings\FirebaseSettings::class))

@if(!empty($fbSettings->database_url))
    @php($fbConfig = [
        'apiKey' => $fbSettings->api_key,
        'authDomain' => $fbSettings->auth_domain,
        'databaseURL' => $fbSettings->database_url,
        'projectId' => $fbSettings->project_id,
        'storageBucket' => $fbSettings->storage_bucket,
        'messagingSenderId' => $fbSettings->messaging_sender_id,
        'appId' => $fbSettings->app_id,
    ])

    {{-- Synchronous stub: available before the (async) module finishes loading. --}}
    <script>
        window.__fb = window.__fb || {
            ready: false,
            _pending: [],
            // Returns an unsubscribe function. If the SDK isn't ready yet the
            // subscription is queued and wired up as soon as it loads.
            subscribe(path, cb) {
                if (this.ready) return this._sub(path, cb);
                const entry = { path, cb, off: null, canceled: false };
                this._pending.push(entry);
                return () => { entry.canceled = true; if (entry.off) entry.off(); };
            },
            _sub() { return () => {}; },
            _flush() {
                this._pending.forEach((e) => { if (!e.canceled) e.off = this._sub(e.path, e.cb); });
                this._pending = [];
            },
        };

        // Alpine helper: <div x-data="fbWatch('SmartTank', 'fetchData')">
        // Calls the given Livewire method whenever the node changes, and cleans
        // up its listener when the element is removed (SPA navigation).
        if (!window.__fbAlpineRegistered) {
            window.__fbAlpineRegistered = true;
            document.addEventListener('alpine:init', () => {
                Alpine.data('fbWatch', (path, method) => ({
                    _off: null,
                    init() {
                        if (!window.__fb) return;
                        this._off = window.__fb.subscribe(path, () => this.$wire.call(method));
                    },
                    destroy() { if (this._off) this._off(); },
                }));
            });
        }
    </script>

    {{-- Firebase modular SDK: opens a single realtime WebSocket to the RTDB. --}}
    <script type="module">
        import { initializeApp, getApps } from 'https://www.gstatic.com/firebasejs/10.12.0/firebase-app.js';
        import { getDatabase, ref, onValue } from 'https://www.gstatic.com/firebasejs/10.12.0/firebase-database.js';

        try {
            const cfg = @json($fbConfig);
            const app = getApps().length ? getApps()[0] : initializeApp(cfg);
            const db = getDatabase(app);

            window.__fb._sub = (path, cb) => onValue(ref(db, path), (snap) => cb(snap.val()));
            window.__fb.ready = true;
            window.__fb._flush();
        } catch (e) {
            console.error('Firebase realtime init failed; falling back to polling.', e);
        }
    </script>
@endif
