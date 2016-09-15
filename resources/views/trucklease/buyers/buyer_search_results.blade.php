@inject('buyerCommonComponent', 'App\Components\BuyerComponent')
@inject('commonComponent', 'App\Components\CommonComponent')
@extends('app')
@section('content') 
<!-- Page top navigation Starts Here-->
@include('partials.page_top_navigation')

<div class="main">
<div class="container">

				<h1 class="page-title">Search Results (Truck Lease)</h1><a class="change-service" data-toggle="modal" data-target="#change-service">Change Service</a>
				{!! Form::open(['url' =>'#','id' => 'buyer_results_form','class'=>'filter_form','method'=>'get']) !!}				
				<!-- Search Block Starts Here -->
				
				<div class="search-block inner-block-bg">
					<div class="from-to-area">
						<span class="search-result">
							<i class="fa fa-map-marker"></i>
							<span class="location-text">
								
								{!! Form::hidden('from_location_id', $from_location_id) !!}
								{!! Form::hidden('from_location', $from_location) !!}
                                {!! Form::hidden('delivery_flexible_hidden', Session::get('session_fdelivery_date_buyer')) !!}
                                {!! Form::hidden('dispatch_flexible_hidden', Session::get('session_fdispatch_date_buyer')) !!}
                                 {!! Form::hidden('is_commercial', Session::get('session_is_commercial_date_buyer')) !!}
								{{$from_location}}</span>
						</span>
					</div>
					<div class="date-area">
						<div class="col-md-6 padding-none">
							<p class="search-head">From</p>
							<span class="search-result">
								<i class="fa fa-calendar-o"></i>
								{!! Form::hidden('from_date', Session::get('session_dispatch_date_buyer'), ['id' => 'from_date','class' => 'form-control']) !!}
								{{$fdispatch}}
							</span>
						</div>
						<div class="col-md-6 padding-none">
							<p class="search-head">To</p>
							<span class="search-result">
								<i class="fa fa-calendar-o"></i>
								{!! Form::hidden('to_date', Session::get('session_delivery_date_buyer'), ['id' => 'to_date','class' => 'form-control']) !!}
								@if($to_date!= '') {{$fdelivery}} @else NA @endif
							</span>
						</div>
					</div>
					<div>
						<p class="search-head">Vehicle Type</p>
						<span class="search-result">
                            {!! Form::hidden('lkp_vehicle_type_id', $lkp_vehicle_type_id, ['id' => 'vehicle_type']) !!}
							{{$vehicle_type_name}}
						</span>
					</div>
					<div>
						<p class="search-head">Lease Term</p>
						{!! Form::hidden('lkp_trucklease_lease_term_id', $lkp_trucklease_lease_term_id, ['id' => 'load_type','class' => 'form-control']) !!}
						<span class="search-result">{{$lease_type_name}}</span>
					</div>
					<div>
						<p class="search-head">Driver</p>
						{!! Form::hidden('driver_availability', $driver_availability_id, ['id' => 'driver_availability','class' => 'form-control']) !!}
						<span class="search-result">{{$driver_availability_text}}</span>
					</div>
					<div class="search-modify" data-toggle="modal" data-target="#modify-search">
						<span>Modify Search +</span>
					</div>

				</div> <!-- Search block -->

				<h2 class="side-head pull-left">Filter Results</h2>
				<!--button class="btn post-btn pull-right">Post & get Quote</button-->
				@include("partials.content_top_navigation_links")

				<div class="clearfix"></div>

				<div class="col-md-12 padding-none">
					<div class="main-inner">

						<!-- Left Section Starts Here -->

						<div class="main-left">
							<input type="hidden" id="driver_availability" name="driver_availability" value="<?php echo isset($_REQUEST['driver_availability']) ? $_REQUEST['driver_availability'] : 1; ?>" />
                            @if ((Session::has('show_layered_filter')&& Session::get('show_layered_filter')!="") || (!isset($_REQUEST['is_search'])))
								@include("partials.filter._price")							
                            @endif     
							<?php
								$selectedPayment = isset($_REQUEST['selected_payments']) ? $_REQUEST['selected_payments'] : array();

							?>
							@if (Session::has('show_layered_filter')&& Session::get('show_layered_filter')!="")
								@if (Session::has('layered_filter_payments')&& Session::get('layered_filter_payments')!="")
							<h2 class="filter-head">Payment Mode</h2>
							<div class="payment-mode inner-block-bg">
								@foreach (Session::get('layered_filter_payments') as $paymentId => $paymentName)
								<?php $selected = in_array($paymentId, $selectedPayment) ? 'checked="checked"' : ""; ?>
								<div class="check-box"><input type="checkbox" class="filtercheckbox" value="{{$paymentId}}" {{$selected}} name="selected_payments[]" onClick="this.form.submit()"/><span class="lbl padding-8"> 
                                                                        @if ($paymentName == 'Advance') 
                                                                    {{--*/ $paymentType = 'Online Payment' /*--}}
                                                                    @else
                                                                    {{--*/ $paymentType = $paymentName /*--}}
                                                                    @endif
                                                                    {{$paymentType}}
                                                                </span></div>
								@endforeach
							</div>
							@endif
							@endif




                                                        
<!--							<h2 class="filter-head">Tracking</h2>
							<div class="tracking inner-block-bg">
								<div class="check-box">
									<input type="checkbox" name="tracking" value="1" onClick="this.form.submit()" <?php if(isset($_REQUEST['tracking']) && $_REQUEST['tracking']!="") { echo "checked='checked'"; } ?>  ><span class="lbl padding-8">Milestone</span>
									</div>
								<div class="check-box"><input type="checkbox" name="tracking1" value="2" onClick="this.form.submit()" <?php if(isset($_REQUEST['tracking1']) && $_REQUEST['tracking1']!="") { echo "checked='checked'"; } ?>><span class="lbl padding-8">Real time</span></div>
							</div>-->


							<div class="tracking inner-block-bg">
								<div class="check-box"><input type="checkbox" name="ftltopseller_orders"  <?php //if(isset($_REQUEST['ftltopseller_orders']) && $_REQUEST['ftltopseller_orders']) { echo "checked='checked'"; } ?>><span class="lbl padding-8">Top Sellers (Orders) </span></div>
								<div class="check-box"><input type="checkbox" name="ftltopseller_rated"  <?php //if(isset($_REQUEST['ftltopseller_rated']) && $_REQUEST['ftltopseller_rated']) { echo "checked='checked'"; } ?>><span class="lbl padding-8">Top Sellers (Rated) </span></div>
							</div>

							<?php
								$selectedSellers = isset($_REQUEST['selected_users']) ? $_REQUEST['selected_users'] : array();
							?>


							@if (Session::has('show_layered_filter')&& Session::get('show_layered_filter')!="")
								@if (Session::has('layered_filter')&& Session::get('layered_filter')!="")
							<h2 class="filter-head"><?php echo (Auth::user()->lkp_role_id == 1) ? "Sellers" : "Buyers"; ?> List</h2>
							<div class="seller-list inner-block-bg">
								@foreach (Session::get('layered_filter') as $userId => $userName)
								<?php $selected = in_array($userId, $selectedSellers) ? 'checked="checked"' : ""; ?>
								<div class="check-box"><input type="checkbox"  class="filtercheckbox" value="{{$userId}}" {{$selected}} name="selected_users[]" onClick="this.form.submit()" /><span class="lbl padding-8"> {{ $userName }}</span></div>
								@endforeach
							</div>
							@endif
							@endif



							<?php /*if((isset($_REQUEST['dispatch_flexible_hidden']) && $_REQUEST['dispatch_flexible_hidden']) || (isset($_REQUEST['date_flexiable']) && ($_REQUEST['date_flexiable']!=""))) { ?>

							<h2 class="filter-head">Preferred Dispatch Date</h2>
							<div class="seller-list inner-block-bg">

								 <?php
							
								$flexdate = (isset($_REQUEST['from_date']) && !empty($_REQUEST['from_date'])) ? $_REQUEST['from_date'] : (isset($_REQUEST['date_flexiable']) ? $_REQUEST['date_flexiable'] : "");
							
									for($i=-3;$i<=3;$i++){
										$selected = "";
										if($i<0){
											$date1 = date('Y-m-d', strtotime(str_replace('/', '-',$flexdate)));//new DateTime($flexdate);
											$date1 = new DateTime($date1);
											$date1=$date1->modify("$i day");
										}else if($i>0){
											$date1 = date('Y-m-d', strtotime(str_replace('/', '-',$flexdate)));
											$date1 = new DateTime($date1);
											$date1=$date1->modify("$i day");
										}else{
											$date1 = date('Y-m-d', strtotime(str_replace('/', '-',$flexdate)));
											$date1 = new DateTime($date1);
											//$selected = "selected='selected'";
										}
										if(isset($_REQUEST['date_flexiable'])){
											if(($_REQUEST['date_flexiable'] == $date1->format('Y-m-d'))){
												//$selected = "selected='selected'";
												$selected = "checked='checked'";
											}
										}else {
											if(isset($_REQUEST['from_date'])){
											if($_REQUEST['from_date'] == $date1->format('d/m/Y')){
												$selected = "checked='checked'";
											}
											}
										}
										if($date1->format('Y-m-d') >= date('Y-m-d')){
											//echo "<option ".$selected." value='".$date1->format('Y-m-d')."'>".$date1->format('d-m-Y')."</option>";

											echo "<div class='check-box'><input type='radio' id ='date_flexiable_$i' name='date_flexiable' onChange='this.form.submit()' ".$selected." value='".$date1->format('Y-m-d')."' /><label for='date_flexiable_$i'><span></span>".$date1->format('d-m-Y')."</label></div>";
										}
									}
									//echo "</select>";
									//echo "<pre>"; print_R($_REQUEST); echo "</pre>"; die;
								 ?>

							</div>
							<?php }*/ ?>
								<?php if(isset($_REQUEST['from_date']) || isset($_REQUEST['to_date']) ) { ?>
									<!--input type="hidden" name="from_date" value="<?php //echo $_REQUEST['from_date']; ?>">
									<input type="hidden" name="to_date" value="<?php //echo $_REQUEST['to_date']; ?>">
									<input type="hidden" name="lkp_trucklease_lease_term_id" value="<?php //echo $_REQUEST['lkp_trucklease_lease_term_id']; ?>">
									<input type="hidden" name="quantity" value="<?php //echo $_REQUEST['quantity']; ?>">
									<input type="hidden" name="capacity" value="<?php //echo $_REQUEST['capacity']; ?>">
									<input type="hidden" name="lkp_vehicle_type_id" value="<?php //echo $_REQUEST['lkp_vehicle_type_id']; ?>"-->

									<!--input type="hidden" name="selected_users" id="selected_users" value="<?php //echo isset($_REQUEST['selected_users']) ? $_REQUEST['selected_users'] : ""; ?>"/>

									<input type="hidden" name="selected_payments" id="selected_payments" value="<?php //echo isset($_REQUEST['selected_payments']) ? $_REQUEST['selected_payments'] : ""; ?>"/-->

									<div class="clearfix"></div>
								<?php } ?>

						</div> <!-- Left Filters section -->

							 <!-- Wrong placement of the filter close, but loop has to be end here. -->

						<div class="main-right">
							<div class='table-data table-div' id="booknow_buyer_form">
								{!! $gridBuyer !!}
							</div>
						</div>


					</div>
				</div>


				{!! Form::close() !!}
</div>
</div>


<!-- Model Window starts -->
		<div class="modal fade" id="modify-search" role="dialog">
	    <div class="modal-dialog">

	      <!-- Modal content-->
	      <div class="modal-content">
	        <button type="button" class="close" data-dismiss="modal">&times;</button>
	        {!! Form::open(['url' =>'byersearchresults','id' => 'buyer_search_form_tl' , 'autocomplete'=>'off','method'=>'get']) !!}
	        <div class="modal-body">
				<div class="col-md-12 padding-none">
                                    
                                                        {!! Form::hidden('is_commercial', Session::get('session_is_commercial_date_buyer')) !!}
                                                        @if(Session::get('session_is_commercial_date_buyer') == 1)
                                                                {{--*/ $is_commercial = "checked" /*--}}
                                                        @else
                                                                {{--*/ $is_commercial = "" /*--}}
                                                        @endif
                                                        @if(Session::get('session_is_commercial_date_buyer') == 0)
                                                                {{--*/ $is_noncommercial = "checked" /*--}}
                                                        @else
                                                                {{--*/ $is_noncommercial = "" /*--}}
                                                        @endif
                                                        
                                                        <div class="col-md-12 form-control-fld margin-none">
                                                            <div class="radio-block">
                                                                <div class="radio_inline"><input type="radio" name="is_commercial" id="is_commercial"  value="1" {{ $is_commercial }} /> <label for="is_commercial"><span></span>Commercial</label></div>
                                                                <div class="radio_inline"><input type="radio" name="is_commercial" id="non_commercial" value="0" {{$is_noncommercial}} /> <label for="non_commercial"><span></span>Non Commercial</label></div>
                                                            </div>
                                                        </div>
                                    
                                    
					<div class="col-md-4 form-control-fld">
						<div class="input-prepend">
							<span class="add-on"><i class="fa fa-map-marker"></i></span>

							{!! Form::text('from_location', Session::get('session_from_location_buyer'), ['id' => 'from_location', 'class'=>'form-control clsLocation','placeholder' => 'Location *']) !!}
							{!! Form::hidden('from_location_id', Session::get('session_from_city_id_buyer'), array('id' => 'from_location_id')) !!}
                                                        
						</div>
					</div>

					<div class="col-md-4 form-control-fld">
						<div class="input-prepend">
							<span class="add-on"><i class="fa fa-calendar-o"></i></span>
							{!! Form::text('from_date', Session::get('session_dispatch_date_buyer'), ['id' => 'dispatch_date1','class' => 'clsFromDate form-control from-date-control', 'placeholder' => 'From Date *','readonly'=>"readonly"]) !!}
							<input type="hidden" name="dispatch_flexible_hidden" id="dispatch_flexible_hidden" value="<?php echo isset($_REQUEST['dispatch_flexible_hidden']) ? $_REQUEST['dispatch_flexible_hidden'] : ""; ?>">
						</div>
					</div>
					<div class="col-md-4 form-control-fld">
						<div class="input-prepend">
							<span class="add-on"><i class="fa fa-calendar-o"></i></span>
							{!! Form::text('to_date', Session::get('session_delivery_date_buyer'), ['id' => 'delivery_date1','class' => 'clsToDate form-control to-date-control', 'placeholder' => 'To Date *','readonly'=>"readonly"]) !!}
							<input type="hidden" name="delivery_flexible_hidden" id="delivery_flexible_hidden" value="<?php echo isset($_REQUEST['delivery_flexible_hidden']) ? $_REQUEST['delivery_flexible_hidden'] : ""; ?>">
						</div>
					</div>
					<div class="clearfix"></div>

					<div class="col-md-4 form-control-fld">
						<div class="input-prepend">
							<span class="add-on"><i class="fa fa-archive"></i></span>
							{!!	Form::select('lkp_vehicle_type_id',(['' => 'Select Vehicle Type *'] +$vehicle_type + ['20'=>'Vehicle Type (Any)']), Session::get('session_vehicle_type_buyer'),['class' =>'selectpicker form_control','id'=>'vechile_type']) !!}
						</div>
					</div>
					<div class="col-md-4 form-control-fld">
						<div class="input-prepend">
							<span class="add-on"><i class="fa fa-archive"></i></span>
							{!!	Form::select('lkp_trucklease_lease_term_id',(['' => 'Lease Term *'] +$getAllleaseTypes ), Session::get('session_lease_term_buyer'),['class' =>'selectpicker form_control','id'=>'lkp_trucklease_lease_term_id']) !!}
						</div>
					</div>

					<div class="col-md-4 form-control-fld">
						<div class="input-prepend">
							<span class="add-on"><i class="fa fa-wheelchair"></i></span>
							{!!	Form::select('driver_availability',$driver_availability, Session::get('session_driver_availability'),['class' =>'selectpicker form_control','id'=>'driver_availability']) !!}
						</div>
					</div>


					{!!	Form::hidden('is_search',1,array('class'=>'form-control')) !!}



					<div class="col-md-6 form-control-fld">
						<img src="images/truck.png" class="truck-type" /> <span class="truck-type-text">Vehicle Dimensions * <span id ='dimension'><?php echo request('lkp_vehicle_type_id')? $commonComponent::getVehicleReqCol((int)request('lkp_vehicle_type_id')):''; ?></span></span>
					</div>
                                        
                                                        

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
	  </div>

<!-- Modal Window ends here --> 
<script type="text/javascript">
$(document).ready(function() {
	loadLeaseterms($(".clsFromDate").val(), $(".clsToDate").val());
});
</script>
@endsection