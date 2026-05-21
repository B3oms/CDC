<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SPUP-CDC | @yield('title', 'Dashboard')</title>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <link rel="stylesheet" href="{{ asset('css/design-system.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/location-management.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
.user-info {
    text-decoration: none;
    color: inherit;
    display: block;
    width: 100%;
    text-align: center;
    cursor: pointer;
}

.user-info:hover {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 8px;
}

.user-info .user-name {
    color: #fff;
}

.user-info .user-avatar {
    margin: 0 auto;
}

</style>
@stack('styles')
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
            <x-back-button :history="true" class="btn-back--sidebar" />
            
            <a href="{{ route('staff.dashboard') }}"
                class="{{ request()->routeIs('staff.dashboard') ? 'active' : '' }}">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
            <a href="{{ route('staff.beneficiaries.index') }}"
                class="{{ request()->routeIs('staff.beneficiaries.*') ? 'active' : '' }}">
                <i class="fas fa-users"></i> Beneficiaries
            </a>
            <a href="{{ route('staff.inventory.index') }}"
                class="{{ request()->routeIs('staff.inventory.*') ? 'active' : '' }}">
                <i class="fas fa-boxes"></i> Inventory
            </a>
            <a href="{{ route('staff.relief.index') }}"
                class="{{ request()->routeIs('staff.relief.*') ? 'active' : '' }}"
                style="display: block !important; visibility: visible !important;">
                <i class="fas fa-hands-helping"></i> Relief Monitor
            </a>
            <a href="{{ route('staff.calamities.index') }}"
                class="{{ request()->routeIs('staff.calamities.*') ? 'active' : '' }}">
                <i class="fas fa-exclamation-triangle"></i> Calamity Meter
            </a>
            <a href="{{ route('staff.locations.index') }}"
                class="{{ request()->routeIs('staff.locations.*') ? 'active' : '' }}">
                <i class="fas fa-map-marker-alt"></i> Barangay Partners
            </a>
            <a href="{{ route('staff.recommended.index') }}"
                class="{{ request()->routeIs('staff.recommended.*') ? 'active' : '' }}">
                <i class="fas fa-star"></i> Recommended
            </a>
            <a href="{{ route('staff.household_requests.index') }}"
                class="{{ request()->routeIs('staff.household_requests.*') ? 'active' : '' }}">
                <i class="fas fa-clipboard-list"></i> Household Requests
            </a>
            {{-- Debug: Current route is {{ request()->route()->getName() }} --}}
{{-- Debug: Relief monitor route match: {{ request()->routeIs('staff.relief.*') ? 'YES' : 'NO' }} --}}
{{-- Debug: Inventory route match: {{ request()->routeIs('staff.inventory.*') ? 'YES' : 'NO' }} --}}
        </nav>
        
        <!-- User Profile Section -->
        <div class="user-profile">
            @if(auth()->check())
            <a href="{{ route('staff.profile.show') }}" class="user-info">
                <div class="user-avatar">
                    {{ strtoupper(substr(auth()->user()->first_name,0,1).substr(auth()->user()->last_name,0,1)) }}
                </div>
                <div class="user-details">
                    <div class="user-name">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</div>
                    <div class="user-role">{{ auth()->user()->role->name }}</div>
                </div>
            </a>
            <form method="POST" action="{{ route('logout') }}" class="logout-form">
                @csrf
                <button type="submit" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </form>
            @else
            <div class="user-info">
                <div class="user-avatar">
                    G
                </div>
                <div class="user-details">
                    <div class="user-name">Guest User</div>
                    <div class="user-role">Not Authenticated</div>
                </div>
            </div>
            @endif
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
                <!-- Notifications -->
                <div class="notifications">
                    <button class="notification-btn" onclick="toggleNotifications()">
                        <i class="fas fa-bell"></i>
                        <span class="notification-badge" id="notificationBadge" style="display: none;">0</span>
                    </button>
                    <div class="notification-dropdown" id="notificationDropdown">
                        <div class="notification-header">
                            <h4>Notifications</h4>
                            <button class="mark-all-read" onclick="markAllNotificationsRead()">Mark all as read</button>
                        </div>
                        <div class="notification-list" id="notificationList">
                            <div class="notification-loading">
                                <i class="fas fa-spinner fa-spin"></i>
                                <p>Loading notifications...</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Quick Actions -->
                <div class="quick-actions">
                    <button class="quick-action-btn" onclick="toggleQuickActions()">
                        <i class="fas fa-plus"></i>
                    </button>
                    <div class="quick-actions-dropdown" id="quickActionsDropdown">
                        <a href="{{ route('staff.beneficiaries.create') }}" class="quick-action-item">
                            <i class="fas fa-users"></i> Beneficiaries
                        </a>
                        <a href="{{ route('staff.location-requests.create') }}" class="quick-action-item">
                            <i class="fas fa-map-marker-alt"></i> Location Request
                        </a>
                        <a href="{{ route('staff.relief.create') }}" class="quick-action-item">
                            <i class="fas fa-hand-holding-heart"></i> Relief Event
                        </a>
                        <a href="#" class="quick-action-item" onclick="alert('Inventory management coming soon!')">
                            <i class="fas fa-box"></i> Inventory
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="main-content">
            @yield('content')
        </main>
    </div>
</div>

@include('partials.pdf-export-scripts')
@stack('scripts')

<script>
// Reset sidebar scroll to top on page load
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.querySelector('.sidebar');
    if (sidebar) {
        sidebar.scrollTop = 0;
    }
});

// Toggle Sidebar
function toggleSidebar() {
    document.querySelector('.sidebar').classList.toggle('collapsed');
    document.querySelector('.main-wrapper').classList.toggle('expanded');
}

// Toggle Notifications
function toggleNotifications() {
    const dropdown = document.getElementById('notificationDropdown');
    const isOpen = dropdown.classList.contains('show');
    
    if (!isOpen) {
        loadNotifications();
    }
    
    dropdown.classList.toggle('show');
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function closeDropdown(e) {
        if (!e.target.closest('.notifications')) {
            dropdown.classList.remove('show');
            document.removeEventListener('click', closeDropdown);
        }
    });
}

// Toggle Quick Actions
function toggleQuickActions() {
    const dropdown = document.getElementById('quickActionsDropdown');
    const notifDropdown = document.getElementById('notificationDropdown');
    
    // Close notifications if open
    if (notifDropdown.classList.contains('show')) {
        notifDropdown.classList.remove('show');
    }
    
    dropdown.classList.toggle('show');
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function closeDropdown(e) {
        if (!e.target.closest('.quick-actions')) {
            dropdown.classList.remove('show');
            document.removeEventListener('click', closeDropdown);
        }
    });
}

// Load real-time notifications
function loadNotifications() {
    const notificationList = document.getElementById('notificationList');
    
    fetch('{{ route("staff.dashboard.notifications") }}')
        .then(response => response.json())
        .then(data => {
            updateNotificationBadge(data.unread_count);
            displayNotifications(data.notifications);
        })
        .catch(error => {
            console.error('Error loading notifications:', error);
            notificationList.innerHTML = `
                <div class="notification-empty">
                    <i class="fas fa-exclamation-triangle" style="color: #ef4444; font-size: 2rem; margin-bottom: 0.5rem;"></i>
                    <p style="color: #ef4444; margin: 0;">Error loading notifications</p>
                </div>
            `;
        });
}

// Display notifications in the dropdown
function displayNotifications(notifications) {
    const notificationList = document.getElementById('notificationList');
    
    if (notifications.length === 0) {
        notificationList.innerHTML = `
            <div class="notification-empty">
                <i class="fas fa-bell-slash" style="color: #888780; font-size: 2rem; margin-bottom: 0.5rem;"></i>
                <p style="color: #888780; margin: 0;">No notifications</p>
            </div>
        `;
        return;
    }
    
    notificationList.innerHTML = notifications.map(notification => `
        <div class="notification-item ${notification.unread ? 'unread' : ''}" onclick="markNotificationRead('${notification.id}')">
            <a href="${notification.url}" style="text-decoration: none; color: inherit;">
                <div class="notification-icon">
                    <i class="${notification.icon}" style="color: ${notification.color};"></i>
                </div>
                <div class="notification-content">
                    <div class="notification-title">${notification.title}</div>
                    <div class="notification-text">${notification.text}</div>
                    <div class="notification-time">${notification.time}</div>
                </div>
            </a>
        </div>
    `).join('');
}

// Update notification badge
function updateNotificationBadge(count) {
    const badge = document.getElementById('notificationBadge');
    if (count > 0) {
        badge.textContent = count > 99 ? '99+' : count;
        badge.style.display = 'block';
    } else {
        badge.style.display = 'none';
    }
}

// Mark notification as read
function markNotificationRead(notificationId) {
    fetch(`{{ route('staff.dashboard.notifications.read', ':notificationId') }}`.replace(':notificationId', notificationId), {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const notificationItem = document.querySelector(`[onclick*="${notificationId}"]`);
            if (notificationItem) {
                notificationItem.classList.remove('unread');
            }
            
            // Update badge count
            const badge = document.getElementById('notificationBadge');
            const currentCount = parseInt(badge.textContent) || 0;
            if (currentCount > 0) {
                updateNotificationBadge(currentCount - 1);
            }
        }
    })
    .catch(error => {
        console.error('Error marking notification as read:', error);
    });
}

// Mark all notifications as read
function markAllNotificationsRead() {
    fetch('{{ route("staff.dashboard.notifications.readAll") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const notificationItems = document.querySelectorAll('.notification-item.unread');
            notificationItems.forEach(item => {
                item.classList.remove('unread');
            });
            
            updateNotificationBadge(0);
        }
    })
    .catch(error => {
        console.error('Error marking all notifications as read:', error);
    });
}

// Initialize notification badge (could be loaded from backend)
// Back button functionality
function goBack() {
    if (window.history.length > 1) {
        window.history.back();
    } else {
        // If no history, go to dashboard
        window.location.href = '{{ route("staff.dashboard") }}';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Set initial notification count if needed
    const badge = document.getElementById('notificationBadge');
    // badge.style.display = 'inline-flex';
    // badge.textContent = '3';
});
</script>

</body>
</html>