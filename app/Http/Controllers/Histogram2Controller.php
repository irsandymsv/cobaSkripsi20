<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Storage;
use App\User;
use App\recovery_image;
use Carbon\Carbon;
use App\Mail\recoveryImage;

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
   	$histogram = $this->makeHistogram($image);
      
      // for ($i=0; $i < 256; $i++) { 
      //    echo $histogram[$i]." ";
      // }
      // echo "<br>";

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
      $message = $request->input('email')." ".$password;
      $message_encrypt = encrypt($message); //Enkripsi email dan password
      $msg_secret = $message_encrypt." ";
      $bin_message = $this->stringToBin($msg_secret);
      $bin_msg_len = strlen($bin_message);

      // echo "msg = ".$msg_secret."<br>";
      // echo "last 2 char : ".substr($msg_secret, -2);
      // die();

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
      	return redirect()->back()->with('gambar_tdk_cukup', 'Gambar tidak cukup untuk menampung data. Harap pilih gambar lain')->withInput();
      }
      // echo "bin msg len: ".$bin_msg_len."<br>";
      // echo "pure_payload: ".$pure_payload;
      // die();

      $tgl_parse = Carbon::parse($request->input('tgl_lahir'));
      $hash_pass = Hash::make($password);
      $new_user = User::create([
         "nama" => $request->input('nama'),
         "email" => $request->input('email'),
         "no_hp" => $request->input('no_hp'),
         "gender" => $request->input('gender'),
         "tgl_lahir" => $tgl_parse,
         "password" => $hash_pass
      ]);

      $this->embedding(
         $image, 
         $peak, 
         $zero, 
         $bin_message, 
         $new_user->id
      );

      Auth::login($new_user);
      return redirect()->route('histogram2.dashboard');

      // echo "message encrypt: ".$message_encrypt;
   }

   public function download_cover()
   {
      $user = Auth::user();
      $is_exist = Storage::exists('user_cover/cover_photo-'.$user->id.'.png');
      if ($is_exist) {
         ob_end_clean();
         $headers = array(
             'Content-Type: image/png',
         );
         return response()->download(storage_path('app/public/user_cover/cover_photo-'.$user->id.'.png'), 'user_cover_image.png', $headers)->deleteFileAfterSend();
      }
      else{
         return redirect()->back()->with('cover_not_found', 'Gambar cover tidak ditemukan atau sudah didownload sebelumnya.');
      }
      
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

      //Dekrip email dan password 
      $dekrip_pesan = decrypt($user_info);
      $kredensial = explode(" ", $dekrip_pesan);

      if (Auth::attempt(['email' => $kredensial[0], 'password' => $kredensial[1]])) {
         // Auth::login($user);
         // return redirect()->route('histogram2.dashboard');
         return redirect()->route('histogram2.dashboard');
      }
      else{
         return redirect()->back()->with('not_found', "Akun tidak ditemukan. Harap gunakan gambar cover yang anda dapat ketika registrasi.");
      }

   }


   //RECOVERY
   public function pemulihan_gambar()
   {
      return view('histogram2.pemulihan_gambar');
   }

   public function send_recovery(Request $request)
   {
      $this->validate($request, [
         'email' => 'required|email',
         'tgl_lahir' => 'required'
      ]);

      $tgl_lahir = Carbon::parse($request->input('tgl_lahir'));
      $user = User::where([
         ['email', $request->input('email')],
         ['tgl_lahir', $tgl_lahir]
      ])->first();

      if (is_null($user)) {
         return redirect()->back()->with('akun_tidak_ada', 'Akun tidak ditemukan. Harap periksa kembali email dan tanggal lahir yang anda masukkan')->withInput();
      }
      else{
         $recovery = recovery_image::create([
            'user_id' => $user->id
         ]);
         
         $code = encrypt($recovery->id);
         MAil::to($request->input('email'))->send(new recoveryImage($code, $user->nama));

         return redirect()->back()->with('email_send', 'Email pemulihan telah dikirimkan ke alamat email anda. Silahkan periksa kotak masuk email anda.')->withInput();
      }
      // $tgl = Carbon::createFromFormat('d F Y', $request->tgl_lahir, 'Asia/Jakarta');

   }

   public function reset_cover($code)
   {
      $recovery_id = decrypt($code);
      $recovery = recovery_image::findOrFail($recovery_id);
      $selisih_waktu = Carbon::now()->diffInMinutes(Carbon::parse($recovery->created_at));
      $timeout = false;
      if ($selisih_waktu > 30) {
         $timeout = true;
      }
      return view('histogram2.reset_cover', [
         'timeout' => $timeout,
         'code' => $code
      ]);
   }

   public function update_cover(Request $request)
   {
      $this->validate($request, [
         "cover_photo" => "required|mimetypes:image/jpeg,image/png",
         "password" => "required|string|max:12|min:6",
      ]);

      $code = '';
      if (is_null($request->input('code'))) {
         return redirect()->back()->with('code_not_found', 'Terjadi kesalahan. Harap buat permintaan pemulihan gambar lagi.');
      }
      else{
         $code = $request->input('code');
      }

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
      $histogram = $this->makeHistogram($image);

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
      $message = $user->email." ".$password;
      $message_encrypt = encrypt($message); //Enkripsi email dan password
      $msg_secret = $message_encrypt." ";
      $bin_message = $this->stringToBin($msg_secret);
      $bin_msg_len = strlen($bin_message);

      // echo "msg = ".$msg_secret."<br>";
      // echo "last 2 char : ".substr($msg_secret, -2);
      // die();

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
         return redirect()->back()->with('gambar_tdk_cukup', 'Gambar tidak cukup untuk menampung data. Harap pilih gambar lain')->withInput();
      }

      //mendapatkan id recovery_image dan user yang terkait
      $recovery_id = decrypt($code);
      $recovery = recovery_image::findOrFail($recovery_id);
      $user = User::findOrFail($recovery->user_id);

      $hash_pass = Hash::make($password);
      $user->password = $hash_pass;
      $user->save();

      $this->embedding(
         $image, 
         $peak, 
         $zero, 
         $bin_message, 
         $user->id
      );

      Auth::login($new_user);
      Session(['pemulihan_sukses' => 'Password dan gambar cover anda berhasil diperbarui.']);
      
      return redirect()->route('histogram2.dashboard');
   }


   public function dashboard()
   {
      $user = Auth::user();
      $is_exist = Storage::exists('user_cover/cover_photo-'.$user->id.'.png');
      $cover_exist = false;
      if ($is_exist) {
         $cover_exist = true;
      }
      return view('histogram2.dashboard', ['cover_exist' => $cover_exist]);
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


   //EMBEDDING
   private function embedding($image, $peak, $zero, $bin_message, $user_id)
   {
      $width = imagesx($image);
      $height = imagesy($image);

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
      // echo "ganti LSB 16 pixel pertama dg binary max/min index <br>";

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



   //EKSTRAKSI
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
         format $message = EMAIL+[space]+PASSWORD+[sapce]+OVERHEAD_INFO/LSB+0000000...(karna message < dr max_point jadinya 0000...)
         */

         //Ambil pesan asli dan overhead info (jika ada)
         $message_len = strlen($bin_message);
         $pesan_asli = '';
         $overhead_info = '';
         $key_LSB_asli = '';
         // $space_count = 0;
         for ($i=0; ($i + 7) < $message_len; $i += 8) { 
            $bin_part = substr($bin_message, $i, 8);
            $char = pack('H*', dechex(bindec($bin_part)));
            // echo $char."<br>";   

            if ($char == " ") {
               // echo "space found ";
               //setelah space adalah overhead info atau LSB

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

            // if ($space_count == 2) { 
               
            // }

            $pesan_asli .= $char;
         }

         // echo "tes: ".$key_LSB_asli;
         // die();

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

         // $histogram = $this->makeHistogram($cover_photo);
         // for ($i=0; $i < 256; $i++) { 
         //    echo $histogram[$i]." ";
         // }
         // echo "<br>";
         // die();

         // imagedestroy($cover_photo);
         return $pesan_asli;
      } 
      catch (Exception $e) {
         return "error_cover";
      }

      
   }
}
