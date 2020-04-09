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
    	<div class="card-header"><i class="fas fa-image mr-2"></i>Gambar Cover</div>
    	<div class="card-body">
    		<button class="btn btn-outline-success btn-lg"><i class="fas fa-download mr-2"></i>Download</button>
    	</div>
    </div>

    <div class="card mb-4">
    	<div class="card-body">
    		<h3>Selamat Datang</h3>
    		<p>Nama User - Member sejak ...</p> 
    	</div>
    </div>
  </div>
</main>
@endsection

@section('script')
@endsection