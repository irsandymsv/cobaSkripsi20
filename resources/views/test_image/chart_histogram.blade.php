{{-- <!DOCTYPE html>
<html>
<head>
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<title>Show Histogram</title>
	<style type="text/css">
		.bg{
			padding: 20px;
		}

		.hasil_histogram{
			display: none;
			width: 100%;
		}

		.chart_canvas{
			height:250px;
		}
	</style>
</head>
<body>
	<div class="bg">
		<h1>show histogram</h1>

		<form method="post" enctype="multipart/form-data" id="input_form">
			<label><b>Pilih Gambar Asli</b></label><br>
			<input type="file" name="gambar1" id="gambar1">
			<br><br>

			<label><b>Pilih Gambar Stego</b></label><br>
			<input type="file" name="gambar2" id="gambar2">
			<br><br>

			<button type="submit" id="show_histo">Show Histogram</button>
		</form>

		<br>
		

		<br><br><br>
	</div>
</body>
</html> --}}

@extends('templates.template_view')
@section('page_title')
Histogram Gambar
@endsection

@section('custom_css')
<meta name="csrf-token" content="{{ csrf_token() }}">
<style type="text/css">
	.top-index-bg{
		min-height: 600px;
		padding-right: 5px;
		padding-left: 5px;
	}

	.base_card{
		background: white;
		padding: 25px 5px;
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

	.hasil_histogram{
		display: none;
		width: 100%;
		padding-left: 5px;
		padding-right: 5px;
	}

	.chart_canvas{
		height:250px;
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
				<h2>Histogram Gambar</h2>
			</div>

			<form method="post" enctype="multipart/form-data" id="input_form">
				<div class="row">
					<div class="col-lg-6">
						<div class="form-wrapper">
							<label for="gambar1">Pilih Gambar Asli (SEBELUM penyisipan)</label>
							<input type="file" name="gambar1" id="gambar1" accept="image/jpeg,image/png">
							<br><br>
							{{-- @error('gambar1')
								<span class="invalid-feedback" role="alert" style="color: red;">
									<strong>{{ $message }}</strong>
								</span>
							@enderror --}}

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
							{{-- @error('gambar2')
								<span class="invalid-feedback" role="alert" style="color: red;">
									<strong>{{ $message }}</strong>
								</span>
							@enderror --}}

							<div class="img_container" id="img_container2">
								<img src="#" id="preview_img2">
							</div>
						</div>
					</div>
				</div>

				<div id="btn_wrapper">
					<button class="btn btn-primary" type="button" id="show_histo">Tampilkan</button>
				</div>

				<div id="error_msg">
					
				</div>
			</form>

			<div class="hasil_histogram">
				<br><br>
				<h4>Histogram Gambar Asli</h4>
				<canvas id="barChart" class="chart_canvas"></canvas>
				<br><br>
				<h4>Histogram Gambar stego</h4>
				<canvas id="barChart2" class="chart_canvas"></canvas>
			</div>

		</div>
	</div>
</section>
@endsection

@section('script')
<!-- New ChartJS -->
<script src="{{asset('/chart.js/Chart.bundle.min.js')}}"></script>
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


	$("#show_histo").click(function(event) {
		event.preventDefault();
		var form = $("#input_form")[0];
		var input = new FormData(form);
		// console.log('gambar : '+input);

		$.ajaxSetup({
      headers: {
      	'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
   	});

		$.ajax({
			url: '{{ route('test_image.get.histogram') }}',
			type: 'post',
			enctype: 'multipart/form-data',
			// dataType: 'default: Intelligent Guess (Other values: xml, json, script, or html)',
			data: input,
			processData: false,
			contentType: false,
		})
		.done(function(histogram) {
			console.log("success");
			console.log(histogram);
			$(".hasil_histogram").show();

			var label_histogram = [];
			for (var i = 0; i < 256; i++) {
				label_histogram[i] = i;
			}
			console.log('label : '+label_histogram);

			//chart Histogram gambar asli
			var ctx = $('#barChart').get(0).getContext('2d');
			var chart1 = new Chart(ctx, {
				type: 'bar',
				data: {
					labels : label_histogram,
					datasets: [{
						label: 'Jumlah Piksel',
			        	maxBarThickness: 3,
			        	data: histogram[0],
			        	backgroundColor: "rgba(54, 162, 235, 1)",
			        	borderWidth : 1
			    	}]
				},
				options: {
					scales : {
						xAxes : [{
							ticks: {
								autoSkip : true,
								maxRotation: 10
							}
						}]
					}
				}
			});

			//cahrt histogram gambar stego
			var ctx = $('#barChart2').get(0).getContext('2d');
			var chart2 = new Chart(ctx, {
				type: 'bar',
				data: {
					labels : label_histogram,
					datasets: [{
						label: 'Jumlah Piksel',
			        	maxBarThickness: 3,
			        	data: histogram[1],
			        	backgroundColor: "rgba(54, 162, 235, 1)",
			        	borderWidth : 1
			    	}]
				},
				options: {
					scales : {
						xAxes : [{
							ticks: {
								autoSkip : true,
								maxRotation: 10
							}
						}]
					}
				}
			});
		})
		.fail(function() {
			console.log("error");
		});
		
	});
</script>
@endsection