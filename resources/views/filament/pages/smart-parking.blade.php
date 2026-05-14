<x-filament-panels::page>
    <div wire:poll.1s="refreshSlots" class="smart-parking-container">

        {{-- Stats Overview --}}
        <div class="stats-grid">
            {{-- Total Slots --}}
            <div class="stat-card">
                <div class="icon-wrapper bg-blue">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" />
                    </svg>
                </div>
                <div>
                    <p class="stat-label">Total Slots</p>
                    <h3 class="stat-value">{{ $totalSlots }}</h3>
                </div>
            </div>

            {{-- Available --}}
            <div class="stat-card">
                <div class="icon-wrapper bg-green">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                </div>
                <div>
                    <p class="stat-label">Available</p>
                    <h3 class="stat-value">{{ $availableSlots }}</h3>
                </div>
            </div>

            {{-- Occupied --}}
            <div class="stat-card">
                <div class="icon-wrapper bg-red">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" />
                    </svg>
                </div>
                <div>
                    <p class="stat-label">Occupied</p>
                    <h3 class="stat-value">{{ $occupiedSlots }}</h3>
                </div>
            </div>

            {{-- Revenue --}}
            @can('view_smart_parking_revenue')
                <div class="stat-card">
                    <div class="icon-wrapper bg-amber">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                    </div>
                    <div>
                        <p class="stat-label">Today's Revenue</p>
                        <h3 class="stat-value">${{ number_format($totalRevenue, 2) }}</h3>
                    </div>
                </div>
            @endcan
        </div>

        {{-- Parking Lot Grid --}}
        @foreach($parkingSlots as $area => $areaSlots)
            <div class="area-section">
                <div class="area-header">
                    Area {{ $area }}
                </div>

                <div class="slots-grid">
                    @foreach($areaSlots as $slot)
                        <div class="slot-card {{ $slot['status'] }} {{ $slot['status'] !== 'available' && optional($slot['active_reservation'] ?? null)['user_id'] === auth()->id() ? 'my-reservation' : '' }}"
                            @if($slot['status'] === 'available')
                            wire:click="mountAction('reserveSlot', { slot_id: {{ $slot['id'] }} })" @elseif($slot['status'] !== 'available' && optional($slot['active_reservation'] ?? null)['user_id'] === auth()->id())
                            wire:click="mountAction('releaseSlot', { slot_id: {{ $slot['id'] }} })" @endif>
                            {{-- Slot Number --}}
                            <div class="slot-number">
                                {{ $area }}-{{ $slot['slot_number'] }}
                            </div>

                            {{-- Lane Marking --}}
                            <div class="lane-marking"></div>

                            {{-- Content --}}
                            <div class="slot-content">
                                @if($slot['status'] === 'available')
                                    <div class="reserve-overlay">
                                        <div class="icon-circle">
                                            <x-heroicon-o-plus class="w-6 h-6" />
                                        </div>
                                        <span class="reserve-text">Reserve</span>
                                    </div>
                                    <div class="parking-placeholder">
                                        <span>P</span>
                                    </div>
                                @else
                                    <div class="occupied-icon">
                                        <x-heroicon-s-truck class="w-20 h-20 icon" />
                                        @if($slot['status'] === 'reserved')
                                            <div class="status-indicator"></div>
                                        @endif
                                    </div>

                                    <div class="slot-info">
                                        <span class="status-badge {{ $slot['status'] }}">
                                            {{ $slot['status'] === 'reserved' ? 'Reserved' : 'Occupied' }}
                                        </span>
                                        @if(isset($slot['active_reservation']))
                                            <p class="user-name">
                                                {{ $slot['active_reservation']['user']['name'] ?? 'Guest' }}
                                            </p>
                                            <p class="time-sw">
                                                Since
                                                {{ \Carbon\Carbon::parse($slot['active_reservation']['start_time'])->format('H:i') }}
                                            </p>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach

        <x-filament-actions::modals />
    </div>

    <style>
        .smart-parking-container {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(1, 1fr);
            gap: 1rem;
        }

        @media (min-width: 768px) {
            .stats-grid {
                grid-template-columns: repeat(4, 1fr);
            }
        }

        /* Stat Card */
        .stat-card {
            background-color: white;
            padding: 1rem 1.25rem;
            border-radius: 1rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
            border: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .dark .stat-card {
            background-color: #18181b;
            /* zinc-900 */
            border-color: #27272a;
            /* zinc-800 */
        }

        .icon-wrapper {
            padding: 0.75rem;
            border-radius: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .icon-wrapper svg {
            width: 2rem;
            height: 2rem;
            stroke: currentColor;
            stroke-width: 1.5;
        }

        .icon-wrapper.bg-blue {
            background-color: #dbeafe;
            color: #2563eb;
        }

        .icon-wrapper.bg-green {
            background-color: #dcfce7;
            color: #16a34a;
        }

        .icon-wrapper.bg-red {
            background-color: #fee2e2;
            color: #dc2626;
        }

        .icon-wrapper.bg-amber {
            background-color: #fef3c7;
            color: #d97706;
        }

        .dark .icon-wrapper.bg-blue {
            background-color: rgba(30, 58, 138, 0.3);
            color: #60a5fa;
        }

        .dark .icon-wrapper.bg-green {
            background-color: rgba(20, 83, 45, 0.3);
            color: #4ade80;
        }

        .dark .icon-wrapper.bg-red {
            background-color: rgba(127, 29, 29, 0.3);
            color: #f87171;
        }

        .dark .icon-wrapper.bg-amber {
            background-color: rgba(120, 53, 15, 0.3);
            color: #fbbf24;
        }

        .stat-label {
            font-size: 0.875rem;
            font-weight: 500;
            color: #6b7280;
        }

        .dark .stat-label {
            color: #9ca3af;
        }

        .stat-value {
            font-size: 1.5rem;
            /* 24px */
            font-weight: 700;
            color: #111827;
            margin: 0;
        }

        .dark .stat-value {
            color: white;
        }

        /* Area Section */
        .area-section {
            background-color: #f3f4f6;
            /* gray-100 */
            padding: 1rem 1.25rem 1.25rem;
            border-radius: 1.25rem;
            border: 1px solid #e5e7eb;
            position: relative;
        }

        .dark .area-section {
            background-color: rgba(24, 24, 27, 0.5);
            /* zinc-900/50 */
            border-color: #27272a;
        }

        .area-header {
            position: absolute;
            top: 0;
            left: 0;
            padding: 0.5rem 1.5rem;
            background-color: #1f2937;
            /* gray-800 */
            color: white;
            border-bottom-right-radius: 1rem;
            border-top-left-radius: 1.5rem;
            /* Match container */
            font-weight: 700;
            font-size: 1.125rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .slots-grid {
            margin-top: 1.5rem;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
        }

        @media (min-width: 768px) {
            .slots-grid {
                grid-template-columns: repeat(4, 1fr);
            }
        }

        /* Slot Card */
        .slot-card {
            position: relative;
            height: 12rem;
            border-radius: 0.75rem;
            border: 2px dashed;
            transition: all 0.3s ease;
            cursor: default;
        }

        .slot-card.available {
            border-color: #d1d5db;
            /* gray-300 */
            background-color: rgba(255, 255, 255, 0.5);
            cursor: pointer;
        }

        .dark .slot-card.available {
            border-color: #374151;
            /* gray-700 */
            background-color: rgba(31, 41, 55, 0.5);
        }

        .slot-card.available:hover {
            border-color: #22c55e;
            /* green-500 */
            box-shadow: 0 10px 15px -3px rgba(34, 197, 94, 0.1);
        }

        .slot-card.occupied,
        .slot-card.reserved {
            border-color: rgba(239, 68, 68, 0.5);
            /* red-500/50 */
            background-color: rgba(254, 242, 242, 0.5);
            /* red-50/50 */
        }

        .dark .slot-card.occupied,
        .dark .slot-card.reserved {
            background-color: rgba(127, 29, 29, 0.1);
        }

        .slot-card.my-reservation {
            cursor: pointer;
            border-color: #f59e0b;
            /* amber-500 */
            background-color: rgba(254, 251, 235, 0.8);
        }

        .slot-number {
            position: absolute;
            top: 0.5rem;
            left: 50%;
            transform: translateX(-50%);
            color: #9ca3af;
            font-family: monospace;
            font-size: 0.875rem;
        }

        .dark .slot-number {
            color: #4b5563;
        }

        .lane-marking {
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 75%;
            height: 0.25rem;
            background-color: #facc15;
            /* yellow-400 */
            opacity: 0.3;
            border-radius: 9999px;
        }

        .slot-content {
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding-top: 1rem;
        }

        /* Available State content */
        .reserve-overlay {
            opacity: 0;
            transition: opacity 0.3s;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .slot-card.available:hover .reserve-overlay {
            opacity: 1;
        }

        .icon-circle {
            padding: 0.5rem;
            background-color: #dcfce7;
            color: #16a34a;
            border-radius: 9999px;
            margin-bottom: 0.5rem;
        }

        .reserve-text {
            font-size: 0.875rem;
            font-weight: 600;
            color: #16a34a;
        }

        .parking-placeholder {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0.2;
            transition: opacity 0.3s;
        }

        .slot-card.available:hover .parking-placeholder {
            opacity: 0;
        }

        .parking-placeholder span {
            font-size: 2.25rem;
            font-weight: 700;
            color: #d1d5db;
        }

        .dark .parking-placeholder span {
            color: #4b5563;
        }

        .dark .icon-circle {
            background-color: rgba(20, 83, 45, 0.5);
            color: #4ade80;
        }

        .dark .reserve-text {
            color: #4ade80;
        }

        .dark .slot-card.my-reservation {
            background-color: rgba(120, 53, 15, 0.3);
            border-color: #d97706;
        }

        /* Occupied State content */
        .occupied-icon {
            position: relative;
        }

        .occupied-icon .icon {
            color: #ef4444;
            /* red-500 */
            filter: drop-shadow(0 20px 13px rgba(0, 0, 0, 0.03)) drop-shadow(0 8px 5px rgba(0, 0, 0, 0.08));
            transition: transform 0.3s;
        }

        .slot-card:hover .occupied-icon .icon {
            transform: scale(1.05);
        }

        .status-indicator {
            position: absolute;
            top: -0.5rem;
            right: -0.5rem;
            width: 1rem;
            height: 1rem;
            background-color: #facc15;
            border-radius: 9999px;
            border: 2px solid white;
        }

        .slot-info {
            margin-top: 0.75rem;
            text-align: center;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.125rem 0.625rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .status-badge.reserved {
            background-color: #fee2e2;
            color: #991b1b;
        }

        /* red-100 red-800 */
        .status-badge.occupied {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .dark .status-badge {
            background-color: #7f1d1d;
            color: #fecaca;
        }

        .user-name {
            font-size: 0.75rem;
            color: #6b7280;
            margin-top: 0.25rem;
            font-weight: 500;
        }

        .time-sw {
            font-size: 0.625rem;
            /* 10px */
            color: #9ca3af;
        }

        .dark .user-name {
            color: #9ca3af;
        }

        .dark .time-sw {
            color: #6b7280;
        }

        .dark .occupied-icon .icon {
            color: #f87171;
        }
    </style>
</x-filament-panels::page>