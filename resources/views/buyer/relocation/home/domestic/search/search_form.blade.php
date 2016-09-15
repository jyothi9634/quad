@extends('app')
@section('content')
@include('partials.page_top_navigation')
	<div class="main">
		<div class="container">
			{!! Form::open(['url' => 'byersearchresults','id'=>'posts-form_buyer_relocation','method'=>'get']) !!}
				<div class="home-search gray-bg margin-top-none">
					@include('buyer.relocation.home.domestic.search._form') 
				</div>
				<div class="col-md-4 col-md-offset-4">
					{!! Form::submit('&nbsp; Search &nbsp;', ['name' => 'Search','class'=>'btn theme-btn btn-block','id' => 'Search']) !!}
				</div>	
			{!! Form::close() !!}	
		</div>
	</div>
	<div class="clearfix"></div>
@include('partials.footer')
@endsection