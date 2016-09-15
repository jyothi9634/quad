@extends('app')
@section('content')
<!-- Page top navigation Starts Here-->
 
 {{--*/ $searchrequest=array(); /*--}}
  {{--*/ $from_loaction=''; /*--}}
  {{--*/ $to_loaction=''; /*--}}
  {{--*/ $from_loaction_id=''; /*--}}
  {{--*/ $to_loaction_id=''; /*--}}
  {{--*/ $from_date=''; /*--}}
  {{--*/ $to_date=''; /*--}}
  {{--*/ $property_type=''; /*--}}
  {{--*/ $volume=''; /*--}}
  {{--*/ $load_type=''; /*--}}
  {{--*/ $post_rate_card_type=''; /*--}}
  {{--*/ $vehicle_cat=''; /*--}}
  {{--*/ $vehicle_category_type=''; /*--}}
  {{--*/ $vehicle_model=''; /*--}}
  {{--*/ $elevator1yes = "" /*--}}
  {{--*/ $elevator1no = "" /*--}}
  {{--*/ $elevator2yes = "" /*--}}
  {{--*/ $elevator2no = "" /*--}}
 @if(Session::has('relocbuyerrequest'))
 
  {{--*/ $searchrequest=Session::get('relocbuyerrequest'); /*--}}
  {{--*/ $from_loaction=$searchrequest['from_location']; /*--}}
  {{--*/ $to_loaction=$searchrequest['to_location']; /*--}}
  {{--*/ $from_loaction_id=$searchrequest['from_location_id']; /*--}}
  {{--*/ $to_loaction_id=$searchrequest['to_location_id']; /*--}}
  {{--*/ $from_date=$searchrequest['from_date']; /*--}}
  {{--*/ $to_date=$searchrequest['to_date']; /*--}}
  {{--*/ $property_type=$searchrequest['property_type']; /*--}}
  {{--*/ $volume=$searchrequest['volume']; /*--}}
  {{--*/ $load_type=$searchrequest['load_type']; /*--}}
  {{--*/ $post_rate_card_type=$searchrequest['post_rate_card_type']; /*--}}
  @if(isset($searchrequest['vehicle_category']))
  {{--*/ $vehicle_cat=$searchrequest['vehicle_category']; /*--}}
  @endif
  @if(isset($searchrequest['vehicle_category_type']))
  {{--*/ $vehicle_category_type=$searchrequest['vehicle_category_type']; /*--}}
  @endif
  @if(isset($searchrequest['vehicle_model']))
  {{--*/ $vehicle_model=$searchrequest['vehicle_model']; /*--}}
  @endif
  
	 {{--*/ $elevator1yes = "" /*--}}
	 {{--*/ $elevator1no = "" /*--}}
	 {{--*/ $elevator2yes = "" /*--}}
	 {{--*/ $elevator2no = "" /*--}}
	 @if(isset($searchrequest['elevator1']) && $searchrequest['elevator1']==1)
	 {{--*/ $elevator1yes = "checked" /*--}}
	 @else
	 {{--*/ $elevator1no = "checked" /*--}}
	 @endif
	 @if(isset($searchrequest['elevator1']) && $searchrequest['elevator2']==1)
	 {{--*/ $elevator2yes = "checked" /*--}}
	 @else
	 {{--*/ $elevator2no = "checked" /*--}}
	 @endif
 @endif
 
 
 
 
@include('partials.page_top_navigation')


@if(Session::has('relocationtransactionNumber') && Session::get('relocationtransactionNumber')!='')

	{{--*/ $transactionId = Session::get('relocationtransactionNumber') /*--}}        
	{{--*/ Session::get('postType') /*--}}
        
			<script>
			$(document).ready(function(){
                            var postType = {{ Session::get('postType') }}				
                            if (postType==1) {
                                $("#erroralertmodal .modal-body").html("Your request has been posted to the relevant sellers. Your transacton id is <?php echo $transactionId;?>. You would be getting the quotes from the sellers online.");
                                $("#erroralertmodal").modal({
                                    show: true
                                }).one('click','.ok-btn',function (e){
                                        window.location="/buyerposts";
                                });
                            } else {
                                $("#erroralertmodal .modal-body").html("Your request has been posted to the relevant sellers. Your transacton id is <?php echo $transactionId;?>. You would be getting the quotes from the sellers online.");						                                   			
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
            <span class="pull-left"><h1 class="page-title">Post (Relocation)</h1><a class="change-service" data-toggle="modal" data-target="#change-service">Change Service</a></span>
            @if ($url_search_search == 'byersearchresults')
			<span class="pull-right"><a href="{{ $serverpreviUrL  }}" class="back-link">Back to Search</a></span>
		   	@endif

        <div class="clearfix"></div>
		 
			<div class="col-md-12 inner-block-bg inner-block-bg1 margin-bottom-none border-bottom-none padding-bottom-none">
				<div class="col-md-12 form-control-fld margin-none ">				
					<div class="col-md-12 padding-none radio-block radio-devider margin-top">
						<div class="radio_inline"><input type="radio" name="lead_type" id="relocation_spot"  value="1" checked /> <label for="relocation_spot"><span></span>Spot</label></div>
						<div class="radio_inline"><input type="radio" name="lead_type" id="relocation_term" value="2" /> <label for="relocation_term"><span></span>Term</label></div>
					</div>
				</div>
			</div>
            
		   	<div class="relocation_spot_show">
		      
		     	 {!! Form::open(['url' => 'relocationbuyerpostcreation','id'=>'posts-form_buyer_relocation', 'autocomplete'=>'off']) !!}
				 @if($post_rate_card_type=="" || $post_rate_card_type==1)
				 {!! Form::hidden('household_items', '1', array('id' => 'household_items')) !!}
				 @endif
				 @if($post_rate_card_type==2)
				 {!! Form::hidden('household_items', '2', array('id' => 'household_items')) !!}
				 @endif
		            <div class="col-md-12 inner-block-bg1 inner-block-bg-white border-top-none padding-top-none">
							<!-- <div class="col-md-12 form-control-fld margin-none">
								<div class="radio-block">
									<div class="radio_inline"><input type="radio" name="is_check_commercial" id="is_checkcommercial"  value="1" checked  /> <label for="is_checkcommercial"><span></span>Commercial</label></div>
									<div class="radio_inline"><input type="radio" name="is_check_commercial" id="non_checkcommercial" value="0" /> <label for="non_checkcommercial"><span></span>Non Commercial</label></div>
								</div>
							</div> -->
							
							<!-- {!!	Form::hidden('is_commercial',1,array('class'=>'','id'=>'is_commercial'))!!} -->
							<div class="col-md-12 form-control-fld">
									<div class="radio-block">
									{{--*/ $hhg_selected='checked'; /*--}}
									{{--*/ $vehicle_selected=''; /*--}}
									@if($post_rate_card_type!="")
										@if($post_rate_card_type==1)
										{{--*/ $hhg_selected='checked' /*--}}
										@endif
										@if($post_rate_card_type==2)
										{{--*/ $vehicle_selected='checked' /*--}}
										{{--*/ $hhg_selected=''; /*--}}
										@endif
									@endif
									
										<input type="radio" id="post_rate_card_type_1" name="post_rate_card_type" class="ratetype_selection_buyer" value="1" <?php echo $hhg_selected; ?>>
										<label for="post_rate_card_type_1"><span></span>HHG</label>
											
										<input type="radio" id="post_rate_card_type_2" name="post_rate_card_type" class="ratetype_selection_buyer" value="2" <?php echo $vehicle_selected; ?>>
										<label for="post_rate_card_type_2"><span></span>Vehicle</label>
									</div>
								</div>
								<div class="col-md-3 form-control-fld">
										<div class="input-prepend">
											<span class="add-on"><i class="fa fa-map-marker"></i></span>
											 {!! Form::text('from_location',$from_loaction , ['id' => 'from_location','class' => 'form-control', 'placeholder' => 'From Location*']) !!}
		                                     {!! Form::hidden('from_location_id', $from_loaction_id, array('id' => 'from_location_id')) !!}
		                                     {!! Form::hidden('seller_district_id', $session_search_values_create[8], array('id' => 'seller_district_id')) !!}
										</div>
									</div>
									<div class="col-md-3 form-control-fld">
										<div class="input-prepend">
											<span class="add-on"><i class="fa fa-map-marker"></i></span>
											{!! Form::text('to_location', $to_loaction, ['id' => 'to_location','class' => 'form-control', 'placeholder' => 'To Location*']) !!}
		                                    {!! Form::hidden('to_location_id', $to_loaction_id, array('id' => 'to_location_id')) !!}
										</div>
									</div>
									<div class="col-md-3 form-control-fld">
										<div class="input-prepend">
											<span class="add-on"><i class="fa fa-calendar-o"></i></span>
											{!! Form::text('valid_from', $from_date, ['id' => 'datepicker','class' => 'calendar form-control from-date-control','readonly' => true, 'placeholder' => 'Dispatch Date*']) !!}
										</div>
									</div>
									<div class="col-md-3 form-control-fld">
										<div class="input-prepend">
											<span class="add-on"><i class="fa fa-calendar-o"></i></span>
											{!! Form::text('valid_to', $to_date, ['id' => 'datepicker_to_location','class' => 'calendar form-control to-date-control','readonly' => true, 'placeholder' => 'Delivery Date ']) !!}
										</div>
									</div>
                                                                        <div class="clearfix"></div>
									@if($post_rate_card_type=='')
									<div class="relocation_house_hold_buyer_create">
									@endif
									@if($post_rate_card_type==1)
									<div class="relocation_house_hold_buyer_create">
									@endif
									@if($post_rate_card_type==2)
									<div class="relocation_house_hold_buyer_create" style="display:none;">
									@endif
									<div class="col-md-3 form-control-fld">
										<div class="input-prepend">
											<span class="add-on"><i class="fa fa-home"></i></span>
											{!!	Form::select('property_type',(['' => 'Property Type *'] +$property_types), $property_type ,['class' =>'selectpicker','id'=>'property_type','onchange'=>'return getPropertyCft()']) !!}
										</div>
										
									</div>
									<div class="col-md-3 form-control-fld">
											<div class="input-prepend">
												<span class="add-on"><i class="fa fa-balance-scale"></i></span>
												{!! Form::text('volume', $volume, ['id' => 'volume','class' => 'form-control clsRDSVolumeCFT','readonly' => true, 'placeholder' => 'Volume*']) !!}										
												<span class="add-on unit1 manage">
													CFT
												</span>
											</div>
									</div>
									<div class="col-md-3 form-control-fld">
										<div class="input-prepend">
											<span class="add-on"><i class="fa fa-truck"></i></span>
											{!!	Form::select('load_type',(['' => 'Load Type *'] +$load_types), $load_type ,['class' =>'selectpicker','id'=>'load_type']) !!}
										</div>
									</div>
									<div class="col-md-12 form-control-fld text-right margin-none">
										<span class="red spl-link advanced-search-link"><span class="more-search">+</span><span class="less-search">-</span> Inventory Details</span>
									</div>
									<div class="advanced-search-details">
										<div class="col-md-3 form-control-fld margin-top padding-left-none">
											<div class="radio-block">
												<span class="padding-right-15">Origin Elevator</span> 
												<input type="radio" id="elevator1_a" name="elevator_origin" value="1" <?php echo $elevator1yes; ?>>
												<label for="elevator1_a"><span></span>Yes</label>
													
												<input type="radio" id="elevator1_b" name="elevator_origin" value="0" <?php echo $elevator1no; ?>>
												<label for="elevator1_b"><span></span>No</label>
											</div>
										</div>
										<div class="col-md-3 form-control-fld margin-top padding-none">
											<div class="radio-block">
												<span class="padding-right-15">Destination Elevator</span> 
												<input type="radio" id="elevator2_a" name="elevator_destination" value="1" <?php echo $elevator2yes; ?>>
												<label for="elevator2_a"><span></span>Yes</label>
													
												<input type="radio" id="elevator2_b" name="elevator_destination" value="0" <?php echo $elevator2no; ?>>
												<label for="elevator2_b"><span></span>No</label>
											</div>
										</div>
										<div class="clearfix"></div>
										<div class="col-md-3 form-control-fld">
											<div class="radio-block"><input type="checkbox" checked /> <span class="lbl padding-8" name="origin_storage_serivce" id="origin_storage_serivce" <?php if(isset($searchrequest['origin_storage_serivce'])){ echo "checked"; } ?>>Storage</span></div>
											<div class="radio-block"><input type="checkbox" name="origin_handy_serivce" id="origin_handy_serivce" <?php if(isset($searchrequest['origin_handy_serivce'])){ echo "checked"; } ?>> <span class="lbl padding-8">Handyman Services</span></div>
											<div class="radio-block"><input type="checkbox" name="insurance_serivce" id="insurance_serivce" <?php if(isset($searchrequest['insurance_serivce'])){ echo "checked"; } ?>> <span class="lbl padding-8">Insurance</span></div>
											<div class="radio-block"><input type="checkbox" name="escort_serivce" id="escort_serivce" <?php if(isset($searchrequest['escort_serivce'])){ echo "checked"; } ?>> <span class="lbl padding-8">Escort</span></div>
											<div class="radio-block"><input type="checkbox" name="mobilty_serivce" id="mobilty_serivce" <?php if(isset($searchrequest['mobilty_serivce'])){ echo "checked"; } ?>> <span class="lbl padding-8">Mobility</span></div>
											<div class="radio-block"><input type="checkbox" name="property_serivce" id="property_serivce" <?php if(isset($searchrequest['property_serivce'])){ echo "checked"; } ?>> <span class="lbl padding-8">Property</span></div>
											<div class="radio-block"><input type="checkbox" name="setting_serivce" id="setting_serivce" <?php if(isset($searchrequest['setting_serivce'])){ echo "checked"; } ?>> <span class="lbl padding-8">Setting Service</span></div>
											<div class="radio-block"><input type="checkbox" name="insurance_domestic" id="insurance_domestic" <?php if(isset($searchrequest['insurance_domestic'])){ echo "checked"; } ?>> <span class="lbl padding-8">Insurance Domestic</span></div>
										</div>
										<div class="col-md-3 form-control-fld">
											<div class="radio-block"><input type="checkbox" name="destination_storage_serivce" id="destination_storage_serivce" <?php if(isset($searchrequest['destination_storage_serivce'])){ echo "checked"; } ?>> <span class="lbl padding-8">Storage</span></div>
											<div class="radio-block"><input type="checkbox" name="destination_handy_serivce" id="destination_handy_serivce" <?php if(isset($searchrequest['destination_handy_serivce'])){ echo "checked"; } ?>> <span class="lbl padding-8">Handyman Services</span></div>
										</div>
										<div class="clearfix"></div>							
									<h2 class="filter-head1 margin-bottom">Complete Inventory</h2>						
									<div class="col-md-3 form-control-fld margin-top">
										<div class="normal-select">
											{!!	Form::select('room_type',(['' => 'Select Inventory *'] +$room_types), '' ,['class' =>'selectpicker select-inventory','id'=>'room_type','onchange'=>'return getRoomParticulars()']) !!}
										</div>
									</div>	
									<div class="clearfix"></div>
										<!-- Table Starts Here -->							
									<div class="table-div table-style1 inventory-block margin-bottom-none">
										<div class="table-div table-style1 inventory-table">									
											<!-- Table Head Starts Here -->
											<div class="table-heading inner-block-bg">
												<div class="col-md-6 padding-left-none">&nbsp;</div>
												<div class="col-md-2 padding-left-none text-center">No of Items</div>
												<div class="col-md-2 padding-left-none text-center">Packing Required</div>
												<div class="col-md-2 padding-left-none text-center">Crating Required</div>									
											</div>
											<!-- Table Head Ends Here -->
											<div id="inventory_data" name="inventory_data"></div>									
										</div>	
		
										<!-- Table Starts Here -->
										<div class="col-md-12 form-control-fld">
										<input type=button class="btn add-btn pull-right save-continue" name="savecontinue" id="savecontinue" value="Save & Continue">								
										</div>							
										<div class="clearfix"></div>
										<div class="after-inventory-block margin-top">								
											<div class="table-div table-style1">									
											<div name="inventory_count_div" id="inventory_count_div"></div>
										</div>									
									</div>
		
										</div>	
									</div>
									
									
									</div>
									@if($post_rate_card_type=='')
									<div class="relocation_vehicle_buyer_create" style="display:none;">
									@endif
									@if($post_rate_card_type==1)
									<div class="relocation_vehicle_buyer_create" style="display:none;">
									@endif
									@if($post_rate_card_type==2)
									<div class="relocation_vehicle_buyer_create">
										@endif
										
										<div class="col-md-3 form-control-fld">
											<div class="input-prepend">
												<span class="add-on"><i class="fa fa-truck"></i></span>
												{!!	Form::select('vehicle_category',(['' => 'Vehicle Category *'] +$vehicletypecategories), $vehicle_cat ,['class' =>'selectpicker','id'=>'vehicle_category','onchange'=>'return getVehicleTypes()']) !!}
											</div>								
										</div>
										
										<div class="col-md-3 form-control-fld vehicle_type_car">
											<div class="input-prepend">
												<span class="add-on"><i class="fa fa-truck"></i></span>
												{!!	Form::select('vehicle_category_type',(['' => 'Vehicle Category Type *'] +$vehicletypecategorietypes), $vehicle_category_type ,['class' =>'selectpicker','id'=>'vehicle_category_type']) !!}
											</div>								
										</div>
										
										<div class="col-md-3 form-control-fld">
											<div class="input-prepend">
												<span class="add-on"><i class="fa fa-truck"></i></span>
												{!! Form::text('vehicle_model', $vehicle_model, ['id' => 'vehicle_model','class' => 'form-control', 'placeholder' => 'Vehicle Model*', 'maxlength'=>50 ]) !!}										
											</div>
										</div>
									
									
									</div>
									
						<div class="clearfix"></div>
						
					</div>
		
					<div class="col-md-12 inner-block-bg inner-block-bg1">
							
							<div class="col-md-12 form-control-fld margin-top margin-bottom-none">
								<div class="radio-block">
								<div class="radio_inline">
								<input type="radio" name="ptlQuoteaccessId" value="1" id="post-public" checked="checked" class="create-posttype-service crete-relocation" /> 
								<label for="post-public"><span></span>Post Public</label></div>
								<div class="radio_inline"><input type="radio" name="ptlQuoteaccessId" value="2" id="post-private" class="create-posttype-service crete-relocation"/> 
								<label for="post-private"><span></span>Post Private</label></div>
								</div>
							</div>
		
							<div class="col-md-3 form-control-fld" id="hideseller" style="display:none;">
								<input type="text" id="demo-input-local" class="form-control form-control1" name="seller_list" />
							</div>
		
							<div class="clearfix"></div>
							<div class="check-box form-control-fld">
							{!! Form::checkbox('agree', '', '',array('id'=>'agree')) !!}<span class="lbl padding-8">Accept Terms & Conditions (Digital Contract)</span>
							</div>
						</div>
						
						<div class="clearfix"></div>
		
						<div class="container">
							<div class="col-md-4 col-md-offset-4">
							<input  type="submit" class="btn theme-btn btn-block"  name="getquote" id="getquote" value="Get Quote">
							
							</div>
						</div>			
		              {!! Form::close() !!}	
			        </div>
		
			   
			  
		
		</div> 

<!-- ---------------------------------Start Relcoation Term ----------------- -->


<div class="relocation_term_show" style="display:none">
      
          
			<div class="container">		
				
					
				<div class="">	

	<div> <!-- ---statt first form div open -->
	{!! Form::open(['url' =>'#','id' => 'relocation_term_firstform' , 'autocomplete'=>'off']) !!}
	 {!! Form::hidden('term_check_valid', '', array('id' => 'term_check_valid')) !!}
	<input type="hidden" name="update_relocterm_line" id="update_relocterm_line" value="">
	<input type="hidden" name="update_relocterm_row_count" id="update_relocterm_row_count" value="">

	<div class="col-md-12 inner-block-bg inner-block-bg1 border-top-none padding-top-none margin-bottom-none border-bottom-none padding-bottom-none">
		<div class="col-md-12 form-control-fld">
							<div class="radio-block">
							
								<input type="radio" id="term_post_rate_card_type_1" name="term_post_rate_card_type" class="termratetype_selection_buyer" value="1" checked>
								<label for="term_post_rate_card_type_1"><span></span>HHG</label>
									
								<input type="radio" id="term_post_rate_card_type_2" name="term_post_rate_card_type" class="termratetype_selection_buyer" value="2">
								<label for="term_post_rate_card_type_2"><span></span>Vehicle</label>
							</div>
						</div>
		            <div class="col-md-12 padding-none inner-form margin-bottom-none">
						
						<div class="col-md-3 form-control-fld">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-calendar-o"></i></span>
									{!! Form::text('term_dispatch_date', '', ['id' => 'term_dispatch_date','class' => 'form-control calendar  from-date-control', 'placeholder' => 'Valid From *','readonly'=>"readonly"]) !!}
								</div>
							</div>
							<div class="col-md-3 form-control-fld">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-calendar-o"></i></span>
									{!! Form::text('term_delivery_date', '', ['id' => 'term_delivery_date','class' => 'form-control calendar  to-date-control', 'placeholder' => 'Valid To *','readonly'=>"readonly"]) !!}
								</div>
							</div>
					</div>


					
					<div class="col-md-12 padding-low">
					<div class="col-md-12 padding-none inner-block-bg padding-10">
						<h2 class="sub-head">Add Item Details</h2>

							<div class="col-md-3 form-control-fld">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-map-marker"></i></span>
									{!! Form::text('term_from_location', '', ['id' => 'term_from_location', 'class'=>'form-control', 'placeholder' => 'From Location *']) !!}
								   	{!! Form::hidden('term_from_location_id', '', array('id' => 'term_from_location_id')) !!}
								</div>
								
							</div>
							<div class="col-md-3 form-control-fld">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-map-marker"></i></span>
									{!! Form::text('term_to_location', '', ['id' => 'term_to_location', 'class'=>'form-control','placeholder' => 'To Location *']) !!}
									{!! Form::hidden('term_to_location_id', '', array('id' => 'term_to_location_id')) !!}
								</div>
								
							</div>
							<div class="term_relocation_hhg_buyer_create">
							<div class="col-md-3 form-control-fld">
								<div class="col-md-8 padding-none">
									<div class="input-prepend">
										<input type="text" placeholder="Avg Volume/Shipment *" class="form-control form-control1 clsRelocationAvgVolShip" name="relocation_term_volume" id="relocation_term_volume">
									</div>
								</div>
								<div class="col-md-4 padding-none">
									<div class="input-prepend">
										<span class="add-on unit-days manage" >
											<div class="normal-select" name="relocation_term_weighttype" id="relocation_term_weighttype">
												<select class="selectpicker bs-select-hidden">
													<option>CFT</option>
												</select>
											</div>
										</span>
									</div>
								</div>
							</div>

							<div class="col-md-2 form-control-fld">
								<div class="input-prepend">
									<input type="text" maxlength="4" class="form-control form-control1 numericvalidation" placeholder="No of Shipments *" name="relocation_term_noofshipments" id="relocation_term_noofshipments" />
								</div>
							</div>
							
							</div>
							
							<div class="term_relocation_vehicle_buyer_create" style="display:none;">
							
							<div class="col-md-3 form-control-fld">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-truck"></i></span>
									{!!	Form::select('term_vehicle_category',(['' => 'Vehicle Category *'] +$vehicletypecategories), $vehicle_cat ,['class' =>'selectpicker','id'=>'term_vehicle_category','onchange'=>'return getVehicleTypesTerm()']) !!}
								</div>								
							</div>
							
							<div class="col-md-3 form-control-fld vehicle_type_car_term">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-truck"></i></span>
									{!!	Form::select('term_vehicle_category_type',(['' => 'Vehicle Category Type *'] +$vehicletypecategorietypes), $vehicle_category_type ,['class' =>'selectpicker','id'=>'term_vehicle_category_type']) !!}
								</div>								
							</div>

							<div class="clearfix"></div>
							
							<div class="col-md-3 form-control-fld">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-truck"></i></span>
									{!! Form::text('term_vehicle_model', $vehicle_model, ['id' => 'term_vehicle_model','class' => 'form-control', 'placeholder' => 'Vehicle Model*', 'maxlength'=>50]) !!}										
								</div>
							</div>
							<div class="col-md-3 form-control-fld">
								<div class="input-prepend">
									<input type="text" maxlength="4" class="form-control form-control1 numericvalidation" placeholder="No of Vehicles *" name="relocation_term_nooftrips" id="relocation_term_nooftrips" />
								</div>
							</div>
							
							
							</div>

							<div class="col-md-1 form-control-fld">
								<input type="submit" value="Add" class="btn add-btn" id="term_add_relocation">
								<div id="error-relocation-term-add-item" class="error "></div>
							</div>

						</div>
					</div>
				</div>
				{!! Form::close() !!}
			</div> <!-- ---End first form div close -->
	
		
{!! Form::open(['url' =>'relocationbuyertermcreate','id' => 'term_relocbuyer_quote', 'files'=>true, 'autocomplete'=>'off']) !!}
{!! Form::hidden('spot_term_value', '2', array('id' => 'spot_term_value')) !!}
<input type="hidden" id="term_post_rate_card_type" name="term_post_rate_card_type" class="termratetype_selection_buyer" value="1">
<div> <!-- ---Start  second term form div open -->

	<div class="col-md-12 inner-block-bg inner-block-bg1 border-top-none padding-top-none padding-bottom-none">

					<div class="col-md-12 padding-none form-control-fld margin-top">
						<div class="main-inner"> 
							

							<!-- Right Section Starts Here -->

							<div class="main-right">

								<!-- Table Starts Here -->

								<div class="table-div table-style1 margin-bottom-none">
									
									<!-- Table Head Starts Here -->

									<div class="table-heading inner-block-bg hhggrid">
										<div class="col-md-3 padding-left-none">From<i class="fa fa-caret-down"></i></div>
										<div class="col-md-3 padding-left-none">To<i class="fa fa-caret-down"></i></div>
										<div class="col-md-3 padding-left-none">Avg Volume/Shipment<i class="fa fa-caret-down"></i></div>
										<div class="col-md-2 padding-left-none">No of Shipments<i class="fa fa-caret-down"></i></div>
										<div class="col-md-1 padding-left-none"></div>
									</div>
									
									
									<div class="table-heading inner-block-bg vehiclegrid" style="display:none;">
										<div class="col-md-2 padding-left-none">From<i class="fa fa-caret-down"></i></div>
										<div class="col-md-2 padding-left-none">To<i class="fa fa-caret-down"></i></div>
										<div class="col-md-2 padding-left-none">Vehicle Category<i class="fa fa-caret-down"></i></div>
										<div class="col-md-2 padding-left-none">Vehicle Category Type<i class="fa fa-caret-down"></i></div>
										<div class="col-md-2 padding-left-none">Vehicle Model<i class="fa fa-caret-down"></i></div>
										<div class="col-md-1 padding-left-none">No of Vehicles</div>
										<div class="col-md-1 padding-left-none"></div>
									</div>
									

									<!-- Table Head Ends Here -->

									<div class="table-data relocation_term_request_rows">										

										<!-- Table Row Starts Here -->

										
											<input type="hidden" id='next_term_add_relocation_buyer_more_id' value='0'>
										

										<!-- Table Row Ends Here -->

									</div>
								</div>	

								<!-- Table Starts Here -->

							</div>

							<!-- Right Section Ends Here -->

						</div>
					
					</div>
				</div>
				</div>
				
					<div class="col-md-12 inner-block-bg inner-block-bg1">
						<!-- bid type section starts-->
						<div class="col-md-12 padding-none inner-form margin-bottom-none">
						<div class="margin-top"></div>
						
								
							<div class="col-md-3 form-control-fld">
									<div class="input-prepend">
										<span class="add-on"><i class="fa fa-calendar-o"></i></span>
										{!! Form::text('last_bid_date', '', ['id' => 'last_bid_date','class' => 'form-control calendar', 'placeholder' => 'Bid Closure Date *','readonly'=>"readonly"]) !!}
									</div>
						 	 </div>
								
							<div class="col-md-3 form-control-fld">
								<div class="input-prepend date clsbid_close_time" id="bid_time_icon_add"> 
									<span class="add-on"><i class="fa fa-clock-o"></i></span>
									{!! Form::text('bid_close_time', '', ['id' => 'bid_close_time','class' => 'form-control clock timepicker', 'placeholder' => 'Bid Closure Time *', 'readonly'=>"readonly"]) !!}
								</div>
                                <label for="bid_close_time" id="err_bid_close_time" class="error"></label>
							</div>
								
							<div class="clearix"></div>
							<div class="col-md-12 form-control-fld"><span>Bid Terms & Conditions</span></div>
						
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
												<button class="btn add-btn upload-browse-btn pull-right">Browse</button>
												<input type="file" name="terms_condtion_types_term_defualt" class="form-control form-control1 update_txt" value="" id="terms_condtion_types_term_defualt" />
								            </div>
										</div>
										<div class="col-md-3 form-control-fld">	
									           		 									
							
									<input type="button" class="documents-add btn add-btn" value="Add +">
								</div>	
								<div class="clearfix"></div>													
						</div>
						</div>
						
						<div class="col-md-6 form-control-fld">								
								<textarea  class="form-control form-control1 clsFTLComments" name="buyer_notes" id="buyer_notes" placeholder="Comments" ></textarea>	
						</div>	
						

					</div>
					
						<!--file upload div ends-->
					
					
					<div class="col-md-12 inner-block-bg inner-block-bg1">	
						<div class="col-md-12 form-control-fld margin-none padding-none">
							<div class="radio-block">
                            <div class="radio_inline"><input type="radio" name="quoteaccess_id" value="1" id="term_relocation_post_public" checked> <label for="term_relocation_post_public"><span></span>Post Public</label></div>
							<div class="radio_inline"><input type="radio" name="quoteaccess_id" value="2" id="term_relocation_post_private" class="create-posttype-service-ftl-term"> <label for ='term_relocation_post_private' class="create-posttype-service-ftl lbl padding-8"><span></span>Post Private</label></div>
                        </div>
						</div>
						
						<div class="clearfix"></div>
						
						
						<div class="col-md-3 form-control-fld" id="showhidepost" style="display:none;">
						<input type="text" class="form-control form-control1" id="term_seller_list" name="term_seller_list" placeholder="Seller Name (Auto Search)"/>
						</div>
						<div class="clearfix"></div>
						<div class="normal-checkbox">
						   {!! Form::checkbox('agree', '', '', ['class' => 'field','id'=>'agree']) !!} <span class="lbl padding-8">Accept Terms & Conditions (Digital Contract)</span>
						   </div>
					</div>
			

				<div class="clearfix"></div>

				
					<div class="col-md-12 padding-none">
					<input type="hidden" name="confirm_but" id="confirm_but" value="">
						{!! Form::submit('Float RFP', ['name' => 'confirm','class'=>'btn theme-btn flat-btn pull-right term_relocation_add_buyer_quote','id' => 'term_relocation_add_buyer_quote']) !!}
						{!! Form::submit('Save As Draft', ['name' => 'draft','class'=>'btn add-btn flat-btn pull-right term_relocation_add_buyer_quote','id' => 'term_relocation_add_buyer_quote_draft']) !!}
					
				</div>
{!! Form::close() !!}

    </div>
					
		</div>		


</div>




@include('partials.footer')
@endsection



