<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DonorRegistration extends Model
{
    protected $fillable = [
        'user_id', 'donor_schedule_id', 'registration_status',
        'payment_status', 'payment_proof', 'payment_method', 'payment_reference', 'payment_expires_at',
    ];

    protected function casts(): array
    {
        return [
            'payment_expires_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(DonorSchedule::class, 'donor_schedule_id');
    }
}
