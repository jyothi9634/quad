			{!! Form::open(['url' => 'byersearchresults','id'=>'search-form_buyer_relocationgm','method'=>'get']) !!}
				<div class="home-search gray-bg margin-top-none">
				
				{!!	Form::hidden('is_search',1,array('class'=>'form-control')) !!}
					<div class="col-md-12 padding-none">
					
						<div class="col-md-4 form-control-fld">
							<div class="input-prepend">
								<span class="add-on"><i class="fa fa-map-marker"></i></span>
								<!-- <input class="form-control" id="" type="text" placeholder="City *"> -->
									{!! Form::text('to_location',  Session::get('session_to_location_buyer'),  ['id' => 'to_location','class' => 'form-control', 'placeholder' => 'City *']) !!}
                                                                        {!! Form::hidden('to_location_id',  Session::get('session_to_city_id_buyer') , array('id' => 'to_location_id')) !!}

							</div>
						</div>

						<div class="col-md-4 form-control-fld">
							<div class="input-prepend">
								<span class="add-on"><i class="fa fa-calendar-o"></i></span>
								<!-- <input class="form-control" id="" type="text" placeholder="Date *"> -->
								{!! Form::text('from_date', Session::get('session_dispatch_date_buyer'),  ['id' => 'dispatch_date1','class' => 'calendar form-control from-date-control','readonly' => true, 'placeholder' => 'Date *']) !!}
									<input type="hidden" name="dispatch_flexible_hidden" id="dispatch_flexible_hidden" value="<?php echo isset($_REQUEST['dispatch_flexible_hidden']) ? $_REQUEST['dispatch_flexible_hidden'] : ""; ?>">
							</div>
						</div>
						<div class="col-md-4 form-control-fld">
										<div class="input-prepend">
											<span class="add-on"><i class="fa fa-cog"></i></span>
											{!!	Form::select('relgm_service_type',(['' => 'Services'] + $lkp_relgm_services), Session::get('session_service_type_relocation') ,['class' =>'selectpicker','id'=>'relgm_service_type','onchange'=>'return addMeasurementValidation(this.value,"measurement")']) !!}
										</div>
									</div>
								<div class="clearfix"></div>
						<div class="col-md-4 form-control-fld">
							<div class="input-prepend" id="measures_div">

							<input class="form-control form-control1 clsGMSNoOfDays" id="measurement" name="measurement" type="text"  value="{{ Session::get('session_measurement_relocation') }}">
							<span class="add-on unit1 manage"><input type="text" name="measurement_unit" readonly="readonly" placeholder="Day(s)" value="Day(s)" id="measurement_unit" class="form-control form-control1 valid"></span>
							</div>
						</div>

					</div>
				
				</div>
				<div class="col-md-4 col-md-offset-4">
					
						<input  type="submit" class="btn theme-btn btn-block"  name="getquote" id="getquote" value="Search">
					</div>	
		{!! Form::close() !!}	