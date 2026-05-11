<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MunicipalityRequest;
use App\Models\BarangayRequest;
use App\Models\Municipality;
use App\Models\Barangay;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DebugLocationController extends Controller
{
    public function debugApprove($id)
    {
        Log::info("=== DEBUG APPROVE START ===");
        Log::info("Request ID: {$id}");
        Log::info("Authenticated user ID: " . auth()->id());
        Log::info("Current time: " . now());
        
        try {
            // Step 1: Check if we can find the request
            Log::info("Step 1: Looking for MunicipalityRequest with ID: {$id}");
            $municipalityRequest = MunicipalityRequest::find($id);
            Log::info("MunicipalityRequest found: " . ($municipalityRequest ? 'YES' : 'NO'));
            
            if ($municipalityRequest) {
                Log::info("Step 2: MunicipalityRequest data: " . json_encode($municipalityRequest->toArray()));
                Log::info("Step 3: Approving municipality request: {$municipalityRequest->name}");
                
                // Step 4: Update the request
                Log::info("Step 4: Setting status to approved");
                $municipalityRequest->status = 'approved';
                $municipalityRequest->reviewed_by = auth()->id();
                $municipalityRequest->reviewed_at = now();
                Log::info("Step 5: Saving MunicipalityRequest...");
                
                if (!$municipalityRequest->save()) {
                    Log::error("Step 6: FAILED to save MunicipalityRequest");
                    throw new \Exception("Failed to save MunicipalityRequest");
                }
                Log::info("Step 6: MunicipalityRequest saved successfully");

                // Step 7: Create the actual municipality record
                Log::info("Step 7: Creating Municipality record...");
                $municipality = Municipality::create([
                    'name' => $municipalityRequest->name,
                    'province' => $municipalityRequest->province ?? null,
                    'status' => 'approved'
                ]);
                Log::info("Step 8: Municipality created with ID: " . $municipality->id);

                DB::commit();
                Log::info("Step 9: Transaction committed successfully");

                return response()->json([
                    'success' => true,
                    'message' => "Municipality request '{$municipalityRequest->name}' has been approved successfully.",
                    'steps_completed' => 9
                ]);
            }

            // Try barangay requests
            Log::info("Step 1B: Looking for BarangayRequest with ID: {$id}");
            $barangayRequest = BarangayRequest::find($id);
            Log::info("BarangayRequest found: " . ($barangayRequest ? 'YES' : 'NO'));
            
            if ($barangayRequest) {
                Log::info("Step 2B: BarangayRequest data: " . json_encode($barangayRequest->toArray()));
                Log::info("Step 3B: Approving barangay request: {$barangayRequest->name}");
                
                $barangayRequest->status = 'approved';
                $barangayRequest->reviewed_by = auth()->id();
                $barangayRequest->reviewed_at = now();
                Log::info("Step 5B: Saving BarangayRequest...");
                
                if (!$barangayRequest->save()) {
                    Log::error("Step 6B: FAILED to save BarangayRequest");
                    throw new \Exception("Failed to save BarangayRequest");
                }
                Log::info("Step 6B: BarangayRequest saved successfully");

                Log::info("Step 7B: Creating Barangay record...");
                $barangay = Barangay::create([
                    'name' => $barangayRequest->name,
                    'municipality_id' => $barangayRequest->municipality_id,
                    'status' => 'approved'
                ]);
                Log::info("Step 8B: Barangay created with ID: " . $barangay->id);

                DB::commit();
                Log::info("Step 9B: Transaction committed successfully");

                return response()->json([
                    'success' => true,
                    'message' => "Barangay request '{$barangayRequest->name}' has been approved successfully.",
                    'steps_completed' => 9
                ]);
            }

            Log::error("Step 10: No request found with ID: {$id}");
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Request not found.',
                'steps_completed' => 1
            ]);
            
        } catch (\Exception $e) {
            Log::error("=== EXCEPTION CAUGHT ===");
            Log::error("Exception message: " . $e->getMessage());
            Log::error("Exception code: " . $e->getCode());
            Log::error("Exception file: " . $e->getFile());
            Log::error("Exception line: " . $e->getLine());
            Log::error("Stack trace: " . $e->getTraceAsString());
            Log::error("=== EXCEPTION END ===");
            
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to approve request. Please try again.',
                'error' => $e->getMessage()
            ]);
        }
    }

    public function debugReject($id)
    {
        Log::info("=== DEBUG REJECT START ===");
        Log::info("Request ID: {$id}");
        Log::info("Authenticated user ID: " . auth()->id());
        
        try {
            $municipalityRequest = MunicipalityRequest::find($id);
            Log::info("MunicipalityRequest found: " . ($municipalityRequest ? 'YES' : 'NO'));
            
            if ($municipalityRequest) {
                $municipalityRequest->status = 'rejected';
                $municipalityRequest->reviewed_by = auth()->id();
                $municipalityRequest->reviewed_at = now();
                $municipalityRequest->save();
                Log::info("MunicipalityRequest rejected successfully");

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => "Municipality request '{$municipalityRequest->name}' has been rejected successfully."
                ]);
            }

            $barangayRequest = BarangayRequest::find($id);
            Log::info("BarangayRequest found: " . ($barangayRequest ? 'YES' : 'NO'));
            
            if ($barangayRequest) {
                $barangayRequest->status = 'rejected';
                $barangayRequest->reviewed_by = auth()->id();
                $barangayRequest->reviewed_at = now();
                $barangayRequest->save();
                Log::info("BarangayRequest rejected successfully");

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => "Barangay request '{$barangayRequest->name}' has been rejected successfully."
                ]);
            }

            Log::error("No request found with ID: {$id}");
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Request not found.'
            ]);
            
        } catch (\Exception $e) {
            Log::error("=== REJECT EXCEPTION CAUGHT ===");
            Log::error("Exception message: " . $e->getMessage());
            Log::error("Stack trace: " . $e->getTraceAsString());
            
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to reject request. Please try again.',
                'error' => $e->getMessage()
            ]);
        }
    }
}
