<?php

namespace App\Console\Commands;

use App\Models\Beneficiary;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:cleanup-rejected-beneficiaries')]
#[Description('Delete rejected beneficiaries after 10 days')]
class CleanupRejectedBeneficiaries extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting cleanup of rejected beneficiaries...');
        
        // Get rejected beneficiaries whose scheduled deletion date has passed
        $rejectedBeneficiaries = Beneficiary::where('status', 'rejected')
            ->where('scheduled_deletion_date', '<=', now()->toDateString())
            ->get();
        
        $deletedCount = 0;
        
        foreach ($rejectedBeneficiaries as $beneficiary) {
            // Log the deletion before removing
            $this->line("Deleting beneficiary: {$beneficiary->first_name} {$beneficiary->last_name} (ID: {$beneficiary->unique_id}) - Rejected on: {$beneficiary->rejection_date}");
            
            // Delete the beneficiary
            $beneficiary->delete();
            $deletedCount++;
        }
        
        if ($deletedCount > 0) {
            $this->info("Successfully deleted {$deletedCount} rejected beneficiaries.");
        } else {
            $this->info("No rejected beneficiaries found for cleanup.");
        }
        
        $this->info('Cleanup completed.');
        
        return 0;
    }
}
