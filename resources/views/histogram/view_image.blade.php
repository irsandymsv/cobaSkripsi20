<!DOCTYPE html>
<html>
<head>
	<title>View Image</title>
</head>
<body>
	<div>
		<h1>View Image</h1>

		<a href="{{ route('histogram.admin_index') }}">Kembali</a>
		<br><br>
		<span>> width = {{ $image->width }}px</span><br>
		<span>> height = {{ $image->height }}px</span><br>
		<span>> peak = {{ $image->peak }}</span><br>
		<span>> zero = {{ $image->zero }}</span><br>
		<span>> kapasitas = {{ $image->kapasitas }} bit</span><br>

		<br><br>
		<img src="{{ asset('storage/'.$image->image) }}" width="400" height="400">
	</div>
</body>
</html>