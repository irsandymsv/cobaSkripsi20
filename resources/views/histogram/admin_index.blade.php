<!DOCTYPE html>
<html>
<head>
	<title>Admin Image</title>
	<style type="text/css">
		.bg {
			padding: 30px;
		}

		#tbl_gambar{
			border-collapse: collapse;
		}

		td, th{
			padding: 5px;
			border: 1px solid black;
		}
	</style>
</head>
<body>
	<div class="bg">
		<div>
			<h1>Admin Index Gambar</h1>

			<p>
				<a href="{{ route('histogram.create_image') }}">Tambah Gambar</a>
			</p>

			<h3>Tabel Gambar</h3>
			<table id="tbl_gambar">
				<tr>
					<th>No</th>
					<th>Id</th>
					<th>Path</th>
					<th>Width</th>
					<th>Height</th>
					<th>Kapasitas(bit)</th>
					<th>Opsi</th>
				</tr>

				@foreach ($image as $element)
					<tr>
						<td>{{ $loop->index+1 }}</td>
						<td>{{ $element->id }}</td>
						<td>{{ $element->image }}</td>
						<td>{{ $element->width }}</td>
						<td>{{ $element->height }}</td>
						<td>{{ $element->kapasitas }}</td>
						<td><a href="{{ route('histogram.view_image', $element->id) }}">Lihat</a></td>
					</tr>
				@endforeach
				
			</table>
		</div>
	</div>
</body>
</html>