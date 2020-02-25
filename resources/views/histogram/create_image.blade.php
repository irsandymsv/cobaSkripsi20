<!DOCTYPE html>
<html>
<head>
	<title>Create New Image</title>
</head>
<body>
	<div class="bg">
		<h1>Tambah Gambar</h1>
		<a href="{{ route('histogram.admin_index') }}">Kembali</a>
		<br>

		<form action="{{ route('histogram.store_image') }}" method="post" enctype="multipart/form-data">
			@csrf
			
			<label><b>Pilih gambar</b></label><br>
			<input type="file" name="gambar">
			<br><br>

			@error('gambar')
				<span class="invalid-feedback" role="alert" style="color: red;">
					<strong>{{ $message }}</strong>
				</span>
			@enderror

			@if (Session('sudah_ada'))
				<span class="invalid-feedback" role="alert" style="color: red;">
					<strong>{{ Session('sudah_ada') }}</strong>
				</span>
			@endif

			@if (Session('peak_sama_zero'))
				<span class="invalid-feedback" role="alert" style="color: red;">
					<strong>{{ Session('peak_sama_zero') }}</strong>
				</span>
			@endif

			<br><br>
			<button type="submit">Simpan</button>
		</form>
	</div>
</body>
</html>