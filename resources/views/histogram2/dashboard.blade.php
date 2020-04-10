{{-- <!DOCTYPE html>
<html>
<head>
	<title>Steganografi</title>
	<style type="text/css">
		body{
			margin: 0;
			padding: 0;
		}

		#bg{
			background: lightgrey;
			padding: 100px;
		}

		.card	{
			width: 70%;
			background: white;
			padding: 30px;
			margin: 25px auto;
			vertical-align: middle;
		}
	</style>
</head>
<body>
	<div id="bg">
		<div class="card">
			<h1>Dashboard</h1>

			<h3>
				Welcome, {{ Auth::user()->nama }}
			</h3>
			
			<p>
				Klik link Reset Gambar untuk membuat gambar baru jika anda belum mendapatkan atau kehilangan gambar password anda. <a href="#">Reset Gambar</a>
			</p>
			
			<a href="{{ route('histogram2.logout') }}">Logout</a>
			<br>
		</div>
	</div>
	
</body>
</html> --}}

@extends('templates.user_panel')
@section('page_title')
Dashboard
@endsection

@section('content')
<main>
  <div class="container-fluid">
    <h1 class="mt-4">Dashboard</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Dashboard</li>
    </ol>

    <div class="card mb-4">
    	<div class="card-body">
    		<h4>Selamat Datang</h4>
    		<p>{{ Auth::user()->nama }} - Member sejak {{ Carbon\Carbon::parse(Auth::user()->created_at)->locale('id_ID')->isoFormat('D MMMM Y') }}</p> 
    	</div>
    </div>

    @if (Session('pemulihan_sukses'))
    <div class="alert alert-success alert-dismissible" role="alert">
      <strong>Selamat!</strong> {{ Session('pemulihan_sukses') }}
      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>
    @endif
		@php
			Session::forget('pemulihan_sukses');
		@endphp

    @if ($cover_exist)
    	<div class="card mb-4" id="download_card">
    		<div class="card-header"><i class="fas fa-image mr-2"></i>Gambar Cover</div>
    		<div class="card-body">
    			<p style="float: left;">
    				Silahkan download gambar cover anda untuk digunakan ketika log in kembali ke akun anda. Harap download gambar cover ini sebelum anda keluar (Log out) dari akun anda.
    			</p>
    			<a href="{{ route('histogram2.download_cover') }}" style="float: right;" class="btn btn-outline-success btn-lg" id="downlod_cover_btn"><i class="fas fa-download mr-2"></i>Download</a>
    		</div>
    	</div>
    @endif

  </div>
</main>
@endsection

@section('script')
	@if ($cover_exist)
	<script type="text/javascript">
		$("#downlod_cover_btn").click(function(event) {
			$("#download_card").hide();
		});

		$("#logout_link").click(function(event) {
		  event.preventDefault();
		  var display_card = $("#download_card").css('display');
		  if (display_card != "none") {
		  	alert("Harap donwload gambar cover anda dahulu sebelum keluar dari akun anda.");
		  }
		  else{
		  	$("#logout_link").off().trigger('click');
		  }
		});

		// $(window).on('beforeunload',function(){
		// 	var display_card = $("#download_card").css('display');
		// 	if(display_card != "none"){
  //     	return 'Apakah anda yakin ingin pergi sebelum mendownload gambar cover anda? Anda tidak akan dapat masuk ke akun anda tanpa gambar cover anda.';
		// 	}
  //   });
	</script>
	@endif
@endsection