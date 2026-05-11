<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SPUP-CDC | @yield('title', 'Dashboard')</title>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<div class="wrapper">
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="logo">
            <div class="logo-circle"></div>
            @php $role = auth()->user()->role->name ?? 'Admin'; @endphp
            <a href="{{ $role === 'Staff' ? route('staff.dashboard') : ($role === 'Barangay Partner' ? route('barangay.dashboard') : route('admin.dashboard')) }}"
                style="color:#fff;text-decoration:none;">
                SPUP-CDC
            </a>
        </div>
        
        <nav>
            @php $role = auth()->user()->role->name; @endphp

            @if($role === 'Barangay Partner')
                <a href="{{ route('barangay.dashboard') }}"
                    class="{{ request()->routeIs('barangay.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                <a href="{{ route('barangay.recommendations.index') }}"
                    class="{{ request()->routeIs('barangay.recommendations.*') ? 'active' : '' }}">
                    <i class="fas fa-hand-point-up"></i> Recommend
                </a>

            @elseif($role === 'Staff')
                <a href="{{ route('staff.dashboard') }}"
                    class="{{ request()->routeIs('staff.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>

                <a href="{{ route('staff.beneficiaries.index') }}"
                    class="{{ request()->routeIs('staff.beneficiaries.*') ? 'active' : '' }}">
                    <i class="fas fa-users"></i> Beneficiaries
                </a>

                <a href="{{ route('staff.recommended.index') }}"
                    class="{{ request()->routeIs('staff.recommended.*') ? 'active' : '' }}">
                    <i class="fas fa-star"></i> Recommended
                </a>

                <a href="{{ route('staff.locations.index') }}"
                    class="{{ request()->routeIs('staff.locations.*') ? 'active' : '' }}">
                    <i class="fas fa-map-marker-alt"></i> Locations
                </a>

                <a href="{{ route('admin.inventory.index') }}"
                    class="{{ request()->routeIs('admin.inventory.*') ? 'active' : '' }}">
                    <i class="fas fa-boxes"></i> Inventory
                </a>

            @elseif($role === 'Admin')
                <a href="{{ route('admin.dashboard') }}"
                    class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                <a href="{{ route('admin.beneficiaries.index') }}"
                    class="{{ request()->routeIs('admin.beneficiaries.*') ? 'active' : '' }}">
                    <i class="fas fa-users"></i> Beneficiaries
                </a>
                <a href="{{ route('admin.inventory.index') }}"
                    class="{{ request()->routeIs('admin.inventory.*') ? 'active' : '' }}">
                    <i class="fas fa-boxes"></i> Inventory
                </a>
                <a href="{{ route('admin.staff.index') }}"
                    class="{{ request()->routeIs('admin.staff.*') ? 'active' : '' }}">
                    <i class="fas fa-user-tie"></i> Staff
                </a>

                <a href="{{ route('admin.relief.index') }}"
                    class="{{ request()->routeIs('admin.relief.*') ? 'active' : '' }}">
                    <i class="fas fa-hands-helping"></i> Relief Monitor
                </a>
                <a href="{{ route('admin.locations.index') }}"
                    class="{{ request()->routeIs('admin.locations.*') ? 'active' : '' }}">
                    <i class="fas fa-map-marker-alt"></i> Locations
                </a>
            @endif
        </nav>
        
        <!-- User Profile Section -->
        <div class="user-profile">
            <div class="user-info">
                <div class="user-avatar">
                    {{ strtoupper(substr(auth()->user()->first_name,0,1).substr(auth()->user()->last_name,0,1)) }}
                </div>
                <div class="user-details">
                    <div class="user-name">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</div>
                    <div class="user-role">{{ auth()->user()->role->name ?? 'Admin' }}</div>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="logout-form">
                @csrf
                <button type="submit" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i> Log out
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="main-wrapper">
        <!-- Top Header -->
        <header class="top-header">
            <div class="header-left">
                <button class="sidebar-toggle" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="breadcrumb-nav">
                    @yield('breadcrumb', 'Dashboard')
                </div>
            </div>
            
            <div class="header-right">
                <!-- Search Bar -->
                <div class="search-bar">
                    <input type="text" placeholder="Search..." class="search-input">
                    <button class="search-btn">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
                
                <!-- Notifications -->
                <div class="notifications">
                    <button class="notification-btn" onclick="toggleNotifications()">
                        <i class="fas fa-bell"></i>
                        @if($unreadNotificationCount > 0)
                            <span class="notification-badge">{{ $unreadNotificationCount }}</span>
                        @endif
                    </button>
                    <div class="notification-dropdown" id="notificationDropdown">
                        <div class="notification-header">
                            <h4>Notifications</h4>
                            @if($unreadNotificationCount > 0)
                                <button class="mark-all-read" onclick="markAllNotificationsRead()">Mark all as read</button>
                            @endif
                        </div>
                        <div class="notification-list">
                            @forelse($userNotifications as $notification)
                                <div class="notification-item {{ $notification->unread ? 'unread' : '' }}" onclick="markNotificationRead({{ $notification->id }})">
                                    <a href="{{ $notification->url }}" style="text-decoration: none; color: inherit;">
                                        <div class="notification-icon">
                                            <i class="{{ $notification->icon }}" style="color: {{ $notification->color }};"></i>
                                        </div>
                                        <div class="notification-content">
                                            <div class="notification-title">{{ $notification->title }}</div>
                                            <div class="notification-text">{{ $notification->text }}</div>
                                            <div class="notification-time">{{ $notification->time }}</div>
                                        </div>
                                    </a>
                                </div>
                            @empty
                                <div class="notification-empty">
                                    <i class="fas fa-bell-slash" style="color: #888780; font-size: 2rem; margin-bottom: 0.5rem;"></i>
                                    <p style="color: #888780; margin: 0;">No notifications</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
                
                <!-- Quick Actions - Hidden for Barangay Users -->
                @if($canCreateItems)
                <div class="quick-actions">
                    <button class="quick-action-btn" onclick="toggleQuickActions()">
                        <i class="fas fa-plus"></i>
                    </button>
                    <div class="quick-actions-dropdown" id="quickActionsDropdown">
                        @if(Route::has('admin.calamity.create'))
                        <a href="{{ route('admin.calamity.create') }}" class="quick-action-item">
                            <i class="fas fa-exclamation-triangle"></i> New Calamity
                        </a>
                        @endif
                        @if(Route::has('admin.beneficiaries.create'))
                        <a href="{{ route('admin.beneficiaries.create') }}" class="quick-action-item">
                            <i class="fas fa-user-plus"></i> Add Beneficiary
                        </a>
                        @endif
                        @if(Route::has('admin.events.create'))
                        <a href="{{ route('admin.events.create') }}" class="quick-action-item">
                            <i class="fas fa-hand-holding-heart"></i> New Relief Event
                        </a>
                        @endif
                        @if(Route::has('admin.inventory.category.create'))
                        <a href="{{ route('admin.inventory.category.create') }}" class="quick-action-item">
                            <i class="fas fa-box"></i> Add Inventory
                        </a>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </header>

        <!-- Main Content -->
        <main class="main-content">
            @yield('content')
        </main>
    </div>
</div>

@stack('scripts')

<script>
// Toggle Sidebar
function toggleSidebar() {
    document.querySelector('.sidebar').classList.toggle('collapsed');
    document.querySelector('.main-wrapper').classList.toggle('expanded');
}

// Toggle Notifications
function toggleNotifications() {
    const dropdown = document.getElementById('notificationDropdown');
    dropdown.classList.toggle('show');
    
    // Close when clicking outside
    document.addEventListener('click', function closeNotifications(e) {
        if (!e.target.closest('.notifications')) {
            dropdown.classList.remove('show');
            document.removeEventListener('click', closeNotifications);
        }
    });
}

// Toggle Quick Actions
function toggleQuickActions() {
    const dropdown = document.getElementById('quickActionsDropdown');
    dropdown.classList.toggle('show');
    
    // Close when clicking outside
    document.addEventListener('click', function closeQuickActions(e) {
        if (!e.target.closest('.quick-actions')) {
            dropdown.classList.remove('show');
            document.removeEventListener('click', closeQuickActions);
        }
    });
}

// Mark notifications as read
document.querySelector('.mark-all-read')?.addEventListener('click', function() {
    markAllNotificationsRead();
});

function markAllNotificationsRead() {
    document.querySelectorAll('.notification-item.unread').forEach(item => {
        item.classList.remove('unread');
    });
    const badge = document.querySelector('.notification-badge');
    if (badge) {
        badge.style.display = 'none';
    }
}

function markNotificationRead(notificationId) {
    const notification = document.querySelector(`[onclick*="${notificationId}"]`);
    if (notification) {
        notification.classList.remove('unread');
        updateNotificationBadge();
    }
}

function updateNotificationBadge() {
    const unreadCount = document.querySelectorAll('.notification-item.unread').length;
    const badge = document.querySelector('.notification-badge');
    if (badge) {
        if (unreadCount > 0) {
            badge.textContent = unreadCount;
            badge.style.display = 'block';
        } else {
            badge.style.display = 'none';
        }
    }
}
</script>
</body>
</html>