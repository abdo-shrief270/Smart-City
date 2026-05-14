<style>
    .fi-floating-logo {
        position: fixed;
        right: 1.5rem;
        bottom: 1.5rem;
        z-index: 9998;
        width: 56px;
        height: 56px;
        border-radius: 50%;
        overflow: hidden;
        background: #fff;
        border: 2px solid rgba(16, 185, 129, 0.35);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.25), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
        cursor: pointer;
        transition: transform 0.25s ease, box-shadow 0.25s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0;
    }

    .fi-floating-logo:hover {
        transform: scale(1.08) rotate(-4deg);
        box-shadow: 0 18px 35px -5px rgba(16, 185, 129, 0.45), 0 10px 15px -6px rgba(0, 0, 0, 0.15);
    }

    .fi-floating-logo img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .dark .fi-floating-logo {
        background: #1f2937;
        border-color: rgba(16, 185, 129, 0.55);
    }

    @media (max-width: 640px) {
        .fi-floating-logo {
            right: 1rem;
            bottom: 1rem;
            width: 48px;
            height: 48px;
        }
    }
</style>

<a href="{{ url('/') }}" class="fi-floating-logo" title="{{ config('app.name', 'Smart City') }}" aria-label="Go to landing page">
    <img src="{{ asset('logo.jpeg') }}" alt="{{ config('app.name', 'Smart City') }} logo">
</a>
