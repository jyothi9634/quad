@extends('app')
@section('content')
<!-- Page top navigation Starts Here-->
@include('partials.page_top_navigation')

<div class="main">
	<div class="container">
		
		{!! Form::open(['url' =>'byersearchresults','id' => 'buyer_search_form' , 'autocomplete'=>'off','method'=>'get']) !!}	
		<div class="home-search gray-bg margin-top-none">
			<div class="col-md-12 padding-none">
                                            
                @include('buyer.truckhaul.search._form')
						
			</div>
		</div>

		<div class="container">
			<div class="col-md-4 col-md-offset-4">
				{!! Form::submit('&nbsp; Search &nbsp;', ['name' => 'Search','class'=>'btn theme-btn btn-block','id' => 'Search']) !!}	
			</div>
		</div>
		{!! Form::close() !!}
	
	</div>
</div>

<!-- Include static content block on the search page and footer -->
@include('partials.searchcontentblock')
@include('partials.footer')

@endsection