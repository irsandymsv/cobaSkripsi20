<!DOCTYPE html>
<html lang="en">
<head>
	<title>Fasilkom Hosting | @yield("page_title")</title>
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
	@yield("custom_css")
	<style type="text/css">
		.top-index-bg{
			background-image: url('{{asset("/cloud83/img/bg.jpg")}}'); 
			padding-top: 90px;
			padding-right: 30px;
			padding-left: 30px;
			padding-bottom: 20px;
		}	

		.invalid-feedback{
			display: block;
			font-size: 12px;
		}
	</style>

</head>
<body>
	@yield("page_loader")

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
					<a href="{{ route('histogram2.login') }}" class="site-btn sb-c2">Log In</a>
					{{-- <a href="#" class="site-btn sb-c3">Register</a> --}}
				</div>
			</div>
		</div>
	</header>
	<!-- Header section end -->

	@yield("content")

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

	@yield("script")
</body>
</html>
