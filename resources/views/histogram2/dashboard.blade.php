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
	</style>
</head>
<body>
	<div id="bg">
		<div class="card">
			<h1>Dashboard</h1>

			<h3>
				Welcome, {{ Auth::user()->nama }}
			</h3>
			
			<p>
				Klik link Reset Gambar untuk membuat gambar baru jika anda belum mendapatkan atau kehilangan gambar password anda. <a href="#">Reset Gambar</a>
			</p>
			
			<a href="{{ route('histogram2.logout') }}">Logout</a>
			<br>
		</div>
	</div>
	@for ($i = 0; $i < 256; $i++)
		{{ $histogram[$i] }}<span>  </span>
	@endfor	
</body>
</html>