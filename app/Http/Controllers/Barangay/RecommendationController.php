<?php

namespace App\Http\Controllers\Barangay;

use App\Http\Controllers\Controller;
use App\Models\RecommendedBeneficiary;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class RecommendationController extends Controller
{
    public function index()
    {
        $recommendations = RecommendedBeneficiary::where('barangay_id', auth()->user()->barangay_id)
            ->latest()->get();

        return view('barangay.recommendations', compact('recommendations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name'     => 'required|string|max:100',
            'middle_name'    => 'nullable|string|max:100',
            'last_name'      => 'required|string|max:100',
            'gender'         => 'required|in:Male,Female',
            'age'            => 'required|integer|min:0|max:120',
            'contact_number' => 'nullable|string|max:13',
            'address'        => 'nullable|string',
            'notes'          => 'nullable|string',
        ]);

        $recommendation = RecommendedBeneficiary::create([
            'barangay_id'    => auth()->user()->barangay_id,
            'submitted_by'   => auth()->id(),
            'first_name'     => $request->first_name,
            'middle_name'    => $request->middle_name,
            'last_name'      => $request->last_name,
            'gender'         => $request->gender,
            'age'            => $request->age,
            'contact_number' => $request->contact_number,
            'address'        => $request->address,
            'notes'          => $request->notes,
            'status'         => 'Pending',
        ]);

        // Trigger notification for barangay partner recommendation submission
        NotificationService::barangayRecommendationSubmitted($recommendation->id, auth()->id());

        return back()->with('success', 'Recommendation sent successfully.');
    }

    public function edit($id)
    {
        $recommendation = RecommendedBeneficiary::where('id', $id)
            ->where('submitted_by', auth()->id())
            ->firstOrFail();

        $recommendations = RecommendedBeneficiary::where('barangay_id', auth()->user()->barangay_id)
            ->latest()->get();

        return view('barangay.recommendations', compact('recommendations', 'recommendation'));
    }

    public function update(Request $request, $id)
    {
        $recommendation = RecommendedBeneficiary::where('id', $id)
            ->where('submitted_by', auth()->id())
            ->firstOrFail();

        $request->validate([
            'first_name'     => 'required|string|max:100',
            'middle_name'    => 'nullable|string|max:100',
            'last_name'      => 'required|string|max:100',
            'gender'         => 'required|in:Male,Female',
            'age'            => 'required|integer|min:0|max:120',
            'contact_number' => 'nullable|string|max:13',
            'address'        => 'nullable|string',
            'notes'          => 'nullable|string',
        ]);

        $recommendation->fill([
            'first_name'     => $request->first_name,
            'middle_name'    => $request->middle_name,
            'last_name'      => $request->last_name,
            'gender'         => $request->gender,
            'age'            => $request->age,
            'contact_number' => $request->contact_number,
            'address'        => $request->address,
            'notes'          => $request->notes,
        ]);

        if ($recommendation->isDirty()) {
            $recommendation->save();
            NotificationService::barangayRecommendationUpdated($recommendation->id, auth()->id());
            return redirect()->route('barangay.recommendations.index')
                ->with('success', 'Recommendation updated successfully.');
        }

        return redirect()->route('barangay.recommendations.index')
            ->with('success', 'No changes were made to the recommendation.');
    }

    public function destroy($id)
    {
        $recommendation = RecommendedBeneficiary::where('id', $id)
            ->where('submitted_by', auth()->id())
            ->firstOrFail();

        $recommendation->delete();

        return back()->with('success', 'Recommendation deleted successfully.');
    }
}