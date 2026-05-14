<x-filament-panels::page>
<div wire:poll.3000ms="pollData" class="st-page">


    {{-- Compact control dock: mode pill + (auto: live indicator) / (manual: per-light controls) --}}
    <div class="st-dock">
        <button
            type="button"
            wire:click="toggleMode"
            wire:target="toggleMode"
            wire:loading.attr="disabled"
            class="st-dock-mode st-{{ $mode }}"
            title="Switch to {{ $mode === 'manual' ? 'Auto' : 'Manual' }} mode"
        >
            <span class="st-dock-dot"></span>
            <span wire:loading.remove wire:target="toggleMode">
                <strong>{{ strtoupper($mode) }}</strong>
                <span class="st-dock-hint">→ {{ $mode === 'manual' ? 'Auto' : 'Manual' }}</span>
            </span>
            <span wire:loading wire:target="toggleMode">Switching…</span>
        </button>

        @if($mode === 'auto')
            <div class="st-dock-inline">
                <span class="st-live-badge">
                    <span class="st-live-dot"></span>
                    Live from Firebase
                </span>
            </div>
        @else
            <div class="st-dock-inline st-lights-grid">
                @foreach(['north' => 'N', 'south' => 'S', 'east' => 'E', 'west' => 'W'] as $dir => $label)
                    <div class="st-light-ctrl">
                        <span class="st-light-ctrl-label">{{ $label }}</span>
                        @foreach(['red', 'yellow', 'green'] as $color)
                            <button type="button"
                                    wire:click="setLight('{{ $dir }}', '{{ $color }}')"
                                    class="st-light-btn {{ $color }} {{ ($lights[$dir] ?? 'red') === $color ? 'active' : '' }}"
                                    title="Set {{ ucfirst($dir) }} to {{ ucfirst($color) }}"
                                    aria-label="Set {{ $dir }} to {{ $color }}"></button>
                        @endforeach
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- ===================== INTERSECTION ===================== --}}
    <div class="st-intersection-wrap">
        <div class="st-intersection">

            {{-- ---- GRASS QUADRANTS ---- --}}

            {{-- Top-Left: Smart Parking 1 --}}
            <div class="grass tl">
                <div class="grass-icon parking">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <rect x="3.5" y="3.5" width="17" height="17" rx="3.5" stroke-linejoin="round" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.25 17.25V6.75h4.25a2.75 2.75 0 0 1 0 5.5H9.25" />
                    </svg>
                    <span>Smart Parking 1</span>
                </div>
            </div>

            {{-- Top-Right: Smart Farm --}}
            <div class="grass tr">
                <div class="grass-icon farm">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 20.5V11" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 11c0-3.5 2.5-6 6-6 0 3.5-2.5 6-6 6Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 14c0-3-2.2-5.2-5.2-5.2C6.8 11.8 9 14 12 14Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 20.5h14" />
                    </svg>
                    <span>Smart Farm</span>
                </div>
            </div>

            {{-- Bottom-Left: Smart Tank --}}
            <div class="grass bl">
                <div class="grass-icon tank">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 2.5c0 0 7 7.8 7 12.5a7 7 0 0 1-14 0c0-4.7 7-12.5 7-12.5Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" opacity="0.7" d="M8 15a4 4 0 0 0 4 4" />
                    </svg>
                    <span>Smart Tank</span>
                </div>
            </div>

            {{-- Bottom-Right: Smart Parking 2 --}}
            <div class="grass br">
                <div class="grass-icon parking">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <rect x="3.5" y="3.5" width="17" height="17" rx="3.5" stroke-linejoin="round" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.25 17.25V6.75h4.25a2.75 2.75 0 0 1 0 5.5H9.25" />
                    </svg>
                    <span>Smart Parking 2</span>
                </div>
            </div>

            {{-- ---- ROADS ---- --}}
            <div class="road-v"></div>
            <div class="road-h"></div>

            {{-- ---- DASHED CENTER LINES ---- --}}
            <div class="dash-v"></div>
            <div class="dash-h"></div>

            {{-- ---- CENTER CIRCLE ---- --}}
            <div class="center-box">
                <div class="dash-ring"></div>
                <div class="center-island">
                    <div class="center-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:24px;height:24px;opacity:0.45;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75V15m6-6v8.25m.503 3.498 4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.252a1.125 1.125 0 0 0-1.006 0L3.622 5.689C3.24 5.88 3 6.27 3 6.695V19.18c0 .836.88 1.38 1.628 1.006l3.869-1.934c.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006 0Z" />
                        </svg>
                    </div>
                </div>
            </div>

            {{-- ---- TRAFFIC LIGHTS (one per direction, right-of-road) ---- --}}
            @foreach(['north', 'south', 'east', 'west'] as $dir)
                @php $state = $lights[$dir] ?? 'red'; @endphp
                <div class="tl-light {{ $dir }}">
                    <div class="tl-bulb red {{ $state === 'red' ? 'on' : '' }}"></div>
                    <div class="tl-bulb yellow {{ $state === 'yellow' ? 'on' : '' }}"></div>
                    <div class="tl-bulb green {{ $state === 'green' ? 'on' : '' }}"></div>
                </div>
            @endforeach

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
    gap: 0.6rem;
    padding: 0.5rem;
    font-family: 'Inter', sans-serif;
    max-height: calc(100dvh - 8rem);
    overflow: hidden;
}

/* ================ COMPACT DOCK (mode + controls in one row) ================ */
.st-dock {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
    flex-wrap: wrap;
    padding: 0.45rem 0.75rem;
    background: rgba(255,255,255,0.95);
    border: 1px solid rgba(15,23,42,0.1);
    border-radius: 999px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.08);
    backdrop-filter: blur(8px);
    font-size: 0.8rem;
}
.dark .st-dock { background: rgba(24,24,27,0.9); border-color: rgba(255,255,255,0.1); }

.st-dock-mode {
    display: flex; align-items: center; gap: 0.45rem;
    padding: 0.3rem 0.75rem;
    border: none; border-radius: 999px;
    background: transparent;
    font-weight: 700; font-size: 0.75rem; cursor: pointer;
    color: #1f2937;
    transition: background 0.15s, transform 0.15s;
    white-space: nowrap;
}
.dark .st-dock-mode { color: #e4e4e7; }
.st-dock-mode:hover:not(:disabled) { background: rgba(15,23,42,0.05); transform: translateY(-1px); }
.dark .st-dock-mode:hover:not(:disabled) { background: rgba(255,255,255,0.06); }
.st-dock-mode:disabled { cursor: not-allowed; opacity: 0.7; }
.st-dock-mode.st-manual { color: #2563eb; }
.st-dock-mode.st-auto   { color: #059669; }
.st-dock-dot { width: 0.55rem; height: 0.55rem; border-radius: 50%; }
.st-dock-mode.st-manual .st-dock-dot { background: #3b82f6; box-shadow: 0 0 6px rgba(59,130,246,0.7); }
.st-dock-mode.st-auto   .st-dock-dot { background: #10b981; box-shadow: 0 0 6px rgba(16,185,129,0.7); }
.st-dock-hint { font-size: 0.65rem; color: #6b7280; font-weight: 600; margin-left: 0.2rem; }
.dark .st-dock-hint { color: #a1a1aa; }

.st-dock-inline {
    display: flex; align-items: center; gap: 0.5rem;
    padding-left: 0.75rem;
    border-left: 1px solid rgba(15,23,42,0.1);
}
.dark .st-dock-inline { border-left-color: rgba(255,255,255,0.1); }

.st-dock-label {
    font-size: 0.7rem; font-weight: 700; color: #6b7280;
    text-transform: uppercase; letter-spacing: 0.06em;
}
.dark .st-dock-label { color: #a1a1aa; }

.st-live-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    font-size: 0.72rem;
    font-weight: 700;
    color: #059669;
    background: #ecfdf5;
    padding: 0.25rem 0.7rem;
    border-radius: 999px;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    white-space: nowrap;
}
.dark .st-live-badge { color: #34d399; background: rgba(16,185,129,0.15); }
.st-live-dot {
    width: 0.5rem; height: 0.5rem;
    border-radius: 50%;
    background: #10b981;
    box-shadow: 0 0 0 0 rgba(16,185,129,0.7);
    animation: livePulse 1.5s ease-out infinite;
}
@keyframes livePulse {
    0%   { box-shadow: 0 0 0 0 rgba(16,185,129,0.7); }
    70%  { box-shadow: 0 0 0 6px rgba(16,185,129,0); }
    100% { box-shadow: 0 0 0 0 rgba(16,185,129,0); }
}

/* =============================================
   INTERSECTION LAYOUT (scales down to fit viewport)
============================================= */
.st-intersection-wrap {
    /* Fluid scale: 1 on tall screens, shrinks down to 0.5 on very short ones.
       CSS dimensional calc: (length / length) = unitless scalar. */
    --st-scale: max(0.5, min(1, calc((100dvh - 13rem) / 560px)));
    display: flex;
    justify-content: center;
    align-items: flex-start;
    width: calc(560px * var(--st-scale));
    height: calc(560px * var(--st-scale));
    max-width: 100%;
    overflow: visible;
}

.st-intersection {
    position: relative;
    width: 560px;
    height: 560px;
    flex-shrink: 0;
    transform: scale(var(--st-scale));
    transform-origin: top left;
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
.grass-icon.parking svg { color: #e5e7eb; }
.grass-icon.tank svg { color: #60a5fa; }
.grass-icon.farm svg { color: #86efac; }

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
    left: 279.5px; top: 0; bottom: 0;
    width: 2px;
    background-image: repeating-linear-gradient(to bottom, #fef08a 0px, #fef08a 12px, transparent 12px, transparent 24px);
    z-index: 3;
}
.dash-h {
    position: absolute;
    top: 279.5px; left: 0; right: 0;
    height: 2px;
    background-image: repeating-linear-gradient(to right, #fef08a 0px, #fef08a 12px, transparent 12px, transparent 24px);
    z-index: 3;
}

/* ---- CENTER CIRCLE (roundabout island) ---- */
/* Layout container — kept at full intersection size so crosswalks stay at road edges */
.center-box {
    position: absolute;
    left: 190px; top: 190px;
    width: 180px; height: 180px;
    background: transparent;
    z-index: 5;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Actual visual island — smaller centered circle */
.center-island {
    width: 120px;
    height: 120px;
    background: #1f2937;
    border: 3px solid #374151;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 0 0 4px rgba(0,0,0,0.15);
}

/* Dashed yellow ring around the island */
.dash-ring {
    position: absolute;
    top: 50%; left: 50%;
    width: 136px;
    height: 136px;
    transform: translate(-50%, -50%);
    border: 2px dashed #fef08a;
    border-radius: 50%;
    pointer-events: none;
    z-index: 4;
}

.tl-bulb {
    width: 13px;
    height: 13px;
    border-radius: 50%;
    border: 1.5px solid #000;
    opacity: 0.2;
    transition: all 0.25s ease;
    box-shadow: inset 0 1px 2px rgba(0,0,0,0.6);
}
.tl-bulb.red   { background: #991b1b; }
.tl-bulb.yellow{ background: #78350f; }
.tl-bulb.green { background: #14532d; }

.tl-bulb.red.on    { background: #ef4444; opacity: 1; box-shadow: 0 0 8px 2px rgba(239,68,68,0.7); }
.tl-bulb.yellow.on { background: #eab308; opacity: 1; box-shadow: 0 0 8px 2px rgba(234,179,8,0.7); }
.tl-bulb.green.on  { background: #22c55e; opacity: 1; box-shadow: 0 0 8px 2px rgba(34,197,94,0.7); }

/* ---- PER-LIGHT MANUAL CONTROLS ---- */
.st-lights-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 0.4rem;
}
.st-light-ctrl {
    display: flex;
    align-items: center;
    gap: 4px;
    padding: 3px 8px;
    background: rgba(15,23,42,0.05);
    border-radius: 999px;
}
.dark .st-light-ctrl { background: rgba(255,255,255,0.06); }
.st-light-ctrl-label {
    font-size: 0.7rem;
    font-weight: 700;
    color: #6b7280;
    margin-right: 2px;
    letter-spacing: 0.04em;
}
.dark .st-light-ctrl-label { color: #a1a1aa; }
.st-light-btn {
    width: 14px;
    height: 14px;
    padding: 0;
    border-radius: 50%;
    border: 1px solid rgba(0,0,0,0.25);
    cursor: pointer;
    opacity: 0.4;
    transition: transform 0.15s, opacity 0.15s, box-shadow 0.15s;
}
.st-light-btn:hover { opacity: 0.85; transform: scale(1.15); }
.st-light-btn.red    { background: #ef4444; }
.st-light-btn.yellow { background: #eab308; }
.st-light-btn.green  { background: #22c55e; }
.st-light-btn.active { opacity: 1; transform: scale(1.18); }
.st-light-btn.red.active    { box-shadow: 0 0 0 2px #fff, 0 0 8px 2px rgba(239,68,68,0.7); }
.st-light-btn.yellow.active { box-shadow: 0 0 0 2px #fff, 0 0 8px 2px rgba(234,179,8,0.7); }
.st-light-btn.green.active  { box-shadow: 0 0 0 2px #fff, 0 0 8px 2px rgba(34,197,94,0.7); }
.dark .st-light-btn.red.active    { box-shadow: 0 0 0 2px #111, 0 0 8px 2px rgba(239,68,68,0.8); }
.dark .st-light-btn.yellow.active { box-shadow: 0 0 0 2px #111, 0 0 8px 2px rgba(234,179,8,0.8); }
.dark .st-light-btn.green.active  { box-shadow: 0 0 0 2px #111, 0 0 8px 2px rgba(34,197,94,0.8); }

/* ---- TRAFFIC LIGHTS (right-side of each approaching road) ---- */
.tl-light {
    position: absolute;
    background: #111827;
    border: 1.5px solid #374151;
    border-radius: 5px;
    padding: 3px;
    display: flex;
    gap: 3px;
    box-shadow: 0 3px 8px rgba(0,0,0,0.55);
    z-index: 10;
}

/* North & South: vertical stack (default) */
.tl-light.north,
.tl-light.south {
    flex-direction: column;
}

/* East & West: horizontal row */
.tl-light.east,
.tl-light.west {
    flex-direction: row;
}

/* Northbound traffic (going up) — light on driver's right = EAST side, near south edge of intersection */
.tl-light.north {
    bottom: 120px;
    left: 340px;
}

/* Southbound traffic (going down) — light on driver's right = WEST side, near north edge */
.tl-light.south {
    top: 120px;
    right: 340px;
    transform: rotate(180deg);
}

/* Eastbound traffic (going right) — light on driver's right = SOUTH side, near west edge */
.tl-light.east {
    top: 340px;
    left: 120px;

    transform: rotate(180deg);
}

/* Westbound traffic (going left) — light on driver's right = NORTH side, near east edge */
.tl-light.west {
    bottom: 340px;
    right: 120px;
}
</style>
</x-filament-panels::page>
