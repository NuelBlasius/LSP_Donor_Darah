<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DonorSchedule extends Model
{
    protected $fillable = [
        'title', 'location', 'schedule_date', 'start_time',
        'end_time', 'quota', 'status', 'amount',
    ];

    protected function casts(): array
    {
        return [
            'schedule_date' => 'date',
        ];
    }

    public function getMapAddressAttribute(): string
    {
        $location = trim((string) $this->location);

        if (! filter_var($location, FILTER_VALIDATE_URL)) {
            return $location;
        }

        $query = [];
        parse_str((string) parse_url($location, PHP_URL_QUERY), $query);

        foreach (['query', 'q', 'destination', 'daddr'] as $key) {
            if (! empty($query[$key]) && is_string($query[$key])) {
                return trim($query[$key]);
            }
        }

        $path = (string) parse_url($location, PHP_URL_PATH);

        if (preg_match('~/(?:maps/)?(?:place|search)/([^/?]+)~i', $path, $matches)) {
            $address = urldecode($matches[1]);

            return trim(str_replace('_', ' ', $address));
        }

        return 'Lihat lokasi di Google Maps';
    }

    public function getMapUrlAttribute(): ?string
    {
        return filter_var($this->location, FILTER_VALIDATE_URL)
            ? $this->location
            : null;
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(DonorRegistration::class);
    }
}
