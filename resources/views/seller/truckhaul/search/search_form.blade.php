@extends('app')
@section('content')

<!-- Page top navigation Starts Here-->
@include('partials.page_top_navigation')
<div class="main">
	<div class="container container-inner">

		<!-- Left Nav Starts Here -->
		<div class="home-search gray-bg margin-bottom-none padding-bottom-none border-bottom-none margin-top-none">
			
			@include('seller.truckhaul.search._form')
		
		</div>
		<div class="clearfix"></div>

	</div>
</div>
@include('partials.footer')
@endsection