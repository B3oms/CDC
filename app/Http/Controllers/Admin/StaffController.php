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
            'first_name'     => 'required|string|max:100',
            'last_name'      => 'required|string|max:100',
            'email'          => 'required|email|unique:users,email',
            'contact_number' => 'required|string|max:13',
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
            'last_name'      => $request->last_name,
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
            'first_name'     => 'required|string|max:100',
            'last_name'      => 'required|string|max:100',
            'email'          => 'required|email|unique:users,email,' . $id,
            'contact_number' => 'required|string|max:13',
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
            'last_name'      => $request->last_name,
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

        $user->delete();

        return redirect()->route('admin.staff.index')
            ->with('success', 'Staff account deleted.');
    }

    public function resetPassword($id)
    {
        $user = User::findOrFail($id);
        $temp = 'Spup@' . rand(1000, 9999);
        $user->update(['password' => Hash::make($temp)]);

        return back()->with('success', "Password reset. Temporary password: {$temp}");
    }
}