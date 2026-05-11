<h2>Beneficiaries List</h2>

<table width="100%" border="1" cellspacing="0" cellpadding="5">
    <thead>
        <tr>
            <th>#</th>
            <th>Name</th>
            <th>Barangay</th>
            <th>Family Size</th>
            <th>Income</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($beneficiaries as $i => $b)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ $b->first_name }} {{ $b->last_name }}</td>
            <td>{{ $b->barangay->name ?? 'N/A' }}</td>
            <td>{{ $b->family_size }}</td>
            <td>₱{{ number_format($b->monthly_income, 0) }}</td>
            <td>{{ $b->is_verified ? 'Verified' : 'Pending' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>