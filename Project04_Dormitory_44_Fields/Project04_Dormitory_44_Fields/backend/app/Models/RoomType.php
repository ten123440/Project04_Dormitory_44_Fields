<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomType extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'description',
        'capacity',
        'deposit_amount',

        'name',
        'monthly_rate',
        'created_at',
    ];

    public function rooms()
    {
        return $this->hasMany(Room::class, 'room_type_id');
    }
}
