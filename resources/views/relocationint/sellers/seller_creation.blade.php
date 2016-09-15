@extends('app')
@section('content')
        <!-- Page top navigation Starts Here-->
@include('partials.page_top_navigation')
{{--*/ $from_search = 0; /*--}}
{{--*/ $air_display = "block"; /*--}}
{{--*/ $ocean_display = "none"; /*--}}

@if(Session::has('seller_searchrequest_relocationint_type'))
    {{--*/ $search_relocation_inttype = Session::get('seller_searchrequest_relocationint_type'); /*--}}
    @if($search_relocation_inttype != "")
        {{--*/ $from_search = 1; /*--}}
    @else
      {{--*/ $from_search = 0; /*--}}
    @endif
@endif

@if($from_search == 0)
		{{--*/ $air_display = "block"; /*--}}
		{{--*/ $ocean_display = "none"; /*--}}	
@else
	@if($search_relocation_inttype == 1)
		{{--*/ $air_display = "block"; /*--}}
		{{--*/ $ocean_display = "none"; /*--}}
	@else
		{{--*/ $air_display = "none"; /*--}}
		{{--*/ $ocean_display = "block"; /*--}}
	@endif
@endif


<div class="main">
	<div class="container">
		<span class="pull-left"><h1 class="page-title">Seller Post Rate Card</h1>
			<a class="change-service" data-toggle="modal" data-target="#change-service">Change Service</a></span>
		<div class="clearfix"></div>
		<div class="col-md-12 padding-none">
			<div class="main-inner"> 
				<!-- Right Section Starts Here -->
				<div class="main-right">
					<div>
						<div class="col-md-12 padding-none filter">
						
							<div class="col-md-12 inner-block-bg inner-block-bg1  border-bottom-none padding-bottom-none margin-bottom-none">
						
								<div class="col-md-12 form-control-fld">
									@include('relocationint.air_ocean')
								</div>
							</div>	
							<!--Calling Partial for Internation AIR and OCEAN -->
							<div class="relocation_int_air"  style="display:{{$air_display}}">
							@include('relocationint.airint.sellers.seller_creation')
							</div>
						
							
							<div class="relocation_int_ocean" style="display:{{$ocean_display}};" >
							@include('relocationint.ocean.sellers.seller_creation')
							</div>
							<!--Calling Partial for Internation AIR and OCEAN -->
						
					</div>
				</div>
				<!-- Right Section Ends Here -->
			</div>
		</div>
		<div class="clearfix"></div>
	</div>
</div>

@include('partials.footer')
@endsection



