<x-filament-panels::page>
<div wire:poll.3000ms="pollData" class="fa-page">

    {{-- ===================== STATUS CARDS ROW ===================== --}}
    <div class="fa-cards">

        {{-- Fire Status Card --}}
        <div class="fa-card {{ $fireDetected ? 'danger' : 'safe' }}">
            <div class="fa-card-icon">
                @if($fireDetected)
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                        <path fill-rule="evenodd" d="M12.963 2.286a.75.75 0 0 0-1.071-.136 9.742 9.742 0 0 0-3.539 6.176 7.547 7.547 0 0 1-1.705-1.715.75.75 0 0 0-1.152-.082A9 9 0 1 0 15.68 4.534a7.46 7.46 0 0 1-2.717-2.248ZM15.75 14.25a3.75 3.75 0 1 1-7.313-1.172c.628.465 1.35.81 2.133 1a5.99 5.99 0 0 1 1.925-3.546 3.75 3.75 0 0 1 3.255 3.718Z" clip-rule="evenodd" />
                    </svg>
                @else
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                        <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm13.36-1.814a.75.75 0 1 0-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.14-.094l3.75-5.25Z" clip-rule="evenodd" />
                    </svg>
                @endif
            </div>
            <div class="fa-card-body">
                <div class="fa-card-label">Fire Status</div>
                <div class="fa-card-value">{{ $fireDetected ? '🔥 FIRE DETECTED' : '✅ All Clear' }}</div>
            </div>
            @if($fireDetected)
                <div class="fa-pulse-ring"></div>
            @endif
        </div>

        {{-- Flame Sensor Card --}}
        <div class="fa-card sensor">
            <div class="fa-card-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 14.25v2.25m3-4.5v4.5m3-6.75v6.75m3-9v9M6 20.25h12A2.25 2.25 0 0 0 20.25 18V6A2.25 2.25 0 0 0 18 3.75H6A2.25 2.25 0 0 0 3.75 6v12A2.25 2.25 0 0 0 6 20.25Z" />
                </svg>
            </div>
            <div class="fa-card-body">
                <div class="fa-card-label">Flame Sensor Value</div>
                <div class="fa-card-value sensor-val">{{ number_format($flameValue) }}</div>
                <div class="fa-sensor-bar">
                    @php $pct = min(100, round(($flameValue / 4095) * 100)); @endphp
                    <div class="fa-sensor-fill {{ $pct > 60 ? 'hot' : ($pct > 30 ? 'warm' : 'cool') }}"
                         style="width: {{ $pct }}%"></div>
                </div>
                <div class="fa-sensor-range">0 <span>{{ $pct }}%</span> 4095</div>
            </div>
        </div>

        {{-- Pump Status Card --}}
        <div class="fa-card pump {{ $pumpActive ? 'pump-on' : 'pump-off' }}">
            <div class="fa-card-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.362 5.214A8.252 8.252 0 0 1 12 21 8.25 8.25 0 0 1 6.038 7.047 8.287 8.287 0 0 0 9 9.601a8.983 8.983 0 0 1 3.361-6.867 8.21 8.21 0 0 0 3 2.48Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 18a3.75 3.75 0 0 0 .495-7.468 5.99 5.99 0 0 0-1.925 3.547 5.975 5.975 0 0 1-2.133-1A3.75 3.75 0 0 0 12 18Z" />
                </svg>
            </div>
            <div class="fa-card-body">
                <div class="fa-card-label">Water Pump</div>
                <div class="fa-card-value">{{ $pumpActive ? 'Running' : 'Standby' }}</div>
            </div>
        </div>

    </div>

    {{-- ===================== MAIN PANEL ===================== --}}
    <div class="fa-main-panel">

        {{-- LEFT: Visual fire indicator --}}
        <div class="fa-visual {{ $fireDetected ? 'on-fire' : '' }}">
            <div class="fa-building">
                {{-- Fire animation --}}
                @if($fireDetected)
                <div class="fa-flames">
                    <div class="fa-flame f1"></div>
                    <div class="fa-flame f2"></div>
                    <div class="fa-flame f3"></div>
                </div>
                @endif

                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" class="fa-building-icon">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Z" />
                </svg>

                <div class="fa-status-badge {{ $fireDetected ? 'danger' : 'safe' }}">
                    {{ $fireDetected ? 'FIRE!' : 'SAFE' }}
                </div>
            </div>
        </div>

        {{-- RIGHT: Controls --}}
        <div class="fa-controls">
            <h3 class="fa-controls-title">Manual Controls</h3>

            {{-- Pump Toggle --}}
            <div class="fa-control-row">
                <div class="fa-control-info">
                    <div class="fa-control-name">Water Pump</div>
                    <div class="fa-control-desc">Manually activate to suppress fire</div>
                </div>
                <button wire:click="togglePump" class="fa-pump-btn {{ $pumpActive ? 'active' : '' }}" wire:loading.attr="disabled">
                    <span wire:loading.remove>
                        @if($pumpActive)
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:18px;height:18px;display:inline;vertical-align:middle;margin-right:6px;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 7.5A2.25 2.25 0 0 1 7.5 5.25h9a2.25 2.25 0 0 1 2.25 2.25v9a2.25 2.25 0 0 1-2.25 2.25h-9a2.25 2.25 0 0 1-2.25-2.25v-9Z" />
                            </svg>
                            Stop Pump
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:18px;height:18px;display:inline;vertical-align:middle;margin-right:6px;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.347a1.125 1.125 0 0 1 0 1.972l-11.54 6.347a1.125 1.125 0 0 1-1.667-.986V5.653Z" />
                            </svg>
                            Start Pump
                        @endif
                    </span>
                    <span wire:loading>Processing…</span>
                </button>
            </div>

            {{-- Sensor info --}}
            <div class="fa-info-block">
                <div class="fa-info-row">
                    <span class="fa-info-key">Sensor Reading</span>
                    <span class="fa-info-val">{{ number_format($flameValue) }} / 4095</span>
                </div>
                <div class="fa-info-row">
                    <span class="fa-info-key">Detection Threshold</span>
                    <span class="fa-info-val text-yellow-500">≥ 1500</span>
                </div>
                <div class="fa-info-row">
                    <span class="fa-info-key">Firebase Path</span>
                    <span class="fa-info-val font-mono text-xs">fire-alarm/</span>
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
/* =============================================
   FIRE ALARM — DESIGN SYSTEM
============================================= */
.fa-page {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
    font-family: 'Inter', sans-serif;
}

/* ------- STATUS CARDS ------- */
.fa-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 1rem;
}

.fa-card {
    position: relative;
    overflow: hidden;
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.25rem 1.5rem;
    border-radius: 1rem;
    border: 1.5px solid transparent;
    background: #fff;
    box-shadow: 0 2px 12px rgba(0,0,0,0.06);
    transition: all 0.3s;
}
.dark .fa-card { background: #18181b; }

/* Fire status — DANGER */
.fa-card.danger {
    border-color: #ef4444;
    background: linear-gradient(135deg, #fef2f2, #fff);
    animation: dangerShake 0.4s ease infinite alternate;
}
.dark .fa-card.danger { background: linear-gradient(135deg, #450a0a, #18181b); }
@keyframes dangerShake {
    0%  { transform: translateX(0);    box-shadow: 0 0 20px rgba(239,68,68,0.3); }
    100%{ transform: translateX(2px);  box-shadow: 0 0 30px rgba(239,68,68,0.6); }
}

/* Fire status — SAFE */
.fa-card.safe {
    border-color: #10b981;
    background: linear-gradient(135deg, #ecfdf5, #fff);
}
.dark .fa-card.safe { background: linear-gradient(135deg, #052e16, #18181b); }

/* Sensor card */
.fa-card.sensor { border-color: #f59e0b; flex-direction: column; align-items: flex-start; }
.dark .fa-card.sensor { background: #18181b; }

/* Pump card */
.fa-card.pump-on  { border-color: #3b82f6; background: linear-gradient(135deg, #eff6ff, #fff); }
.fa-card.pump-off { border-color: #e5e7eb; }
.dark .fa-card.pump-on  { background: linear-gradient(135deg, #172554, #18181b); }

.fa-card-icon svg { width: 40px; height: 40px; }
.fa-card.danger .fa-card-icon { color: #ef4444; }
.fa-card.safe   .fa-card-icon { color: #10b981; }
.fa-card.sensor .fa-card-icon { color: #f59e0b; }
.fa-card.pump-on  .fa-card-icon { color: #3b82f6; }
.fa-card.pump-off .fa-card-icon { color: #9ca3af; }

.fa-card-label { font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.06em; color: #9ca3af; }
.fa-card-value { font-size: 1.25rem; font-weight: 800; margin-top: 2px; }
.fa-card.danger .fa-card-value { color: #ef4444; }
.fa-card.safe   .fa-card-value { color: #10b981; }

.sensor-val { font-size: 2rem; color: #f59e0b; }

.fa-pulse-ring {
    position: absolute;
    right: -20px; top: 50%; transform: translateY(-50%);
    width: 80px; height: 80px; border-radius: 50%;
    background: rgba(239,68,68,0.15);
    animation: pulseGrow 1.2s ease-out infinite;
}
@keyframes pulseGrow {
    0%  { transform: translateY(-50%) scale(0.6); opacity: 0.8; }
    100%{ transform: translateY(-50%) scale(1.6); opacity: 0; }
}

/* Sensor bar */
.fa-sensor-bar { width: 100%; height: 8px; background: #e5e7eb; border-radius: 999px; margin-top: 10px; overflow: hidden; }
.dark .fa-sensor-bar { background: #3f3f46; }
.fa-sensor-fill { height: 100%; border-radius: 999px; transition: width 0.5s ease; }
.fa-sensor-fill.cool { background: #10b981; }
.fa-sensor-fill.warm { background: #f59e0b; }
.fa-sensor-fill.hot  { background: linear-gradient(to right, #f59e0b, #ef4444); }
.fa-sensor-range { display: flex; justify-content: space-between; font-size: 0.7rem; color: #9ca3af; margin-top: 4px; }
.fa-sensor-range span { font-weight: 700; color: #6b7280; }

/* ------- MAIN PANEL ------- */
.fa-main-panel {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
}
@media (max-width: 768px) { .fa-main-panel { grid-template-columns: 1fr; } }

/* Visual block */
.fa-visual {
    border-radius: 1rem;
    background: #f3f4f6;
    border: 1.5px solid #e5e7eb;
    min-height: 300px;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    overflow: hidden;
    transition: all 0.4s;
}
.dark .fa-visual { background: #18181b; border-color: #27272a; }
.fa-visual.on-fire {
    background: linear-gradient(180deg, #fef2f2 0%, #fff7ed 100%);
    border-color: #ef4444;
    box-shadow: 0 0 40px rgba(239,68,68,0.2) inset;
}
.dark .fa-visual.on-fire { background: linear-gradient(180deg, #450a0a 0%, #431407 100%); }

.fa-building { position: relative; display: flex; flex-direction: column; align-items: center; gap: 1rem; }
.fa-building-icon { width: 120px; height: 120px; color: #6b7280; }
.fa-visual.on-fire .fa-building-icon { color: #f97316; }

.fa-status-badge {
    padding: 0.35rem 1.25rem;
    border-radius: 999px;
    font-weight: 900;
    font-size: 1.1rem;
    letter-spacing: 0.08em;
}
.fa-status-badge.safe   { background: #dcfce7; color: #16a34a; }
.fa-status-badge.danger { background: #ef4444; color: #fff; animation: badgePulse 0.6s ease infinite alternate; }
@keyframes badgePulse { from { opacity: 1; } to { opacity: 0.6; } }

/* Flame animations */
.fa-flames { position: absolute; bottom: 100%; left: 50%; transform: translateX(-50%); display: flex; gap: 8px; }
.fa-flame {
    width: 20px;
    border-radius: 50% 50% 20% 20%;
    animation: flameAnim 0.5s ease-in-out infinite alternate;
}
.fa-flame.f1 { height: 50px; background: linear-gradient(to top, #ef4444, #f59e0b); animation-delay: 0s; }
.fa-flame.f2 { height: 70px; background: linear-gradient(to top, #dc2626, #ef4444, #fb923c); animation-delay: 0.15s; }
.fa-flame.f3 { height: 45px; background: linear-gradient(to top, #f59e0b, #fbbf24); animation-delay: 0.3s; }
@keyframes flameAnim {
    from { transform: scaleX(1)   scaleY(1)   rotate(-3deg); }
    to   { transform: scaleX(0.9) scaleY(1.1) rotate(3deg); }
}

/* Controls panel */
.fa-controls {
    border-radius: 1rem;
    background: #fff;
    border: 1.5px solid #e5e7eb;
    padding: 1.5rem;
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
    box-shadow: 0 2px 12px rgba(0,0,0,0.06);
}
.dark .fa-controls { background: #18181b; border-color: #27272a; }
.fa-controls-title { font-size: 1.1rem; font-weight: 800; color: #111827; }
.dark .fa-controls-title { color: #f4f4f5; }

.fa-control-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1rem;
    border-radius: 0.75rem;
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    gap: 1rem;
}
.dark .fa-control-row { background: #27272a; border-color: #3f3f46; }

.fa-control-name { font-weight: 700; font-size: 0.95rem; color: #111827; }
.dark .fa-control-name { color: #f4f4f5; }
.fa-control-desc { font-size: 0.75rem; color: #9ca3af; margin-top: 2px; }

.fa-pump-btn {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.6rem 1.25rem;
    border-radius: 0.5rem;
    border: none;
    font-weight: 700;
    font-size: 0.9rem;
    cursor: pointer;
    transition: all 0.2s;
    white-space: nowrap;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
.fa-pump-btn:not(.active) { background: #3b82f6; color: #fff; }
.fa-pump-btn:not(.active):hover { background: #2563eb; transform: translateY(-1px); }
.fa-pump-btn.active { background: #ef4444; color: #fff; animation: pumpGlow 1s ease infinite alternate; }
.fa-pump-btn.active:hover { background: #dc2626; }
@keyframes pumpGlow { from { box-shadow: 0 0 8px rgba(239,68,68,0.4); } to { box-shadow: 0 0 20px rgba(239,68,68,0.8); } }
.fa-pump-btn:disabled { opacity: 0.6; cursor: not-allowed; }

/* Info block */
.fa-info-block {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    background: #f9fafb;
    border-radius: 0.75rem;
    padding: 1rem;
    border: 1px solid #e5e7eb;
}
.dark .fa-info-block { background: #27272a; border-color: #3f3f46; }
.fa-info-row { display: flex; justify-content: space-between; align-items: center; font-size: 0.85rem; }
.fa-info-key { color: #6b7280; font-weight: 500; }
.fa-info-val { color: #111827; font-weight: 700; }
.dark .fa-info-val { color: #e4e4e7; }
</style>
</x-filament-panels::page>
