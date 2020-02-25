<!DOCTYPE html>
<html>
<head>
	<title>Steganografi</title>
	<style type="text/css">
		body{
			margin: 0;
			padding: 0;
		}

		#bg{
			background: lightgrey;
			padding: 100px;
		}

		.card	{
			width: 70%;
			background: white;
			padding: 30px;
			margin: 25px auto;
			vertical-align: middle;
		}

		.card form{
			margin: auto;
			padding: 25px;
		}

		.card form .form_title{
			text-align: center;
		}

		.form-field{
			display: block;
			margin: 20px 0;
		}

		input[type="text"], input[type="email"]{
			width: 100%;
			height: 25px;
			border-radius: 3px;
			box-shadow: none;
		}

		.small_note{
			display: block;
			color: grey;
		}

		label	{
			display: block;
		}
	</style>
</head>
<body>
	<div id="bg">
		<div class="card">
			<form action="{{ route('stegano.login.check') }}" method="post" enctype="multipart/form-data">
				@csrf
				
				<h2 class="form_title">Login</h2>

				<div class="form-field">
					<label for="email"><b>Email</b></label>
					<input type="email" name="email" value="{{ old('email') }}">

					@error('email')
						<span class="invalid-feedback" role="alert" style="color: red;">
							<strong>{{ $message }}</strong>
						</span>
					@enderror
				</div>

				<div class="form-field">
					<label for="cover_photo"><b>Cover Photo</b></label>
					<input type="file" name="cover_photo">
					<small class="small_note">Upload foto untuk login</small>

					@error('cover_photo')
						<span class="invalid-feedback" role="alert" style="color: red;">
							<strong>{{ $message }}</strong>
						</span>
					@enderror
				</div>

				@if (Session('not_found'))
					<div class="form-field">
						<span class="invalid-feedback" role="alert" style="color: red;">
							<strong>{{ Session('not_found') }}</strong>
						</span>
					</div>
				@endif

				<div class="form-field" style="text-align: center;">
					<button type="submit" class="btn">Login</button>
					<br><br>
					<span>Belum Punya akun? </span><a href="{{ route('stegano.index') }}">Registrasi di Sini</a>
				</div>

				<div class="form-field" style="text-align: center;">
					<p>
						Kehilangan gambar cover anda ? <a href="{{ route('stegano.lupa_password') }}">klik Di sini</a> untuk login menggunakan kode rahasia yang dikirim melalui email anda.
					</p>
				</div>
				
			</form>
		</div>
	</div>
</body>
</html>