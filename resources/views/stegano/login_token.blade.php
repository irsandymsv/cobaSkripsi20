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
			<form action="{{ route('stegano.check_token', $user->id) }}" method="post" enctype="multipart/form-data">
				@csrf
				
				<h2 class="form_title">Input Token</h2>

				@if (Session('token_resended'))
					<div class="form-field">
						<span class="invalid-feedback" role="alert" style="color: red;">
							<strong>{{ Session('token_resended') }}</strong>
						</span>
					</div>
				@endif
				
				<p>
					Masukkan 6 digit token yang telah dikirm ke email anda
				</p>

				<div class="form-field">
					<label for="token"><b>Token</b></label>
					<input type="text" name="token" value="{{ old('token') }}">

					@error('token')
						<span class="invalid-feedback" role="alert" style="color: red;">
							<strong>{{ $message }}</strong>
						</span>
					@enderror
				</div>

				@if (Session('token_salah'))
					<div class="form-field">
						<span class="invalid-feedback" role="alert" style="color: red;">
							<strong>{{ Session('token_salah') }}</strong>
						</span>
					</div>
				@endif

				<div class="form-field" style="text-align: center;">
					<button type="submit" class="btn">Login</button>
				</div>

				<div class="form-field" style="text-align: center;">
					<p>
						Tidak menerima token? klik <a href="{{ route('stegano.resend_token', $user->id) }}">Di sini</a> untuk mengirim ulang token.
					</p>
				</div>

				<div class="form-field" style="text-align: center;">
					<p>
						<a href="{{ route('stegano.login') }}">Kembali</a>
					</p>
				</div>
				
			</form>
		</div>
	</div>
</body>
</html>