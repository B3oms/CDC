<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPUP-CDC | Login</title>
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            background-image:
                radial-gradient(circle at 20% 20%, rgba(26, 107, 42, 0.03) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(26, 107, 42, 0.02) 0%, transparent 50%),
                repeating-linear-gradient(
                    45deg,
                    transparent,
                    transparent 40px,
                    rgba(26, 107, 42, 0.008) 40px,
                    rgba(26, 107, 42, 0.008) 41px
                );
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }

        .login-wrap {
            display: flex;
            width: 100%;
            max-width: 900px;
            min-height: 520px;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
            border: 1px solid #e5e7eb;
        }

        /* ── Left Panel ── */
        .left-panel {
            flex: 1;
            background: #1a3d1f;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 2.5rem;
            min-width: 260px;
            border-right: 1px solid rgba(255,255,255,0.1);
        }

        .left-top-label {
            font-size: 0.68rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #a5d6a7;
            margin-bottom: 2rem;
        }

        .left-heading {
            color: #fff;
            font-size: 1.5rem;
            font-weight: 600;
            line-height: 1.4;
            margin-bottom: 0.75rem;
        }

        .left-body {
            color: #a5d6a7;
            font-size: 0.825rem;
            line-height: 1.7;
        }

        .left-footer {
            border-top: 1px solid rgba(255,255,255,0.1);
            padding-top: 1.25rem;
            display: flex;
            flex-direction: column;
            gap: 0.6rem;
        }

        .left-footer-item {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            color: #c8e6c9;
            font-size: 0.78rem;
        }

        .left-footer-item i { width: 14px; }

        /* ── Right Panel ── */
        .right-panel {
            flex: 1;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2.5rem;
        }

        .login-card {
            width: 100%;
            max-width: 400px;
        }

        /* Logo */
        .login-header {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1.75rem;
        }

        .logo-circle {
            width: 52px;
            height: 52px;
            background: #1a3d1f;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 20px;
            margin-bottom: 0.25rem;
        }

        .login-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #1f2937;
        }

        .login-subtitle {
            font-size: 0.8rem;
            color: #6b7280;
        }

        /* Section label */
        .section-label {
            font-size: 0.68rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.07em;
            color: #6b7280;
            margin-bottom: 0.6rem;
        }

        /* Role grid */
        .role-options {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
            margin-bottom: 1.25rem;
        }

        .role-box {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 12px;
            cursor: pointer;
            display: flex;
            align-items: flex-start;
            gap: 10px;
            transition: border-color .15s, background .15s;
        }

        .role-box:hover {
            border-color: #3b6d11;
            background: #eaf3de;
        }

        .role-box.selected {
            border: 1.5px solid #1a6b2a;
            background: #eaf3de;
        }

        .role-icon {
            width: 32px;
            height: 32px;
            background: #1a3d1f;
            color: #fff;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            flex-shrink: 0;
        }

        .role-info h4 {
            font-size: 13px;
            font-weight: 600;
            color: #1f2937;
            margin: 0 0 2px;
        }

        .role-info p {
            font-size: 11px;
            color: #6b7280;
            line-height: 1.4;
            margin: 0;
        }

        /* Divider */
        .form-divider {
            border: none;
            border-top: 1px solid #e5e7eb;
            margin: 1.25rem 0;
        }

        /* Fields */
        .field-group { margin-bottom: 1rem; }

        .field-group label {
            display: block;
            font-size: 0.825rem;
            font-weight: 500;
            color: #374151;
            margin-bottom: 0.35rem;
        }

        .field-group input {
            width: 100%;
            padding: 0.575rem 0.75rem;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            font-size: 0.875rem;
            color: #1f2937;
            background: #fff;
            outline: none;
            transition: border-color .15s, box-shadow .15s;
        }

        .field-group input:focus {
            border-color: #1a6b2a;
            box-shadow: 0 0 0 3px rgba(26,107,42,.08);
        }

        .field-group input::placeholder { color: #9ca3af; }

        .required { color: #a32d2d; margin-left: 2px; }

        .error-msg {
            display: block;
            font-size: 0.75rem;
            color: #a32d2d;
            margin-top: 4px;
        }

        /* Button */
        .btn-login {
            width: 100%;
            padding: 0.65rem 1rem;
            background: #1a3d1f;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 600;
            cursor: pointer;
            transition: background .15s;
            margin-top: 0.25rem;
        }

        .btn-login:hover:not(:disabled) { background: #27500a; }

        .btn-login:disabled {
            opacity: 0.45;
            cursor: not-allowed;
        }

        /* Form sections */
        .form-section { display: none; }
        .form-section.visible { display: block; }

        /* Responsive */
        @media (max-width: 700px) {
            .login-wrap {
                flex-direction: column;
                min-height: unset;
            }

            .left-panel {
                min-width: unset;
                padding: 1.5rem;
            }

            .left-footer { display: none; }

            .right-panel { padding: 1.5rem; }
        }

        @media (max-width: 480px) {
            body { padding: 0; }

            .login-wrap {
                border-radius: 0;
                border: none;
                box-shadow: none;
                min-height: 100vh;
            }

            .role-options { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

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

            <form method="POST" action="{{ route('login.post') }}" id="loginForm">
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
                               placeholder="e.g. BEN00123456"
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