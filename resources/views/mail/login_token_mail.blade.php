<!DOCTYPE html>
<html>
<head>
	<title></title>
	<style type="text/css">
		#bg{
			background: lightgrey;
			padding: 30px;
		}

		#card{
			font-family: verdana;
			padding: 15px;
		}

		#card h1{
			text-align: center;
		}
	</style>
</head>
<body>
	<div id="gb">
		<div id="card">
			<h1>Dear, {{ $user->nama }}</h1>

			<p>
				Berikut adalah login token anda:
			</p>
			<h3><b>{{ $token->email_token }}</b></h3>
			<p>
				Token hanya berlaku selama 10 menit
			</p>

			<br>
			<p>Terima Kasih, ttd</p>
			<br>
			{{ config('app.name') }}
		</div>
	</div>
</body>
</html>