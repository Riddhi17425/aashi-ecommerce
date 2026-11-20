{{-- Self-contained on purpose: unmatched-route 404s render without a session,
     so the frontend layout (cart/wishlist/auth in header) can't be used here. --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, follow">
    <title>Page Not Found | Aashi Retail</title>
    <link rel="icon" href="{{ asset('public/frontend/img/aashi_retail_favicon.ico') }}" sizes="any">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --brand: #5db845;
            --brand-dark: #4a9a36;
            --brand-soft: #eef8ea;
            --ink: #1f2a1c;
            --muted: #6b7768;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Poppins', Arial, sans-serif;
            color: var(--ink);
            background: radial-gradient(circle at 15% 20%, var(--brand-soft) 0, transparent 40%),
                        radial-gradient(circle at 85% 80%, var(--brand-soft) 0, transparent 40%),
                        #fff;
            height: 100vh;
            height: 100dvh;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }
        header { padding: 16px 16px 0; text-align: center; }
        header img { height: 44px; width: auto; }
        main {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 8px 16px;
        }
        .card { text-align: center; max-width: 560px; width: 100%; }
        .code {
            font-size: clamp(72px, 16vw, 112px);
            font-weight: 800;
            line-height: 1;
            letter-spacing: -2px;
            background: linear-gradient(135deg, var(--brand) 0%, var(--brand-dark) 100%);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            position: relative;
            display: inline-block;
        }
        .bag {
            width: 56px; height: 56px;
            margin: -6px auto 14px;
            border-radius: 50%;
            background: #fff;
            box-shadow: 0 10px 30px rgba(93, 184, 69, .25);
            display: flex; align-items: center; justify-content: center;
            animation: float 3s ease-in-out infinite;
        }
        @keyframes float { 0%,100% { transform: translateY(0); } 50% { transform: translateY(-8px); } }
        h1 { font-size: clamp(22px, 4vw, 30px); font-weight: 700; margin-bottom: 6px; }
        p { color: var(--muted); font-size: 15px; line-height: 1.6; margin-bottom: 20px; }
        .actions { display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; }
        .btn {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 11px 24px;
            border-radius: 50px;
            font-size: 15px; font-weight: 500;
            text-decoration: none;
            transition: transform .2s, box-shadow .2s, background .2s;
        }
        .btn-primary { background: var(--brand); color: #fff; box-shadow: 0 8px 20px rgba(93, 184, 69, .35); }
        .btn-primary:hover { background: var(--brand-dark); transform: translateY(-2px); }
        .btn-outline { border: 2px solid var(--brand); color: var(--brand-dark); background: #fff; }
        .btn-outline:hover { background: var(--brand-soft); transform: translateY(-2px); }
        footer { text-align: center; padding: 12px 16px; font-size: 13px; color: var(--muted); }
        footer a { color: var(--brand-dark); text-decoration: none; }
    </style>
</head>
<body>
    <header>
        <a href="{{ route('home') }}">
            <img src="{{ asset('public/frontend/img/logo.png') }}" alt="Aashi Retail">
        </a>
    </header>

    <main>
        <div class="card">
            <div class="code">404</div>
            <div class="bag" aria-hidden="true">
                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#5db845" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/>
                    <line x1="3" y1="6" x2="21" y2="6"/>
                    <path d="M16 10a4 4 0 0 1-8 0"/>
                </svg>
            </div>
            <h1>Oops! Page not found</h1>
            <p>The page you're looking for may have been moved, renamed or is no longer available.
               Let's get you back to shopping.</p>
            <div class="actions">
                <a href="{{ route('home') }}" class="btn btn-primary">&larr; Back to Home</a>
                <a href="{{ route('contact') }}" class="btn btn-outline">Contact Us</a>
            </div>
        </div>
    </main>

    <footer>
        &copy; {{ date('Y') }} <a href="{{ route('home') }}">Aashi Retail</a>. All rights reserved.
    </footer>
</body>
</html>
