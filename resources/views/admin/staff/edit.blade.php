@extends('admin.layouts.app')
@section('title', 'Edit Staff')

@section('content')
<div class="dash-header">
    <h1>Edit — {{ $user->first_name }} {{ $user->last_name }}</h1>
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

    <form method="POST" action="{{ route('admin.staff.update', $user->id) }}">
        @csrf
        @method('PUT')

        <div class="form-grid">

            <div class="form-group">
                <label>First Name</label>
                <input type="text" name="first_name"
                    value="{{ old('first_name', $user->first_name) }}" required
                    oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '')"
                    onkeypress="return (event.charCode >= 65 && event.charCode <= 90) || (event.charCode >= 97 && event.charCode <= 122) || event.charCode === 32">
            </div>

            <div class="form-group">
                <label>Middle Name (Optional)</label>
                <input type="text" name="middle_name"
                    value="{{ old('middle_name', $user->middle_name) }}"
                    oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '')"
                    onkeypress="return (event.charCode >= 65 && event.charCode <= 90) || (event.charCode >= 97 && event.charCode <= 122) || event.charCode === 32">
            </div>

            <div class="form-group">
                <label>Last Name</label>
                <input type="text" name="last_name"
                    value="{{ old('last_name', $user->last_name) }}" required
                    oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '')"
                    onkeypress="return (event.charCode >= 65 && event.charCode <= 90) || (event.charCode >= 97 && event.charCode <= 122) || event.charCode === 32">
            </div>

            <div class="form-group">
                <label>Suffix (Optional)</label>
                <select name="suffix">
                    <option value="">-- None --</option>
                    <option value="Jr." {{ old('suffix', $user->suffix) == 'Jr.' ? 'selected' : '' }}>Jr.</option>
                    <option value="Sr." {{ old('suffix', $user->suffix) == 'Sr.' ? 'selected' : '' }}>Sr.</option>
                    <option value="I" {{ old('suffix', $user->suffix) == 'I' ? 'selected' : '' }}>I</option>
                    <option value="II" {{ old('suffix', $user->suffix) == 'II' ? 'selected' : '' }}>II</option>
                    <option value="III" {{ old('suffix', $user->suffix) == 'III' ? 'selected' : '' }}>III</option>
                    <option value="IV" {{ old('suffix', $user->suffix) == 'IV' ? 'selected' : '' }}>IV</option>
                    <option value="V" {{ old('suffix', $user->suffix) == 'V' ? 'selected' : '' }}>V</option>
                </select>
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email"
                    value="{{ old('email', $user->email) }}" required>
            </div>

            <div class="form-group">
                <label>Contact Number</label>
                <input type="text" name="contact_number"
                    value="{{ old('contact_number', $user->contact_number) }}" required maxlength="11" pattern="[0-9]{11}"
                    oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 11)"
                    onkeypress="return event.charCode >= 48 && event.charCode <= 57">
                <small style="color: #666; font-size: 12px;">Must be exactly 11 digits (numbers only)</small>
            </div>

            <div class="form-group">
                <label>Birthdate</label>
                <input type="date" name="birthdate"
                    value="{{ old('birthdate', $user->birthdate) }}">
            </div>

            <div class="form-group">
                <label>Position</label>
                <input type="text" name="position"
                    value="{{ old('position', $user->position) }}"
                    placeholder="e.g. Team Leader">
            </div>

            <div class="form-group">
                <label>Role</label>
                <select name="role_id" id="role-select" required onchange="toggleFields()">
                    @foreach($roles as $role)
                    <option value="{{ $role->id }}"
                        data-name="{{ $role->name }}"
                        {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                        {{ $role->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Status</label>
                <select name="status" required>
                    <option value="active"   {{ old('status', $user->status) == 'active'   ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status', $user->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

            <div class="form-group" id="barangay-field" style="display:none;">
                <label>Barangay</label>
                <select name="barangay_id">
                    <option value="">-- Select Barangay --</option>
                    @foreach($barangays as $barangay)
                    <option value="{{ $barangay->id }}"
                        {{ old('barangay_id', $user->barangay_id) == $barangay->id ? 'selected' : '' }}>
                        {{ $barangay->name }} — {{ $barangay->municipality->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group" id="org-field" style="display:none;">
                <label>Organization</label>
                <input type="text" name="organization"
                    value="{{ old('organization', $user->organization) }}"
                    placeholder="e.g. Red Cross, DSWD">
            </div>

            <div class="form-group full-width">
                <label>Address</label>
                <textarea name="address" rows="2">{{ old('address', $user->address) }}</textarea>
            </div>

        </div>

        <div class="form-actions">
            <button type="submit" class="btn-primary">Save Changes</button>
            <a href="{{ route('admin.staff.index') }}" class="btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function toggleFields() {
    const select   = document.getElementById('role-select');
    const selected = select.options[select.selectedIndex];
    const roleName = selected ? selected.dataset.name : '';
    const barangay = document.getElementById('barangay-field');
    const org      = document.getElementById('org-field');

    barangay.style.display = roleName === 'Barangay Partner' ? 'flex' : 'none';
    org.style.display      = (roleName === 'Volunteer' || roleName === 'Barangay Partner') ? 'flex' : 'none';
}

toggleFields();
</script>
@endpush