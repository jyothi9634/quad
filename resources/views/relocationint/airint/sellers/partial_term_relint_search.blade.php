@if(isset($from_location) && $from_location!='')
    {{--*/ $from_location = $from_location; /*--}}
@else
    {{--*/ $from_location = '' ; /*--}}
@endif

@if(isset($from_city_id) && $from_city_id!='')
    {{--*/ $from_city_id = $from_city_id; /*--}}
@else
    {{--*/ $from_city_id = '' ; /*--}}
@endif

@if(isset($seller_district_id) && $seller_district_id!='')
    {{--*/ $seller_district_id = $seller_district_id; /*--}}
@else
    {{--*/ $seller_district_id = '' ; /*--}}
@endif

@if(isset($to_location) && $to_location!='')
    {{--*/ $to_location = $to_location; /*--}}
@else
    {{--*/ $to_location = '' ; /*--}}
@endif

@if(isset($to_city_id) && $to_city_id!='')
    {{--*/ $to_city_id = $to_city_id; /*--}}
@else
    {{--*/ $to_city_id = '' ; /*--}}
@endif
{!! Form::open(['url' =>'termsellersearchresults','method'=>'GET','id'=>'term_relocint_air_ocean']) !!}
			{!! Form::hidden('lead_type', '1', array('id' => 'post_type')) !!}
			<div class="home-search gray-bg margin-top-none padding-top-none border-top-none">
				<div class="home-search-form">
					<div class="clearfix"></div>
					<div class="col-md-12 padding-none">
						<div class="col-md-12 form-control-fld">
							<div class="radio-block">
								<input type="radio" checked="checked" name="term_service_type" id="term_air" value="1">
								<label for="term_air"><span></span>Air</label>
									
								<input type="radio" name="term_service_type" id="term_ocean" value="2">
								<label for="term_ocean"><span></span>Ocean</label>
							</div>
						</div>
						<div class="col-md-4 form-control-fld">
							<div class="input-prepend">
								<span class="add-on"><i class="fa fa-map-marker"></i></span>
								{!! Form::text('term_from_location', $from_location, ['id' => 'term_from_location','class' => 'top-text-fld form-control', 'placeholder' => 'From Location*']) !!}
								{!! Form::hidden('term_from_city_id', $from_city_id, array('id' => 'term_from_location_id')) !!}
								{!! Form::hidden('seller_district_id', $seller_district_id, array('id' => 'seller_district_id')) !!}
								{!! Form::hidden('is_search',1,array('class'=>'form-control')) !!}
								{!! Form::hidden('spot_or_term',2,array('class'=>'form-control')) !!}
							</div>
						</div>
						<div class="col-md-4 form-control-fld">
							<div class="input-prepend">
								<span class="add-on"><i class="fa fa-map-marker"></i></span>
								{!! Form::text('term_to_location', $to_location, ['id' => 'term_to_location','class' => 'top-text-fld form-control', 'placeholder' => 'To Location*']) !!}
								{!! Form::hidden('term_to_city_id', $to_city_id, array('id' => 'term_to_location_id')) !!}
							</div>
						</div>

						<div class="clearfix"></div>
					</div>
				</div>
			</div>
			<div class="submit_container">
				<div class="col-md-4 col-md-offset-4">
					<!--button class="btn theme-btn btn-block">Get Quote</button-->
					<input type="submit" id ='buyersearchresults' value="&nbsp; Search &nbsp;" class="btn theme-btn btn-block">
				</div>
			</div>
			{!! Form::close() !!}