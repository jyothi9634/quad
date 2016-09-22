@extends('app') @section('content')
@include('partials.page_top_navigation')

<div class="clearfix"></div>
<div class="login-head">
			<h1>Thank you for <span>REGISTRATION</span></h1>
		</div>
<div class="main">
	<div class="container reg_crumb">
		
		<div class="home-block home-block-login">
			
			<div class="tabs">

				<div class="tab-content">
					{!! Form::open(array('url' => '/storeMarketplaceDetails', 'name' => 'individual-form', 'id' => 'individual-form', 'class'=>'form-inline margin-top','autocomplete'=>"off" )) !!}
						<div class="login-block">
							<div class="login-form login-form-2">
								
								
								<div class="center-width">
									<div class="col-md-12 padding-none">
										<div class="col-md-6 form-control-fld">
											<div class="input-prepend">
												{!! Form:: text ('business_name','',
												array( 'placeholder'=>'Name of the Business','class'=>'form-control form-control1', 'id'=>'business_name', 'maxlength'=>'100' ))
												!!}
											</div>
										</div>
										<div class="col-md-6 form-control-fld">
											
											<a href="/auth/login" style="margin-left:63%;color:white;" id="askmeLater" class="btn add-btn pull-right">ASK ME LATER</a>
											
										</div>
									</div>
								</div>
								
								<div class="col-md-12 padding-none">
									<div class="col-md-6 form-control-fld">
										<div class="input-prepend">
										<div class="normal-select">	
										<select name="business_const" id="business_const" class="form-control form-control1">
										<option value="">Constitution of Business</option>
										 <option value="1">Proprietorship</option>
									      <option value="2">Private Limited</option>
									      <option value="3">Public Limited</option>
									      <option value="4">MNC</option>
									      <option value="5">Partnership Firm</option>
									      <option value="6">Government/Quas Government</option>
									      <option value="7">HUF</option>
									      <option value="8">Partnership Firm</option>
									      <option value="9">Others</option>
										</select>
										</div>
										</div>
										</div>	
									<!-- <div class="col-md-6 form-control-fld">
										<div class="input-prepend">
											{!! Form:: text ('const_business_others', '', array( 'class'=>'form-control form-control1','id'=>'const_business_others','placeholder'=>'Others*', 'maxlength'=>'6' )) !!}
										</div>
									</div> -->
									<div class="col-md-6 form-control-fld">
										<div class="input-prepend">
											{!!Form:: text ('cin_no', '', array( 'class'=>'form-control form-control1 ignore', 'id'=>'cin_no', 'placeholder'=>'CIN No', 'maxlength'=>'30' , 'style' => 'display:none;')) !!}
										</div>
									</div>
								</div>
								<div class="col-md-12 padding-none">
									<div class="col-md-6 form-control-fld">
										<div class="input-prepend">
											{!! Form:: text ('business_place', '', array( 'class'=>'form-control form-control1','id'=>'business_place','placeholder'=>'Principal Place of Business', 'maxlength'=>'30' )) !!}
										</div>
									</div>
									
									<div class="col-md-6 form-control-fld margin-none">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-map-marker"></i></span>
									
									{!! Form::text('pincodeLocation', '' , ['id' => 'pincodeLocation', 'class'=>'form-control numericvalidation_autopop maxlimitsix_lmtVal', 'placeholder' => 'Pincode*']) !!}
	                           		{!! Form::hidden('pincodeLocationId', '' , array('id' => 'pincodeLocationId')) !!}
									{!! Form::hidden('business_pincode', '' , array('id' => 'business_pincode')) !!}
									{!! Form::hidden('region', '' , array('id' => 'region')) !!}
								</div>
							</div>
							
								
									
								</div>
								<div class="col-md-12 padding-none">
									<div class="col-md-6 form-control-fld">
										<div class="input-prepend">
											{!! Form:: text ('business_location', '', array( 'class'=>'form-control form-control1','id'=>'business_location','placeholder'=>'Location', 'maxlength'=>'150' )) !!}
										</div>
									</div>
									<div class="col-md-6 form-control-fld">
										<div class="input-prepend">
											{!! Form:: text ('business_city', '', array( 'class'=>'form-control form-control1','id'=>'business_city','placeholder'=>'City', 'maxlength'=>'150' )) !!}
										</div>
									</div>
								</div>
								<div class="col-md-12 padding-none">
									<div class="col-md-6 form-control-fld">
										<div class="input-prepend">
											{!! Form:: text ('business_district', '', array( 'class'=>'form-control form-control1','id'=>'business_district','placeholder'=>'District', 'maxlength'=>'12' )) !!}
										</div>
									</div>
									<div class="col-md-6 form-control-fld">
										<div class="input-prepend">
											{!! Form:: text ('business_state', '', array( 'class'=>'form-control form-control1','id'=>'business_state','placeholder'=>'State','readonly' )) !!}
										</div>
									</div>
								</div>
								<div class="col-md-12 padding-none">
									<div class="col-md-6 form-control-fld">
										<div class="input-prepend">
											{!! Form:: text('address1', '', array( 'class'=>'form-control form-control1','id'=>'address1','placeholder'=>'Address1*', 'maxlength'=>'50', 'rows'=>'5' )) !!}
										</div>
									</div>
									<div class="col-md-6 form-control-fld">
										<div class="input-prepend">
											{!! Form:: text ('address2', '', array( 'class'=>'form-control form-control1','id'=>'address2','placeholder'=>'Address2*', 'maxlength'=>'50')) !!}
										</div>
									</div>
								</div>
								<div class="col-md-12 padding-none">
									<div class="col-md-6 form-control-fld">
										<div class="input-prepend">
											{!! Form:: text ('address3', '', array( 'class'=>'form-control form-control1','id'=>'address3','placeholder'=>'Address3', 'maxlength'=>'50')) !!}
										</div>
									</div>
									<div class="col-md-6 form-control-fld">
										<div class="normal-select">
											 <!--<select name="year_of_est" id="year_of_est">
											  <option value="">Year of Establishment*</option>
											  <option value="2016">2016</option>
											  <option value="2015">2015</option>
											  <option value="2014">2014</option>
											</select>-->
											{!!
												Form::select('year_of_est',array('' => 'Year of Establishment*')
												+ $getYearofEstablished
												,null,['class'=>'selectpicker','id'=>'year_of_est'])
											!!}
										</div>
									</div>
								</div>	
								
								<div class="col-md-12 padding-none">
									&nbsp;&nbsp;&nbsp;
								</div>

									<div class="col-md-12 padding-none">
										<label for="" class="col-md-12 padding-none">Annual Turn over</label>
										<div class="col-md-6 form-control-fld">
											<div class="input-prepend">{!! Form:: text
												('current_turnover', '',array( 'class'=>'form-control numberVal form-control1', 'id'=>'current_turnover',
												'placeholder'=>'Current Year', 'maxlength'=>'10' )) !!}</div>
										</div>
										<div class="col-md-6 form-control-fld">
											<div class="input-prepend">{!! Form:: text
												('first_year_turnover', '',array( 'class'=>'form-control numberVal form-control1', 'id'=>'txt_first_yr_turnover',
												'placeholder'=>'Year1', 'maxlength'=>'10' )) !!}</div>
										</div>
										<div class="col-md-6 form-control-fld">
											<div class="input-prepend">{!! Form:: text
												('second_year_turnover', '',array( 'class'=>'form-control numberVal form-control1', 'id'=>'txt_second_yr_turnover',
												'placeholder'=>'Year2', 'maxlength'=>'10' )) !!}</div>
										</div>
										<div class="col-md-6 form-control-fld">
											<div class="input-prepend">{!! Form:: text
												('third_year_turnover', '',array( 'class'=>'form-control numberVal form-control1', 'id'=>'txt_second_yr_turnover',
												'placeholder'=>'Year3', 'maxlength'=>'10' )) !!}</div>
										</div>
									</div>
								<div class="col-md-12 padding-none">
									&nbsp;&nbsp;&nbsp;
								</div>
								<div class="col-md-12 padding-none">
								<label for="" class="col-md-12 padding-none">Contact Person</label>	
								<div class="col-md-6 form-control-fld">
									<div class="input-prepend">
										{!! Form:: text ('contact_fname', '', array( 'class'=>'form-control form-control1','id'=>'contact_fname','placeholder'=>'FirstName', 'maxlength'=>'30' )) !!}
										<!-- {!! Form:: text ('business_mobile_no', '', array( 'class'=>'form-control form-control1','id'=>'business_mobile_no','placeholder'=>'Business Mobile No*', 'maxlength'=>'55' )) !!} -->
									</div>
								</div>
								<div class="col-md-6 form-control-fld">
									<div class="input-prepend">
										{!! Form:: text ('contact_lname', '', array( 'class'=>'form-control form-control1','id'=>'contact_lname','placeholder'=>'LastName', 'maxlength'=>'30' )) !!}
										<!-- {!! Form:: text ('business_landline', '', array( 'class'=>'form-control form-control1','id'=>'business_landline','placeholder'=>'Business Landline*', 'maxlength'=>'55' )) !!} -->
									</div>
								</div>
								</div>
								<div class="col-md-12 padding-none">
									&nbsp;&nbsp;&nbsp;
								</div>
								<div class="col-md-12 padding-none">
								<div class="col-md-6 form-control-fld">
									<div class="input-prepend">
										<!-- {!! Form:: text ('business_type_id', '', array( 'class'=>'form-control form-control1','id'=>'business_type_id','placeholder'=>'Business Types*', 'maxlength'=>'55' )) !!} -->
										{!! Form:: text ('business_designatn', '', array( 'class'=>'form-control form-control1','id'=>'business_designatn','placeholder'=>'Designation', 'maxlength'=>'55' )) !!}
									</div>
								</div>

								
								<div class="col-md-6 form-control-fld">
									<div class="input-prepend">
										{!! Form:: text ('business_emailId', '', array( 'class'=>'form-control form-control1','id'=>'business_emailId','placeholder'=>'Business EmailId*', 'maxlength'=>'55' )) !!}
									</div>
									<div class="error-1" id="show_validate" style="display:none; color:#ff0000;">EmailId Already Exists</div>
								</div>

								
								</div>
								<div class="col-md-12 padding-none">
								<div class="col-md-6 form-control-fld">
									<div class="input-prepend">
										<!-- {!! Form:: text ('employee_strn', '', array( 'class'=>'form-control form-control1','id'=>'employee_strn','placeholder'=>'Employee Strength*', 'maxlength'=>'55' )) !!} -->
										{!! Form:: text ('business_mobile_no', '', array( 'class'=>'form-control numberVal form-control1','id'=>'business_mobile_no','placeholder'=>'Mobile No*(Official)', 'maxlength'=>'10' )) !!}
									</div>
								</div>
								<div class="col-md-6 form-control-fld">
									<div class="input-prepend">
										<!-- {!! Form:: text ('industry_type_name', '', array( 'class'=>'form-control form-control1','id'=>'industry_type_name','placeholder'=>'Industry Types*', 'maxlength'=>'55' )) !!} -->
										{!! Form:: text ('business_landline', '', array( 'class'=>'form-control numberVal form-control1','id'=>'business_landline','placeholder'=>'Landline', 'maxlength'=>'12' )) !!}

									</div>
								</div>
								</div>
								<div class="col-md-12 padding-none">
								<label for="" class="col-md-12 padding-none">Business Info*</label>
								<div class="col-md-6 form-control-fld">
									<div class="normal-select">
										 <select name="business_type_id" id="business_type_id">
						                  <option value="">Business Type*</option>
						                  @foreach($business as $key=>$value)
						                  <option value="{{$key}}">{{$value}}</option>
						                  @endforeach
						                  </select>
										<!-- {!! Form:: text ('contact_fname', '', array( 'class'=>'form-control form-control1','id'=>'contact_fname','placeholder'=>'Contact Fname*', 'maxlength'=>'55' )) !!} -->
									</div>
								</div>
								
								<div class="col-md-6 form-control-fld">
									<div class="normal-select">
										 <select name="employee_strn" id="employee_strn">
						                  <option value="">Employee Strength</option>
						                  @foreach($getEmployeeStrengths as $key=>$value)
						                  <option value="{{$key}}">{{$value}}</option>
						                  @endforeach
						                 <!--  <option value="2015">2015</option>
						                  <option value="2014">2014</option> -->
						                </select>
										<!-- {!! Form:: text ('contact_fname', '', array( 'class'=>'form-control form-control1','id'=>'contact_fname','placeholder'=>'Contact Fname*', 'maxlength'=>'55' )) !!} -->
									</div>
								</div>
								
								</div>
								

								<div class="col-md-12 padding-none">
									<div class="col-md-6 form-control-fld">
									<div class="normal-select">
										<!--  <select name="industry_type_name" id="industry_type_name" class="industry_types">
						                  <option value="">Industry Type*</option>
						                  @foreach($lkp_industry as $key=>$value)
						                  <option value="{{$key}}">{{$value}}</option>
						                  @endforeach
						                  </select> -->

						                  {!!Form::select('industry_type_name',array('' => 'Select Industry')
														+ $lkp_industry,null,['class'=>'drop-scroll selectpicker industry_types',
														'id'=>'industry_multiple']) !!}

										<!-- {!! Form:: text ('contact_fname', '', array( 'class'=>'form-control form-control1','id'=>'contact_fname','placeholder'=>'Contact Fname*', 'maxlength'=>'55' )) !!} -->
									</div>
								</div>	
									<div class="col-md-6 form-control-fld" id="displayToggleSector">
										<div class="normal-select">
										{!! Form::select('sector_type', ([''=>'Select Sector']  ), null, ['class' => 'selectpicker','id' => 'sector_type']) !!}
										</div>
									</div>	
								</div>
								<div class="col-md-12 padding-none">
								<div class="col-md-6 form-control-fld">
									<div class="input-prepend">
										{!! Form:: text ('business_pan', '', array( 'class'=>'form-control form-control1','id'=>'business_pan','placeholder'=>'PAN (Business)*', 'maxlength'=>'55' )) !!}
									</div>
									<div class="error-1" id="show_pan" style="display:none; color:#ff0000;">Pancard Already Exists</div>
								</div>
								<div class="col-md-6 form-control-fld">
									<div class="input-prepend">
										{!! Form:: text ('others_name', '', array( 'class'=>'form-control form-control1','id'=>'others_name','placeholder'=>'Others (Specify)', 'maxlength'=>'55' )) !!}
									</div>
								</div>	
								</div>
								<div class="col-md-12 padding-none">
									<div class="col-md-6 form-control-fld">
									</div>
									<div class="col-md-6 form-control-fld">
									<div class="input-prepend">
										{!! Form:: text ('others_text', '', array( 'class'=>'form-control form-control1','id'=>'others_text', 'maxlength'=>'55' )) !!}
									</div>
								</div>
								</div>	
								<div class="col-md-12 padding-none space-top reg_input-check">
								<!-- <div class="home-block home-block-login"> -->
									<!-- <div class="tabs"> -->

										<ul class="nav nav-tabs">
									    <li class="active" style="width:25%;" ><a data-toggle="tab" href="#offered" style="font-size:14px; padding:8px 0px !important;" > Services Offered</a></li>
									    <li style="width:25%;" ><a data-toggle="tab" href="#required"  style="font-size:14px; padding:8px 0px !important;">Services Required</a></li>
									    <li style="width:25%;" ><a data-toggle="tab" href="#prod_offered"  style="font-size:14px; padding:8px 0px !important;">Products Offered</a></li>
									    <li style="width:25%;" ><a data-toggle="tab" href="#prod_required"  style="font-size:14px; padding:8px 0px !important;">Products Required</a></li>
									    </ul>
									<!-- </div> -->
									<!-- </div>	 -->	
										<!-- <p class="line-space">
											Services Offered <span class="red">*</span>
										</p> -->

									<!-- <div class="tab-content">
					<div id="buyer" class="tab-pane fade in active"> -->
			<div class="tab-content">
			<div id="offered" class="tab-pane fade active in">
			<span class="error" id="error_services"></span>
			<div class="clearfix"></div>	
			<div class="service_selection">
				<p class="line-space">
				Services Offered<span class="red"></span>
				</p>
			<div class="select_service_block">
												
			<p class="col-md-12 padding-none line-space service-icon-div">Transportation</p>
				@foreach ($services as $service) @if($service->group_name ==
												'Road') @if($service->service_crumb_name ==
												'Transportation')

		       <div class="col-md-6 padding-none pull-left margin-bottom" title="{{strtoupper($service->service_name)}}">
				<input type="checkbox" id="service_{{$service->id}}"
						name="services[]" value="{{$service->id}}"><span
								class="lbl padding-8  pull-left"></span>
													<div class="service-div service-icon-div  pull-left">
														<a><img src="{{url($service->service_image_path)}}" title="{{strtoupper($service->service_name)}}">
															{{$service->service_name}}</a>
														<div class="clearfix"></div>
													</div>
												</div>

												@endif @endif @endforeach @foreach ($services as $service)


												@if($service->group_name == 'Rail')
												@if($service->service_crumb_name == 'Transportation')

												<div class="col-md-12 padding-left-none  margin-bottom" title="{{strtoupper($service->service_name)}}">
													<input type="checkbox" id="service_{{$service->id}}"
														name="services[]" value="{{$service->id}}"><span
														class="lbl padding-8  pull-left"></span>
													<div class="service-div service-icon-div  pull-left">
														<a><img src="{{url($service->service_image_path)}}" title="{{strtoupper($service->service_name)}}">
															{{$service->service_name}}</a>
														<div class="clearfix"></div>
													</div>
												</div>

												@endif @endif @endforeach
												@foreach ($services as $service)


												@if($service->group_name == 'Air')
												@if($service->service_crumb_name == 'Transportation')

												<div class="col-md-6 padding-none pull-left margin-bottom" title="{{strtoupper($service->service_name)}}">
													<input type="checkbox" id="service_{{$service->id}}"
														name="services[]" value="{{$service->id}}"><span
														class="lbl padding-8  pull-left"></span>
													<div class="service-div service-icon-div  pull-left">
														<a><img src="{{url($service->service_image_path)}}" title="{{strtoupper($service->service_name)}}">
															{{$service->service_name}}</a>
														<div class="clearfix"></div>
													</div>
												</div>

												@endif @endif @endforeach

												@foreach ($services as $service)


												@if($service->group_name == 'Ocean')
												@if($service->service_crumb_name == 'Transportation')

												<div class="col-md-12 padding-none pull-left margin-bottom" title="{{strtoupper($service->service_name)}}">
													<input type="checkbox" id="service_{{$service->id}}"
														name="services[]" value="{{$service->id}}"><span
														class="lbl padding-8  pull-left"></span>
													<div class="service-div service-icon-div  pull-left">
														<a><img src="{{url($service->service_image_path)}}" title="{{strtoupper($service->service_name)}}">
															{{$service->service_name}}</a>
														<div class="clearfix"></div>
													</div>
												</div>

												@endif @endif @endforeach @foreach ($services as $service)


												@if($service->group_name == 'Intracity')
												@if($service->service_crumb_name == 'Transportation')

												<div class="col-md-12 padding-none pull-left margin-bottom" title="{{strtoupper($service->service_name)}}">
													<input type="checkbox" id="service_{{$service->id}}"
														name="services[]" value="{{$service->id}}"><span
														class="lbl padding-8  pull-left"></span>
														<div class="service-div service-icon-div  pull-left hyperlocal-image checkserviceseller">
													<a><img src="{{url('images/log-icons/hyper_local.png')}}" title="{{INTRACITY_HYPERLOCAL_IMAGE_TITLE}}" />
														Hyper Local</a>
													<div class="clearfix"></div>
												</div>
													<div class="service-div service-icon-div  pull-left">
														<a><img src="{{url($service->service_image_path)}}" title="{{strtoupper($service->service_name)}}">
															{{$service->service_name}}</a>
														<div class="clearfix"></div>
													</div>
												</div>
												@endif @endif @endforeach @foreach ($services as $service)


												@if($service->group_name == 'Courier')
												@if($service->service_crumb_name == 'Transportation')

												<div class="col-md-12 padding-none pull-left margin-bottom" title="{{strtoupper($service->service_name)}}">
													<input type="checkbox" id="service_{{$service->id}}"
														name="services[]" value="{{$service->id}}"><span
														class="lbl padding-8  pull-left"></span>
													<div class="service-div service-icon-div  pull-left">
														<a><img src="{{url($service->service_image_path)}}" title="{{strtoupper($service->service_name)}}">
															{{$service->service_name}}</a>
														<div class="clearfix"></div>
													</div>
												</div>

												@endif @endif @endforeach

											</div>
											<div class="select_service_block">
												<p
													class="col-md-12 padding-none line-space service-icon-div">Vehicle</p>
												@foreach ($services as $service) @if($service->group_name ==
												'Road') @if($service->service_crumb_name == 'Vehicle')

												<div class="col-md-6 padding-none pull-left margin-bottom" title="{{strtoupper($service->service_name)}}">
													<input type="checkbox" id="service_{{$service->id}}"
														name="services[]" value="{{$service->id}}"><span
														class="lbl padding-8  pull-left"></span>
													<div class="service-div service-icon-div  pull-left">
														<a><img src="{{url($service->service_image_path)}}" title="{{strtoupper($service->service_name)}}">
															{{$service->service_name}}</a>
														<div class="clearfix"></div>
													</div>
												</div>

												@endif @endif @endforeach

											</div>
											<div class="select_service_block border-none">
												<p
													class="col-md-12 padding-none line-space service-icon-div">Relocation</p>
												@foreach ($services as $service) @if($service->group_name ==
												'Relocation') @if($service->service_crumb_name ==
												'Relocation')
													
												<div class="col-md-6 padding-none pull-left margin-bottom" title="{{strtoupper($service->service_name)}}">
													
													<input type="checkbox" id="service_{{$service->id}}"
														name="services[]" value="{{$service->id}}"><span
														class="lbl padding-8  pull-left"></span>
													<div class="service-div service-icon-div  pull-left">
                                                                       <a><img src="{{url($service->service_image_path)}}" title="{{strtoupper($service->service_name)}}">
															{{$service->service_name}}</a>
														<div class="clearfix"></div>
													</div>
												</div>
												@endif @endif @endforeach
											</div>
											<div class="col-md-4 padding-left-none displayNone">
												<p
													class="col-md-12 padding-none line-space service-icon-div">Upcoming</p>
												@foreach ($services as $service) @if($service->group_name ==
												'Upcoming')

												<div class="col-md-12 padding-left-none  margin-bottom " title="{{strtoupper($service->service_name)}}">
													<input type="checkbox" id="service_{{$service->id}}"
														name="services[]" value="{{$service->id}}"><span
														class="lbl padding-8  pull-left"></span>
													<div class="service-div service-icon-div  pull-left">
														<a><img src="{{url($service->service_image_path)}}" title="{{strtoupper($service->service_name)}}">
															{{$service->service_name}}</a>
														<div class="clearfix"></div>
													</div>
												</div>

												@endif @endforeach
											</div>
											<div class="col-sm-12 padding-none space-top">


												<div class="col-xs-12 padding-none displayNone space"
													id="intracityArea">
													<label for="" class="col-md-12 padding-none">For Road
														Intracity only</label>
													<div
														class="normal-select margin-bottom col-md-6 padding-left-none">{!!
														Form::select('intracity_city[]',array('' => 'Select City')
														+ $intracity_cities,null,['class'=>'drop-scroll selectpicker',
														'onChange'=>"getIntraLocality();",
														'id'=>'states_multipleSelect','multiple'=>true]) !!}</div>
													<div
														class="normal-select margin-bottom col-md-6 padding-left-none">
														{!! Form::select('intracity_locality[]',array('' =>
														'Select Locality'),null,['class'=>'drop-scroll
														selectpicker','id'=>'locality_multiple','multiple'=>true])
														!!}</div>
													<span class="error error form-group margin-top"
														id="error_intracity_area"></span>

												</div>

												<div class="col-sm-12 padding-none space-top">

												<div class="col-sm-12 padding-none displayNone" id="pmArea">
													<small>For Relocation only</small><br>
													<div
														class="normal-select margin-bottom col-md-6 padding-left-none">
														{!! Form::select('pm_state[]',array('Default' => 'Select
														State') + $stateList,null,['class'=>'selectpicker',
														'onChange'=>"return getpmCity();",
														'id'=>'states_multiple','multiple'=>true]) !!}</div>
													<div
														class="normal-select margin-bottom col-md-6 padding-left-none">{!!
														Form::select('pm_city[]', array('Default' => 'Select
														City') ,
														null,['class'=>'selectpicker','id'=>'city_multiple','multiple'=>true])
														!!}</div>
													<span class="error error form-group margin-top"
														id="error_pm_area"></span>
												</div>
											</div>

											</div>

										</div>
									</div>
									<div id="required" class="tab-pane fade">
									<span class="error" id="error_services"></span>
										<div class="clearfix"></div>	
										<div class="service_selection">
											<p class="line-space">
														Services Required<span class="red">*</span>
													</p>
											<div class="select_service_block">
													<p
													class="col-md-12 padding-none line-space service-icon-div">Transportation</p>
												@foreach ($services as $service) @if($service->group_name ==
												'Road') @if($service->service_crumb_name ==
												'Transportation')

												<div class="col-md-6 padding-none pull-left margin-bottom" title="{{strtoupper($service->service_name)}}">
													<input type="checkbox" id="service_{{$service->id}}"
														name="servicesreq[]" value="{{$service->id}}"><span
														class="lbl padding-8  pull-left"></span>
													<div class="service-div service-icon-div  pull-left">
														<a><img src="{{url($service->service_image_path)}}" title="{{strtoupper($service->service_name)}}">
															{{$service->service_name}}</a>
														<div class="clearfix"></div>
													</div>
												</div>

												@endif @endif @endforeach @foreach ($services as $service)


												@if($service->group_name == 'Rail')
												@if($service->service_crumb_name == 'Transportation')

												<div class="col-md-12 padding-left-none  margin-bottom" title="{{strtoupper($service->service_name)}}">
													<input type="checkbox" id="service_{{$service->id}}"
														name="servicesreq[]" value="{{$service->id}}"><span
														class="lbl padding-8  pull-left"></span>
													<div class="service-div service-icon-div  pull-left">
														<a><img src="{{url($service->service_image_path)}}" title="{{strtoupper($service->service_name)}}">
															{{$service->service_name}}</a>
														<div class="clearfix"></div>
													</div>
												</div>

												@endif @endif @endforeach
												@foreach ($services as $service)


												@if($service->group_name == 'Air')
												@if($service->service_crumb_name == 'Transportation')

												<div class="col-md-6 padding-none pull-left margin-bottom" title="{{strtoupper($service->service_name)}}">
													<input type="checkbox" id="service_{{$service->id}}"
														name="servicesreq[]" value="{{$service->id}}"><span
														class="lbl padding-8  pull-left"></span>
													<div class="service-div service-icon-div  pull-left">
														<a><img src="{{url($service->service_image_path)}}" title="{{strtoupper($service->service_name)}}">
															{{$service->service_name}}</a>
														<div class="clearfix"></div>
													</div>
												</div>

												@endif @endif @endforeach

												@foreach ($services as $service)


												@if($service->group_name == 'Ocean')
												@if($service->service_crumb_name == 'Transportation')

												<div class="col-md-12 padding-none pull-left margin-bottom" title="{{strtoupper($service->service_name)}}">
													<input type="checkbox" id="service_{{$service->id}}"
														name="servicesreq[]" value="{{$service->id}}"><span
														class="lbl padding-8  pull-left"></span>
													<div class="service-div service-icon-div  pull-left">
														<a><img src="{{url($service->service_image_path)}}" title="{{strtoupper($service->service_name)}}">
															{{$service->service_name}}</a>
														<div class="clearfix"></div>
													</div>
												</div>

												@endif @endif @endforeach @foreach ($services as $service)


												@if($service->group_name == 'Intracity')
												@if($service->service_crumb_name == 'Transportation')

												<div class="col-md-12 padding-none pull-left margin-bottom" title="{{strtoupper($service->service_name)}}">
													<input type="checkbox" id="service_{{$service->id}}"
														name="servicesreq[]" value="{{$service->id}}"><span
														class="lbl padding-8  pull-left"></span>
														<div class="service-div service-icon-div  pull-left hyperlocal-image checkserviceseller">
													<a><img src="{{url('images/log-icons/hyper_local.png')}}" title="{{INTRACITY_HYPERLOCAL_IMAGE_TITLE}}" />
														Hyper Local</a>
													<div class="clearfix"></div>
												</div>
													<div class="service-div service-icon-div  pull-left">
														<a><img src="{{url($service->service_image_path)}}" title="{{strtoupper($service->service_name)}}">
															{{$service->service_name}}</a>
														<div class="clearfix"></div>
													</div>
												</div>
												@endif @endif @endforeach @foreach ($services as $service)


												@if($service->group_name == 'Courier')
												@if($service->service_crumb_name == 'Transportation')

												<div class="col-md-12 padding-none pull-left margin-bottom" title="{{strtoupper($service->service_name)}}">
													<input type="checkbox" id="service_{{$service->id}}"
														name="servicesreq[]" value="{{$service->id}}"><span
														class="lbl padding-8  pull-left"></span>
													<div class="service-div service-icon-div  pull-left">
														<a><img src="{{url($service->service_image_path)}}" title="{{strtoupper($service->service_name)}}">
															{{$service->service_name}}</a>
														<div class="clearfix"></div>
													</div>
												</div>

												@endif @endif @endforeach

											</div>
											<div class="select_service_block">
												<p
													class="col-md-12 padding-none line-space service-icon-div">Vehicle</p>
												@foreach ($services as $service) @if($service->group_name ==
												'Road') @if($service->service_crumb_name == 'Vehicle')

												<div class="col-md-6 padding-none pull-left margin-bottom" title="{{strtoupper($service->service_name)}}">
													<input type="checkbox" id="service_{{$service->id}}"
														name="servicesreq[]" value="{{$service->id}}"><span
														class="lbl padding-8  pull-left"></span>
													<div class="service-div service-icon-div  pull-left">
														<a><img src="{{url($service->service_image_path)}}" title="{{strtoupper($service->service_name)}}">
															{{$service->service_name}}</a>
														<div class="clearfix"></div>
													</div>
												</div>

												@endif @endif @endforeach

											</div>
											<div class="select_service_block border-none">
												<p
													class="col-md-12 padding-none line-space service-icon-div">Relocation</p>
												@foreach ($services as $service) @if($service->group_name ==
												'Relocation') @if($service->service_crumb_name ==
												'Relocation')
													
												<div class="col-md-6 padding-none pull-left margin-bottom" title="{{strtoupper($service->service_name)}}">
													
													<input type="checkbox" id="service_{{$service->id}}"
														name="servicesreq[]" value="{{$service->id}}"><span
														class="lbl padding-8  pull-left"></span>
													<div class="service-div service-icon-div  pull-left">
                                                                       <a><img src="{{url($service->service_image_path)}}" title="{{strtoupper($service->service_name)}}">
															{{$service->service_name}}</a>
														<div class="clearfix"></div>
													</div>
												</div>
												@endif @endif @endforeach
											</div>
											<div class="col-md-4 padding-left-none displayNone">
												<p
													class="col-md-12 padding-none line-space service-icon-div">Upcoming</p>
												@foreach ($services as $service) @if($service->group_name ==
												'Upcoming')

												<div class="col-md-12 padding-left-none  margin-bottom " title="{{strtoupper($service->service_name)}}">
													<input type="checkbox" id="service_{{$service->id}}"
														name="servicesreq[]" value="{{$service->id}}"><span
														class="lbl padding-8  pull-left"></span>
													<div class="service-div service-icon-div  pull-left">
														<a><img src="{{url($service->service_image_path)}}" title="{{strtoupper($service->service_name)}}">
															{{$service->service_name}}</a>
														<div class="clearfix"></div>
													</div>
												</div>

												@endif @endforeach
											</div>
											<div class="col-sm-12 padding-none space-top">


												<div class="col-xs-12 padding-none displayNone space"
													id="intracityArea">
													<label for="" class="col-md-12 padding-none">For Road
														Intracity only</label>
													<div
														class="normal-select margin-bottom col-md-6 padding-left-none">{!!
														Form::select('intracity_city[]',array('' => 'Select City')
														+ $intracity_cities,null,['class'=>'drop-scroll selectpicker',
														'onChange'=>"getIntraLocality();",
														'id'=>'states_multipleSelect','multiple'=>true]) !!}</div>
													<div
														class="normal-select margin-bottom col-md-6 padding-left-none">
														{!! Form::select('intracity_locality[]',array('' =>
														'Select Locality'),null,['class'=>'drop-scroll
														selectpicker','id'=>'locality_multiple','multiple'=>true])
														!!}</div>
													<span class="error error form-group margin-top"
														id="error_intracity_area"></span>

												</div>

												<div class="col-sm-12 padding-none space-top">

												<div class="col-sm-12 padding-none displayNone" id="pmArea">
													<small>For Relocation only</small><br>
													<div
														class="normal-select margin-bottom col-md-6 padding-left-none">
														{!! Form::select('pm_state[]',array('Default' => 'Select
														State') + $stateList,null,['class'=>'selectpicker',
														'onChange'=>"return getpmCity();",
														'id'=>'states_multiple','multiple'=>true]) !!}</div>
													<div
														class="normal-select margin-bottom col-md-6 padding-left-none">{!!
														Form::select('pm_city[]', array('Default' => 'Select
														City') ,
														null,['class'=>'selectpicker','id'=>'city_multiple','multiple'=>true])
														!!}</div>
													<span class="error error form-group margin-top"
														id="error_pm_area"></span>
												</div>
											</div>

											</div>
											

										</div>
									</div>
									<div id="prod_offered" class="tab-pane fade">
										<div class="select_service_block">
									Products Offered
								</div>
									</div>
									<div id="prod_required" class="tab-pane fade">
									<div class="select_service_block">
									Products Required
								</div>
									</div>
								</div>
								</div>		
								<div class="col-md-12 padding-none">
								<table width="75%" border="0" cellspacing="0" cellpadding="0">
							        <tbody><tr>
									
							          <td width="200" valign="bottom">Are you GTA Registered</td>

							          <!--<td width="80" valign="bottom"><input type="checkbox" id="gta_yes" name="gta_yes" value="1"><span class="lbl padding-8">Yes</span></td>
							          <td width="80" valign="bottom"><input type="checkbox" id="gta_no" name="gta_no" value="2" checked><span class="lbl padding-8">No</span></td>-->
									  
									  <td width="80"><div class="radio-block">
										<input type="radio" name="is_gta" id="gta_yes"  value="1"/> 
											<label for="gta_yes"><span></span>Yes</label></td>
										<td width="80"><input type="radio" name="is_gta" id="gta_no" value="2" checked/>
										 <label for="gta_no"><span></span>No</label>
									</div></td>
									  
									 
							          <td valign="bottom">{!! Form:: text ('gta_number', '', array( 'class'=>'form-control form-control1 displayToggle','id'=>'gta_number','placeholder'=>'GTA', 'maxlength'=>'55' )) !!}</td>
							        </tr>
							        <input type="hidden" id="is_seller" name="is_seller" value='0'>
							      </tbody></table>
								<div class="col-md-6 form-control-fld">
									<div class="input-prepend">
										<!-- {!! Form:: text ('others_name', '', array( 'class'=>'form-control form-control1','id'=>'others_name','placeholder'=>'Others (Specify)', 'maxlength'=>'55' )) !!} -->
									</div>
								</div>
								<div class="col-md-12 padding-none">
								<div class="col-md-6 form-control-fld">
									<div class="input-prepend">
										{!! Form:: text ('service_taxno', '', array( 'class'=>'form-control form-control1','id'=>'service_taxno','placeholder'=>'Service Tax No.', 'maxlength'=>'55' )) !!}
									</div>
								</div>
								<div class="col-md-6 form-control-fld">
									<div class="input-prepend">
										{!! Form:: text ('tin_no', '', array( 'class'=>'form-control form-control1','id'=>'tin_no','placeholder'=>'TIN No.', 'maxlength'=>'55' )) !!}
									</div>
								</div>	
								</div>	
								<div class="col-md-12 padding-none">
								<label for="" class="col-md-12 padding-none">Bank Details</label>		
								<div class="col-md-4 form-control-fld">
									<div class="input-prepend">
										{!! Form:: text ('bank_name', '', array( 'class'=>'form-control form-control1','id'=>'bank_name','placeholder'=> 'Bank Name', 'maxlength'=>'55' )) !!}
									</div>
								</div>
								<div class="col-md-4 form-control-fld">
									<div class="input-prepend">
										{!! Form:: text ('bank_branch', '', array( 'class'=>'form-control form-control1','id'=>'bank_branch','placeholder'=>'Bank Branch', 'maxlength'=>'55' )) !!}
									</div>
								</div>	
								<div class="col-md-4 form-control-fld">
									<div class="input-prepend">
										{!! Form:: text ('ifsc_code', '', array( 'class'=>'form-control form-control1','id'=>'ifsc_code','placeholder'=>'IFSC Code', 'maxlength'=>'55' )) !!}
									</div>
								</div>

								</div>	
								<div class="col-md-12 padding-none">
								<div class="col-md-4 form-control-fld">
									<div class="input-prepend">
										{!! Form:: text ('account_type', '', array( 'class'=>'form-control form-control1','id'=>'account_type','placeholder'=> 'Account Type', 'maxlength'=>'55' )) !!}
									</div>
								</div>
								<div class="col-md-4 form-control-fld">
									<div class="input-prepend">
										{!! Form:: text ('account_no', '', array( 'class'=>'form-control form-control1','id'=>'account_no','placeholder'=>'Account Number', 'maxlength'=>'55' )) !!}
									</div>
								</div>	
								<div class="col-md-4 form-control-fld">
									<div class="input-prepend">
										{!! Form:: text ('account_branch', '', array( 'class'=>'form-control form-control1','id'=>'account_branch','placeholder'=>'Branch', 'maxlength'=>'55' )) !!}
									</div>
								</div>

								</div>	
								</div>
								<div class="col-md-12 form-control-fld">
								<br>
									<div class="col-md-8 form-control-fld">
										<input type="checkbox" id="cdbaccept" name="cdbaccept"><span class="lbl padding-8"></span> Logistiks.com Terms and conditions
									</div>
									<div class="col-md-4 form-control-fld space-top pull-right">
										{!! Form::submit('Submit', array( 'class'=>'btn add-btn pull-right', 'id'=>'submit_id')) !!}
									</div>
								</div>
								
							</div>
						</div>	
					{!! Form::close() !!}
					</div>

				</div>
			</div>
			
		</div>

	</div>
</div>
<!-- Confirm Modal window for cancel seller post -->
	<div id="serviceTaxModal" class="modal fade" role="dialog">
		<div class="modal-dialog">

			<!-- Modal content-->
			<div class="modal-content registeration">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">ServiceTax Confirmation</h4>
				</div>
				<div class="modal-body">
				
					<p>Are You sure , you are not a service Tax assessee?</p>
				</div>
				<div class="modal-footer">
					<button type="button" id="serviceTaxYes" class="cancel_serviceTax_yes btn flat-btn red-btn">Yes</button>
					<button type="button" id="serviceTaxNo" id="" class="cancel_serviceTax_no btn flat-btn post-btn">No</button>
				</div>
			</div>

		</div>
	</div>
@include('partials.footer')
</div>
<script></script>
<script>
$(document).ready(function () {
    
  $('#individual-form').validate({ 
    errorClass: "error-1",
	ignore: ':not(select:hidden, input:visible, textarea:visible)',
	
        rules: {
      
           business_mobile_no: {
                required: true,
                mobileNumber: true,
                maxlength: 10
            },
		 
          pincodeLocation: {
                required: true,
                //pincode:true
            },
            
            address1: {
                required: true,
            
            },
            address2: {
                required: true,
               
            },
            year_of_est: {
                required: true,
              
            },
             
             business_emailId: {
                required: true,
                email:true
                
            },
            
             
            business_type_id: {
                required: true
                
            },
            industry_type_name: {
                required: true,
                
            },
             sector_type: {
                //required: true,
                
            },
             business_pan: {
              required :true,
              panCard:true,
			  /*remote:{
					url: '/validatePancard?',
					type: "post"
				}*/
            
            },

            service_taxno: {
              serviceTaxNumber:true
                
            } ,
           
           ifsc_code: {
              //required :true,
              alphanumeric : true
                
            } ,
            tin_no: {
              required :true,
              tinNumber: true
            
            }, 

            cin_no: {
              required :true,
              CinNumber: true
            
            },
            current_turnover: {
            	twoDecimalPlace: true
				//turnovervalidations: true
            },     
                     

            first_year_turnover: {
            	
            	twoDecimalPlace: true
            },
            second_year_turnover: {
            	
            	twoDecimalPlace: true
            },
            third_year_turnover: {
            	
            	twoDecimalPlace: true
            },
			cdbaccept: {
				required: true
			}
       },
	   /*messages: {
			business_pan:{
				remote: "Pancard Already Exists"
			}
		},*/
    errorPlacement: function(error, element) {
          var parentTag = $(element).parent();
      if(parentTag.is('span')) {
        $(element).parent('span').after(error);
      } else {
        $(element).parent('div').after(error);
      }
      
        },
    
        
    });
  

  $("[name^=annual_turnvalue]").each(function () {
        $(this).rules("add", {
            required: true,
            twoDecimalPlace: true
        });
    });

  $('#business_const').change(function(){
  var cin_no = document.getElementById('cin_no')
  
    if($(this).val()=="1" || $(this).val()=="2" || $(this).val()=="3"){
    
      cin_no.style.display = 'block';

    }
    else{
	
      cin_no.style.display = 'none';


    }
  
  var selected_contitution = $("#business_const option:selected" ).val();
  if(selected_contitution == 8) {
    $("#const_business_others").css("display","block");
  } else {
    $("#const_business_others").css("display","none");
  }

 });
	
	
	jQuery.validator.addMethod("alphanumeric", function(value, element) {
		return this.optional(element) || /^\w+$/i.test(value);
	}, "Please enter valid ifsc code");
	jQuery.validator.addMethod("mobileNumber", function(value, element) {
		return this.optional(element) || /([0-9]{10})|(\([0-9]{3}\)\s+[0-9]{3}\-[0-9]{4})/i.test(value);
	}, "Please enter valid mobile number");
	jQuery.validator.addMethod("pincode", function(value, element) {
	  return this.optional(element) || /^\d{6}(?:-\d{4})?$/.test(value);
	}, "Please provide a valid pincode.");

    jQuery.validator.addMethod("panCard", function(value, element) {
      return this.optional(element) || /^[A-Za-z]{5}\d{4}[A-Za-z]{1}$/.test(value);
    }, "Please enter valid Pan number"); 

     jQuery.validator.addMethod("CinNumber", function(value, element) {
  return this.optional(element) || /^[A-Z]{1}\d{5}[A-Z]{2}\d{4}[A-Z]{3}\d{6}$/.test(value);
	}, "Please enter valid CIN Number");

     jQuery.validator.addMethod("twoDecimalPlace", function(value, element) {
  return this.optional(element) || /^\d+(?:\.\d\d?)?$/.test(value);
	}, "Please enter valid integer or decimal number with 2 decimal places.");

     jQuery.validator.addMethod("serviceTaxNumber", function(value, element) {
  return this.optional(element) || /^[A-Z]{5}\d{5}[A-Z]{2}\d{5}$/.test(value);
}, "Please enter valid Service Tax Number"); 

     jQuery.validator.addMethod("tinNumber", function(value, element) {
  return this.optional(element) || /^[A-Z]{4}\d{5}[A-Z]{1}$/.test(value);
}, "Please enter valid TIN number"); 


     $("#contact_fname,#contact_lname,#others_name,#business_designatn,#business_place").keydown(function (e) {
      if (e.altKey) {
       e.preventDefault();
      } else {
       var key = e.keyCode;
       
     if(key != 9) {
     	
       if (!((key == 8) || (key == 32) || (key == 38) || (key >= 35 && key <= 40) || (key >= 65 && key <= 90) || (key == 190))) {
      	
      e.preventDefault();
       }
     }
      }
     });

     $("#current_turnover").keydown(function (e) {
     	
     	
     	if($("#current_turnover").valid() == ""){
     	//e.preventDefault();
     	}
     /* if (e.altKey) {
       e.preventDefault();
      } else {
       var key = e.keyCode;
       
     if(key != 9) {
     	
       if (!((key == 8) || (key == 32) || (key == 38) || (key >= 35 && key <= 40) || (key >= 65 && key <= 90) || (key == 190))) {
      	
      e.preventDefault();
       }
     }
      }*/
     });

   

   
     jQuery.validator.addMethod("businessName", function(value, element) {
  return this.optional(element) || /^\w+$/.test(value);
}, "Letters and underscores only please");  



     /*$("input[name='gta']").change(function(id){
        
        var gta_number = document.getElementById('gta_number');

        //var service_taxno = document.getElementById('service_taxno');
        
        if($("input[id='gta_yes']:checked").length == 1){
          
          gta_number.style.display = 'block';

          //service_taxno.style.display = 'block';
          
        }
        else{
          gta_number.style.display = 'none';

          //service_taxno.style.display = 'none';
        }
    });*/


    $("#business_mobile_no,#business_landline,#pincodeLocation").keypress(function (e) {
      
        if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57) && e.which == 46) {
               return false;
        }

   });


});

    var i=0;

    function autoIncrement(){

    if(i<2) {
       $("#myTable tr:first").clone().find("input").each(function() {
        
        $(this).val('').attr('id', function(_, id) { return id + i });
        $(this).val('').attr('value', function(_, value) { return value + 'Add'});
        
        }).end().appendTo("#myTable");
      i++;
    }

      
    }

    function setTwoNumberDecimal(event) {

    this.value = parseFloat(this.value).toFixed(2);
    
    }

      function getPaging(tab_id){
        
         if(tab_id == 'tab-1')
          var is_seller = 1;

          if(tab_id == 'tab-2')
          var is_seller = 0;

          $('#is_seller').val(is_seller);      

      }



   $('#terms_check').on('click',function(){

      var test=$('input[name="terms_check"]:checked').length ;
    
      if(test ==1){
        
        var e = document.getElementById('submit_id');
        e.style.display = 'block';
      
      }
      else{
       
        var e = document.getElementById('submit_id');
        e.style.display = 'none';

      }

      
    });
/*
  function checkValidation(){
    
   var offered = $('#offeredDiv').filter(function(){  return $(this).find(':checked').length === 0 }).length > 0 ;
   var required = $('#requiredDiv').filter(function(){  return $(this).find(':checked').length === 0 }).length > 0 ;

   if (offered && required) {
   
   alert('Please Select atleast one service');
    
    }

  
  } */
  $(document).ready(function () {
  $(".servicesOffered").on("click", function () {
    var self = $(this).attr('id');
    if($('#'+self).is(':checked')) {
      $("#sellerSelectedService").val(1);
    } else {
      $("#sellerSelectedService").val(0);
    } 
  });
  
  $(".industry_types").on("change",function() {
	var industryTypeId = $(this).val();
    //var industryTypeId = $("#industry_type_name option:selected" ).val();
    var _options = "<option value=''>Sector Type*</option>";
    $.ajax({
      url: "/getSectorTypes/"+industryTypeId, 
	  dataType: 'text',
      success: function(data){
		  
		 if(data == 0) {
			 $('#displayToggleSector').addClass("displayToggle");
			 $('#displayToggleSector').removeClass("displayToggleBlock");
		 } else {
			 $('#displayToggleSector').removeClass("displayToggle");
			 $('#displayToggleSector').addClass("displayToggleBlock");
			 
			$("#sector_type").html(data);
         
			$('.selectpicker').selectpicker('refresh'); 
		 }
		}
    });
    
    
  });

  $("#submit_id").on("click",function(){
      
    if($('#show_validate').css('display') == "block"){
      $('#business_emailId').focus();      
      return false;
    
    }

    if($("#service_taxno").val() == "") { 
		if($("#individual-form").valid()) {
			$("#serviceTaxModal").modal('show');
			return false;
		}
		
    }
	
	
	
    
  });
  $("#serviceTaxNo").on("click",function() {
	 $("#serviceTaxModal").modal('hide');
	 $("#individual-form").valid()
	if($("#service_taxno").val() == "") { 
		return false;
    }
  });	
  
  $("#serviceTaxYes").on("click",function() {
	 $("#serviceTaxModal").modal('hide');
	 if($("#individual-form").valid()) {
		$( "form" ).submit();
	 }	
  });
  
  $("#business_emailId").on("blur",function(){
    var business_emailId = $("#business_emailId").val();
    if(business_emailId != ""){
      var url = "/validateUserEmail";
      var data = "business_emailId=" + business_emailId;
      $.ajax({
        url: url,
        type: "GET",
        data: data,
        success: function (result){
          
          if(result == 'true')
          $('#show_validate').css('display','block');
      	  else
      	  $('#show_validate').css('display','none');	
          
        }
      }); 
    }
  });

  $("#business_pan").on("keyup",function(){
	  $('#show_pan').css('display','none');
  });  

   $("#business_pan").on("blur",function(){
    var business_pan = $("#business_pan").val();
	$('#show_pan').css('display','none');	
	
    if(business_pan != ""){
      var url = "/validatePancard";
      var data = "business_pan=" + business_pan;
      $.ajax({
        url: url,
        type: "GET",
        data: data,
        success: function (result){
          
          if(result == 'true')
          $('#show_pan').css('display','block');
      	  else
      	  $('#show_pan').css('display','none');	
          
        }
      }); 
    }
  });

   $('#service_taxno').on("blur",function(){

   });

  
    
  });
$('#business_pincode1').blur(function(){
          
          var prop_pinid = $('#business_pincode').val();


      $.ajax
            (
              {
                url: '/getPincodeDetails',
                type: "GET", 
                data: "prop_pinid=" + prop_pinid,
                success: function(data)
                {
                   
                   $('#business_location').val(data.postoffice_name);
                   $('#business_city').val(data.divisionname);
                   $('#business_district').val(data.districtname);
                   $('#business_state').val(data.statename);

                   /*$("#principal_place").val(data.districtname);
		        	$("#hidden_user_pincode").val(data.districtname);
					$("#city").val(data.divisionname);
					$("#state").val(data.statename);
					$("#location").val(data.postoffice_name);
					$("#district").val(data.districtname);*/

                   
                },
                error:function()
                {
                  //console.log("AJAX request was a failure");
                }   
              }
            );

    });
	
$("#gta_yes").on("click",function(e) {
	//$('#gta_yes').attr('checked', true);
	
	if($(this).is(':checked')){
		
		$('#gta_no').attr('checked', false);
		$("#gta_number").addClass("displayToggleBlock");
		$("#gta_number").removeClass("displayToggle");
	} 
	
});

$("#gta_no").on("click",function() {
	$('#gta_no').attr('checked', true);
	if($(this).is(':checked')){
		
		$('#gta_yes').attr('checked', false);
		$("#gta_number").addClass("displayToggle");
		$("#gta_number").removeClass("displayToggleBlock");
	} 
});


</script>
@endsection
