@extends('admin.layouts.app')

@section('breadcrumb', 'Profile')

@section('content')
<div class="profile-wrapper">
    <div class="profile-card">
        {{-- Identity Row --}}
        <div class="profile-identity">
            <div class="profile-avatar">
                {{ strtoupper(substr($user->first_name,0,1).substr($user->last_name,0,1)) }}
            </div>
            <div class="profile-meta">
                <p class="profile-name">{{ $user->first_name }} {{ $user->last_name }}</p>
                <div class="profile-badges">
                    <span class="badge badge-role">
                        <i class="fas fa-shield-alt"></i>
                        {{ $user->role->name ?? 'Barangay Partner' }}
                    </span>
                    <span class="badge badge-status {{ $user->status == 'active' ? 'badge-active' : 'badge-inactive' }}">
                        <span class="dot"></span>
                        {{ ucfirst($user->status) }}
                    </span>
                </div>
            </div>
        </div>

        <div class="divider"></div>

        {{-- View Mode (Default) --}}
        <div id="viewMode">
            {{-- Personal Information --}}
            <div class="profile-section">
                <p class="section-title"><i class="fas fa-user"></i> Personal Information</p>
                <div class="info-grid">
                    <div class="info-item">
                        <p class="info-label">First name</p>
                        <p class="info-value">{{ $user->first_name }}</p>
                    </div>
                    <div class="info-item">
                        <p class="info-label">Last name</p>
                        <p class="info-value">{{ $user->last_name }}</p>
                    </div>
                    <div class="info-item">
                        <p class="info-label">Email address</p>
                        <p class="info-value info-email">{{ $user->email }}</p>
                    </div>
                    <div class="info-item">
                        <p class="info-label">Contact number</p>
                        <p class="info-value">{{ $user->contact_number ?? 'Not set' }}</p>
                    </div>
                    <div class="info-item">
                        <p class="info-label">Address</p>
                        <p class="info-value">{{ $user->address ?? 'Not set' }}</p>
                    </div>
                    <div class="info-item">
                        <p class="info-label">Birthdate</p>
                        <p class="info-value">{{ $user->birthdate ? \Carbon\Carbon::parse($user->birthdate)->format('M d, Y') : 'Not set' }}</p>
                    </div>
                </div>
            </div>

            <div class="divider"></div>

            {{-- Professional Information --}}
            <div class="profile-section">
                <p class="section-title"><i class="fas fa-briefcase"></i> Professional Information</p>
                <div class="info-grid">
                    <div class="info-item">
                        <p class="info-label">Position</p>
                        <p class="info-value">{{ $user->position ?? 'Not set' }}</p>
                    </div>
                    <div class="info-item">
                        <p class="info-label">Organization</p>
                        <p class="info-value">{{ $user->organization ?? 'Not set' }}</p>
                    </div>
                    <div class="info-item">
                        <p class="info-label">Role</p>
                        <p class="info-value">{{ $user->role->name ?? 'Barangay Partner' }}</p>
                    </div>
                    <div class="info-item">
                        <p class="info-label">Account status</p>
                        <p class="info-value {{ $user->status == 'active' ? 'text-success' : 'text-danger' }}">
                            <span class="dot"></span>
                            {{ ucfirst($user->status) }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="divider"></div>

            {{-- Account Information --}}
            <div class="profile-section">
                <p class="section-title"><i class="fas fa-clock"></i> Account Information</p>
                <div class="info-grid">
                    <div class="info-item">
                        <p class="info-label">Member since</p>
                        <p class="info-value">{{ $user->created_at->format('F d, Y') }}</p>
                    </div>
                    <div class="info-item">
                        <p class="info-label">Last updated</p>
                        <p class="info-value">{{ $user->updated_at->diffForHumans() }}</p>
                    </div>
                </div>
            </div>

            <div class="divider"></div>

            {{-- Actions --}}
            <div class="profile-actions">
                <a href="{{ route('barangay.dashboard') }}" class="btn btn-ghost">
                    <i class="fas fa-arrow-left"></i> Dashboard
                </a>
                <button type="button" onclick="showEditMode()" class="btn btn-primary">
                    <i class="fas fa-edit"></i> Edit Profile
                </button>
                <button type="button" onclick="showPasswordModal()" class="btn btn-secondary">
                    <i class="fas fa-key"></i> Change Password
                </button>
                <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-sign-out-alt"></i> Log out
                    </button>
                </form>
            </div>
        </div>

        {{-- Edit Mode --}}
        <div id="editMode" style="display: none;">
            <form method="POST" action="{{ route('barangay.profile.update') }}">
                @csrf
                @method('PUT')

                {{-- Personal Information --}}
                <div class="profile-section">
                    <p class="section-title"><i class="fas fa-user"></i> Personal Information</p>
                    <div class="info-grid">
                        <div class="info-item">
                            <p class="info-label">First name</p>
                            <input type="text" name="first_name" value="{{ $user->first_name }}" class="form-input" required>
                        </div>
                        <div class="info-item">
                            <p class="info-label">Last name</p>
                            <input type="text" name="last_name" value="{{ $user->last_name }}" class="form-input" required>
                        </div>
                        <div class="info-item">
                            <p class="info-label">Email address</p>
                            <input type="email" name="email" value="{{ $user->email }}" class="form-input" required>
                        </div>
                        <div class="info-item">
                            <p class="info-label">Contact number</p>
                            <input type="text" name="contact_number" value="{{ $user->contact_number ?? '' }}" class="form-input" placeholder="Enter contact number">
                        </div>
                        <div class="info-item">
                            <p class="info-label">Address</p>
                            <input type="text" name="address" value="{{ $user->address ?? '' }}" class="form-input" placeholder="Enter address">
                        </div>
                        <div class="info-item">
                            <p class="info-label">Birthdate</p>
                            <input type="date" name="birthdate" value="{{ $user->birthdate ? \Carbon\Carbon::parse($user->birthdate)->format('Y-m-d') : '' }}" class="form-input">
                        </div>
                    </div>
                </div>

                <div class="divider"></div>

                {{-- Professional Information --}}
                <div class="profile-section">
                    <p class="section-title"><i class="fas fa-briefcase"></i> Professional Information</p>
                    <div class="info-grid">
                        <div class="info-item">
                            <p class="info-label">Position</p>
                            <input type="text" name="position" value="{{ $user->position ?? '' }}" class="form-input" placeholder="Enter position">
                        </div>
                        <div class="info-item">
                            <p class="info-label">Organization</p>
                            <input type="text" name="organization" value="{{ $user->organization ?? '' }}" class="form-input" placeholder="Enter organization">
                        </div>
                        <div class="info-item">
                            <p class="info-label">Role</p>
                            <p class="info-value">{{ $user->role->name ?? 'Barangay Partner' }}</p>
                        </div>
                        <div class="info-item">
                            <p class="info-label">Account status</p>
                            <p class="info-value {{ $user->status == 'active' ? 'text-success' : 'text-danger' }}">
                                <span class="dot"></span>
                                {{ ucfirst($user->status) }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="divider"></div>

                {{-- Actions --}}
                <div class="profile-actions">
                    <button type="button" onclick="showViewMode()" class="btn btn-ghost">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Change Password Modal --}}
<div id="passwordModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-key"></i> Change Password</h3>
            <button type="button" onclick="closePasswordModal()" class="modal-close">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form method="POST" action="{{ route('barangay.profile.updatePassword') }}">
            @csrf
            @method('PUT')
            <div class="modal-body">
                <div class="form-group">
                    <label>Current Password</label>
                    <input type="password" name="current_password" class="form-input" required>
                </div>
                <div class="form-group">
                    <label>New Password</label>
                    <input type="password" name="new_password" class="form-input" required minlength="8">
                </div>
                <div class="form-group">
                    <label>Confirm New Password</label>
                    <input type="password" name="new_password_confirmation" class="form-input" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closePasswordModal()" class="btn btn-ghost">Cancel</button>
                <button type="submit" class="btn btn-primary">Update Password</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
<style>
/* ─── Layout ─────────────────────────────────────────── */
.profile-wrapper {
    width: 100%;
    margin: 1rem;
    padding: 0;
}

.profile-card {
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    width: 100%;
}

/* ─── Identity Row ───────────────────────────────────── */
.profile-identity {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.5rem;
    background: #f8f9fa;
    border-bottom: 1px solid #e5e7eb;
}

.profile-avatar {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: linear-gradient(135deg, #f8fafc, #e2e8f0);
    color: #475569;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    border: 1px solid #e2e8f0;
    transition: all 0.2s ease;
    position: relative;
    overflow: hidden;
}

.profile-avatar::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(245, 195, 0, 0.1), rgba(245, 195, 0, 0.05));
    opacity: 0;
    transition: opacity 0.2s ease;
}

.profile-avatar:hover::before {
    opacity: 1;
}

.profile-avatar:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.profile-meta {
    flex: 1;
    min-width: 0;
}

.profile-name {
    font-size: 22px;
    font-weight: 700;
    color: #1a3d1f;
    margin: 0 0 8px;
    font-family: 'Segoe UI', sans-serif;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
}

.profile-badges {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    align-items: center;
}

.profile-id {
    font-size: 12px;
    color: #6b7280;
    font-family: 'Segoe UI', sans-serif;
    white-space: nowrap;
    background: #f1efe8;
    padding: 4px 8px;
    border-radius: 4px;
}

/* ─── Badges ─────────────────────────────────────────── */
.badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 12px;
    font-weight: 500;
    padding: 4px 12px;
    border-radius: 20px;
    font-family: 'Segoe UI', sans-serif;
}

.badge-role {
    background: #1a3d1f;
    color: #ffffff;
}

.badge-role i {
    font-size: 10px;
}

.badge-active {
    background: #10b981;
    color: #ffffff;
}

.badge-inactive {
    background: #ef4444;
    color: #ffffff;
}

/* ─── Divider ────────────────────────────────────────── */
.divider {
    height: 1px;
    background: #e5e7eb;
}

/* ─── Sections ───────────────────────────────────────── */
.profile-section {
    padding: 1.5rem;
}

.section-title {
    font-size: 14px;
    font-weight: 600;
    color: #2c2c2a;
    margin: 0 0 1rem;
    display: flex;
    align-items: center;
    gap: 8px;
    font-family: 'Segoe UI', sans-serif;
}

.section-title i {
    color: #1a3d1f;
    font-size: 14px;
}

/* ─── Info Grid ──────────────────────────────────────── */
.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
}

.info-item {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.info-label {
    font-size: 12px;
    font-weight: 600;
    color: #6b7280;
    margin: 0;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-family: 'Segoe UI', sans-serif;
}

.info-value {
    font-size: 14px;
    color: #2c2c2a;
    margin: 0;
    font-family: 'Segoe UI', sans-serif;
}

.info-email {
    color: #1a3d1f;
    font-weight: 500;
}

.info-mono {
    font-family: 'Segoe UI', sans-serif;
    font-size: 13px;
    background: #f1efe8;
    padding: 2px 6px;
    border-radius: 4px;
    display: inline-block;
}

/* ─── Status dot ─────────────────────────────────────── */
.dot {
    display: inline-block;
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: currentColor;
    vertical-align: middle;
    margin-right: 5px;
}

.text-success {
    color: #10b981;
    display: flex;
    align-items: center;
    gap: 5px;
    font-weight: 500;
}

.text-danger {
    color: #ef4444;
    display: flex;
    align-items: center;
    gap: 5px;
    font-weight: 500;
}

/* ─── Actions ────────────────────────────────────────── */
.profile-actions {
    padding: 1.5rem;
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    flex-wrap: wrap;
    background: #f8f9fa;
    border-top: 1px solid #e5e7eb;
}

.btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    text-decoration: none;
    border: 1px solid #e5e7eb;
    background: #ffffff;
    color: #2c2c2a;
    transition: all 0.2s ease;
    font-family: 'Segoe UI', sans-serif;
}

.btn-ghost:hover {
    background: #1a3d1f;
    border-color: #1a3d1f;
    color: #ffffff;
}

.btn-danger {
    background: #ef4444;
    border-color: #ef4444;
    color: #ffffff;
}

.btn-danger:hover {
    background: #dc2626;
    border-color: #dc2626;
}

/* ─── Form Inputs ─────────────────────────────────────── */
.form-input {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    font-size: 14px;
    font-family: 'Segoe UI', sans-serif;
    color: #2c2c2a;
    background: #ffffff;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

.form-input:focus {
    outline: none;
    border-color: #1a3d1f;
    box-shadow: 0 0 0 3px rgba(26, 61, 31, 0.1);
}

.form-input::placeholder {
    color: #9ca3af;
}

.btn-primary {
    background: #1a3d1f;
    border-color: #1a3d1f;
    color: #ffffff;
}

.btn-primary:hover {
    background: #2d5a33;
    border-color: #2d5a33;
}

/* ─── Alert Styling ───────────────────────────────────── */
.alert {
    padding: 12px 16px;
    border-radius: 6px;
    display: flex;
    align-items: center;
    gap: 8px;
    font-family: 'Segoe UI', sans-serif;
    font-size: 14px;
}

.alert-success {
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    color: #15803d;
}

/* ─── Modal Styling ───────────────────────────────────── */
.modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
}

.modal-content {
    background: white;
    border-radius: 8px;
    width: 90%;
    max-height: 90vh;
    overflow-y: auto;
}

.modal-header {
    padding: 1.5rem;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-header h3 {
    margin: 0;
    font-size: 18px;
    font-weight: 600;
    color: #2c2c2a;
    font-family: 'Segoe UI', sans-serif;
    display: flex;
    align-items: center;
    gap: 8px;
}

.modal-header h3 i {
    color: #1a3d1f;
}

.modal-close {
    background: none;
    border: none;
    color: #6b7280;
    cursor: pointer;
    padding: 4px;
    border-radius: 4px;
    transition: all 0.2s ease;
}

.modal-close:hover {
    background: #f3f4f6;
    color: #2c2c2a;
}

.modal-body {
    padding: 1.5rem;
}

.form-group {
    margin-bottom: 1rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-size: 14px;
    font-weight: 500;
    color: #2c2c2a;
    font-family: 'Segoe UI', sans-serif;
}

.modal-footer {
    padding: 1.5rem;
    border-top: 1px solid #e5e7eb;
    display: flex;
    justify-content: flex-end;
    gap: 12px;
}

.btn-secondary {
    background: #6b7280;
    border-color: #6b7280;
    color: #ffffff;
}

.btn-secondary:hover {
    background: #4b5563;
    border-color: #4b5563;
}

/* ─── Responsive ─────────────────────────────────────── */
@media (max-width: 768px) {
    .profile-wrapper {
        margin: 0.5rem;
    }

    .profile-identity {
        flex-wrap: wrap;
    }

    .profile-id {
        width: 100%;
        margin-top: 0.5rem;
    }

    .profile-actions {
        flex-direction: column;
    }

    .btn {
        width: 100%;
        justify-content: center;
    }

    .info-grid {
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
    }
}

@media (max-width: 480px) {
    .profile-wrapper {
        margin: 0;
    }

    .profile-card {
        border-radius: 0;
        border-left: none;
        border-right: none;
    }

    .info-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
function showEditMode() {
    document.getElementById('viewMode').style.display = 'none';
    document.getElementById('editMode').style.display = 'block';
}

function showViewMode() {
    document.getElementById('editMode').style.display = 'none';
    document.getElementById('viewMode').style.display = 'block';
}

function showPasswordModal() {
    document.getElementById('passwordModal').style.display = 'flex';
}

function closePasswordModal() {
    document.getElementById('passwordModal').style.display = 'none';
}

// Close modal when clicking outside
document.addEventListener('click', function(e) {
    const modal = document.getElementById('passwordModal');
    if (e.target === modal) {
        closePasswordModal();
    }
});
</script>
@endpush
