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
                    <input type="text" name="first_name" value="{{ old('first_name') }}" required>
                </div>
                <div class="form-group" style="margin-bottom:10px;">
                    <label>Last Name</label>
                    <input type="text" name="last_name" value="{{ old('last_name') }}" required>
                </div>
                <div class="form-group" style="margin-bottom:10px;">
                    <label>Contact Number</label>
                    <input type="text" name="contact_number" value="{{ old('contact_number') }}"
                        placeholder="09XXXXXXXXX">
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