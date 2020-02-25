<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class login_token extends Model
{
   /**
     * The attributes that are NOT mass assignable.
     *
     * @var array
    */
   protected $guarded = [
   	'id'
   ];

   protected $table = 'login_token';



   public function user()
   {
   	return $this->belongsTo('App\User', 'user_id');
   }
 }
