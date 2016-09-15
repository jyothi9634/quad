@extends('app')
@section('content')
<!-- Page top navigation Starts Here-->
@include('partials.page_top_navigation')
	
		<!-- Header Starts Here -->		
		<div class="clearfix"></div>
		<div class="main">

			<div class="container">
				@include('relocationglobal.buyers._searchform')
			</div>
			</div>
			<div class="clearfix"></div>
			
@include('partials.footer')
@endsection