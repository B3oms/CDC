@extends('admin.layouts.app')
@section('title', 'Add Staff')

@section('content')
<div class="dash-header">
    <h1>Add Staff / Partner</h1>
    <a href="{{ route('admin.staff.index') }}" class="btn-back">← Back</a>
</div>

<div class="form-card">
    @if($errors->any())
    <div class="alert-error">
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('admin.staff.store') }}">
        @csrf

        <div class="form-grid">

            <div class="form-group">
                <label>First Name</label>
                <input type="text" name="first_name" value="{{ old('first_name') }}" required>
            </div>

            <div class="form-group">
                <label>Middle Name (Optional)</label>
                <input type="text" name="middle_name" value="{{ old('middle_name') }}">
            </div>

            <div class="form-group">
                <label>Last Name</label>
                <input type="text" name="last_name" value="{{ old('last_name') }}" required>
            </div>

            <div class="form-group">
                <label>Suffix (Optional)</label>
                <input type="text" name="suffix" value="{{ old('suffix') }}" placeholder="e.g. Jr., Sr., III">
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required>
            </div>

            <div class="form-group">
                <label>Contact Number</label>
                <input type="text" name="contact_number" value="{{ old('contact_number') }}"
                    placeholder="09XXXXXXXXX" required maxlength="11" pattern="[0-9]{11}"
                    oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 11)"
                    onkeypress="return event.charCode >= 48 && event.charCode <= 57">
                <small style="color: #666; font-size: 12px;">Must be exactly 11 digits (numbers only)</small>
            </div>

            <div class="form-group">
                <label>Birthdate</label>
                <input type="date" name="birthdate" value="{{ old('birthdate') }}">
            </div>

            <div class="form-group">
                <label>Position</label>
                <input type="text" name="position" value="{{ old('position') }}"
                    placeholder="e.g. Team Leader">
            </div>

            <div class="form-group">
                <label>Role</label>
                <select name="role_id" id="role-select" required onchange="toggleFields()">
                    <option value="">-- Select Role --</option>
                    @foreach($roles as $role)
                    <option value="{{ $role->id }}"
                        data-name="{{ $role->name }}"
                        {{ old('role_id') == $role->id ? 'selected' : '' }}>
                        {{ $role->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Status</label>
                <select name="status" required>
                    <option value="active"   {{ old('status') == 'active'   ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

            <div class="form-group">
                <label>Temporary Password</label>
                <input type="text" name="password" value="{{ old('password') }}"
                    placeholder="Min. 6 characters" required>
            </div>

            {{-- Barangay (for Barangay Partner) --}}
            <div class="form-group" id="barangay-field" style="display:none;">
                <label>Barangay</label>
                <select name="barangay_id">
                    <option value="">-- Select Barangay --</option>
                    @foreach($barangays as $barangay)
                    <option value="{{ $barangay->id }}"
                        {{ old('barangay_id') == $barangay->id ? 'selected' : '' }}>
                        {{ $barangay->name }} — {{ $barangay->municipality->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            {{-- Organization (for Volunteer/Barangay Partner) --}}
            <div class="form-group" id="org-field" style="display:none;">
                <label>Organization</label>
                <input type="text" name="organization" value="{{ old('organization') }}"
                    placeholder="e.g. Red Cross, DSWD">
            </div>

            <div class="form-group full-width">
                <label>Address</label>
                <textarea name="address" rows="2"
                    placeholder="Full address">{{ old('address') }}</textarea>
            </div>

        </div>

        <div class="form-actions">
            <button type="submit" class="btn-primary">Create Account</button>
            <a href="{{ route('admin.staff.index') }}" class="btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function toggleFields() {
    const select    = document.getElementById('role-select');
    const selected  = select.options[select.selectedIndex];
    const roleName  = selected ? selected.dataset.name : '';
    const barangay  = document.getElementById('barangay-field');
    const org       = document.getElementById('org-field');

    barangay.style.display = roleName === 'Barangay Partner' ? 'flex' : 'none';
    org.style.display      = (roleName === 'Volunteer' || roleName === 'Barangay Partner') ? 'flex' : 'none';
}

// Run on page load to restore old() selections
toggleFields();
</script>
@endpush