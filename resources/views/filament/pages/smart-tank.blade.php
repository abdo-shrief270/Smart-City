<x-filament-panels::page>
    <div wire:poll.3s="fetchData" class="tank-container">

        {{-- Header Stats --}}
        <div class="stats-grid">
            {{-- Status Card --}}
            <div class="stat-card group">
                <div class="card-bg-gradient"></div>
                <div class="icon-box 
                    @if($status === 'Normal') bg-green
                    @elseif($status === 'Low') bg-yellow
                    @else bg-red @endif">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                    </svg>
                </div>
                <div>
                    <p class="stat-label">System Status</p>
                    <h3 class="stat-value">{{ $status }}</h3>
                </div>
            </div>

            {{-- Capacity Card --}}
            <div class="stat-card group">
                <div class="card-bg-gradient blue"></div>
                <div class="icon-box bg-blue">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9.75 3.104v5.714a2.25 2.25 0 0 1-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 0 1 4.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l-1.57.393A9.065 9.065 0 0 1 12 15a9.065 9.065 0 0 0-6.23-.693L5 14.5m14.8.8 1.402 1.402c1.232 1.232.65 3.318-1.067 3.611l-.997.17a9.035 9.035 0 0 1-9.436-5.102l-.206-.489a9.035 9.035 0 0 0-3.242-3.786L5 10.5V4.25a2.25 2.25 0 0 1 2.25-2.25h9.5A2.25 2.25 0 0 1 19 4.25V8" />
                    </svg>
                </div>
                <div>
                    <p class="stat-label">Total Capacity</p>
                    <h3 class="stat-value">1000 L</h3>
                </div>
            </div>

            {{-- Last Updated --}}
            <div class="stat-card group">
                <div class="card-bg-gradient purple"></div>
                <div class="icon-box bg-purple">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                </div>
                <div>
                    <p class="stat-label">Last Updated</p>
                    <h3 class="stat-value">{{ $lastUpdated }}</h3>
                </div>
            </div>
        </div>

        <div class="main-content-grid">
            {{-- Main Gauge --}}
            <div class="gauge-card">
                <h2 class="gauge-title">
                    Live Water Level
                </h2>

                <div class="gauge-wrapper">
                    <div class="gauge-outer-ring"></div>
                    <div class="gauge-inner">
                        <div class="wave-container" style="height: {{ $level }}%;">
                            <div class="wave wave-back"></div>
                            <div class="wave wave-front"></div>
                        </div>

                        {{-- Bubbles --}}
                        <div class="bubbles-container">
                            <div class="bubble w-2 h-2 left-1/4"></div>
                            <div class="bubble w-3 h-3 left-1/2 delay-1000"></div>
                            <div class="bubble w-1 h-1 left-3/4 delay-500"></div>
                        </div>
                    </div>

                    <div class="gauge-text">
                        <span class="gauge-percent">
                            {{ $level }}<span class="percent-symbol">%</span>
                        </span>
                        <span class="gauge-volume">Volume: {{ $level * 10 }}L</span>
                    </div>
                </div>
            </div>

            {{-- Control & History Mockup --}}
            <div class="controls-column">
                {{-- Pump Control --}}
                <div class="control-card">
                    <h3 class="card-heading">
                        Pump Control
                    </h3>
                    <div class="control-row">
                        <div class="status-group">
                            <div class="status-icon {{ $isPumpOn ? 'on' : 'off' }}">
                                <x-heroicon-s-bolt class="w-5 h-5 {{ $isPumpOn ? 'text-white' : 'text-gray-500' }}" />
                            </div>
                            <div>
                                <p class="status-text">{{ $isPumpOn ? 'Running' : 'Stopped' }}</p>
                                <p class="status-subtext">{{ $isPumpOn ? 'Active pumping' : 'System idle' }}</p>
                            </div>
                        </div>
                        <button wire:click="togglePump" class="pump-btn {{ $isPumpOn ? 'stop' : 'start' }}">
                            {{ $isPumpOn ? 'Stop Pump' : 'Start Pump' }}
                        </button>
                    </div>
                </div>

                {{-- History Mini-Chart --}}
                <div class="control-card">
                    <h3 class="card-heading">
                        Recent Activity
                    </h3>
                    <div class="chart-container">
                        @foreach($history as $val)
                            <div class="chart-bar-wrapper" style="height: {{ $val }}%;">
                                <div class="chart-bar"></div>
                                <div class="chart-tooltip">{{ $val }}%</div>
                            </div>
                        @endforeach
                    </div>
                    <div class="chart-labels">
                        <span>1m ago</span>
                        <span>Now</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .tank-container {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(1, 1fr);
            gap: 1.5rem;
        }

        @media (min-width: 768px) {
            .stats-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        .stat-card {
            padding: 1.5rem;
            background-color: white;
            border-radius: 1rem;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            border: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            gap: 1rem;
            position: relative;
            overflow: hidden;
        }

        .dark .stat-card {
            background-color: #111827;
            border-color: #374151;
        }

        .card-bg-gradient {
            position: absolute;
            right: 0;
            top: 0;
            height: 100%;
            width: 33%;
            background: linear-gradient(to left, #f9fafb, transparent);
            opacity: 0.5;
        }

        .card-bg-gradient.blue {
            background: linear-gradient(to left, #eff6ff, transparent);
        }

        .card-bg-gradient.purple {
            background: linear-gradient(to left, #faf5ff, transparent);
        }

        .dark .card-bg-gradient {
            background: linear-gradient(to left, #1f2937, transparent);
        }

        .icon-box {
            padding: 0.75rem;
            border-radius: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 3.5rem;
            min-height: 3.5rem;
        }

        .icon-box svg {
            width: 2rem;
            height: 2rem;
            stroke: currentColor;
            stroke-width: 1.5;
        }

        .icon-box.bg-green {
            background-color: #dcfce7;
            color: #16a34a;
        }

        .icon-box.bg-yellow {
            background-color: #fef9c3;
            color: #ca8a04;
        }

        .icon-box.bg-red {
            background-color: #fee2e2;
            color: #dc2626;
        }

        .icon-box.bg-blue {
            background-color: #dbeafe;
            color: #2563eb;
        }

        .icon-box.bg-purple {
            background-color: #f3e8ff;
            color: #9333ea;
        }

        .dark .icon-box.bg-green {
            background-color: rgba(6, 78, 59, 0.5);
            color: #34d399;
        }

        .dark .icon-box.bg-yellow {
            background-color: rgba(113, 63, 18, 0.5);
            color: #fbbf24;
        }

        .dark .icon-box.bg-red {
            background-color: rgba(127, 29, 29, 0.5);
            color: #f87171;
        }

        .dark .icon-box.bg-blue {
            background-color: rgba(30, 58, 138, 0.5);
            color: #60a5fa;
        }

        .dark .icon-box.bg-purple {
            background-color: rgba(88, 28, 135, 0.5);
            color: #c084fc;
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
            font-weight: 700;
            color: #111827;
            margin: 0;
        }

        .dark .stat-value {
            color: white;
        }

        /* Main Content Grid */
        .main-content-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }

        @media (min-width: 1024px) {
            .main-content-grid {
                grid-template-columns: 2fr 1fr;
            }
        }

        /* Gauge Card */
        .gauge-card {
            padding: 2rem;
            background-color: white;
            border-radius: 1rem;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            border: 1px solid #e5e7eb;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            position: relative;
            min-height: 400px;
        }

        .dark .gauge-card {
            background-color: #111827;
            border-color: #374151;
        }

        .gauge-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: #1f2937;
            position: absolute;
            top: 1.5rem;
            left: 1.5rem;
        }

        .dark .gauge-title {
            color: white;
        }

        .gauge-wrapper {
            position: relative;
            width: 18rem;
            height: 18rem;
        }

        .gauge-outer-ring {
            position: absolute;
            inset: 0;
            border-radius: 9999px;
            border: 8px solid #f3f4f6;
            box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.06);
        }

        .dark .gauge-outer-ring {
            border-color: #1f2937;
        }

        .gauge-inner {
            position: absolute;
            inset: 0.5rem;
            border-radius: 9999px;
            overflow: hidden;
            background-color: #f9fafb;
            box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.06);
            transform: translateZ(0);
            /* Fix Safari overflow */
        }

        .dark .gauge-inner {
            background-color: #1f2937;
        }

        .wave-container {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            transition: height 1s ease-in-out;
            background: linear-gradient(180deg, rgba(59, 130, 246, 0.6) 0%, rgba(37, 99, 235, 0.9) 100%);
        }

        .wave {
            position: absolute;
            left: 0;
            width: 200%;
            height: 1.5rem;
            background-repeat: repeat-x;
            border-radius: 50%;
        }

        .wave-back {
            top: -0.25rem;
            left: -50%;
            background-color: rgba(37, 99, 235, 0.3);
            animation: wave 7s linear infinite;
        }

        .wave-front {
            top: -0.75rem;
            background-color: rgba(96, 165, 250, 0.5);
            animation: wave 4s linear infinite;
        }

        .gauge-text {
            position: absolute;
            inset: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 10;
            pointer-events: none;
        }

        .gauge-percent {
            font-size: 3.75rem;
            font-weight: 900;
            color: #1f2937;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        }

        .dark .gauge-percent {
            color: white;
        }

        .percent-symbol {
            font-size: 1.875rem;
            color: #6b7280;
        }

        .gauge-volume {
            font-size: 0.875rem;
            font-weight: 500;
            color: #6b7280;
            margin-top: 0.25rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }

        /* Controls Column */
        .controls-column {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .control-card {
            padding: 1.5rem;
            background-color: white;
            border-radius: 1rem;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            border: 1px solid #e5e7eb;
        }

        .dark .control-card {
            background-color: #111827;
            border-color: #374151;
        }

        .card-heading {
            font-size: 1.125rem;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 1rem;
        }

        .dark .card-heading {
            color: white;
        }

        .control-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem;
            border-radius: 0.75rem;
            background-color: #f9fafb;
            border: 1px solid #f3f4f6;
        }

        .dark .control-row {
            background-color: #1f2937;
            border-color: #374151;
        }

        .status-group {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .status-icon {
            padding: 0.5rem;
            border-radius: 0.5rem;
        }

        .status-icon.on {
            background-color: #3b82f6;
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        .status-icon.off {
            background-color: #e5e7eb;
        }

        .dark .status-icon.off {
            background-color: #374151;
        }

        .status-text {
            font-weight: 500;
            color: #111827;
            margin: 0;
        }

        .dark .status-text {
            color: white;
        }

        .status-subtext {
            font-size: 0.75rem;
            color: #6b7280;
            margin: 0;
        }

        .pump-btn {
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            font-weight: 600;
            cursor: pointer;
            border: none;
            transition: all 0.2s;
        }

        .pump-btn.start {
            background-color: #2563eb;
            color: white;
            box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.3);
        }

        .pump-btn.start:hover {
            background-color: #1d4ed8;
        }

        .pump-btn.stop {
            background-color: #fee2e2;
            color: #dc2626;
        }

        .pump-btn.stop:hover {
            background-color: #fecaca;
        }

        .chart-container {
            height: 8rem;
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 0.25rem;
            padding: 0 0.5rem;
        }

        .chart-bar-wrapper {
            width: 100%;
            background-color: #dbeafe;
            border-top-left-radius: 2px;
            border-top-right-radius: 2px;
            position: relative;
        }

        .dark .chart-bar-wrapper {
            background-color: rgba(30, 58, 138, 0.4);
        }

        .chart-bar {
            position: absolute;
            bottom: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(59, 130, 246, 0.8);
            border-top-left-radius: 2px;
            border-top-right-radius: 2px;
        }

        .chart-tooltip {
            position: absolute;
            top: -1.5rem;
            left: 50%;
            transform: translateX(-50%);
            background-color: #1f2937;
            color: white;
            font-size: 0.75rem;
            padding: 0.125rem 0.375rem;
            border-radius: 0.25rem;
            opacity: 0;
            transition: opacity 0.2s;
            pointer-events: none;
        }

        .chart-bar-wrapper:hover .chart-tooltip {
            opacity: 1;
        }

        .chart-labels {
            display: flex;
            justify-content: space-between;
            margin-top: 0.5rem;
            font-size: 0.75rem;
            color: #9ca3af;
        }

        /* Animations */
        @keyframes wave {
            0% {
                transform: translateX(0);
            }

            100% {
                transform: translateX(-50%);
            }
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: .5;
            }
        }

        /* Bubble Animation */
        .bubbles-container {
            position: absolute;
            inset: 0;
            overflow: hidden;
            pointer-events: none;
        }

        .bubble {
            position: absolute;
            bottom: -10px;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            animation: float 4s infinite ease-in;
        }

        .delay-500 {
            animation-delay: 0.5s;
        }

        .delay-1000 {
            animation-delay: 1s;
        }

        @keyframes float {
            0% {
                transform: translateY(100%);
                opacity: 0;
            }

            50% {
                opacity: 0.5;
            }

            100% {
                transform: translateY(-20px);
                opacity: 0;
            }
        }
    </style>
</x-filament-panels::page>