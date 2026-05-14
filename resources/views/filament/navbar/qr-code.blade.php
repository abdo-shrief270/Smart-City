@php
    $qrValue = url('/about');
    $encoded = urlencode($qrValue);
    $smallUrl = "https://api.qrserver.com/v1/create-qr-code/?size=80x80&margin=2&data={$encoded}";
    $largeUrl = "https://api.qrserver.com/v1/create-qr-code/?size=320x320&margin=4&data={$encoded}";
@endphp

<style>
    .fi-topbar-qr-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 4px;
        background: #fff;
        border: 1px solid rgba(15, 23, 42, 0.1);
        border-radius: 8px;
        cursor: pointer;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .fi-topbar-qr-btn:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);
    }

    .fi-topbar-qr-btn img {
        display: block;
        width: 32px;
        height: 32px;
        border-radius: 4px;
    }

    .fi-topbar-qr-modal {
        position: fixed;
        inset: 0;
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1rem;
    }

    .fi-topbar-qr-modal[hidden] {
        display: none !important;
    }

    .fi-topbar-qr-backdrop {
        position: absolute;
        inset: 0;
        background: rgba(15, 23, 42, 0.65);
        backdrop-filter: blur(4px);
        animation: fi-qr-fade 0.2s ease-out;
    }

    .fi-topbar-qr-dialog {
        position: relative;
        z-index: 1;
        background: #fff;
        color: #0f172a;
        border-radius: 16px;
        padding: 1.5rem;
        width: 100%;
        max-width: 400px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4);
        animation: fi-qr-scale 0.2s ease-out;
    }

    .dark .fi-topbar-qr-dialog {
        background: #1f2937;
        color: #f8fafc;
    }

    .fi-topbar-qr-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1rem;
    }

    .fi-topbar-qr-title {
        font-size: 1.125rem;
        font-weight: 600;
    }

    .fi-topbar-qr-close {
        background: transparent;
        border: none;
        cursor: pointer;
        color: inherit;
        opacity: 0.6;
        font-size: 1.75rem;
        line-height: 1;
        padding: 0 0.5rem;
        transition: opacity 0.2s ease;
    }

    .fi-topbar-qr-close:hover {
        opacity: 1;
    }

    .fi-topbar-qr-body {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.75rem;
    }

    .fi-topbar-qr-body img {
        width: 100%;
        max-width: 320px;
        height: auto;
        border-radius: 12px;
        background: #fff;
        padding: 0.5rem;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }

    .fi-topbar-qr-url {
        font-size: 0.85rem;
        opacity: 0.75;
        text-align: center;
        word-break: break-all;
        margin: 0;
    }

    @keyframes fi-qr-fade {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    @keyframes fi-qr-scale {
        from { opacity: 0; transform: scale(0.92); }
        to { opacity: 1; transform: scale(1); }
    }
</style>

<div x-data="{ open: false }" style="display: inline-flex; align-items: center; margin: 0 0.5rem;">
    <button
        type="button"
        x-on:click="open = true"
        class="fi-topbar-qr-btn"
        title="Show QR code"
        aria-label="Show QR code"
    >
        <img src="{{ $smallUrl }}" alt="QR code" width="32" height="32" loading="lazy">
    </button>

    <template x-teleport="body">
        <div
            class="fi-topbar-qr-modal"
            x-bind:hidden="! open"
            x-on:keydown.escape.window="open = false"
            role="dialog"
            aria-modal="true"
        >
            <div class="fi-topbar-qr-backdrop" x-on:click="open = false"></div>
            <div class="fi-topbar-qr-dialog">
                <div class="fi-topbar-qr-header">
                    <h3 class="fi-topbar-qr-title">Scan QR code</h3>
                    <button
                        type="button"
                        class="fi-topbar-qr-close"
                        x-on:click="open = false"
                        aria-label="Close"
                    >&times;</button>
                </div>
                <div class="fi-topbar-qr-body">
                    <img src="{{ $largeUrl }}" alt="QR code" width="320" height="320">
                    <p class="fi-topbar-qr-url">{{ $qrValue }}</p>
                </div>
            </div>
        </div>
    </template>
</div>
