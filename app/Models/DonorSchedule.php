<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DonorSchedule extends Model
{
    protected $fillable = [
        'title', 'location', 'schedule_date', 'start_time',
        'end_time', 'quota', 'status',
    ];

    protected function casts(): array
    {
        return [
            'schedule_date' => 'date',
        ];
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(DonorRegistration::class);
    }
}
