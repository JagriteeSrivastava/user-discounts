<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Laravel | 100% NPM Free</title>
        <!-- Bootstrap 5 CDN -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <style>
            body { background: #f8f9fa; display: flex; align-items: center; justify-content: center; min-height: 100vh; font-family: sans-serif; }
            .welcome-card { background: white; padding: 3rem; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); text-align: center; }
            .btn-custom { padding: 12px 30px; border-radius: 10px; font-weight: 600; transition: all 0.3s; }
            .btn-custom:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        </style>
    </head>
    <body>
        <div class="welcome-card">
            <h1 class="fw-bold mb-4">Welcome to Task B</h1>
            <p class="text-muted mb-5">Your Laravel project is now fully independent of NPM and Vite.</p>
            
            <div class="d-flex gap-3 justify-content-center">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/') }}" class="btn btn-primary btn-custom">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-primary btn-custom">Log in</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn btn-outline-primary btn-custom">Register</a>
                        @endif
                    @endauth
                @endif
            </div>
        </div>
    </body>
</html>
