@extends('app') @section('content')
<div class="container container-inner">
	
		@if(Session::has('sumsg')) 
        <div class="flash">
		<p class="text-success col-sm-12 text-center flash-txt alert-success">
		{{ Session::get('sumsg') }}
		</p>
		</div>
		@endif
		@if(Session::has('succmsg')) 
        <div class="flash">
		<p class="text-success col-sm-12 text-center flash-txt alert-success">
		{{ Session::get('succmsg') }}
		</p>
		</div>
		@endif
	
	<!-- Left Nav Starts Here -->
	@include('partials.leftnav')
	<!-- Left Nav Ends Here -->
	<!-- Page Center Content Starts Here -->
	<div class="col-lg-7 col-md-6 col-sm-12 col-xs-12 main-middle">

		<div class="block">
			<div class="tab-nav underline">
				@include('partials.page_top_navigation')
			</div>

			<div class="col-md-12 col-sm-12 col-xs-12 padding-top">

				<div class="gray_bg">
					{!! Form::open(array('url' => 'buyerposts/search', 'id'
					=>'buyer-post-search', 'class'=>'form-inline ' )) !!}

					<div class="col-md-3 col-sm-3 col-xs-12 padding-none">

						<select class="selectpicker"><option>Enquiry Type (All)</option>
							<option>Enquiry Type (Spot)</option>
							<option>Enquiry Type (Term)</option></select>

						<!-- {!!
						Form::select('lkp_enquiry_type_id',array('' => 'Enquiry Type (All)')+
						$enquiry_types,$enquiry_type,['class'=>'selectpicker','id'=>'enquiry_types'])
						!!}-->
					</div>
					<div class="col-md-3 col-sm-3 col-xs-12 padding-right-none mobile-padding-none">{!!
						Form::select('service_id',array('' => 'Service Type
						(All)')+$services,$service_id,['class'=>'selectpicker','id'=>'service_offered'])
						!!}</div>
					<div class="col-md-3 col-sm-3 col-xs-12 padding-right-none mobile-padding-none">{!!
						Form::select('status_id',array('' => 'Status (All)') +
						$status,$post_status,['class'=>'selectpicker','id'=>'post_status'])!!} 
                    </div>
                        <div class="col-md-3 col-sm-3 col-xs-12 padding-right-none mobile-padding-none">
                            <div class="form-group">
                                {!! Form::submit(' GO ', array( 'class'=>'btn 
						 ')) !!} {!! Form :: close() !!}</div>
                                </div>
					<div class="clearfix"></div>
				</div>
				
				{!! $filter->open !!}
					 {!! $filter->field('src') !!}
				<div class="gray_bg">

					<div class="col-md-3 col-sm-3 col-xs-12 padding-none">
								{!!	$filter->field('bqi.from_city_id') !!}
					</div>
					<div class="col-md-3 col-sm-3 col-xs-12 padding-right-none mobile-padding-none">
								{!!	$filter->field('bqi.to_city_id') !!}

					</div>
					<div class="clearfix"></div>

					<div class="col-md-3 col-sm-3 col-xs-12 padding-none">
								{!!	$filter->field('bqi.lkp_load_type_id') !!}
					</div>
					<div class="col-md-3 col-sm-3 col-xs-12 padding-right-none mobile-padding-none">
                        {!!	$filter->field('bqi.lkp_vehicle_type_id') !!}
                        
					</div>

					<div class="col-md-3 col-sm-3 col-xs-12 padding-right-none mobile-padding-none">
                        <div class="form-group">{!! $filter->field('bqi.start_dispatch_date') !!}
                        </div>
					</div>
					<div class="col-md-3 col-sm-3 col-xs-12 padding-right-none mobile-padding-none">
                        <div class="form-group">{!! $filter->field('bqi.end_dispatch_date') !!}
						</div>
					</div>

				</div>
				
				
				{!! $filter->close !!} 
					
				<div class="clearfix"></div>
			</div>
			{!! $grid !!}

		</div>
	</div>
	<!-- Page Center Content Ends Here -->
	<!-- Right Starts Here -->
	@include('partials.right')
	<!-- Right Ends Here -->
</div>
@endsection