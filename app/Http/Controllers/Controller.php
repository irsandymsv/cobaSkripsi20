<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
	use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    //Mengubah String ke Binary
	protected function stringToBin($str)
	{
		$str = (string)$str;
		$pjg = strlen($str);
		$res = '';
		while ($pjg--) {
			$res = str_pad(decbin(ord($str[$pjg])), 8, "0", STR_PAD_LEFT).$res;
		}
		return $res;
	}

	//Mengubah integer ke Binary
	protected function integerToBin($angka)
	{
		$bin_angka = str_pad(decbin($angka), 8, "0", STR_PAD_LEFT);
		
		return $bin_angka;
	}
	
    //Mengubah Binary ke String
	protected function binaryToString($binary)
	{
       // $binaries = explode(' ', $binary);
    	// var_dump($binaries);

		$panjang = strlen($binary);
		$jml_index = $panjang / 8;
		$start = 0;
		$string = null;

		for ($i=0; $i < $jml_index; $i++) { 
			$bin_part = substr($binary, $start, 8);
    		// echo substr($binary, $start, 8)."<br>";
			$string .= pack('H*', dechex(bindec($bin_part)));
			$start = (8*($i+1));
		}
		
		return $string;    
	}

    //Mengubah String ke Binary (alternatif)
	protected function stringToBinary($string)
	{
		$characters = str_split($string);
		
		$binary = [];
		foreach ($characters as $character) {
			$data = unpack('H*', $character);
            // $binary[] = base_convert($data[1], 16, 2);
			$binary[] = str_pad(base_convert($data[1], 16, 2), 8, "0", STR_PAD_LEFT);
		}
		
		return implode('', $binary);    
	}

	protected function makeHistogram($image)
   {
   	$histogram = [];
   	$width = imagesx($image);
      $height = imagesy($image);
      for ($i=0; $i <= 255; $i++) { 
         $histogram[$i] = 0;
      }

      for ($y=0; $y < $height; $y++) { 
         for ($x=0; $x < $width; $x++) { 
            $rgb = imagecolorat($image, $x, $y);
            $r = ($rgb >> 16) & 0xFF;
            $histogram[$r]++;
         }
      }

      return $histogram;
   }
}
