{{-- <!DOCTYPE html>
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
	
</body>
</html> --}}

@extends('templates.template_view')
@section('page_title')
Pengujian Kualitas Gambar
@endsection

@section('custom_css')
<meta name="csrf-token" content="{{ csrf_token() }}">
<style type="text/css">
	.top-index-bg{
		min-height: 600px;
		padding-right: 20px;
		padding-left: 20px;
	}

	.base_card{
		background: white;
		padding: 25px 15px;
		min-height: 480px;
	}

	.section-title{
		margin-bottom: 0;
	}

	#input_form{
		margin-top: 30px;
	}

	.img_container{
		display: none;
		width: 100%;
		height: auto;
		margin: auto;
	}

	.img_container img{
		width: 100%;
		height: auto;
		object-fit: cover;
	}

	#error_msg{
		display: none;
		font-size: 17px;
		color : red;
		font-weight: bold;
		text-align: center;
		margin-top: 15px;
	}

	#btn_wrapper{
		text-align: center;
		margin-top: 25px;
	}

	#card_hasil{
		display: none;
		width: 90%;
		text-align: center;
		margin: auto;
		margin-bottom: 25px;
		padding: 25px 100px;
		border: 3px solid #25ae88;
	}

	#card_hasil p{
		font-size: 18px;
	}

	#hasil_mse p{
		font-size: 15px;
	}

	#hasil_mse h4{
		font-size: 18px;
	}

	@media only screen and (max-width: 991px){
		.col-lg-6{
			margin-top: 20px;
		}
	}
</style>
@endsection

@section('content')
<section class="hero-section">
	<div class="top-index-bg">
		<div class="base_card">

			<div class="section-title">
				{{-- <p>The only ones</p> --}}
				<h2>Pengujian Kualitas Citra</h2>
			</div>

			<form method="post" enctype="multipart/form-data" id="input_form">
				<div class="row">
					<div class="col-lg-6">
						<div class="form-wrapper">
							<label for="gambar1">Pilih Gambar Asli (SEBELUM penyisipan)</label>
							<input type="file" name="gambar1" id="gambar1" accept="image/jpeg,image/png">
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
					</div>

					<div class="col-lg-6">
						<div class="form-wrapper">
							<label for="gambar2">Pilih Gambar Stego (SETELAH penyisipan)</label>
							<input type="file" name="gambar2" id="gambar2" accept="image/jpeg,image/png">
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
				</div>

				<div id="btn_wrapper">
					<button class="btn btn-primary" type="button" id="tes_submit">Tes Gambar</button>
				</div>

				<div id="error_msg">
					
				</div>
			</form>


			<div class="row" id="card_hasil">
				<div class="col-lg-12" id="hasil_pengukuran">
					<h4>Hasil Pengukuran</h4>

					<div id="hasil_psnr">
						<br>
						<p><b>PSNR</b></p>
						<h2 id="nilai_psnr">0</h2>
					</div>
					<br>
					<div id="hasil_mse">
						<p><b>MSE</b></p>
						<h4 id="nilai_mse">0</h4>
					</div>
				</div>
			</div>	

		</div>
	</div>
</section>
@endsection

@section('script')
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

	$('#tes_submit').click(function(event) {
		event.preventDefault();
		// var gambar1 = $('input[name="gambar1"]').val();
		// var gambar2 = $('input[name="gambar2"]').val();
		var form = $("#input_form")[0];
		var input = new FormData(form);

		$("#error_msg").hide();
		$.ajaxSetup({
      headers: {
      	'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
   	});

		$.ajax({
			url: '{{ route('test_image.test') }}',
			type: 'post',
			enctype: 'multipart/form-data',
			dataType: 'json',
			data: input,
			processData: false,
			contentType: false,
		})
		.done(function(hasil) {
			console.log("success");
			console.log(hasil);
			$("#nilai_psnr").text(hasil['psnr']);
			$("#nilai_mse").text(hasil['mse']);
			$("#card_hasil").show();
		})
		.fail(function() {
			console.log("error");
			$("#error_msg").text("Terjadi Kesalahan. Harap coba kembali");
			$("#error_msg").show();
		});
		
	});
</script>
@endsection