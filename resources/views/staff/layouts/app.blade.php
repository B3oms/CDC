<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SPUP-CDC | @yield('title', 'Dashboard')</title>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/location-management.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<div class="wrapper">
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="logo">
            <div class="logo-circle"></div>
            @php $role = auth()->user()->role->name ?? 'Staff'; @endphp
            <a href="{{ $role === 'Staff' ? route('staff.dashboard') : ($role === 'Barangay Partner' ? route('barangay.dashboard') : route('admin.dashboard')) }}"
                style="color:#fff;text-decoration:none;">
                SPUP-CDC
            </a>
        </div>
        
        <nav>
            <a href="{{ route('staff.dashboard') }}"
                class="{{ request()->routeIs('staff.dashboard') ? 'active' : '' }}">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
            <a href="{{ route('staff.beneficiaries.index') }}"
                class="{{ request()->routeIs('staff.beneficiaries.*') ? 'active' : '' }}">
                <i class="fas fa-users"></i> Beneficiaries
            </a>
            <a href="{{ route('staff.locations.index') }}"
                class="{{ request()->routeIs('staff.locations.*') ? 'active' : '' }}">
                <i class="fas fa-map-marker-alt"></i> Locations
            </a>
            <a href="{{ route('staff.recommended.index') }}"
                class="{{ request()->routeIs('staff.recommended.*') ? 'active' : '' }}">
                <i class="fas fa-star"></i> Recommended
            </a>
            <a href="{{ route('admin.relief.index') }}"
                class="{{ request()->routeIs('admin.relief.*') ? 'active' : '' }}">
                <i class="fas fa-hands-helping"></i> Relief Monitor
            </a>
            <a href="{{ route('admin.inventory.index') }}"
                class="{{ request()->routeIs('admin.inventory.*') ? 'active' : '' }}">
                <i class="fas fa-boxes"></i> Inventory
            </a>
        </nav>
        </nav>
        
        <!-- User Profile Section -->
        <div class="user-profile">
            <div class="user-info">
                <div class="user-avatar">
                    {{ strtoupper(substr(auth()->user()->first_name,0,1).substr(auth()->user()->last_name,0,1)) }}
                </div>
                <div class="user-details">
                    <div class="user-name">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</div>
                    <div class="user-role">{{ auth()->user()->role->name }}</div>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="logout-form">
                @csrf
                <button type="submit" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="main-wrapper">
        <main class="main-content">
            @yield('content')
        </main>
    </div>
</div>

@push('scripts')
@stack('scripts')
@endpush

</body>
</html>