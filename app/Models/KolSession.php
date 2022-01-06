<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KolSession extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }   
    
    public function qrimage()
    {
        return $this->hasOne(Qrimage::class);
    } 
}
