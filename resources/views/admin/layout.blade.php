<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Panel – SmartWorker</title>
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fonts/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fonts/themify-icons.css') }}">
    <style>
        body { background: #f4f6f9; }
        .admin-sidebar {
            min-height: 100vh;
            background: #2c3e50;
            padding: 0;
        }
        .admin-sidebar .brand {
            background: #1a252f;
            padding: 20px;
            color: #fff;
            font-size: 18px;
            font-weight: bold;
            display: block;
            text-decoration: none;
        }
        .admin-sidebar .nav > li > a {
            color: #bdc3c7;
            padding: 14px 20px;
            display: block;
            border-bottom: 1px solid #34495e;
            transition: background 0.2s;
        }
        .admin-sidebar .nav > li > a:hover,
        .admin-sidebar .nav > li.active > a {
            background: #34495e;
            color: #fff;
            text-decoration: none;
        }
        .admin-sidebar .nav > li > a i { margin-right: 8px; width: 16px; }
        .admin-main { padding: 30px; }
        .stat-card { border-radius: 8px; padding: 20px; color: #fff; margin-bottom: 20px; }
        .stat-card h2 { font-size: 36px; margin: 0; font-weight: bold; }
        .stat-card p  { margin: 5px 0 0; opacity: 0.85; }
        .bg-primary-dark { background: #3498db; }
        .bg-success-dark  { background: #27ae60; }
        .bg-warning-dark  { background: #e67e22; }
        .bg-danger-dark   { background: #e74c3c; }
        .bg-info-dark     { background: #16a085; }
        .admin-topbar {
            background: #fff;
            border-bottom: 1px solid #dee2e6;
            padding: 12px 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .badge-unread { background: #e74c3c; color: #fff; border-radius: 12px; padding: 2px 8px; font-size: 11px; }
        table th { background: #f8f9fa; }
        .table-actions a, .table-actions form { display: inline-block; margin: 0 2px; }
    </style>
</head>
<body>
<div class="row" style="margin:0;">
    {{-- Sidebar --}}
    <div class="col-md-2 admin-sidebar" style="padding:0;">
        <a href="{{ route('admin.dashboard') }}" class="brand"><i class="ti-dashboard"></i> Admin Panel</a>
        <ul class="nav nav-pills nav-stacked" style="margin:0;">
            <li class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <a href="{{ route('admin.dashboard') }}"><i class="ti-home"></i> Dashboard</a>
            </li>
            <li class="{{ request()->routeIs('admin.jobs*') ? 'active' : '' }}">
                <a href="{{ route('admin.jobs') }}"><i class="ti-briefcase"></i> Jobs</a>
            </li>
            <li class="{{ request()->routeIs('admin.users*') ? 'active' : '' }}">
                <a href="{{ route('admin.users') }}"><i class="ti-user"></i> Users</a>
            </li>
            <li class="{{ request()->routeIs('admin.applications*') ? 'active' : '' }}">
                <a href="{{ route('admin.applications') }}"><i class="ti-briefcase"></i> Applications</a>
            </li>
            <li class="{{ request()->routeIs('admin.contacts*') ? 'active' : '' }}">
                <a href="{{ route('admin.contacts') }}">
                    <i class="ti-email"></i> Messages
                    @php $unread = \App\Models\Contact::where('is_read', false)->count(); @endphp
                    @if ($unread > 0)
                        <span class="badge-unread">{{ $unread }}</span>
                    @endif
                </a>
            </li>
            <li>
                <a href="{{ url('/') }}"><i class="ti-arrow-left"></i> Back to Site</a>
            </li>
            <li>
                <a href="#" onclick="document.getElementById('admin-logout').submit();">
                    <i class="ti-power-off"></i> Logout
                </a>
                <form id="admin-logout" method="POST" action="{{ route('logout') }}">@csrf</form>
            </li>
        </ul>
    </div>

    {{-- Main content --}}
    <div class="col-md-10" style="padding:0;">
        <div class="admin-topbar">
            <span><strong>@yield('page-title', 'Dashboard')</strong></span>
            <span>Logged in as: <strong>{{ auth()->user()->name ?? 'Admin' }}</strong></span>
        </div>
        <div class="admin-main">
            @if (session('success'))
                <div class="alert alert-success alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    {{ session('success') }}
                </div>
            @endif

            @yield('admin-content')
        </div>
    </div>
</div>

<script src="{{ asset('assets/js/jquery-min.js') }}"></script>
<script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
</body>
</html>
