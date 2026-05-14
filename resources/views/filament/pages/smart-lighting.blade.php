<x-filament-panels::page>
<div wire:poll.2s="refreshData" class="sl-container">

    @php
        $byRoad = [
            'N' => [$lights[0] ?? false, $lights[1] ?? false],
            'E' => [$lights[2] ?? false, $lights[3] ?? false],
            'S' => [$lights[4] ?? false, $lights[5] ?? false],
            'W' => [$lights[6] ?? false, $lights[7] ?? false],
        ];
        $roadCount = fn ($pair) => array_sum(array_map(fn ($v) => $v ? 1 : 0, $pair));
    @endphp

    {{-- Crossroad + road status cards (cards positioned around the scene, one per direction) --}}
    <div class="sl-arena">
        @foreach(['N','E','S','W'] as $road)
            @php $onCount = $roadCount($byRoad[$road]); @endphp
            <div class="sl-road-card sl-card-{{ strtolower($road) }}">
                <div class="sl-road-title">Road {{ $road }}</div>
                <div class="sl-road-count {{ $onCount === 2 ? 'full' : ($onCount === 1 ? 'half' : 'empty') }}">
                    {{ $onCount }} / 2 on
                </div>
            </div>
        @endforeach

        {{-- Crossroad scene --}}
        <div class="sl-scene">
        {{-- HUD: mode toggle (top-left) + live count (top-right) --}}
        <button
            type="button"
            wire:click="toggleMode"
            wire:target="toggleMode"
            wire:loading.attr="disabled"
            class="sl-hud sl-hud-mode sl-{{ $mode }}"
            title="Switch to {{ $mode === 'manual' ? 'Auto' : 'Manual' }} mode"
        >
            <span class="sl-hud-dot"></span>
            <span wire:loading.remove wire:target="toggleMode">
                <strong>{{ strtoupper($mode) }}</strong>
                <span class="sl-hud-hint">→ {{ $mode === 'manual' ? 'Auto' : 'Manual' }}</span>
            </span>
            <span wire:loading wire:target="toggleMode">Switching…</span>
        </button>

        <div class="sl-hud sl-hud-count">
            <span class="sl-dot on"></span>
            <strong>{{ collect($lights)->filter()->count() }} / 8</strong>
            <span class="sl-hud-hint">ON</span>
        </div>

        {{-- Ground --}}
        <div class="sl-ground"></div>

        {{-- Roads: '+' shape --}}
        <div class="sl-road sl-road-ns"></div>
        <div class="sl-road sl-road-ew"></div>

        {{-- Lane markings --}}
        <div class="sl-lanes sl-lanes-ns"></div>
        <div class="sl-lanes sl-lanes-ew"></div>

        {{-- Compass labels --}}
        <div class="sl-compass sl-compass-n">NORTH</div>
        <div class="sl-compass sl-compass-e">EAST</div>
        <div class="sl-compass sl-compass-s">SOUTH</div>
        <div class="sl-compass sl-compass-w">WEST</div>

        @php
            // 8 lamps, two per road section. Indexes match the $lights array.
            $lamps = [
                0 => ['pos' => 'n-west',  'label' => 'N-1', 'road' => 'N'],
                1 => ['pos' => 'n-east',  'label' => 'N-2', 'road' => 'N'],
                2 => ['pos' => 'e-north', 'label' => 'E-1', 'road' => 'E'],
                3 => ['pos' => 'e-south', 'label' => 'E-2', 'road' => 'E'],
                4 => ['pos' => 's-east',  'label' => 'S-1', 'road' => 'S'],
                5 => ['pos' => 's-west',  'label' => 'S-2', 'road' => 'S'],
                6 => ['pos' => 'w-south', 'label' => 'W-1', 'road' => 'W'],
                7 => ['pos' => 'w-north', 'label' => 'W-2', 'road' => 'W'],
            ];
        @endphp

        @foreach($lamps as $index => $lamp)
            @php $isOn = $lights[$index] ?? false; @endphp
            <div class="sl-lamp sl-pos-{{ $lamp['pos'] }}">
                <button
                    wire:click="toggleLight({{ $index }})"
                    class="sl-bulb {{ $isOn ? 'on' : 'off' }} {{ $mode === 'auto' ? 'disabled' : '' }}"
                    title="Light {{ $lamp['label'] }} (Road {{ $lamp['road'] }})"
                    wire:target="toggleLight({{ $index }})"
                    wire:loading.attr="disabled"
                >
                    <x-heroicon-s-light-bulb class="sl-bulb-icon" />
                    <span class="sl-bulb-label">{{ $lamp['label'] }}</span>
                </button>
                @if($isOn)
                    <div class="sl-glow"></div>
                @endif
            </div>
        @endforeach
        </div>  {{-- /.sl-scene --}}
    </div>      {{-- /.sl-arena --}}

</div>

<style>
.sl-container {
    display: flex; flex-direction: column;
    gap: 0.5rem;
    align-items: center;
    font-family: 'Inter', sans-serif;
    max-height: calc(100dvh - 8rem);
    overflow: hidden;
}

/* ================== HUD pills overlaid on scene ================== */
.sl-hud {
    position: absolute;
    z-index: 20;
    display: flex; align-items: center; gap: 0.45rem;
    padding: 0.35rem 0.75rem;
    border-radius: 999px;
    background: rgba(255,255,255,0.92);
    border: 1px solid rgba(15,23,42,0.1);
    backdrop-filter: blur(8px);
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    font-size: 0.75rem;
    font-weight: 700;
    color: #1f2937;
    white-space: nowrap;
}
.dark .sl-hud { background: rgba(24,24,27,0.9); border-color: rgba(255,255,255,0.1); color: #e4e4e7; }

.sl-hud strong { font-weight: 800; letter-spacing: 0.04em; }
.sl-hud-hint { color: #6b7280; font-size: 0.65rem; font-weight: 600; margin-left: 0.15rem; }
.dark .sl-hud-hint { color: #a1a1aa; }

/* Mode pill (button, top-left) */
.sl-hud-mode {
    top: 0.65rem; left: 0.65rem;
    cursor: pointer; transition: transform 0.15s, box-shadow 0.15s;
}
.sl-hud-mode:hover:not(:disabled) { transform: translateY(-1px); box-shadow: 0 8px 15px rgba(0,0,0,0.15); }
.sl-hud-mode:disabled { cursor: not-allowed; opacity: 0.7; }
.sl-hud-mode.sl-manual { color: #2563eb; }
.sl-hud-mode.sl-auto   { color: #059669; }
.sl-hud-dot { width: 0.55rem; height: 0.55rem; border-radius: 50%; flex: 0 0 auto; }
.sl-hud-mode.sl-manual .sl-hud-dot { background: #3b82f6; box-shadow: 0 0 6px rgba(59,130,246,0.7); }
.sl-hud-mode.sl-auto   .sl-hud-dot { background: #10b981; box-shadow: 0 0 6px rgba(16,185,129,0.7); }

/* Count pill (top-right) */
.sl-hud-count { top: 0.65rem; right: 0.65rem; }
.sl-dot { display: inline-block; width: 0.55rem; height: 0.55rem; border-radius: 50%; background: #facc15; box-shadow: 0 0 8px rgba(250,204,21,0.7); flex: 0 0 auto; }

/* ================== CROSSROAD SCENE ================== */
.sl-scene {
    position: relative;
    width: 100%;
    max-width: min(640px, calc(100dvh - 14rem));
    aspect-ratio: 1;
    border-radius: 1.25rem;
    overflow: hidden;
    background: #dcedc8;
    box-shadow: 0 10px 25px -5px rgba(0,0,0,0.15), inset 0 0 80px rgba(0,0,0,0.05);
    margin: 0 auto;
}
.dark .sl-scene { background: #1b2e20; }

.sl-ground {
    position: absolute; inset: 0;
    background:
        radial-gradient(circle at 20% 20%, rgba(255,255,255,0.25), transparent 40%),
        repeating-linear-gradient(45deg, rgba(0,0,0,0.03), rgba(0,0,0,0.03) 2px, transparent 2px, transparent 10px);
}

/* Roads */
.sl-road {
    position: absolute;
    background: #374151;
    box-shadow: inset 0 0 0 2px #1f2937;
}
.sl-road-ns { top: 0; bottom: 0; left: 40%; width: 20%; }
.sl-road-ew { left: 0; right: 0; top: 40%; height: 20%; }
.dark .sl-road { background: #1f2937; box-shadow: inset 0 0 0 2px #0f172a; }

/* Lane markings */
.sl-lanes { position: absolute; pointer-events: none; z-index: 2; }
.sl-lanes-ns {
    top: 0; bottom: 0;
    left: 50%; width: 3px; transform: translateX(-50%);
    background: repeating-linear-gradient(to bottom, #fbbf24 0 12px, transparent 12px 24px);
}
.sl-lanes-ew {
    left: 0; right: 0;
    top: 50%; height: 3px; transform: translateY(-50%);
    background: repeating-linear-gradient(to right, #fbbf24 0 12px, transparent 12px 24px);
}

/* Compass labels */
.sl-compass {
    position: absolute;
    color: rgba(55, 65, 81, 0.55);
    font-weight: 900; font-size: 0.75rem; letter-spacing: 0.2em;
    pointer-events: none; z-index: 3;
}
.dark .sl-compass { color: rgba(228, 228, 231, 0.5); }
.sl-compass-n { top: 0.6rem;    left: 50%; transform: translateX(-50%); }
.sl-compass-s { bottom: 0.6rem; left: 50%; transform: translateX(-50%); }
.sl-compass-e { right: 0.6rem;  top: 50%;  transform: translateY(-50%) rotate(90deg); transform-origin: right center; }
.sl-compass-w { left: 0.6rem;   top: 50%;  transform: translateY(-50%) rotate(-90deg); transform-origin: left center; }

/* ================== LAMPS ================== */
.sl-lamp { position: absolute; z-index: 10; }

/* North road — both lamps on the asphalt, along the road center line */
.sl-pos-n-west { top: 12%; left: 50%; transform: translate(-50%, -50%); }
.sl-pos-n-east { top: 28%; left: 50%; transform: translate(-50%, -50%); }
/* South road — both lamps on the asphalt, along the road center line */
.sl-pos-s-west { top: 88%; left: 50%; transform: translate(-50%, -50%); }
.sl-pos-s-east { top: 72%; left: 50%; transform: translate(-50%, -50%); }
/* East road — both lamps on the asphalt, along the road center line */
.sl-pos-e-north { top: 50%; left: 72%; transform: translate(-50%, -50%); }
.sl-pos-e-south { top: 50%; left: 88%; transform: translate(-50%, -50%); }
/* West road — both lamps on the asphalt, along the road center line */
.sl-pos-w-north { top: 50%; left: 28%; transform: translate(-50%, -50%); }
.sl-pos-w-south { top: 50%; left: 12%; transform: translate(-50%, -50%); }

/* Bulb */
.sl-bulb {
    width: 4rem; height: 4rem;
    border-radius: 50%;
    display: flex; flex-direction: column; align-items: center; justify-content: center;
    background: #e5e7eb; color: #9ca3af;
    border: 3px solid #d1d5db;
    cursor: pointer; transition: all 0.3s ease;
    box-shadow: 0 4px 10px rgba(0,0,0,0.15);
    position: relative; z-index: 11;
}
.sl-bulb.on {
    background: #fef3c7; color: #b45309;
    border-color: #f59e0b;
    box-shadow:
        0 0 20px 6px rgba(251,191,36,0.6),
        0 4px 10px rgba(0,0,0,0.2);
    animation: sl-pulse 2.4s ease-in-out infinite;
}
@keyframes sl-pulse {
    0%,100% { box-shadow: 0 0 20px 6px rgba(251,191,36,0.55), 0 4px 10px rgba(0,0,0,0.2); }
    50%     { box-shadow: 0 0 32px 12px rgba(251,191,36,0.8), 0 4px 10px rgba(0,0,0,0.2); }
}
.sl-bulb:hover:not(.disabled) { transform: scale(1.08); }
.sl-bulb.disabled { cursor: not-allowed; opacity: 0.65; }
.sl-bulb-icon { width: 1.4rem; height: 1.4rem; }
.sl-bulb-label { font-size: 0.7rem; font-weight: 800; margin-top: -1px; letter-spacing: 0.05em; }

.dark .sl-bulb { background: #27272a; border-color: #3f3f46; color: #71717a; }
.dark .sl-bulb.on { background: #fde68a; color: #92400e; border-color: #f59e0b; }

/* Soft halo behind an ON lamp (simulated light cone on the road) */
.sl-glow {
    position: absolute;
    top: 50%; left: 50%;
    width: 9rem; height: 9rem;
    transform: translate(-50%, -50%);
    background: radial-gradient(circle, rgba(251,191,36,0.45) 0%, rgba(251,191,36,0) 70%);
    pointer-events: none;
    z-index: 10;
    animation: sl-halo 3s ease-in-out infinite alternate;
}
@keyframes sl-halo {
    from { opacity: 0.7; }
    to   { opacity: 1;   }
}

/* ================== ARENA (scene + 4 direction cards) ================== */
.sl-arena {
    display: grid;
    width: 100%;
    max-width: 880px;
    gap: 0.75rem;
    align-items: center;
    justify-items: center;
    grid-template-columns: minmax(120px, 160px) minmax(0, 1fr) minmax(120px, 160px);
    grid-template-rows: auto minmax(0, 1fr) auto;
    grid-template-areas:
        ".  north  ."
        "west scene east"
        ".  south  .";
}
.sl-arena .sl-scene { grid-area: scene; width: 100%; }
.sl-card-n { grid-area: north; }
.sl-card-e { grid-area: east;  }
.sl-card-s { grid-area: south; }
.sl-card-w { grid-area: west;  }

.sl-road-card {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 0.85rem;
    padding: 0.75rem 1rem;
    text-align: center;
    box-shadow: 0 4px 10px rgba(0,0,0,0.05);
    min-width: 110px;
    position: relative;
}
.dark .sl-road-card { background: #18181b; border-color: #27272a; }

/* A subtle pointer (arrow) on each card toward its matching road */
.sl-card-n::after,
.sl-card-e::after,
.sl-card-s::after,
.sl-card-w::after {
    content: "";
    position: absolute;
    width: 0; height: 0;
    border: 8px solid transparent;
}
.sl-card-n::after { bottom: -8px; left: 50%; transform: translateX(-50%); border-top-color: #e5e7eb; }
.sl-card-s::after { top: -8px;    left: 50%; transform: translateX(-50%); border-bottom-color: #e5e7eb; }
.sl-card-e::after { left: -8px;   top: 50%;  transform: translateY(-50%); border-right-color: #e5e7eb; }
.sl-card-w::after { right: -8px;  top: 50%;  transform: translateY(-50%); border-left-color: #e5e7eb; }
.dark .sl-card-n::after { border-top-color: #27272a; }
.dark .sl-card-s::after { border-bottom-color: #27272a; }
.dark .sl-card-e::after { border-right-color: #27272a; }
.dark .sl-card-w::after { border-left-color: #27272a; }

.sl-road-title { font-size: 0.7rem; font-weight: 800; color: #6b7280; letter-spacing: 0.15em; }
.sl-road-count { margin-top: 0.25rem; font-weight: 800; font-size: 0.95rem; }
.sl-road-count.full  { color: #f59e0b; }
.sl-road-count.half  { color: #3b82f6; }
.sl-road-count.empty { color: #9ca3af; }

/* On narrow screens, drop the cards into a 2×2 pack below the scene */
@media (max-width: 720px) {
    .sl-arena {
        grid-template-columns: 1fr 1fr;
        grid-template-rows: auto auto auto;
        grid-template-areas:
            "scene scene"
            "north east"
            "west  south";
    }
    .sl-road-card { width: 100%; }
    .sl-card-n::after, .sl-card-e::after,
    .sl-card-s::after, .sl-card-w::after { display: none; }
}

/* ================== RESPONSIVE ================== */
@media (max-width: 540px) {
    .sl-bulb { width: 3rem; height: 3rem; }
    .sl-bulb-icon { width: 1.1rem; height: 1.1rem; }
    .sl-bulb-label { font-size: 0.55rem; }
    .sl-glow { width: 6rem; height: 6rem; }
    .sl-compass { font-size: 0.6rem; }
    .sl-road-summary { grid-template-columns: repeat(2, 1fr); }
}
</style>
</x-filament-panels::page>
