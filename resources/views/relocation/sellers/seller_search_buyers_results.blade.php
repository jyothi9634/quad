@extends('app')
@section('content')
@inject('commonComponent', 'App\Components\CommonComponent')
<!-- Page top navigation Starts Here-->
@include('partials.page_top_navigation')


		<div class="clearfix"></div>

		<div class="main">

			<div class="container">
				<h1 class="page-title">Search Results (Relocation)</h1><a class="change-service" data-toggle="modal" data-target="#change-service">Change Service</a>
				
				<!-- Search Block Starts Here -->

				<div class="search-block inner-block-bg">
					<div class="from-to-area">
						<span class="search-result">
							<i class="fa fa-map-marker"></i>
							<span class="location-text">{{$request['from_location']}} to {{$request['to_location']}}</span>
						</span>
					</div>
					<div class="date-area">
						<div class="col-md-6 padding-none">
							<p class="search-head">From Date</p>
							<span class="search-result">
								<i class="fa fa-calendar-o"></i>
								{{$request['valid_from']}}
							</span>
						</div>
						<div class="col-md-6 padding-none">
							<p class="search-head">To Date</p>
							<span class="search-result">
								<i class="fa fa-calendar-o"></i>								
                                                                @if(isset($request['valid_to']) && $request['valid_to']!='')
								{{ $request['valid_to'] }}
                                                                @else
                                                                NA
                                                                @endif
							</span>
						</div>
					</div>
					<div>
						<p class="search-head">Post For</p>
						<span class="search-result">@if($request['post_type']==1)
						HHG
						@else
						Vehicle
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
				<a onclick="return checkSession(15,'/relocation/createsellerpost');" href="#"><button class="btn post-btn pull-right">+ Post</button></a>

				<div class="clearfix"></div>
				
				<div class="col-md-12 padding-none">
					<div class="main-inner"> 
						
						<!-- Left Section Starts Here -->

						<div class="main-left">
							
						{!! Form::open(['url' =>'#','id' => 'buyer_results_form','class'=>'filter_form','method'=>'get']) !!}
						{!! Form::hidden('from_location_id', $from_location_id) !!}
						{!! Form::hidden('to_location_id', $to_location_id) !!}
						{!! Form::hidden('from_location', $from_location) !!}
						{!! Form::hidden('to_location', $to_location) !!}
						{!! Form::hidden('valid_from', $valid_from) !!}
						{!! Form::hidden('valid_to', $valid_to) !!}
						{!! Form::hidden('post_type', $post_type) !!}
							<h2 class="filter-head">Form Filter</h2>
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
								{!! Form::text('from_location', $from_location , ['id' => '','class' => 'form-control', 'placeholder' => 'From Location*','readonly'=>'true']) !!}
	                            
									</div>
								</div>
								<div class="form-control-fld">
									<div class="input-prepend">
										<span class="add-on"><i class="fa fa-map-marker"></i></span>
								{!! Form::text('to_location', $to_location,  ['id' => '','class' => 'form-control', 'placeholder' => 'To Location*','readonly'=>'true']) !!}
	                            
									</div>
								</div>
								<div class="form-control-fld">
									<div class="input-prepend">
										<span class="add-on"><i class="fa fa-calendar-o"></i></span>
										<input type="text" class="form-control calendar" placeholder="Dispatch Date" value="{{ $valid_from }}" onChange="this.form.submit()" readonly />
									</div>
								</div>
								<div class="form-control-fld">
									<div class="input-prepend">
										<span class="add-on"><i class="fa fa-calendar-o"></i></span>
										<input type="text" class="form-control calendar" placeholder="Delivery Date" value="{{ $valid_to }}" onChange="this.form.submit()" readonly />
									</div>
								</div>
								
								{!!	Form::hidden('is_search',1,array('class'=>'form-control')) !!}
								@if($request['post_type']==1)
								<?php $load_type = isset($_REQUEST['load_type']); ?>
								@if (Session::has('show_layered_filter')&& Session::get('show_layered_filter')!="")	
								@if (Session::has('layered_filter_loadtype')&& Session::get('layered_filter_loadtype')!="")								
								<div class="form-control-fld margin-top">
									<div class="input-prepend">
										<span class="add-on"><i class="fa fa-archive"></i></span>
										<select class="selectpicker" name="load_type" onChange="this.form.submit()" >
										<option value="">Load Type</option>
										@foreach (Session::get('layered_filter_loadtype') as $loadtypeId => $loadtypeName)										
											<option <?php if (isset ( $_REQUEST ['load_type'] ) && $_REQUEST ['load_type'] == $loadtypeId) { ?> selected="selected" value="{{$loadtypeId}}" <?php } else { ?>value="{{$loadtypeId}}"<?php } ?>   > {{ $loadtypeName }}</option>
										@endforeach
										</select>
									</div>
								</div>		
								@endif
							@endif	
							
								<?php $propertType = isset($_REQUEST['property_type'])  ?>
								@if (Session::has('show_layered_filter')&& Session::get('show_layered_filter')!="")	
								@if (Session::has('layered_filter_propertytype')&& Session::get('layered_filter_propertytype')!="")								
								<div class="form-control-fld margin-top">
									<div class="input-prepend">
										<span class="add-on"><i class="fa fa-archive"></i></span>
										<select class="selectpicker" name="property_type" onChange="this.form.submit()" >
										<option value="">Property Type</option>
										@foreach (Session::get('layered_filter_propertytype') as $propId => $propName)										
											<option <?php if (isset ( $_REQUEST ['property_type'] ) && $_REQUEST ['property_type'] == $propId) { ?> selected="selected" value="{{$propId}}" <?php } else { ?>value="{{$propId}}"<?php } ?> > {{ $propName }}</option>
										@endforeach
										</select>
									</div>
								</div>		
								@endif
							@endif	
						@endif
						
						<?php	$selectedSellers = isset($_REQUEST['selected_users']) ? $_REQUEST['selected_users'] : array();
								?>
								
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
						
							</div>				
							
						{!! Form::close() !!}
							
						</div>

						<!-- Left Section Ends Here -->


						<!-- Right Section Starts Here -->

						<div class="main-right">
							<!-- Table Starts Here -->
							<div class="table-div">	
							<input type="hidden" id="from_search_page" name="from_search_page" value="1">							
								{!! $gridBuyer !!}
							</div>	
						</div>

						<!-- Right Section Ends Here -->

					</div>
				</div>

				<div class="clearfix"></div>
				<div class="clearfix"></div>
				<a onclick="return checkSession(15,'/relocation/createsellerpost');" href="#"><button class="btn post-btn pull-right">+ Post</button></a>
			</div>
		</div>

@include('partials.footer')
<div class="modal fade" id="modify-search" role="dialog">
	    <div class="modal-dialog">
	    @if(Session::get('session_post_type_relocation') && Session::get('session_post_type_relocation') == '1') 
			{{--*/ $hhg = 'selected' /*--}}
		@else
			{{--*/ $hhg = '' /*--}}
		@endif
		
		@if(Session::get('session_post_type_relocation') && Session::get('session_post_type_relocation') == '2') 
			{{--*/ $vehicle = 'selected' /*--}}
		@else
			{{--*/ $vehicle = '' /*--}}
		@endif
	      <!-- Modal content-->
	      <div class="modal-content">
	        <button type="button" class="close" data-dismiss="modal">&times;</button>
	        <div class="modal-body">
	          <div class="col-md-12 modal-form">
				<div class="col-md-12 padding-none">
					
					
				<div class="col-md-3 padding-none text-center">
				<div class="col-md-12 form-control-fld">

					<div class="radio-block">
						<div class="radio_inline"><input type="radio" name="lead_type" id="spot_lead_type" value="1" checked="checked" /><label for="spot_lead_type"><span></span>Spot</label></div>
						<div class="radio_inline"><input type="radio" name="lead_type" id="term_lead_type" value="2" /><label for="term_lead_type"> <span></span>Term</label></div>
					</div>

				</div>
			</div>
		</div>
		<div class="clearfix"></div>

		{{-- Seller/Home/Transportation/FTL/Search/Spot  --}}
		<div class="showhide_spot" id="showhide_spot">

			{!! Form::open(['url' => 'buyersearchresults','id'=>'relocation_domestic_sellersearch_buyers','method'=>'get']) !!}

				<div class="home-search-modfy">
					<div class="col-md-12 padding-none">
						<div class="col-md-4 form-control-fld">
							<div class="input-prepend">
								<span class="add-on"><i class="fa fa-map-marker"></i></span>
								{!! Form::text('from_location', Session::get('session_from_location_relocation') , ['id' => 'from_location','class' => 'form-control', 'placeholder' => 'From Location*']) !!}
                                {!! Form::hidden('from_location_id', Session::get('session_from_location_id_relocation'), array('id' => 'from_location_id')) !!}
                                {!!	Form::hidden('spot_or_term',1,array('class'=>'form-control')) !!}
                                {!!	Form::hidden('is_search',1,array('class'=>'form-control')) !!}
                                    
							</div>
						</div>
						<div class="col-md-4 form-control-fld">
							<div class="input-prepend">
								<span class="add-on"><i class="fa fa-map-marker"></i></span>
								{!! Form::text('to_location', Session::get('session_to_location_relocation'),  ['id' => 'to_location','class' => 'form-control', 'placeholder' => 'To Location*']) !!}
                                {!! Form::hidden('to_location_id', Session::get('session_to_location_id_relocation') , array('id' => 'to_location_id')) !!}
							</div>
						</div>
	
						<div class="col-md-4 form-control-fld">
							<div class="input-prepend">
								<span class="add-on"><i class="fa fa-calendar-o"></i></span>
								{!! Form::text('valid_from', Session::get('session_valid_from_relocation'),  ['id' => 'datepicker_search','class' => 'calendar form-control from-date-control','readonly' => true, 'placeholder' => 'Dispatch Date*']) !!}
								<input type="hidden" name="dispatch_flexible_hidden" id="dispatch_flexible_hidden" value="<?php echo isset($_REQUEST['dispatch_flexible_hidden']) ? $_REQUEST['dispatch_flexible_hidden'] : ""; ?>">
							</div>
						</div>
						<div class="col-md-4 form-control-fld">
							<div class="input-prepend">
								<span class="add-on"><i class="fa fa-calendar-o"></i></span>
								{!! Form::text('valid_to', Session::get('session_valid_to_relocation'), ['id' => 'datepicker_to_location_search','class' => 'calendar form-control to-date-control','readonly' => true, 'placeholder' => 'Delivery Date']) !!}
								<input type="hidden" name="delivery_flexible_hidden" id="delivery_flexible_hidden" value="<?php echo isset($_REQUEST['delivery_flexible_hidden']) ? $_REQUEST['delivery_flexible_hidden'] : ""; ?>">
							</div>
						</div>
						<div class="col-md-4 form-control-fld">
							<div class="input-prepend">
								<span class="add-on"><i class="fa fa-archive"></i></span>								
								<select class="selectpicker" id="post_type" name="post_type">
									<option value="">Post Type</option>
									<option value="1" {{ $hhg }}>HHG</option>
									<option value="2" {{ $vehicle }}>Vehicle</option>
								</select>								
							</div>
						</div>
					</div>
					<div class="col-md-4 col-md-offset-4">
						<button class="btn theme-btn btn-block">Search</button>
					</div>					
				</div>

    {!! Form::close() !!}
		</div>

		{{-- Seller/Home/Transportation/FTL/Search/Term  --}}
		<div class="showhide_term" style="display:none" id="showhide_term">
			{!! Form::open(['url' =>'termsellersearchresults','method'=>'GET','id'=>'sellers-search-buyers']) !!}
			<div class="col-md-12 padding-none">
				<div class="home-search-form">

					<div class="clearfix"></div>
					<div class="col-md-12 padding-none">
						<div class="col-md-12 form-control-fld">
							<div class="radio-block">

								<input type="radio" id="term_post_rate_card_type_1" name="term_post_rate_card_type" class="termratetype_selection_buyer" value="1" checked>
								<label for="term_post_rate_card_type_1"><span></span>HHG</label>

								<input type="radio" id="term_post_rate_card_type_2" name="term_post_rate_card_type" class="termratetype_selection_buyer" value="2">
								<label for="term_post_rate_card_type_2"><span></span>Vehicle</label>
							</div>
						</div>
						<div class="col-md-4 form-control-fld">
							<div class="input-prepend">
								<span class="add-on"><i class="fa fa-map-marker"></i></span>
								<!--input class="form-control" id="" type="text" placeholder="From Location"-->
								{!! Form::text('term_from_location', '', ['id' => 'term_from_location','class' => 'top-text-fld form-control', 'placeholder' => 'From Location*']) !!}
								{!! Form::hidden('term_from_city_id', '', array('id' => 'term_from_location_id')) !!}
								{!! Form::hidden('seller_district_id', '', array('id' => 'seller_district_id')) !!}
								{!!	Form::hidden('is_search',1,array('class'=>'form-control')) !!}
								{!!	Form::hidden('spot_or_term',2,array('class'=>'form-control spot_or_term')) !!}
							</div>
						</div>
						<div class="col-md-4 form-control-fld">
							<div class="input-prepend">
								<span class="add-on"><i class="fa fa-map-marker"></i></span>
								<!--input class="form-control" id="" type="text" placeholder="To Location"-->
								{!! Form::text('term_to_location', '', ['id' => 'term_to_location','class' => 'top-text-fld form-control', 'placeholder' => 'To Location*']) !!}
								{!! Form::hidden('term_to_city_id', '', array('id' => 'term_to_location_id')) !!}
							</div>
						</div>

					</div>
				</div>
			</div>


			<div class="submit_container">
				<div class="col-md-4 col-md-offset-4">
					<!--button class="btn theme-btn btn-block">Get Quote</button-->
					<input type="submit" id ='buyersearchresults' value="&nbsp; Search &nbsp;" class="btn theme-btn btn-block">
				</div>
			</div>
			<!--<div class="col-md-12 col-sm-12 col-xs-12 padding-top"><a class="pull-right" href="#">Helpdesk</a></div>-->
			{!! Form::close() !!}
		</div>
						<div class="clearfix"></div>	
					
					
				

				</div>
			</div>
	        </div>
	      </div>
	      
	    </div>
	  </div>
	  @endsection