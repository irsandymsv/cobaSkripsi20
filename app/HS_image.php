<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HS_image extends Model
{
    /**
     * The attributes that are NOT mass assignable.
     *
     * @var array
    */
   protected $guarded = [
   	'id'
   ];

   protected $table = 'hs_image';
}
