<div class="staff-profile-card" onclick="showStaffModal({{ $user->id }})" style="cursor:pointer;">
    <div class="staff-profile-top">
        <div class="staff-avatar-lg">
            {{ strtoupper(substr($user->first_name,0,1).substr($user->last_name,0,1)) }}
        </div>
        <div class="staff-profile-info">
            <div class="staff-profile-name">{{ $user->first_name }} {{ $user->last_name }}</div>
            <div class="staff-profile-role">
                <span class="role-tag">{{ $user->role->name }}</span>
                @if($user->status === 'active')
                    <span class="status-dot active"></span>
                @else
                    <span class="status-dot inactive"></span>
                @endif
            </div>
            @if($user->position)
                <div class="staff-profile-position">{{ $user->position }}</div>
            @endif
            @if($user->barangay)
                <div class="staff-profile-sub">{{ $user->barangay->name }}</div>
            @endif
            @if($user->organization)
                <div class="staff-profile-sub">{{ $user->organization }}</div>
            @endif
        </div>
    </div>

    <div class="staff-profile-actions" onclick="event.stopPropagation()">
        <a href="{{ route('admin.staff.edit', $user->id) }}" class="btn-sm-secondary">Edit</a>

        <form method="POST" action="{{ route('admin.staff.resetPassword', $user->id) }}"
            style="display:inline;"
            onsubmit="return confirm('Reset password for {{ $user->first_name }}?')">
            @csrf
            <button type="submit" class="btn-sm-warning">Reset Password</button>
        </form>

        <form method="POST" action="{{ route('admin.staff.destroy', $user->id) }}"
            style="display:inline;"
            onsubmit="return confirm('Delete {{ $user->first_name }} {{ $user->last_name }}?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn-sm-danger">Delete</button>
        </form>
    </div>

    {{-- Hidden data for modal --}}
    <div class="staff-modal-data" style="display:none;"
        data-id="{{ $user->id }}"
        data-name="{{ $user->first_name }} {{ $user->last_name }}"
        data-role="{{ $user->role->name }}"
        data-status="{{ $user->status }}"
        data-position="{{ $user->position ?? 'N/A' }}"
        data-email="{{ $user->email }}"
        data-contact="{{ $user->contact_number }}"
        data-address="{{ $user->address ?? 'N/A' }}"
        data-birthdate="{{ $user->birthdate ? \Carbon\Carbon::parse($user->birthdate)->format('M d, Y') : 'N/A' }}"
        data-barangay="{{ $user->barangay->name ?? 'N/A' }}"
        data-organization="{{ $user->organization ?? 'N/A' }}"
        data-initials="{{ strtoupper(substr($user->first_name,0,1).substr($user->last_name,0,1)) }}">
    </div>
</div>