@extends('app')
@section('content')
<!-- Page top navigation Starts Here-->
@include('partials.page_top_navigation')
	
		<!-- Header Starts Here -->		
		<div class="clearfix"></div>
		<div class="main">

			<div class="container">
				@include('buyer.relocation.home.global_mobility.search._form')
			</div>
			</div>
			<div class="clearfix"></div>
			
@include('partials.footer')
@endsection