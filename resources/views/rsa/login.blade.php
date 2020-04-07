<!DOCTYPE html>
<html>
<head>
	<title>RSA</title>
</head>
<body>

	<form action="#" method="post" enctype="multipart/form-data" id="form_gambar">
		<label>Input gambar</label><br>
		<input type="file" name="gambar" id="gambar">
		<br><br>

		<button type="submit" id="submit">submit</button>
	</form>
	<br><br>

	<div id="image_wraper">
		<img src="#" style="display: none;" id="preview">
		<canvas id="myCanvas" style="display: none;"></canvas>
	</div>
	<br>

	<div id="output">
		
	</div>

	<script src="{{ asset('/js/jquery-3.4.1.min.js') }}"></script>
	<script type="text/javascript">
		$('#gambar').change(function(event) {
			
			if(this.files && this.files[0]){
				var gmbr = new Image();
				var image_url = URL.createObjectURL(this.files[0]);
			  gmbr.src = image_url;
			  $('#preview').attr('src', image_url);
			  gmbr.onload = function () {
			  	var width = this.width;
			  	var height = this.height;
			  	var c = document.getElementById('myCanvas');
			  	var ctx = c.getContext('2d');
			  	var img = document.getElementById('preview');
			  	ctx.drawImage(img, 0, 0);
			  	var imgData = ctx.getImageData(0,0,width,height);

			  	// var red = [];
			  	for (var i = 0; i < imgData.data.length; i+=4) {
			  		$("#output").append(imgData.data[i] + " "); //red
			  	}

					URL.revokeObjectURL(this.src);
			  }
			}
		});
	</script>
</body>
</html>