@inject('buyerCommonComponent', 'App\Components\BuyerComponent')
@inject('commonComponent', 'App\Components\CommonComponent')

@extends('app')
@section('content') 
<!-- Page top navigation Starts Here-->
@include('partials.page_top_navigation')

<div class="main">
	<div class="container">

	<h1 class="page-title">Search Results (Haul)</h1>
	<a class="change-service" data-toggle="modal" data-target="#change-service">Change Service</a>
	{!! Form::open(['url' =>'#','id' => 'buyer_results_form','class'=>'filter_form','method'=>'get']) !!}				
	
		<div class="search-block inner-block-bg">
			<div class="from-to-area">
				<span class="search-result">
					<i class="fa fa-map-marker"></i>
					<span class="location-text">
						{!! Form::hidden('from_location_id', $from_location_id) !!}
						{!! Form::hidden('to_location_id', $to_location_id) !!}
                        {!! Form::hidden('from_location', $from_location) !!}
                        {!! Form::hidden('to_location', $to_location) !!}
                        {!! Form::hidden('dispatch_flexible_hidden', session('searchMod.fdispatch_date_buyer')) !!}
						{{$from_location}} to {{$to_location}}
					</span>
                    {!! Form::hidden('is_commercial', session('searchMod.is_commercial_date_buyer')) !!}
				</span>
			</div>

			<div class="date-area">
				<div class="col-md-6 padding-none">
					<p class="search-head">Reporting Date</p>
					<span class="search-result">
						<i class="fa fa-calendar-o"></i>
						{!! Form::hidden('from_date', session('searchMod.dispatch_date_buyer'), ['id' => 'from_date','class' => 'form-control']) !!}
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

		</div> 
		<!-- Search block -->

		<h2 class="side-head pull-left">Filter Results</h2>
		<!--button class="btn post-btn pull-right">Post & get Quote</button-->
		@include("partials.content_top_navigation_links")

				<div class="clearfix"></div>

				<div class="col-md-12 padding-none">
					<div class="main-inner">

						<!-- Left Section Starts Here -->
						<div class="main-left">

                            @if( (session()->has('show_layered_filter') && session('show_layered_filter') !="") || !request()->exists('is_search') )
								@include("partials.filter._price")
                            @endif
							
							<?php
							$selectedPayment = request()->exists('selected_payments')? request('selected_payments'): array();
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
	                                    </span>
	                               	</div>
									@endforeach
								</div>
								@endif
							@endif
                                                        
							<h2 class="filter-head">Tracking</h2>
							<div class="tracking inner-block-bg">
								<div class="check-box">
									<input type="checkbox" name="tracking" value="1" onClick="this.form.submit()" <?php if(isset($_REQUEST['tracking']) && $_REQUEST['tracking']!="") { echo "checked='checked'"; } ?>  ><span class="lbl padding-8">{{TRACKING_MILE_STONE}}</span>
								</div>
								<div class="check-box"><input type="checkbox" name="tracking1" value="2" onClick="this.form.submit()" <?php if(isset($_REQUEST['tracking1']) && $_REQUEST['tracking1']!="") { echo "checked='checked'"; } ?>><span class="lbl padding-8">{{TRACKING_REAL_TIME}}</span>
								</div>
							</div>


							<div class="tracking inner-block-bg">
								<div class="check-box"><input type="checkbox" name="ftltopseller_orders"><span class="lbl padding-8">Top Sellers (Orders) </span></div>
								<div class="check-box"><input type="checkbox" name="ftltopseller_rated"><span class="lbl padding-8">Top Sellers (Rated) </span></div>
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

@include('partials.footer')

<div class="modal fade" id="modify-search" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
	        <button type="button" class="close" data-dismiss="modal">&times;</button>
	        
	        {!! Form::open(['url' =>'byersearchresults','id' => 'buyer_search_form_modify' , 'autocomplete'=>'off','method'=>'get']) !!}
	        
	        <div class="modal-body">
	          	<div class="col-md-12 padding-none">

		          	@include('buyer.truckhaul.search._form')
		          	
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

@endsection