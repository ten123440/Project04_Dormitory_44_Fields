<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'check_out_date',
        'monthly_rate',
        'deposit_amount',
        'notes',
        'created_at',

        'tracking_code',
        'user_id',
        'room_id',
        'check_in_date',
        'status',
    ];

    protected $casts = ['monthly_rate' => 'decimal:2', 'deposit_amount' => 'decimal:2'];

    protected static function booted(): void
    {
        static::creating(function (Booking $booking) {
            $booking->created_at ??= now();
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id');
    }
}
