<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
   protected $dates = [
        'created_at',
        'updated_at',
        'scheduled'
    ];

    public function emails()
    {
        return $this->hasMany('App\Email');
    }    
}
