<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Location extends Model
{
    use HasFactory;

    protected $fillable = [
        'distributor_id',
        'name',
        'address',
        'latitude',
        'longitude',
        'stock',
        'capacity',
        'is_open',
        'phone',
        'photo',
        'operating_hours',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
            'stock' => 'integer',
            'capacity' => 'integer',
            'is_open' => 'boolean',
        ];
    }

    public function distributor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'distributor_id');
    }

    public function stockLogs(): HasMany
    {
        return $this->hasMany(StockLog::class);
    }

    public function scopeWithDistance(Builder $query, float $latitude, float $longitude): Builder
    {
        $haversine = '(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude))))';

        return $query
            ->select('locations.*')
            ->selectRaw($haversine.' AS distance', [$latitude, $longitude, $latitude]);
    }

    public function scopeNearby(Builder $query, float $latitude, float $longitude, float $radiusKm = 5): Builder
    {
        return $query
            ->withDistance($latitude, $longitude)
            ->having('distance', '<=', $radiusKm)
            ->orderBy('distance');
    }

    public static function haversineDistanceKm(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadiusKm = 6371;

        $lat1Rad = deg2rad($lat1);
        $lat2Rad = deg2rad($lat2);
        $deltaLat = deg2rad($lat2 - $lat1);
        $deltaLng = deg2rad($lng2 - $lng1);

        $a = sin($deltaLat / 2) ** 2 +
            cos($lat1Rad) * cos($lat2Rad) * sin($deltaLng / 2) ** 2;

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadiusKm * $c;
    }
}
