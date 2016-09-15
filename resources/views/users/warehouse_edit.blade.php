@extends('app') @section('content')
@include('partials.page_top_navigation') @if (Session::has('message') &&
Session::get('message')!='')
<div class="flash">
	<p
		class="text-success col-md-12 form-control-fld text-center flash-txt alert-info">{{
		Session::get('message') }}</p>
</div>
@endif


<div class="main-container">
<div class="login-head heading-margin-top">
			<h1 class="margin-top margin-bottom-none">
				<span>LOGISTIKS.COM</span>
				<p>Warehouse Registration</p>
			</h1>
		</div>
<div class="main">
	<div class="container reg_crumb">

		
		<div class="home-block home-block-login">
			<div class="tabs">

				<div class="tab-content">
					<div id="buyer" class="tab-pane fade in active">
						<div class="login-block">
							<div class="login-form login-form-2">
								<div class="center-width">

									{!! Form::open(['url' =>
									'warehouse_update/'.$warehouses->id,'id'=>'warehouse-master-form','enctype'=>'multipart/form-data','class'=>'form-inline
									margin-top']) !!}


									<div
										class="col-md-12 form-control-fld padding-none  form-control-fld">
										<label><b>Warehouse ID: #{{$warehouses->id}}</b></label> {!!
										Form::label('wh_type', 'Warehouse Type', array('class' =>
										'col-md-12 form-control-fld')) !!}
										<div class="col-md-6 form-control-fld">
											<div class="normal-select">{!!
												Form::select('wh_type',array('' => 'Select warehouse Type
												*') + $warehouse,
												$warehouses->lkp_warehouse_type_id,['class'=>'form-control
												form-control1','id'=>'wh_type']) !!}</div>
										</div>
									</div>

									<div class="col-md-12 form-control-fld padding-none">

										<div class="col-md-6 form-control-fld">
											<div class="normal-select">{!!
												Form::select('state_id',array('' => 'Select State *') +
												$state,$warehouses->lkp_state_id,['class'=>'form-control
												form-control1','onChange'=>"getDistrict()",'id'=>'state_id'])
												!!}</div>
										</div>


										<div class="col-md-6 form-control-fld">
											<div class="normal-select">{!!
												Form::select('district_id',array('' => 'Select District
												*')+$district
												,$warehouses->lkp_district_id,['class'=>'form-control
												form-control1','id'=>'district_id','onChange'=>"getCity()"])
												!!}</div>
										</div>

										<div class="col-md-6 form-control-fld">
											<div class="normal-select">{!!
												Form::select('city_id',array('' => 'Select City *')+$city
												,$warehouses->lkp_location_id,['class'=>'form-control
												form-control1','id'=>'city_id']) !!}</div>
										</div>

									</div>

									<div class="col-md-6 form-control-fld">{!!
										Form::text('pincode',$warehouses->pincode,['class'=>'form-control
										form-control1','placeholder'=>'Pincode *']) !!}</div>
									<div class="col-md-6 form-control-fld">{!!
										Form::text('from_dt',date("d/m/Y",
										strtotime($warehouses->from_date)),['class'=>'form-control
										form-control1 calendar', 'placeholder'=>'From date *',
										'id'=>'datepicker_from']) !!}</div>
									<div class="col-md-6 form-control-fld">{!!
										Form::text('to_dt',date("d/m/Y",
										strtotime($warehouses->to_date)),['class'=>'form-control
										form-control1 calendar', 'placeholder'=>'To date *' ,
										'id'=>'datepicker_to']) !!}</div>
									<div class="col-md-6 form-control-fld"><div class="normal-select">{!!
										Form::select('cargo_type',array('' => 'Select Cargo Type') +
										$load_type,$warehouses->cargo_type,['class'=>'selectpicker','id'=>'cargo_type'])
										!!}</div></div>






									<div class="clearfix"></div>
									<div class="form-group">

										<div class="col-md-12 form-control-fld">
											<label>Space Available (Sq Feet)</label>
										</div>
										<div class="col-md-6 form-control-fld">{!!
											Form::text('space_min_ft',$warehouses->space_min_feet,['class'=>'form-control
											form-control1','placeholder'=>'Minimum *']) !!}</div>
										<div class="col-md-6 form-control-fld">{!!
											Form::text('space_max_ft',$warehouses->space_max_feet,['class'=>'form-control
											form-control1','placeholder'=>'Maximum *']) !!}</div>
										<div class="col-md-6 form-control-fld">{!!
											Form::text('capacity',$warehouses->capacity,['class'=>'form-control
											form-control1','placeholder'=>'Capacity *']) !!}</div>

									</div>
									<div class="clearfix"></div>
									<div class="form-group">
										<div class="col-md-12 form-control-fld">{!!
											Form::label('wh_owner_fist_name', 'Name of the WH Owner',
											array('class' => '')) !!}</div>

										<div class="col-md-6 form-control-fld">{!!
											Form::text('wh_owner_fist_name',$warehouses->owner_firstname,['class'=>'form-control
											form-control1','placeholder'=>'First Name *']) !!}</div>
										<div class="col-md-6 form-control-fld">{!!
											Form::text('wh_owner_middle_name',$warehouses->owner_middlename,['class'=>'form-control
											form-control1','placeholder'=>'Middle Name']) !!}</div>
										<div class="clearfix"></div>
										<div class="col-md-6 form-control-fld">{!!
											Form::text('wh_owner_last_name',$warehouses->owner_lastname,['class'=>'form-control
											form-control1','placeholder'=>'Last Name *']) !!}</div>
									</div>

									<div class="form-group">
										{!! Form::label('cp_first_name', 'Contact Person',
										array('class' => 'col-md-12 form-control-fld')) !!}

										<div class="col-md-6 form-control-fld">{!!
											Form::text('cp_first_name',$warehouses->contact_firstname,['class'=>'form-control
											form-control1','placeholder'=>'First Name']) !!}</div>
										<div class="col-md-6 form-control-fld">{!!
											Form::text('cp_middle_name',$warehouses->contact_middlename,['class'=>'form-control
											form-control1 margin-bottom','placeholder'=>'Middle Name'])
											!!}</div>
										<div class="col-md-6 form-control-fld">{!!
											Form::text('cp_last_name',$warehouses->contact_lastname,['class'=>'form-control
											form-control1 margin-bottom','placeholder'=>'Last Name']) !!}</div>
									</div>
									<div class="clearfix"></div>

									{!! Form::label('ownership_type', 'Ownership Type',
									array('class' => 'col-md-12 form-control-fld')) !!}
									<div class="col-md-6 form-control-fld"><div class="normal-select">{!!
										Form::select('ownership_type',array('' => 'Select Ownership
										Type') + array(1 => 'Partnership', 2 =>
										'Company'),$warehouses->ownership_type,['class'=>'form-control
										form-control1 ','id'=>'ownership_type']) !!}</div></div>




									<div class="col-md-6 form-control-fld">{!!
										Form::text('mobile_number',$warehouses->mobile_number,['class'=>'form-control
										form-control1', 'placeholder'=>'Mobile Number *']) !!}</div>

									<div class="col-md-6 form-control-fld">{!!
										Form::text('email',$warehouses->email,['class'=>'form-control
										form-control1','placeholder'=>'Email *']) !!}</div>




									<div class="col-md-12 form-control-fld">
										{!! Form::label('wh_address', 'Premises address with door
										number', array('class' => 'col-md-12 form-control-fld')) !!}
										<div class="col-md-12 form-control-fld">{!!
											Form::textarea('wh_address',$warehouses->address,['rows'=>'3','cols'=>'54','class'=>'form-control
											form-control1', 'placeholder'=>'Address *']) !!}</div>
									</div>

									<div class="col-md-12 form-control-fld">
										{!! Form::label('wh_short_name', 'WH Short Name',
										array('class' => 'col-md-12 form-control-fld padding-none'))
										!!}
										<div class="col-md-6 form-control-fld">{!!
											Form::text('wh_short_name',$warehouses->short_name,['class'=>'form-control
											form-control1', 'placeholder'=>'Short Name *']) !!}</div>
									</div>

									<div class="form-group">
										{!! Form::label('infrastructure_available', 'Infrastructure
										Available', array('class' => 'col-md-12 form-control-fld'))
										!!}






										<div class="col-sm-9 form-control-fld">
                            			   <?php
																																		$infra = explode ( ',', $warehouses->infrastructure_available );
																																		$inf_data = array (
																																				"ups" => 'UPS, Generator',
																																				"docks" => 'Docks, Forklift , Dock Levelers',
																																				'fhs' => 'Fire Hydrant System, CC Tv',
																																				'rack' => 'Racking, DG etc' 
																																		);
																																		// echo $form->checkBoxList($model, 'infrastructure_available', $inf_data, array('template' => '<label class="radio-inline">{input} {label}</label>', 'separator' => ' '));
																																		foreach ( $inf_data as $k => $v ) {
																																			if (in_array ( $k, $infra )) {
																																				$str = true;
																																			} else {
																																				$str = false;
																																			}
																																			?>
                      <div
												class="col-md-12 form-control-fld padding-right-none">
												{!! Form::checkbox('Warehouse[infrastructure][]',$k,$str)
												!!} <span class="lbl padding-8"></span> {{$v}}
											</div>
                        <?php }?>                       
                        
                        
                       
                        </div>


									</div>
									<div class="col-md-12 form-control-fld padding-none">
										{!! Form::label('amenities', 'Amenities', array('class' =>
										'col-sm-12 padding-none')) !!}
										<div class="col-sm-9 padding-none">
                            
                           <?php
																											$amenities = explode ( ',', $warehouses->amenities );
																											$inf_data1 = array (
																													"driver" => 'Driver Room',
																													"security" => 'Security',
																													'tv' => 'TV, Internet ',
																													'ts' => 'Transport Services' 
																											);
																											//
																											foreach ( $inf_data1 as $k => $v ) {
																												
																												if (in_array ( $k, $amenities )) {
																													$str = true;
																												} else {
																													$str = false;
																												}
																												?>
                                <div
												class="col-md-12 form-control-fld padding-right-none">
												{!! Form::checkbox('Warehouse[amenities][]',$k,$str) !!} <span
													class="lbl padding-8"></span>{{$v}}
											</div>
                            <?php } ?>
                        </div>
									</div>

									<div class="col-md-12 form-control-fld padding-none">
										{!! Form::label('additional_services', 'Additional Services',
										array('class' => 'col-sm-12 padding-none')) !!}
										<div class="col-sm-9 padding-none">
                           <?php
																											$services = explode ( ',', $warehouses->additional_services );
																											$add_data1 = array (
																													"insurance" => 'Insurance',
																													"packaging_services" => 'Packaging Services',
																													'handling_services' => 'Handling Services',
																													'ts' => 'Transport Services' 
																											);
																											// echo $form->checkBoxList($model, 'additional_services', $add_data1, array('template' => '<label class="radio-inline">{input} {label}</label>','separator' => ' '));
																											foreach ( $add_data1 as $k => $v ) {
																												if (in_array ( $k, $services )) {
																													$str = true;
																												} else {
																													$str = false;
																												}
																												?>
                                <div
												class="col-md-12 form-control-fld padding-right-none">
												{!!
												Form::checkbox('Warehouse[additional_services][]',$k,$str)
												!!} <span class="lbl padding-8"></span>{{$v}}
											</div>
                            <?php } ?>
                        </div>
									</div>
									<div class="col-md-12 padding-none ">
										{!! Form::label('transport_reg_id', 'LSP/Transport Reg. ID',
										array('class' => 'col-md-12 form-control-fld')) !!}
										<div class="col-md-6 form-control-fld">{!!
											Form::text('transport_reg_id',$warehouses->transport_reg_id,['class'=>'form-control
											form-control1']) !!}</div>
									</div>
									<div class="col-md-12 form-control-fld">
										<div class="col-sm-9 padding-none">
											<input type="checkbox" checked="checked" id="cdbaccept"
												name="cdbaccept"><span class="lbl padding-8"></span> Accept
											Term &amp; Conditions (Digital Contract) &nbsp; &nbsp;
										</div>

										<div class="clearfix"></div>
										<div class="col-sm-3 padding-none">{!! Form::submit('Confirm &
											Register', ['class' => 'btn post-btn theme-btn
											register_submit']) !!}</div>
										{!! Form::close() !!}

									</div>
								</div>
							</div>
						</div>
					</div>


				</div>
			</div>
		</div>
	</div>
</div>
</div>
@include('partials.footer')
</div>



@endsection
