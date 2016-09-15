@inject('commonComponent', 'App\Components\CommonComponent')
{{--*/ $serviceId = Session::get('service_id') /*--}}
{{--*/ $slabs =   $commonComponent->getRelocationIntSlabs(); /*--}}	
{{--*/ $from_date=''; /*--}}
{{--*/ $to_date=''; /*--}}
{{--*/ $from_loaction=''; /*--}}
{{--*/ $from_loaction_id=''; /*--}}  
{{--*/ $to_loaction=''; /*--}}
{{--*/ $to_loaction_id=''; /*--}}  
{{--*/ $seller_district_id = ''; /*--}}

@if(Session::has('seller_searchrequest_relocationint_type'))
    {{--*/ $search_relocation_inttype = Session::get('seller_searchrequest_relocationint_type'); /*--}}
    {{--*/ $searchrequest = Session::get('seller_searchrequest_relint_air'); /*--}}
    @if($search_relocation_inttype != "" && $search_relocation_inttype ==1)
        {{--*/ $from_loaction=$searchrequest['from_location']; /*--}}
        {{--*/ $from_loaction_id=$searchrequest['from_location_id']; /*--}}
        {{--*/ $from_date=$searchrequest['valid_from']; /*--}}
        {{--*/ $to_date=$searchrequest['valid_to']; /*--}}
        {{--*/ $to_loaction=$searchrequest['to_location']; /*--}}
        {{--*/ $to_loaction_id=$searchrequest['to_location_id']; /*--}}        
        {{--*/ $seller_district_id=$searchrequest['seller_district_id']; /*--}}
    @endif
@endif


{!! Form::open(['url' => 'relocationsellerpostcreation','id'=>'posts-form-lines_int']) !!}

<div class="col-md-12 inner-block-bg inner-block-bg1 border-top-none padding-top-none">
<div class="col-md-6 form-control-fld padding-none"> 
											<div class="col-md-6 form-control-fld margin-none">
												<div class="input-prepend">
													<span class="add-on"><i class="fa fa-map-marker"></i></span>
													{!! Form::text('from_location', $from_loaction, ['id' => 'from_location_intre','class' => 'form-control', 'placeholder' => 'From Location*']) !!}
                                    				{!! Form::hidden('from_location_id', $from_loaction_id, array('id' => 'from_location_id_intre')) !!}
                                    				{!! Form::hidden('seller_district_id', $seller_district_id, array('id' => 'seller_district_id_intre')) !!}
                                					{!! Form::hidden('int_air_coean', '1', array('id' => 'int_air_coean_air')) !!}
													
													{!! Form::hidden('sellerpoststatus', '', array('id' => 'sellerpoststatus')) !!}
													<input type="hidden" name ='next_terms_count_search' id='next_terms_count_search' value='0'>
												</div>
											</div>

											<div class="col-md-6 form-control-fld margin-none">
												<div class="input-prepend">
													<span class="add-on"><i class="fa fa-map-marker"></i></span>
													{!! Form::text('to_location', $to_loaction, ['id' => 'to_location_intre','class' => 'form-control', 'placeholder' => 'To Location*']) !!}
                                    				{!! Form::hidden('to_location_id', $to_loaction_id, array('id' => 'to_location_id_intre')) !!} 
												</div>
											</div>
											<div class="clearfix"></div>
											

										</div>
										
										<div class="col-md-3 form-control-fld">
											<div class="input-prepend">
												<span class="add-on"><i class="fa fa-calendar-o"></i></span>
												{!! Form::text('valid_from', $from_date, ['id' => 'datepicker_air_re','class' => 'calendar form-control from-date-control','readonly' => true, 'placeholder' => 'Valid From*']) !!}
											</div>
										</div>
										<div class="col-md-3 form-control-fld">
											<div class="input-prepend">
												<span class="add-on"><i class="fa fa-calendar-o"></i></span>
												{!! Form::text('valid_to', $to_date, ['id' => 'datepicker_to_location_air_re','class' => 'calendar form-control to-date-control','readonly' => true, 'placeholder' => 'Valid To*']) !!}
											</div>
										</div>
										<div class="clearfix"></div>
										<div class="col-md-3 form-control-fld ">
		                                	<div class="col-md-8 padding-none">
		                                    <div class="input-prepend">
		                                        <span class="add-on"><i class="fa fa-hourglass-1"></i></span>
		                                        {!! Form::text('transitdays',null,['class'=>'form-control clsIDtransitdaysAir clsCOURTransitDays','id'=>'transitdays','placeholder'=>'Transit Days*']) !!}
		                                       
		                                    </div>
		                                	</div>
		                                	<div class="col-md-4 padding-none">
		                                    <div class="input-prepend">
		                                        <span class="add-on unit-days manage">
		                                                <div class="normal-select">
		                                                    {!! Form::select('units',['Days' => 'Days','Weeks' => 'Weeks'], null, ['class' => 'selectpicker clsSelTransitDaysTypeAir','id'=>'transitdays_units','data-serviceId' => $serviceId, 'data-posttype' => 'Air']) !!}    
		                                                </div>
		                                        </span>
		                                    </div>
		                                	</div>
		                                
		                            	</div>

							
	
									<div class="table-div table-style1 margin-top form-control-grid">
									
									<!-- Table Head Starts Here -->

									<div class="table-heading inner-block-bg">
										<div class="col-md-6 padding-left-none">Weight Bracket (KGs)</div>
										<div class="col-md-3 padding-left-none">Freight Charges (per KG)</div>
										<div class="col-md-3 padding-none">O & D Charges (Rate per CFT)</div>
									</div>

									<!-- Table Head Ends Here -->

									<div class="table-data">
										

									<!-- Table Row Starts Here -->
									<?php $i = 1 ?>
										@foreach($slabs as $slab)
										<div class="table-row inner-block-bg">
											<div class="col-md-6 padding-left-none padding-top">{{$slab->min_slab_weight}}-{{$slab->max_slab_weight}}</div>
											
											<div class="col-md-3 padding-left-none">
												<div class="input-prepend">
													{!! Form::text("freightcharge_$i",null,['class'=>'form-control form-control1 clsRIASFreightChargespKG','placeholder'=>'Freight Charge *']) !!}
													<span class="add-on unit1 manage">per KG</span>
												</div>
											</div>
											<div class="col-md-3 padding-left-none">
												<div class="input-prepend">
													{!! Form::text("odcharges_$i",null,['class'=>'form-control form-control1 clsRIASODChargespCFT','placeholder'=>'O & D Charges *']) !!}
													<span class="add-on unit1 manage">Rate / CFT</span>
												</div>
											</div>
										</div>
										<?php $i++ ?>
										@endforeach
										<!-- Table Row Ends Here -->

										

										
									</div>
								

								<!-- Table Ends Here -->
	

								

								</div>

							</div>


							<div class="col-md-12 inner-block-bg inner-block-bg1">

		                    <h2 class="filter-head1">Additional Charges</h2>
		
							<div class="form-control-fld terms-and-conditions-block">
		                        <div class="col-md-3 padding-none tc-block-fld">
		                            <div class="input-prepend">
		                                <input type="text" name="storate_charges"  class="form-control form-control1 clsRIASStorageCharges"  placeholder ='Storage Charges' id="cancellation3" />
		                                <span class="add-on unit">Rs</span>
		                            </div>
		                        </div>
		                        <div class="col-md-3 tc-block-btn"></div>
		                    </div>
		
		                    <div class="form-control-fld terms-and-conditions-block">
		                        <div class="col-md-3 padding-none tc-block-fld">
		                            <div class="input-prepend">
		                                <input type="text" name="terms_condtion_types1"  class="form-control form-control1 clsRIASCancelCharges"  placeholder ='Cancellation Charges' id="cancellation1" />
		                                <span class="add-on unit">Rs</span>
		                            </div>
		                        </div>
		                        <div class="col-md-3 tc-block-btn"></div>
		                    </div>
		
		                    <div class="my-form">
		                        <div class="text-box form-control-fld terms-and-conditions-block">
		                            <div class="col-md-3 padding-none tc-block-fld">
		                                <div class="input-prepend">
		                                    <input type="text" name="terms_condtion_types2"  placeholder ='Other Charges' class="form-control form-control1 clsRIASOtherCharges"  id="cancellation2" />
		                                    <span class="add-on unit">Rs</span>
		                                </div>
		                            </div>
		                            <div class="col-md-3 tc-block-btn"><input type="button" class="add-box btn add-btn" value="Add"></div>
		                        </div>
		                    </div>
		
		
		
		                    <div class="col-md-6 form-control-fld">
		                          {!! Form::textarea('terms_conditions',null,['class'=>'form-control form-control1 clsRIASAdditionalInfo','id'=>'terms_conditions', 'rows' => 5,'placeholder' => 'Notes to Terms & Conditions (Optional)']) !!}
		                    </div>
		                    <div class="clearfix"></div>
							</div> 


							<div class="col-md-12 inner-block-bg inner-block-bg1">
					
						                    <div class="col-md-3 form-control-fld margin-none">
						                        <div class="normal-select">
						                            {!! Form::select('tracking',(['' => 'Tracking*']+$trackingtypes), null, ['id' => 'tracking_vechile','class' => 'selectpicker form-control']) !!}
						                        </div>
						                    </div>
					                   
					                   		<div class="clearfix"></div>
					
						                    <h2 class="filter-head1">Payment Terms</h2>
						
						                    <div class="col-md-3 form-control-fld">
						                        <div class="normal-select">
						                            {!! Form::select('paymentterms', ($paymentterms), null, ['class' => 'selectpicker','id' => 'payment_options']) !!}
						                        </div>
						                    </div>
					
						                    <div class="col-md-12 form-control-fld" id="show_advanced_period">
						                        <div class="check-block">
						                            <div class="checkbox_inline">{!! Form::checkbox('accept_payment[]', 1, '', false, ['class' => 'accept_payment']) !!}<span class="lbl padding-8">NEFT/RTGS</span></div>
						                            <div class="checkbox_inline">{!! Form::checkbox('accept_payment[]', 2, '', false, ['class' => 'accept_payment']) !!}<span class="lbl padding-8">Credit Card</span></div>
						                            <div class="checkbox_inline">{!! Form::checkbox('accept_payment[]', 3, '', false, ['class' => 'accept_payment']) !!}<span class="lbl padding-8">Debit Card</span></div>
						
						                        </div>
						                    </div>
					
						                    <div class="col-md-12 form-control-fld" style="display: none;" id="show_credit_period">
						                        <div class="col-md-3 form-control-fld padding-left-none">
						                        	
						                        	<div class="col-md-7 padding-none">
						                        		<div class="input-prepend">
						                                {!! Form::text('credit_period',null,['class'=>'form-control form-control1 clsIDCredit_periodAir clsCreditPeriod','placeholder'=>'Credit Period']) !!}
						                                
						                            </div>
						                        	</div>
						                        	<div class="col-md-5 padding-none">
						                        		<div class="input-prepend">
						                        			<span class="add-on unit-days">
						                                            <div class="normal-select">
						                                                {!! Form::select('credit_period_units',['Days' => 'Days','Weeks' => 'Weeks'], null, ['class' => 'selectpicker clsSelPaymentCreditTypeAir bs-select-hidden', 'data-posttype' => 'Air']) !!}       
						                                            </div>
						                                        </span>
						                        		</div>
						                        	</div>
						                        
						                            
						                            
						                        </div>
						
						                        <div class="col-md-12 padding-none">
						                            <div class="checkbox_inline">
						                                {!! Form::checkbox('accept_credit_netbanking[]', 1, false) !!}<span class="lbl padding-8">Net Banking</span>
						                                {!! Form::checkbox('accept_credit_netbanking[]', 2, false) !!}<span class="lbl padding-8">Cheque / DD</span>
						                            </div>
						                        </div>
						                    </div>
					
					                    <div class="clearfix"></div>
					          </div>



									<div class="col-md-12 inner-block-bg inner-block-bg1">
					                    <div class="col-md-12 form-control-fld">
					                        <div class="radio-block">
					                            <div class="radio_inline"><input type="radio" name="optradio" id="post-public" value="1" checked="checked" class="create-posttype-service" /> <label for="post-public"><span></span>Post Public</label></div>
					                            <div class="radio_inline"><input type="radio" name="optradio" id="post-private" value="2" class="create-posttype-service" /> <label for="post-private"><span></span>Post Private</label></div>
					                        </div>
					                    </div>
				
					                    <div class="col-md-3 form-control-fld demo-input_buyers padding-right-none" style="display:none">
						                    <div class="input-prepend">
						                        <input type="hidden" id="demo-input" name="buyer_list_for_sellers" />
						                    </div>
					                    </div>
				
				                    	<div class="clearfix"></div>
					                    <div class="check-box form-control-fld margin-none">
					                    {!! Form::checkbox('agree', 1, false,array('id'=>'agree')) !!}<span class="lbl padding-8">Accept Terms & Conditions (Digital Contract)</span>
					                    </div>
				                	</div>

								<div class="col-md-12 padding-none">
									{!! Form::submit('Confirm', ['class' => 'btn theme-btn flat-btn pull-right','name' => 'confirm','id' => 'add_quote_seller_id_int','onclick'=>"updatepoststatus(1)"]) !!}
			                    	{!! Form::submit('Save as Draft', ['class' => 'btn add-btn flat-btn pull-right','name' => 'draft','id' => 'add_quote_seller_int','onclick'=>"updatepoststatus(0)"]) !!}
								</div>
				{!! Form::close() !!}			
