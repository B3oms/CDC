<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Municipality;
use App\Models\Barangay;
use App\Models\MunicipalityRequest;
use App\Models\BarangayRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LocationController extends Controller
{
    /**
     * Display staff location requests page
     */
    public function index()
    {
        // Get user's submitted municipality requests
        $municipalityRequests = MunicipalityRequest::with(['requester'])
            ->where('requested_by', Auth::id())
            ->latest()
            ->get();

        // Get user's submitted barangay requests
        $barangayRequests = BarangayRequest::with(['requester', 'municipality'])
            ->where('requested_by', Auth::id())
            ->latest()
            ->get();

        // Get approved municipalities and barangays for display
        $approvedMunicipalities = Municipality::orderBy('name')->get();
        $approvedBarangays = Barangay::orderBy('name')->get();

        // All live municipalities with their barangays
        $municipalities = DB::table('municipalities')
            ->orderBy('name')
            ->get()
            ->map(function ($muni) {
                $muni->barangays = DB::table('barangays')
                    ->where('municipality_id', $muni->id)
                    ->orderBy('name')
                    ->get();
                return $muni;
            });

        // ── Pending requests (both roles see this) ──
        $pendingMuniRequests = DB::table('municipality_requests as mr')
            ->join('users as u', 'u.id', '=', 'mr.requested_by')
            ->where('mr.status', 'pending')
            ->select(
                'mr.*',
                'u.first_name as requested_by_firstname',
                'u.last_name  as requested_by_lastname'
            )
            ->orderBy('mr.created_at')
            ->get();

        $pendingBgyRequests = DB::table('barangay_requests as br')
            ->join('users as u', 'u.id', '=', 'br.requested_by')
            ->leftJoin('municipalities as m', 'm.id', '=', 'br.municipality_id')
            ->leftJoin('municipality_requests as mr', 'mr.id', '=', 'br.municipality_request_id')
            ->where('br.status', 'pending')
            ->select(
                'br.*',
                'u.first_name  as requested_by_firstname',
                'u.last_name   as requested_by_lastname',
                'm.name        as municipality_name',
                'mr.name       as pending_muni_name'
            )
            ->orderBy('br.created_at')
            ->get();

        // ── Approved / rejected history ──
        $approvedMuniRequests = DB::table('municipality_requests as mr')
            ->join('users as u',       'u.id',  '=', 'mr.requested_by')
            ->leftJoin('users as ru', 'ru.id', '=', 'mr.reviewed_by')
            ->where('mr.status', 'approved')
            ->select(
                'mr.*',
                'u.first_name   as requested_by_firstname',
                'u.last_name    as requested_by_lastname',
                'ru.first_name  as reviewed_by_firstname',
                'ru.last_name   as reviewed_by_lastname'
            )
            ->orderByDesc('mr.reviewed_at')
            ->get();

        $approvedBgyRequests = DB::table('barangay_requests as br')
            ->join('users as u',       'u.id',  '=', 'br.requested_by')
            ->leftJoin('users as ru', 'ru.id', '=', 'br.reviewed_by')
            ->leftJoin('municipalities as m', 'm.id', '=', 'br.municipality_id')
            ->where('br.status', 'approved')
            ->select(
                'br.*',
                'u.first_name   as requested_by_firstname',
                'u.last_name    as requested_by_lastname',
                'ru.first_name  as reviewed_by_firstname',
                'ru.last_name   as reviewed_by_lastname',
                'm.name         as municipality_name'
            )
            ->orderByDesc('br.reviewed_at')
            ->get();

        $rejectedMuniRequests = DB::table('municipality_requests as mr')
            ->join('users as u',       'u.id',  '=', 'mr.requested_by')
            ->leftJoin('users as ru', 'ru.id', '=', 'mr.reviewed_by')
            ->where('mr.status', 'rejected')
            ->select(
                'mr.*',
                'u.first_name   as requested_by_firstname',
                'u.last_name    as requested_by_lastname',
                'ru.first_name  as reviewed_by_firstname',
                'ru.last_name   as reviewed_by_lastname'
            )
            ->orderByDesc('mr.reviewed_at')
            ->get();

        $rejectedBgyRequests = DB::table('barangay_requests as br')
            ->join('users as u',       'u.id',  '=', 'br.requested_by')
            ->leftJoin('users as ru', 'ru.id', '=', 'br.reviewed_by')
            ->where('br.status', 'rejected')
            ->select(
                'br.*',
                'u.first_name   as requested_by_firstname',
                'u.last_name    as requested_by_lastname',
                'ru.first_name  as reviewed_by_firstname',
                'ru.last_name   as reviewed_by_lastname'
            )
            ->orderByDesc('br.reviewed_at')
            ->get();

        // ── Staff: their own submitted requests ──
        $user = auth()->user();
        $myPendingMuniRequests = DB::table('municipality_requests')
            ->where('requested_by', $user->id)
            ->where('status', 'pending')
            ->get();

        $muniReqs = DB::table('municipality_requests')
            ->where('requested_by', $user->id)
            ->select(
                DB::raw("'municipality' as type"),
                'id', 'name',
                DB::raw('province as details'),
                'status', 'rejection_note', 'created_at'
            )
            ->get();

        $bgyReqs = DB::table('barangay_requests as br')
            ->where('br.requested_by', $user->id)
            ->leftJoin('municipalities as m', 'm.id', '=', 'br.municipality_id')
            ->leftJoin('municipality_requests as mr', 'mr.id', '=', 'br.municipality_request_id')
            ->select(
                DB::raw("'barangay' as type"),
                'br.id', 'br.name',
                DB::raw("COALESCE(m.name, CONCAT('Pending: ', mr.name)) as details"),
                'br.status', 'br.rejection_note', 'br.created_at'
            )
            ->get();

        $myAllRequests  = $muniReqs->concat($bgyReqs)->sortByDesc('created_at');
        $myPendingCount = $myAllRequests->where('status', 'pending')->count();
        $pendingCount   = $pendingMuniRequests->count() + $pendingBgyRequests->count();

        return view('staff.locations.index', compact(
            'municipalities',
            'approvedBarangays',
            'pendingMuniRequests',   'pendingBgyRequests',
            'approvedMuniRequests',  'approvedBgyRequests',
            'rejectedMuniRequests',  'rejectedBgyRequests',
            'myPendingMuniRequests', 'myAllRequests',
            'myPendingCount',        'pendingCount'
        ));
    }

    // ──────────────────────────────────────────────────────
    // ORIGINAL: Store Municipality
    // Now submits a REQUEST instead of inserting directly.
    // Admin must approve before it appears in municipalities.
    // ──────────────────────────────────────────────────────
    public function storeMunicipality(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:100',
            'region'   => 'required|string|max:100',
        ]);

        // Check live duplicates
        $liveExists = DB::table('municipalities')
            ->whereRaw('LOWER(name) = ?', [strtolower($data['name'])])
            ->where('province', $data['region'])
            ->exists();

        if ($liveExists) {
            return back()
                ->withErrors(['name' => "'{$data['name']}' already exists in {$data['region']}."])
                ->withInput();
        }

        // Check pending duplicate
        $pendingExists = DB::table('municipality_requests')
            ->whereRaw('LOWER(name) = ?', [strtolower($data['name'])])
            ->where('province', $data['region'])
            ->where('status', 'pending')
            ->exists();

        if ($pendingExists) {
            return back()
                ->withErrors(['name' => "'{$data['name']}' is already pending approval in {$data['region']}."])
                ->withInput();
        }

        DB::table('municipality_requests')->insert([
            'name'         => $data['name'],
            'province'     => $data['region'],
            'requested_by' => Auth::id(),
            'status'       => 'pending',
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        return redirect()
            ->route('staff.locations.index', ['tab' => 'my_requests'])
            ->with('success', "Municipality request for '{$data['name']}' submitted. Waiting for admin approval.");
    }

    // ──────────────────────────────────────────────────────
    // ORIGINAL: Update Municipality (live record)
    // Only admin can update already-approved municipalities
    // ──────────────────────────────────────────────────────
    public function updateMunicipality(Request $request, $id)
    {
        if (Auth::user()->role_id != 1) {
            abort(403, 'Only Admin can edit approved municipalities.');
        }

        $data = $request->validate([
            'name'     => 'required|string|max:100',
            'region'   => 'required|string|max:100',
        ]);

        DB::table('municipalities')->where('id', $id)->update([
            'name'       => $data['name'],
            'province'   => $data['region'],
            'updated_at' => now(),
        ]);

        return back()->with('success', "Municipality updated successfully.");
    }

    // ──────────────────────────────────────────────────────
    // ORIGINAL: Delete Municipality (live record)
    // Only admin can delete
    // ──────────────────────────────────────────────────────
    public function destroyMunicipality($id)
    {
        if (Auth::user()->role_id != 1) {
            abort(403, 'Only Admin can delete municipalities.');
        }

        // Check if any barangays are linked
        $barangayCount = DB::table('barangays')
            ->where('municipality_id', $id)
            ->count();

        if ($barangayCount > 0) {
            return back()->with('error', "Cannot delete — this municipality has {$barangayCount} barangay(s) linked to it.");
        }

        $muni = DB::table('municipalities')->where('id', $id)->first();
        DB::table('municipalities')->where('id', $id)->delete();

        return back()->with('success', "Municipality '{$muni->name}' deleted.");
    }

    // ──────────────────────────────────────────────────────
    // ORIGINAL: Store Barangay
    // Now submits a REQUEST instead of inserting directly.
    // ──────────────────────────────────────────────────────
    public function storeBarangay(Request $request)
    {
        $request->validate([
            'name'               => 'required|string|max:100',
            'municipality_source'=> 'required|string',
        ]);

        $muniId    = $request->municipality_id         ?: null;
        $muniReqId = $request->municipality_request_id ?: null;

        if (!$muniId && !$muniReqId) {
            return back()
                ->withErrors(['municipality_source' => 'Please select a municipality.'])
                ->withInput();
        }

        // Duplicate check in live barangays
        if ($muniId) {
            $liveExists = DB::table('barangays')
                ->whereRaw('LOWER(name) = ?', [strtolower($request->name)])
                ->where('municipality_id', $muniId)
                ->exists();

            if ($liveExists) {
                return back()
                    ->withErrors(['name' => "Barangay '{$request->name}' already exists in this municipality."])
                    ->withInput();
            }
        }

        DB::table('barangay_requests')->insert([
            'name'                    => $request->name,
            'municipality_id'         => $muniId,
            'municipality_request_id' => $muniReqId,
            'requested_by'            => Auth::id(),
            'status'                  => 'pending',
            'created_at'              => now(),
            'updated_at'              => now(),
        ]);

        return redirect()
            ->route('staff.locations.index', ['tab' => 'my_requests'])
            ->with('success', "Barangay request for '{$request->name}' submitted. Waiting for admin approval.");
    }

    // ──────────────────────────────────────────────────────
    // ORIGINAL: Update Barangay (live record)
    // Only admin can update
    // ──────────────────────────────────────────────────────
    public function updateBarangay(Request $request, $id)
    {
        if (Auth::user()->role_id != 1) {
            abort(403, 'Only Admin can edit approved barangays.');
        }

        $data = $request->validate([
            'name'            => 'required|string|max:100',
            'municipality_id' => 'required|exists:municipalities,id',
        ]);

        DB::table('barangays')->where('id', $id)->update([
            'name'            => $data['name'],
            'municipality_id' => $data['municipality_id'],
            'updated_at'      => now(),
        ]);

        return back()->with('success', "Barangay updated successfully.");
    }

    // ──────────────────────────────────────────────────────
    // ORIGINAL: Delete Barangay (live record)
    // Only admin can delete
    // ──────────────────────────────────────────────────────
    public function destroyBarangay($id)
    {
        if (Auth::user()->role_id != 1) {
            abort(403, 'Only Admin can delete barangays.');
        }

        $bgy = DB::table('barangays')->where('id', $id)->first();
        DB::table('barangays')->where('id', $id)->delete();

        return back()->with('success', "Barangay '{$bgy->name}' deleted.");
    }

    // ──────────────────────────────────────────────────────
    // NEW: Admin approves a municipality request
    // ──────────────────────────────────────────────────────
    public function approveMunicipality(Request $request, $id)
    {
        if (Auth::user()->role_id != 1) {
            abort(403);
        }

        $req = DB::table('municipality_requests')
            ->where('id', $id)
            ->where('status', 'pending')
            ->first();

        if (!$req) {
            return back()->with('error', 'Request not found or already reviewed.');
        }

        DB::transaction(function () use ($req, $id) {
            // Insert into live municipalities
            $muniId = DB::table('municipalities')->insertGetId([
                'name'       => $req->name,
                'province'   => $req->province,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Mark request approved
            DB::table('municipality_requests')->where('id', $id)->update([
                'status'          => 'approved',
                'reviewed_by'     => Auth::id(),
                'reviewed_at'     => now(),
                'municipality_id' => $muniId,
                'updated_at'      => now(),
            ]);

            // Update any pending barangay requests that were waiting
            // on this municipality to be approved
            DB::table('barangay_requests')
                ->where('municipality_request_id', $id)
                ->where('status', 'pending')
                ->update([
                    'municipality_id'         => $muniId,
                    'municipality_request_id' => null,
                    'updated_at'              => now(),
                ]);
        });

        return redirect()
            ->route('staff.locations.index', ['tab' => 'pending'])
            ->with('success', "Municipality '{$req->name}' approved and is now live in the system.");
    }

    // ──────────────────────────────────────────────────────
    // NEW: Admin rejects a municipality request
    // ──────────────────────────────────────────────────────
    public function rejectMunicipality(Request $request, $id)
    {
        if (Auth::user()->role_id != 1) {
            abort(403);
        }

        $request->validate([
            'rejection_note' => 'required|string|max:500',
        ]);

        $req = DB::table('municipality_requests')->where('id', $id)->first();

        if (!$req) {
            return back()->with('error', 'Request not found.');
        }

        DB::table('municipality_requests')->where('id', $id)->update([
            'status'         => 'rejected',
            'reviewed_by'    => Auth::id(),
            'reviewed_at'    => now(),
            'rejection_note' => $request->rejection_note,
            'updated_at'     => now(),
        ]);

        // Also reject pending barangay requests tied to this municipality
        DB::table('barangay_requests')
            ->where('municipality_request_id', $id)
            ->where('status', 'pending')
            ->update([
                'status'         => 'rejected',
                'reviewed_by'    => Auth::id(),
                'reviewed_at'    => now(),
                'rejection_note' => 'Rejected because the linked municipality request was rejected.',
                'updated_at'     => now(),
            ]);

        return redirect()
            ->route('staff.locations.index', ['tab' => 'pending'])
            ->with('success', "Municipality request for '{$req->name}' rejected.");
    }

    // ──────────────────────────────────────────────────────
    // NEW: Admin approves a barangay request
    // ──────────────────────────────────────────────────────
    public function approveBarangay(Request $request, $id)
    {
        if (Auth::user()->role_id != 1) {
            abort(403);
        }

        $req = DB::table('barangay_requests')
            ->where('id', $id)
            ->where('status', 'pending')
            ->first();

        if (!$req) {
            return back()->with('error', 'Request not found or already reviewed.');
        }

        if (!$req->municipality_id) {
            return back()->with('error', 'Cannot approve yet — the linked municipality is still pending approval. Approve the municipality first.');
        }

        DB::transaction(function () use ($req, $id) {
            // Insert into live barangays
            $bgyId = DB::table('barangays')->insertGetId([
                'municipality_id' => $req->municipality_id,
                'name'            => $req->name,
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);

            // Mark request approved
            DB::table('barangay_requests')->where('id', $id)->update([
                'status'      => 'approved',
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now(),
                'barangay_id' => $bgyId,
                'updated_at'  => now(),
            ]);
        });

        return redirect()
            ->route('staff.locations.index', ['tab' => 'pending'])
            ->with('success', "Barangay '{$req->name}' approved and is now live in the system.");
    }

    // ──────────────────────────────────────────────────────
    // NEW: Admin rejects a barangay request
    // ──────────────────────────────────────────────────────
    public function rejectBarangay(Request $request, $id)
    {
        if (Auth::user()->role_id != 1) {
            abort(403);
        }

        $request->validate([
            'rejection_note' => 'required|string|max:500',
        ]);

        $req = DB::table('barangay_requests')->where('id', $id)->first();

        DB::table('barangay_requests')->where('id', $id)->update([
            'status'         => 'rejected',
            'reviewed_by'    => Auth::id(),
            'reviewed_at'    => now(),
            'rejection_note' => $request->rejection_note,
            'updated_at'     => now(),
        ]);

        return redirect()
            ->route('staff.locations.index', ['tab' => 'pending'])
            ->with('success', "Barangay request for '{$req->name}' rejected.");
    }
}