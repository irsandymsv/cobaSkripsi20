<!DOCTYPE html>
<html lang="en">
<head>
	<title>Fasilkom UNEJ Hosting</title>
	<meta charset="UTF-8">
	<meta name="description" content="Fasilkom UNEJ Hosting">
	<meta name="keywords" content="cloud, hosting, creative, html">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<!-- Favicon -->
	<link href="{{asset('/cloud83/img/favicon.ico')}}" rel="shortcut icon"/>

	<!-- Google Font -->
	<link href="https://fonts.googleapis.com/css?family=Poppins:400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

	<!-- Stylesheets -->
	<link rel="stylesheet" href="{{asset('/cloud83/css/bootstrap.min.css')}}"/>
	<link rel="stylesheet" href="{{asset('/cloud83/css/font-awesome.min.css')}}"/>
	<link rel="stylesheet" href="{{asset('/cloud83/css/magnific-popup.css')}}"/>
	<link rel="stylesheet" href="{{asset('/cloud83/css/owl.carousel.min.css')}}"/>
	<link rel="stylesheet" href="{{asset('/cloud83/css/style.css')}}"/>
	<link rel="stylesheet" href="{{asset('/cloud83/css/animate.css')}}"/>

	<!--[if lt IE 9]>
	  <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
	  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<![endif]-->

	{{-- CSS Form Registration --}}
	<!-- LINEARICONS -->
	<link rel="stylesheet" href="{{asset('/regform-25/fonts/linearicons/style.css')}}">

	<!-- MATERIAL DESIGN ICONIC FONT -->
	<link rel="stylesheet" href="{{asset('/regform-25/fonts/material-design-iconic-font/css/material-design-iconic-font.min.css')}}">

	<!-- DATE-PICKER -->
	<link rel="stylesheet" href="{{asset('/regform-25/vendor/date-picker/css/datepicker.min.css')}}">
	
	<!-- STYLE CSS -->
	<link rel="stylesheet" href="{{asset('/regform-25/css/style.css')}}">

	{{-- custom CSS --}}
	<style type="text/css">
		.top-index-bg{
			background-image: url('{{asset("/cloud83/img/bg.jpg")}}'); 
			padding-top: 90px;
			padding-right: 30px;
			padding-left: 30px;
			padding-bottom: 20px;
		}

		.top-wrap .col-lg-5{
			margin-top: 60px;
		}

		.top-wrap h2{
			color: white;
		}

		.top-wrap p{
			color: white;
			font-size: 17px;
		}

		.form-group label{
			display: block;
			font-family: "Muli-SemiBold";
			font-size: 16px;
			color: #4c4c4c;
		}

		.invalid-feedback{
			display: block;
		}
	</style>
</head>
<body>
	<!-- Page Preloder -->
	<div id="preloder">
		<div class="loader"></div>
	</div>

	<!-- Header section -->
	<header class="header-section">
		<div class="container">
			<a href="{{ route('histogram2.index') }}" class="site-logo">
				<img src="{{asset('/cloud83/img/logo.png')}}" alt="logo">
			</a>
			<!-- Switch button -->
			<div class="nav-switch">
				<div class="ns-bar"></div>
			</div>
			<div class="header-right">
				<ul class="main-menu">
					{{-- <li class="active"><a href="index.html">Home</a></li> --}}
					{{-- <li><a href="about.html">About us</a></li>
					<li><a href="service.html">Services</a></li>
					<li><a href="blog.html">News</a></li>
					<li><a href="contact.html">Contact</a></li> --}}
				</ul>
				<div class="header-btns">
					<a href="#" class="site-btn sb-c2">Log In</a>
					{{-- <a href="#" class="site-btn sb-c3">Register</a> --}}
				</div>
			</div>
		</div>
	</header>
	<!-- Header section end -->


	<!-- Hero section -->
	<section class="hero-section">
		<div class="top-index-bg">
			<div class="row top-wrap">
				<div class="col-lg-5">
					<h2>The Best Hosting</h2>
					<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec malesuada lorem maximus mauris sceleri sque,</p>
					<p>at rutrum nulla dictum. Ut ac ligula sapien. Suspendisse cursus faucibus finibus.</p>
					<br><br>
				</div>

				<div class="col-lg-7">
					<div class="inner">
						<form action="{{ route('histogram2.store.user') }}" method="post" enctype="multipart/form-data">
							@csrf
							<h3>Registrasi</h3>
							
							<div class="form-group">
								<label for="">Nama</label>
								<input type="text" name="nama" class="form-control" value="{{ old('nama') }}">

								@error('nama')
									<span class="invalid-feedback" role="alert" style="color: red;">
										<strong>{{ $message }}</strong>
									</span>
								@enderror
							</div>

							<div class="form-group">
								<div class="row">
									<div class="col-sm-6">
										<label for="">Email</label>
										<input type="email" name="email" class="form-control" value="{{ old('email') }}">

										@error('email')
											<span class="invalid-feedback" role="alert" style="color: red;">
												<strong>{{ $message }}</strong>
											</span>
										@enderror
									</div>

									<div class="col-sm-6">
										<label for="">Password</label>
										<input type="password" name="password" class="form-control">

										@error('password')
											<span class="invalid-feedback" role="alert" style="color: red;">
												<strong>{{ $message }}</strong>
											</span>
										@enderror
									</div>
								</div>
							</div>

							<div class="form-group">
								<div class="row">
									<div class="col-sm-6">
										<label for="">No. HP</label>
										<input type="text" class="form-control" name="no_hp" value="{{ old('no_hp') }}">

										@error('no_hp')
											<span class="invalid-feedback" role="alert" style="color: red;">
												<strong>{{ $message }}</strong>
											</span>
										@enderror
									</div>

									<div class="col-sm-6">
										<label for="">Tanggal Lahir</label>
										<span class="lnr lnr-calendar-full"></span>
										<input type="text" name="tgl_lahir" class="form-control datepicker-here" data-language='en' data-date-format="dd M yyyy" id="dp1" value="{{ old('tgl_lahir') }}">

										@error('tgl_lahir')
											<span class="invalid-feedback" role="alert" style="color: red;">
												<strong>{{ $message }}</strong>
											</span>
										@enderror
									</div>
								</div>
							</div>

							<div class="form-group">
								<div class="row">
									<div class="col-sm-6">
										<label for="">Jenis Kelamin</label>
										<label><input type="radio" name="gender" value="Laki-laki" {{ (old('gender') == "Laki-laki"? "checked":"") }}> Laki-laki </label>
										<label><input type="radio" name="gender" value="Perempuan" {{ (old('gender') == "Perempuan"? "checked":"") }}> Perempuan </label>

										@error('gender')
											<span class="invalid-feedback" role="alert" style="color: red;">
												<strong>{{ $message }}</strong>
											</span>
										@enderror
									</div>

									<div class="col-sm-6">
										<label for="">Cover Photo</label>
										<input type="file" name="cover_photo" class="form-control" accept="image/jpeg,image/png">
										<small>Foto ini akan digunakan sebagai media Log In</small><br>

										@error('cover_photo')
											<span style="color: red;">
												<strong>{{ $message }}</strong>
											</span>
										@enderror

										@if (Session('peak_zero'))
											<span class="invalid-feedback" role="alert" style="color: red;">
												<strong>{{ Session('peak_zero') }}</strong>
											</span>
										@endif

										@if (Session('gambar_tdk_cukup'))
											<span class="invalid-feedback" role="alert" style="color: red;">
												<strong>{{ Session('gambar_tdk_cukup') }}</strong>
											</span>
										@endif
									</div>
								</div>
							</div>
							
							<button data-text="Daftar" type="submit">
								<span>Daftar</span>
							</button>
						</form>
					</div>
				</div>
			</div>
		</div>
	</section>
	<!-- Hero section end -->


	<!-- Feature section -->
	{{-- <section class="feature-section spad">
		<div class="container">
			<div class="row">
				<div class="col-md-4 feature">
					<img src="{{asset('/cloud83/img/feature-icons/1.png')}}" alt="#">
					<h4>Worldwide Support</h4>
					<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec malesuada lorem maximus mauris sceleri sque, at rutrum nulla dictum. </p>
				</div>
				<div class="col-md-4 feature">
					<img src="{{asset('/cloud83/img/feature-icons/2.png')}}" alt="#">
					<h4>Safe & Secure</h4>
					<p>Ipsum dolor sit amet, consectetur adipiscing elit. Donec malesuada lorem maximus mauris sceleri sque, at rutrum nulla dictum. </p>
				</div>
				<div class="col-md-4 feature">
					<img src="{{asset('/cloud83/img/feature-icons/3.png')}}" alt="#">
					<h4>Cloud Hosting</h4>
					<p>Donec malesuada lorem maximus mauris sceleri sque, at rutrum nulla dictum. Ut ac ligula sapien. Suspendisse cursus faucibus finibus.</p>
				</div>
			</div>
		</div>
	</section> --}}
	<!-- Feature section end -->


	<!-- Footer top section -->
	{{-- <section class="footer-top-section">
		<div class="container">
			<div class="row">
				<div class="col-lg-3 col-md-12">
					<div class="footer-widget about-widget">
						<img src="{{asset('/cloud83/img/logo.png')}}" alt="">
						<p>Sed ultrices interdum libero, laoreet facilisis dui fringilla ut. Nullam nisi sem, tristique ut sapien nec, tempus auctor purus. Maecenas eu lectus non dolor euismod dignissim vitae vel tortor. </p>
						<div class="social-links">
							<a href="#"><i class="fa fa-pinterest"></i></a>
							<a href="#"><i class="fa fa-facebook"></i></a>
							<a href="#"><i class="fa fa-twitter"></i></a>
							<a href="#"><i class="fa fa-dribbble"></i></a>
							<a href="#"><i class="fa fa-behance"></i></a>
							<a href="#"><i class="fa fa-linkedin"></i></a>
						</div>
					</div>
				</div>
				<div class="col-lg-3 col-sm-4">
					<div class="footer-widget">
						<h5 class="fw-title">Our Products</h5>
						<ul>
							<li><a href="#">Web Hosting</a></li>
							<li><a href="#">Reseller Hosting</a></li>
							<li><a href="#">VPS Hosting</a></li>
							<li><a href="#">Dedicated Servers</a></li>
							<li><a href="#">Windows Hosting</a></li>
							<li><a href="#">Cloud Hosting</a></li>
							<li><a href="#">Linux Servers</a></li>
							<li><a href="#">WordPress Hosting</a></li>
						</ul>
					</div>
				</div>
				<div class="col-lg-3 col-sm-4">
					<div class="footer-widget">
						<h5 class="fw-title">Our Solutions</h5>
						<ul>
							<li><a href="#">Reseller Hosting</a></li>
							<li><a href="#">WordPress Hosting</a></li>
							<li><a href="#">VPS Hosting</a></li>
							<li><a href="#">Dedicated Servers</a></li>
							<li><a href="#">Windows Hosting</a></li>
							<li><a href="#">Cloud Hosting</a></li>
							<li><a href="#">Linux Servers</a></li>
							<li><a href="#">Web Hosting</a></li>
						</ul>
					</div>
				</div>
				<div class="col-lg-3 col-sm-4">
					<div class="footer-widget">
						<h5 class="fw-title">Our Products</h5>
						<ul>
							<li><a href="#">Dedicated Servers</a></li>
							<li><a href="#">Windows Hosting</a></li>
							<li><a href="#">Cloud Hosting</a></li>
						</ul>
					</div>
					<div class="footer-widget">
						<h5 class="fw-title">Company</h5>
						<ul>
							<li><a href="#">Dedicated Servers</a></li>
							<li><a href="#">Windows Hosting</a></li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</section> --}}
	<!-- Footer top section end -->
	
	<!-- Footer section -->
	<footer class="footer-section">
		<div class="container">
			<div class="footer-nav">
				<ul>
					<li><a href="home.html">Home</a></li>
					{{-- <li><a href="about.html">About us</a></li>
					<li><a href="service.html">Services</a></li>
					<li><a href="blog.html">News</a></li>
					<li><a href="contact.html">Contact</a></li> --}}
				</ul>
			</div>
			<div class="copyright">
				<p><!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
					Copyright &copy;<script>document.write(new Date().getFullYear());</script> All rights reserved | This template is made with <i class="fa fa-heart-o" aria-hidden="true"></i> by <a href="https://colorlib.com" target="_blank">Colorlib</a>
				<!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
				</p>
			</div>
		</div>
	</footer>
	<!-- Footer section end -->


	<!--====== Javascripts & Jquery ======-->
	<script src="{{asset('/cloud83/js/jquery-3.2.1.min.js')}}"></script>
	<script src="{{asset('/cloud83/js/bootstrap.min.js')}}"></script>
	<script src="{{asset('/cloud83/js/owl.carousel.min.js')}}"></script>
	<script src="{{asset('/cloud83/js/jquery.magnific-popup.min.js')}}"></script>
	<script src="{{asset('/cloud83/js/circle-progress.min.js')}}"></script>
	<script src="{{asset('/cloud83/js/main.js')}}"></script>


	{{-- Registration Form --}}
	<script src="{{asset('/regform-25/js/jquery-3.3.1.min.js')}}"></script>
	<!-- DATE-PICKER -->
	<script src="{{asset('/regform-25/vendor/date-picker/js/datepicker.js')}}"></script>
	<script src="{{asset('/regform-25/vendor/date-picker/js/datepicker.en.js')}}"></script>
	<script src="{{asset('/regform-25/js/main.js')}}"></script>
</body>
</html>
