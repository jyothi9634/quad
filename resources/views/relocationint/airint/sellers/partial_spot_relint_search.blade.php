{!! Form::open(['url' => 'buyersearchresults','id'=>'relocation_international_sellersearch_buyers_spot','method'=>'get']) !!}
                        {!! Form::hidden('lead_type', '1', array('id' => 'post_type')) !!}
			<div class="home-search gray-bg margin-top-none padding-top-none border-top-none">
				<div class="home-search-form">
					<div class="clearfix"></div>
					<div class="col-md-12 padding-none">
						<div class="col-md-12 form-control-fld">
							<div class="radio-block">
								<input type="radio" checked="" name="service_type" id="spot_service_air" value="1">
								<label for="spot_service_air"><span></span>Air</label>
									
								<input type="radio" name="service_type" id="spot_service_ocean" value="2">
								<label for="spot_service_ocean"><span></span>Ocean</label>
							</div>
						</div>
						<div class="col-md-4 form-control-fld">
							<div class="input-prepend">
								<span class="add-on"><i class="fa fa-map-marker"></i></span>
								{!! Form::text('from_location', '' , ['id' => 'from_location','class' => 'form-control', 'placeholder' => 'From Location*']) !!}
								{!! Form::hidden('from_location_id', '' , array('id' => 'from_location_id')) !!}
								{!! Form::hidden('seller_district_id', '', array('id' => 'seller_district_id')) !!}
							</div>
						</div>
						<div class="col-md-4 form-control-fld">
							<div class="input-prepend">
								<span class="add-on"><i class="fa fa-map-marker"></i></span>
								{!! Form::text('to_location', '',  ['id' => 'to_location','class' => 'form-control', 'placeholder' => 'To Location*']) !!}
								{!! Form::hidden('to_location_id', '' , array('id' => 'to_location_id')) !!}
							</div>
						</div>

						<div class="col-md-4 form-control-fld">
							<div class="input-prepend">
								<span class="add-on"><i class="fa fa-calendar-o"></i></span>
								{!! Form::text('valid_from', '',  ['id' => 'datepicker_search','class' => 'calendar form-control from-date-control','readonly' => true, 'placeholder' => 'Dispatch Date*']) !!}
								<input type="hidden" name="dispatch_flexible_hidden" id="dispatch_flexible_hidden" value="0">
								
							</div>
						</div>
						<div class="clearfix"></div>
						{!!	Form::hidden('is_search',1,array('class'=>'form-control')) !!}
						{!!	Form::hidden('spot_or_term',1,array('class'=>'form-control')) !!}
						<div class="col-md-4 form-control-fld">
							<div class="input-prepend">
								<span class="add-on"><i class="fa fa-calendar-o"></i></span>
								{!! Form::text('valid_to', '' , ['id' => 'datepicker_to_location_search','class' => 'calendar form-control to-date-control','readonly' => true, 'placeholder' => 'Delivery Date ']) !!}
								<input type="hidden" name="delivery_flexible_hidden" id="delivery_flexible_hidden" value="0">
							</div>
						</div>
						<div class="clearfix"></div>

						<!--	
							{{-- Seller/Home/Relocation/International/Search/Spot/Air  --}}
								<div class="show_spot_air" id="show_spot_air">
			                    	    {!! Form::hidden('service_type', '1', array('id' => 'post_type')) !!}
											@include('relocationint.airint.sellers.seller_search_buyers')
								</div>	
							{{-- Seller/Home/Relocation/International/Search/Spot/Ocean  --}}
								<div class="show_spot_ocean" id="show_spot_ocean" style="display:none">
									<div class="clearfix"></div>
											@include('relocationint.ocean.sellers.seller_search_buyers')
								</div>	
						-->		
					</div>
				</div>
			</div>
			<div class="col-md-4 col-md-offset-4">
				<button class="btn theme-btn btn-block">Search</button>
			</div>
			{!! Form::close() !!}