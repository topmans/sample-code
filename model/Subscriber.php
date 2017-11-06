<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subscriber extends Model
{
   	protected $dates = [
        'created_at',
        'updated_at',
        'last_activity'
    ];

	public function page()
	{
		return $this->belongsToMany('App\Page', 'subscriber_page')->withTimestamps();
	}     	
}