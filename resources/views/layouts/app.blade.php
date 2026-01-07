<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Task B | Discounts</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --sidebar-width: 260px; --primary-color: #6366f1; }
        body { background-color: #f9fafb; overflow-x: hidden; }
        #sidebar { 
            width: var(--sidebar-width); 
            height: 100vh; 
            position: fixed; 
            background: #111827; 
            color: white;
            transition: all 0.3s;
        }
        #content { 
            margin-left: var(--sidebar-width); 
            padding: 40px; 
            min-height: 100vh; 
        }
        .nav-link { color: #9ca3af; padding: 12px 20px; border-radius: 8px; margin: 4px 15px; }
        .nav-link:hover, .nav-link.active { background: rgba(255,255,255,0.05); color: #6366f1; }
        .nav-link i { margin-right: 10px; width: 20px; }
        .card { border: none; border-radius: 16px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
        .btn-primary { background: var(--primary-color); border: none; padding: 10px 20px; border-radius: 10px; }
    </style>
</head>
<body>
    <div id="sidebar">
        <div class="p-4 mb-3 border-bottom border-gray-700 text-center">
            <h4 class="fw-bold mb-0 text-white">DISCOUNT <span class="text-primary">PRO</span></h4>
        </div>
        <nav class="nav flex-column mt-4">
            <a class="nav-link {{ request()->is('/') ? 'active' : '' }}" href="{{ url('/') }}">
                <i class="fas fa-chart-line"></i> Dashboard
            </a>
            <a class="nav-link {{ request()->is('discounts*') ? 'active' : '' }}" href="{{ route('discounts.index') }}">
                <i class="fas fa-tags"></i> All Discounts
            </a>
            <a class="nav-link {{ request()->is('audits*') ? 'active' : '' }}" href="{{ route('demo.audits') }}">
                <i class="fas fa-history"></i> Audit Logs
            </a>
        </nav>
    </div>

    <div id="content">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm mb-4" role="alert">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show rounded-4 shadow-sm mb-4" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>
</html>
