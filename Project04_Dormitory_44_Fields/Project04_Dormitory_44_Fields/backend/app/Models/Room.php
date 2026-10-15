<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'building_id',

        'room_number',
        'room_type_id',
        'floor',
        'status',
        'description',
        'created_at',
    ];

    public function building()
    {
        return $this->belongsTo(Building::class);
    }

    public function roomType()
    {
        return $this->belongsTo(RoomType::class, 'room_type_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'room_id');
    }
}
