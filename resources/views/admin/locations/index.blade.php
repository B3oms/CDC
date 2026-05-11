@extends('admin.layouts.app')

@section('title', 'Location Management')

@section('content')
<div class="min-h-screen bg-[#f5f3ee] p-6">

    {{-- ALERT --}}
    @if(session('error'))
        <div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 px-5 py-4 rounded-xl shadow-sm flex items-center gap-3">
            <i class="fas fa-exclamation-triangle"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    {{-- HEADER --}}
    <div class="bg-white rounded-2xl shadow-sm px-8 py-7 mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-4xl font-bold text-gray-800">
                Location Management
            </h1>
            <p class="text-gray-500 mt-1">
                Manage approved municipalities and barangays in the system
            </p>
        </div>

        <button onclick="loadLocationRequests()"
            class="bg-gray-500 hover:bg-gray-600 text-white px-5 py-3 rounded-xl font-semibold transition">
            <i class="fas fa-sync-alt mr-2"></i> Refresh
        </button>
    </div>

    {{-- STATS --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-5 mb-6">

        <div class="bg-white rounded-2xl p-6 shadow-sm text-center">
            <div class="text-5xl font-bold text-[#174d2c]">
                {{ $pendingMunicipalities ?? 0 }}
            </div>
            <div class="mt-2 text-gray-500">
                Pending Municipalities
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-sm text-center">
            <div class="text-5xl font-bold text-[#174d2c]">
                {{ $pendingBarangays ?? 0 }}
            </div>
            <div class="mt-2 text-gray-500">
                Pending Barangays
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-sm text-center">
            <div class="text-5xl font-bold text-[#174d2c]">
                {{ $approvedCount ?? 0 }}
            </div>
            <div class="mt-2 text-gray-500">
                Approved
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-sm text-center">
            <div class="text-5xl font-bold text-[#174d2c]">
                {{ $rejectedCount ?? 0 }}
            </div>
            <div class="mt-2 text-gray-500">
                Rejected
            </div>
        </div>

    </div>

    {{-- TABLE --}}
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">

        {{-- TOP BAR --}}
        <div class="px-6 py-5 border-b bg-[#f8f7f3] flex justify-between items-center">

            <div>
                <h2 class="text-3xl font-bold text-gray-800">
                    Location Requests
                </h2>

                <div class="flex items-center gap-3 mt-2">
                    <select id="statusFilter"
                        class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        <option value="">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                    </select>

                    <span class="text-gray-500 text-lg" id="totalCount">
                        0 total
                    </span>
                </div>
            </div>

            <div class="relative">
                <input type="text"
                    id="searchInput"
                    placeholder="Search..."
                    class="border border-gray-300 rounded-xl pl-10 pr-4 py-3 w-72">

                <i class="fas fa-search absolute left-3 top-4 text-gray-400"></i>
            </div>

        </div>

        {{-- TABLE CONTENT --}}
        <div class="overflow-x-auto">

            <table class="w-full">

                <thead class="bg-gray-50 text-gray-600 uppercase text-sm">
                    <tr>
                        <th class="px-6 py-4 text-left">Location Name</th>
                        <th class="px-6 py-4 text-left">Type</th>
                        <th class="px-6 py-4 text-left">Submitted By</th>
                        <th class="px-6 py-4 text-left">Status</th>
                        <th class="px-6 py-4 text-left">Submitted</th>
                        <th class="px-6 py-4 text-center">Actions</th>
                    </tr>
                </thead>

                <tbody id="locationTableBody" class="divide-y divide-gray-100">
                    {{-- JS LOAD --}}
                </tbody>

            </table>

        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    loadLocationRequests();

    document.getElementById('statusFilter')
        .addEventListener('change', loadLocationRequests);

    document.getElementById('searchInput')
        .addEventListener('input', loadLocationRequests);

});

function loadLocationRequests() {

    const status = document.getElementById('statusFilter').value;
    const search = document.getElementById('searchInput').value;

    fetch(`/admin/locations/data?status=${status}&search=${search}`)
        .then(response => response.json())
        .then(data => {

            const table = document.getElementById('locationTableBody');
            const total = document.getElementById('totalCount');

            table.innerHTML = '';
            total.innerText = data.requests.length + ' total';

            if (data.requests.length === 0) {

                table.innerHTML = `
                    <tr>
                        <td colspan="6" class="text-center py-10 text-gray-500">
                            No location requests found.
                        </td>
                    </tr>
                `;

                return;
            }

            data.requests.forEach(request => {

                let badge = `
                    <span class="px-3 py-1 rounded-full text-xs bg-yellow-100 text-yellow-700">
                        Pending
                    </span>
                `;

                if (request.status === 'approved') {
                    badge = `
                        <span class="px-3 py-1 rounded-full text-xs bg-green-100 text-green-700">
                            Approved
                        </span>
                    `;
                }

                if (request.status === 'rejected') {
                    badge = `
                        <span class="px-3 py-1 rounded-full text-xs bg-red-100 text-red-700">
                            Rejected
                        </span>
                    `;
                }

                table.innerHTML += `
                    <tr class="hover:bg-gray-50">

                        <td class="px-6 py-4 font-semibold text-gray-800">
                            ${request.name}
                        </td>

                        <td class="px-6 py-4 capitalize">
                            ${request.request_type}
                        </td>

                        <td class="px-6 py-4 text-green-500 font-medium">
                            ${request.requested_by_firstname ?? ''} ${request.requested_by_lastname ?? ''}
                        </td>

                        <td class="px-6 py-4">
                            ${badge}
                        </td>

                        <td class="px-6 py-4 text-gray-600">
                            ${formatDate(request.created_at)}
                        </td>

                        <td class="px-6 py-4">
                            <div class="flex justify-center gap-2">

                                ${
                                    request.status === 'pending'
                                    ? `
                                    <button onclick="approveRequest(${request.id}, '${request.request_type}')"
                                        class="bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded-lg text-sm">
                                        Approve
                                    </button>

                                    <button onclick="rejectRequest(${request.id}, '${request.request_type}')"
                                        class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg text-sm">
                                        Reject
                                    </button>
                                    `
                                    : ''
                                }

                                <button onclick="deleteRequest(${request.id}, '${request.request_type}')"
                                    class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm">
                                    Delete
                                </button>

                            </div>
                        </td>

                    </tr>
                `;
            });

        })
        .catch(error => {
            console.error(error);
        });
}

function formatDate(dateString) {

    const date = new Date(dateString);

    return date.toLocaleString('en-US', {
        month: 'short',
        day: '2-digit',
        year: 'numeric',
        hour: 'numeric',
        minute: '2-digit'
    });
}

function approveRequest(id, type) {

    if(confirm('Approve this request?')) {
        window.location.href =
        `/admin/locations/approve-${type}/${id}`;
    }
}

function rejectRequest(id, type) {

    let reason = prompt('Reason for rejection:');

    if(reason) {
        window.location.href =
        `/admin/locations/reject-${type}/${id}?reason=` + encodeURIComponent(reason);
    }
}

function deleteRequest(id, type) {

    if(confirm('Delete this request?')) {
        window.location.href =
        `/admin/locations/delete-${type}/${id}`;
    }
}
</script>
@endsection