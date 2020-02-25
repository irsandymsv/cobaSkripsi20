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
		}

		.small_note{
			display: block;
			color: grey;
		}

		label	{
			display: block;
		}

		#card_success{
			display: none;
		}
	</style>
</head>
<body>
	<div id="bg">
		
		<div class="card" id="card_form">
			<form action="{{ route('stegano.registrasi.store') }}" method="post" enctype="multipart/form-data">
				@csrf
				
				<h2 class="form_title">Registrasi</h2>

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
					<label for="nama"><b>Nama</b></label>
					<input type="text" name="nama" value="{{ old('nama') }}">

					@error('nama')
						<span class="invalid-feedback" role="alert" style="color: red;">
							<strong>{{ $message }}</strong>
						</span>
					@enderror
				</div>

				<div class="form-field">
					<label for="no_hp"><b>No HP</b></label>
					<input type="text" name="no_hp" value="{{ old('no_hp') }}">

					@error('no_hp')
						<span class="invalid-feedback" role="alert" style="color: red;">
							<strong>{{ $message }}</strong>
						</span>
					@enderror
				</div>

				<div class="form-field">
					<label for="tgl_lahir"><b>Tanggal Lahir</b></label>
					<input type="date" name="tgl_lahir" value="{{ old('tgl_lahir') }}">

					@error('tgl_lahir')
						<span class="invalid-feedback" role="alert" style="color: red;">
							<strong>{{ $message }}</strong>
						</span>
					@enderror
				</div>

				<div class="form-field">
					<label for="gender"><b>Jenis Kelamin</b></label>
					<label><input type="radio" name="gender" value="Laki-laki" {{ (old('gender') == "Laki-laki"? "checked":"") }}> Laki-laki </label>
					<label><input type="radio" name="gender" value="Perempuan" {{ (old('gender') == "Perempuan"? "checked":"") }}> Perempuan </label>

					@error('gender')
						<span class="invalid-feedback" role="alert" style="color: red;">
							<strong>{{ $message }}</strong>
						</span>
					@enderror
				</div>

				<div class="form-field">
					<label for="cover_photo"><b>Cover Photo</b></label>
					<input type="file" name="cover_photo" accept="image/*">
					<small class="small_note">Upload foto yang nantinya akan digunakan sebagai password pada saat login</small>

					@error('cover_photo')
						<span class="invalid-feedback" role="alert" style="color: red;">
							<strong>{{ $message }}</strong>
						</span>
					@enderror
					@if (session('resolusiMin'))
						<span class="invalid-feedback" role="alert" style="color: red;">
							<strong>{{ session('resolusiMin') }}</strong>
						</span>
					@endif
				</div>

				<div class="form-field" style="text-align: center;">
					<button type="submit" id="btn_register">Register</button>
					<br><br>
					<span>Sudah Punya akun? </span><a href="{{ route('stegano.login') }}">Login di Sini</a>
				</div>
			</form>
		</div>

		<div class="card" id="card_success">
			<h1>Selamat</h1>
			<p> Registrasi Berhasil, klik <a href="{{ route('stegano.dashboard') }}">dashboard</a> untuk masuk ke akun anda</p>
		</div>
	</div>

	<script src="{{asset('/js/jquery-3.4.1.min.js')}}"></script>
	<script type="text/javascript">
		$("#btn_register").click(function(event) {
			$("#card_form").delay(7000).hide();
			$("#card_success").delay(7000).show();
		});
	</script>
</body>
</html>