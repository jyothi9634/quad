@extends('app')
@section('content')
@inject('commonComponent', 'App\Components\CommonComponent')
<!-- Page top navigation Starts Here-->
@include('partials.page_top_navigation')

		<div class="clearfix"></div>

		<div class="main">

			<div class="container">
			
			<h1 class="page-title">Search Results (Relocation Office)</h1><a href="#" class="change-service" data-toggle="modal" data-target="#change-service">Change Service</a>
				
				<!-- Search Block Starts Here -->
				<div class="search-block inner-block-bg">
					<div class="from-to-area">
						<span class="search-result">
							<i class="fa fa-map-marker"></i>
							<span class="location-text">{{ $request['from_location'] }}</span>
						</span>
					</div>
					<div class="date-area">
						<div class="col-md-6 padding-none">
							<p class="search-head">Dispatch Date</p>
							<span class="search-result">
								<i class="fa fa-calendar-o"></i>
								{{ $request['from_date'] }}
							</span>
						</div>
						<div class="col-md-6 padding-none">
							<p class="search-head">Delivery Date</p>
							<span class="search-result">
								<i class="fa fa-calendar-o"></i>
								@if(isset($request['to_date']) && $request['to_date']!='')
									{{ $request['to_date'] }}
								@else
									NA
								@endif
							</span>
						</div>
					</div>
					
					<div>
						<p class="search-head">Approximate Distance</p>
						<span class="search-result">
							@if(isset($request['distance']) && $request['distance']!='')
								{{ $request['distance'] }} KM
							@else
								NA
							@endif
						</span>
					</div>
					
					<div class="search-modify" data-toggle="modal" data-target="#modify-search">
						<span>Modify Search +</span>
					</div>
				</div>

				<!-- Search Block Ends Here -->



				<h2 class="side-head pull-left">Filter Results </h2>
				<div class="page-results pull-left col-md-2 padding-none">
					<div class="form-control-fld">
						<div class="normal-select">
							<select class="selectpicker">
								<option value="0">10 Records Per page</option>
							</select>
						</div>
					</div>
				</div>
				<a href="{{'/relocation/creatbuyerrpost?search=1'}}"><button class="btn post-btn pull-right">Post &amp; get Quote</button></a>

				<div class="clearfix"></div>
				
				<div class="col-md-12 padding-none">
					<div class="main-inner"> 
						
						<!-- Left Section Starts Here -->
					
						<div class="main-left">
						{!! Form::open(['url' =>'#','id' => 'buyer_results_form','class'=>'filter_form','method'=>'get']) !!}
								{!! Form::hidden('from_location_id', $from_location_id) !!}
                                {!! Form::hidden('from_location', $from_location) !!}
                                {!! Form::hidden('volume', $volume) !!}			
								@foreach($particulars as $particular)
									{!! Form::hidden('roomitems['.$particular->id.']',$request['roomitems'][$particular->id])  !!}
								@endforeach
								{!! Form::hidden('dispatch_flexible_hidden', $request['dispatch_flexible_hidden']) !!}	
								{!! Form::hidden('delivery_flexible_hidden', $request['delivery_flexible_hidden']) !!}	
                                <input type="hidden" name="filter_set" id="filter_set" value="1">
							<div class="seller-list inner-block-bg">
								<div class="form-control-fld margin-top">
									<div class="input-prepend">
										<span class="add-on"><i class="fa fa-map-marker"></i></span>
										<input type="text" class="form-control" placeholder="From Location" value="{{ $from_location }}" readonly/>
									</div>
								</div>		
								<div class="form-control-fld">
									<div class="input-prepend">
										<span class="add-on"><i class="fa fa-calendar-o"></i></span>
										<input type="text" class="form-control " name="from_date" placeholder="Dispatch Date" value="{{ $request['from_date'] }}" onChange="this.form.submit()" readonly/>
									</div>									
								</div>
								<div class="form-control-fld">
									<div class="input-prepend">
										<span class="add-on"><i class="fa fa-calendar-o"></i></span>
										<input type="text" class="form-control " placeholder="Delivery Date" name="to_date" value="{{ $request['to_date'] }}" onChange="this.form.submit()" readonly/>
									</div>									
								</div>								
								<div class="form-control-fld">
									<div class="input-prepend">
										<span class="add-on"><i class="fa fa-road"></i></span>

										@if(isset($request['distance']) && $request['distance']!='')
											<input type="text" class="form-control" placeholder="Appoximate Distance" name="distance" value="{{$request['distance']}}" readonly/>
										@else
											<input type="text" class="form-control" placeholder="Appoximate Distance" name="distance" value="" readonly/>
										@endif
										<span class="add-on unit1 manage">
											KM
										</span>
									</div>
								</div>		
							</div>

							


							<?php $selectedPayment = isset($_REQUEST['selected_payments']) ? $_REQUEST['selected_payments'] : array(); ?>
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
							

							@if ((Session::has('show_layered_filter')&& Session::get('show_layered_filter')!="") || (!isset($_REQUEST['is_search'])))
                            	@include("partials.filter._price")
							@endif


							<?php	$selectedSellers = isset($_REQUEST['selected_users']) ? $_REQUEST['selected_users'] : array();
								?>
								
								@if (Session::has('show_layered_filter')&& Session::get('show_layered_filter')!="")
									@if (Session::has('layered_filter')&& Session::get('layered_filter')!="")
										<h2 class="filter-head"><?php echo (Auth::user()->lkp_role_id == 1) ? "Sellers" : "Buyers"; ?> List</h2>
										<div class="seller-list inner-block-bg">
											@foreach (Session::get('layered_filter') as $userId => $userName)
												<?php $selected = in_array($userId, $selectedSellers) ? 'checked="checked"' : ""; ?>
												<div class="check-box"><input type="checkbox" class="checkbox pull-left filtercheckbox" value="{{$userId}}" {{$selected}} name="selected_users[]" onChange="this.form.submit()"><span class="lbl padding-8">{{ $userName }}</span></div>
												<div class="col-xs-12 padding-none"> </div>
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
											//if($_REQUEST['from_date'] == $date1->format('Y-m-d')){
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
         
							
						{!! Form::close() !!}	
						</div>
					
						<!-- Left Section Ends Here -->


						<!-- Right Section Starts Here -->

						<div class="main-right">
							<!-- Table Starts Here -->
							<div class="table-div">								
								{!! $gridBuyer !!}
							</div>	
						</div>

						<!-- Right Section Ends Here -->

					</div>
				</div>
				
		
     <div class="clearfix"></div>
				<div class="clearfix"></div>
			<a href="/relocation/creatbuyerrpost"><button class="btn post-btn pull-right">Post &amp; get Quote</button></a>	
			</div>
	</div>
		
@include('partials.footer')

	<!-- Modal -->
	  <div class="modal fade" id="modify-search" role="dialog">
	    <div class="modal-dialog">	    
		@if(Session::get('session_household_items') == 1)
			{{--*/ $household_selected = "checked" /*--}}
		@else
			{{--*/ $household_selected = "" /*--}}
		@endif
		@if(Session::get('session_household_items') == 2)
			{{--*/ $vechile_selected = "checked" /*--}}
		@else
			{{--*/ $vechile_selected = "" /*--}}
		@endif
	      <!-- Modal content-->
	      <div class="modal-content">
	        <button type="button" class="close" data-dismiss="modal">&times;</button>
	        <div class="modal-body">
	          <div class="col-md-12 modal-form">
				<div class="col-md-12 padding-none">
					
					{!! Form::open(['url' => 'byersearchresults','id'=>'relocation_domestic_office_buyersearch_sellers','method'=>'get']) !!}
					{!!	Form::hidden('is_search',1,array('class'=>'form-control')) !!}
					<div class="col-md-12 padding-none">
							<div class="col-md-4 form-control-fld">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-map-marker"></i></span>
									{!! Form::text('from_location', $request['from_location'] , ['id' => 'from_location','class' => 'form-control', 'placeholder' => 'City*']) !!}
	                                {!! Form::hidden('from_location_id', $request['from_location_id'] , array('id' => 'from_location_id')) !!}
								</div>
							</div>
	
							<div class="col-md-4 form-control-fld">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-calendar-o"></i></span>
									{!! Form::text('from_date', $request['from_date'],  ['id' => 'dispatch_date1','class' => 'calendar form-control from-date-control','readonly' => true, 'placeholder' => 'Pickup Date*']) !!}
									<input type="hidden" name="dispatch_flexible_hidden" id="dispatch_flexible_hidden" value="0">
								</div>
							</div>
							<div class="col-md-4 form-control-fld">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-calendar-o"></i></span>
									{!! Form::text('to_date', $request['to_date'] , ['id' => 'delivery_date1','class' => 'calendar form-control to-date-control','readonly' => true, 'placeholder' => 'Delivery Date']) !!}
									<input type="hidden" name="delivery_flexible_hidden" id="delivery_flexible_hidden" value="0">
								</div>
							</div>
							<div class="clearfix"></div>
							
							<div class="relocation_house_hold_buyer_create">
							<div class="col-md-4 form-control-fld">
									<div class="input-prepend">
										<span class="add-on"><i class="fa fa-road"></i></span>
										{!! Form::text('distance', $request['distance'], ['id' => 'distance','class' => 'form-control clsROMDistanceKM','placeholder' => 'Distance*']) !!}
										<span class="add-on unit1 manage">
											KM
										</span>
									</div>
							</div>
							{{-- <div class="col-md-12 form-control-fld text-right margin-none">
								<span class="red spl-link advanced-search-link"><span class="more-search">+</span><span class="less-search">-</span> Invetory Details</span>
							</div>	 --}}
							
							<div class="advanced-search-details-office">

								<div class="clearfix"></div>

								<!-- Table Starts Here -->

							<div class="table-div table-style1 inventory-block-officemove">
								<div class="table-div table-style1 inventory-table padding-none">
									
									<!-- Table Head Starts Here -->

									<div class="table-heading inner-block-bg">
										<div class="col-md-8 padding-left-none">&nbsp;</div>
										<div class="col-md-4 padding-left-none">No of Items</div>
									</div>

									<!-- Table Head Ends Here -->

									<div class="table-data">
									<!-- Table Row Starts Here -->
											@foreach($particulars as $particular)
													<div class="table-row inner-block-bg">
														<div class="col-md-6 padding-left-none">{{$particular->office_particular_type}}</div>
																								
														 <div class="col-md-6 padding-left-none">
															{!! Form::text('roomitems['.$particular->id.']',$request['roomitems'][$particular->id] , ['id' => 'roomitems_'.$particular->id,'maxlength'=>3,'class' => 'form-control form-control1 roomitems clsROMNoOfItems', 'onblur' => 'javascript:valuecheck(this.value,this.id)'])  !!}
														</div>
													</div>
											@endforeach
										<!-- Table Row Ends Here -->
									</div>
								</div>	
								</div>	
							</div>
							
							</div>
										
					</div>
					
				</div>
				<div class="col-md-4 col-md-offset-4">
						<input type="hidden" name="total_hidden_volume" id="total_hidden_volume" value="">
						<input  type="submit" class="btn theme-btn btn-block"  name="getquote" id="getquote" value="Search">
					</div>	
	{!! Form::close() !!}	




				</div>
			</div>
	        </div>
	      </div>
	      
	    </div>
	  </div>
@endsection