<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LocationApprovalsController extends Controller
{
    public function index()
    {
        $municipalities = DB::table('municipalities')
            ->orderBy('name')
            ->get()
            ->map(function ($m) {

                $m->barangays = DB::table('barangays')
                    ->where('municipality_id', $m->id)
                    ->orderBy('name')
                    ->get();

                return $m;
            });

        // =========================
        // PENDING MUNICIPALITIES
        // =========================

        $pendingMuniRequests = DB::table('municipality_requests as mr')
            ->join('users as u', 'u.id', '=', 'mr.requested_by')
            ->where('mr.status', 'pending')
            ->select(
                'mr.*',
                'u.first_name as requested_by_firstname',
                'u.last_name as requested_by_lastname'
            )
            ->orderBy('mr.created_at')
            ->get();

        // =========================
        // PENDING BARANGAYS
        // =========================

        $pendingBgyRequests = DB::table('barangay_requests as br')
            ->join('users as u', 'u.id', '=', 'br.requested_by')
            ->leftJoin('municipalities as m', 'm.id', '=', 'br.municipality_id')
            ->leftJoin('municipality_requests as mr', 'mr.id', '=', 'br.municipality_request_id')
            ->where('br.status', 'pending')
            ->select(
                'br.*',
                'u.first_name as requested_by_firstname',
                'u.last_name as requested_by_lastname',
                'm.name as municipality_name',
                'mr.name as pending_muni_name'
            )
            ->orderBy('br.created_at')
            ->get();

        // =========================
        // APPROVED MUNICIPALITIES
        // =========================

        $approvedMuniRequests = DB::table('municipality_requests as mr')
            ->join('users as u', 'u.id', '=', 'mr.requested_by')
            ->leftJoin('users as ru', 'ru.id', '=', 'mr.reviewed_by')
            ->where('mr.status', 'approved')
            ->select(
                'mr.*',
                'u.first_name as requested_by_firstname',
                'u.last_name as requested_by_lastname',
                'ru.first_name as reviewed_by_firstname',
                'ru.last_name as reviewed_by_lastname'
            )
            ->orderByDesc('mr.reviewed_at')
            ->get();

        // =========================
        // APPROVED BARANGAYS
        // =========================

        $approvedBgyRequests = DB::table('barangay_requests as br')
            ->join('users as u', 'u.id', '=', 'br.requested_by')
            ->leftJoin('users as ru', 'ru.id', '=', 'br.reviewed_by')
            ->leftJoin('municipalities as m', 'm.id', '=', 'br.municipality_id')
            ->where('br.status', 'approved')
            ->select(
                'br.*',
                'u.first_name as requested_by_firstname',
                'u.last_name as requested_by_lastname',
                'ru.first_name as reviewed_by_firstname',
                'ru.last_name as reviewed_by_lastname',
                'm.name as municipality_name'
            )
            ->orderByDesc('br.reviewed_at')
            ->get();

        // =========================
        // REJECTED MUNICIPALITIES
        // =========================

        $rejectedMuniRequests = DB::table('municipality_requests as mr')
            ->join('users as u', 'u.id', '=', 'mr.requested_by')
            ->leftJoin('users as ru', 'ru.id', '=', 'mr.reviewed_by')
            ->where('mr.status', 'rejected')
            ->select(
                'mr.*',
                'u.first_name as requested_by_firstname',
                'u.last_name as requested_by_lastname',
                'ru.first_name as reviewed_by_firstname',
                'ru.last_name as reviewed_by_lastname'
            )
            ->orderByDesc('mr.reviewed_at')
            ->get();

        // =========================
        // REJECTED BARANGAYS
        // =========================

        $rejectedBgyRequests = DB::table('barangay_requests as br')
            ->join('users as u', 'u.id', '=', 'br.requested_by')
            ->leftJoin('users as ru', 'ru.id', '=', 'br.reviewed_by')
            ->where('br.status', 'rejected')
            ->select(
                'br.*',
                'u.first_name as requested_by_firstname',
                'u.last_name as requested_by_lastname',
                'ru.first_name as reviewed_by_firstname',
                'ru.last_name as reviewed_by_lastname'
            )
            ->orderByDesc('br.reviewed_at')
            ->get();

        return view('admin.locations.approvals', compact(
            'municipalities',
            'pendingMuniRequests',
            'pendingBgyRequests',
            'approvedMuniRequests',
            'approvedBgyRequests',
            'rejectedMuniRequests',
            'rejectedBgyRequests'
        ));
    }

    // ======================================================
    // APPROVE MUNICIPALITY
    // ======================================================

    public function approveMunicipality($id)
    {
        $request = DB::table('municipality_requests')
            ->where('id', $id)
            ->first();

        if (!$request) {
            return back()->with('error', 'Request not found.');
        }

        // CREATE MUNICIPALITY
        DB::table('municipalities')->insert([
            'name' => $request->name,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // UPDATE REQUEST STATUS
        DB::table('municipality_requests')
            ->where('id', $id)
            ->update([
                'status' => 'approved',
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now(),
                'updated_at' => now(),
            ]);

        return back()->with(
            'success',
            'Municipality request approved successfully.'
        );
    }

    // ======================================================
    // REJECT MUNICIPALITY
    // ======================================================

    public function rejectMunicipality(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:255'
        ]);

        DB::table('municipality_requests')
            ->where('id', $id)
            ->update([
                'status' => 'rejected',
                'rejection_reason' => $request->rejection_reason,
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now(),
                'updated_at' => now(),
            ]);

        return back()->with(
            'success',
            'Municipality request rejected.'
        );
    }

    // ======================================================
    // APPROVE BARANGAY
    // ======================================================

    public function approveBarangay($id)
    {
        $request = DB::table('barangay_requests')
            ->where('id', $id)
            ->first();

        if (!$request) {
            return back()->with('error', 'Request not found.');
        }

        DB::table('barangays')->insert([
            'name' => $request->name,
            'municipality_id' => $request->municipality_id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('barangay_requests')
            ->where('id', $id)
            ->update([
                'status' => 'approved',
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now(),
                'updated_at' => now(),
            ]);

        return back()->with(
            'success',
            'Barangay request approved successfully.'
        );
    }

    // ======================================================
    // REJECT BARANGAY
    // ======================================================

    public function rejectBarangay(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:255'
        ]);

        DB::table('barangay_requests')
            ->where('id', $id)
            ->update([
                'status' => 'rejected',
                'rejection_reason' => $request->rejection_reason,
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now(),
                'updated_at' => now(),
            ]);

        return back()->with(
            'success',
            'Barangay request rejected.'
        );
    }
}