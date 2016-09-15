@extends('app') @section('content')

<div class="main-container">
	<div class="container container-inner">
		<!-- Left Nav Starts Here -->
		@include('partials.leftnav')
		<!-- Left Nav Ends Here -->
		<div class="col-lg-7 col-md-6 col-sm-12 col-xs-12 main-middle">

			<div class="block">
				<div class="tab-nav underline">
					@include('partials.page_top_navigation')</div>

				{!! Form::open(['url'=>'#','id'=>'seller-intracity-create-post'])!!}
				<div class="col-md-12 col-sm-12 col-xs-12 padding-none">
					<div class="col-md-3 col-sm-3 col-xs-12 padding-none form-group">
						{!! Form::select('lkp_city_id', (['' => 'City'] + $cities),
						null,['class' => 'selectpicker
						form-control','onchange'=>"populateLocality();",'id'
						=>'intracity_city_list']) !!}</div>


					<div
						class="col-md-3 col-sm-3 col-xs-12 padding-right-none mobile-padding-none form-group">
						{!! Form::select('from_location_id', (['' => 'From Location'] ),
						null, ['class' => 'selectpicker form-control','id' =>
						'from_locality_list']) !!}</div>
					<div
						class="col-md-3 col-sm-3 col-xs-12 padding-right-none mobile-padding-none form-group">

						{!! Form::select('to_location_id', (['' => 'To Location'] ), null,
						['class' => 'selectpicker form-control','id' =>
						'to_locality_list']) !!}</div>

					<div class="clearfix"></div>
					<div class="col-md-3 col-sm-3 col-xs-12 padding-none form-group">
						{!! Form::text('from_date','', ['id' => 'from_date','class' =>
						'calendar form-control','placeholder'=>'From Date']) !!}</div>
					<div
						class="col-md-3 col-sm-3 col-xs-12 padding-right-none mobile-padding-none form-group">
						{!! Form::text('to_date','', ['id' => 'to_date','class' =>
						'calendar form-control','placeholder'=>'To Date']) !!}</div>
					<div class="clearfix"></div>
					<div class="col-md-3 col-sm-3 col-xs-12 padding-none form-group">
						{!! Form::text('lkp_load_type_id','', ['id' =>
						'load_type_id','class' => ' form-control','placeholder'=>'Load
						Type']) !!}</div>
					<div
						class="col-md-2 col-sm-2 col-xs-4 padding-right-none mobile-padding-none form-group ">
						{!! Form::text('actual_weight','', ['id' =>
						'actual_weight','class' => ' form-control','placeholder'=>'350'])
						!!}</div>
					<div
						class="col-md-2 col-sm-2 col-xs-3 padding-right-none form-group">
						{!! Form::select('units', (['Kgs' => 'Kgs'] ), null, ['class' =>
						'selectpicker form-control','id' => 'units']) !!}</div>
					<div
						class="col-md-3 col-sm-3 col-xs-5 padding-right-none form-group">
						{!! Form::select('lkp_vehicle_type_id', (['' => 'Vehicle
						Type']+$vehicleType ), null, ['class' => 'selectpicker
						form-control','id' => 'vehicle_type_list']) !!}</div>
					<div
						class="col-md-3 col-sm-3 col-xs-12 padding-right-none mobile-padding-none form-group">
						<img src="{{url('images/truck.png')}}" alt="" class="pull-left" />
						<p id="vehicle-dimension pull-left">200 X 180</p>
					</div>
					<div class="clearfix"></div>

					<div class="col-md-3 col-sm-3 col-xs-12 padding-none form-group">

		{!! Form::select('lkp_vehicle_type_id', (['' => 'Vehicle
						Type']+$vehicleType ), null, ['class' => 'selectpicker
						form-control','id' => 'vehicle_type_list']) !!}
						
					<select class="form-group selectpicker" id="rate_type_id">
					<option value="1" selected="selected">Per Trip Rate </option>
					<option value="2">Per Hour Rate</option>
					<option value="3">Per Km Rate</option>
					
					
					
					</select>
					</div>
					<div
						class="col-md-2 col-sm-2 col-xs-12  mobile-padding-none small-width padding-right-none tran">

						<p>Transit Time</p>
					</div>
					<div
						class="col-md-2 col-sm-2 col-xs-6 padding-none mobile-padding-none form-group kg-width">
						{!! Form::text('transit_time','', ['id' => 'transit_time','class'
						=> 'form-control number']) !!}</div>
					<div
						class="col-md-2 col-sm-2 col-xs-6 padding-right-none  form-group kg-width">

						{!! Form::select('period_type', (['Hours' =>
						'Hours','Days'=>'Days'] ), null, ['class' => 'selectpicker
						form-control','id' => 'period_type']) !!}</div>

				</div>
				<div class="clearfix"></div>
				<div
					class="col-md-12 col-sm-12 padding-none form-group margin-top ">


					<div class="col-md-12 col-sm-12 padding-none form-group">
						<div class="form-group tab-wdth  margin-top " id="minHours">
							<label
								class="col-md-4 col-sm-6 col-xs-7 padding-none tb-margin-bottom">Minimum
								Hours</label>
							<div
								class="form-group input-group col-md-5 col-sm-6 col-xs-5 margin-bottom tb-padding-none mobile-padding-none ">

								{!! Form::text('minimum_hours','', ['id'
								=>'minimum_hours','class'=> ' form-control number']) !!}</div>

						</div>
						<div class="form-group tab-wdth  margin-top " id="minKilo">
							<label
								class="col-md-4 col-sm-6 col-xs-7 padding-none tb-margin-bottom">Minimum
								Kms</label>
							<div
								class="form-group input-group col-md-5 col-sm-6 col-xs-5 margin-bottom tb-padding-none mobile-padding-none ">

								{!! Form::text('minimum_kms','', ['id' =>'minimum_kms','class'=>
								' form-control number']) !!}</div>

						</div>
						<div class="form-group tab-wdth  margin-top "  id="minCharges">
							<label
								class="col-md-4 col-sm-6 col-xs-7 padding-none tb-margin-bottom">Minimum
								Charges<span id="perHour"> (Per Hour)</span><span id="perKm"> (Per Km)</span></label>
							<div
								class="form-group input-group col-md-5 col-sm-6 col-xs-5 margin-bottom tb-padding-none mobile-padding-none ">

								{!! Form::text('minimum_charges','', ['id'
								=>'minimum_charges','class'=> 'form-control
								number']) !!}</div>

						</div>

						<div class="form-group tab-wdth margin-top"  id="basicCharges">
							<label
								class="col-md-4 col-sm-6 col-xs-7 padding-none tb-margin-bottom">Basic
								Charges</label>
							<div
								class="form-group input-group col-md-5 col-sm-6 col-xs-5 margin-bottom tb-padding-none mobile-padding-none">
								{!! Form::text('basic_charges','', ['id' =>
								'basic_charges','class'=> 'form-control number']) !!}</div>
							<!--<input type="button" value="Add" class="btn btn-black btn-black">-->

						</div>

						<div class="form-group tab-wdth  margin-top"  id="waitingCharges">
							<label
								class="col-md-4 col-sm-6 col-xs-7 padding-none tb-margin-bottom">Waiting
								Charges (per hour)</label>
							<div
								class="form-group input-group col-md-5 col-sm-6 col-xs-5 margin-bottom tb-padding-none mobile-padding-none">
								{!! Form::text('hourly_waiting_charges','', ['id' =>
								'hourly_waiting_charges','class'=> 'form-control
								number']) !!}</div>
							<!--<input type="button" value="Add" class="btn btn-black btn-black">-->

						</div>
						<div class="form-group tab-wdth  margin-top"  id="overDimCharges">
							<label
								class="col-md-4 col-sm-6 col-xs-7 padding-none tb-margin-bottom">Over
								Dimension Charges</label>
							<div
								class="form-group input-group col-md-5 col-sm-6 col-xs-5 margin-bottom tb-padding-none mobile-padding-none">
								{!! Form::text('over_dimension_charges','', ['id' =>
								'over_dimension_charges','class'=> 'form-control
								number']) !!}</div>
							<!--<input type="button" value="Add" class="btn btn-black btn-black">-->

						</div>
						<div class="form-group tab-wdth  margin-top"  id="labourCharges">
							<label
								class="col-md-4 col-sm-6 col-xs-7 padding-none tb-margin-bottom">Labour
								Charges(per person)</label>
							<div
								class="form-group input-group col-md-5 col-sm-6 col-xs-5 margin-bottom tb-padding-none mobile-padding-none ">

								{!! Form::text('labour_charges','', ['id'
								=>'labour_charges','class'=> 'form-control
								number']) !!}</div>

						</div>
					</div>
					<div class="col-md-4 col-sm-4 col-xs-12 text-right padding-right-none pull-right margin-bottom">
						<input type="button" class="btn black-btn" value="Bulk Upload"> <input
							type="submit" class="btn black-btn" value="Add"
							id="add-seller-post">
					</div>
				</div>
					{!!Form::Close()!!}
						<div class="clearfix"></div>
					
					{!! Form::open(['url'=>'','id'=>'seller-intracity-submit-post'])!!}
					<div class="col-md-12 col-sm-12 padding-none form-group margin-top ">
					<div class="clearfix"></div>
					<div width="100%" class="table table-head">
						<div class="col-md-12 padding-none">
							<div class="col-md-3 col-sm-2 col-xs-2 padding-none">From Location</div>
							<div class="col-md-3 col-sm-2 col-xs-2 padding-none">To Location</div>
							<div class="col-md-2 col-sm-3 col-xs-3 padding-none">Load Type</div>
							<div class="col-md-2 col-sm-3 col-xs-3 padding-none">Vehicle Type</div>
							<div class="col-md-2 col-sm-2 col-xs-2 padding-none">Price</div>
						</div>
					</div>
					<div class="clearfix"></div>
					<div
						class="col-md-3 col-sm-3 col-xs-12 padding-none mobile-padding-none">
						<div class=" form-group">
							<select class="selectpicker">
								<option>Tracking</option>
								<option>Milestone</option>
								<option>Real time</option>
							</select>
						</div>
					</div>
					<div class="clearfix"></div>
					<div class="col-md-12 col-sm-12 padding-none  mobile-padding-none">
						<textarea cols="57" row="5" class="form-control margin-bottom"
							placeholder="Notes to Terms & Conditions (optional)"></textarea>

					</div>
					<div class="clearfix"></div>
					<div
						class="col-md-12 col-sm-3 padding-none  mobile-padding-none margin-bottom">
						<input type="button" value="Submit"
							class="btn btn-black btn-black">
					</div>



				</div>
				
				<div class="clearfix"></div>
				<div class="col-md-12 col-sm-12 padding-none border-top">
					<div class="heading">Payment Terms</div>
					<div class="col-md-6 col-sm-6 col-xs-12 padding-none form-group">
						
							<div
								class="padding-top col-md-9 mobile-padding-none tb-padding-none form-group">
								<select class="selectpicker">
									<option>Advance</option>
									<option>Cash on Pickup</option>
									<option>Cash on Delivery</option>
									<option>Credit Card</option>
								</select>
							</div>
							<div class="clearfix"></div>
							<div class="checkbox-group">
								<div
									class="margin-bottom col-md-12 col-sm-6 col-xs-12 padding-none">
									<input type="checkbox">&nbsp; NEFT/RTGS
								</div>
								<div class="clearfix"></div>
								<div
									class="margin-bottom col-md-12 col-sm-6 col-xs-12 padding-none">
									<input type="checkbox">&nbsp; Credit Card
								</div>
								<div class="clearfix"></div>

								<div
									class="margin-bottom col-md-12 col-sm-6 col-xs-12 padding-none">
									<input type="checkbox">&nbsp; Debit Card
								</div>

							</div>
						
					</div>
					<div
						class="col-md-6 col-sm-6 col-xs-12 padding-right-none mobile-padding-none">
						<div class="padding-top font-bold">Credit</div>
						<div class="padding-top form-group">Credit Period</div>
						<div class="padding-none col-sm-3 col-xs-6 mobile-padding-none">
							<input type="text" value="" class="form-control">
						</div>
						<div
							class="padding-none col-sm-3 col-xs-5 margin-left mobile-padding-none form-group">
							<select class="selectpicker">
								<option>Days</option>
							</select>
						</div>
						<div class="clearfix"></div>
						<div class="col-md-4 col-sm-4 col-xs-12 padding-none form-group">
							<input type="checkbox">&nbsp; Net Banking &nbsp;&nbsp;
						</div>
						<div class="col-md-4 col-sm-4 col-xs-12 padding-none form-group">
							<input type="checkbox">&nbsp; Cheque / DD &nbsp;&nbsp;
						</div>
					</div>
					<div class="clearfix"></div>
				</div>
				<div class="clearfix"></div>
				<div class="col-md-12 col-sm-12 padding-none border-top">

					<div class="col-md-12 col-sm-12 padding-none margin-bottom">Disclaimer</div>
					<div class="clearfix"></div>
					<div class="col-md-12 col-sm-12 col-xs-12 form-group padding-none">

						<label class="radio-inline"><input type="radio" name="optradio">Post
							Public </label> <label class="radio-inline"><input type="radio"
							name="optradio">Post Private </label>

					</div>
					<div class="clearfix"></div>
					<div class="col-md-4 col-sm-4 col-xs-12 padding-none">
						<select class="selectpicker bs-select-hidden">
							<option>Buyer Name (Auto Search)</option>
							<option>Buyer Name (Auto Search)</option>

						</select>


					</div>

					<div class="col-md-3 col-sm-3 col-xs-12">
						<h5>Selected Buyers</h5>
						<p>NFCL</p>
						<p>P&G</p>

					</div>

					<div class="clearfix"></div>
					<div class="spacing space-margin">
						<input type="checkbox">&nbsp; &nbsp; Accept Terms &amp; Conditions
						( Digital Contract )
					</div>
					<div class="clearfix"></div>
					<input type="button" value="Save as draft"
						class="btn black-btn margin-top">
					<input type="button"
						value="Confirm" class="btn black-btn margin-top">

				</div>
				{!!Form::Close()!!}
			</div>
		</div>
		<!-- Right Starts Here -->
		@include('partials.right')
		<!-- Right Ends Here -->
	</div>
</div>
@endsection
