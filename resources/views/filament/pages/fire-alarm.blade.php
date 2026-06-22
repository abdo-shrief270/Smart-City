<x-filament-panels::page>
<div wire:poll.15s="pollData" x-data="fbWatch('SmartEmergency', 'pollData')" class="fa-page">

    @php $danger = $fire || $smoke || $alarm; @endphp

    {{-- ===================== STATUS CARDS ROW ===================== --}}
    <div class="fa-cards">

        {{-- Fire Status Card --}}
        <div class="fa-card {{ $fire ? 'danger' : 'safe' }}">
            <div class="fa-card-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                    <path fill-rule="evenodd" d="M12.963 2.286a.75.75 0 0 0-1.071-.136 9.742 9.742 0 0 0-3.539 6.176 7.547 7.547 0 0 1-1.705-1.715.75.75 0 0 0-1.152-.082A9 9 0 1 0 15.68 4.534a7.46 7.46 0 0 1-2.717-2.248ZM15.75 14.25a3.75 3.75 0 1 1-7.313-1.172c.628.465 1.35.81 2.133 1a5.99 5.99 0 0 1 1.925-3.546 3.75 3.75 0 0 1 3.255 3.718Z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="fa-card-body">
                <div class="fa-card-label">Fire</div>
                <div class="fa-card-value">{{ $fire ? '🔥 DETECTED' : '✅ Clear' }}</div>
            </div>
            @if($fire)<div class="fa-pulse-ring"></div>@endif
        </div>

        {{-- Smoke Status Card --}}
        <div class="fa-card {{ $smoke ? 'danger' : 'safe' }}">
            <div class="fa-card-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.59 14.37a6 6 0 0 1-5.84 7.38v-4.8m5.84-2.58a14.98 14.98 0 0 0 6.16-12.12A14.98 14.98 0 0 0 9.631 8.41m5.96 5.96a14.926 14.926 0 0 1-5.841 2.58m-.119-8.54a6 6 0 0 0-7.381 5.84h4.8m2.581-5.84a14.927 14.927 0 0 0-2.58 5.84m2.699 2.7c-.103.021-.207.041-.311.06a15.09 15.09 0 0 1-2.448-2.448 14.9 14.9 0 0 1 .06-.312m-2.24 2.39a4.493 4.493 0 0 0-1.757 4.306 4.493 4.493 0 0 0 4.306-1.758M16.5 9a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Z" />
                </svg>
            </div>
            <div class="fa-card-body">
                <div class="fa-card-label">Smoke / Gas</div>
                <div class="fa-card-value">{{ $smoke ? '⚠ DETECTED' : '✅ Clear' }}</div>
            </div>
            @if($smoke)<div class="fa-pulse-ring"></div>@endif
        </div>

        {{-- Alarm Status Card --}}
        <div class="fa-card {{ $alarm ? 'danger' : 'safe' }}">
            <div class="fa-card-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                </svg>
            </div>
            <div class="fa-card-body">
                <div class="fa-card-label">Alarm</div>
                <div class="fa-card-value">{{ $alarm ? '🔔 ACTIVE' : 'Silent' }}</div>
            </div>
            @if($alarm)<div class="fa-pulse-ring"></div>@endif
        </div>

    </div>

    {{-- ===================== MAIN PANEL ===================== --}}
    <div class="fa-main-panel">

        {{-- LEFT: Visual indicator --}}
        <div class="fa-visual {{ $danger ? 'on-fire' : '' }}">
            <div class="fa-building">
                @if($fire)
                <div class="fa-flames">
                    <div class="fa-flame f1"></div>
                    <div class="fa-flame f2"></div>
                    <div class="fa-flame f3"></div>
                </div>
                @endif

                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" class="fa-building-icon">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Z" />
                </svg>

                <div class="fa-status-badge {{ $danger ? 'danger' : 'safe' }}">
                    {{ $danger ? 'EMERGENCY' : 'ALL SAFE' }}
                </div>
            </div>
        </div>

        {{-- RIGHT: Controls --}}
        <div class="fa-controls">
            <h3 class="fa-controls-title">Manual Controls</h3>

            <div class="fa-control-row">
                <div class="fa-control-info">
                    <div class="fa-control-name">Alarm / Siren</div>
                    <div class="fa-control-desc">Trigger or silence the emergency alarm</div>
                </div>
                <button wire:click="toggleAlarm" class="fa-pump-btn {{ $alarm ? 'active' : '' }}" wire:loading.attr="disabled" wire:target="toggleAlarm">
                    <span wire:loading.remove wire:target="toggleAlarm">{{ $alarm ? 'Silence Alarm' : 'Trigger Alarm' }}</span>
                    <span wire:loading wire:target="toggleAlarm">Processing…</span>
                </button>
            </div>

            <div class="fa-info-block">
                <div class="fa-info-row">
                    <span class="fa-info-key">Fire Sensor</span>
                    <span class="fa-info-val">{{ $fire ? 'Detected' : 'Clear' }}</span>
                </div>
                <div class="fa-info-row">
                    <span class="fa-info-key">Smoke / Gas Sensor</span>
                    <span class="fa-info-val">{{ $smoke ? 'Detected' : 'Clear' }}</span>
                </div>
                <div class="fa-info-row">
                    <span class="fa-info-key">Alarm State</span>
                    <span class="fa-info-val">{{ $alarm ? 'Active' : 'Silent' }}</span>
                </div>
                <div class="fa-info-row">
                    <span class="fa-info-key">Firebase Path</span>
                    <span class="fa-info-val font-mono text-xs">SmartEmergency/</span>
                </div>
                <div class="fa-info-row">
                    <span class="fa-info-key">Last Updated</span>
                    <span class="fa-info-val">{{ now()->format('H:i:s') }}</span>
                </div>
            </div>
        </div>
    </div>

</div>

<style>
.fa-page { display: flex; flex-direction: column; gap: 1.5rem; font-family: 'Inter', sans-serif; }

/* ------- STATUS CARDS ------- */
.fa-cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem; }

.fa-card {
    position: relative; overflow: hidden;
    display: flex; align-items: center; gap: 1rem;
    padding: 1.25rem 1.5rem;
    border-radius: 1rem;
    border: 1.5px solid transparent;
    background: #fff;
    box-shadow: 0 2px 12px rgba(0,0,0,0.06);
    transition: all 0.3s;
}
.dark .fa-card { background: #18181b; }

.fa-card.danger {
    border-color: #ef4444;
    background: linear-gradient(135deg, #fef2f2, #fff);
    animation: dangerShake 0.4s ease infinite alternate;
}
.dark .fa-card.danger { background: linear-gradient(135deg, #450a0a, #18181b); }
@keyframes dangerShake {
    0%  { transform: translateX(0);   box-shadow: 0 0 20px rgba(239,68,68,0.3); }
    100%{ transform: translateX(2px); box-shadow: 0 0 30px rgba(239,68,68,0.6); }
}

.fa-card.safe { border-color: #10b981; background: linear-gradient(135deg, #ecfdf5, #fff); }
.dark .fa-card.safe { background: linear-gradient(135deg, #052e16, #18181b); }

.fa-card-icon svg { width: 40px; height: 40px; }
.fa-card.danger .fa-card-icon { color: #ef4444; }
.fa-card.safe   .fa-card-icon { color: #10b981; }

.fa-card-label { font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.06em; color: #9ca3af; }
.fa-card-value { font-size: 1.25rem; font-weight: 800; margin-top: 2px; }
.fa-card.danger .fa-card-value { color: #ef4444; }
.fa-card.safe   .fa-card-value { color: #10b981; }

.fa-pulse-ring {
    position: absolute; right: -20px; top: 50%; transform: translateY(-50%);
    width: 80px; height: 80px; border-radius: 50%;
    background: rgba(239,68,68,0.15);
    animation: pulseGrow 1.2s ease-out infinite;
}
@keyframes pulseGrow {
    0%  { transform: translateY(-50%) scale(0.6); opacity: 0.8; }
    100%{ transform: translateY(-50%) scale(1.6); opacity: 0; }
}

/* ------- MAIN PANEL ------- */
.fa-main-panel { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }
@media (max-width: 768px) { .fa-main-panel { grid-template-columns: 1fr; } }

.fa-visual {
    border-radius: 1rem; background: #f3f4f6; border: 1.5px solid #e5e7eb;
    min-height: 300px; display: flex; align-items: center; justify-content: center;
    position: relative; overflow: hidden; transition: all 0.4s;
}
.dark .fa-visual { background: #18181b; border-color: #27272a; }
.fa-visual.on-fire {
    background: linear-gradient(180deg, #fef2f2 0%, #fff7ed 100%);
    border-color: #ef4444; box-shadow: 0 0 40px rgba(239,68,68,0.2) inset;
}
.dark .fa-visual.on-fire { background: linear-gradient(180deg, #450a0a 0%, #431407 100%); }

.fa-building { position: relative; display: flex; flex-direction: column; align-items: center; gap: 1rem; }
.fa-building-icon { width: 120px; height: 120px; color: #6b7280; }
.fa-visual.on-fire .fa-building-icon { color: #f97316; }

.fa-status-badge { padding: 0.35rem 1.25rem; border-radius: 999px; font-weight: 900; font-size: 1.1rem; letter-spacing: 0.08em; }
.fa-status-badge.safe   { background: #dcfce7; color: #16a34a; }
.fa-status-badge.danger { background: #ef4444; color: #fff; animation: badgePulse 0.6s ease infinite alternate; }
@keyframes badgePulse { from { opacity: 1; } to { opacity: 0.6; } }

.fa-flames { position: absolute; bottom: 100%; left: 50%; transform: translateX(-50%); display: flex; gap: 8px; }
.fa-flame { width: 20px; border-radius: 50% 50% 20% 20%; animation: flameAnim 0.5s ease-in-out infinite alternate; }
.fa-flame.f1 { height: 50px; background: linear-gradient(to top, #ef4444, #f59e0b); animation-delay: 0s; }
.fa-flame.f2 { height: 70px; background: linear-gradient(to top, #dc2626, #ef4444, #fb923c); animation-delay: 0.15s; }
.fa-flame.f3 { height: 45px; background: linear-gradient(to top, #f59e0b, #fbbf24); animation-delay: 0.3s; }
@keyframes flameAnim {
    from { transform: scaleX(1)   scaleY(1)   rotate(-3deg); }
    to   { transform: scaleX(0.9) scaleY(1.1) rotate(3deg); }
}

.fa-controls {
    border-radius: 1rem; background: #fff; border: 1.5px solid #e5e7eb;
    padding: 1.5rem; display: flex; flex-direction: column; gap: 1.25rem;
    box-shadow: 0 2px 12px rgba(0,0,0,0.06);
}
.dark .fa-controls { background: #18181b; border-color: #27272a; }
.fa-controls-title { font-size: 1.1rem; font-weight: 800; color: #111827; }
.dark .fa-controls-title { color: #f4f4f5; }

.fa-control-row {
    display: flex; align-items: center; justify-content: space-between;
    padding: 1rem; border-radius: 0.75rem; background: #f9fafb; border: 1px solid #e5e7eb; gap: 1rem;
}
.dark .fa-control-row { background: #27272a; border-color: #3f3f46; }
.fa-control-name { font-weight: 700; font-size: 0.95rem; color: #111827; }
.dark .fa-control-name { color: #f4f4f5; }
.fa-control-desc { font-size: 0.75rem; color: #9ca3af; margin-top: 2px; }

.fa-pump-btn {
    display: flex; align-items: center; gap: 0.5rem;
    padding: 0.6rem 1.25rem; border-radius: 0.5rem; border: none;
    font-weight: 700; font-size: 0.9rem; cursor: pointer; transition: all 0.2s;
    white-space: nowrap; box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
.fa-pump-btn:not(.active) { background: #ef4444; color: #fff; }
.fa-pump-btn:not(.active):hover { background: #dc2626; transform: translateY(-1px); }
.fa-pump-btn.active { background: #6b7280; color: #fff; }
.fa-pump-btn.active:hover { background: #4b5563; }
.fa-pump-btn:disabled { opacity: 0.6; cursor: not-allowed; }

.fa-info-block {
    display: flex; flex-direction: column; gap: 0.5rem;
    background: #f9fafb; border-radius: 0.75rem; padding: 1rem; border: 1px solid #e5e7eb;
}
.dark .fa-info-block { background: #27272a; border-color: #3f3f46; }
.fa-info-row { display: flex; justify-content: space-between; align-items: center; font-size: 0.85rem; }
.fa-info-key { color: #6b7280; font-weight: 500; }
.fa-info-val { color: #111827; font-weight: 700; }
.dark .fa-info-val { color: #e4e4e7; }
</style>
</x-filament-panels::page>
