<x-filament-panels::page>
<div wire:poll.3000ms="pollData" class="st-page"
     x-data="{
         timeLeft: 0,
         _iv: null,
         _triggered: false,
         init() {
             this.tick();
             this._iv = setInterval(() => this.tick(), 1000);
         },
         tick() {
             const nst = $wire.nextSwitchTime;
             if (!nst) { this.timeLeft = 0; this._triggered = false; return; }
             const now = Math.floor(Date.now() / 1000);
             const left = nst - now;
             this.timeLeft = Math.max(0, left);

             // When countdown hits 0, force an immediate server poll to switch lights
             if (left <= 0 && !this._triggered) {
                 this._triggered = true;
                 $wire.pollData().then(() => { this._triggered = false; });
             }
             // Reset trigger when new nextSwitchTime arrives
             if (left > 2) { this._triggered = false; }
         }
     }"
     x-init="init()">


    {{-- Header --}}
    <div class="st-header">
        <div class="st-mode-label">
            Current Mode:
            <span class="st-mode-value {{ $mode }}">{{ ucfirst($mode) }}</span>
        </div>
        <button wire:click="toggleMode" class="st-toggle-btn {{ $mode }}">
            Switch to {{ $mode === 'manual' ? 'Auto' : 'Manual' }}
        </button>
    </div>

    {{-- Auto Mode Settings --}}
    @if($mode === 'auto')
    <div class="st-settings-bar">
        <div class="st-settings-group">
            <span class="st-settings-label">Green Light Duration</span>
            <div class="st-input-wrap">
                <input type="number" wire:model.defer="greenTimer" class="st-input" min="5" />
                <span class="st-input-unit">sec</span>
            </div>
            <button wire:click="saveTimers" class="st-save-btn">Save</button>
        </div>
        <div class="st-countdown" :class="{ urgent: timeLeft <= 5 }">
            Next switch in <strong x-text="timeLeft + 's'"></strong>
        </div>
    </div>
    @endif

    {{-- Manual Controls --}}
    @if($mode === 'manual')
    <div class="st-manual-controls">
        <button wire:click="setDirection('ns_green')"
                class="st-dir-btn {{ $direction === 'ns_green' ? 'active' : '' }}"
                {{ $isYellow ? 'disabled' : '' }}>
            ↕ N/S Green
        </button>
        <button wire:click="setDirection('ew_green')"
                class="st-dir-btn {{ $direction === 'ew_green' ? 'active' : '' }}"
                {{ $isYellow ? 'disabled' : '' }}>
            ↔ E/W Green
        </button>
    </div>
    @endif

    {{-- ===================== INTERSECTION ===================== --}}
    @php
        $nsState = $direction === 'ns_green' ? ($isYellow ? 'yellow' : 'green') : 'red';
        $ewState = $direction === 'ew_green' ? ($isYellow ? 'yellow' : 'green') : 'red';
    @endphp

    <div class="st-intersection-wrap">
        <div class="st-intersection">

            {{-- ---- GRASS QUADRANTS ---- --}}

            {{-- Top-Left: Smart Parking 1 --}}
            <div class="grass tl">
                <div class="grass-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" />
                    </svg>
                    <span>Smart Parking 1</span>
                </div>
            </div>

            {{-- Top-Right: Smart Parking 2 --}}
            <div class="grass tr">
                <div class="grass-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" />
                    </svg>
                    <span>Smart Parking 2</span>
                </div>
            </div>

            {{-- Bottom-Left: Smart Tank --}}
            <div class="grass bl">
                <div class="grass-icon tank">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.104v5.714a2.25 2.25 0 0 1-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 0 1 4.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l-1.57.393A9.065 9.065 0 0 1 12 15a9.065 9.065 0 0 1-6.23-.693L5 14.5m14.8.8 1.402 1.402c1.232 1.232.65 3.318-1.067 3.611A48.309 48.309 0 0 1 12 21c-2.773 0-5.491-.235-8.135-.687-1.718-.293-2.3-2.379-1.067-3.61L5 14.5" />
                    </svg>
                    <span>Smart Tank</span>
                </div>
            </div>

            {{-- Bottom-Right: Smart Farm --}}
            <div class="grass br">
                <div class="grass-icon farm">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z" />
                    </svg>
                    <span>Smart Farm</span>
                </div>
            </div>

            {{-- ---- ROADS ---- --}}
            <div class="road-v"></div>
            <div class="road-h"></div>

            {{-- ---- DASHED CENTER LINES ---- --}}
            <div class="dash-v"></div>
            <div class="dash-h"></div>

            {{-- ---- CENTER BOX ---- --}}
            <div class="center-box">
                @if($mode === 'auto')
                    <div class="center-timer" :class="{ urgent: timeLeft <= 3 }" x-text="timeLeft"></div>
                @else
                    <div class="center-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:30px;height:30px;opacity:0.4;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75V15m6-6v8.25m.503 3.498 4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.252a1.125 1.125 0 0 0-1.006 0L3.622 5.689C3.24 5.88 3 6.27 3 6.695V19.18c0 .836.88 1.38 1.628 1.006l3.869-1.934c.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006 0Z" />
                        </svg>
                    </div>
                @endif
            </div>

            {{-- ---- TRAFFIC LIGHTS ---- --}}

            {{-- North --}}
            <div class="tl-light north">
                <div class="tl-bulb red {{ $nsState === 'red' ? 'on' : '' }}"></div>
                <div class="tl-bulb yellow {{ $nsState === 'yellow' ? 'on' : '' }}"></div>
                <div class="tl-bulb green {{ $nsState === 'green' ? 'on' : '' }}"></div>
            </div>

            {{-- South --}}
            <div class="tl-light south">
                <div class="tl-bulb red {{ $nsState === 'red' ? 'on' : '' }}"></div>
                <div class="tl-bulb yellow {{ $nsState === 'yellow' ? 'on' : '' }}"></div>
                <div class="tl-bulb green {{ $nsState === 'green' ? 'on' : '' }}"></div>
            </div>

            {{-- East --}}
            <div class="tl-light east">
                <div class="tl-bulb red {{ $ewState === 'red' ? 'on' : '' }}"></div>
                <div class="tl-bulb yellow {{ $ewState === 'yellow' ? 'on' : '' }}"></div>
                <div class="tl-bulb green {{ $ewState === 'green' ? 'on' : '' }}"></div>
            </div>

            {{-- West --}}
            <div class="tl-light west">
                <div class="tl-bulb red {{ $ewState === 'red' ? 'on' : '' }}"></div>
                <div class="tl-bulb yellow {{ $ewState === 'yellow' ? 'on' : '' }}"></div>
                <div class="tl-bulb green {{ $ewState === 'green' ? 'on' : '' }}"></div>
            </div>

        </div>{{-- /st-intersection --}}
    </div>{{-- /st-intersection-wrap --}}

</div>{{-- /st-page --}}


<style>
/* =============================================
   SMART TRAFFIC — DESIGN SYSTEM
============================================= */
.st-page {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1.5rem;
    padding: 1rem;
    font-family: 'Inter', sans-serif;
}

/* --- HEADER --- */
.st-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    width: 100%;
    max-width: 680px;
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 1rem;
    padding: 1rem 1.5rem;
    box-shadow: 0 2px 12px rgba(0,0,0,0.06);
}
.dark .st-header { background: #18181b; border-color: #27272a; }

.st-mode-label { font-size: 1rem; font-weight: 600; color: #6b7280; }
.st-mode-value.manual { color: #3b82f6; }
.st-mode-value.auto { color: #10b981; }

.st-toggle-btn {
    padding: 0.5rem 1.25rem;
    border-radius: 0.5rem;
    border: none;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.2s;
}
.st-toggle-btn.manual { background: #dcfce7; color: #16a34a; }
.st-toggle-btn.manual:hover { background: #bbf7d0; }
.st-toggle-btn.auto { background: #dbeafe; color: #2563eb; }
.st-toggle-btn.auto:hover { background: #bfdbfe; }
.dark .st-toggle-btn.manual { background: rgba(22,163,74,0.2); color: #4ade80; }
.dark .st-toggle-btn.auto { background: rgba(37,99,235,0.2); color: #60a5fa; }

/* --- SETTINGS BAR (AUTO) --- */
.st-settings-bar {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 2rem;
    flex-wrap: wrap;
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 1rem;
    padding: 1rem 2rem;
    width: 100%;
    max-width: 680px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.06);
}
.dark .st-settings-bar { background: #18181b; border-color: #27272a; }

.st-settings-group { display: flex; align-items: center; gap: 0.75rem; }
.st-settings-label { font-size: 0.875rem; font-weight: 600; color: #6b7280; white-space: nowrap; }
.dark .st-settings-label { color: #9ca3af; }

.st-input-wrap { position: relative; }
.st-input {
    border: 2px solid #e5e7eb;
    border-radius: 0.5rem;
    background: #f9fafb;
    color: #111827;
    padding: 0.4rem 2.5rem 0.4rem 0.4whenrem;
    font-size: 1rem;
    font-weight: 700;
    width: 120px;
    text-align: center;
    transition: border-color 0.2s;
}
.st-input:focus { outline: none; border-color: #10b981; }
.dark .st-input { background: #1f1f23; border-color: #3f3f46; color: #fff; }
.st-input-unit { position: absolute; right: 8px; top: 50%; transform: translateY(-50%); font-size: 0.75rem; color: #9ca3af; pointer-events: none; }

.st-save-btn {
    background: #10b981; color: #fff;
    padding: 0.4rem 1rem;
    border-radius: 0.5rem; border: none;
    font-weight: 700; cursor: pointer;
    transition: all 0.2s;
    box-shadow: 0 2px 6px rgba(16,185,129,0.3);
}
.st-save-btn:hover { background: #059669; transform: translateY(-1px); }

.st-countdown {
    font-size: 0.9rem; font-weight: 600;
    background: #ecfdf5; color: #10b981;
    padding: 0.3rem 0.75rem; border-radius: 999px;
    transition: all 0.3s;
}
.st-countdown.urgent { background: #fef2f2; color: #ef4444; animation: urgentPulse 0.8s infinite alternate; }
.dark .st-countdown { background: rgba(16,185,129,0.15); color: #34d399; }
.dark .st-countdown.urgent { background: rgba(239,68,68,0.15); color: #f87171; }
@keyframes urgentPulse { from { opacity: 1; } to { opacity: 0.6; } }

/* --- MANUAL CONTROLS --- */
.st-manual-controls {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
    width: 100%;
    max-width: 500px;
}
.st-dir-btn {
    flex: 1;
    min-width: 160px;
    padding: 0.75rem 1.5rem;
    border-radius: 0.75rem; border: 2px solid #e5e7eb;
    background: #f3f4f6; color: #374151;
    font-weight: 700; font-size: 1rem;
    cursor: pointer; transition: all 0.2s;
    box-shadow: 0 2px 6px rgba(0,0,0,0.07);
}
.dark .st-dir-btn { background: #27272a; border-color: #3f3f46; color: #e4e4e7; }
.st-dir-btn:hover:not(:disabled) { background: #e5e7eb; transform: translateY(-2px); }
.dark .st-dir-btn:hover:not(:disabled) { background: #3f3f46; }
.st-dir-btn.active { background: #3b82f6; color: #fff; border-color: #3b82f6; box-shadow: 0 0 14px rgba(59,130,246,0.4); }
.dark .st-dir-btn.active { background: #2563eb; border-color: #2563eb; }
.st-dir-btn:disabled { opacity: 0.5; cursor: not-allowed; animation: btnPulse 1s infinite alternate; }
@keyframes btnPulse { from { transform: scale(1); } to { transform: scale(0.97); opacity: 0.4; } }

/* =============================================
   INTERSECTION LAYOUT
============================================= */
.st-intersection-wrap {
    display: flex;
    justify-content: center;
    align-items: center;
    width: 100%;
}

.st-intersection {
    position: relative;
    width: 560px;
    height: 560px;
    /* Grid of 4 grass quads + road cross */
}

/* ---- GRASS QUADRANTS ---- */
.grass {
    position: absolute;
    background-color: #4ade80; /* green-400 */
    border-radius: 0;
    display: flex;
    align-items: center;
    justify-content: center;
}
.dark .grass { background-color: #166534; }

/* Quad sizes: each is (560/2 - road_half) = 280 - 90 = 190px */
/* Road width = 180px. Half = 90px. Center at 280px. */
/* So quads: TL is top:0,left:0 size 190×190 */
.grass.tl { top: 0;    left: 0;    width: 190px; height: 190px; border-radius: 1.5rem 0 0 0; }
.grass.tr { top: 0;    right: 0;   width: 190px; height: 190px; border-radius: 0 1.5rem 0 0; }
.grass.bl { bottom: 0; left: 0;    width: 190px; height: 190px; border-radius: 0 0 0 1.5rem; }
.grass.br { bottom: 0; right: 0;   width: 190px; height: 190px; border-radius: 0 0 1.5rem 0; }

.grass-icon {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 6px;
    color: #fff;
}
.grass-icon svg {
    width: 56px;
    height: 56px;
    filter: drop-shadow(0 2px 6px rgba(0,0,0,0.4));
}
.grass-icon span {
    font-size: 0.65rem;
    font-weight: 700;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    background: rgba(0,0,0,0.3);
    padding: 2px 8px;
    border-radius: 999px;
    white-space: nowrap;
}
.grass-icon.tank svg { color: #60a5fa; }
.grass-icon.farm svg { color: #fde68a; }

/* ---- ROADS ---- */
.road-v {
    position: absolute;
    left: 190px;
    width: 180px;
    top: 0; bottom: 0;
    background: #374151;
    border-left: 4px solid #d1d5db33;
    border-right: 4px solid #d1d5db33;
    z-index: 2;
}
.road-h {
    position: absolute;
    top: 190px;
    height: 180px;
    left: 0; right: 0;
    background: #374151;
    border-top: 4px solid #d1d5db33;
    border-bottom: 4px solid #d1d5db33;
    z-index: 2;
}

/* ---- DASHED CENTER LINES ---- */
.dash-v {
    position: absolute;
    left: 279px; top: 0; bottom: 0;
    width: 4px;
    background-image: repeating-linear-gradient(to bottom, #fef08a 0px, #fef08a 20px, transparent 20px, transparent 40px);
    z-index: 3;
}
.dash-h {
    position: absolute;
    top: 279px; left: 0; right: 0;
    height: 4px;
    background-image: repeating-linear-gradient(to right, #fef08a 0px, #fef08a 20px, transparent 20px, transparent 40px);
    z-index: 3;
}

/* ---- CENTER BOX ---- */
.center-box {
    position: absolute;
    left: 190px; top: 190px;
    width: 180px; height: 180px;
    background: #1f2937;
    z-index: 5;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 3px solid #374151;
}

.center-timer {
    font-size: 4rem;
    font-weight: 900;
    color: #fff;
    font-family: monospace;
    text-shadow: 0 0 20px rgba(255,255,255,0.3);
    transition: color 0.2s;
}
.center-timer.urgent { color: #ef4444; text-shadow: 0 0 20px rgba(239,68,68,0.6); }

/* ---- TRAFFIC LIGHTS ---- */
.tl-light {
    position: absolute;
    background: #111827;
    border: 2px solid #374151;
    border-radius: 10px;
    padding: 7px;
    display: flex;
    gap: 7px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.5);
    z-index: 10;
}

/* North: horizontal light sitting on vertical road, upper arm */
.tl-light.north {
    flex-direction: row;
    left: 50%; transform: translateX(-50%);
    top: 100px;
}
/* South: horizontal light sitting on vertical road, lower arm */
.tl-light.south {
    flex-direction: row;
    left: 50%; transform: translateX(-50%);
    bottom: 100px;
}
/* East: vertical light on horizontal road, right arm */
.tl-light.east {
    flex-direction: column;
    top: 50%; transform: translateY(-50%);
    right: 100px;
}
/* West: vertical light on horizontal road, left arm */
.tl-light.west {
    flex-direction: column;
    top: 50%; transform: translateY(-50%);
    left: 100px;
}

.tl-bulb {
    width: 26px;
    height: 26px;
    border-radius: 50%;
    border: 2px solid #000;
    opacity: 0.2;
    transition: all 0.3s ease;
    box-shadow: inset 0 2px 4px rgba(0,0,0,0.6);
}
.tl-bulb.red   { background: #991b1b; }
.tl-bulb.yellow{ background: #78350f; }
.tl-bulb.green { background: #14532d; }

.tl-bulb.red.on    { background: #ef4444; opacity: 1; box-shadow: 0 0 14px 4px rgba(239,68,68,0.7); }
.tl-bulb.yellow.on { background: #eab308; opacity: 1; box-shadow: 0 0 14px 4px rgba(234,179,8,0.7); }
.tl-bulb.green.on  { background: #22c55e; opacity: 1; box-shadow: 0 0 14px 4px rgba(34,197,94,0.7); }

/* Crosswalks */
.center-box::before {
    content: '';
    position: absolute;
    top: -12px; left: 0; right: 0; height: 10px;
    background-image: repeating-linear-gradient(to right, #e5e7eb 0px, #e5e7eb 12px, transparent 12px, transparent 24px);
}
.center-box::after {
    content: '';
    position: absolute;
    bottom: -12px; left: 0; right: 0; height: 10px;
    background-image: repeating-linear-gradient(to right, #e5e7eb 0px, #e5e7eb 12px, transparent 12px, transparent 24px);
}
</style>
</x-filament-panels::page>
