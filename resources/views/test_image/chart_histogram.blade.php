<!DOCTYPE html>
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
		<div class="hasil_histogram">
			<h3>Histogram Gambar Asli</h3>
			<canvas id="barChart" class="chart_canvas"></canvas>
			{{-- <br><br> --}}

			<h3>Histogram Gambar stego</h3>
			<canvas id="barChart2" class="chart_canvas"></canvas>
		</div>

		<br><br><br>
	</div>

	<script src="{{ asset('/js/jquery-3.4.1.min.js') }}"></script>
	<!-- New ChartJS -->
	<script src="{{asset('/chart.js/Chart.bundle.min.js')}}"></script>
	<script type="text/javascript">
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
</body>
</html>