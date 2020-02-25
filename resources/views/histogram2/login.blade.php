<!DOCTYPE html>
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
</html>