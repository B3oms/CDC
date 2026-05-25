<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Barangay;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StaffController extends Controller
{
    public function index()
    {
        $staffs = User::with(['role', 'barangay'])
            ->whereHas('role', fn($q) => $q->where('name', 'Staff'))
            ->get();

        $partners = User::with(['role', 'barangay'])
            ->whereHas('role', fn($q) => $q->whereIn('name', ['Barangay Partner', 'Volunteer']))
            ->get();

        return view('admin.staff.index', compact('staffs', 'partners'));
    }

    public function create()
    {
        $roles     = Role::whereIn('name', ['Staff', 'Barangay Partner', 'Volunteer'])->get();
        $barangays = Barangay::with('municipality')->get();

        return view('admin.staff.create', compact('roles', 'barangays'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'role_id'        => 'required|exists:roles,id',
            'first_name'     => 'required|string|max:100|regex:/^[a-zA-Z\s]+$/',
            'middle_name'    => 'nullable|string|max:100|regex:/^[a-zA-Z\s]*$/',
            'last_name'      => 'required|string|max:100|regex:/^[a-zA-Z\s]+$/',
            'suffix'         => 'nullable|string|max:20',
            'email'          => 'required|email|unique:users,email',
            'contact_number' => 'required|string|regex:/^[0-9]{11}$/',
            'address'        => 'nullable|string',
            'birthdate'      => 'nullable|date',
            'position'       => 'nullable|string|max:100',
            'organization'   => 'nullable|string|max:150',
            'barangay_id'    => 'nullable|exists:barangays,id',
            'password'       => 'required|string|min:6',
            'status'         => 'required|in:active,inactive',
        ]);

        User::create([
            'role_id'        => $request->role_id,
            'first_name'     => $request->first_name,
            'middle_name'    => $request->middle_name,
            'last_name'      => $request->last_name,
            'suffix'         => $request->suffix,
            'email'          => $request->email,
            'contact_number' => $request->contact_number,
            'address'        => $request->address,
            'birthdate'      => $request->birthdate,
            'position'       => $request->position,
            'organization'   => $request->organization,
            'barangay_id'    => $request->barangay_id,
            'password'       => Hash::make($request->password),
            'status'         => $request->status,
        ]);

        return redirect()->route('admin.staff.index')
            ->with('success', 'Staff account created successfully.');
    }

    public function edit($id)
    {
        $user      = User::findOrFail($id);
        $roles     = Role::whereIn('name', ['Staff', 'Barangay Partner', 'Volunteer'])->get();
        $barangays = Barangay::with('municipality')->get();

        return view('admin.staff.edit', compact('user', 'roles', 'barangays'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'role_id'        => 'required|exists:roles,id',
            'first_name'     => 'required|string|max:100|regex:/^[a-zA-Z\s]+$/',
            'middle_name'    => 'nullable|string|max:100|regex:/^[a-zA-Z\s]*$/',
            'last_name'      => 'required|string|max:100|regex:/^[a-zA-Z\s]+$/',
            'suffix'         => 'nullable|string|max:20',
            'email'          => 'required|email|unique:users,email,' . $id,
            'contact_number' => 'required|string|regex:/^[0-9]{11}$/',
            'address'        => 'nullable|string',
            'birthdate'      => 'nullable|date',
            'position'       => 'nullable|string|max:100',
            'organization'   => 'nullable|string|max:150',
            'barangay_id'    => 'nullable|exists:barangays,id',
            'status'         => 'required|in:active,inactive',
        ]);

        $user->update([
            'role_id'        => $request->role_id,
            'first_name'     => $request->first_name,
            'middle_name'    => $request->middle_name,
            'last_name'      => $request->last_name,
            'suffix'         => $request->suffix,
            'email'          => $request->email,
            'contact_number' => $request->contact_number,
            'address'        => $request->address,
            'birthdate'      => $request->birthdate,
            'position'       => $request->position,
            'organization'   => $request->organization,
            'barangay_id'    => $request->barangay_id,
            'status'         => $request->status,
        ]);

        return redirect()->route('admin.staff.index')
            ->with('success', 'Staff account updated successfully.');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        try {
            // Check for related records that would prevent deletion
            $relatedTables = [
                'beneficiaries' => 'beneficiary records',
                'reliefEvents' => 'relief events',
                'evacuationReports' => 'evacuation reports',
                'recommendations' => 'recommendations',
                'locationRequests' => 'location requests',
                'barangayRequests' => 'barangay requests',
                'municipalityRequests' => 'municipality requests',
                'notifications' => 'notifications',
                'households' => 'household records',
            ];

            $hasRelatedRecords = false;
            $relatedRecordInfo = [];

            // Check beneficiaries relationship
            if ($user->beneficiary) {
                $hasRelatedRecords = true;
                $relatedRecordInfo[] = 'beneficiary record';
            }

            // Check other potential relationships
            $tablesToCheck = [
                'relief_events' => 'created_by',
                'evacuation_reports' => 'reported_by',
                'recommended_beneficiaries' => 'submitted_by',
                'location_requests' => 'requested_by',
                'barangay_requests' => 'requested_by',
                'municipality_requests' => 'requested_by',
                'notifications' => 'user_id',
                'households' => 'created_by',
            ];

            foreach ($tablesToCheck as $table => $column) {
                try {
                    $count = \DB::table($table)->where($column, $id)->count();
                    if ($count > 0) {
                        $hasRelatedRecords = true;
                        $relatedRecordInfo[] = "{$count} record(s) in {$table}";
                    }
                } catch (\Exception $e) {
                    // Table might not exist or other issue, continue checking
                    continue;
                }
            }

            if ($hasRelatedRecords) {
                return back()->with('error', 'Cannot delete user. User has related records: ' . implode(', ', $relatedRecordInfo) . '. Please deactivate the account instead.');
            }

            // If no related records, proceed with deletion
            $user->delete();

            return redirect()->route('admin.staff.index')
                ->with('success', 'Staff account deleted successfully.');

        } catch (\Illuminate\Database\QueryException $e) {
            // Handle foreign key constraint violation
            if (str_contains($e->getMessage(), 'foreign key constraint')) {
                return back()->with('error', 'Cannot delete user due to foreign key constraints. Please deactivate the account instead.');
            }
            
            // Handle other database errors
            \Log::error('Error deleting user: ' . $e->getMessage());
            return back()->with('error', 'An error occurred while trying to delete the user.');
        } catch (\Exception $e) {
            \Log::error('Unexpected error deleting user: ' . $e->getMessage());
            return back()->with('error', 'An unexpected error occurred.');
        }
    }

    public function resetPassword($id)
    {
        $user = User::findOrFail($id);
        $temp = 'Spup@' . rand(1000, 9999);
        $user->update(['password' => Hash::make($temp)]);

        return back()->with('success', "Password reset. Temporary password: {$temp}");
    }

    public function deactivate($id)
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot deactivate your own account.');
        }

        $user->update(['status' => 'inactive']);

        return redirect()->route('admin.staff.index')
            ->with('success', 'Staff account deactivated successfully.');
    }

    public function activate($id)
    {
        $user = User::findOrFail($id);
        $user->update(['status' => 'active']);

        return redirect()->route('admin.staff.index')
            ->with('success', 'Staff account activated successfully.');
    }
}