<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\User;
use App\login_token;
use App\Mail\loginToken;
use Carbon\Carbon;

class steganoController extends Controller
{
   public function index()
   {
   	return view('stegano.index');
   }

   public function store(Request $request)
   {
   	$this->validate($request, [
   		"nama" => "required|string",
   		"email" => "required|email|string|unique:users",
   		"no_hp" => "required|string|unique:users",
   		"tgl_lahir" => "required",
   		"gender" => "required",
   		"cover_photo" => "required|mimetypes:image/jpeg,image/png"
   	]);

   	$cover_photo = $request->file('cover_photo');
   	$extensi = $cover_photo->getClientOriginalExtension();
   	$image = '';
   	if ($extensi == "jpeg" || $extensi == "jpg") {
   		$image = imagecreatefromjpeg($cover_photo->path());
   	}
   	elseif ($extensi == "png") {
   		$image = imagecreatefrompng($cover_photo->path());	
   	}

   	$width = imagesx($image);
   	$height = imagesy($image);
   	if ($width < 256 || $height < 256) {
   		return redirect()->back()->with('resolusiMin', 'Resolusi gambar harus lebih besar dari 300x300 px')->withInput();
   	}

   	$time = time();
   	$text = $time."QWERTYUIOPASDFGHJKLZXCVBNMqwertyuiopasdfghjklzxcvbnm?!@%&#$,./;";
   	$password = md5($text);

   	$new_user = User::create([
   		"nama" => $request->input('nama'),
   		"email" => $request->input('email'),
   		"no_hp" => $request->input('no_hp'),
   		"gender" => $request->input('gender'),
   		"tgl_lahir" => $request->input('tgl_lahir'),
   		"password" => $password
   	]);
   	// dd($new_user);

   	$this->insert($password, $image, $width, $height, $new_user->id);
   	Auth::login($new_user);
   	
   	$request->session()->flash('registrasi_sukses', 'Registrasi Berhasil');
   	return response()->download(storage_path('app/public/user_cover/cover_photo-'.$new_user->id.'.png'))->deleteFileAfterSend();
   	// return redirect()->route('stegano.dashboard');
   }

   public function login()
   {
   	if (Auth::check()) {
   		return redirect()->route('stegano.dashboard');
   	}
   	else{
   		return view('stegano.login');
   	}
   }

   public function checkLogin(Request $request)
   {
   	$this->validate($request, [
   		"email" => "required|email|string",
   		"cover_photo" => "required|mimetypes:image/jpeg,image/png"
   	]);

   	$cover_photo = $request->file('cover_photo');
   	$extensi = $cover_photo->getClientOriginalExtension();
   	$image = '';
   	if ($extensi == "jpeg" || $extensi == "jpg") {
   		$image = imagecreatefromjpeg($cover_photo->path());
   	}
   	elseif ($extensi == "png") {
   		$image = imagecreatefrompng($cover_photo->path());	
   	}

   	$password = $this->extract($image);

   	$user = User::where([
   		['email', $request->input('email')],
   		['password', $password]
   	])->first();

   	if (is_null($user)) {
   		return redirect()->back()->with('not_found', "Akun tidak ditemukan. Harap periksa kembali email dan gambar cover anda")->withInput();
   	}
   	else{
   		Auth::login($user);
   		return redirect()->route('stegano.dashboard');
         // $this->set_logedIn($user);
   	}
   }

   // private function set_logedIn($user)
   // {
   //    Auth::login($user);
   //    return redirect()->route('stegano.dashboard');
   // }

   public function dashboard()
   {
   	return view('stegano.dashboard');
   }

   public function logout()
   {
   	Auth::logout();
   	return redirect()->route('stegano.index');
   }

   public function lupa_password()
   {
      return view('stegano.lupa_password');
   }

   public function send_token_mail(Request $request)
   {
      $this->validate($request, [
         'email' => 'required|email'
      ]);

      $user = User::where('email', $request->input('email'))->first();
      if (is_null($user)) {
         return redirect()->back()->with('email_not_found', 'Email tidak ditemukan, pastikan email yang dimasukkan benar dan telah terdaftar sebelumnya.')->withInput();
      }

      $email_token = '';
      do {
         $email_token = mt_rand(100000, 1000000);
         $test_token = login_token::where('email_token', $email_token)->first();
      } while (!is_null($test_token));

      $token = login_token::create([
         'user_id' => $user->id,
         'email_token' => $email_token
      ]);

      Mail::to($request->input('email'))->send(new loginToken($token));
      return view('stegano.login_token', ['user'=> $user]);

      //preview
      // return new loginToken($token);
   }

   public function login_token($user_id)
   {
      $user = User::findOrFail($user_id);
      return view('stegano.login_token', ['user' => $user]);
   }

   public function check_token(Request $request, $user_id)
   {
      $this->validate($request, [
         'token' => 'required|string'
      ]);

      $login_token = login_token::where([
         ['user_id', $user_id],
         ['email_token', $request->input('token')],
      ])->with('user')->first();

      if (is_null($login_token)) {
         return redirect()->back()->with('token_salah', 'Token tidak ditemukan, harap periksa kembali token anda')->withInput();
      }
      else{
         $selisih_waktu = Carbon::now()->diffInSeconds(Carbon::parse($login_token->created_at));
         if ($selisih_waktu >= 600) {
            return redirect()->route('stegano.lupa_password')->with('token_expire', 'Token telah kadar luasa, masukkan email lagi untuk mendapatkan token baru');
         }else{
            Auth::login($login_token->user);
            return redirect()->route('stegano.dashboard');
         }
      }      

   }

   public function resend_token($user_id)
   {
      $user = findOrFail($user_id);
      $email_token = '';
      do {
         $email_token = mt_rand(100000, 1000000);
         $test_token = login_token::where('email_token', $email_token)->first();
      } while (!is_null($test_token));

      $token = login_token::create([
         'user_id' => $user_id,
         'email_token' => $email_token
      ]);

      Mail::to($user->email)->send(new loginToken($token));
      return view('stegano.login_token', ['user'=> $user])->with('token_resended', 'Token baru telah dikirim ulang. Silahkan Periksa email anda.');
   }



   //insert password to image
   private function insert($password, $image, $img_width, $img_height, $user_id)
   {
   	$binMessage = $this->stringToBin($password);
   	$msgLng = strlen($binMessage);

   	$count = 0;
   	for ($y=0; $y < $img_height; $y++) {
   		for ($x=0; $x < $img_width; $x++) { 
   			if ($count == $msgLng) {
   				break 2;
   			}
   		 	$rgb = imagecolorat($image, $x, $y);
   		 	$r = ($rgb >> 16) & 0xFF;
   		 	$g = ($rgb >> 8) & 0xFF;
   		 	$b = $rgb & 0xFF;

   			$newB = $this->integerToBin($b);
   			$newB[strlen($newB)-1] = $binMessage[$x];
   			$newB = bindec($newB);

   			$newColor = imagecolorallocate($image, $r, $g, $newB);
   			imagesetpixel($image, $x, $y, $newColor);

   			$count+=1;
   		 	// echo $r." ";
   		 }
   		 // echo "<br>"; 
   	}

   	$path = storage_path("app/public/user_cover/cover_photo-".$user_id.".png");
   	imagepng($image, $path);
   	imagedestroy($image);
   }

   private function extract($image)
   {
   	$password = '';
   	$width = imagesx($image);
   	$height = imagesy($image);
   	$count = 0;
   	for ($y=0; $y < $height; $y++) { 
   		for ($x=0; $x < $width; $x++) { 
   			if ($count == 256) {
   				break 2;
   			}

   			$rgb = imagecolorat($image, $x, $y);
   			// $r = ($rgb >> 16) & 0xFF;
   			// $g = ($rgb >> 8) & 0xFF;
   			$b = $rgb & 0xFF;

   			$blue = $this->integerToBin($b);
   			$password .= $blue[strlen($blue)-1];
   			
   			// $sign_bin[] = $blue[strlen($blue)-1];
   			// if (count($sign_bin)%8 == 0) {
   			// 	$character_biner = implode('', $sign_bin);
   			// 	$character = binaryToString($character_biner);
   			// 	if ($character == "~") {
   			// 		$is_sign = true;
   			// 	}
   			// 	else{
   			// 		unset($sign_bin);
   			// 	}
   			// }

   			// if ($is_sign) {
   			// 	break 2;
   			// }

   			$count+=1;
   		}
   	}

   	$password = $this->binaryToString($password);
   	return $password;
   }

   
}
