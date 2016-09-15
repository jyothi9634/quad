@inject('buyerCommonComponent', 'App\Components\BuyerComponent')
@inject('commonComponent', 'App\Components\CommonComponent')

@extends('app')
@section('content') 
<!-- Page top navigation Starts Here-->
@include('partials.page_top_navigation')

<div class="main">
<div class="container">

				<h1 class="page-title">Search Results (Haul)</h1><a class="change-service" data-toggle="modal" data-target="#change-service">Change Service</a>
				{!! Form::open(['url' =>'#','id' => 'buyer_results_form','class'=>'filter_form','method'=>'get']) !!}				
				<!-- Search Block Starts Here -->
				
				<div class="search-block inner-block-bg">
					<div class="from-to-area">
						<span class="search-result">
							<i class="fa fa-map-marker"></i>
							<span class="location-text">
								
								{!! Form::hidden('from_location_id', $from_location_id) !!}
								{!! Form::hidden('to_location_id', $to_location_id) !!}
                                {!! Form::hidden('from_location', $from_location) !!}
                                {!! Form::hidden('to_location', $to_location) !!}
                                {!! Form::hidden('dispatch_flexible_hidden', Session::get('session_fdispatch_date_buyer')) !!}
								{{$from_location}} to {{$to_location}}</span>
                                                        {!! Form::hidden('is_commercial', Session::get('session_is_commercial_date_buyer')) !!}
						</span>
					</div>
					<div class="date-area">
						<div class="col-md-6 padding-none">
							<p class="search-head">Reporting Date</p>
							<span class="search-result">
								<i class="fa fa-calendar-o"></i>
<!--								{!! Form::hidden('from_date', $from_date, ['id' => 'from_date','class' => 'form-control']) !!}-->
                                                                {!! Form::hidden('from_date', Session::get('session_dispatch_date_buyer'), ['id' => 'from_date','class' => 'form-control']) !!}
								{{$fdispatch}}
							</span>
						</div>						
					</div>
					<div>
						<p class="search-head">Load Type</p>
                                                {!! Form::hidden('lkp_load_type_id', $lkp_load_type_id, ['id' => 'load_type','class' => 'form-control']) !!}
						<span class="search-result">{{$load_type_name}}</span>
					</div>
					<div>
						<p class="search-head">Quantity</p>
						<span class="search-result">
							{!! Form::hidden('quantity', $quantity, ['class' => 'form-control']) !!}
							{!! Form::hidden('capacity', $capacity, ['class' => 'form-control']) !!}
							{{$quantity}}&nbsp;{{$capacity}}

						</span>
					</div>
					<div>
						<p class="search-head">Vehicle Type</p>
						<span class="search-result">

                                                        {!! Form::hidden('lkp_vehicle_type_id', $lkp_vehicle_type_id, ['id' => 'vehicle_type']) !!}
							{{$vehicle_type_name}}
						</span>
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




                                                        
							<h2 class="filter-head">Tracking</h2>
							<div class="tracking inner-block-bg">
								<div class="check-box">
									<input type="checkbox" name="tracking" value="1" onClick="this.form.submit()" <?php if(isset($_REQUEST['tracking']) && $_REQUEST['tracking']!="") { echo "checked='checked'"; } ?>  ><span class="lbl padding-8">{{TRACKING_MILE_STONE}}</span>
									</div>
								<div class="check-box"><input type="checkbox" name="tracking1" value="2" onClick="this.form.submit()" <?php if(isset($_REQUEST['tracking1']) && $_REQUEST['tracking1']!="") { echo "checked='checked'"; } ?>><span class="lbl padding-8">{{TRACKING_REAL_TIME}}</span></div>
							</div>


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



							<?php if((isset($_REQUEST['dispatch_flexible_hidden']) && $_REQUEST['dispatch_flexible_hidden']) || (isset($_REQUEST['date_flexiable']) && ($_REQUEST['date_flexiable']!=""))) { ?>

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
										}
										if(isset($_REQUEST['date_flexiable'])){
											if(($_REQUEST['date_flexiable'] == $date1->format('Y-m-d'))){
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
											echo "<div class='check-box'><input type='radio' id ='date_flexiable_$i' name='date_flexiable' onChange='this.form.submit()' ".$selected." value='".$date1->format('Y-m-d')."' /><label for='date_flexiable_$i'><span></span>".$date1->format('d-m-Y')."</label></div>";
										}
									}
								 ?>

							</div>
							<?php } ?>
								<?php if(isset($_REQUEST['from_date']) || isset($_REQUEST['to_date']) ) { ?>
									<input type="hidden" name="no_of_loads" value="<?php echo $_REQUEST['no_of_loads']; ?>">
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
	        {!! Form::open(['url' =>'byersearchresults','id' => 'buyer_search_form_modify' , 'autocomplete'=>'off','method'=>'get']) !!}
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

								{!! Form::text('from_location', Session::get('session_from_location_buyer'), ['id' => 'from_location', 'class'=>'form-control clsTHFromLocation','placeholder' => 'From Location *']) !!}
								{!! Form::hidden('from_location_id', Session::get('session_from_city_id_buyer'), array('id' => 'from_location_id')) !!}
                                                                
							</div>
						</div>
						<div class="col-md-4 form-control-fld">
							<div class="input-prepend">
								<span class="add-on"><i class="fa fa-map-marker"></i></span>
								{!! Form::text('to_location', Session::get('session_to_location_buyer'), ['id' => 'to_location', 'class'=>'form-control clsTHToLocation', 'placeholder' => 'To Location *']) !!}
								{!! Form::hidden('to_location_id', Session::get('session_to_city_id_buyer'), array('id' => 'to_location_id')) !!}
							</div>
						</div>
						<div>
							<div class="col-md-4 form-control-fld">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-calendar-o"></i></span>
									{!! Form::text('from_date', Session::get('session_dispatch_date_buyer'), ['id' => 'dispatch_date1','class' => 'calendar form-control from-date-control clsTHReportingDate', 'placeholder' => 'Reporting Date *','readonly'=>"readonly"]) !!}
									<input type="hidden" name="dispatch_flexible_hidden" id="dispatch_flexible_hidden" value="<?php echo isset($_REQUEST['dispatch_flexible_hidden']) ? $_REQUEST['dispatch_flexible_hidden'] : ""; ?>">
								</div>
							</div>							
							<div class="col-md-4 form-control-fld">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-archive"></i></span>
									{!!	Form::select('lkp_load_type_id',( ['11'=>'Load Type (Any)'] +$load_type ), Session::get('session_load_type_buyer'),['class' =>'selectpicker form_control','id'=>'load_type','onChange'=>'return GetCapacity()']) !!}
								</div>
							</div>
							<div class="col-md-4 form-control-fld">
									<div class="input-prepend">
									<span class="add-on"><i class="fa fa-balance-scale"></i></span>
									<!-- input type="text" placeholder="Qty *" class="form-control" name="quantity" id="quantity"-->
									{!! Form::text('quantity', Session::get('session_quantity_buyer'), ['id' => 'quantity', 'class'=>'form-control clsTHQuantity','placeholder' => 'Qty *']) !!}
									<span class="add-on unit1">{!!	Form::text('capacity',Session::get('session_capacity_buyer'),array('class'=>'form-control','id'=>'capacity','placeholder'=>'Capacity','readonly')) !!}</span>
								</div>
							</div>
							<div class="col-md-4 form-control-fld">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-archive"></i></span>                                                                        
                                    {!!	Form::select('lkp_vehicle_type_id',(['20'=>'Vehicle Type (Any)'] +$vehicle_type ), Session::get('session_vehicle_type_buyer'),['class' =>'selectpicker form_control','id'=>'vechile_type']) !!}
                                </div>
							</div>
							{!!	Form::hidden('no_of_loads',null,array('class'=>'form-control','placeholder'=>'Loads','id'=>'no_of_loads')) !!}
							{!!	Form::hidden('is_search',1,array('class'=>'form-control')) !!}



							<div class="col-md-4 form-control-fld pull-right">
								<img src="images/truck.png" class="truck-type" /> <span class="truck-type-text">Vehicle Dimensions * <span id ='dimension'><?php echo request('lkp_vehicle_type_id')? $commonComponent::getVehicleReqCol((int)request('lkp_vehicle_type_id')):''; ?></span>/span>
							</div>
                                                        
                                                        
                                                        
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




@endsection