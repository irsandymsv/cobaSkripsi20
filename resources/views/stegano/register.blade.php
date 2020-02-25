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

		.form-field input:not([type="file"]){
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
			<form action="{{ route('stegano.registrasi.store') }}" method="post">
				@csrf
				
				<h2 class="form_title">Registrasi</h2>

				<div class="form-field">
					<label for="username"><b>Username</b></label>
					<input type="text" name="username">
				</div>

				<div class="form-field">
					<label for="email"><b>Email</b></label>
					<input type="email" name="email">
				</div>

				<div class="form-field">
					<label for="cover_photo"><b>Cover Photo</b></label>
					<input type="file" name="cover_photo">
					<small class="small_note">Upload foto yang nantinya akan digunakan sebagai password pada saat login</small>
				</div>

				<div class="form-field" style="text-align: center;">
					<button type="submit" class="btn">Register</button>
				</div>
			</form>
		</div>
	</div>
</body>
</html>