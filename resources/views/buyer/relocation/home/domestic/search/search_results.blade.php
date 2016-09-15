@extends('app')
@section('content')
@inject('commonComponent', 'App\Components\CommonComponent')
<!-- Page top navigation Starts Here-->
@include('partials.page_top_navigation')

		<div class="clearfix"></div>

		<div class="main">

			<div class="container">
			
			<h1 class="page-title">Search Results (Relocation)</h1><a href="#" class="change-service" data-toggle="modal" data-target="#change-service">Change Service</a>
				
				<!-- Search Block Starts Here -->

				<div class="search-block inner-block-bg">
					<div class="from-to-area">
						<span class="search-result">
							<i class="fa fa-map-marker"></i>
							<span class="location-text">{{ $request['from_location'] }} to {{ $request['to_location'] }}</span>
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
					@if(isset($household_items) && $household_items == 1)
					<div>
						<p class="search-head">Property Type</p>
						<span class="search-result">{{ $commonComponent->getPropertyType($request['property_type']) }}</span>
					</div>
					<div>
						<p class="search-head">CFT</p>
						{{--*/
							$searchvolume = (isset($request['total_hidden_volume']) && !empty($request['total_hidden_volume']) && $request['total_hidden_volume']!=1) ? $request['total_hidden_volume'] : $request['volume'];
						/*--}}
						<span class="search-result">{{ $searchvolume }}</span>
					</div>
					<div>
						<p class="search-head">Load Type</p>
						<span class="search-result">
						@if(isset($request['load_type']))
							@if($request['load_type']==1)
								Full Load
							@else
								Part Load
							@endif
						@else
							-
						@endif
						</span>
					</div>
					@else
					<div>
						<p class="search-head">Vehicle Type</p>
						<span class="search-result">{{ $commonComponent->getVehicleCategoryById($request['vehicle_category']) }}</span>
					</div>
					<div>
						<p class="search-head">Vehicle Model</p>
						<span class="search-result">{{ $vehicle_model }}</span>
					</div>
					<div>
						<p class="search-head">Category Type</p>
						<span class="search-result">
							@if($request['vehicle_category'] == 1)
								{{ $commonComponent->getVehicleCategorytypeById($request['vehicle_category_type']) }}
							@else
								N/A
							@endif	
						</span>
					</div>
					@endif
					
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
								{!! Form::hidden('filter_set', 1) !!}
								{!! Form::hidden('to_location_id', $to_location_id) !!}
                                {!! Form::hidden('from_location', $from_location) !!}
                                {!! Form::hidden('to_location', $to_location) !!}		
                                {!! Form::hidden('property_type', $property_type) !!}		
                                {!! Form::hidden('volume', $volume) !!}			
                                {!! Form::hidden('post_rate_card_type', $post_rate_card_type) !!}
                                {!! Form::hidden('load_type', $load_type) !!}		
                                {!! Form::hidden('household_items', $household_items) !!}	
                                {!! Form::hidden('vehicle_category', $vehicle_category) !!}	
                                {!! Form::hidden('vehicle_model', $vehicle_model) !!}	
                                {!! Form::hidden('vehicle_category_type', $vehicle_category_type) !!}
                                {!! Form::hidden('total_hidden_volume', $request['total_hidden_volume']) !!}
                                @if(Session::has('session_elevator1'))
                                	{!! Form::hidden('elevator1', Session::get('session_elevator1')) !!}
								@endif
								@if(Session::has('session_elevator2'))
                                	{!! Form::hidden('elevator2', Session::get('session_elevator2')) !!}
								@endif
                                <input type="hidden" name="filter_set" id="filter_set" value="1">
							<div class="seller-list inner-block-bg">
								<div class="form-control-fld margin-top">
									<div class="input-prepend">
										<span class="add-on"><i class="fa fa-archive"></i></span>
										<select class="selectpicker">
											<option>Enquiry Type (All)</option>
										</select>
									</div>
								</div>
								<div class="form-control-fld">
									<div class="input-prepend">
										<span class="add-on"><i class="fa fa-map-marker"></i></span>
										<input type="text" class="form-control" placeholder="From Location" value="{{ $from_location }}" readonly/>
									</div>
								</div>		
								<div class="form-control-fld">
									<div class="input-prepend">
										<span class="add-on"><i class="fa fa-map-marker"></i></span>
										<input type="text" class="form-control" placeholder="To Location" value="{{ $to_location }}" readonly/>
									</div>
								</div>					
								<div class="form-control-fld">
									<div class="input-prepend">
										<span class="add-on"><i class="fa fa-calendar-o"></i></span>
										<input type="text" class="form-control calendar" name="from_date" placeholder="Dispatch Date" value="{{ $request['from_date'] }}" onChange="this.form.submit()" readonly/>
									</div>									
								</div>
								<div class="form-control-fld">
									<div class="input-prepend">
										<span class="add-on"><i class="fa fa-calendar-o"></i></span>
										<input type="text" class="form-control calendar" placeholder="Delivery Date" name="to_date" value="{{ $request['to_date'] }}" onChange="this.form.submit()" readonly/>
									</div>									
								</div>								
							</div>

							


							{{--*/ $selectedPayment = isset($request['selected_payments']) ? $request['selected_payments'] : array(); /*--}}
							@if (Session::has('show_layered_filter')&& Session::get('show_layered_filter')!="")
								@if (Session::has('layered_filter_payments')&& Session::get('layered_filter_payments')!="")
									<h2 class="filter-head">Payment Mode</h2>
									<div class="payment-mode inner-block-bg">
										@if(Session::has('layered_filter_payments') && is_array(Session::get('layered_filter_payments')))
											@foreach (Session::get('layered_filter_payments') as $paymentId => $paymentName)
												{{--*/ $selected = in_array($paymentId, $selectedPayment) ? 'checked="checked"' : ""; /*--}}
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
										@endif
									</div>
								@endif
							@endif
							
                           @include("partials.filter._price")

							{{--*/ $selectedSellers = isset($_REQUEST['selected_users']) ? $_REQUEST['selected_users'] : array(); /*--}}
								@if (Session::has('show_layered_filter')&& Session::get('show_layered_filter')!="")
									@if (Session::has('layered_filter')&& Session::get('layered_filter')!="")
										<h2 class="filter-head"><?php echo (Auth::user()->lkp_role_id == 1) ? "Sellers" : "Buyers"; ?> List</h2>
										<div class="seller-list inner-block-bg">
											@if(Session::has('layered_filter') && is_array(Session::get('layered_filter')))
												@foreach (Session::get('layered_filter') as $userId => $userName)
													<?php $selected = in_array($userId, $selectedSellers) ? 'checked="checked"' : ""; ?>
													<div class="check-box"><input type="checkbox" class="checkbox pull-left filtercheckbox" value="{{$userId}}" {{$selected}} name="selected_users[]" onChange="this.form.submit()"><span class="lbl padding-8">{{ $userName }}</span></div>
													<div class="col-xs-12 padding-none"> </div>
												@endforeach
											@endif
										</div>
									@endif
								@endif

							                
							
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
			<a href="{{'/relocation/creatbuyerrpost?search=1'}}"><button class="btn post-btn pull-right">Post &amp; get Quote</button></a>
		</div>
	</div>
		
@include('partials.footer')

	<!-- Modal -->
	  <div class="modal fade" id="modify-search" role="dialog">
	    <div class="modal-dialog">	    
			<!-- Modal content-->
			<div class="modal-content">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<div class="modal-body">
					<div class="col-md-12 modal-form">
						<div class="col-md-12 padding-none">
							{!! Form::open(['url' => 'byersearchresults','id'=>'posts-form_buyer_relocation','method'=>'get']) !!}
								<div class="col-md-12 padding-none">
									@include('buyer.relocation.home.domestic.search._form') 
								</div>
								<div class="col-md-4 col-md-offset-4">
									{!! Form::submit('&nbsp; Search &nbsp;', ['name' => 'Search','class'=>'btn theme-btn btn-block','id' => 'Search']) !!}
								</div>									
							{!! Form::close() !!}
						</div>
					</div>
				</div>
			</div>
	      </div>
	      
	    </div>
	  </div>
@endsection