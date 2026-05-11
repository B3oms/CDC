<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TestApproveController extends Controller
{
    public function test($id)
    {
        Log::info("=== TEST APPROVE METHOD CALLED ===");
        Log::info("Request ID: {$id}");
        Log::info("Authenticated user ID: " . auth()->id());
        
        try {
            $municipalityRequest = \App\Models\MunicipalityRequest::find($id);
            Log::info("MunicipalityRequest found: " . ($municipalityRequest ? 'YES' : 'NO'));
            
            if ($municipalityRequest) {
                Log::info("SUCCESS: Found municipality request and would approve");
                return response()->json([
                    'success' => true,
                    'message' => "Test approve method working correctly"
                ]);
            } else {
                Log::info("FAILED: Municipality request not found");
                return response()->json([
                    'success' => false,
                    'message' => "Municipality request not found"
                ]);
            }
        } catch (\Exception $e) {
            Log::error("TEST EXCEPTION CAUGHT: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => "Test failed: " . $e->getMessage()
            ]);
        }
    }
}
