@extends('admin.layouts.app')
@section('title', 'Recommend Beneficiaries')

@section('content')
<div class="dash-header">
    <h1>Recommend Beneficiaries</h1>
</div>

@if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
@endif

<div class="dash-grid">
    <div class="yearly-col">
        <div class="section-card" style="padding: 1rem;">
            <h3 style="margin-bottom: 0.75rem; font-size: 0.9rem;">Submit Recommendation</h3>
            <form method="POST" action="{{ route('barangay.recommendations.store') }}">
                @csrf
                @if($errors->any())
                <div class="alert-error" style="margin-bottom: 0.75rem; padding: 0.5rem; font-size: 0.8rem;">
                    <ul style="margin: 0; padding-left: 1rem;">
                        @foreach($errors->all() as $error)
                            <li style="margin-bottom: 0.25rem;">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
                
                <div class="form-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem; margin-bottom: 0.5rem;">
                    <div class="form-group" style="margin-bottom: 0.5rem;">
                        <label style="font-size: 0.75rem; margin-bottom: 0.2rem; display: block;">First Name</label>
                        <input type="text" name="first_name" value="{{ old('first_name') }}" required
                            style="padding: 0.4rem; font-size: 0.8rem; border: 1px solid #ddd; border-radius: 4px; width: 100%;"
                            oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '')"
                            onkeypress="return (event.charCode >= 65 && event.charCode <= 90) || (event.charCode >= 97 && event.charCode <= 122) || event.charCode === 32">
                    </div>
                    <div class="form-group" style="margin-bottom: 0.5rem;">
                        <label style="font-size: 0.75rem; margin-bottom: 0.2rem; display: block;">Last Name</label>
                        <input type="text" name="last_name" value="{{ old('last_name') }}" required
                            style="padding: 0.4rem; font-size: 0.8rem; border: 1px solid #ddd; border-radius: 4px; width: 100%;"
                            oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '')"
                            onkeypress="return (event.charCode >= 65 && event.charCode <= 90) || (event.charCode >= 97 && event.charCode <= 122) || event.charCode === 32">
                    </div>
                </div>
                
                <div class="form-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem; margin-bottom: 0.5rem;">
                    <div class="form-group" style="margin-bottom: 0.5rem;">
                        <label style="font-size: 0.75rem; margin-bottom: 0.2rem; display: block;">Middle Name</label>
                        <input type="text" name="middle_name" value="{{ old('middle_name') }}"
                            style="padding: 0.4rem; font-size: 0.8rem; border: 1px solid #ddd; border-radius: 4px; width: 100%;"
                            oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '')"
                            onkeypress="return (event.charCode >= 65 && event.charCode <= 90) || (event.charCode >= 97 && event.charCode <= 122) || event.charCode === 32">
                    </div>
                    <div class="form-group" style="margin-bottom: 0.5rem;">
                        <label style="font-size: 0.75rem; margin-bottom: 0.2rem; display: block;">Suffix</label>
                        <select name="suffix" style="padding: 0.4rem; font-size: 0.8rem; border: 1px solid #ddd; border-radius: 4px; width: 100%;">
                            <option value="">-- None --</option>
                            <option value="Jr." {{ old('suffix') == 'Jr.' ? 'selected' : '' }}>Jr.</option>
                            <option value="Sr." {{ old('suffix') == 'Sr.' ? 'selected' : '' }}>Sr.</option>
                            <option value="I" {{ old('suffix') == 'I' ? 'selected' : '' }}>I</option>
                            <option value="II" {{ old('suffix') == 'II' ? 'selected' : '' }}>II</option>
                            <option value="III" {{ old('suffix') == 'III' ? 'selected' : '' }}>III</option>
                            <option value="IV" {{ old('suffix') == 'IV' ? 'selected' : '' }}>IV</option>
                            <option value="V" {{ old('suffix') == 'V' ? 'selected' : '' }}>V</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem; margin-bottom: 0.5rem;">
                    <div class="form-group" style="margin-bottom: 0.5rem;">
                        <label style="font-size: 0.75rem; margin-bottom: 0.2rem; display: block;">Age</label>
                        <input type="number" name="age" value="{{ old('age') }}" min="18" max="110" required
                            style="padding: 0.4rem; font-size: 0.8rem; border: 1px solid #ddd; border-radius: 4px; width: 100%;">
                    </div>
                    <div class="form-group" style="margin-bottom: 0.5rem;">
                        <label style="font-size: 0.75rem; margin-bottom: 0.2rem; display: block;">Contact</label>
                        <input type="text" name="contact_number" value="{{ old('contact_number') }}"
                            style="padding: 0.4rem; font-size: 0.8rem; border: 1px solid #ddd; border-radius: 4px; width: 100%;"
                            placeholder="09XXXXXXXXX"
                            maxlength="11"
                            oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                            onkeypress="return (event.charCode >= 48 && event.charCode <= 57)">
                    </div>
                </div>
                
                <div class="form-group" style="margin-bottom: 0.75rem;">
                    <label style="font-size: 0.75rem; margin-bottom: 0.2rem; display: block;">Address</label>
                    <textarea name="address" rows="2" required
                        style="padding: 0.4rem; font-size: 0.8rem; border: 1px solid #ddd; border-radius: 4px; width: 100%; resize: vertical;">{{ old('address') }}</textarea>
                </div>
                
                <button type="submit" class="btn-primary" style="width:100%; padding: 0.5rem; font-size: 0.8rem;">
                    Submit Recommendation
                </button>
            </form>
        </div>
    </div>

    <div class="right-col">
        <div class="section-card">
            <h3>Submitted Recommendations</h3>
            <table class="dist-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Contact</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recommendations as $r)
                    <tr>
                        <td>{{ $r->first_name }} {{ $r->last_name }}</td>
                        <td>{{ $r->contact_number ?? 'N/A' }}</td>
                        <td>
                            @if($r->status === 'Pending')
                                <span class="relief-status-badge upcoming">Pending</span>
                            @elseif($r->status === 'Converted')
                                <span class="relief-status-badge ongoing">Accepted</span>
                            @else
                                <span class="relief-status-badge done">Rejected</span>
                            @endif
                        </td>
                        <td>{{ $r->created_at->format('M d, Y') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" style="text-align:center;color:#888;padding:16px;">
                            No recommendations submitted yet.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection