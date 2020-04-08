<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
	use Notifiable;

   /**
    * The attributes that are NOT mass assignable.
    *
    * @var array
    */
   protected $guarded = [
   	'id'
   ];

   /**
    * The attributes that should be hidden for arrays.
    *
    * @var array
    */
   protected $hidden = [
   	'password', 'remember_token',
   ];

   /**
    * The attributes that should be cast to native types.
    *
    * @var array
    */
   protected $casts = [
   	'email_verified_at' => 'datetime',
   ];



   public function login_token()
   {
   	return $this->hasMany('App\login_token', 'user_id');
   }

	public function recovery_image()
   {
   	return $this->hasMany('App\recovery_image', 'user_id');
   }   
 }
