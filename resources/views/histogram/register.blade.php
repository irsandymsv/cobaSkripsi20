<!DOCTYPE html>
<html>
<head>
	<title>Register</title>
	<style type="text/css">
		.form-field{
			display: block;
			margin: 20px 0;
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
	<div id="card_form">
		<form action="{{ route('histogram.store.user') }}" method="post">
			@csrf
			
			<h2>Registrasi</h2>

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
				<label for="email"><b>Email</b></label>
				<input type="email" name="email" value="{{ old('email') }}">

				@error('email')
					<span class="invalid-feedback" role="alert" style="color: red;">
						<strong>{{ $message }}</strong>
					</span>
				@enderror
			</div>

			<div class="form-field">
				<label for="password"><b>password</b></label>
				<input type="password" name="password" value="{{ old('password') }}"><br>
				<small>minimal 6 dan maksimal 12 karakter</small>

				@error('password')
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

			@if (Session('gambar_tdk_cukup'))
				<span class="invalid-feedback" role="alert" style="color: red;">
					<strong>{{ Session('gambar_tdk_cukup') }}</strong>
				</span>
			@endif

			<div class="form-field">
				<button type="submit" class="btn">Register</button>
			</div>
		</form>	

		<br><br>
		<a href="{{ route('histogram.login') }}">Login Di sini</a>
	</div>
	
	<div class="card" id="card_success">
		<h1>Selamat</h1>
		<p> Registrasi Berhasil, klik <a href="{{ route('histogram.dashboard') }}">dashboard</a> untuk masuk ke akun anda</p>
	</div>

	<script src="{{asset('/js/jquery-3.4.1.min.js')}}"></script>
	<script type="text/javascript">
		$("#btn_register").click(function(event) {
			@if (Session('registrasi_sukses'))
				console.log('{{ Session('registrasi_sukses') }}')
				$("#card_form").delay(7000).hide();
				$("#card_success").delay(7000).show();
			@endif
		});
	</script>
</body>
</html>