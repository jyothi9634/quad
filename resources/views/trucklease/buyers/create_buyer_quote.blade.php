@extends('app')
@section('content')
@inject('common', 'App\Components\CommonComponent')

<!-- Page top navigation Starts Here-->
@include('partials.page_top_navigation')
<!-- Page top navigation ends Here-->

@if(Session::has('transactionId') && Session::get('transactionId')!='')

	{{--*/ $transactionId = Session::get('transactionId') /*--}}
	{{--*/ Session::get('postsCount') /*--}}
	{{--*/ Session::get('postType') /*--}}
	
<script>
			$(document).ready(function(){				
				var postCount = {{ Session::get('postsCount') }}				
				if (postCount==1) {
				   $("#erroralertmodal .modal-body").html("Your request has been posted to the relevant sellers. Your transacton id is <?php echo $transactionId;?>. You would be getting the quotes from the sellers online.");				
		     	   $("#erroralertmodal").modal({
		               show: true
		           }).one('click','.ok-btn',function (e){
		        	   window.location="/buyerposts";	        	 
		           });				
				} else {
					var postType = {{ Session::get('postType') }}
						if (postType == 2) {							
							$("#erroralertmodal .modal-body").html("Term Quote submitted successfully.");						   
						} else {
							$("#erroralertmodal .modal-body").html(postCount + " Quotes submitted successfully .");
						}				
			     	   $("#erroralertmodal").modal({
			               show: true
			           }).one('click','.ok-btn',function (e){
			        	   window.location="/buyerposts";	        	 
			           });	
					}	
				 });
</script>
				
		
@endif
<div class="main">

	<div class="container">
		<span class="pull-left"><h1 class="page-title">Post & Get Quote (Truck Lease)</h1><a href="#" class="change-service" data-toggle="modal" data-target="#change-service">Change Service</a></span>
		@if ($url_search_search == 'byersearchresults')
		<span class="pull-right"><a href="{{ $serverpreviUrL  }}" class="back-link">Back to Search</a></span>
		@endif

		<div class="clearfix"></div>	
		
		<div class="col-md-12 inner-block-bg inner-block-bg1 spl-radio-append">		
			<div class="col-md-12 form-control-fld margin-none">
				
			</div>
		</div>
		
	<div class="showhide_spot" id="showhide_spot"><!-- Add custom div for FTl spot srinu -->
		<div class="col-md-12 inner-block-bg single-layout padding-none">
		
			
			<div class="col-md-12 inner-block-bg inner-block-bg1 border-top-none border-bottom-none margin-bottom-none padding-bottom-none padding-top-none">
			
			
			
			{!! Form::open(['url' =>'#','id' => 'buyer_quote_items_truck_lease' , 'autocomplete'=>'off']) !!}
				<div class="col-md-12 form-control-fld margin-none">
					<div class="radio-block">
						<div class="radio_inline"><input type="radio" name="is_check_commercial" id="is_checkcommercial"  value="1" checked  /> <label for="is_checkcommercial"><span></span>Commercial</label></div>
						<div class="radio_inline"><input type="radio" name="is_check_commercial" id="non_checkcommercial" value="0" /> <label for="non_checkcommercial"><span></span>Non Commercial</label></div>
					</div>
				</div>
				<div class="col-md-12 padding-none inner-form">					
					<div class="col-md-3 form-control-fld">
						<div class="input-prepend">
							<span class="add-on"><i class="fa fa-map-marker"></i></span>
							{!! Form::text('from_location', $session_search_values_create[5], ['id' => 'from_location', 'class'=>'form-control clsLocation', 'placeholder' => 'Location *']) !!}
							{!! Form::hidden('from_location_id', $session_search_values_create[4], array('id' => 'from_location_id')) !!}
						</div>
					</div>
					<div class="col-md-3 form-control-fld">
						<div class="input-prepend">
							<span class="add-on"><i class="fa fa-calendar-o"></i></span>
							{!! Form::text('dispatch_date', $session_search_values_create[1], ['id' => 'datepicker','class' => 'flexible_dispatch_date form-control clsFromDate from-date-control', 'placeholder' => 'From Date *','readonly'=>"readonly"]) !!}
						</div>
					</div>
					<div class="col-md-3 form-control-fld">
						<div class="input-prepend">
							<span class="add-on"><i class="fa fa-calendar-o"></i></span>
							{!! Form::text('delivery_date', $session_search_values_create[0], ['id' => 'datepicker_to_location','class' => 'flexible_delivery_date form-control clsToDate to-date-control', 'placeholder' => 'To Date','readonly'=>"readonly"]) !!}
						</div>
					</div>
					<div class="col-lg-3 col-md-4 form-control-fld">
						<div>
							{!! Form::checkbox('need_diver', 1, $session_search_values_create[6],array('id'=>'need_diver')) !!}
							<span class="lbl padding-8 padding-top-3"><b>Need Driver</b></span>
						</div>
					</div>
					<div class="clearfix"></div>
					<div class="col-md-3 form-control-fld">
						<div class="input-prepend">
							<span class="add-on"><i class="fa fa-calendar-o"></i></span>
							{!!	Form::select('lease_terms',(['' => 'Lease Term*'] +$getAllTruckLeaseTerms), $session_search_values_create[3],['class' =>'selectpicker','id'=>'lease_type', 'onchange' => "changeRateClass(this.value)"]) !!}
						</div>
					</div>
					
					<div class="col-md-2 form-control-fld">
						<div class="input-prepend">
							<span class="add-on"><i class="fa fa-truck"></i></span>
							{!! Form::select('driver',['' => 'Fuel','1' => 'Included','0' => 'Not Included'], null, ['id' => 'driver','class' => 'selectpicker form_contro']) !!}
						</div>
					</div>
					
					
					<div class="col-md-3 form-control-fld">
						<div class="input-prepend">
							<span class="add-on"><i class="fa fa-truck"></i></span>
							{!!	Form::select('vehicle_type',(['' => 'Select Vehicle Type *'] +$vehicle_type), $session_search_values_create[2],['class' =>'selectpicker form_control','id'=>'vehicle_type']) !!}
						</div>
					</div>
					
					
					
					<div class="col-md-4 form-control-fld">
						<div class="input-prepend">
							<span class="add-on"><i class="fa fa-truck"></i></span>
							{!!	Form::text('year_make_model',null,array('class'=>'form-control clsVehicleModel','placeholder'=>'Vehicle Make & Model & Year*','id'=>'year_make_model')) !!}
						</div>
					</div>
					<div class="clearfix"></div>
					
					<div class="col-md-3 form-control-fld">
						<div class="input-prepend">
							<span class="add-on"><i class="fa fa-archive"></i></span>
							{!! Form::select('quote_type',(['' => 'Price Type *'] +$quote_price_type), null,['class' =>'selectpicker  form_control','id'=>'quote_id','onChange'=>'return	HidePrice(this.value)']) !!}
						</div>
					</div>
					<div class="col-md-2 form-control-fld" id="hide_price">
						<div class="input-prepend" >
							<span class="add-on"><i class="fa fa-rupee "></i></span>
								{!!	Form::text('price',null,array('class'=>'form-control clsTLeasePrice','placeholder'=>'Price *','id'=>'price','autocomplete'=>'off')) !!}
						</div>
					</div>
					
					
					<div class="col-md-2 form-control-fld">
						<div>
							<img src="{{asset('/images/truck.png')}}" />
                                              <span id="dimensions">
                                                    @if(isset($session_search_values_create[11]))
                                                    	{{$session_search_values_create[11]}}
                                                    @else 
                                                    	20x8x12 
                                                    @endif
                                             </span>
						</div>
						<div class="clearfix"></div>
					</div>
					
					
					<div class="col-md-1 form-control-fld text-right pull-right">
							<input type="submit" value="Add +" class="btn add-btn" id="lease_add_buyer_more">
							<div id="error-add-item" class="error "></div>
					</div>
					
					

				</div>
				
				{!! Form::close() !!}
			</div>
			

			{!! Form::open(['url' =>'trucklease/createbuyerquote','id' => 'buyer_quote_truck_lease']) !!}
			{!!	Form::hidden('service_id',1,array('class'=>'','id'=>'service_id'))!!}
			{!!	Form::hidden('lead_type',1,array('class'=>'','id'=>'lead_type'))!!}
			{!!	Form::hidden('update_line',0,array('class'=>'','id'=>'update_line'))!!}
			{!!	Form::hidden('update_row_count','',array('class'=>'','id'=>'update_row_count'))!!}
			{!!	Form::hidden('is_commercial',1,array('class'=>'','id'=>'is_commercial'))!!}

			<div class="col-md-12 inner-block-bg inner-block-bg1 border-top-none padding-top-none">
			<div class="col-md-12 padding-none">
				<div class="main-inner">

					<!-- Right Section Starts Here -->

					<div class="main-right">

						<!-- Table Starts Here -->

						<div class="table-div margin-none">

							<!-- Table Head Starts Here -->
							<div class="table-heading inner-block-bg">
								<div class="col-md-2 padding-left-none">Location<i class="fa  fa-caret-down"></i></div>
											<div class="col-md-2 padding-left-none">Vehicle Type<i class="fa  fa-caret-down"></i></div>
											<div class="col-md-2 padding-left-none">Lease Term<i class="fa  fa-caret-down"></i></div>
											<div class="col-md-2 padding-left-none">Driver<i class="fa  fa-caret-down"></i></div>
											<div class="col-md-2 padding-left-none">Price Type<i class="fa  fa-caret-down"></i></div>
											<div class="col-md-1 padding-left-none">Price<i class="fa  fa-caret-down"></i></div>
											<div class="col-md-1 padding-left-none"></div>
							</div>
							<!-- Table Head Ends Here -->
                                                        <input type="hidden" id='next_add_buyer_more_id' value='0'>
							<div class="table-data request_rows">
								
								<!-- Table Row Starts Here -->
								<!-- Table Row Ends Here -->
							</div>
						</div>

						<!-- Table Starts Here -->

					</div>

					<!-- Right Section Ends Here -->

				</div>
			</div>
			</div>

			<div class="col-md-12 inner-block-bg inner-block-bg1">
				<div class="col-md-12 form-control-fld margin-none">
					<div class="radio-block">
						<div class="radio_inline"><input type="radio" name="quoteaccess_id" value="1" id="post-public" checked="checked" class="lease-create-posttype" /><label for ="post-public"> <span></span>Post Public</label></div>
						<div class="radio_inline"><input type="radio" name="quoteaccess_id" value="2" id="post-private" class="create-posttype-service-ftl lease-create-posttype"/><label for ="post-private"><span></span>Post Private</label></div>

					</div>
				</div>
				<div class="col-md-3 form-control-fld" id="hideseller" style="display:none;">
					<input type="text" class="form-control form-control1"  id="demo-input-local" name="seller_list" placeholder="Seller Name (Auto Search)"/>
				</div>
				<div class="clearfix"></div>
				<div class="check-box form-control-fld margin-none">
					<div class="normal-checkbox">
						{!! Form::checkbox('agree', '', '', ['class' => 'field','id'=>'agree'])!!}<span class="lbl padding-8">Accept Terms &amp; Conditions ( Digital Contract)</span>
					</div>
				</div>
			</div>
		</div>

		<div class="clearfix"></div>

		<div class="container">
			<div class="col-md-4 col-md-offset-4">
				{!! Form::submit('Get Quote', ['name' => 'confirm','class'=>'btn theme-btn btn-block','id' => 'add_buyer_quote_lease']) !!}
			</div>
		</div>
		
		{!! Form::close() !!}
	
	</div> <!-- End custom div for FTl spot srinu -->
	

	</div> <!-- End container div here -->
	
</div> <!-- End Main braces -->

{!! Form::close() !!}

@include('partials.footer')
@endsection