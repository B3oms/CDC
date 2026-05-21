<?php

// Test the new validation approach
$testIds = [
    'BE-URAN-Y67W',        // New format (12 chars)
    'BAL-SPUP-2026-069',   // Old format (17 chars)
    'ILL-SPUP-2026-005',   // Old format (17 chars)
    'BE-ABC-123',          // Invalid (too short)
    'be-uran-y67w',        // Invalid (lowercase)
    'BAL-SPUP-2026-069-EXTRA', // Invalid (too long)
];

echo "Testing New Validation Approach:\n\n";

foreach ($testIds as $id) {
    echo "ID: $id (Length: " . strlen($id) . ")\n";
    
    $valid = false;
    $error = '';
    
    // Simulate the validation logic
    if (preg_match('/^[A-Z]{3}-[A-Z]{4}-[0-9]{4}-[0-9]{3}$/', $id)) {
        $valid = true;
    } elseif (preg_match('/^[A-Z]{2}-[A-Z]{4}-[A-Z0-9]{4}$/', $id)) {
        $valid = true;
    } else {
        $error = 'Unique ID must be in format: BAL-SPUP-2026-069 or BE-URAN-Y67W';
    }
    
    echo "  Validation: " . ($valid ? 'PASS' : 'FAIL') . "\n";
    if (!$valid) {
        echo "  Error: $error\n";
    }
    echo "\n";
}

echo "New validation approach test completed!\n";
