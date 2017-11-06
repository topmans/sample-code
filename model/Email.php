<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Email extends Model
{
   protected $dates = [
        'created_at',
        'updated_at',
        'scheduled',
        'sent_at'
    ]; 
}
