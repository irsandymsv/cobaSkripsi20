<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\User;

class Histogram2Controller extends Controller
{
   public function index()
   {
      return view('histogram2.index');
   }

   public function register()
   {
      return view('histogram2.register');
   }

   public function store_user(Request $request)
   {
      $this->validate($request, [
         "cover_photo" => "required|mimetypes:image/jpeg,image/png",
         "nama" => "required|string",
         "email" => "required|email|string|max:190|unique:users",
         "no_hp" => "required|string|unique:users",
         "tgl_lahir" => "required",
         "gender" => "required",
         "password" => "required|string|max:12|min:6",
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

   	//Buat histogram dari $image
      echo "<br>";
   	$histogram = $this->makeHistogram($image);
      for ($i=0; $i < 256; $i++) { 
         echo $histogram[$i]." ";
      }
      echo "<br>";

   	//Tentukan Peak dan Zero
      $max_point = max($histogram);
      $peak = array_search($max_point, $histogram);

      $min_point = min($histogram);
      $zero = array_search($min_point, $histogram); 

      //Return back jika peak == zero
      if ($peak == $zero) {
         return redirect()->back()->with('peak_zero', 'Gambar tidak dapat digunakan, harap pilih gambar lain');
      }

      $password = $request->input('password');
      $message = $request->input('email')." ".$password." ";
      $bin_message = $this->stringToBin($message);
      $bin_msg_len = strlen($bin_message);

      //tentukan kapasitas image
      $overhead_len = 0; //jml pixel zero(jika ada) + pixel di sampingnya
      if ($min_point > 0) {
         $overhead_len = $min_point;

         if ($peak > $zero) {
            $overhead_len += $histogram[$zero + 1];
         }
         else {
            $overhead_len += $histogram[$zero - 1];
         }
      }

      $unused_key_pixel = 0; //jmlh pixel peak yg tidak dapat digunakan utk embedding karena digunakan utk menyimpan binary key (peak n zero)
      $yAxis=0;
      for ($x=0; $x < 16; $x++) { 
      	$rgb = imagecolorat($image, $x, $yAxis);
      	$r = ($rgb >> 16) & 0xFF;
      	if ($r == $peak) {
      		$unused_key_pixel++;
      	}
      }

      $pure_payload = $max_point - ($overhead_len + $unused_key_pixel);
      if ($bin_msg_len > $pure_payload) {
      	return redirect()->back()->with('gambar_tdk_cukup', 'Gambar tidak cukup untuk menampung data. Harap pilih gambar lain');
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

      $this->embedding(
         $image, 
         $peak, 
         $zero, 
         $bin_message, 
         $new_user->id
      );

      $request->session()->flash('registrasi_sukses', 'Registrasi Berhasil');
      
      ob_end_clean();
      $headers = array(
          'Content-Type: image/png',
      );
      return response()->download(storage_path('app/public/user_cover/cover_photo-'.$new_user->id.'.png'), 'cover_photo-'.$new_user->id.'.png', $headers)->deleteFileAfterSend();
   }

   public function login()
   {
      return view('histogram2.login');
   }

   public function checkLogin(Request $request)
   {
      $this->validate($request, [
         "cover_photo" => "required|mimetypes:image/jpeg,image/png",
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

      $user_info = $this->ekstraksi($image);
      // dd($user_info);

      if ($user_info == "error_cover") {
         return redirect()->back()->with('error_cover', "Gambar tidak dapat digunakan. Pastikan gambar yang digunakan adalah gambar cover yang didapat ketika registrasi");
      }

      if (Auth::attempt(['email' => $user_info[0], 'password' => $user_info[1]])) {
         // Auth::login($user);
         // return redirect()->route('histogram2.dashboard');
         return redirect()->route('histogram2.dashboard');
      }
      else{
         return redirect()->back()->with('not_found', "Akun tidak ditemukan. Harap gunakan gambar cover yang anda dapat ketika registrasi.");
      }

   }

   public function dashboard()
   {
      // $histogram = [];
      return view('histogram2.dashboard');
   }

   public function logout()
   {
      Auth::logout();
      return redirect()->route('histogram2.login');
   }

   public function view_histogram()
   {
      return view('histogram2.view_histogram');
   }

   public function show_histogram(Request $request)
   {
      $cover_photo = $request->file('cover_photo');
      $ekstensi = $cover_photo->getClientOriginalExtension();
      $image = '';
      if ($ekstensi == "jpeg" || $ekstensi == "jpg") {
         $image = imagecreatefromjpeg($cover_photo->path());
      }
      elseif ($ekstensi == "png") {
         $image = imagecreatefrompng($cover_photo->path());
      }

      $histogram = $this->makeHistogram($image);
      for ($i=0; $i < 256; $i++) { 
         echo $histogram[$i]." ";
      }
      echo "<br>";
   }


   private function embedding($image, $peak, $zero, $bin_message, $user_id)
   {
      $width = imagesx($image);
      $height = imagesy($image);

      //Buat Histogram
      // $histogram = [];
      // for ($i=0; $i <= 255; $i++) { 
      //    $histogram[$i] = 0;
      // }

      // for ($y=0; $y < $height; $y++) { 
      //    for ($x=0; $x < $width; $x++) { 
      //       $rgb = imagecolorat($image, $x, $y);
      //       $r = ($rgb >> 16) & 0xFF;
      //       $histogram[$r]++;
      //    }
      // }

      //Tentukan frekuensi(jumlah pixel) untuk peak dan zero point
      // $max_point = $histogram[$peak];
      // $min_point = $histogram[$zero];

      //simpan nilai binary dari peak dan zero
      $bin_key = ''; //nilai binary peak dan zero
      $bin_peak = $this->integerToBin($peak);
      $bin_zero = $this->integerToBin($zero);
      $bin_key = $bin_peak.$bin_zero;

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

      /*Simpan LSB 16 pixel pertama ke bin_message. Ganti dg bin key (max/min index)
      */
      echo "ganti LSB 16 pixel pertama dg binary max/min index <br>";
      $lsb_asli = '';
      for ($y=0; $y < 1; $y++) { 
         for ($x=0; $x < 16; $x++) { 
            $rgb = imagecolorat($image, $x, $y);
            $r = ($rgb >> 16) & 0xFF;
            $g = ($rgb >> 8) & 0xFF;
            $b = $rgb & 0xFF;

            $newR = $this->integerToBin($r);

            $bin_message .= $newR[strlen($newR) - 1]; // simpan LSB asli di bin_message
            $newR[strlen($newR) - 1] = $bin_key[$x]; // ganti LSB dg binary max/min index
            $newR = bindec($newR);

            $newColor = imagecolorallocate($image, $newR, $g, $b);
            imagesetpixel($image, $x, $y, $newColor);
         }
      }

      //Embedding
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




   private function ekstraksi($cover_photo)
   {
      $width = imagesx($cover_photo);
      $height = imagesy($cover_photo);
      $min_point = 0; //jumlah/frekuensi zero
      $bin_message = '';
      $user_info = '';

      try {
         /*extract max dan min index 
         */
         $bin_key = '';
         $bin_peak = '';
         $bin_zero = '';
         for ($y=0; $y < 1; $y++) { 
            for ($x=0; $x < 16; $x++) { 
               $rgb = imagecolorat($cover_photo, $x, $y);
               $r = ($rgb >> 16) & 0xFF;
               $bin_r = $this->integerToBin($r);
               $bin_key .= $bin_r[strlen($bin_r) - 1];
            }
         }

         $bin_peak = substr($bin_key, 0, 8);
         $bin_zero = substr($bin_key, 8, 8);
         $peak = bindec($bin_peak);
         $zero = bindec($bin_zero);

         //Ekstrak pesan yg disisipkan
         if ($peak < $zero) { //jika peak di sebelah kiri zero
            for ($y=0; $y < $height; $y++) { 
               for ($x=0; $x < $width; $x++) { 
                  if ($y == 0) { 
                     if ($x >= 0 && $x < 16) {
                        //cek apakah pixel termasuk 16 pertama, jika ya lewati
                        continue;
                     }
                  }

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
         elseif ($peak > $zero){ //jika peak di sebelah kanan zero
            for ($y=0; $y < $height; $y++) { 
               for ($x=0; $x < $width; $x++) { 
                  if ($y == 0) { 
                     if ($x >= 0 && $x < 16) {
                        //cek apakah pixel termasuk 16 pertama, jika ya lewati
                        continue;
                     }
                  }

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
            return "error_cover";
            // echo "Max index dan Min index tidak boleh sama. Failed";
            // die();
         }

         
         /* NOTE: 
         format $message = EMAIL[space]PASSWORD[sapce]OVERHEAD_INFO0000000...(karna message < dr max_point)
         */

         //Ambil pesan asli dan overhead info (jika ada)
         $message_len = strlen($bin_message);
         $pesan_asli = '';
         $overhead_info = '';
         $key_LSB_asli = '';
         $space_count = 0;
         for ($i=0; ($i + 7) < $message_len; $i += 8) { 
            $bin_part = substr($bin_message, $i, 8);
            $char = pack('H*', dechex(bindec($bin_part)));
            // echo $char."<br>";   

            if ($char == " ") {
               $space_count++;
            }

            if ($space_count == 2) { 
               //setelah space ke dua adalah overhead info atau LSB

               if ($min_point > 0) { //cek apakah ada Overhead Info
                  $mulai = $i + 8;
                  $overhead_info = substr($bin_message, $mulai, $min_point);
                  $mulai = $mulai + $min_point;
                  $key_LSB_asli = substr($bin_message, $mulai, 16);

                  break;
               }
               else{ 
                  $mulai = $i + 8;
                  $key_LSB_asli = substr($bin_message, $mulai, 16);
                  break;
               }
            }

            $pesan_asli .= $char;
         }

         /*set LSB asli ke 16 pixel pertama
         */
         for ($y=0; $y < 1; $y++) { 
            for ($x=0; $x < 16; $x++) { 
               $rgb = imagecolorat($cover_photo, $x, $y);
               $r = ($rgb >> 16) & 0xFF;
               $g = ($rgb >> 8) & 0xFF;
               $b = $rgb & 0xFF;

               $newR = $this->integerToBin($r);
               $newR[strlen($newR) - 1] = $key_LSB_asli[$x]; // ganti LSB 16 pixel pertama dg yg LSB asli
               $newR = bindec($newR);

               $newColor = imagecolorallocate($cover_photo, $newR, $g, $b);
               imagesetpixel($cover_photo, $x, $y, $newColor);
            }
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
         // imagedestroy($cover_photo);
         // $histogram = $this->makeHistogram($cover_photo);
         $user_info = explode(" ", $pesan_asli);
         // $user_info[] = $histogram;
         return $user_info;
      } 
      catch (Exception $e) {
         return "error_cover";
      }

      
   }
}
