<x-filament-panels::page>
<div wire:poll.15s="pollData" x-data="fbWatch('SmartTraffic', 'pollData')" class="st-page">

    {{-- ===================== CONTROL DOCK ===================== --}}
    <div class="st-dock">
        <span class="st-dock-title">Manual Override</span>
        <div class="st-dock-btns">
            <button type="button"
                    wire:click="setActive('A')"
                    class="st-go-btn a {{ $light === 'A' ? 'active' : '' }}">
                Green → Road A
            </button>
            <button type="button"
                    wire:click="setActive('B')"
                    class="st-go-btn b {{ $light === 'B' ? 'active' : '' }}">
                Green → Road B
            </button>
        </div>
    </div>

    {{-- ===================== INTERSECTION ===================== --}}
    @php
        $aGreen = $light === 'A';
        $bGreen = $light === 'B';
    @endphp
    <div class="st-intersection-wrap">
        <div class="st-intersection">

            {{-- Grass quadrants --}}
            <div class="grass tl"></div>
            <div class="grass tr"></div>
            <div class="grass bl"></div>
            <div class="grass br"></div>

            {{-- Roads: A = horizontal, B = vertical --}}
            <div class="road-h"></div>
            <div class="road-v"></div>
            <div class="dash-h"></div>
            <div class="dash-v"></div>

            {{-- Road labels --}}
            <div class="road-label a">ROAD A</div>
            <div class="road-label b">ROAD B</div>

            {{-- Traffic light for Road A (controls horizontal traffic) --}}
            <div class="tl-light a">
                <div class="tl-bulb red {{ $aGreen ? '' : 'on' }}"></div>
                <div class="tl-bulb yellow"></div>
                <div class="tl-bulb green {{ $aGreen ? 'on' : '' }}"></div>
            </div>

            {{-- Traffic light for Road B (controls vertical traffic) --}}
            <div class="tl-light b">
                <div class="tl-bulb red {{ $bGreen ? '' : 'on' }}"></div>
                <div class="tl-bulb yellow"></div>
                <div class="tl-bulb green {{ $bGreen ? 'on' : '' }}"></div>
            </div>

            {{-- Center island --}}
            <div class="center-island"></div>
        </div>
    </div>

    {{-- ===================== ROAD COUNT CARDS ===================== --}}
    <div class="st-cards">
        <div class="st-card {{ $aGreen ? 'go' : 'stop' }}">
            <div class="st-card-top">
                <span class="st-card-name">Road A</span>
                <span class="st-signal {{ $aGreen ? 'go' : 'stop' }}">{{ $aGreen ? 'GO' : 'STOP' }}</span>
            </div>
            <div class="st-card-count">{{ $roadA }}</div>
            <div class="st-card-sub">vehicles waiting</div>
        </div>

        <div class="st-card {{ $bGreen ? 'go' : 'stop' }}">
            <div class="st-card-top">
                <span class="st-card-name">Road B</span>
                <span class="st-signal {{ $bGreen ? 'go' : 'stop' }}">{{ $bGreen ? 'GO' : 'STOP' }}</span>
            </div>
            <div class="st-card-count">{{ $roadB }}</div>
            <div class="st-card-sub">vehicles waiting</div>
        </div>
    </div>

</div>

<style>
.st-page {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1.25rem;
    font-family: 'Inter', sans-serif;
}

/* ================ CONTROL DOCK ================ */
.st-dock {
    display: flex;
    align-items: center;
    gap: 1rem;
    flex-wrap: wrap;
    justify-content: center;
    padding: 0.6rem 1rem;
    background: rgba(255,255,255,0.95);
    border: 1px solid rgba(15,23,42,0.1);
    border-radius: 999px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.08);
}
.dark .st-dock { background: rgba(24,24,27,0.9); border-color: rgba(255,255,255,0.1); }
.st-dock-title { font-size: 0.7rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em; color: #6b7280; }
.dark .st-dock-title { color: #a1a1aa; }
.st-dock-btns { display: flex; gap: 0.5rem; }
.st-go-btn {
    padding: 0.4rem 0.9rem;
    border-radius: 999px;
    border: 2px solid transparent;
    font-weight: 700; font-size: 0.8rem; cursor: pointer;
    background: #f3f4f6; color: #374151;
    transition: all 0.15s;
}
.dark .st-go-btn { background: #27272a; color: #d4d4d8; }
.st-go-btn:hover { transform: translateY(-1px); }
.st-go-btn.active { background: #22c55e; color: #fff; box-shadow: 0 0 0 3px rgba(34,197,94,0.25); }

/* ================ INTERSECTION ================ */
.st-intersection-wrap {
    --st-scale: max(0.55, min(1, calc((100dvh - 24rem) / 440px)));
    display: flex;
    justify-content: center;
    width: calc(440px * var(--st-scale));
    height: calc(440px * var(--st-scale));
    max-width: 100%;
}
.st-intersection {
    position: relative;
    width: 440px; height: 440px;
    flex-shrink: 0;
    transform: scale(var(--st-scale));
    transform-origin: top center;
}

.grass {
    position: absolute;
    background-color: #4ade80;
    width: 150px; height: 150px;
}
.dark .grass { background-color: #166534; }
.grass.tl { top: 0; left: 0; border-radius: 1.25rem 0 0 0; }
.grass.tr { top: 0; right: 0; border-radius: 0 1.25rem 0 0; }
.grass.bl { bottom: 0; left: 0; border-radius: 0 0 0 1.25rem; }
.grass.br { bottom: 0; right: 0; border-radius: 0 0 1.25rem 0; }

.road-h {
    position: absolute; left: 0; right: 0; top: 150px; height: 140px;
    background: #374151; z-index: 2;
}
.road-v {
    position: absolute; top: 0; bottom: 0; left: 150px; width: 140px;
    background: #374151; z-index: 2;
}
.dark .road-h, .dark .road-v { background: #1f2937; }

.dash-h {
    position: absolute; top: 219px; left: 0; right: 0; height: 2px; z-index: 3;
    background-image: repeating-linear-gradient(to right, #fef08a 0 12px, transparent 12px 24px);
}
.dash-v {
    position: absolute; left: 219px; top: 0; bottom: 0; width: 2px; z-index: 3;
    background-image: repeating-linear-gradient(to bottom, #fef08a 0 12px, transparent 12px 24px);
}

.road-label {
    position: absolute; z-index: 4;
    font-size: 0.7rem; font-weight: 900; letter-spacing: 0.2em;
    color: rgba(255,255,255,0.6);
}
.road-label.a { left: 12px; top: 213px; }
.road-label.b { left: 198px; top: 10px; }

/* Center island */
.center-island {
    position: absolute;
    left: 170px; top: 170px;
    width: 100px; height: 100px;
    background: #1f2937; border: 3px solid #374151;
    border-radius: 50%; z-index: 5;
    box-shadow: 0 0 0 4px rgba(0,0,0,0.15);
}

/* Traffic lights */
.tl-light {
    position: absolute;
    z-index: 10;
    display: flex;
    gap: 4px;
    padding: 4px;
    background: #111827;
    border: 1.5px solid #374151;
    border-radius: 6px;
    box-shadow: 0 3px 8px rgba(0,0,0,0.5);
}
.tl-light.a { flex-direction: row; left: 100px; top: 100px; }       /* governs Road A (horizontal) */
.tl-light.b { flex-direction: column; right: 100px; bottom: 100px; } /* governs Road B (vertical) */

.tl-bulb {
    width: 16px; height: 16px; border-radius: 50%;
    border: 1.5px solid #000; opacity: 0.2;
    transition: all 0.25s ease;
    box-shadow: inset 0 1px 2px rgba(0,0,0,0.6);
}
.tl-bulb.red    { background: #991b1b; }
.tl-bulb.yellow { background: #78350f; }
.tl-bulb.green  { background: #14532d; }
.tl-bulb.red.on   { background: #ef4444; opacity: 1; box-shadow: 0 0 8px 2px rgba(239,68,68,0.7); }
.tl-bulb.green.on { background: #22c55e; opacity: 1; box-shadow: 0 0 8px 2px rgba(34,197,94,0.7); }

/* ================ ROAD COUNT CARDS ================ */
.st-cards {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
    width: 100%;
    max-width: 440px;
}
.st-card {
    background: #fff;
    border: 1.5px solid #e5e7eb;
    border-radius: 1rem;
    padding: 1rem 1.25rem;
    text-align: center;
    box-shadow: 0 2px 12px rgba(0,0,0,0.06);
    transition: all 0.3s;
}
.dark .st-card { background: #18181b; border-color: #27272a; }
.st-card.go   { border-color: #22c55e; }
.st-card.stop { border-color: #ef4444; }

.st-card-top { display: flex; align-items: center; justify-content: space-between; }
.st-card-name { font-size: 0.8rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: #6b7280; }
.dark .st-card-name { color: #a1a1aa; }
.st-signal { font-size: 0.7rem; font-weight: 800; padding: 0.15rem 0.55rem; border-radius: 999px; }
.st-signal.go   { background: #dcfce7; color: #16a34a; }
.st-signal.stop { background: #fee2e2; color: #dc2626; }
.dark .st-signal.go   { background: rgba(20,83,45,0.5); color: #4ade80; }
.dark .st-signal.stop { background: rgba(127,29,29,0.5); color: #f87171; }

.st-card-count { font-size: 2.5rem; font-weight: 900; color: #111827; margin: 0.25rem 0 0; }
.dark .st-card-count { color: #f4f4f5; }
.st-card-sub { font-size: 0.7rem; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.05em; }
</style>
</x-filament-panels::page>
