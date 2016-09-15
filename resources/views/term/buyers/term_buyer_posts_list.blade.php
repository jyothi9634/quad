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

					<div class="col-md-3 col-sm-3 col-xs-12 padding-left-none mobile-padding-none mobile-margin-none">

						<select class="selectpicker" onchange="javascript:changePostType(this.value)">
						    <option value="term">Post Type (Term)</option>
							<option value="spot">Post Type (Spot)</option>
						</select>	
                    
                    <div class="col-md-3 col-sm-3 col-xs-12 padding-right-none mobile-padding-none">{!!
						Form::select('status_id',array('' => 'Status (All)') +
						$status,$post_status,['class'=>'selectpicker','id'=>'post_status'])!!} 
                    </div>
                        <div class="col-md-3 col-sm-3 col-xs-12 padding-right-none mobile-padding-none">
                                {!! Form::submit(' GO ', array( 'class'=>'btn 
						 ')) !!} {!! Form :: close() !!}</div>
					<div class="clearfix"></div>
				</div>
				
				{!! $filter->open !!}
					 {!! $filter->field('src') !!}
				<div class="gray_bg">
					<div class="col-md-12 col-sm-12 col-xs-12 padding-none margin-bottom mobile-margin-none">
					<div class="col-md-3 col-sm-3 col-xs-12 padding-none margin-bottom mobile-margin-none">
								{!!	$filter->field('bqi.from_city_id') !!}
					</div>
					<div class="col-md-3 col-sm-3 col-xs-12 padding-right-none mobile-padding-none">
								{!!	$filter->field('bqi.to_city_id') !!}

					</div>
					
					<div class="col-md-3 col-sm-3 col-xs-12 padding-right-none mobile-padding-none">
                        <div class="form-group">
<!--                         	{!! $filter->field('bqi.dispatch_date') !!}                         -->
                         	@if(isset($_GET['dispatch_date']))
                            {!! Form::text('dispatch_date', $_GET['dispatch_date'],['id' => 'dispatch_date','class'=>'form-control dateRange', 'placeholder' => 'From']) !!}
                            @else
                            {!! Form::text('dispatch_date', '',['id' => 'dispatch_date','class'=>'form-control dateRange', 'placeholder' => 'From']) !!}
                            @endif
                        
                        </div>
					
					</div>
					<div class="col-md-3 col-sm-3 col-xs-12 padding-right-none mobile-padding-none">
                        <div class="form-group">
                       		 @if(isset($_GET['delivery_date']))
                            {!! Form::text('delivery_date', $_GET['delivery_date'],['id' => 'delivery_date','class'=>'form-control dateRange', 'placeholder' => 'To']) !!}
                            @else
                            {!! Form::text('delivery_date', '',['id' => 'delivery_date','class'=>'form-control dateRange', 'placeholder' => 'From']) !!}
                            @endif
						</div>
					</div>

				</div>
				</div>
				
				{!! $filter->close !!} 
					
				<div class="clearfix"></div>
				
				<div class="table-top col-md-12 col-sm-12 col-xs-12 padding-none"><input type="checkbox"  id="globalbuyerpostlistcheck" name="globalbuyerpostlistcheck">
					<a href="javascript:void(0)">Select All</a> <a href="javascript:void(0)" onclick="javascript:buyerpostcancel('items')">Delete</a>
<!-- 					<span class="pull-right"> -->
<!-- 						View 1-50 -->
<!-- 					</span> -->
				</div>
				
				
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
