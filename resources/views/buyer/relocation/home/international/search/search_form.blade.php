@extends('app')
@section('content')
@inject('commonComponent', 'App\Components\CommonComponent')
<!-- Page top navigation Starts Here-->
@include('partials.page_top_navigation')
	
<!-- Header Starts Here -->		
<div class="clearfix"></div>
<div class="main">
   
   <div class="container">

		<div class="gray-bg">
			<div class="col-md-12 padding-none">
				
				<div class="col-md-12 form-control-fld">
					<div class="radio-block">
						<input type="radio" name="post_type" id="relocation_air"  value="1" checked /> 
							<label for="relocation_air"><span></span>Air</label>
						<input type="radio" name="post_type" id="relocation_ocean" value="2" />
						 <label for="relocation_ocean"><span></span>Ocean</label>
					</div>
				</div>

				<!-- Start - Spot [Air/Ocean] -->
				<div class="relocation_spot_show">
                    <div class="relocation_air_show">
						{!! Form::open(['url' => 'byersearchresults','id'=>'posts-form_buyer_relocationair','method'=>'get']) !!}
						{!!	Form::hidden('is_search',1,array('class'=>'form-control')) !!}
						{!! Form::hidden('post_type', '1', array('id' => 'post_type')) !!}

						    @include('buyer.relocation.home.international.search.airint._form')

						{!! Form::close() !!}	
                    </div>
                     <div class="relocation_ocean_show" style="display:none">
						{!! Form::open(['url' => 'byersearchresults','id'=>'relocationint_ocean_getquote','method'=>'get']) !!}
						{!!	Form::hidden('is_search',1,array('class'=>'form-control')) !!}
						{!! Form::hidden('post_type', '2', array('id' => 'post_type')) !!}

							   @include('buyer.relocation.home.international.search.ocean._form')

						{!! Form::close() !!}	
					 </div>
	        	</div>
				<!-- End - Spot [Air/Ocean] -->

				
		
			</div>		
		</div>

	</div>
</div>
<div class="clearfix"></div>
			
@include('partials.footer')
@endsection