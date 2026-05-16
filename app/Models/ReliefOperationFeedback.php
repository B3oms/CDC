<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReliefOperationFeedback extends Model
{
    protected $table = 'relief_operation_feedbacks';

    protected $fillable = [
        'relief_operation_id',
        'barangay_id',
        'message',
        'created_by',
    ];

    public function reliefOperation(): BelongsTo
    {
        return $this->belongsTo(ReliefOperation::class);
    }

    public function barangay(): BelongsTo
    {
        return $this->belongsTo(Barangay::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
