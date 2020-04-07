<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RSAController extends Controller
{
   public function generate_key()
   {
   	$p = mt_rand(500, 1000);
   	$q = mt_rand(500, 1000);
   	while ($q == $p) {
   		$q = mt_rand(500, 5000);
   	}

   	$n = $p * $q;
   	$phi = ($p - 1) * ($q - 1); 
   	$e = mt_rand(1, ($phi-1));

   	echo "p = ".$p."<br>";
   	echo "q = ".$q."<br>";
   	echo "n = ".$n."<br>";
   	echo "phi = ".$phi."<br>";
   	echo "e = ".$e."<br>";
   }

   public function view_login()
   {
   	return view('rsa.login');
   }
}
