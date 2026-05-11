@extends('admin.layouts.app')
@section('title', 'Staff')

@section('content')
<div class="dash-header">
    <h1>Staff Management</h1>
    <a href="{{ route('admin.staff.create') }}" class="btn-primary">+ Create</a>
</div>

@if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert-error">{{ session('error') }}</div>
@endif

{{-- SPUP-CDC Staffs --}}
<div class="relief-section">
    <div class="relief-section-title">SPUP-CDC Staffs</div>
    @if($staffs->isEmpty())
    <div class="section-card" style="text-align:center;padding:2rem;">
        <p style="color:#888;">No staff added yet.</p>
    </div>
    @else
    <div class="staff-card-grid">
        @foreach($staffs as $staff)
            @include('admin.staff.partials.card', ['user' => $staff])
        @endforeach
    </div>
    @endif
</div>

{{-- Partners --}}
<div class="relief-section">
    <div class="relief-section-title">Partners</div>
    @if($partners->isEmpty())
    <div class="section-card" style="text-align:center;padding:2rem;">
        <p style="color:#888;">No partners added yet.</p>
    </div>
    @else
    <div class="staff-card-grid">
        @foreach($partners as $partner)
            @include('admin.staff.partials.card', ['user' => $partner])
        @endforeach
    </div>
    @endif
</div>

{{-- Staff Detail Modal --}}
<div id="staff-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.45);z-index:999;align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:14px;width:100%;max-width:480px;margin:1rem;overflow:hidden;">

        {{-- Modal Header --}}
        <div style="background:#1a3d1f;padding:1.5rem;display:flex;align-items:center;gap:14px;">
            <div id="modal-avatar"
                style="width:56px;height:56px;border-radius:50%;background:#f5c300;color:#1a3d1f;
                display:flex;align-items:center;justify-content:center;font-size:18px;font-weight:700;flex-shrink:0;">
            </div>
            <div>
                <div id="modal-name" style="font-size:1.1rem;font-weight:700;color:#fff;"></div>
                <div style="display:flex;align-items:center;gap:8px;margin-top:4px;">
                    <span id="modal-role" style="font-size:11px;background:rgba(255,255,255,0.2);color:#fff;padding:2px 8px;border-radius:4px;"></span>
                    <span id="modal-status-dot"
                        style="width:7px;height:7px;border-radius:50%;display:inline-block;"></span>
                    <span id="modal-status" style="font-size:11px;color:rgba(255,255,255,0.7);"></span>
                </div>
            </div>
            <button onclick="closeStaffModal()"
                style="margin-left:auto;background:rgba(255,255,255,0.15);border:none;color:#fff;
                width:30px;height:30px;border-radius:50%;font-size:16px;cursor:pointer;flex-shrink:0;">
                ✕
            </button>
        </div>

        {{-- Modal Body --}}
        <div style="padding:1.5rem;">
            <table style="width:100%;border-collapse:collapse;font-size:13px;">
                <tr>
                    <td style="color:#888780;padding:7px 0;width:120px;border-bottom:1px solid #f1efe8;">Position</td>
                    <td id="modal-position" style="padding:7px 0;border-bottom:1px solid #f1efe8;font-weight:500;"></td>
                </tr>
                <tr>
                    <td style="color:#888780;padding:7px 0;border-bottom:1px solid #f1efe8;">Email</td>
                    <td id="modal-email" style="padding:7px 0;border-bottom:1px solid #f1efe8;"></td>
                </tr>
                <tr>
                    <td style="color:#888780;padding:7px 0;border-bottom:1px solid #f1efe8;">Contact</td>
                    <td id="modal-contact" style="padding:7px 0;border-bottom:1px solid #f1efe8;"></td>
                </tr>
                <tr>
                    <td style="color:#888780;padding:7px 0;border-bottom:1px solid #f1efe8;">Birthdate</td>
                    <td id="modal-birthdate" style="padding:7px 0;border-bottom:1px solid #f1efe8;"></td>
                </tr>
                <tr>
                    <td style="color:#888780;padding:7px 0;border-bottom:1px solid #f1efe8;">Barangay</td>
                    <td id="modal-barangay" style="padding:7px 0;border-bottom:1px solid #f1efe8;"></td>
                </tr>
                <tr>
                    <td style="color:#888780;padding:7px 0;border-bottom:1px solid #f1efe8;">Organization</td>
                    <td id="modal-organization" style="padding:7px 0;border-bottom:1px solid #f1efe8;"></td>
                </tr>
                <tr>
                    <td style="color:#888780;padding:7px 0;">Address</td>
                    <td id="modal-address" style="padding:7px 0;"></td>
                </tr>
            </table>
        </div>

        {{-- Modal Footer --}}
        <div style="padding:1rem 1.5rem;border-top:1px solid #f1efe8;display:flex;justify-content:flex-end;gap:8px;">
            <button onclick="closeStaffModal()" class="btn-secondary" style="font-size:13px;">Close</button>
            <a id="modal-edit-link" href="#" class="btn-primary" style="font-size:13px;">Edit</a>
        </div>
    </div>
</div>
@push('scripts')
<script>
function showStaffModal(id) {
    const data = document.querySelector(`.staff-modal-data[data-id="${id}"]`);
    if (!data) return;

    document.getElementById('modal-avatar').textContent      = data.dataset.initials;
    document.getElementById('modal-name').textContent        = data.dataset.name;
    document.getElementById('modal-role').textContent        = data.dataset.role;
    document.getElementById('modal-position').textContent    = data.dataset.position;
    document.getElementById('modal-email').textContent       = data.dataset.email;
    document.getElementById('modal-contact').textContent     = data.dataset.contact;
    document.getElementById('modal-birthdate').textContent   = data.dataset.birthdate;
    document.getElementById('modal-barangay').textContent    = data.dataset.barangay;
    document.getElementById('modal-organization').textContent= data.dataset.organization;
    document.getElementById('modal-address').textContent     = data.dataset.address;

    const statusDot = document.getElementById('modal-status-dot');
    const statusTxt = document.getElementById('modal-status');
    if (data.dataset.status === 'active') {
        statusDot.style.background = '#639922';
        statusTxt.textContent = 'Active';
    } else {
        statusDot.style.background = '#e24b4a';
        statusTxt.textContent = 'Inactive';
    }

    document.getElementById('modal-edit-link').href =
        `/admin/staff/${id}/edit`;

    const modal = document.getElementById('staff-modal');
    modal.style.display = 'flex';
}

function closeStaffModal() {
    document.getElementById('staff-modal').style.display = 'none';
}

// Close on backdrop click
document.getElementById('staff-modal').addEventListener('click', function(e) {
    if (e.target === this) closeStaffModal();
});
</script>
@endpush
@endsection