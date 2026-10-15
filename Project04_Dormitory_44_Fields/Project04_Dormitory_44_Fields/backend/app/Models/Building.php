<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Building extends Model
{
    public $timestamps = false;
    protected $fillable = ['building_code', 'name', 'address', 'total_floors', 'contact_phone', 'description'];
    protected $casts = ['total_floors' => 'integer'];
    public function rooms() { return $this->hasMany(Room::class); }
}
