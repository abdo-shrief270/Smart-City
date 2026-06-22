<x-filament-panels::page>
    <div wire:poll.15s="fetchData" x-data="fbWatch('SmartFarm', 'fetchData')" class="farm-grid">
        {{-- Temperature Card --}}
        <div class="farm-card">
            <div class="icon-bubble orange">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z" />
                </svg>
            </div>
            <div class="card-content">
                <h3 class="card-label">Temperature</h3>
                <p class="card-value">{{ $temp }}</p>
            </div>
            <div class="progress-bar-bg">
                <div class="progress-bar-fill orange" style="width: {{ min(round($temp / 4095 * 100), 100) }}%"></div>
            </div>
        </div>

        {{-- Soil Moisture Card --}}
        <div class="farm-card">
            <div class="icon-bubble blue">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15a4.5 4.5 0 0 0 4.5 4.5H18a3.75 3.75 0 0 0 1.332-7.257 3 3 0 0 0-3.758-3.848 5.25 5.25 0 0 0-10.233 2.33A4.502 4.502 0 0 0 2.25 15Z" />
                </svg>
            </div>
            <div class="card-content">
                <h3 class="card-label">Soil Moisture</h3>
                <p class="card-value">{{ $soil }}</p>
            </div>
            <div class="progress-bar-bg">
                <div class="progress-bar-fill blue" style="width: {{ min(round($soil / 4095 * 100), 100) }}%"></div>
            </div>
        </div>

        {{-- Rain Card --}}
        <div class="farm-card">
            <div class="icon-bubble {{ $rain ? 'blue' : 'gray' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15a4.5 4.5 0 0 0 4.5 4.5H18a3.75 3.75 0 0 0 1.332-7.257 3 3 0 0 0-3.758-3.848 5.25 5.25 0 0 0-10.233 2.33A4.502 4.502 0 0 0 2.25 15Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 19.5 7 22m5-2.5L11 22m5-2.5L15 22" />
                </svg>
            </div>
            <div class="card-content">
                <h3 class="card-label">Rain</h3>
                <p class="card-value {{ $rain ? '' : 'text-gray' }}">{{ $rain ? 'Raining' : 'Dry' }}</p>
            </div>
            <div class="action-badge-wrapper">
                <span class="action-badge {{ $rain ? 'green' : 'gray' }}">
                    {{ $rain ? 'Moisture incoming' : 'No rain detected' }}
                </span>
            </div>
        </div>

        {{-- Pump Status Card --}}
        <div wire:click="togglePump" class="farm-card interactive {{ $pump ? 'active' : '' }}">
            <div class="icon-bubble {{ $pump ? 'green' : 'gray' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m3.75 13.5 10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75Z" />
                </svg>
            </div>
            <div class="card-content">
                <h3 class="card-label">Pump Status</h3>
                <p class="card-value {{ $pump ? 'text-green' : 'text-gray' }}">
                    {{ $pump ? 'ON' : 'OFF' }}
                </p>
            </div>
            <div class="action-badge-wrapper">
                <span class="action-badge {{ $pump ? 'green' : 'gray' }}">
                    {{ $pump ? 'Click to Stop' : 'Click to Start' }}
                </span>
            </div>
        </div>
    </div>

    <style>
        .farm-grid {
            display: grid;
            grid-template-columns: repeat(1, 1fr);
            gap: 1.5rem;
        }

        @media (min-width: 640px) {
            .farm-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (min-width: 1024px) {
            .farm-grid {
                grid-template-columns: repeat(4, 1fr);
            }
        }

        .farm-card {
            background-color: white;
            border-radius: 0.75rem;
            padding: 1.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 1rem;
            transition: transform 0.2s;
        }

        .dark .farm-card {
            background-color: #1f2937;
            /* gray-800 */
        }

        .farm-card:hover {
            transform: scale(1.02);
        }

        .farm-card.interactive {
            cursor: pointer;
            border: 2px solid transparent;
        }

        .farm-card.interactive.active {
            border-color: #22c55e;
            box-shadow: 0 10px 15px -3px rgba(34, 197, 94, 0.2);
        }

        .farm-card.interactive:hover {
            border-color: #d1d5db;
        }

        .dark .farm-card.interactive:hover {
            border-color: #4b5563;
        }

        .icon-bubble {
            padding: 1rem;
            border-radius: 9999px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .icon-bubble svg {
            width: 3rem;
            height: 3rem;
            stroke: currentColor;
            stroke-width: 1.5;
        }

        .icon-bubble.orange {
            background-color: #ffedd5;
            color: #ea580c;
        }

        /* orange-100, orange-600 */
        .icon-bubble.blue {
            background-color: #dbeafe;
            color: #2563eb;
        }

        /* blue-100, blue-600 */
        .icon-bubble.green {
            background-color: #dcfce7;
            color: #16a34a;
        }

        .icon-bubble.gray {
            background-color: #f3f4f6;
            color: #6b7280;
        }

        .dark .icon-bubble.orange {
            background-color: #7c2d12;
            color: #fb923c;
        }

        .dark .icon-bubble.blue {
            background-color: #1e3a8a;
            color: #60a5fa;
        }

        .dark .icon-bubble.green {
            background-color: #14532d;
            color: #4ade80;
        }

        .dark .icon-bubble.gray {
            background-color: #374151;
            color: #9ca3af;
        }

        .card-content {
            text-align: center;
        }

        .card-label {
            font-size: 1.125rem;
            font-weight: 500;
            color: #6b7280;
        }

        .dark .card-label {
            color: #9ca3af;
        }

        .card-value {
            font-size: 2.25rem;
            font-weight: 700;
            color: #1f2937;
            margin-top: 0.5rem;
        }

        .dark .card-value {
            color: #f3f4f6;
        }

        .card-value.text-green {
            color: #16a34a;
        }

        .card-value.text-gray {
            color: #6b7280;
        }

        .progress-bar-bg {
            width: 100%;
            height: 0.625rem;
            background-color: #e5e7eb;
            border-radius: 9999px;
            margin-top: 0.5rem;
        }

        .dark .progress-bar-bg {
            background-color: #374151;
        }

        .progress-bar-fill {
            height: 100%;
            border-radius: 9999px;
            transition: width 0.5s;
        }

        .progress-bar-fill.orange {
            background-color: #f97316;
        }

        .progress-bar-fill.blue {
            background-color: #3b82f6;
        }

        .action-badge-wrapper {
            margin-top: 0.5rem;
        }

        .action-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .action-badge.green {
            background-color: #dcfce7;
            color: #15803d;
        }

        .action-badge.gray {
            background-color: #f3f4f6;
            color: #374151;
        }

        .dark .action-badge.green {
            background-color: #14532d;
            color: #86efac;
        }

        .dark .action-badge.gray {
            background-color: #374151;
            color: #d1d5db;
        }
    </style>
</x-filament-panels::page>