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
		<span class="pull-left"><h1 class="page-title">Post & Get Quote (FTL)</h1><a href="#" class="change-service" data-toggle="modal" data-target="#change-service">Change Service</a></span>
		@if ($url_search_search == 'byersearchresults')
		<span class="pull-right"><a href="{{ $serverpreviUrL  }}" class="back-link">Back to Search</a></span>
		@endif

		<div class="clearfix"></div>	
		
		<div class="col-md-12 inner-block-bg inner-block-bg1 spl-radio-append">		
			
			<div class="col-md-12 form-control-fld margin-none">
				<div class="radio-block">
					<div class="radio_inline"><input type="radio" name="lead_type" id="spot_lead_type"  value="1" checked /> <label for="spot_lead_type"><span></span>Spot</label></div>
					<div class="radio_inline"><input type="radio" name="lead_type" id="term_lead_radio" value="2" /> <label for="term_lead_radio"><span></span>Term</label></div>
				</div>
			</div>
			
		</div>
		
	<div class="showhide_spot" id="showhide_spot"><!-- Add custom div for FTl spot srinu -->
		<div class="col-md-12 inner-block-bg single-layout padding-none">
		
			
			<div class="col-md-12 inner-block-bg inner-block-bg1 border-top-none border-bottom-none margin-bottom-none padding-bottom-none padding-top-none">
			
			
			
			{!! Form::open(['url' =>'#','id' => 'buyer_quotelineitems_form_validation' , 'autocomplete'=>'off']) !!}
				<div class="col-md-12 form-control-fld">
					<div class="radio-block">
						<div class="radio_inline"><input type="radio" name="is_check_commercial" id="is_checkcommercial"  value="1" checked  /> <label for="is_checkcommercial"><span></span>Commercial</label></div>
						<div class="radio_inline"><input type="radio" name="is_check_commercial" id="non_checkcommercial" value="0" /> <label for="non_checkcommercial"><span></span>Non Commercial</label></div>
					</div>
				</div>
				<div class="col-md-12 padding-none inner-form">					
					<div class="col-md-3 form-control-fld">
						<div class="input-prepend">
							<span class="add-on"><i class="fa fa-map-marker"></i></span>
							{!! Form::text('from_location', $session_search_values_create[6], ['id' => 'from_location', 'class'=>'form-control clsFTLFromLocation', 'placeholder' => 'From Location *']) !!}
							{!! Form::hidden('from_location_id', $session_search_values_create[4], array('id' => 'from_location_id')) !!}
						</div>
					</div>
					<div class="col-md-3 form-control-fld">
						<div class="input-prepend">
							<span class="add-on"><i class="fa fa-map-marker"></i></span>
							{!! Form::text('to_location', $session_search_values_create[7], ['id' => 'to_location', 'class'=>'form-control clsFTLToLocation','placeholder' => 'To Location *']) !!}
							{!! Form::hidden('to_location_id', $session_search_values_create[5], array('id' => 'to_location_id')) !!}								</div>
					</div>
					<div class="col-md-3 form-control-fld">
						<div class="input-prepend">
							<span class="add-on"><i class="fa fa-calendar-o"></i></span>
							{!! Form::text('dispatch_date', $session_search_values_create[1], ['id' => 'dispatch_date','class' => 'flexible_dispatch_date form-control calendar from-date-control', 'placeholder' => 'Dispatch Date *','readonly'=>"readonly"]) !!}
							<input type="hidden" name="is_dispatch_flexible_hidden" id="is_dispatch_flexible_hidden" value="{{ $session_search_values_create[12] }}">
						</div>
					</div>
					<div class="col-md-3 form-control-fld">
						<div class="input-prepend">
							<span class="add-on"><i class="fa fa-calendar-o"></i></span>
							{!! Form::text('delivery_date', $session_search_values_create[0], ['id' => 'delivery_date','class' => 'flexible_delivery_date form-control calendar to-date-control', 'placeholder' => 'Delivery Date','readonly'=>"readonly"]) !!}
							<input type="hidden" name="is_delivery_flexible_hidden" id="is_delivery_flexible_hidden" value="{{ $session_search_values_create[13] }}">
						</div>
					</div>
					<div class="clearfix"></div>
					<div class="col-md-3 form-control-fld">
						<div class="input-prepend">
							<span class="add-on"><i class="fa fa-archive"></i></span>
							{!!	Form::select('load_type',(['' => 'Select Load Type *'] +$load_type), $session_search_values_create[3],['class' =>'selectpicker form_control','id'=>'load_type','onChange'=>'return GetCapacity()']) !!}
						</div>
					</div>
					<div class="col-md-3 form-control-fld">
						<div class="input-prepend">
							{!!	Form::text('c',$session_search_values_create[8],array('class'=>'form-control form-control1 clsFTLSQuantity','placeholder'=>'Quantity *','id'=>'quantity')) !!}
							<span class="add-on unit1">
							{!!	Form::text('capacity',$session_search_values_create[9],array('class'=>'form-control form-control1','id'=>'capacity','placeholder'=>'Capacity','readonly')) !!}							
							</span>
						</div>
					</div>
					
					<div class="col-md-3 form-control-fld">
						<div class="input-prepend">
							<span class="add-on"><i class="fa fa-truck"></i></span>
							{!!	Form::select('vehicle_type',(['' => 'Select Vehicle Type *'] +$vehicle_type), $session_search_values_create[2],['class' =>'selectpicker form_control','id'=>'vehicle_type','onChange'=>'return CheckLoads(this.value)']) !!}
						</div>
					</div>
					
					<div class="col-md-3 form-control-fld">
						<div>
							<img src="{{asset('/images/truck.png')}}" />
							&nbsp;&nbsp;
                                                        <span id="dimensions">
                                                            @if(isset($session_search_values_create[11]))
                                                            {{$session_search_values_create[11]}}
                                                            @else 20x8x12 
                                                            @endif
                                                        </span>
						</div>
						<div class="clearfix"></div>
					</div>
					<div class="clearfix"></div>
					<div class="col-md-3 form-control-fld">
						<div class="input-prepend">
							{!!	Form::text('no_of_loads',$session_search_values_create[10],array('class'=>'form-control form-control1 clsFTSLoads','placeholder'=>'Loads','id'=>'no_of_loads', 'maxlength'=>4)) !!}
						</div>
					</div>
					<div class="col-md-3 form-control-fld">
						<div class="input-prepend">
							<span class="add-on"><i class="fa fa-archive"></i></span>
							{!! Form::select('quote_type',(['' => 'Select Price Type *'] +$quote_price_type), null,['class' =>'selectpicker  form_control','id'=>'quote_id','onChange'=>'return	HidePrice(this.value)']) !!}
						</div>
					</div>
					<div class="col-md-2 form-control-fld" id="hide_price">
						<div class="input-prepend" >
							<span class="add-on"><i class="fa fa-rupee "></i></span>
								{!!	Form::text('price',null,array('class'=>'form-control clsFTLSPrice','placeholder'=>'Price *','id'=>'price','autocomplete'=>'off')) !!}
						</div>
					</div>
					<div class="col-md-3 form-control-fld">
							<input type="submit" value="Add +" class="btn add-btn" id="add_buyer_more">
							<div id="error-add-item" class="error "></div>
					</div>

				</div>
				
				{!! Form::close() !!}
			</div>
			

			{!! Form::open(['url' =>'createbuyerquote','id' => 'buyer_quote_form']) !!}
			{!!	Form::hidden('service_id',1,array('class'=>'','id'=>'service_id'))!!}
                        {!!	Form::hidden('service_id_ftl',1,array('class'=>'','id'=>'service_id_ftl'))!!}
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

						<div class="table-div padding-none">

							<!-- Table Head Starts Here -->
							<div class="table-heading inner-block-bg">
								<div class="col-md-2 padding-left-none">From<i class="fa  fa-caret-down"></i></div>
								<div class="col-md-2 padding-left-none">To<i class="fa  fa-caret-down"></i></div>
								<div class="col-md-3 padding-left-none">Load Type<i class="fa  fa-caret-down"></i></div>
								<div class="col-md-2 padding-left-none">Vehicle Type<i class="fa  fa-caret-down"></i></div>
								<div class="col-md-2 padding-left-none">Price<i class="fa  fa-caret-down"></i></div>
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
				<div class="col-md-12 form-control-fld">
					<div class="radio-block">
						<div class="radio_inline"><input type="radio" name="quoteaccess_id" value="1" id="post-public" checked="checked" class="create-posttype" /><label for ="post-public"> <span></span>Post Public</label></div>
						<div class="radio_inline"><input type="radio" name="quoteaccess_id" value="2" id="post-private" class="create-posttype-service-ftl create-posttype"/><label for ="post-private"><span></span>Post Private</label></div>

					</div>
				</div>
				
				<div class="col-md-3 form-control-fld" id="hideseller" style="display:none;">
					<input type="text" class="form-control form-control1"  id="demo-input-local" name="seller_list" placeholder="Seller Name (Auto Search)"/>
				</div>
				<div class="clearfix"></div>
				<div class="check-box form-control-fld">
					<div class="normal-checkbox">
						{!! Form::checkbox('agree', '', '', ['class' => 'field','id'=>'agree'])!!}<span class="lbl padding-8">Accept Terms &amp; Conditions ( Digital Contract)</span>
					</div>
				</div>
			</div>
		</div>

		<div class="clearfix"></div>

		<div class="container">
			<div class="col-md-4 col-md-offset-4">
				{!! Form::submit('Get Quote', ['name' => 'confirm','class'=>'btn theme-btn btn-block','id' => 'add_buyer_quote']) !!}
			</div>
		</div>
		
		{!! Form::close() !!}
	
	</div> <!-- End custom div for FTl spot srinu -->
	
<!-- ------------Starts FTL Term starts Here---------- -->
	
<div class="showhide_term" id="showhide_term" style="display:none"> <!-- Add custom div for FTl term srinu -->

	<div class="col-md-12 inner-block-bg single-layout padding-none">
	
				<div class="col-md-12 inner-block-bg inner-block-bg1 border-top-none border-bottom-none margin-bottom-none padding-bottom-none padding-top-none">
		
					<div class="col-md-12 padding-none inner-form">	     
					{!! Form::open(['url' =>'#','id' => 'ftl_term_insert' , 'autocomplete'=>'off']) !!}
					{!!	Form::hidden('update_term_line',0,array('class'=>'','id'=>'update_term_line'))!!}
					{!!	Form::hidden('update_term_row_count','',array('class'=>'','id'=>'update_term_row_count'))!!}               
					{!!	Form::hidden('update_term_row_unique','',array('class'=>'','id'=>'update_term_row_unique'))!!}
	                    	<div class="col-md-3 form-control-fld">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-calendar-o"></i></span>									
									{!! Form::text('term_dispatch_date', '', ['id' => 'term_dispatch_date','class' => 'form-control calendar from-date-control', 'placeholder' => 'Valid From *','readonly'=>"readonly"]) !!}
								</div>
							</div>
							<div class="col-md-3 form-control-fld">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-calendar-o"></i></span>
									{!! Form::text('term_delivery_date', '', ['id' => 'term_delivery_date','class' => 'form-control calendar to-date-control', 'placeholder' => 'Valid To *','readonly'=>"readonly"]) !!}
								</div>
							</div>
	                        
						<div class="col-md-3 form-control-fld">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-map-marker"></i></span>
									{!! Form::text('term_from_location', '', ['id' => 'term_from_location', 'class'=>'form-control clsFTLTFromLocation', 'placeholder' => 'From Location *']) !!}
								   	{!! Form::hidden('term_from_location_id', '', array('id' => 'term_from_location_id')) !!}
								</div>
							</div>
							<div class="col-md-3 form-control-fld">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-map-marker"></i></span>
									{!! Form::text('term_to_location', '', ['id' => 'term_to_location', 'class'=>'form-control clsFTLTtoLocation','placeholder' => 'To Location *']) !!}
									{!! Form::hidden('term_to_location_id', '', array('id' => 'term_to_location_id')) !!}
								</div>
							</div>
	                        <div class="clearfix"></div>
	                        
	                        <div class="col-md-3 form-control-fld">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-archive"></i></span>
									{!!	Form::select('term_load_type',(['' => 'Select Load Type *'] +$load_type), '',['class' =>'selectpicker form_control','id'=>'term_load_type','onChange'=>'return getTermCapacity()']) !!}
								</div>
							</div>
	                        <div class="col-md-3 form-control-fld">
									<div class="input-prepend">
									<span class="add-on"><i class="fa fa-balance-scale"></i></span>
									{!!	Form::text('term_quantity','',array('class'=>'form-control clsFTLTQuantity','placeholder'=>'Quantity *','id'=>'term_quantity')) !!}
									<span class="add-on unit1">
										{!!	Form::text('term_capacity','',array('class'=>'form-control','id'=>'term_capacity','placeholder'=>'Capacity','readonly')) !!}
									</span>
									</div>
							</div>
							
							<div class="col-md-3 form-control-fld">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-truck"></i></span>
									{!!	Form::select('term_vehicle_type',(['' => 'Select Vehicle Type *'] +$vehicle_type), '' ,['class' =>'selectpicker form_control','id'=>'term_vehicle_type']) !!}
								</div>
							</div> 

							<div class="col-md-3 form-control-fld">								  	
								  	<input type="submit" value="Add +" class="btn add-btn" id="term_add_buyer_more">
									<div id="error-add-item" class="error "></div>
							</div>
					</div>
					
					</div>
					{!! Form::close() !!}

					{!! Form::open(['url' =>'createbuyerquote','id' => 'term_buyer_quote', 'files'=>true, 'autocomplete'=>'off']) !!}	
					{!!	Form::hidden('lead_type',2,array('class'=>'','id'=>'lead_type'))!!}
					
					<div class="col-md-12 inner-block-bg inner-block-bg1 border-top-none padding-top-none">
					<div class="col-md-12 padding-none">
						<div class="main-inner"> 							

							<!-- Right Section Starts Here -->

							<div class="main-right">

								<!-- Table Starts Here -->

								<div class="table-div table-style1 padding-none">
									
									<!-- Table Head Starts Here -->

									<div class="table-heading inner-block-bg">
										<div class="col-md-2 padding-left-none">From</div>
										<div class="col-md-2 padding-left-none">To</div>
										<div class="col-md-3 padding-left-none">Load Type</div>
										<div class="col-md-2 padding-left-none">Vehicle Type</div>
										<div class="col-md-2 padding-left-none">Quantity</div>
										<div class="col-md-1 padding-left-none"></div>
									</div>

									<!-- Table Head Ends Here -->

									<div class="table-data term_request_rows">

										<!-- Table Row Starts Here -->

										<input type="hidden" id='next_term_add_buyer_more_id' value='0'>

										<div class="clearfix"></div>

										<!-- Table Row Ends Here -->

									</div>
								</div>	

								<!-- Table Starts Here -->

							</div>

							<!-- Right Section Ends Here -->

						</div>
					</div>
					</div>
					<!-- bid type section starts-->
					
					<div class="col-md-12 inner-block-bg inner-block-bg1">
					<div class="col-md-12 padding-left-none padding-right-none pad-top-20">
					
							<div class="col-md-3 form-control-fld">
								<label class="col-md-4 pull-left padding-none" style="margin-top:9px;">Bid Type * :</label>
								<div class="normal-select col-md-8 padding-none">
									{!!	Form::select('bid_type',($bid_type), '' ,['class' =>'selectpicker form_control','id'=>'bid_type']) !!}
								</div>
						 	</div>							
							<div class="col-md-3 form-control-fld">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-calendar-o"></i></span>
									{!! Form::text('last_bid_date', '', ['id' => 'last_bid_date','class' => 'form-control clsFTLTBidCloserDate', 'placeholder' => 'Bid Closure Date *','readonly'=>"readonly"]) !!}		
								</div>
						   </div>							
						    <div class="col-md-3 form-control-fld">
								<div id="bid_time_icon_add" class="input-prepend date clsbid_close_time">
									<span class="add-on"><i class="fa fa-clock-o"></i></span>
									{!! Form::text('bid_close_time', '', ['id' => 'bid_close_time','class' => 'form-control form-control1 disable-bg-white clsFTLTBidCloserTime', 'placeholder' => 'Bid Closure Time *', 'readonly'=>"readonly"]) !!}
								</div>
								<label for="bid_close_time" id="err_bid_close_time" class="error"></label>
							</div>
							<div class="clearfix"></div>
							<div class="form-control-fld"><span>Bid Terms & Conditions</span></div>							
					
					</div>
				
					
					<!-- 	bid type section ends-->
					
					<!--file upload div starts-->
					<input type="hidden" name ='term_next_terms_count_search' id='term_next_terms_count_search' value='0'>
					<div class="documents-terms">
					<div class="col-md-12 padding-none text-box">
					<span style='display:none;' class="box-number-delete">2</span>
						<div class="col-md-4 form-control-fld">
							 	              
<!-- 	                			<input type="text" class="form-control form-control1 upload-control" readonly>  -->
										<div class="upload-fld">
											<button class="btn add-btn upload-browse-btn pull-right">Browse...</button>
											<input type="file" name="terms_condtion_types_term_defualt" class="form-control form-control1 update_txt" value="" id="terms_condtion_types_term_defualt" />
							            </div>
									</div>
									<div class="col-md-3 form-control-fld">	
								           		 									
						
								<input type="button" class="documents-add btn add-btn" value="Add +">
							</div>	
							<div class="clearfix"></div>													
					</div>
					</div>
					
					<div class="col-md-9 form-control-fld">								
							<textarea  class="form-control form-control1 clsFTLComments" name="buyer_notes" id="buyer_notes" placeholder="Comments"></textarea>	
					</div>	
					
					</div>	
					
						<!--file upload div ends-->
						
					
					<div class="col-md-12 inner-block-bg inner-block-bg1">
						<div class="col-md-12 form-control-fld move-bottom-10 padding-left-none">
							<div class="radio-block">

								<div class="radio_inline"><input type="radio" name="quoteaccess_id" value="1" id="term_post_public" checked> <label for="term_post_public"><span></span>Post Public</label></div>
								<div class="radio_inline"><input type="radio" name="quoteaccess_id" value="2" id="term_post_private" class="create-posttype-service-ftl-term"> <label for ='term_post_private' class="create-posttype-service-ftl lbl padding-8"><span></span>Post Private</label></div>

							</div>
						</div>
						
						<div class="clearfix"></div>					
						
						<div class="col-md-3 form-control-fld padding-none" id="showhidepost" style="display:none;">					
							<input type="text" class="form-control form-control1" id="term_seller_list" name="term_seller_list" placeholder="Seller Name (Auto Search)"/>
						</div>
						<div class="clearfix"></div>
						<div class="check-box form-control-fld">
							<div class="normal-checkbox">
							  {!! Form::checkbox('agree','', '', ['class' => 'field','id'=>'agree']) !!} <span class="lbl padding-8">Accept Terms & Conditions (Digital Contract)</span></div>
							</div>
						</div>
						
				</div>

		<div class="clearfix"></div>


			<div class="col-md-12 padding-none">
<!-- 				{!! Form::submit('Save as Draft', ['name' => 'draft','class'=>'btn black-btn margin-top','id' => 'term_ftl_draft']) !!}	 -->
				
				<input type="hidden" name="confirm_but" id="confirm_but" value="">
				{!! Form::submit('Float RFP', ['name' => 'confirm','class'=>'btn theme-btn flat-btn pull-right term_add_buyer_quote','id' => 'term_add_buyer_quote']) !!}
				{!! Form::submit('Save As Draft', ['name' => 'draft','class'=>'btn add-btn flat-btn pull-right term_add_buyer_quote','id' => 'term_add_buyer_quote_draft']) !!}

				
			</div>

	</div>	<!-- End custom div for FTl term srinu -->	
<!-- End Term get quote form -->

	</div> <!-- End container div here -->
	
</div> <!-- End Main braces -->

{!! Form::close() !!}

@include('partials.footer')
@endsection