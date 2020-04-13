{{-- <!DOCTYPE html>
<html>
<head>
	<title>Histogram Login</title>
</head>
<body>
	<div>
		<h1>Login Histogram2</h1>
		<form action="{{ route('histogram2.checkLogin') }}" method="post" enctype="multipart/form-data">
			@csrf
			
			<label>Pilih Gambar</label><br>
			<input type="file" name="cover_photo">
			<br><br>

			@error('cover_photo')
				<span class="invalid-feedback" role="alert" style="color: red;">
					<strong>{{ $message }}</strong>
				</span>
			@enderror

			@if (Session('gambar_salah'))
				<span class="invalid-feedback" role="alert" style="color: red;">
					<strong>{{ Session('gambar_salah') }}</strong>
				</span>
			@endif

			@if (Session('not_found'))
				<span class="invalid-feedback" role="alert" style="color: red;">
					<strong>{{ Session('not_found') }}</strong>
				</span>
			@endif

			<br><br>
			<button type="submit">Log In</button>
		</form>

		<br><br>
		<a href="{{ route('histogram2.register') }}">Register Di sini</a>
	</div>
</body>
</html> --}}

@extends("templates.template_view")
@section("page_title")
	Log In
@endsection

@section("custom_css")
<style type="text/css">
	.top-index-bg{
		/*padding: 250px 30px;*/
	}

	.inner{
		width: 60%;
		margin: auto;
		margin-top: 110px;
		margin-bottom: 110px;
	}	

	.inner h3{
		text-align: center;
	}

	button {
	  width: 100px;
	  height: 35px;
	  margin: unset;
	  /*margin-top: 10px;*/
	}

	@media only screen and (max-width: 991px) {
	  .inner {
	   	width: 85%;
	 	} 
	}
	
	@media only screen and (max-width: 767px) {
	  .inner {
	    width: 100%;
	  }
	}
</style>
@endsection

@section("content")
<section class="hero-section">
	<div class="top-index-bg">
		<div class="inner">
			<form action="{{ route('histogram2.checkLogin') }}" method="post" enctype="multipart/form-data">
				@csrf
				
				<h3>Masuk ke Akun</h3>
				<div class="form-wrapper">
					<label for="cover_photo">Pilih Gambar Cover</label>
					<input type="file" class="form-control" name="cover_photo" accept="image/jpeg,image/png">
					<small>Pilih gambar cover yang anda dapatkan ketika melakukan registrasi</small>

					@error('cover_photo')
						<span class="invalid-feedback" role="alert" style="color: red;">
							<strong>{{ $message }}</strong>
						</span>
					@enderror

					@if (Session('error_found'))
						<span class="invalid-feedback" role="alert" style="color: red;">
							<strong>{{ Session('error_found') }}</strong>
						</span>
					@endif

					@if (Session('user_not_found'))
						<span class="invalid-feedback" role="alert" style="color: red;">
							<strong>{{ Session('user_not_found') }}</strong>
						</span>
					@endif
				</div>
				<br><br>

				<div class="row">
					<div class="col-md-6">
						<button type="submit">Log In</button>
					</div>
					<div class="col-md-6" style="text-align: right;">
						<span>Belum punya akun ? <a href="{{ route('histogram2.index') }}">Register</a></span><br>
					</div>
				</div>
				<br>
				<div style="text-align: center;">
					<span>Kehilangan gambar cover ? Gunakan <a href="{{ route('histogram2.pemulihan_gambar') }}">Pemulihan (recovery) Gambar Cover</a> untuk mendapatkan gambar cover baru</span>
				</div>
			</form>
		</div>
	</div>
</section>
@endsection

@section("script")
<script src="{{asset('/regform-25/js/jquery-3.3.1.min.js')}}"></script>
@endsection