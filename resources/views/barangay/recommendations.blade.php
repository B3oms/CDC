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
            <form method="POST" action="{{ isset($recommendation) ? route('barangay.recommendations.update', $recommendation->id) : route('barangay.recommendations.store') }}">
                @csrf
                @if(isset($recommendation))
                    @method('PUT')
                @endif
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
                    <input type="text" name="first_name" value="{{ old('first_name', $recommendation->first_name ?? '') }}" required>
                </div>
                <div class="form-group" style="margin-bottom:10px;">
                    <label>Middle Name</label>
                    <input type="text" name="middle_name" value="{{ old('middle_name', $recommendation->middle_name ?? '') }}">
                </div>
                <div class="form-group" style="margin-bottom:10px;">
                    <label>Last Name</label>
                    <input type="text" name="last_name" value="{{ old('last_name', $recommendation->last_name ?? '') }}" required>
                </div>
                <div class="form-group" style="margin-bottom:10px;">
                    <label>Gender</label>
                    <select name="gender" required>
                        <option value="">Select gender</option>
                        <option value="Male" {{ old('gender', $recommendation->gender ?? '') === 'Male' ? 'selected' : '' }}>Male</option>
                        <option value="Female" {{ old('gender', $recommendation->gender ?? '') === 'Female' ? 'selected' : '' }}>Female</option>
                    </select>
                </div>
                <div class="form-group" style="margin-bottom:10px;">
                    <label>Age</label>
                    <input type="number" name="age" value="{{ old('age', $recommendation->age ?? '') }}" min="0" max="120" required>
                </div>
                <div class="form-group" style="margin-bottom:10px;">
                    <label>Contact Number</label>
                    <input type="text" name="contact_number" value="{{ old('contact_number', $recommendation->contact_number ?? '') }}"
                        placeholder="09XXXXXXXXX">
                </div>
                <div class="form-group" style="margin-bottom:10px;">
                    <label>Address</label>
                    <textarea name="address" rows="2">{{ old('address', $recommendation->address ?? '') }}</textarea>
                </div>
                <div class="form-group" style="margin-bottom:10px;">
                    <label>Description / Notes</label>
                    <textarea name="notes" rows="3" placeholder="Add any additional details or context">{{ old('notes', $recommendation->notes ?? '') }}</textarea>
                </div>
                <button type="submit" class="btn-primary" style="width:100%;margin-top:8px;">
                    {{ isset($recommendation) ? 'Update Recommendation' : 'Submit Recommendation' }}
                </button>
                @if(isset($recommendation))
                    <a href="{{ route('barangay.recommendations.index') }}" class="btn-secondary" style="display:block;text-align:center;margin-top:8px;">Cancel Edit</a>
                @endif
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
                        <th>Gender</th>
                        <th>Age</th>
                        <th>Contact</th>
                        <th>Description / Notes</th>
                        <th>Date Sent</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recommendations as $r)
                    <tr>
                        <td>{{ $r->first_name }}{{ $r->middle_name ? ' ' . $r->middle_name : '' }} {{ $r->last_name }}</td>
                        <td>{{ $r->gender ?? 'N/A' }}</td>
                        <td>{{ $r->age ?? 'N/A' }}</td>
                        <td>{{ $r->contact_number ?? 'N/A' }}</td>
                        <td>{{ $r->notes ? \Illuminate\Support\Str::limit($r->notes, 80) : 'N/A' }}</td>
                        <td>{{ $r->created_at->format('M d, Y') }}</td>
                        <td>
                            @if($r->submitted_by === auth()->id())
                                <a href="{{ route('barangay.recommendations.edit', $r->id) }}" class="btn-link">Edit</a>
                                <form method="POST" action="{{ route('barangay.recommendations.destroy', $r->id) }}" style="display:inline-block;margin-left:6px;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-link" style="color:#d00;">Delete</button>
                                </form>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align:center;color:#888;padding:16px;">
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