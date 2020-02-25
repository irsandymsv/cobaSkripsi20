<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\HS_image;
use App\User;

class HistogramController extends Controller
{
	public function admin_index()
	{
		$image = HS_image::all();
		return view('histogram.admin_index', ['image' => $image]);
	}

   public function create_image()
   {
      return view('histogram.create_image');  
   }

   public function store_image(Request $request)
   {
      $this->validate($request, [
         'gambar' => 'required|mimetypes:image/jpeg,image/png'
      ]);

      $gambar = $request->file('gambar');
      $extensi = $gambar->getClientOriginalExtension();
      $image = '';
      if ($extensi == "jpeg" || $extensi == "jpg") {
         $image = imagecreatefromjpeg($gambar->path());
      }
      elseif ($extensi == "png") {
         $image = imagecreatefrompng($gambar->path()); 
      }

      $width = imagesx($image);
      $height = imagesy($image);

      $getImage = HS_image::where([
         ['width', $width],
         ['height', $height]
      ])->first();

      if (!is_null($getImage)) {
         return redirect()->back()->with('sudah_ada', 'Harap pilih gambar dengan resolusi berbeda.');
      }

      //Buat Histogram
      $histogram = [];
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

      //Tentukan Peak dan Zero
      $max_point = max($histogram);
      $peak = array_search($max_point, $histogram);

      $min_point = min($histogram);
      $zero = array_search($min_point, $histogram); 

      if ($peak == $zero) {
          return redirect()->back()->with('peak_sama_zero', 'Gagal, gambar tidak dapat digunakan. Harap pilih gambar lain');
       } 

      //Tentukan Kapasitas
      $overhead_len = 0;
      if ($min_point > 0) {
         $overhead_len = $min_point;

         if ($peak > $zero) {
            $overhead_len += $histogram[$zero + 1];
         }
         else {
            $overhead_len += $histogram[$zero - 1];
         }
      }

      $kapasitas = $max_point - $overhead_len;

      $newImage = HS_image::create([
         'width' => $width,
         'height' => $height,
         'peak' => $peak,
         'zero' => $zero,
         'kapasitas' => $kapasitas
      ]);

      $path = $request->file('gambar')->store('histogram');
      $newImage->image = $path;
      $newImage->save();

      return redirect()->route('histogram.view_image', $newImage->id);
   }

   public function view_image($id)
   {
      $image = HS_image::findOrFail($id);
      return view('histogram.view_image', ['image'=>$image]);
   }

   public function register()
   {
      return view('histogram.register');
   }

   public function store_user(Request $request)
   {
      $this->validate($request, [
         "nama" => "required|string",
         "email" => "required|email|string|max:190|unique:users",
         "no_hp" => "required|string|unique:users",
         "tgl_lahir" => "required",
         "gender" => "required",
         "password" => "required|string|max:12|min:6",
      ]);

      $password = $request->input('password');
      $message = $request->input('email')." ".$password." ";
      $bin_message = $this->stringToBin($message);
      $bin_msg_len = strlen($bin_message);

      $images = HS_image::where('kapasitas', '>', $bin_msg_len)->get();
      if (is_null($images)) {
         return redirect()->back()->with('gambar_tdk_cukup', 'Tidak ditemukan gambar yang cukup besar. Harap hubungin admin untuk menambahkan gambar baru');
      }

      $hash_pass = Hash::make($password);
      $new_user = User::create([
         "nama" => $request->input('nama'),
         "email" => $request->input('email'),
         "no_hp" => $request->input('no_hp'),
         "gender" => $request->input('gender'),
         "tgl_lahir" => $request->input('tgl_lahir'),
         "password" => $hash_pass
      ]);

      $path_image = storage_path('app/public/'.$images[0]->image);
      $this->embedding(
         $path_image, 
         $images[0]->peak, 
         $images[0]->zero, 
         $bin_message, 
         $new_user->id
      );

      $request->session()->flash('registrasi_sukses', 'Registrasi Berhasil');
      return response()->download(storage_path('app/public/user_cover/cover_photo-'.$new_user->id.'.png'))->deleteFileAfterSend();
   }

   public function login()
   {
      return view('histogram.login');
   }

   public function checkLogin(Request $request)
   {
      $this->validate($request, [
         "cover_photo" => "required|mimetypes:image/jpeg,image/png"
      ]);

      $cover_photo = $request->file('cover_photo');
      $ekstensi = $cover_photo->getClientOriginalExtension();
      $image = '';
      if ($ekstensi == "jpeg" || $ekstensi == "jpg") {
         $image = imagecreatefromjpeg($cover_photo->path());
      }
      elseif ($ekstensi == "png") {
         $image = imagecreatefrompng($cover_photo->path());
      }

      $width = imagesx($image);
      $height = imagesy($image);

      $image_info = HS_image::where([
         ['width', $width],
         ['height', $height]
      ])->first();

      if (is_null($image_info)) {
         return redirect()->back()->with('gambar_salah', 'Gambar tidak dapat digunakan karena bukan gambar cover untuk login');
      }

      $user_info = $this->ekstraksi($image, $image_info->peak, $image_info->zero);
      // dd($user_info);

      if (Auth::attempt(['email' => $user_info[0], 'password' => $user_info[1]])) {
         // Auth::login($user);
         return redirect()->route('histogram.dashboard');
      }
      else{
         return redirect()->back()->with('not_found', "Akun tidak ditemukan. Harap periksa kembali gambar cover anda");
      }

   }

   public function dashboard()
   {
      return view('histogram.dashboard');
   }

   public function logout()
   {
      Auth::logout();
      return redirect()->route('histogram.login');
   }
   


   private function embedding($path_image, $peak, $zero, $bin_message, $user_id)
   {
      $ekstensi = pathinfo($path_image, PATHINFO_EXTENSION);
      $image = '';
      if ($ekstensi == "jpeg" || $ekstensi == "jpg") {
         $image = imagecreatefromjpeg($path_image);
      }
      elseif ($ekstensi == "png") {
         $image = imagecreatefrompng($path_image); 
      }

      $width = imagesx($image);
      $height = imagesy($image);

      //Buat Histogram
      $histogram = [];
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

      //Tentukan frekuensi(jumlah pixel) untuk peak dan zero point
      $max_point = $histogram[$peak];
      $min_point = $histogram[$zero];

      //Shifting
      if ($peak < $zero) {
         //ditambah (shift to right)
         for ($y=0; $y < $height; $y++) { 
            for ($x=0; $x < $width; $x++) { 
               $rgb = imagecolorat($image, $x, $y);
               $r = ($rgb >> 16) & 0xFF;
               $g = ($rgb >> 8) & 0xFF;
               $b = $rgb & 0xFF;

               //overhead info (ketika min_point > 0)
               if ($r == $zero) {
                  $bin_message .= "0";
               }
               elseif($r == ($zero - 1)){
                  $bin_message .= "1";
               }

               //SHIFT
               if ($r > $peak && $r < $zero) {
                  $newR = $r+1;
                  $newColor = imagecolorallocate($image, $newR, $g, $b);
                  imagesetpixel($image, $x, $y, $newColor);
               }
            }
         }
      }
      else{
         //dikurangi (shift to left)
         for ($y=0; $y < $height; $y++) { 
            for ($x=0; $x < $width; $x++) { 
               $rgb = imagecolorat($image, $x, $y);
               $r = ($rgb >> 16) & 0xFF;
               $g = ($rgb >> 8) & 0xFF;
               $b = $rgb & 0xFF;

               //overhead info (ketika min_point > 0)
               if ($r == $zero) {
                  $bin_message .= "0";
               }
               elseif($r == ($zero + 1)){
                  $bin_message .= "1";
               }

               if ($r < $peak && $r > $zero) {
                  $newR = $r-1;
                  $newColor = imagecolorallocate($image, $newR, $g, $b);
                  imagesetpixel($image, $x, $y, $newColor);
               }
            }
         }
      }

      //Embedding
      $bin_msg_len = strlen($bin_message);
      $count = 0;
      for ($y=0; $y < $height; $y++) { 
         for ($x=0; $x < $width; $x++) { 
            if ($count == $bin_msg_len) {
               break 2;
            }
            $rgb = imagecolorat($image, $x, $y);
            $r = ($rgb >> 16) & 0xFF;
            $g = ($rgb >> 8) & 0xFF;
            $b = $rgb & 0xFF;

            if ($r == $peak) {
               if ($peak < $zero) {
                  //r ditambah 1
                  if ($bin_message[$count] == 1) {
                     $newR = $r+1;
                  }
                  else{
                     $newR = $r;
                  }
               }
               else{
                  //r dikurangi 1
                  if ($bin_message[$count] == 1) {
                     $newR = $r-1;
                  }
                  else{
                     $newR = $r;
                  }
               }
               
               $newColor = imagecolorallocate($image, $newR, $g, $b);
               imagesetpixel($image, $x, $y, $newColor);

               $count+=1;
            }
         }
      }

      $path = storage_path("app/public/user_cover/cover_photo-".$user_id.".png");
      imagepng($image, $path);
      imagedestroy($image);
   }

   private function ekstraksi($cover_photo, $peak, $zero)
   {
      $width = imagesx($cover_photo);
      $height = imagesy($cover_photo);
      $min_point = 0; //jumlah/frekuensi zero
      $bin_message = '';
      $user_info = '';

      //Ekstrak pesan yg disisipkan
      if ($peak < $zero) { //jika peak di sebelah kiri zero
         for ($y=0; $y < $height; $y++) { 
            for ($x=0; $x < $width; $x++) { 
               $rgb = imagecolorat($cover_photo, $x, $y);
               $r = ($rgb >> 16) & 0xFF;

               if ($r == $peak) {
                  $bin_message .= "0";
               }
               elseif ($r == ($peak + 1)) {
                  $bin_message .= "1";
               }
               elseif ($r == $zero) {
                  $min_point++;
               }
            }
         }
      }
      elseif ($peak > $zero){ //jika max index di sebelah kanan min index
         for ($y=0; $y < $height; $y++) { 
            for ($x=0; $x < $width; $x++) { 
               $rgb = imagecolorat($cover_photo, $x, $y);
               $r = ($rgb >> 16) & 0xFF;

               if ($r == $peak) {
                  $bin_message .= "0";
               }
               elseif ($r == ($peak - 1)) {
                  $bin_message .= "1";
               }
               elseif ($r == $zero) {
                  $min_point++;
               }
            }
         }
      }
      else{
         echo "Max index dan Min index tidak boleh sama. Failed";
         die();
      }

      
      /* NOTE: 
      format $message = EMAIL[space]PASSWORD[sapce]OVERHEAD_INFO0000000...(karna message < dr max_point)
      */

      //Ambil pesan alsi dan overhead info (jika ada)
      $message_len = strlen($bin_message);
      $pesan_asli = '';
      $overhead_info = '';
      $space_count = 0;
      for ($i=0; ($i + 7) < $message_len; $i += 8) { 
         $bin_part = substr($bin_message, $i, 8);
         $char = pack('H*', dechex(bindec($bin_part)));
         // echo $char."<br>";   

         if ($char == " ") {
            $space_count++;
         }

         if ($space_count == 2) { 
            //setelah space ke dua adalah overhead info atau 0000...

            if ($min_point > 0) { //cek apakah ada Overhead Info
               $overhead_info = substr($message, $i+=8, $min_point);
               break;
            }
            else{ //break jika tidak ada overhead info
               break;
            }
         }

         $pesan_asli .= $char;
      }


      //Shift Back 
      $index = 0;
      if ($peak < $zero) { //jika max index lbh kecil, geser ke kiri
         for ($y=0; $y < $height; $y++) { 
            for ($x=0; $x < $width; $x++) { 
               $rgb = imagecolorat($cover_photo, $x, $y);
               $r = ($rgb >> 16) & 0xFF;
               $g = ($rgb >> 8) & 0xFF;
               $b = $rgb & 0xFF;

               if ($r > $peak && $r < $zero) {
                  $newR = $r - 1;
                  $newColor = imagecolorallocate($cover_photo, $newR, $g, $b);
                  imagesetpixel($cover_photo, $x, $y, $newColor);
               }
               elseif ($r == $zero) {
                  if ((int)$overhead_info[$index] == 1) {
                     $newR = $r - 1;
                     $newColor = imagecolorallocate($cover_photo, $newR, $g, $b);
                     imagesetpixel($cover_photo, $x, $y, $newColor);    
                  }
                  $index++;
               }
            }
         }
      }
      else {
         for ($y=0; $y < $height; $y++) { //jika max index lbh besar, geser ke kanan
            for ($x=0; $x < $width; $x++) { 
               $rgb = imagecolorat($cover_photo, $x, $y);
               $r = ($rgb >> 16) & 0xFF;
               $g = ($rgb >> 8) & 0xFF;
               $b = $rgb & 0xFF;

               if ($r < $peak && $r > $zero) {
                  $newR = $r + 1;
                  $newColor = imagecolorallocate($cover_photo, $newR, $g, $b);
                  imagesetpixel($cover_photo, $x, $y, $newColor);
               }
               elseif ($r == $zero) {
                  if ((int)$overhead_info[$index] == 1) {
                     $newR = $r + 1;
                     $newColor = imagecolorallocate($cover_photo, $newR, $g, $b);
                     imagesetpixel($cover_photo, $x, $y, $newColor);    
                  }
                  $index++;
               }
            }
         }
      }
      imagedestroy($cover_photo);

      $user_info = explode(" ", $pesan_asli);
      return $user_info;
   }








	public function index()
	{
    	echo "Histogram Shifting Test"."<br>";
    	$pic = storage_path('app/public/temp_image/contoh_gambar.jpg');
    	$image = imagecreatefromjpeg($pic);
    	$width = imagesx($image);
   	$height = imagesy($image);
   	echo "width = ".$width."<br>";
   	echo "height = ".$height."<br>";

   	$message = 'halo123@gmail.com password123 ';
   	$bin_message = $this->stringToBin($message);
   	$bin_message_len = strlen($bin_message);
   	var_dump($bin_message)."<br>";
   	
      /*Buat histogram
      */
   	$histogram = [];
   	for ($i=0; $i <= 255; $i++) { 
   		$histogram[$i] = 0;
   	}

   	for ($y=0; $y < $height; $y++) { 
   		for ($x=0; $x < $width; $x++) { 
   			$rgb = imagecolorat($image, $x, $y);
   			$r = ($rgb >> 16) & 0xFF;
      		// $g = ($rgb >> 8) & 0xFF;
        		// $b = $rgb & 0xFF;
   			
   			$histogram[$r]++;
   			// echo $r." ";
   			// echo "(".$r." ".$g." ".$b.") ";
   		}
   		// echo "<br>";
   	}

      /*Tentukan max index, max point, min index, min point
      */
   	//max_index = nilai greyscale dg jumlah pixel terbanyak
   	//max_point = jumlah pixel dr max_index

   	$max_point = max($histogram);
   	$max_index = array_search($max_point, $histogram);
   	echo "max index= ".$max_index." (".$max_point." pixel)"."<br>";

   	$min_point = min($histogram);
   	$min_index = array_search($min_point, $histogram);
   	echo "min index= ".$min_index." (".$min_point."pixel)"."<br>";

   	if ($max_index == $min_index) {
   		echo "Gambar tidak bisa digunakan. Failed!"."<br>";
   		die();
   	}

   	echo "histogram: "."<br>";
   	for ($i=0; $i < count($histogram); $i++) { 
   		if ($histogram[$i] == $max_point || $histogram[$i] == $min_point ) {
   			echo "<b>".$histogram[$i]."</b> ";	
   		}
   		else{
   			echo $histogram[$i]." ";
   		}
   	}
   	echo "<br><br>";

      /*Gabungkan nilai binary dr max min index ke bin_key
      */
      $bin_key = '';
      $bin_max_index = $this->integerToBin($max_index);
      $bin_min_index = $this->integerToBin($min_index);
      echo "bin max index = "."<br>";
      var_dump($bin_max_index);
      echo "<br> bin min index = "."<br>";
      var_dump($bin_min_index);
      echo "<br>";

      $bin_key = $bin_max_index.$bin_min_index;
      echo "bin key:"."<br>";
      var_dump($bin_key);
      echo "<br><br>";
      
      /*Hitung pure payload (kapasitas)
      */
      $overhead_len = 0;
      if ($min_point > 0) {
         $overhead_len = $min_point;

         if ($max_index > $min_index) {
            $overhead_len += $histogram[$min_index + 1];
         }
         else {
            $overhead_len += $histogram[$min_index - 1];
         }
      }

      $pure_payload = $max_point - ($overhead_len + strlen($bin_key));
      echo "pure payload (kapasitas) = ".$pure_payload."<br>";
      if ($bin_message_len > $pure_payload) {
         echo "not enough capacity, find more pair of max min points"."<br>";
      }

      echo "<br> 16 pixel pertama sblm shift <br>";
      for ($y=0; $y < 1; $y++) { 
         for ($x=0; $x < 16; $x++) { 
            $rgb = imagecolorat($image, $x, $y);
            $r = ($rgb >> 16) & 0xFF;

            echo $r."<br>";
         }
      }

   	/*Shifting
      */
   	if ($max_index < $min_index) {
   		//ditambah (shift to right)

			for ($y=0; $y < $height; $y++) { 
				for ($x=0; $x < $width; $x++) { 

               // if ($y == 0) { 
               //    if ($x >= 0 && $x < 16) {
               //       //cek apakah pixel termasuk 16 pertama, jika ya lewati
               //       continue;
               //    }
               // }

					$rgb = imagecolorat($image, $x, $y);
					$r = ($rgb >> 16) & 0xFF;
					$g = ($rgb >> 8) & 0xFF;
        			$b = $rgb & 0xFF;

        			//overhead info (ketika min_point > 0)
        			if ($r == $min_index) {
        				$bin_message .= "0";
        			}
        			elseif($r == ($min_index - 1)){
        				$bin_message .= "1";
        			}

        			//SHIFT
					if ($r > $max_index && $r < $min_index) {
						$newR = $r+1;
						$newColor = imagecolorallocate($image, $newR, $g, $b);
   					imagesetpixel($image, $x, $y, $newColor);
					}
				}
			}
   	}
   	else{
   		//dikurangi (shift to left)

   		for ($y=0; $y < $height; $y++) { 
				for ($x=0; $x < $width; $x++) { 

               // if ($y == 0) { 
               //    if ($x >= 0 && $x < 16) {
               //       //cek apakah pixel termasuk 16 pertama, jika ya lewati
               //       continue;
               //    }
               // }

					$rgb = imagecolorat($image, $x, $y);
					$r = ($rgb >> 16) & 0xFF;
					$g = ($rgb >> 8) & 0xFF;
        			$b = $rgb & 0xFF;

        			//overhead info (ketika min_point > 0)
        			if ($r == $min_index) {
        				$bin_message .= "0";
        			}
        			elseif($r == ($min_index + 1)){
        				$bin_message .= "1";
        			}

					if ($r < $max_index && $r > $min_index) {
						$newR = $r-1;
						$newColor = imagecolorallocate($image, $newR, $g, $b);
   					imagesetpixel($image, $x, $y, $newColor);
					}
				}
			}
   	}

      echo "<br> 16 pixel pertama Setelah shift <br>";
      for ($y=0; $y < 1; $y++) { 
         for ($x=0; $x < 16; $x++) { 
            $rgb = imagecolorat($image, $x, $y);
            $r = ($rgb >> 16) & 0xFF;

            echo $r."<br>";
         }
      }
      echo "<br>";

      /*Simpan LSB 16 pixel pertama di lsb_asli dan tambahkan juga ke bin_message. Ganti dg bin key (max/min index)
      */
      echo "ganti LSB 16 pixel pertama dg binary max/min index <br>";
      $lsb_asli = '';
      for ($y=0; $y < 1; $y++) { 
         for ($x=0; $x < 16; $x++) { 
            $rgb = imagecolorat($image, $x, $y);
            $r = ($rgb >> 16) & 0xFF;
            $g = ($rgb >> 8) & 0xFF;
            $b = $rgb & 0xFF;

            echo $r." -> ";
            $newR = $this->integerToBin($r);

            $lsb_asli .= $newR[strlen($newR) - 1];
            $bin_message .= $newR[strlen($newR) - 1]; // simpan LSB asli di bin_message
            $newR[strlen($newR) - 1] = $bin_key[$x]; // ganti LSB dg binary max/min index
            $newR = bindec($newR);
            echo $newR."<br>";

            $newColor = imagecolorallocate($image, $newR, $g, $b);
            imagesetpixel($image, $x, $y, $newColor);
         }
      }
      echo "<br> LSB asli <br>";
      var_dump($lsb_asli);
      echo "<br><br>";

   	/*Embedding
      */
      echo "bin message: <br>";
   	var_dump($bin_message)."<br>";
   	$bin_msg_len = strlen($bin_message);

   	$count = 0;
		for ($y=0; $y < $height; $y++) { 
			for ($x=0; $x < $width; $x++) { 

            if ($y == 0) { 
               if ($x >= 0 && $x < 16) {
                  //cek apakah pixel termasuk 16 pertama, jika ya lewati
                  continue;
               }
            }

				if ($count == $bin_msg_len) {
					break 2;
				}
				$rgb = imagecolorat($image, $x, $y);
				$r = ($rgb >> 16) & 0xFF;
				$g = ($rgb >> 8) & 0xFF;
     			$b = $rgb & 0xFF;

				if ($r == $max_index) {
					if ($max_index < $min_index) {
						//r ditambah 1
						if ($bin_message[$count] == 1) {
							$newR = $r+1;
							// $newG = $g+1;
							// $newB = $b+1;
						}
						else{
							$newR = $r;
							// $newG = $g;
							// $newB = $b;
						}
					}
					else{
						//r dikurangi 1
						if ($bin_message[$count] == 1) {
							$newR = $r-1;
							// $newG = $g-1;
							// $newB = $b-1;
						}
						else{
							$newR = $r;
							// $newG = $g;
							// $newB = $b;
						}
					}
					
					$newColor = imagecolorallocate($image, $newR, $g, $b);
					imagesetpixel($image, $x, $y, $newColor);

					$count+=1;
				}
			}
		}

      echo "<br> 16 pixel pertama Setelah embedding <br>";
      for ($y=0; $y < 1; $y++) { 
         for ($x=0; $x < 16; $x++) { 
            $rgb = imagecolorat($image, $x, $y);
            $r = ($rgb >> 16) & 0xFF;

            echo $r."<br>";
         }
      }
      echo "<br>";


		$path = storage_path("app/public/temp_image/hasil_histogram.png");
   	imagepng($image, $path);
   	// imagedestroy($image);

   	echo "gambar berhasil dibuat. Histogram baru: "."<br>";
   	$histogram = [];
   	for ($i=0; $i <= 255; $i++) { 
   		$histogram[$i] = 0;
   	}

   	for ($y=0; $y < $height; $y++) { 
   		for ($x=0; $x < $width; $x++) { 
   			$rgb = imagecolorat($image, $x, $y);
   			$r = ($rgb >> 16) & 0xFF;
      		// $g = ($rgb >> 8) & 0xFF;
        		// $b = $rgb & 0xFF;
   			
   			$histogram[$r]++;
   		}
   	}

   	for ($i=0; $i < count($histogram); $i++) { 
   		echo $histogram[$i]." ";
   	}
   	echo "<br>";
   	imagedestroy($image);

   	// $this->showHistogram($image, $width, $height);
	}



	public function extract()
	{
    	echo "Histogram Shifting Test"."<br>";
    	$pic = storage_path('app/public/temp_image/hasil_histogram.png');
    	$image = imagecreatefrompng($pic);
    	$width = imagesx($image);
   	$height = imagesy($image);
   	echo "width = ".$width."<br>";
   	echo "height = ".$height."<br>";

   	$histogram = [];
   	for ($i=0; $i <= 255; $i++) { 
   		$histogram[$i] = 0;
   	}

		for ($y=0; $y < $height; $y++) { 
			for ($x=0; $x < $width; $x++) { 
				$rgb = imagecolorat($image, $x, $y);
				$r = ($rgb >> 16) & 0xFF;
	   		// $g = ($rgb >> 8) & 0xFF;
	     		// $b = $rgb & 0xFF;
				
				$histogram[$r]++;
			}
		}

   	echo "histrogram gambar stego:"."<br>";
   	for ($i=0; $i < count($histogram); $i++) { 
   		echo $histogram[$i]." ";
   	}
   	echo "<br>";

      echo "extract max min <br>";
      /*extract max dan min index 
      */
      $bin_key = '';
      $bin_max_index = '';
      $bin_max_index = '';
      for ($y=0; $y < 1; $y++) { 
         for ($x=0; $x < 16; $x++) { 
            $rgb = imagecolorat($image, $x, $y);
            $r = ($rgb >> 16) & 0xFF;
            $bin_r = $this->integerToBin($r);
            $bin_key .= $bin_r[strlen($bin_r) - 1];
         }
      }
      echo "<br>";

      echo "bin key: <br>";
      var_dump($bin_key);
      echo "<br>";

      $bin_max_index = substr($bin_key, 0, 8);
      $bin_min_index = substr($bin_key, 8, 8);
      $max_index = bindec($bin_max_index);
      $min_index = bindec($bin_min_index);

      echo "max_index : ";
      var_dump($max_index);
      echo "<br> min index : ";
      var_dump($min_index);
      echo "<br>";

   	// $max_index = 0; //nilai greyscale dg frekuensi (jml pixel) tertinggi
   	// $min_index = 125; //nilai greyscale dg frekuensi terendah
   	$min_point = 0; //jumlah/frekuensi min_index
   	$message = '';

   	/*Ekstrak pesan yg disisipkan
      */
   	if ($max_index < $min_index) { //jika max index di sebelah kiri min index
   		for ($y=0; $y < $height; $y++) { 
   			for ($x=0; $x < $width; $x++) { 

               if ($y == 0) { 
                  if ($x >= 0 && $x < 16) {
                     //cek apakah pixel termasuk 16 pertama, jika ya lewati
                     continue;
                  }
               }

   				$rgb = imagecolorat($image, $x, $y);
   				$r = ($rgb >> 16) & 0xFF;

   				if ($r == $max_index) {
   					$message .= "0";
   				}
   				elseif ($r == ($max_index + 1)) {
   					$message .= "1";
   				}
   				elseif ($r == $min_index) {
   					$min_point++;
   				}
   			}
   		}
   	}
   	elseif ($max_index > $min_index){ //jika max index di sebelah kanan min index
   		for ($y=0; $y < $height; $y++) { 
   			for ($x=0; $x < $width; $x++) { 

               if ($y == 0) { 
                  if ($x >= 0 && $x < 16) {
                     //cek apakah pixel termasuk 16 pertama, jika ya lewati
                     continue;
                  }
               }

   				$rgb = imagecolorat($image, $x, $y);
   				$r = ($rgb >> 16) & 0xFF;

   				if ($r == $max_index) {
   					$message .= "0";
   				}
   				elseif ($r == ($max_index - 1)) {
   					$message .= "1";
   				}
   				elseif ($r == $min_index) {
   					$min_point++;
   				}
   			}
   		}
   	}
   	else{
   		echo "Max index dan Min index tidak boleh sama. Failed";
   		die();
   	}

      echo "bin message: <br>";
      var_dump($message);
      echo "<br>";
   	
   	/* NOTE: 
   	format $message = [EMAIL][ ][PASSWORD][ ][OVERHEAD_INFO][LSB_ASLI][0000000...](karna message < dr max_point)
   	*/

   	//Ambil pesan alsi, LSB asli 16 pixel pertama, dan overhead info (jika ada)
   	$message_len = strlen($message);
   	$pesan_asli = '';
   	$overhead_info = '';
      $key_LSB_asli = '';
   	$space_count = 0;
   	for ($i=0; ($i + 7) < $message_len; $i += 8) { 
   		$bin_part = substr($message, $i, 8);
   		$char = pack('H*', dechex(bindec($bin_part)));
   		// echo $char."<br>";	

   		if ($char == " ") {
   			$space_count++;
   		}

   		if ($space_count == 2) { 
   			//setelah space ke dua adalah overhead info atau LSB

   			if ($min_point > 0) { //cek apakah ada Overhead Info
               $mulai = $i + 8;
					$overhead_info = substr($message, $mulai, $min_point);
               $mulai = $mulai + $min_point;
               $key_LSB_asli = substr($message, $mulai, 16);

					break;
   			}
   			else{ 
               $mulai = $i + 8;
               $key_LSB_asli = substr($message, $mulai, 16);
					break;
   			}
			}

   		$pesan_asli .= $char;
   	}

   	// var_dump($pesan_asli);
   	echo "<br> overehad info: "."<br>";
   	var_dump($overhead_info);
   	echo "<br><br>";

      echo "LSB asli (revocery): "."<br>";
      var_dump($key_LSB_asli);
      echo "<br><br>";
   	// die();

      echo "pesan asli = ".$pesan_asli."<br>";
      $hasil = explode(" ", $pesan_asli);
      echo "explode hasil: "."<br>";
      var_dump($hasil);
      echo "<br><br>";

      /*set LSB asli ke 16 pixel pertama
      */
      echo "LSB 16 pixel pertama diubah ke nilai awal (setelah shifting) <br>";
      for ($y=0; $y < 1; $y++) { 
         for ($x=0; $x < 16; $x++) { 
            $rgb = imagecolorat($image, $x, $y);
            $r = ($rgb >> 16) & 0xFF;
            $g = ($rgb >> 8) & 0xFF;
            $b = $rgb & 0xFF;

            echo $r." -> ";
            $newR = $this->integerToBin($r);

            $newR[strlen($newR) - 1] = $key_LSB_asli[$x]; // ganti LSB 16 pixel pertama dg yg LSB asli
            $newR = bindec($newR);
            echo $newR."<br>";

            $newColor = imagecolorallocate($image, $newR, $g, $b);
            imagesetpixel($image, $x, $y, $newColor);
         }
      }

   	/*Shift Back
      */ 
   	$index = 0;
   	if ($max_index < $min_index) { //jika max index lbh kecil, geser ke kiri
   		for ($y=0; $y < $height; $y++) { 
   			for ($x=0; $x < $width; $x++) { 

               // if ($y == 0) { 
               //    if ($x >= 0 && $x < 16) {
               //       //cek apakah pixel termasuk 16 pertama, jika ya lewati
               //       continue;
               //    }
               // }

   				$rgb = imagecolorat($image, $x, $y);
   				$r = ($rgb >> 16) & 0xFF;
   				$g = ($rgb >> 8) & 0xFF;
   				$b = $rgb & 0xFF;

   				if ($r > $max_index && $r < $min_index) {
   					$newR = $r - 1;
   					$newColor = imagecolorallocate($image, $newR, $g, $b);
   					imagesetpixel($image, $x, $y, $newColor);
   				}
   				elseif ($r == $min_index) {
   					if ((int)$overhead_info[$index] == 1) {
   						$newR = $r - 1;
	   					$newColor = imagecolorallocate($image, $newR, $g, $b);
	   					imagesetpixel($image, $x, $y, $newColor);		
   					}
   					$index++;
   				}
   			}
   		}
   	}
   	else {
   		for ($y=0; $y < $height; $y++) { //jika max index lbh besar, geser ke kanan
   			for ($x=0; $x < $width; $x++) { 

               // if ($y == 0) { 
               //    if ($x >= 0 && $x < 16) {
               //       //cek apakah pixel termasuk 16 pertama, jika ya lewati
               //       continue;
               //    }
               // }

   				$rgb = imagecolorat($image, $x, $y);
   				$r = ($rgb >> 16) & 0xFF;
   				$g = ($rgb >> 8) & 0xFF;
   				$b = $rgb & 0xFF;

   				if ($r < $max_index && $r > $min_index) {
   					$newR = $r + 1;
   					$newColor = imagecolorallocate($image, $newR, $g, $b);
   					imagesetpixel($image, $x, $y, $newColor);
   				}
   				elseif ($r == $min_index) {
   					if ((int)$overhead_info[$index] == 1) {
   						$newR = $r + 1;
	   					$newColor = imagecolorallocate($image, $newR, $g, $b);
	   					imagesetpixel($image, $x, $y, $newColor);		
   					}
   					$index++;
   				}
   			}
   		}
   	}

   	$histogram = [];
   	for ($i=0; $i <= 255; $i++) { 
   		$histogram[$i] = 0;
   	}

   	for ($y=0; $y < $height; $y++) { 
   		for ($x=0; $x < $width; $x++) { 
   			$rgb = imagecolorat($image, $x, $y);
   			$r = ($rgb >> 16) & 0xFF;
      		// $g = ($rgb >> 8) & 0xFF;
        		// $b = $rgb & 0xFF;
   			
   			$histogram[$r]++;
   		}
   	}

   	echo "<br> histrogram gambar recorey:"."<br>";
   	for ($i=0; $i < count($histogram); $i++) { 
   		echo $histogram[$i]." ";
   	}

	}



	public function showHistogram($image, $width, $height)
	{
		// $histogram = [];
		for ($i=0; $i <= 255; $i++) { 
			$histogram[$i][] = 0;
		}

		for ($y=0; $y < $height; $y++) { 
			for ($x=0; $x < $width; $x++) { 
				$rgb = imagecolorat($image, $x, $y);
				$r = ($rgb >> 16) & 0xFF;
	   		// $g = ($rgb >> 8) & 0xFF;
	     		// $b = $rgb & 0xFF;
				
				$histogram[$r][] = 1;
			}
		}

		for ($i=0; $i <= 255; $i++) { 
			echo "> ".$i." = ";
			for ($k=0; $k < count($histogram[$i]); $k++) { 
				echo $histogram[$i][$k];
			}
			echo "<br>";
		}
	}
}
