<?php
use Illuminate\Support\Facades\Hash;
use Illuminate\Contracts\Encryption\DecryptException;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/stem', 'stemmerController@index')->name('stemmer');

Route::get('/youtube/searchVideo', 'youtubeController@searchVideo')->name('youtube.searchVideo');
Route::get('/youtube/getComment/{videoId?}', 'youtubeController@getComment')->name('youtube.getComment');

//Stegano LSB
Route::prefix('stegano')->name('stegano.')->group(function ()
{
	Route::get('/', 'steganoController@index')->name('index');
	Route::post('/registrasi/store', 'steganoController@store')->name('registrasi.store');
	Route::get('/login', 'steganoController@login')->name('login');
	Route::post('/login/check', 'steganoController@checkLogin')->name('login.check');
	Route::get('/dashboard', 'steganoController@dashboard')->name('dashboard');
	Route::get('/logout', 'steganoController@logout')->name('logout');
	Route::get('/lupa_password', 'steganoController@lupa_password')->name('lupa_password');
	Route::post('/send_token_mail', 'steganoController@send_token_mail')->name('send_token_mail');
	// Route::get('/login_token/{id_user}', 'steganoController@login_token')->name('login_token');
	Route::get('/resend_token/{id_user}', 'steganoController@resend_token')->name('resend_token');
	Route::post('/check_token/{id_user}', 'steganoController@check_token')->name('check_token');
});



//Stegano Histogram Shifting
Route::prefix('histogram')->name('histogram.')->group(function()
{
	Route::get('/', 'HistogramController@index')->name('index');
	Route::get('/extract', 'HistogramController@extract')->name('extract');
	Route::get('/admin-index', 'HistogramController@admin_index')->name('admin_index');
	Route::get('/create-image', 'HistogramController@create_image')->name('create_image');
	Route::post('/store-image', 'HistogramController@store_image')->name('store_image');
	Route::get('/view-image/{id}', 'HistogramController@view_image')->name('view_image');

	Route::get('/register', 'HistogramController@register')->name('register');
	Route::post('/store_user', 'HistogramController@store_user')->name('store.user');
	Route::get('/login', 'HistogramController@login')->name('login');
	Route::post('/checkLogin', 'HistogramController@checkLogin')->name('checkLogin');
	Route::get('/dashboard', 'HistogramController@dashboard')->name('dashboard');
	Route::get('/logout', 'HistogramController@logout')->name('logout');
});


//Stegano Histogram Shifting2 (peak,zero disisipkan juga)
Route::prefix('histogram2')->name('histogram2.')->group(function()
{
	Route::get('/', 'Histogram2Controller@index')->name('index');
	Route::get('/register', 'Histogram2Controller@register')->name('register');
	Route::post('/store_user', 'Histogram2Controller@store_user')->name('store.user');
	Route::get('/login', 'Histogram2Controller@login')->name('login');
	Route::post('/checkLogin', 'Histogram2Controller@checkLogin')->name('checkLogin');
	Route::get('/dashboard', 'Histogram2Controller@dashboard')->name('dashboard')->middleware('auth');
	Route::get('/logout', 'Histogram2Controller@logout')->name('logout');
	Route::get('/download_cover', 'Histogram2Controller@download_cover')->name('download_cover')->middleware('auth');
	Route::get('/view_histogram', 'Histogram2Controller@view_histogram')->name('view_histogram');
	Route::post('/show_histogram', 'Histogram2Controller@show_histogram')->name('show_histogram');

	Route::get('/pemulihan_gambar', 'Histogram2Controller@pemulihan_gambar')->name('pemulihan_gambar');
	Route::post('/send_recovery_email', 'Histogram2Controller@send_recovery')->name('send_recovery_email');
	Route::get('/pemulihan_gambar/reset/{code}', 'Histogram2Controller@reset_cover')->name('reset_cover');
	Route::post('/update_cover', 'Histogram2Controller@update_cover')->name('update_cover');

	Route::get('/pemulihan_gambar/reset', function()
	{
		return redirect()->route('histogram2.pemulihan_gambar');
	});
});

Route::prefix('test_image')->name('test_image.')->group(function()
{
	Route::get('/', 'testImageController@index')->name('index');
	Route::post('/test', 'testImageController@test')->name('test');
	Route::get('/histogram', 'testImageController@chart_histogram')->name('chart.histogram');
	Route::post('/histogram', 'testImageController@get_histogram')->name('get.histogram');
});

Route::prefix('rsa')->name('rsa.')->group(function()
{
	Route::get('/keygen', 'RSAController@generate_key')->name('keygen');
	Route::get('/login', 'RSAController@view_login')->name('login');
});

Route::get('/tes-hash', function()
{
	$time = time();
	$text = $time."QWERTYUIOPASDFGHJKLZXCVBNMqwertyuiopasdfghjklzxcvbnm?!@%&#$,./;";
	// $md5pass = md5($text);
	var_dump(md5($text));  
	echo "<br>";
	var_dump(md5($time));

	// $pass = Hash::make(time(), [
	// 	'rounds' => 5
	// ]);
	// var_dump($pass);
	// echo "<br>"."new Pass: "."<br>";
	// $newPass = Hash::make($pass, [
	// 	'rounds' => 5
	// ]);
	// var_dump($newPass);
});

Route::get('/timediff', function()
{
	echo Carbon\Carbon::now()->diffInSeconds(Carbon\Carbon::parse('2020-02-11 21:41:47'));
});

Route::get('/hash-image', function ()
{
	$file1 = storage_path('app/public/temp_image/contoh_gambar.jpg');
	$file2 = storage_path('app/public/temp_image/hasil_histogram.png');
	$hashing1 = hash_file('md5', $file1);
	$hashing2 = hash_file('md5', $file2);
	echo "hashing file 1 : ".$hashing1."<br>"."hashing file 2 : ".$hashing2;
});

Route::get('/kripto', function ()
{
	$msg = "bambangWk@gmail.com bambangwk";
	// $msg = 1;
	$chip = encrypt($msg)."-/-1234567";
	echo $chip."<br><br>";

	$real_msg = '';
	try {
		$real_msg = decrypt($chip);
	} catch (Exception $e) {
		echo "dekripsi error!";
	}
	echo "dekripsi: "."<br>".$real_msg;
	// try {
	// } catch (DecryptException $e) {
	// 	echo $e;
	// }
});