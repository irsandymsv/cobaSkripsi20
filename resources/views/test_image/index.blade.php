<!DOCTYPE html>
<html>
<head>
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<title>Test PSNR Stego Image</title>
	<style type="text/css">
		.bg{
			padding: 25px;
		}

		.wraper{
			width: 45%;
			float: left;
			padding: 20px;
		}

		.wrap_container{
			overflow: hidden;
		}

		.wrap_container::after{
			float: none;
			clear: both;
		}

		.img_container{
			display: none;
			width: 80%;
			height: auto;
		}

		.img_container img{
			width: 100%;
			height: auto;
			object-fit: cover;
		}

		#tes_submit{
			padding: 5px;
			font-size: 16px;
		}

		.hasil_tes{
			display: none;
		}

		.err_msg{
			display: none;
		}

		.err_msg span{
			color: red;
			font-weight: bold;
		}
	</style>
</head>
<body>
	<div class="bg">
		<h1>Test PSNR Image</h1>
		<form action="{{ route('test_image.test') }}" method="post" enctype="multipart/form-data">
			@csrf
			<div class="wrap_container">
				<div class="wraper">
					<h2>Pilih gambar asli (SEBELUM disisipi pesan)</h2>
					<label><b>Gambar asli</b></label><br>
					<input type="file" name="gambar1" id="gambar1">
					<br><br>
					@error('gambar1')
						<span class="invalid-feedback" role="alert" style="color: red;">
							<strong>{{ $message }}</strong>
						</span>
					@enderror

					<div class="img_container" id="img_container1">
						<img src="#" id="preview_img1">
					</div>
				</div>
				<div class="wraper">
					<h2>Pilih gambar stego (SETELAH disisipi pesan)</h2>
					<label><b>Gambar stego</b></label><br>
					<input type="file" name="gambar2" id="gambar2">
					<br><br>
					@error('gambar2')
						<span class="invalid-feedback" role="alert" style="color: red;">
							<strong>{{ $message }}</strong>
						</span>
					@enderror

					<div class="img_container" id="img_container2">
						<img src="#" id="preview_img2">
					</div>
				</div>
			</div>

			<br><br>
			<button type="submit" id="tes_submit">Test</button> <br>
		</form>
		<br><br>

		<div class="hasil_tes">
			<span><b>Nilai PSNR = </b></span>
			<br>
		</div>

		<div class="err_msg">
			<span></span>
			<br>
		</div>
	</div>

	<script src="{{ asset('/js/jquery-3.4.1.min.js') }}"></script>
	<script type="text/javascript">
		function readURL(input, img_container) {
		 	if (input.files && input.files[0]) {
		   	var reader = new FileReader();

		   	reader.onload = function(e) {
		      	$(img_container).show();
		      	$(img_container+' img').attr('src', e.target.result);
		    	}

		   	reader.readAsDataURL(input.files[0]);
		  	}
		}

		$("#gambar1").change(function() {
		 	readURL(this, '#img_container1');
		});

		$("#gambar2").change(function() {
		 	readURL(this, '#img_container2');
		});

		// $('#tes_submit').click(function(event) {
		// 	event.preventDefault();
		// 	gambar1 = $('input[name="gambar1"]').val();
		// 	gambar2 = $('input[name="gambar2"]').val();

		// 	$.ajax({
		// 		url: '',
		// 		type: 'post',
		// 		// dataType: '',
		// 		data: {'gambar1': gambar1, 'gambar2': gambar2},
		// 	})
		// 	.done(function(hasil) {
		// 		console.log("success");
		// 	})
		// 	.fail(function() {
		// 		console.log("error");
		// 	});
			
		// });
	</script>
</body>
</html>