@extends('app')
@section('content')
<!-- Page top navigation Starts Here-->
@include('partials.page_top_navigation')
<!-- Header Starts Here -->		
<div class="clearfix"></div>
<div class="main">
	<div class="container">
		{!! Form::open(['url' => 'byersearchresults','id'=>'relocation_domestic_office_buyersearch_sellers','method'=>'get']) !!}
			@include('buyer.relocation.office.domestic.search._form') 
		{!! Form::close() !!}	
	</div>
</div>
<div class="clearfix"></div>
			
@include('partials.footer')
@endsection