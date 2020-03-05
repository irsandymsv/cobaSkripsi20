<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class testImageController extends Controller
{
	public function index()
	{
		return view('test_image.index');
	}

	public function test(Request $request)
	{
		$this->validate($request, [
			'gambar1' => 'required|mimetypes:image/jpeg,image/png',
			'gambar2' => 'required|mimetypes:image/jpeg,image/png',
		]);

		$gambar1 = $request->file('gambar1');
		$gambar2 = $request->file('gambar2');
		$ekstensi1 = $gambar1->getClientOriginalExtension();
		$ekstensi2 = $gambar2->getClientOriginalExtension();
		$image1 = '';
		$image2 = '';
		if ($ekstensi1 == "jpeg" || $ekstensi1 == "jpg") {
   		$image1 = imagecreatefromjpeg($gambar1->path());
   	}
   	elseif ($ekstensi1 == "png") {
   		$image1 = imagecreatefrompng($gambar1->path());	
   	}

   	if ($ekstensi2 == "jpeg" || $ekstensi2 == "jpg") {
   		$image2 = imagecreatefromjpeg($gambar2->path());
   	}
   	elseif ($ekstensi2 == "png") {
   		$image2 = imagecreatefrompng($gambar2->path());	
   	}

   	$width1 = imagesx($image1);
   	$width2 = imagesx($image2);
   	$height1 = imagesy($image1);
   	$height2 = imagesy($image2);
   	if ($width1 != $width2 && $height1 != $height2) {
   		return redirect()->back()->with('gambar_berbeda', 'Ukuran (resolusi) kedua gambar berbeda. Harap gunakan gambar yang sesuai');
   	}

   	$mse = $this->getMSE($image1, $image2);
   	if ($mse == 0) {
   		echo "gambar identik. Tidak ditemukan perbedaan nilai kecerahan/keabuan (grey level) piksel <br>";
   		die();
   	}
   	$psnr = $this->getPSNR($mse);

   	echo "MSE = ".$mse."<br>";
   	echo "PSNR = ".$psnr."<br><br><br>";
	}

	private function getMSE($image1, $image2)
	{
		$mse = 0;
    	$width = imagesx($image1);
   	$height = imagesy($image1);
   	$temp = 0;
   	for ($y=0; $y < $height; $y++) { 
   		for ($x=0; $x < $width; $x++) { 
   			$rgb1 = imagecolorat($image1, $x, $y);
            $r1 = ($rgb1 >> 16) & 0xFF;

            $rgb2 = imagecolorat($image2, $x, $y);
            $r2 = ($rgb2 >> 16) & 0xFF;

            $diff = $r1 - $r2;
            // echo $diff." ";
            $temp += pow($diff, 2);
   		}
   	}

   	echo "<br> jmlh piksel error (kuadrat) = ".$temp."<br>";
   	$mse = $temp / ($width * $height);
   	return $mse;
	}

	private function getPSNR($mse)
	{	
		$ratio = pow(255, 2) / $mse;
    	$psnr = 10 * log10($ratio);
    	return $psnr;
	}
}