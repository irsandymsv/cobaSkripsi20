<?php
use Illuminate\Support\Facades\Hash;
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

Route::get('/tes', function()
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



//Histogram Shifting
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

Route::prefix('histogram2')->name('histogram2.')->group(function()
{
	Route::get('/register', 'Histogram2Controller@register')->name('register');
	Route::post('/store_user', 'Histogram2Controller@store_user')->name('store.user');
	Route::get('/login', 'Histogram2Controller@login')->name('login');
	Route::post('/checkLogin', 'Histogram2Controller@checkLogin')->name('checkLogin');
	Route::get('/dashboard', 'Histogram2Controller@dashboard')->name('dashboard');
	Route::get('/logout', 'Histogram2Controller@logout')->name('logout');
	Route::get('/view_histogram', 'Histogram2Controller@view_histogram')->name('view_histogram');
	Route::post('/show_histogram', 'Histogram2Controller@show_histogram')->name('show_histogram');
});

Route::prefix('test-image')->name('test_image.')->group(function()
{
	Route::get('/', 'testImageController@index')->name('index');
	Route::post('/test', 'testImageController@test')->name('test');
});