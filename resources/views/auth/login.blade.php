<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPUP-CDC | Login</title>
    <link rel="stylesheet" href="{{ asset('css/design-system.css') }}">
    <link rel="stylesheet" href="{{ asset('css/login-page.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="login-page">

<div class="login-wrap">

    {{-- Left Panel --}}
    <div class="left-panel">
        <div>
            <div class="left-top-label">SPUP-CDC</div>
            <h2 class="left-heading">Disaster Response System</h2>
            <p class="left-body">Coordinating relief operations and beneficiary management across Cagayan Valley.</p>
        </div>
        <div class="left-footer">
            <div class="left-footer-item">
                <i class="fas fa-map-marker-alt"></i>
                Cagayan Valley, Philippines
            </div>
            <div class="left-footer-item">
                <i class="fas fa-shield-alt"></i>
                St. Paul University Philippines
            </div>
        </div>
    </div>

    {{-- Right Panel --}}
    <div class="right-panel">
        <div class="login-card">

            <div class="login-header">
                @if(file_exists(public_path('images/spup-logo.png')))
                    <img src="{{ asset('images/spup-logo.png') }}" alt="SPUP Logo" style="height:48px; margin-bottom:0.25rem;">
                @else
                    <div class="logo-circle"><i class="fas fa-shield-halved"></i></div>
                @endif
                <div class="login-title">Welcome back</div>
                <div class="login-subtitle">Select your role to continue</div>
            </div>

            <form method="POST" action="{{ route('login.post') }}" id="loginForm" onsubmit="return validateLoginForm()">
                @csrf
                <input type="hidden" name="user_role" id="user_role" required>

                <div class="section-label">Select your role</div>

                <div class="role-options">
                    <div class="role-box" data-role="admin" onclick="selectRole('admin')">
                        <div class="role-icon"><i class="fas fa-shield-halved"></i></div>
                        <div class="role-info">
                            <h4>Administrator</h4>
                            <p>Full system access</p>
                        </div>
                    </div>
                    <div class="role-box" data-role="staff" onclick="selectRole('staff')">
                        <div class="role-icon"><i class="fas fa-users-gear"></i></div>
                        <div class="role-info">
                            <h4>Staff</h4>
                            <p>Relief operations</p>
                        </div>
                    </div>
                    <div class="role-box" data-role="barangay" onclick="selectRole('barangay')">
                        <div class="role-icon"><i class="fas fa-building"></i></div>
                        <div class="role-info">
                            <h4>Barangay Rep</h4>
                            <p>Local distribution</p>
                        </div>
                    </div>
                    <div class="role-box" data-role="beneficiary" onclick="selectRole('beneficiary')">
                        <div class="role-icon"><i class="fas fa-user"></i></div>
                        <div class="role-info">
                            <h4>Beneficiary</h4>
                            <p>Relief recipient</p>
                        </div>
                    </div>
                </div>

                @if($errors->has('user_role'))
                    <span class="error-msg">{{ $errors->first('user_role') }}</span>
                @endif

                {{-- Email / Password --}}
                <div id="emailForm" class="form-section">
                    <hr class="form-divider">
                    <div class="field-group">
                        <label for="email">Email address <span class="required">*</span></label>
                        <input type="email" id="email" name="email"
                               placeholder="Enter your email address"
                               value="{{ old('email') }}" autocomplete="email">
                        @if($errors->has('email'))
                            <span class="error-msg">{{ $errors->first('email') }}</span>
                        @endif
                    </div>
                    <div class="field-group">
                        <label for="password">Password <span class="required">*</span></label>
                        <input type="password" id="password" name="password"
                               placeholder="Enter your password"
                               autocomplete="current-password">
                        @if($errors->has('password'))
                            <span class="error-msg">{{ $errors->first('password') }}</span>
                        @endif
                    </div>
                    <button type="submit" class="btn-login">Log in</button>
                </div>

                {{-- Unique ID --}}
                <div id="idForm" class="form-section">
                    <hr class="form-divider">
                    <div class="field-group">
                        <label for="unique_id">Unique ID <span class="required">*</span></label>
                        <input type="text" id="unique_id" name="unique_id"
                               placeholder="e.g. BAL-SPUP-2026-069 or BE-URAN-Y67W"
                               style="text-transform:uppercase"
                               autocomplete="off">
                        @if($errors->has('unique_id'))
                            <span class="error-msg">{{ $errors->first('unique_id') }}</span>
                        @endif
                    </div>
                    <button type="submit" class="btn-login">Log in</button>
                </div>

            </form>
        </div>
    </div>

</div>

<script>
function validateLoginForm() {
    const userRole = document.getElementById('user_role').value;
    
    if (!userRole) {
        alert('Please select a role to continue.');
        return false;
    }
    
    if (userRole === 'beneficiary') {
        const uniqueId = document.getElementById('unique_id').value;
        if (!uniqueId.trim()) {
            alert('Please enter your Unique ID.');
            return false;
        }
    } else {
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;
        
        if (!email.trim()) {
            alert('Please enter your email address.');
            return false;
        }
        
        if (!password.trim()) {
            alert('Please enter your password.');
            return false;
        }
    }
    
    return true;
}

function selectRole(role) {
    document.getElementById('user_role').value = role;

    document.querySelectorAll('.role-box').forEach(b => b.classList.remove('selected'));
    document.querySelector('[data-role="' + role + '"]').classList.add('selected');

    document.getElementById('emailForm').classList.remove('visible');
    document.getElementById('idForm').classList.remove('visible');

    ['email', 'password', 'unique_id'].forEach(id => {
        const el = document.getElementById(id);
        if (el) { el.removeAttribute('required'); el.value = ''; }
    });

    if (role === 'beneficiary') {
        document.getElementById('idForm').classList.add('visible');
        document.getElementById('unique_id').setAttribute('required', 'required');
    } else {
        document.getElementById('emailForm').classList.add('visible');
        document.getElementById('email').setAttribute('required', 'required');
        document.getElementById('password').setAttribute('required', 'required');
    }
}

document.addEventListener('DOMContentLoaded', function () {
    const saved = document.getElementById('user_role').value;
    if (saved) selectRole(saved);
});
</script>

</body>
</html>
