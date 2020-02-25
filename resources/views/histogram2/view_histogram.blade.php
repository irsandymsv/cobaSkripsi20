<!DOCTYPE html>
<html>
<head>
	<title>view histogram</title>
</head>
<body>
	<h1>lihat histogram gambar</h1>
	<form action="{{ route('histogram2.show_histogram') }}" method="post" enctype="multipart/form-data">
		@csrf

		<input type="file" name="cover_photo">
		<br><br>
		
		<button type="submit">Kirim</button>
	</form>
</body>
</html>