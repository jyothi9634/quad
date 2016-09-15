@extends('app') 
@section('content')
@inject('commonComponent', 'App\Components\CommonComponent')
<!-- Page top navigation Starts Here-->
@include('partials.page_top_navigation')
<!-- Page top navigation Ends Here-->
<div class="clearfix"></div>
	<div class="main">

		<div class="container">
			<h1 class="page-title">Search Results (Haul)</h1><a class="change-service" data-toggle="modal" data-target="#change-service">Change Service</a>

			<!-- Search Block Starts Here -->
			<div class="search-block inner-block-bg">
				<div class="from-to-area">
					<span class="search-result">
						<i class="fa fa-map-marker"></i>
						<span class="location-text">{{ $from_location }} to  {{ $to_location }}</span>
					</span>
				</div>
				<div class="date-area">
					<div class="col-md-6 padding-none">
						<p class="search-head">Reporting Date</p>
						<span class="search-result">
							<i class="fa fa-calendar-o"></i>
							{{ $dispatch_date }}
						</span>
					</div>						
				</div>
				<div>
					<p class="search-head">Load Type</p>
						@if(isset($load_type_name_results) && $load_type_name_results !='')
						<span class="search-result">{{ $load_type_name_results }}</span>
						@elseif(isset($load_type_name) && $load_type_name !='')
						<span class="search-result">{{ $load_type_name }}</span>
						@endif
				</div>
				<div>
					<p class="search-head">Quantity</p>
					@if(isset($qty) && $qty !='')
					<span class="search-result">{{ $qty }} {{ $capacity }}</span>
					@else
					<span class="search-result">NA</span>
					@endif
				</div>
				<div>
					<p class="search-head">Vehicle Type</p>
					@if(isset($vehicle_type_name_results) && $vehicle_type_name_results !='')
					<span class="search-result">{{ $vehicle_type_name_results }}</span>
					@elseif(isset($vehicle_type_name) && $vehicle_type_name !='')
					<span class="search-result">{{ $vehicle_type_name }}</span>
					@endif
				</div>
				<div class="search-modify" data-toggle="modal" data-target="#modify-search">
					<span>Modify Search +</span>
				</div>
			</div>
			<!-- Search Block Ends Here -->

			<h2 class="side-head pull-left">Filter Results</h2>
			<a href="createseller"><button class="btn post-btn pull-right">+ Post</button></a>

			<div class="clearfix"></div>

				<div class="col-md-12 padding-none">
					<div class="main-inner">

						<!-- Left Section Starts Here -->
						<div class="main-left">
							<h2 class="filter-head">Search Filter</h2>
							<div class="inner-block-bg">
								<div class="gray_bg">

									{!! Form::open(['url' => 'buyersearchresults','id'=>'seller_posts_buyers_search_filter','method'=>'get','class'=>'filter_form']) !!}
								
									<div class="col-xs-12 padding-none margin-bottom  displayNone">
										{!! Form::text('from_location', $from_location, ['class' => 'form-control form-control1', 'readonly' => true]) !!}
										{!! Form::hidden('from_city_id', $from_city_id, array('id' => 'from_city_id')) !!}
										{!! Form::hidden('seller_district_id', $seller_district_id, array('id' => 'seller_district_id')) !!}
										{!! Form::hidden('qty', $qty, array('id' => 'qty123')) !!}
										{!! Form::hidden('capacity', $capacity, array('id' => 'capacity')) !!}
									</div>
									<div class="col-xs-12 padding-none margin-bottom displayNone">			{!! Form::text('to_location', $to_location, ['class' => 'form-control form-control1', 'readonly' => true]) !!}
										{!! Form::hidden('to_city_id', $to_city_id, array('id' => 'to_city_id')) !!}
									</div>

									<div class="clearfix"></div>
							
							<div class="col-xs-12 padding-none margin-bottom">
							@if(isset($load_type_name) && $load_type_name !='')
								{!! Form::text('load_type_name', $load_type_name, ['class' => 'form-control form-control1 ', 'readonly' => true]) !!}
							@else
								{{--*/ $filter->field('bqi.lkp_load_type_id') /*--}} 
                                                                
                            <?php $selectedLoads = isset($_REQUEST['selected_load_type_id']) ? $_REQUEST['selected_load_type_id'] : ''; ?>

	                            @if (session()->has('show_layered_filter'))
									@if (session()->has('load_type_filter'))
										<div class="normal-select">
		                                    <select class="selectpicker form-control"  placeholder="Load Type" type="select" id="lkp_load_type_id" name="lkp_load_type_id">
		                                        
		                                        @foreach (session('load_type_filter') as $loadid => $loadtype)
													<?php $selected=($loadid== $selectedLoads) ? 'selected="selected"' : ''; ?>
													<option value="{{$loadid}}" {{ $selected }}>{{$loadtype}}</option>

		                                        @endforeach
		                                    </select>
		                                </div>
									@endif
	                            @endif

								@if($_REQUEST['lkp_load_type_id'] != '')
								{!! Form::hidden('lkp_load_type_ids', $_REQUEST['lkp_load_type_id'], array('id' => 'lkp_load_type_ids')) !!}
								@elseif(isset($_REQUEST['lkp_load_type_ids']) && $_REQUEST['lkp_load_type_ids'] != '')
								{!! Form::hidden('lkp_load_type_ids', $_REQUEST['lkp_load_type_ids'], array('id' => 'lkp_load_type_ids')) !!}
								@endif
							@endif
							</div>
							<div class="col-xs-12 padding-none margin-bottom ">							
							@if(isset($vehicle_type_name) && $vehicle_type_name !='')
								{!! Form::text('vehicle_type_name', $vehicle_type_name, ['class' => 'form-control form-control1', 'readonly' => true]) !!}
							@else

								<?php $selectedVehicles = isset($_REQUEST['selected_vehicle_type_id']) ? $_REQUEST['selected_vehicle_type_id'] : ''; ?>
                                                    @if (session()->has('show_layered_filter'))
									@if (session()->has('vehicle_type_filter'))
										<div class="normal-select">
		                                    <select class="selectpicker form-control"  placeholder="Vehicle Type" type="select" id="lkp_vehicle_type_id" name="lkp_vehicle_type_id">
			                                @foreach (session('vehicle_type_filter') as $vehicleid => $vehicletype)
			                                        <?php $selectedv=($vehicleid== $selectedVehicles) ? 'selected="selected"' : ''; ?>
			                                        <option value="{{$vehicleid}}" {{ $selectedv }}>{{$vehicletype}}</option>

			                                @endforeach
		                                    </select>
										</div>
									@endif
                                @endif
						
								@if($_REQUEST['lkp_vehicle_type_id'] != '')
								{!! Form::hidden('lkp_vehicle_type_ids', $_REQUEST['lkp_vehicle_type_id'], array('id' => 'lkp_vehicle_type_ids')) !!}
								@elseif(isset($_REQUEST['lkp_vehicle_type_ids']) && $_REQUEST['lkp_vehicle_type_ids'] != '')
								{!! Form::hidden('lkp_vehicle_type_ids', $_REQUEST['lkp_vehicle_type_ids'], array('id' => 'lkp_vehicle_type_ids')) !!}
								@endif
							@endif
							</div>

							<div class="col-xs-12 padding-none margin-bottom displayNone">
								<div class="form-group">
									{!! Form::text('dispatch_date', $dispatch_date, ['id' => 'dispatch_filter_calendar','class' => 'calendar-icon form-control form-control1', 'placeholder' => 'Reporting Date', 'readonly'=>'true']) !!}
								</div>
							</div>
							
							<div class="clearfix"></div>
							<input type="hidden" name="selected_users" id="selected_users" value="<?php echo isset($_REQUEST['selected_users']) ? $_REQUEST['selected_users'] : ""; ?>"/>
							<input type="hidden" name="selected_prices" id="selected_prices" value="<?php echo isset($_REQUEST['selected_prices']) ? $_REQUEST['selected_prices'] : ""; ?>"/>
							<input type="hidden" name="selected_from_date" id="selected_from_date" value="<?php echo isset($_REQUEST['selected_from_date']) ? $_REQUEST['selected_from_date'] : ""; ?>"/>
							<input type="hidden" name="selected_to_date" id="selected_to_date" value=""/>
							<input type="hidden" name="selected_flexible_dispatch" id="selected_flexible_dispatch" value="<?php echo isset($_REQUEST['selected_flexible_dispatch']) ? $_REQUEST['selected_flexible_dispatch'] : ""; ?>"/>
							<input type="hidden" name="selected_flexible_delivery" id="selected_flexible_delivery" value=""/>
							
							<input type="hidden" name="lkp_vehicle_type_id" id="lkp_vehicle_type_id" value="<?php echo isset($_REQUEST['lkp_vehicle_type_id']) ? $_REQUEST['lkp_vehicle_type_id'] : ""; ?>"/>
							<input type="hidden" name="lkp_load_type_id" id="lkp_load_type_id" value="<?php echo isset($_REQUEST['lkp_load_type_id']) ? $_REQUEST['lkp_load_type_id'] : ""; ?>"/>
							<input type="hidden" name="selected_vehicle_type_id" id="selected_vehicle_type_id" value="<?php echo isset($_REQUEST['selected_vehicle_type_id']) ? $_REQUEST['selected_vehicle_type_id'] : ""; ?>"/>
							<input type="hidden" name="selected_load_type_id" id="selected_load_type_id" value="<?php echo isset($_REQUEST['selected_load_type_id']) ? $_REQUEST['selected_load_type_id'] : ""; ?>"/>

						{!! Form::close() !!}
						</div></div>
							
						<?php
							$selectedPrices = isset($_REQUEST['selected_prices']) ? explode(",",$_REQUEST['selected_prices']) : array();
						?>
						@if (session()->has('show_layered_filter'))
							@if (session()->has('price_filter'))
							<h2 class="filter-head">Pricing</h2>
								<div class="payment-mode inner-block-bg">
								<div class="layered_nav  margin-top col-xs-12 padding-none">
									@foreach (session('price_filter') as $priceid => $pricetype)
										<?php $selected = in_array($priceid, $selectedPrices) ? 'checked="checked"' : ""; ?>
										<div class="check-box">
										<input type="checkbox" class="checkbox pull-left filtercheckbox" value="{{$priceid}}" {{$selected}} name="search_by_price"/><span class="lbl padding-8">
										@if($pricetype == 1)
										Competitive
										@else
										Firm
										@endif</span>
										</div>
									@endforeach
								</div>
								</div>
							@endif
						@endif
							

							
						<?php
							$selectedSellers = isset($_REQUEST['selected_users']) ? explode(",",$_REQUEST['selected_users']) : array();
						?>

						@if (session()->has('show_layered_filter'))
							@if (session()->has('layered_filter'))
							<h2 class="filter-head"><?php echo (Auth::user()->lkp_role_id == 1) ? "Sellers" : "Buyers"; ?> List</h2>
							<div class="seller-list inner-block-bg">
				
								<div class="layered_nav  margin-top col-xs-12 padding-none">
									@foreach (session('layered_filter') as $userId => $userName)
										<?php $selected = in_array($userId, $selectedSellers) ? 'checked="checked"' : ""; ?>
										<div class="check-box">
										<input type="checkbox" class="checkbox pull-left filtercheckbox" value="{{$userId}}" {{$selected}} name="search_by_user"/><span class="lbl padding-8">{{ $userName }}</span>
										</div>
									@endforeach
								</div>
								</div>
							@endif
						@endif
							
							<?php
								session()->forget('show_layered_filter');
							?>
							<?php if((isset($_REQUEST['dispatch_date']) && ($_REQUEST['dispatch_date']!="")) || (isset($_REQUEST['selected_flexible_dispatch']) && ($_REQUEST['selected_flexible_dispatch']!="")) ) { ?>

							<h2 class="filter-head">Preferred Dispatch Date</h2>
							<div class="seller-list inner-block-bg">
								
								 <?php
								
								$flexdate = (isset($_REQUEST['dispatch_date']) && !empty($_REQUEST['dispatch_date'])) ? $_REQUEST['dispatch_date'] : (isset($_REQUEST['selected_flexible_dispatch']) ? $_REQUEST['selected_flexible_dispatch'] : "");
								
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
										if(isset($_REQUEST['selected_flexible_dispatch'])){
											
											if(($_REQUEST['selected_flexible_dispatch'] == $date1->format('Y-m-d'))){
											
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

											echo "<div class='check-box'><input id ='date_flexiable_$i' class='checkbox pull-left filtercheckbox' name ='date_flexiable' onChange='this.form.submit()' ".$selected." type='radio' value='".$date1->format('Y-m-d')."' /><label for='date_flexiable_$i'><span></span>".$date1->format('d-m-Y')."</label></div>";
										}
									}
								 ?>
								
							</div>	
							<?php } ?>

							<?php session()->forget('show_layered_filter'); ?>
						</div>
						<!-- Left Section Ends Here -->


						<!-- Right Section Starts Here -->
						<div class="main-right">
							{!! $grid !!}
						</div>
						<!-- Right Section Ends Here -->

					</div>
				</div>
				
				<div class="clearfix"></div>
				<a href="createseller"><button class="btn post-btn pull-right">+ Post</button></a>
			</div>
			
		</div>

	<!-- Left Nav Starts Here -->
	@include('partials.footer')
	<!-- Left Nav Ends Here -->
</div>

<div class="modal fade" id="modify-search" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<button type="button" class="close" data-dismiss="modal">&times;</button>
			<div class="modal-body">
			  	<div class="col-md-12 modal-form">
					<div class="home-search-modfy">
						@include('seller.truckhaul.search._form')
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection