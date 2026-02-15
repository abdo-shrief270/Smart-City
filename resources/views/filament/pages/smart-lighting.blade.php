<x-filament-panels::page>
    <div wire:poll.2s="refreshData" class="smart-lighting-container">
        
        {{-- Header / Mode Switch --}}
        <div class="control-header">
            <div class="mode-status">
                <span class="label">Current Mode:</span>
                <span class="value {{ $mode }}">{{ ucfirst($mode) }}</span>
            </div>
            
            <button wire:click="toggleMode" class="mode-toggle-btn {{ $mode }}">
                Switch to {{ $mode === 'manual' ? 'Auto' : 'Manual' }}
            </button>
        </div>

        {{-- Light Tree Container --}}
        <div class="light-tree-wrapper">
            <div class="tree-structure">
                {{-- Central Pole --}}
                <div class="pole"></div>
                
                {{-- Branches & Lights --}}
                <div class="branches">
                    @foreach($lights as $index => $isOn)
                        <div class="branch-level level-{{ ceil(($index + 1) / 2) }}">
                            <div class="light-node {{ $index % 2 == 0 ? 'left' : 'right' }}">
                                <div class="branch-arm"></div>
                                <button 
                                    wire:click="toggleLight({{ $index }})"
                                    class="light-bulb {{ $isOn ? 'on' : 'off' }} {{ $mode === 'auto' ? 'disabled' : '' }}"
                                    title="Light {{ $index + 1 }}"
                                >
                                    <x-heroicon-s-light-bulb class="w-8 h-8 icon" />
                                    <span class="light-number">{{ $index + 1 }}</span>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

    </div>

    <style>
        .smart-lighting-container {
            display: flex;
            flex-direction: column;
            gap: 2rem;
            align-items: center;
        }

        /* Header Controls */
        .control-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            max-width: 600px;
            padding: 1.5rem;
            background-color: white;
            border-radius: 1rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            border: 1px solid #e5e7eb;
        }

        .dark .control-header {
            background-color: #18181b; /* zinc-900 */
            border-color: #27272a;
        }

        .mode-status {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 1.125rem;
            font-weight: 600;
        }

        .mode-status .value.manual { color: #3b82f6; } /* blue-500 */
        .mode-status .value.auto { color: #10b981; } /* green-500 */

        .mode-toggle-btn {
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            font-weight: 600;
            transition: all 0.2s;
            cursor: pointer;
        }

        .mode-toggle-btn.manual { /* Currently manual, button switches to auto */
            background-color: #dcfce7;
            color: #16a34a;
        }
        .mode-toggle-btn.manual:hover { background-color: #bbf7d0; }

        .mode-toggle-btn.auto { /* Currently auto, button switches to manual */
            background-color: #dbeafe;
            color: #2563eb;
        }
        .mode-toggle-btn.auto:hover { background-color: #bfdbfe; }
        
        .dark .mode-toggle-btn.manual { background-color: rgba(22, 163, 74, 0.2); color: #4ade80; }
        .dark .mode-toggle-btn.auto { background-color: rgba(37, 99, 235, 0.2); color: #60a5fa; }

        /* Tree Structure */
        .light-tree-wrapper {
            position: relative;
            padding: 2rem;
            background: linear-gradient(to bottom, #f3f4f6, #e5e7eb);
            border-radius: 2rem;
            width: 100%;
            max-width: 500px;
            min-height: 600px;
            display: flex;
            justify-content: center;
        }

        .dark .light-tree-wrapper {
            background: linear-gradient(to bottom, #27272a, #18181b);
        }

        .tree-structure {
            position: relative;
            width: 100%;
            max-width: 300px;
            display: flex;
            justify-content: center;
        }

        .pole {
            position: absolute;
            top: 20px;
            bottom: 0;
            width: 12px;
            background-color: #4b5563; /* gray-600 */
            border-radius: 6px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1;
        }
        
        .branches {
            display: flex;
            flex-direction: column;
            width: 100%;
            gap: 4rem; /* Spacing between levels */
            padding-top: 3rem;
            z-index: 2;
        }

        .branch-level {
            display: flex;
            justify-content: center;
            position: relative;
            height: 60px;
        }

        .light-node {
            position: absolute;
            display: flex;
            align-items: center;
        }

        .light-node.left {
            left: 0;
            right: 50%;
            justify-content: flex-start;
            padding-right: 6px; /* half pole width */
        }

        .light-node.right {
            left: 50%;
            right: 0;
            justify-content: flex-end;
            padding-left: 6px; /* half pole width */
        }

        .branch-arm {
            height: 8px;
            background-color: #4b5563;
            width: 60px; /* arm length */
            border-radius: 4px;
        }
        
        .light-node.left .branch-arm {
            border-top-left-radius: 100%; /* Curve effect */
            transform: rotate(-10deg) translateY(5px);
            margin-left: auto;
            margin-right: -5px; /* Connect to pole */
        }

        .light-node.right .branch-arm {
            border-top-right-radius: 100%; /* Curve effect */
            transform: rotate(10deg) translateY(5px);
            margin-right: auto;
            margin-left: -5px; /* Connect to pole */
        }

        /* Light Bulb Button */
        .light-bulb {
            width: 4rem;
            height: 4rem;
            border-radius: 50%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background-color: #e5e7eb; /* Off state */
            color: #9ca3af;
            border: 4px solid #d1d5db;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            position: relative;
            z-index: 10;
        }

        .light-node.left .light-bulb { margin-right: 10px; }
        .light-node.right .light-bulb { margin-left: 10px; }

        .light-bulb.on {
            background-color: #fef08a; /* yellow-200 */
            color: #ca8a04; /* yellow-600 */
            border-color: #facc15; /* yellow-400 */
            box-shadow: 0 0 20px 5px rgba(250, 204, 21, 0.5); /* Glow effect */
        }

        .light-bulb:hover:not(.disabled) {
            transform: scale(1.1);
        }

        .light-bulb.disabled {
            cursor: not-allowed;
            opacity: 0.8;
        }
        
        /* Adjust positioning for levels to create a tree shape if desired, 
           or keep them uniform. Here we stagger them slightly? 
           Let's keep them uniform for a clean look, but maybe widen the arms for lower levels if we want a christmas tree shape.
           For now, uniform arms.
        */

        .light-number {
            font-size: 0.75rem;
            font-weight: 700;
            margin-top: -2px;
        }

        .dark .pole, .dark .branch-arm { background-color: #71717a; } /* zinc-500 */
        .dark .light-bulb { background-color: #27272a; border-color: #3f3f46; color: #52525b; }
        .dark .light-bulb.on { 
            background-color: #fef08a; 
            color: #ca8a04; 
            border-color: #facc15;
            box-shadow: 0 0 25px 8px rgba(250, 204, 21, 0.3);
        }

    </style>
</x-filament-panels::page>
