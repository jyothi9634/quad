@extends('app')
@section('content')
<!-- Page top navigation Starts Here-->
@include('partials.page_top_navigation')


<div class="main">
	<div class="container">
            
            {!! Form::open(['url' => 'buyersearchresults','id'=>'posts_form_sellersearch_relocationpet','method'=>'get']) !!}
            <div class="home-search gray-bg margin-top-none">
                @include('seller.relocation.home.petmove.search._form')
            </div> 
            
            <div class="col-md-4 col-md-offset-4">
                <button class="btn theme-btn btn-block">Search</button>
            </div>
            
            {!! Form::close() !!}
            	
            <div class="clearfix"></div>

	</div>
</div>

@include('partials.footer')
@endsection