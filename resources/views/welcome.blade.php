<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Smart City') }} - Smart City Solutions</title>

    <!-- SEO Meta Tags -->
    <meta name="description"
        content="Comprehensive smart city solutions including smart farm, parking, traffic, lighting, fire alarm, and tank management systems.">
    <meta name="keywords"
        content="smart city, IoT, smart farm, smart parking, smart traffic, smart lighting, fire alarm, smart tank">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --hero-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
            --bg-light: #f8fafc;
            --bg-dark: #0f172a;
            --text-light: #1e293b;
            --text-dark: #f1f5f9;
            --card-light: #ffffff;
            --card-dark: #1e293b;
            --border-light: rgba(148, 163, 184, 0.2);
            --border-dark: rgba(148, 163, 184, 0.1);
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-light);
            color: var(--text-light);
            line-height: 1.6;
            overflow-x: hidden;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        /* Dark mode class overrides */
        body.dark-mode {
            background-color: var(--bg-dark);
            color: var(--text-dark);
        }

        /* System preference dark mode (only if no manual override) */
        @media (prefers-color-scheme: dark) {
            body:not(.light-mode) {
                background-color: var(--bg-dark);
                color: var(--text-dark);
            }
        }

        /* Navbar Styles */
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.95) 0%, rgba(118, 75, 162, 0.95) 100%);
            backdrop-filter: blur(10px);
            padding: 1rem 2rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .navbar-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar-logo {
            font-size: 1.5rem;
            font-weight: 800;
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .logo-icon {
            width: 32px;
            height: 32px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }

        .navbar-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .theme-toggle {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            width: 44px;
            height: 44px;
            border-radius: 12px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(10px);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 1.2rem;
        }

        .theme-toggle:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: scale(1.05) rotate(15deg);
        }

        .admin-btn {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            border: 2px solid rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(10px);
        }

        .admin-btn:hover {
            background: white;
            color: #667eea;
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.2);
        }

        /* Hero Section */
        .hero {
            margin-top: 80px;
            padding: 6rem 2rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
            background-size: 200% 200%;
            animation: gradientShift 15s ease infinite;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        @keyframes gradientShift {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(circle at 30% 50%, rgba(255, 255, 255, 0.1) 0%, transparent 50%);
            animation: pulse 8s ease-in-out infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 0.5;
            }

            50% {
                opacity: 1;
            }
        }

        .hero-content {
            position: relative;
            z-index: 1;
            max-width: 900px;
            margin: 0 auto;
        }

        .hero h1 {
            font-size: 3.5rem;
            font-weight: 800;
            color: white;
            margin-bottom: 1.5rem;
            line-height: 1.2;
            text-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            animation: fadeInUp 0.8s ease-out;
        }

        .hero p {
            font-size: 1.25rem;
            color: rgba(255, 255, 255, 0.95);
            margin-bottom: 2rem;
            animation: fadeInUp 0.8s ease-out 0.2s backwards;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Features Section */
        .features {
            padding: 5rem 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .section-title {
            text-align: center;
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 3rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 2rem;
            animation: fadeIn 0.8s ease-out 0.4s backwards;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .feature-card {
            background: var(--card-light);
            border-radius: 20px;
            padding: 2rem;
            transition: all 0.3s ease;
            border: 1px solid var(--border-light);
            position: relative;
            overflow: hidden;
            cursor: pointer;
        }

        body.dark-mode .feature-card {
            background: var(--card-dark);
            border-color: var(--border-dark);
        }

        @media (prefers-color-scheme: dark) {
            body:not(.light-mode) .feature-card {
                background: var(--card-dark);
                border-color: var(--border-dark);
            }
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--card-gradient);
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }

        .feature-card:hover::before {
            transform: scaleX(1);
        }

        .feature-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        .feature-icon {
            width: 80px;
            height: 80px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
            font-size: 2.5rem;
            transition: all 0.3s ease;
            background: var(--card-gradient);
            color: white;
            box-shadow: 0 10px 15px -3px var(--shadow-color);
        }

        .feature-card:hover .feature-icon {
            transform: scale(1.1) rotate(5deg);
        }

        .feature-card h3 {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.75rem;
            color: var(--text-light);
        }

        body.dark-mode .feature-card h3 {
            color: var(--text-dark);
        }

        @media (prefers-color-scheme: dark) {
            body:not(.light-mode) .feature-card h3 {

                color: var(--text-dark);
            }
        }

        .feature-card p {
            color: #64748b;
            line-height: 1.6;
        }

        body.dark-mode .feature-card p {
            color: #94a3b8;
        }

        @media (prefers-color-scheme: dark) {
            body:not(.light-mode) .feature-card p {
                color: #94a3b8;
            }
        }

        /* Individual card color themes */
        .feature-farm {
            --card-gradient: linear-gradient(135deg, #10b981 0%, #059669 100%);
            --shadow-color: rgba(16, 185, 129, 0.4);
        }

        .feature-parking {
            --card-gradient: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            --shadow-color: rgba(59, 130, 246, 0.4);
        }

        .feature-traffic {
            --card-gradient: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
            --shadow-color: rgba(249, 115, 22, 0.4);
        }

        .feature-lighting {
            --card-gradient: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
            --shadow-color: rgba(251, 191, 36, 0.4);
        }

        .feature-fire {
            --card-gradient: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            --shadow-color: rgba(239, 68, 68, 0.4);
        }

        .feature-tank {
            --card-gradient: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
            --shadow-color: rgba(6, 182, 212, 0.4);
        }

        /* Footer */
        .footer {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            color: white;
            padding: 2rem;
            text-align: center;
            margin-top: 3rem;
        }

        .footer p {
            color: rgba(255, 255, 255, 0.7);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2.5rem;
            }

            .hero p {
                font-size: 1.1rem;
            }

            .section-title {
                font-size: 2rem;
            }

            .features-grid {
                grid-template-columns: 1fr;
            }

            .navbar {
                padding: 1rem;
            }

            .navbar-logo {
                font-size: 1.25rem;
            }

            .admin-btn {
                padding: 0.6rem 1rem;
                font-size: 0.9rem;
            }
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="navbar-container">
            <a href="/" class="navbar-logo">
                <span class="logo-icon">🏙️</span>
                <span>Smart City</span>
            </a>
            <div class="navbar-actions">
                <button class="theme-toggle" id="themeToggle" aria-label="Toggle theme">
                    <span id="themeIcon">🌙</span>
                </button>
                <a href="{{ url('/admin') }}" class="admin-btn">
                    Admin Panel
                </a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h1>Smart City Solutions</h1>
            <p>Transforming urban life with innovative IoT technology and intelligent management systems for a
                sustainable future</p>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features">
        <h2 class="section-title">Our Smart Solutions</h2>
        <div class="features-grid">
            <!-- Smart Farm -->
            <div class="feature-card feature-farm">
                <div class="feature-icon">🌾</div>
                <h3>Smart Farm</h3>
                <p>Advanced agricultural monitoring and automation system with real-time crop health tracking, soil
                    analysis, and automated irrigation control for optimal yield.</p>
            </div>

            <!-- Smart Parking -->
            <div class="feature-card feature-parking">
                <div class="feature-icon">🅿️</div>
                <h3>Smart Parking</h3>
                <p>Intelligent parking management with real-time space detection, automated guidance systems, and
                    seamless payment integration for hassle-free parking.</p>
            </div>

            <!-- Smart Traffic -->
            <div class="feature-card feature-traffic">
                <div class="feature-icon">🚦</div>
                <h3>Smart Traffic</h3>
                <p>AI-powered traffic flow optimization, adaptive signal control, and congestion prediction to reduce
                    travel time and improve urban mobility.</p>
            </div>

            <!-- Smart Lighting -->
            <div class="feature-card feature-lighting">
                <div class="feature-icon">💡</div>
                <h3>Smart Lighting</h3>
                <p>Energy-efficient street lighting with motion sensors, automatic brightness adjustment, and scheduled
                    operation for significant cost savings.</p>
            </div>

            <!-- Fire Alarm -->
            <div class="feature-card feature-fire">
                <div class="feature-icon">🚨</div>
                <h3>Fire Alarm</h3>
                <p>Advanced fire detection and alert system with multi-sensor technology, instant notifications, and
                    integrated emergency response protocols.</p>
            </div>

            <!-- Smart Tank -->
            <div class="feature-card feature-tank">
                <div class="feature-icon">💧</div>
                <h3>Smart Tank</h3>
                <p>Intelligent water management with level monitoring, automated filling control, quality sensors, and
                    consumption analytics for efficient resource usage.</p>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; {{ date('Y') }} {{ config('app.name', 'Smart City') }}. All rights reserved.</p>
    </footer>

    <!-- Theme Toggle Script -->
    <script>
        // Get theme elements
        const themeToggle = document.getElementById('themeToggle');
        const themeIcon = document.getElementById('themeIcon');
        const body = document.body;

        // Check for saved theme preference or default to system preference
        function getInitialTheme() {
            const savedTheme = localStorage.getItem('theme');
            if (savedTheme) {
                return savedTheme;
            }
            // Check system preference
            if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
                return 'dark';
            }
            return 'light';
        }

        // Apply theme
        function applyTheme(theme) {
            if (theme === 'dark') {
                body.classList.add('dark-mode');
                body.classList.remove('light-mode');
                themeIcon.textContent = '☀️';
            } else {
                body.classList.add('light-mode');
                body.classList.remove('dark-mode');
                themeIcon.textContent = '🌙';
            }
            localStorage.setItem('theme', theme);
        }

        // Initialize theme on page load
        const initialTheme = getInitialTheme();
        applyTheme(initialTheme);

        // Toggle theme on button click
        themeToggle.addEventListener('click', () => {
            const currentTheme = body.classList.contains('dark-mode') ? 'dark' : 'light';
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            applyTheme(newTheme);
        });

        // Listen for system theme changes (only if no manual override)
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
            if (!localStorage.getItem('theme')) {
                applyTheme(e.matches ? 'dark' : 'light');
            }
        });
    </script>
</body>

</html>