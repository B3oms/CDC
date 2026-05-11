<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MunicipalityRequest;
use Illuminate\Support\Facades\Log;

class TestController extends Controller
{
    public function testApprove($id)
    {
        Log::info("Test approve called with ID: {$id}");
        
        try {
            $municipalityRequest = MunicipalityRequest::find($id);
            Log::info("MunicipalityRequest found: " . ($municipalityRequest ? 'YES' : 'NO'));
            
            if ($municipalityRequest) {
                Log::info("Approving municipality request: {$municipalityRequest->name}");
                
                $municipalityRequest->status = 'approved';
                $municipalityRequest->approved_by = auth()->id();
                $municipalityRequest->approved_at = now();
                $municipalityRequest->save();
                Log::info("Municipality request updated successfully");

                // Create the actual municipality record
                $municipality = Municipality::create([
                    'name' => $municipalityRequest->name,
                    'status' => 'approved'
                ]);
                Log::info("Municipality created successfully: {$municipality->name}");

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => "Municipality request '{$municipalityRequest->name}' has been approved successfully."
                ]);
            } else {
                Log::warning("MunicipalityRequest not found");
                return response()->json([
                    'success' => false,
                    'message' => 'Request not found.'
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Approve request error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to approve request. Please try again.',
                'error' => $e->getMessage()
            ]);
        }
    }
}
