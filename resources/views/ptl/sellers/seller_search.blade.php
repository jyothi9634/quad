@inject('common', 'App\Components\CommonComponent')
@extends('app') @section('content')
<div class="clearfix"></div>
@include('partials.page_top_navigation')

	<div class="clearfix"></div>
	<div class="main">

		<div class="container">
                    <span class="pull-left">
			<h1 class="page-title">Search Results 
				@if(Session::get('service_id') == ROAD_PTL)
				(LTL)
				@elseif(Session::get('service_id') == RAIL)
				(RAIL)
				@elseif(Session::get('service_id') == AIR_DOMESTIC)
				(AIR DOMESTIC)
				@elseif(Session::get('service_id') == AIR_INTERNATIONAL)
				(AIR INTERNATIONAL)
				@elseif(Session::get('service_id') == OCEAN)
				(OCEAN)
				@elseif(Session::get('service_id') == COURIER)
				(COURIER)
				@endif</h1>
                    
			<a class="change-service" data-toggle="modal" data-target="#change-service">Change Service</a>
                </span>
			<!-- Search Block Starts Here -->

			<div class="search-block inner-block-bg">
				<div class="from-to-area">
					<span class="search-result"> <i class="fa fa-map-marker"></i> <span
						class="location-text">
						@if($zone_or_location == 2)
							@if(Session::get('service_id') == ROAD_PTL)
							{{$common::getPinName($from_city_id)}} to {{$common::getPinName($to_city_id)}}
							@elseif(Session::get('service_id') == COURIER)
							{{$common::getPinName($from_city_id)}} to 
							@if($package_type_name == '1')
							{{$common::getPinName($to_city_id)}}
							@else
							{{$common::getCountry($to_city_id)}}
							@endif
							@elseif(Session::get('service_id') == RAIL)
							{{$common::getPinName($from_city_id)}} to {{$common::getPinName($to_city_id)}}
							@elseif(Session::get('service_id') == AIR_DOMESTIC)
							{{$common::getPinName($from_city_id)}} to {{$common::getPinName($to_city_id)}}
							@elseif(Session::get('service_id') == AIR_INTERNATIONAL)
							{{$common::getAirportName($from_city_id)}} to {{$common::getAirportName($to_city_id)}}
							@elseif(Session::get('service_id') == OCEAN)
							{{$common::getSeaportName($from_city_id)}} to {{$common::getSeaportName($to_city_id)}}
							@endif
						@else
							@if(Session::get('service_id') == ROAD_PTL)
							{{$common::getZoneName($from_city_id)}} to {{$common::getZoneName($to_city_id)}}
							@elseif(Session::get('service_id') == COURIER)
							{{$common::getZoneName($from_city_id)}} to 
							@if($package_type_name == '1')
							{{$common::getZoneName($to_city_id)}}
							@else
							{{$common::getCountry($to_city_id)}}
							@endif
							@elseif(Session::get('service_id') == RAIL)
							{{$common::getZoneName($from_city_id)}} to {{$common::getZoneName($to_city_id)}}
							@elseif(Session::get('service_id') == AIR_DOMESTIC)
							{{$common::getZoneName($from_city_id)}} to {{$common::getZoneName($to_city_id)}}
							@elseif(Session::get('service_id') == AIR_INTERNATIONAL)
							{{$common::getAirportName($from_city_id)}} to {{$common::getAirportName($to_city_id)}}
							@elseif(Session::get('service_id') == OCEAN)
							{{$common::getSeaportName($from_city_id)}} to {{$common::getSeaportName($to_city_id)}}
							@endif
						@endif
						</span>
					</span>
				</div>
				<div class="date-area">
					<div class="col-md-6 padding-none">
						<p class="search-head">Dispatch Date</p>
						<span class="search-result"> <i class="fa fa-calendar-o"></i> {!! $dispatch_date !!}
						</span>
					</div>
					<div class="col-md-6 padding-none">
						<p class="search-head">Delivery Date</p>
						<span class="search-result"> <i class="fa fa-calendar-o"></i> 
						@if($delivery_date=='')
						NA
						@else
						{!! $delivery_date !!}
						@endif
						</span>
					</div>
				</div>
				@if(Session::get('service_id') != COURIER)
					<div>
						<p class="search-head">Load Type</p>
						<span class="search-result">@if(isset($load_type_name) && $load_type_name !='')
						{!! $load_type_name !!}
						@endif
						</span>
					</div>
					<div>
						<p class="search-head">Package Type</p>
						<span class="search-result">@if(isset($package_type_name) && $package_type_name !='')
						{!! $package_type_name !!}
						@endif
						</span>
					</div>
				@else
					<div>
						<p class="search-head">Destination Type</p>
						<span class="search-result">
						@if(isset($package_type_name) && $package_type_name !='')
							@if($package_type_name==1)
							Domestic
							@else
							International
							@endif
						@endif
						</span>
					</div>
					<div>
						<p class="search-head">Courier Type</p>
						<span class="search-result">
						@if(isset($load_type_name) && $load_type_name !='')
						@if($load_type_name == 1)
						Document
						@else
						Parcel
						@endif
						@endif
						</span>
					</div>
				@endif
				<div class="search-modify" data-toggle="modal"
					data-target="#modify-search">
					<span>Modify Search +</span>
				</div>
			</div>

			<!-- Search Block Ends Here -->



			<h2 class="side-head pull-left">Filter Results</h2>
			@include('partials.content_top_navigation_links')

			<div class="clearfix"></div>

			<div class="col-md-12 padding-none">
				<div class="main-inner">

					<!-- Left Section Starts Here -->

					<div class="main-left">
{!! Form::open(['url' => 'buyersearchresults','id'=>'seller_posts_buyers_search_filter','method'=>'get','class'=>'filter_form']) !!}
<input type="hidden" name="spot_or_term" id="spot_or_term" value="<?php echo isset($_REQUEST['spot_or_term']) ? $_REQUEST['spot_or_term'] : ""; ?>"/>                                                        
<input type="hidden" name="option_wise_ptl" id="option_wise_ptl" value="<?php echo isset($_REQUEST['option_wise_ptl']) ? $_REQUEST['option_wise_ptl'] : ""; ?>"/>
<input type="hidden" name="zone_or_location" id="zone_or_location_change" value="<?php echo isset($_REQUEST['zone_or_location']) ? $_REQUEST['zone_or_location'] : ""; ?>"/>
<input type="hidden" name="selected_users" id="selected_users" value="<?php echo isset($_REQUEST['selected_users']) ? $_REQUEST['selected_users'] : ""; ?>"/>                                                        
<input type="hidden" name="courier_or_types" id="courier_or_types" value="<?php echo isset($_REQUEST['courier_or_types']) ? $_REQUEST['courier_or_types'] : ""; ?>"/>                                                        
<input type="hidden" name="post_or_delivery_type" id="post_or_delivery_type" value="<?php echo isset($_REQUEST['post_or_delivery_type']) ? $_REQUEST['post_or_delivery_type'] : ""; ?>"/>                                                        


<!--input type="hidden" name="from_location" id="from_location" value="<?php //echo isset($_REQUEST['from_location']) ? $_REQUEST['from_location'] : ""; ?>"/-->
<input type="hidden" name="from_location_id" id="from_location_id_filter" value="<?php echo isset($_REQUEST['from_location_id']) ? $_REQUEST['from_location_id'] : ""; ?>"/>
<!--input type="hidden" name="to_location" id="to_location" value="<?php echo isset($_REQUEST['to_location']) ? $_REQUEST['to_location'] : ""; ?>"/-->
<input type="hidden" name="to_location_id" id="to_location_id_filter" value="<?php echo isset($_REQUEST['to_location_id']) ? $_REQUEST['to_location_id'] : ""; ?>"/>
<input type="hidden" name="lkp_load_type_id"  value="<?php echo isset($_REQUEST['lkp_load_type_id']) ? $_REQUEST['lkp_load_type_id'] : ""; ?>"/>
<input type="hidden" name="lkp_packaging_type_id"  value="<?php echo isset($_REQUEST['lkp_packaging_type_id']) ? $_REQUEST['lkp_packaging_type_id'] : ""; ?>"/>                                                        
<input type="hidden" name="lkp_air_ocean_shipment_type_id" id="lkp_air_ocean_shipment_type_id" value="<?php echo isset($_REQUEST['lkp_air_ocean_shipment_type_id']) ? $_REQUEST['lkp_air_ocean_shipment_type_id'] : ""; ?>"/>
<input type="hidden" name="lkp_air_ocean_sender_identity_id" id="lkp_air_ocean_sender_identity_id" value="<?php echo isset($_REQUEST['lkp_air_ocean_sender_identity_id']) ? $_REQUEST['lkp_air_ocean_sender_identity_id'] : ""; ?>"/>

<!--input type="hidden" name="dispatch_date" id="dispatch_date" value="<?php //echo isset($_REQUEST['dispatch_date']) ? $_REQUEST['dispatch_date'] : ""; ?>"/>
<input type="hidden" name="delivery_date" id="delivery_date" value="<?php //echo isset($_REQUEST['delivery_date']) ? $_REQUEST['delivery_date'] : ""; ?>"/-->                                                        
<input type="hidden" name="dispatch_flexible_hidden" id="dispatch_flexible_hidden1" value="<?php echo isset($_REQUEST['dispatch_flexible_hidden']) ? $_REQUEST['dispatch_flexible_hidden'] : ""; ?>"/>
<input type="hidden" name="delivery_flexible_hidden" id="delivery_flexible_hidden1" value="<?php echo isset($_REQUEST['delivery_flexible_hidden']) ? $_REQUEST['delivery_flexible_hidden'] : ""; ?>"/>
<input type="hidden" name="is_filter" id="is_filter" value="1">	
				<h2 class="filter-head">Search Filter</h2>
				<div class="inner-block-bg">
				<div class="gray_bg">
						
                                                
							<div class="col-xs-12 padding-none margin-bottom displayNone">
								{!! Form::text('from_location', $from_location, ['class' => 'form-control form-control1', 'readonly' => true]) !!}
								
							</div>
							
							<div class="col-xs-12 padding-none margin-bottom displayNone">
								 {!! Form::text('to_location', $to_location, ['class' => 'form-control form-control1', 'readonly' => true]) !!}
								 
							</div>
                                    
							<div class="clearfix"></div>
							<div class="col-xs-12 padding-none padding-top">
							<div class="normal-select">
								
							@if(isset($_REQUEST['zone_or_location']) && $_REQUEST['zone_or_location']==2)
							@if(Session::get('service_id') == ROAD_PTL)
							<div class="col-xs-12 form-control-fld">
								<div class="input-prepend">	
									{!! Form::text('from_name', $common::getPinName($from_city_id), ['class' => 'form-control form-control1', 'readonly' => true]) !!}
								</div>
							</div>
							<div class="col-xs-12 form-control-fld">
								<div class="input-prepend">
									{!! Form::text('to_name', $common::getPinName($to_city_id), ['class' => 'form-control form-control1', 'readonly' => true]) !!}
								</div>
							</div>
							
							@elseif(Session::get('service_id') == COURIER)
							<div class="col-xs-12 form-control-fld">
								<div class="input-prepend">
									{!! Form::text('from_name', $common::getPinName($from_city_id), ['class' => 'form-control form-control1', 'readonly' => true]) !!} 
								</div>
							</div>
                                                            @if($package_type_name == 1)
                                                            <div class="col-xs-12 form-control-fld">
                                                                    <div class="input-prepend">
                                                                            {!! Form::text('to_name', $common::getPinName($to_city_id), ['class' => 'form-control form-control1', 'readonly' => true]) !!}
                                                                    </div>
                                                            </div>
                                                            @else
                                                            <div class="col-xs-12 form-control-fld">
                                                                    <div class="input-prepend">
                                                                            {!! Form::text('to_name', $common::getCountry($to_city_id), ['class' => 'form-control form-control1', 'readonly' => true]) !!}
                                                                    </div>
                                                            </div>
                                                            @endif
							@elseif(Session::get('service_id') == RAIL)
							<div class="col-xs-12 form-control-fld">
								<div class="input-prepend">
									{!! Form::text('load_type_name', $common::getPinName($from_city_id), ['class' => 'form-control form-control1', 'readonly' => true]) !!}
								</div>
							</div>
							<div class="col-xs-12 form-control-fld">
								<div class="input-prepend">
									{!! Form::text('load_type_name', $common::getPinName($to_city_id), ['class' => 'form-control form-control1', 'readonly' => true]) !!}
								</div>
							</div>
							@elseif(Session::get('service_id') == AIR_DOMESTIC)
							<div class="col-xs-12 form-control-fld">
								<div class="input-prepend">
									{!! Form::text('load_type_name', $common::getPinName($from_city_id), ['class' => 'form-control form-control1', 'readonly' => true]) !!}
								</div>
							</div>
							<div class="col-xs-12 form-control-fld">
								<div class="input-prepend">
									{!! Form::text('load_type_name', $common::getPinName($to_city_id), ['class' => 'form-control form-control1', 'readonly' => true]) !!}
								</div>
							</div>
							@elseif(Session::get('service_id') == AIR_INTERNATIONAL)
							<div class="col-xs-12 form-control-fld">
								<div class="input-prepend">	
									{!! Form::text('load_type_name', $common::getAirportName($from_city_id), ['class' => 'form-control form-control1', 'readonly' => true]) !!}
								</div>
							</div>
							<div class="col-xs-12 form-control-fld">
								<div class="input-prepend">
									{!! Form::text('load_type_name', $common::getAirportName($to_city_id), ['class' => 'form-control form-control1', 'readonly' => true]) !!}
								</div>
							</div>
							
							@elseif(Session::get('service_id') == OCEAN)
							<div class="col-xs-12 form-control-fld">
								<div class="input-prepend">
									{!! Form::text('load_type_name', $common::getSeaportName($from_city_id), ['class' => 'form-control form-control1', 'readonly' => true]) !!}
								</div>
							</div>
							<div class="col-xs-12 form-control-fld">
								<div class="input-prepend">
									{!! Form::text('load_type_name', $common::getSeaportName($to_city_id), ['class' => 'form-control form-control1', 'readonly' => true]) !!}
								</div>
							</div>
							
							@endif
						@else
						
						@if(Session::get('results_count')==1)
							@if(Session::get('service_id') == ROAD_PTL)
								<div class="col-xs-12 form-control-fld">
									<div class="input-prepend">
										{!! Form::text('from_name', $common::getZoneName($from_city_id), ['class' => 'form-control form-control1', 'readonly' => true]) !!}
									</div>
								</div>
								<div class="col-xs-12 form-control-fld">
									<div class="input-prepend">
										{!! Form::text('to_name', $common::getZoneName($to_city_id), ['class' => 'form-control form-control1', 'readonly' => true]) !!}
									</div>
								</div>
							
							@elseif(Session::get('service_id') == COURIER)
							<div class="col-xs-12 form-control-fld">
								<div class="input-prepend">
									{!! Form::text('from_name', $common::getZoneName($from_city_id), ['class' => 'form-control form-control1', 'readonly' => true]) !!}
								</div>
							</div>
							
							@if($package_type_name == 1)
							<div class="col-xs-12 form-control-fld">
								<div class="input-prepend">
									{!! Form::text('to_name', $common::getZoneName($to_city_id), ['class' => 'form-control form-control1', 'readonly' => true]) !!}
								</div>
							</div>
							@else
							<div class="col-xs-12 form-control-fld">
								<div class="input-prepend">
									{!! Form::text('to_name', $common::getCountry($to_city_id), ['class' => 'form-control form-control1', 'readonly' => true]) !!}
								</div>
							</div>
							
							@endif
							@elseif(Session::get('service_id') == RAIL)
								<div class="col-xs-12 form-control-fld">
									<div class="input-prepend">
										{!! Form::text('from_name', $common::getZoneName($from_city_id), ['class' => 'form-control form-control1', 'readonly' => true]) !!}
									</div>
								</div>
								<div class="col-xs-12 form-control-fld">
									<div class="input-prepend">
										{!! Form::text('to_name', $common::getZoneName($to_city_id), ['class' => 'form-control form-control1', 'readonly' => true]) !!}
									</div>
								</div>
							@elseif(Session::get('service_id') == AIR_DOMESTIC)
							<div class="col-xs-12 form-control-fld">
									<div class="input-prepend">
									{!! Form::text('from_name', $common::getZoneName($from_city_id), ['class' => 'form-control form-control1', 'readonly' => true]) !!}
								</div>
							</div>
							<div class="col-xs-12 form-control-fld">
								<div class="input-prepend">
									{!! Form::text('to_name', $common::getZoneName($to_city_id), ['class' => 'form-control form-control1', 'readonly' => true]) !!}
								</div>
							</div>
							@elseif(Session::get('service_id') == AIR_INTERNATIONAL)
							<div class="col-xs-12 form-control-fld">
								<div class="input-prepend">
									{!! Form::text('from_name', $common::getAirportName($from_city_id), ['class' => 'form-control form-control1', 'readonly' => true]) !!}
								</div>
							</div>
							<div class="col-xs-12 form-control-fld">
								<div class="input-prepend">
									{!! Form::text('to_name', $common::getAirportName($to_city_id), ['class' => 'form-control form-control1', 'readonly' => true]) !!}
								</div>
							</div>
							@elseif(Session::get('service_id') == OCEAN)
							<div class="col-xs-12 form-control-fld">
								<div class="input-prepend">
								{!! Form::text('from_name', $common::getSeaportName($from_city_id), ['class' => 'form-control form-control1', 'readonly' => true]) !!}
								</div>
							</div>
							<div class="col-xs-12 form-control-fld">
								<div class="input-prepend">
								{!! Form::text('to_name', $common::getSeaportName($to_city_id), ['class' => 'form-control form-control1', 'readonly' => true]) !!}
								</div>
							</div>
							
							@endif
						@endif		
					  @endif
						</div>
						</div>
							<div class="col-xs-12 padding-none">
								<div class="normal-select">
                                                                    
								@if(isset($load_type_name) && $load_type_name !='')
                                                                @if(Session::get('service_id')==COURIER)
								@if($load_type_name == 1)
								<div class="col-xs-12 form-control-fld">
									<div class="input-prepend">
										{!! Form::text('load_type_name', 'Document', ['class' => 'form-control form-control1', 'readonly' => true]) !!}
									</div>
								</div>
								@else
								<div class="col-xs-12 form-control-fld">
									<div class="input-prepend">
										{!! Form::text('load_type_name', 'Parcel', ['class' => 'form-control form-control1', 'readonly' => true]) !!}
									</div>
								</div>
								@endif
								@endif
								
								@else
									{!! $filter->field('lkp_load_type_id') !!}
									@if(isset($_REQUEST['lkp_load_type_id']) && $_REQUEST['lkp_load_type_id'] != '')
									{!! Form::hidden('lkp_load_type_ids', $_REQUEST['lkp_load_type_id'], array('id' => 'lkp_load_type_ids')) !!}
									@elseif(isset($_REQUEST['lkp_load_type_ids']) && $_REQUEST['lkp_load_type_ids'] != '')
									{!! Form::hidden('lkp_load_type_ids', $_REQUEST['lkp_load_type_ids'], array('id' => 'lkp_load_type_ids')) !!}
									@endif
								@endif
								</div>
							</div>
							<div class="col-xs-12 padding-none">
								<div class="normal-select">
								@if(isset($package_type_name) && $package_type_name !='')
                                                                @if(Session::get('service_id')==COURIER)
								@if($package_type_name==1)
								<div class="col-xs-12 form-control-fld">
									<div class="input-prepend">
										{!! Form::text('package_type_name', 'Domestic', ['class' => 'form-control form-control1', 'readonly' => true]) !!}
									</div>
								</div>
									@else
								<div class="col-xs-12 form-control-fld">
									<div class="input-prepend">
										{!! Form::text('package_type_name', 'International', ['class' => 'form-control form-control1', 'readonly' => true]) !!}
									</div>
								</div>
									@endif
								@endif
                                                                
								@else
									{!! $filter->field('lkp_packaging_type_id') !!}
									@if(isset($_REQUEST['lkp_packaging_type_id']) && $_REQUEST['lkp_packaging_type_id'] != '')
									{!! Form::hidden('lkp_packaging_type_ids', $_REQUEST['lkp_packaging_type_id'], array('id' => 'lkp_packaging_type_ids')) !!}
									@elseif(isset($_REQUEST['lkp_packaging_type_ids']) && $_REQUEST['lkp_packaging_type_ids'] != '')
									{!! Form::hidden('lkp_packaging_type_ids', $_REQUEST['lkp_packaging_type_ids'], array('id' => 'lkp_packaging_type_ids')) !!}
									@endif
								@endif
									</div>
							</div>

							<div class="col-xs-12 padding-none margin-bottom displayNone ">
							<div class="">
							{!! Form::text('dispatch_date', $dispatch_date, ['id' => 'dispatch_filter_calendar','class' => 'calendar-icon form-control form-control1', 'placeholder' => 'dispatch date', 'readonly' => true]) !!}
							</div>
							</div>
							<div class="col-xs-12 padding-none margin-bottom displayNone">
							<div class="">
								{!! Form::text('delivery_date', $delivery_date, ['id' => 'delivery_filter_calendar','class' => 'calendar-icon form-control form-control1', 'placeholder' => 'Delivery Date', 'readonly' => true]) !!}
							</div>
							</div>
							<div class="clearfix"></div>
						
						</div>
						

</div>
@if (Session::has('layered_filter'))
<h2 class="filter-head"><?php echo (Auth::user()->lkp_role_id == 1) ? "Sellers" : "Buyers"; ?> List</h2>
	<div class="seller-list inner-block-bg">
							
		<?php
		$selectedSellers = isset($_REQUEST['selected_users']) ? explode(",",$_REQUEST['selected_users']) : array();
		?>

		
			
				<div class="layered_nav  margin-top col-xs-12 padding-none">
				@if(Session::has('layered_filter') && is_array(Session::get('layered_filter')))
					@foreach (Session::get('layered_filter') as $userId => $userName)
						<?php $selected = in_array($userId, $selectedSellers) ? 'checked="checked"' : ""; ?>
						<div class="check-box"><input type="checkbox" class="checkbox pull-left filtercheckbox" value="{{$userId}}" {{$selected}} name="search_by_user"/><span class="lbl padding-8">{{ $userName }}</span></div>
						<!--div class="col-xs-12 padding-none"><input type="checkbox" class="checkbox pull-left filtercheckbox" value="{{$userId}}" {{$selected}} name="search_by_user"> {{ $userName }}</div-->
					@endforeach
				@endif
				</div>
			
		
</div>
@endif
<?php
	Session::forget('show_layered_filter');
?>
<?php //echo "<pre>";print_r($_REQUEST);exit;

if((isset($_REQUEST['dispatch_flexible_hidden']) && $_REQUEST['dispatch_flexible_hidden']) || (isset($_REQUEST['date_flexiable']) && ($_REQUEST['date_flexiable']!=""))) { ?>

							<h2 class="filter-head">Preferred Dispatch Date</h2>
							<div class="seller-list inner-block-bg">
								
								 <?php
								
								$flexdate = (isset($_REQUEST['dispatch_date']) && !empty($_REQUEST['dispatch_date'])) ? $_REQUEST['dispatch_date'] : (isset($_REQUEST['date_flexiable']) ? $_REQUEST['date_flexiable'] : "");
								
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
											if(isset($_REQUEST['dispatch_date'])){
											if($_REQUEST['dispatch_date'] == $date1->format('d/m/Y')){
												$selected = "checked='checked'";
											}
										}
									}
										if($date1->format('Y-m-d') >= date('Y-m-d')){
											
											echo "<div class='check-box'><input id ='date_flexiable_$i' class='dispatch_dates_cust checkbox pull-left filtercheckbox' name ='date_flexiable' onChange='this.form.submit()' ".$selected." type='radio' value='".$date1->format('Y-m-d')."' /><label for='date_flexiable_$i'><span></span>".$date1->format('d-m-Y')."</label></div>";
										}
									}
								 ?>
								
							</div>	
							<?php } ?>
						
{!! Form::close() !!}                                                        
					</div>


					<div class="main-right">
					{!! $grid !!}

					</div>

					

				</div>
				@include('partials.content_top_navigation_links')
				<div class="clearfix"></div>
			</div>
		</div>
	






<!-- Modal -->
<div class="modal fade" id="modify-search" role="dialog">
	<div class="modal-dialog">

		<!-- Modal content-->
		<div class="modal-content">
			<button type="button" class="close" data-dismiss="modal">&times;</button>
			<div class="modal-body">
				<div class="col-md-12 modal-form">
					<div class="home-search-modfy">
						<div class="col-md-12 padding-none">
							<div class="col-md-12 form-control-fld">
								<div class="radio-block">
									<div class="radio_inline">
									<input type="radio" name="lead_type" id="spot_lead_type" value="1" checked="checked" /> 
									<label for="spot_lead_type"><span></span>Spot</label>
									</div>
									<div class="radio_inline">
									<input type="radio" name="lead_type" id="term_lead_type" value="2" /> 
									<label for="term_lead_type"><span></span>Term</label>
									</div>
 								</div>


							</div>
						</div>
						<div class="clearfix"></div>
						<div class="showhide_spot" id="showhide_spot">
							{!! Form::open(['url' =>'buyersearchresults','method'=>'GET','id'=>'sellers-posts-buyers-ptl']) !!}
							{!! Form::hidden('spot_or_term',1,array('class'=>'form-control spot_or_term')) !!}
							<input type="hidden" id="serviceid" value={{ Session::get('service_id') }}>
							@if(Session::get('service_id') != AIR_INTERNATIONAL && Session::get('service_id') != OCEAN && Session::get('service_id') != COURIER)
								@if(Session::get('zone_or_location_ptl') && Session::get('zone_or_location_ptl') == '1') 
									{{--*/ $zone_selected = true /*--}}
								@else
									{{--*/ $zone_selected = false /*--}}
								@endif

								@if(Session::has('zone_or_location_ptl') && Session::get('zone_or_location_ptl') == '2') 
									{{--*/ $location_selected = true /*--}}
								@else
									{{--*/ $location_selected = false /*--}}
								@endif
								<div class="col-md-12 text-center padding-none">
									<div class="col-md-12 form-control-fld">
										<div class="radio-block">
											<div class="radio_inline">
												{!! Form::radio('option_wise_ptl', 'Zone wise', $zone_selected, ['id' => 'zone_wise_ptl']) !!}
												<label for="zone_wise_ptl"><span></span>Zone wise</label>
											</div>
											<div class="radio_inline">
												{!! Form::radio('option_wise_ptl', 'Location wise', $location_selected, ['id' => 'location_wise_ptl']) !!}

												<label for="location_wise_ptl"><span></span>Location wise</label>
												{!! Form::hidden('zone_or_location', Session::get('zone_or_location_ptl'), array('id' => 'zone_or_location')) !!}
											</div>
										</div>
									</div>
								</div>
								<div class="clearfix"></div>
							@endif
							@if(Session::get('service_id') == COURIER)
														
								@if(Session::get('zone_or_location_ptl') && Session::get('zone_or_location_ptl') == '1') 
									{{--*/ $zone_selected = true /*--}}
								@else
									{{--*/ $zone_selected = false /*--}}
								@endif

								@if(Session::has('zone_or_location_ptl') && Session::get('zone_or_location_ptl') == '2') 
									{{--*/ $location_selected = true /*--}}
								@else
									{{--*/ $location_selected = false /*--}}
								@endif
								
								
								@if(Session::has('post_or_delivery_type_ptl') && Session::get('post_or_delivery_type_ptl') == '1') 
									{{--*/ $domestic_selected = true /*--}}
									{{--*/ $to_location_class = 'numericvalidation_autopop maxlimitsix_lmtVal' /*--}}
									{{--*/ $domestic_international_selected = 1 /*--}}
								@else
									{{--*/ $domestic_selected = false /*--}}
									{{--*/ $to_location_class = '' /*--}}
								@endif
								
								@if(Session::has('post_or_delivery_type_ptl') && Session::get('post_or_delivery_type_ptl') == '2') 
									{{--*/ $international_selected = true /*--}}
									{{--*/ $domestic_international_selected = 2 /*--}}
								@else
									{{--*/ $international_selected = false /*--}}
								@endif
								
								@if(Session::has('courier_or_types_ptl') && Session::get('courier_or_types_ptl') == '1')
									{{--*/ $document_selected = true /*--}}
									{{--*/ $document_parcel_selected = 1 /*--}}
								@else
									{{--*/ $document_selected = false /*--}}
								@endif
								
								@if(Session::has('courier_or_types_ptl') && Session::get('courier_or_types_ptl') == '2') 
									{{--*/ $parcel_selected = true /*--}}
									{{--*/ $document_parcel_selected = 2 /*--}}
								@else
									{{--*/ $parcel_selected = false /*--}}
								@endif
								
								<div class="col-md-3 form-control-fld">
								<div class="radio-block">
									<div class="radio_inline">
									{!! Form::radio('option_wise_ptl', 'Zone wise', $zone_selected, ['id' => 'zone_wise_ptl']) !!} <label for="zone_wise_ptl"><span></span>Zone Wise</label>
								   </div>
									<div class="radio_inline">
									{!! Form::radio('option_wise_ptl', 'Location wise', $location_selected, ['id' => 'location_wise_ptl']) !!} <label for="location_wise_ptl"><span></span>Location Wise</label>
									{!! Form::hidden('zone_or_location', Session::get('zone_or_location_ptl'), array('id' => 'zone_or_location')) !!}
									</div>
								</div>
							</div>
							<div class="col-md-3 form-control-fld">
								<div class="radio-block">
									<div class="radio_inline">
									{!! Form::radio('post_delivery_type', 'Domestic', $domestic_selected, ['id' => 'domestic']) !!} <label for="domestic"><span></span>Domestic</label>
									</div>
									<div class="radio_inline">
									{!! Form::radio('post_delivery_type', 'International', $international_selected, ['id' => 'international']) !!} <label for="international"><span></span>International</label>
									{!! Form::hidden('post_or_delivery_type', $domestic_international_selected, array('id' => 'post_delivery_type')) !!}
									</div>
								</div>
							</div>
							<div class="col-md-3 form-control-fld">
								<div class="radio-block">
									<div class="radio_inline">
									{!! Form::radio('courier_types', 'Documents', $document_selected, ['id' => 'documents']) !!} <label for="documents"><span></span>Documents</label>
									</div>
									<div class="radio_inline">
									{!! Form::radio('courier_types', 'Parcel', $parcel_selected, ['id' => 'parcel']) !!} <label for="parcel"><span></span>Parcel</label>
									{!! Form::hidden('courier_or_types', $document_parcel_selected, array('id' => 'courier_types')) !!}
									</div>
								</div>
							</div>
								<div class="clearfix"></div>
							@endif
							
							

							<div class="col-md-3 form-control-fld">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-calendar-o"></i></span>
									{!! Form::text('dispatch_date', Session::get('session_dispatch_date_ptl'), ['id' => 'datepicker_search','class' => 'calendar form-control from-date-control', 'readonly'=>true,'placeholder' => 'Dispatch Date*']) !!}
									<input type="hidden" name="dispatch_flexible_hidden" class="dispatch_flexi"  id="dispatch_flexible_hidden" value="<?php echo isset($_REQUEST['dispatch_flexible_hidden']) ? $_REQUEST['dispatch_flexible_hidden'] : ""; ?>"/>

									<!-- <input type="hidden" class="dispatch_flexi" name="dispatch_flexible_hidden" id="dispatch_flexible_hidden" value="0"> -->
								</div>
							</div>

							<div class="col-md-3 form-control-fld">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-calendar-o"></i></span>
									{{--*/ $search_delivery_date = (Session::get('session_delivery_date_ptl') == "0000-00-00") ? "" : ( (strpos(Session::get('session_delivery_date_ptl'), '-') !== false) ? "" : Session::get('session_delivery_date_ptl')) /*--}}
									{!! Form::text('delivery_date', $search_delivery_date, ['id' => 'datepicker_to_location_search','class' => 'calendar form-control to-date-control', 'readonly'=>true,'placeholder' => 'Delivery Date']) !!}
									<input type="hidden" name="delivery_flexible_hidden" class="delivery_flexi"  id="delivery_flexible_hidden" value="<?php echo isset($_REQUEST['delivery_flexible_hidden']) ? $_REQUEST['delivery_flexible_hidden'] : ""; ?>"/>
									<!-- input type="hidden" class="delivery_flexi" name="delivery_flexible_hidden" id="delivery_flexible_hidden" value="0"> -->
								</div>
							</div>
							@if(Session::get('service_id') != AIR_INTERNATIONAL && Session::get('service_id') != OCEAN)
								<div class="col-md-3 form-control-fld">
									<div class="input-prepend">
										<span class="add-on"><i class="fa fa-map-marker"></i></span>
										{!! Form::text('from_location', Session::get('session_from_location_ptl'), ['id' => 'from_location_ptl_search','class' => 'top-text-fld form-control numericvalidation_autopop maxlimitsix_lmtVal', 'placeholder' => 'From Location with pin code / Zone']) !!}
										{!! Form::hidden('from_location_id', Session::get('session_from_city_id_ptl'), array('id' => 'from_location_id')) !!}
									</div>
								</div>
								<div class="col-md-3 form-control-fld">
									<div class="input-prepend">
										<span class="add-on"><i class="fa fa-map-marker"></i></span>
										{!! Form::text('to_location', Session::get('session_to_location_ptl'), ['id' => 'to_location_ptl_search','class' => 'top-text-fld form-control '.$to_location_class, 'placeholder' => 'To Location with pin code / Zone']) !!}
										{!! Form::hidden('to_location_id', Session::get('session_to_city_id_ptl'), array('id' => 'to_location_id')) !!}
									</div>
								</div>
							@else
								@if(Session::get('service_id') == AIR_INTERNATIONAL)
									{{--*/ $plceholder = 'Airports' /*--}}
								@elseif(Session::get('service_id') == OCEAN)
									{{--*/ $plceholder = 'Ocean' /*--}}
								@else
									{{--*/ $plceholder = '' /*--}}
								@endif

								<div class="col-md-3 form-control-fld">
									<div class="input-prepend">
										<span class="add-on"><i class="fa fa-map-marker"></i></span>
										{!! Form::text('from_location', Session::get('session_from_location_ptl'), ['id' => 'from_location_ptl_search','class' => 'top-text-fld form-control', 'placeholder' => 'From '.$plceholder.'*']) !!}
										{!! Form::hidden('from_location_id', Session::get('session_from_city_id_ptl'), array('id' => 'from_location_id')) !!}
									</div>
								</div>
								<div class="col-md-3 form-control-fld">
									<div class="input-prepend">
										<span class="add-on"><i class="fa fa-map-marker"></i></span>
										{!! Form::text('to_location', Session::get('session_to_location_ptl'), ['id' => 'to_location_ptl_search','class' => 'top-text-fld form-control', 'placeholder' => 'To '.$plceholder.'*']) !!}
										{!! Form::hidden('to_location_id', Session::get('session_to_city_id_ptl'), array('id' => 'to_location_id')) !!}
									</div>
								</div>
							@endif
							<div class="clearfix"></div>
							@if(Session::get('service_id') != COURIER)
								<div class="col-md-3 form-control-fld">
									<div class="input-prepend">
										<span class="add-on"><i class="fa fa-archive"></i></span>
										{!! Form::select('lkp_load_type_id', (['11'=>'Load Type (Any)'] + $loadtypemasters ), Session::get('session_load_type_ptl'), ['class' => 'selectpicker bs-select-hidden','id' => 'load_type','onChange'=>'return GetCapacity()']) !!}
									</div>
								</div>
								<div class="col-md-3 form-control-fld">
									<div class="input-prepend">
										<span class="add-on"><i class="fa fa-archive"></i></span>
										{!! Form::select('lkp_packaging_type_id', (['' => 'Packaging Type*'] + $packagingtypesmasters), Session::get('session_vehicle_type_ptl'), ['class' => 'selectpicker bs-select-hidden','id' => 'lkp_packaging_type_id']) !!}
									</div>
								</div>
							@endif




							@if(Session::get('service_id') == AIR_INTERNATIONAL || Session::get('service_id') == OCEAN)


								<div class="col-md-3 form-control-fld">
									<div class="input-prepend">
										<span class="add-on"><i class="fa fa-archive"></i></span>
										{!! Form::select('lkp_air_ocean_shipment_type_id', (['' => 'Shipment Type*'] + $shipmenttypes), Session::get('session_shipment_type'), ['class' => 'selectpicker bs-select-hidden','id' => 'lkp_air_ocean_shipment_type_id','onChange'=>'return GetCapacity()']) !!}
									</div>
								</div>
								<div class="col-md-3 form-control-fld">
									<div class="input-prepend">
										<span class="add-on"><i class="fa fa-archive"></i></span>
										{!! Form::select('lkp_air_ocean_sender_identity_id', (['' => 'Sender Identity*'] + $senderidentity), Session::get('session_sender_identity'), ['class' => 'selectpicker bs-select-hidden','id' => 'lkp_air_ocean_sender_identity_id']) !!}
									</div>
								</div>


							@endif
							<div class="submit_container">
								<div class="col-md-4 col-md-offset-4">
									<input type="submit" id ='buyersearchresults' value="&nbsp; Search &nbsp;" class="btn theme-btn btn-block">
								</div>
							</div>

							{!! Form::close() !!}

						</div>





						<div class="showhide_term" id="showhide_term" style="display:none">
							{!! Form::open(['url' =>'termsellersearchresults','method'=>'GET','id'=>'sellers-search-buyers-ptl']) !!}
							{!! Form::hidden('spot_or_term',1,array('class'=>'form-control spot_or_term')) !!}
							@if(Session::get('service_id') != AIR_INTERNATIONAL && Session::get('service_id') != OCEAN && Session::get('service_id') != COURIER)
								<div class="col-md-12 text-center padding-none">
									<div class="col-md-12 form-control-fld">
										<div class="radio-block">
											<div class="radio_inline">
												{!! Form::radio('option_wise_ptl', 'Zone wise', true, ['id' => 'term_zone_wise_ptl']) !!}
												<label for="term_zone_wise_ptl"><span></span>Zone wise</label>
											</div>
											<div class="radio_inline">
												{!! Form::radio('option_wise_ptl', 'Location wise', false, ['id' => 'term_location_wise_ptl']) !!}

												<label for="term_location_wise_ptl"><span></span>Location wise</label>
												{!! Form::hidden('zone_or_location', '1', array('id' => 'term_zone_or_location')) !!}
											</div>
										</div>
									</div>
								</div>
							@endif


							@if(Session::get('service_id') == COURIER)
								
								<div class="col-md-3 form-control-fld">
								<div class="radio-block">
									<div class="radio_inline">
									{!! Form::radio('option_wise_ptl', 'Zone wise', true, ['id' => 'term_zone_wise_ptl']) !!} <label for="term_zone_wise_ptl"><span></span>Zone Wise</label>
								   </div>
									<div class="radio_inline">
									{!! Form::radio('option_wise_ptl', 'Location wise', false, ['id' => 'term_location_wise_ptl']) !!} <label for="term_location_wise_ptl"><span></span>Location Wise</label>
									{!! Form::hidden('zone_or_location', '1', array('id' => 'term_zone_or_location')) !!}
									</div>
								</div>
							</div>
							<div class="col-md-3 form-control-fld">
								<div class="radio-block">
									<div class="radio_inline">
									{!! Form::radio('post_delivery_type', 'Domestic', true, ['id' => 'term_domestic']) !!} <label for="term_domestic"><span></span>Domestic</label>
									</div>
									<div class="radio_inline">
									{!! Form::radio('post_delivery_type', 'International', false, ['id' => 'term_international']) !!} <label for="term_international"><span></span>International</label>
									{!! Form::hidden('post_or_delivery_type', 1, array('id' => 'term_post_delivery_type')) !!}
									</div>
								</div>
							</div>
							<div class="col-md-3 form-control-fld">
								<div class="radio-block">
									<div class="radio_inline">
									{!! Form::radio('courier_types', 'Documents', true, ['id' => 'term_documents']) !!} <label for="term_documents"><span></span>Documents</label>
									</div>
									<div class="radio_inline">
									{!! Form::radio('courier_types', 'Parcel', false, ['id' => 'term_parcel']) !!} <label for="term_parcel"><span></span>Parcel</label>
									{!! Form::hidden('courier_or_types', 1, array('id' => 'term_courier_types')) !!}
									</div>
								</div>
							</div>
								<div class="clearfix"></div>
							@endif
							
							
							
							@if(Session::get('service_id') != AIR_INTERNATIONAL && Session::get('service_id') != OCEAN)
								<div class="col-md-3 form-control-fld">
									<div class="input-prepend">
										<span class="add-on"><i class="fa fa-map-marker"></i></span>
										{!! Form::text('term_from_location', '', ['id' => 'term_from_location_ptl_search','class' => 'top-text-fld form-control alphanumericspace_strVal', 'placeholder' => 'From Zone','maxlength'=>10]) !!}
										{!! Form::hidden('term_from_location_id', '', array('id' => 'term_from_location_id')) !!}
									</div>
								</div>
								<div class="col-md-3 form-control-fld">
									<div class="input-prepend">
										<span class="add-on"><i class="fa fa-map-marker"></i></span>
										{!! Form::text('term_to_location', '', ['id' => 'term_to_location_ptl_search','class' => 'top-text-fld form-control alphanumericspace_strVal', 'placeholder' => 'To Zone','maxlength'=>10]) !!}
										{!! Form::hidden('term_to_location_id', '', array('id' => 'term_to_location_id')) !!}
									</div>
								</div>
							@else
								@if(Session::get('service_id') == AIR_INTERNATIONAL)
									{{--*/ $plceholder = 'Airports' /*--}}
								@elseif(Session::get('service_id') == OCEAN)
									{{--*/ $plceholder = 'Ocean' /*--}}
								@else
									{{--*/ $plceholder = '' /*--}}
								@endif

								<div class="col-md-3 form-control-fld">
									<div class="input-prepend">
										<span class="add-on"><i class="fa fa-map-marker"></i></span>
										{!! Form::text('term_from_location', '', ['id' => 'term_from_location_ptl_search','class' => 'top-text-fld form-control', 'placeholder' => 'From '.$plceholder.'*']) !!}
										{!! Form::hidden('term_from_location_id', '', array('id' => 'term_from_location_id')) !!}
									</div>
								</div>
								<div class="col-md-3 form-control-fld">
									<div class="input-prepend">
										<span class="add-on"><i class="fa fa-map-marker"></i></span>
										{!! Form::text('term_to_location', '', ['id' => 'term_to_location_ptl_search','class' => 'top-text-fld form-control', 'placeholder' => 'To '.$plceholder.'*']) !!}
										{!! Form::hidden('term_to_location_id', '', array('id' => 'term_to_location_id')) !!}
									</div>
								</div>
							@endif
							
							@if(Session::get('service_id') != COURIER)
							<div class="col-md-3 form-control-fld">
								<div class="input-prepend normal-select">
									<span class="add-on"><i class="fa fa-archive"></i></span>
									{!! Form::select('lkp_load_type_id', (['11'=>'Load Type (Any)'] + $loadtypemasters), null, ['class' => 'selectpicker bs-select-hidden','id' => 'term_load_type','onChange'=>'return GetCapacity()']) !!}
								</div>
							</div>
							<div class="col-md-3 form-control-fld">
								<div class="input-prepend normal-select">
									<span class="add-on"><i class="fa fa-archive"></i></span>
									{!! Form::select('lkp_packaging_type_id', (['' => 'Packaging Type*'] + $packagingtypesmasters_term), null, ['class' => 'selectpicker bs-select-hidden','id' => 'term_package_type']) !!}
								</div>
							</div>
							@endif



							@if(Session::get('service_id') == AIR_INTERNATIONAL || Session::get('service_id') == OCEAN)


								<div class="col-md-3 form-control-fld">
									<div class="input-prepend normal-select">
										<span class="add-on"><i class="fa fa-archive"></i></span>
										{!! Form::select('lkp_air_ocean_shipment_type_id', (['' => 'Shipment Type*'] + $shipmenttypes), null, ['class' => 'selectpicker bs-select-hidden','id' => 'lkp_air_ocean_shipment_type_id','onChange'=>'return GetCapacity()']) !!}
									</div>
								</div>
								<div class="col-md-3 form-control-fld">
									<div class="input-prepend normal-select">
										<span class="add-on"><i class="fa fa-archive"></i></span>
										{!! Form::select('lkp_air_ocean_sender_identity_id', (['' => 'Sender Identity*'] + $senderidentity), null, ['class' => 'selectpicker bs-select-hidden','id' => 'lkp_air_ocean_sender_identity_id']) !!}
									</div>
								</div>


							@endif
							<div class="submit_container">
								<div class="col-md-4 col-md-offset-4">
									<!--button class="btn theme-btn btn-block">Get Quote</button-->
									<input type="submit" id ='buyersearchresults' value="&nbsp; Search &nbsp;" class="btn theme-btn btn-block">
								</div>
							</div>

							{!! Form::close() !!}

						</div>
					</div>
				</div>
			</div>
		</div>

	</div>
</div>

<!-- footer -->
@include('partials.footer')
</div>
</div>
@endsection
