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
        <div class="section-card">
            <h3>Submit Recommendation</h3>
            <form method="POST" action="{{ route('barangay.recommendations.store') }}">
                @csrf
                @if($errors->any())
                <div class="alert-error">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
                <div class="form-group" style="margin-bottom:10px;">
                    <label>First Name</label>
                    <input type="text" name="first_name" value="{{ old('first_name') }}" required
                        oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '')"
                        onkeypress="return (event.charCode >= 65 && event.charCode <= 90) || (event.charCode >= 97 && event.charCode <= 122) || event.charCode === 32">
                </div>
                <div class="form-group" style="margin-bottom:10px;">
                    <label>Middle Name (Optional)</label>
                    <input type="text" name="middle_name" value="{{ old('middle_name') }}"
                        oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '')"
                        onkeypress="return (event.charCode >= 65 && event.charCode <= 90) || (event.charCode >= 97 && event.charCode <= 122) || event.charCode === 32">
                </div>
                <div class="form-group" style="margin-bottom:10px;">
                    <label>Last Name</label>
                    <input type="text" name="last_name" value="{{ old('last_name') }}" required
                        oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '')"
                        onkeypress="return (event.charCode >= 65 && event.charCode <= 90) || (event.charCode >= 97 && event.charCode <= 122) || event.charCode === 32">
                </div>
                <div class="form-group" style="margin-bottom:10px;">
                    <label>Suffix (Optional)</label>
                    <select name="suffix">
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
                <div class="form-group" style="margin-bottom:10px;">
                    <label>Age</label>
                    <input type="number" name="age" value="{{ old('age') }}" min="0" max="120" required>
                </div>
                <div class="form-group" style="margin-bottom:10px;">
                    <label>Contact Number</label>
                    <input type="text" name="contact_number" value="{{ old('contact_number') }}"
                        placeholder="09XXXXXXXXX"
                        maxlength="11"
                        oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                        onkeypress="return (event.charCode >= 48 && event.charCode <= 57)">
                </div>
                <div class="form-group" style="margin-bottom:10px;">
                    <label>Address</label>
                    <textarea name="address" rows="2">{{ old('address') }}</textarea>
                </div>
                <button type="submit" class="btn-primary" style="width:100%;margin-top:8px;">
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