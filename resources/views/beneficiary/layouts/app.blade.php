<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SPUP-CDC | @yield('title', 'Dashboard')</title>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <link rel="stylesheet" href="{{ asset('css/design-system.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
    .btn-back {
        background: #dc3545;
        color: white;
        border: none;
        padding: 0.5rem 1rem;
        border-radius: 4px;
        cursor: pointer;
        font-size: 0.875rem;
        transition: background-color 0.2s;
    }
    
    .btn-back:hover {
        background: #c82333;
    }
    
    .btn-back i {
        margin-right: 0.5rem;
    }
    
    /* Ensure main content wrapper has proper styling */
    .content-wrapper {
        padding: 2rem 3rem;
        background: #f8f9fa;
        min-height: calc(100vh - 80px);
        width: 100%;
        max-width: 1400px;
        margin: 0 auto;
    }
    
    /* Ensure main content area is styled */
    .main-content {
        flex: 1;
        background: #f8f9fa;
        width: 100%;
        margin-left: 0;
        padding-left: 0;
    }
    
    /* Ensure wrapper takes full height and width */
    .wrapper {
        min-height: 100vh;
        width: 100%;
        display: flex;
        flex-direction: column;
    }
    
    /* Main wrapper styling */
    .main-wrapper {
        display: flex;
        flex-direction: column;
        min-height: 100vh;
        width: 100%;
        margin-left: 0;
    }
    
    /* Header styling */
    .top-header {
        background: #fff;
        border-bottom: 1px solid #e5e7eb;
        padding: 1rem 2rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }
    
    .header-left {
        display: flex;
        align-items: center;
    }
    
    .header-right {
        display: flex;
        align-items: center;
    }
    
    .breadcrumb-nav {
        font-size: 1.125rem;
        font-weight: 600;
        color: #374151;
    }
    
    /* Beneficiary Dashboard Specific Styles */
    .beneficiary-dashboard {
        max-width: 100%;
        padding: 0;
        margin: 0 auto;
    }
    
    .dashboard-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2.5rem;
        flex-wrap: wrap;
        gap: 1.5rem;
        padding: 0 0.5rem;
    }
    
    .welcome-section {
        flex: 1;
        min-width: 300px;
    }
    
    .welcome-title {
        font-size: 1.75rem;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 0.5rem;
        line-height: 1.2;
    }
    
    .welcome-subtitle {
        color: #6b7280;
        font-size: 1rem;
        line-height: 1.4;
    }
    
    .beneficiary-info {
        background: #eaf3de;
        border: 1px solid #1a6b2a;
        border-radius: 12px;
        padding: 1.25rem;
        min-width: 280px;
        box-shadow: 0 2px 4px rgba(26, 107, 42, 0.1);
    }
    
    .beneficiary-name {
        font-weight: 600;
        color: #1a6b2a;
        margin-bottom: 0.5rem;
        font-size: 1.1rem;
    }
    
    .beneficiary-id {
        font-size: 0.9rem;
        color: #6b7280;
        font-weight: 500;
    }
    
    /* Stats Cards */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 2rem;
        margin-bottom: 3rem;
        max-width: 1200px;
        margin-left: auto;
        margin-right: auto;
    }
    
    .stat-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        padding: 2rem;
        text-align: center;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        transition: transform 0.3s, box-shadow 0.3s;
        position: relative;
        overflow: hidden;
    }
    
    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #10b981, #059669);
    }
    
    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.12);
    }
    
    .stat-icon {
        width: 64px;
        height: 64px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
        font-size: 1.5rem;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    
    .stat-value {
        font-size: 2.5rem;
        font-weight: 800;
        color: #1f2937;
        margin-bottom: 0.75rem;
        line-height: 1;
    }
    
    .stat-label {
        color: #6b7280;
        font-size: 0.95rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    /* Relief History */
    .relief-history {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        padding: 2rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        max-width: 1200px;
        margin: 0 auto;
    }
    
    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        padding-bottom: 1.5rem;
        border-bottom: 2px solid #f3f4f6;
    }
    
    .section-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1f2937;
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    
    .section-icon {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        background: #f3f4f6;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #6b7280;
        font-size: 1.1rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    /* Icon colors */
    .icon-blue { background: #dbeafe; color: #2563eb; }
    .icon-green { background: #dcfce7; color: #16a34a; }
    .icon-purple { background: #f3e8ff; color: #9333ea; }
    .icon-orange { background: #fed7aa; color: #ea580c; }
    </style>
</head>
<body>

@stack('styles')

<div class="wrapper">
    <!-- Main Content Area -->
    <div class="main-wrapper">
        <!-- Top Header -->
        <header class="top-header">
            <div class="header-left">
                <div style="display:flex;align-items:center;gap:15px;">
                    <img src="{{ asset('images/images-5.jpeg') }}" alt="SPUP-CDC Logo" style="height:35px;width:auto;border-radius:50%;object-fit:cover;">
                    <div class="breadcrumb-nav">
                        @yield('breadcrumb', 'Dashboard')
                    </div>
                </div>
            </div>
            
            <div class="header-right">
                <!-- Back Button -->
                <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn-back" style="margin-right: 1rem;">
                        <i class="fas fa-arrow-left"></i> Back
                    </button>
                </form>
            </div>
        </header>

        <!-- Main Content -->
        <main class="main-content">
            <div class="content-wrapper">
                @yield('content')
            </div>
        </main>
    </div>
</div>

@include('partials.pdf-export-scripts')
@stack('scripts')

<script>
// Logout functionality
document.querySelector('.btn-back')?.addEventListener('click', function(e) {
    if (confirm('Are you sure you want to logout?')) {
        // Form will submit normally
    } else {
        e.preventDefault();
    }
});
</script>

</body>
</html>
