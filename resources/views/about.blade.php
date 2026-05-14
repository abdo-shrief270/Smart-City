@php
    /*
    |--------------------------------------------------------------------------
    | Edit project info, abstract, team and supervisors below
    |--------------------------------------------------------------------------
    | Drop member photos in public/team/ and reference e.g. asset('team/ali.jpg').
    | Any member without a photo falls back to an initials avatar automatically.
    */

    $projectTitle = 'Smart City Platform';
    $projectTagline = 'An integrated IoT system for modern urban management.';

    $abstract = 'This project presents a unified Smart City platform that connects
        multiple IoT subsystems — smart farming, parking, traffic, lighting, fire alarms,
        and water-tank monitoring — into a single administration panel. Sensors publish
        live data to Firebase, which the platform reads in real time, allowing operators
        to supervise the city, intervene remotely, and collect analytics for continuous
        improvement of urban services.';

    $projectIdea = 'The idea is to demonstrate how low-cost ESP32 devices, a cloud
        realtime database, and a web dashboard can cooperate to automate essential
        city infrastructure. Each subsystem is autonomous on the edge yet fully
        observable and controllable from a central Laravel + Filament panel, giving
        a blueprint for scalable municipal IoT deployments.';

    $team = [
        [
            'name' => 'Team Member 1',
            'role' => 'Team Lead · Backend',
            'image' => null, // e.g. asset('team/member1.jpg')
        ],
        [
            'name' => 'Team Member 2',
            'role' => 'IoT / Firmware',
            'image' => null,
        ],
        [
            'name' => 'Team Member 3',
            'role' => 'Frontend · UI/UX',
            'image' => null,
        ],
        [
            'name' => 'Team Member 4',
            'role' => 'Data & Firebase',
            'image' => null,
        ],
    ];

    $supervisors = [
        [
            'name' => 'Supervisor Name',
            'role' => 'Project Supervisor',
            'image' => null,
        ],
    ];

    $avatarFor = function (array $person): string {
        if (!empty($person['image'])) {
            return $person['image'];
        }
        $initials = urlencode(collect(explode(' ', $person['name']))
            ->map(fn ($p) => mb_substr($p, 0, 1))
            ->take(2)
            ->implode(''));
        return "https://ui-avatars.com/api/?name={$initials}&size=256&background=667eea&color=fff&bold=true";
    };
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $projectTitle }} — About the Project</title>
    <meta name="description" content="{{ $projectTagline }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --bg-light: #f8fafc;
            --bg-dark: #0f172a;
            --text-light: #1e293b;
            --text-dark: #f1f5f9;
            --muted-light: #64748b;
            --muted-dark: #94a3b8;
            --card-light: #ffffff;
            --card-dark: #1e293b;
            --border-light: rgba(148, 163, 184, 0.2);
            --border-dark: rgba(148, 163, 184, 0.1);
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg-light);
            color: var(--text-light);
            line-height: 1.6;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        body.dark-mode { background: var(--bg-dark); color: var(--text-dark); }
        @media (prefers-color-scheme: dark) {
            body:not(.light-mode) { background: var(--bg-dark); color: var(--text-dark); }
        }

        /* Navbar */
        .navbar {
            position: fixed; top: 0; left: 0; right: 0; z-index: 1000;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.95), rgba(118, 75, 162, 0.95));
            backdrop-filter: blur(10px);
            padding: 1rem 2rem;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,.1);
        }
        .navbar-container {
            max-width: 1200px; margin: 0 auto;
            display: flex; justify-content: space-between; align-items: center;
        }
        .navbar-logo {
            font-size: 1.5rem; font-weight: 800; color: white; text-decoration: none;
            display: flex; align-items: center; gap: 0.5rem;
        }
        .logo-icon {
            width: 32px; height: 32px;
            background: rgba(255,255,255,0.2); border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.2rem;
        }
        .navbar-actions { display: flex; align-items: center; gap: 1rem; }
        .nav-link {
            color: white; text-decoration: none;
            padding: 0.6rem 1.1rem; border-radius: 10px;
            font-weight: 600; transition: all 0.3s ease;
            border: 2px solid rgba(255,255,255,0.3);
            background: rgba(255,255,255,0.15);
        }
        .nav-link:hover { background: white; color: #667eea; transform: translateY(-2px); }
        .theme-toggle {
            background: rgba(255,255,255,0.2); color: white;
            width: 44px; height: 44px; border-radius: 12px;
            border: 2px solid rgba(255,255,255,0.3);
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; transition: all 0.3s ease; font-size: 1.2rem;
        }
        .theme-toggle:hover { background: rgba(255,255,255,0.3); transform: scale(1.05) rotate(15deg); }

        /* Hero */
        .hero {
            margin-top: 80px; padding: 5rem 2rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
            background-size: 200% 200%;
            animation: gradientShift 15s ease infinite;
            text-align: center; position: relative; overflow: hidden; color: white;
        }
        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        .hero h1 {
            font-size: 3rem; font-weight: 800; margin-bottom: 0.75rem;
            text-shadow: 0 4px 6px rgba(0,0,0,.15);
        }
        .hero p.tagline { font-size: 1.2rem; opacity: .95; max-width: 700px; margin: 0 auto; }

        /* Section wrapper */
        .section {
            max-width: 1100px; margin: 0 auto; padding: 4rem 2rem;
        }
        .section-title {
            font-size: 2rem; font-weight: 800; margin-bottom: 1rem;
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .section-kicker {
            font-size: .8rem; text-transform: uppercase; letter-spacing: 0.12em;
            color: #667eea; font-weight: 700; margin-bottom: 0.25rem;
        }
        .section p.body {
            color: var(--muted-light); font-size: 1.05rem; line-height: 1.8;
        }
        body.dark-mode .section p.body { color: var(--muted-dark); }
        @media (prefers-color-scheme: dark) {
            body:not(.light-mode) .section p.body { color: var(--muted-dark); }
        }

        .panel {
            background: var(--card-light);
            border: 1px solid var(--border-light);
            border-radius: 20px; padding: 2rem;
            box-shadow: 0 4px 10px rgba(0,0,0,.03);
        }
        body.dark-mode .panel { background: var(--card-dark); border-color: var(--border-dark); }
        @media (prefers-color-scheme: dark) {
            body:not(.light-mode) .panel { background: var(--card-dark); border-color: var(--border-dark); }
        }

        /* People grids */
        .people-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1.5rem;
            margin-top: 1.5rem;
        }
        .person-card {
            background: var(--card-light);
            border: 1px solid var(--border-light);
            border-radius: 18px;
            padding: 1.5rem 1.25rem;
            text-align: center;
            transition: transform .3s ease, box-shadow .3s ease;
        }
        body.dark-mode .person-card { background: var(--card-dark); border-color: var(--border-dark); }
        @media (prefers-color-scheme: dark) {
            body:not(.light-mode) .person-card { background: var(--card-dark); border-color: var(--border-dark); }
        }
        .person-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 25px -5px rgba(102,126,234,.25);
        }
        .person-avatar {
            width: 110px; height: 110px; margin: 0 auto 1rem;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea, #764ba2);
            padding: 3px;
        }
        .person-avatar img {
            width: 100%; height: 100%; border-radius: 50%;
            object-fit: cover; background: #fff;
        }
        .person-name { font-weight: 700; font-size: 1.05rem; margin-bottom: 0.25rem; }
        .person-role {
            font-size: 0.85rem; color: var(--muted-light);
            text-transform: uppercase; letter-spacing: 0.06em; font-weight: 600;
        }
        body.dark-mode .person-role { color: var(--muted-dark); }
        @media (prefers-color-scheme: dark) {
            body:not(.light-mode) .person-role { color: var(--muted-dark); }
        }

        .supervisor-card .person-avatar {
            background: linear-gradient(135deg, #f59e0b, #ef4444);
        }

        /* Footer */
        .footer {
            background: linear-gradient(135deg, #1e293b, #0f172a);
            color: rgba(255,255,255,0.8);
            padding: 2rem; text-align: center; margin-top: 2rem;
        }
        .footer a { color: #a5b4fc; text-decoration: none; }

        @media (max-width: 640px) {
            .hero h1 { font-size: 2.2rem; }
            .section-title { font-size: 1.6rem; }
            .navbar { padding: 0.9rem 1rem; }
            .nav-link { padding: 0.5rem 0.9rem; font-size: 0.9rem; }
        }
    </style>
</head>

<body>
    <nav class="navbar">
        <div class="navbar-container">
            <a href="{{ url('/') }}" class="navbar-logo">
                <span class="logo-icon">🏙️</span>
                <span>Smart City</span>
            </a>
            <div class="navbar-actions">
                <button class="theme-toggle" id="themeToggle" aria-label="Toggle theme">
                    <span id="themeIcon">🌙</span>
                </button>
                <a href="{{ url('/') }}" class="nav-link">Home</a>
                <a href="{{ url('/admin') }}" class="nav-link">Admin Panel</a>
            </div>
        </div>
    </nav>

    <section class="hero">
        <h1>{{ $projectTitle }}</h1>
        <p class="tagline">{{ $projectTagline }}</p>
    </section>

    <section class="section">
        <div class="panel">
            <div class="section-kicker">Abstract</div>
            <h2 class="section-title">What this project is</h2>
            <p class="body">{{ $abstract }}</p>
        </div>
    </section>

    <section class="section">
        <div class="panel">
            <div class="section-kicker">Project Idea</div>
            <h2 class="section-title">The concept</h2>
            <p class="body">{{ $projectIdea }}</p>
        </div>
    </section>

    <section class="section">
        <div class="section-kicker">The Team</div>
        <h2 class="section-title">Team members</h2>
        <div class="people-grid">
            @foreach($team as $person)
                <div class="person-card">
                    <div class="person-avatar">
                        <img src="{{ $avatarFor($person) }}" alt="{{ $person['name'] }}" loading="lazy">
                    </div>
                    <div class="person-name">{{ $person['name'] }}</div>
                    <div class="person-role">{{ $person['role'] }}</div>
                </div>
            @endforeach
        </div>
    </section>

    <section class="section">
        <div class="section-kicker">Supervision</div>
        <h2 class="section-title">Supervisors</h2>
        <div class="people-grid">
            @foreach($supervisors as $person)
                <div class="person-card supervisor-card">
                    <div class="person-avatar">
                        <img src="{{ $avatarFor($person) }}" alt="{{ $person['name'] }}" loading="lazy">
                    </div>
                    <div class="person-name">{{ $person['name'] }}</div>
                    <div class="person-role">{{ $person['role'] }}</div>
                </div>
            @endforeach
        </div>
    </section>

    <footer class="footer">
        <p>&copy; {{ date('Y') }} {{ config('app.name', 'Smart City') }} · <a href="{{ url('/') }}">Back to home</a></p>
    </footer>

    <script>
        const themeToggle = document.getElementById('themeToggle');
        const themeIcon = document.getElementById('themeIcon');
        const body = document.body;

        function applyTheme(theme) {
            if (theme === 'dark') {
                body.classList.add('dark-mode'); body.classList.remove('light-mode');
                themeIcon.textContent = '☀️';
            } else {
                body.classList.add('light-mode'); body.classList.remove('dark-mode');
                themeIcon.textContent = '🌙';
            }
            localStorage.setItem('theme', theme);
        }

        const saved = localStorage.getItem('theme');
        const initial = saved || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
        applyTheme(initial);

        themeToggle.addEventListener('click', () => {
            const current = body.classList.contains('dark-mode') ? 'dark' : 'light';
            applyTheme(current === 'dark' ? 'light' : 'dark');
        });
    </script>
</body>
</html>
