@extends('app')
@section('content')

 {{--*/ $from_date=''; /*--}}
  {{--*/ $to_date=''; /*--}}
  {{--*/ $from_loaction=''; /*--}}
  {{--*/ $from_loaction_id=''; /*--}}  
@if(Session::has('seller_searchrequest_officemove'))
    {{--*/ $searchrequest=Session::get('seller_searchrequest_officemove'); /*--}}
    @if($searchrequest != "")
        {{--*/ $from_loaction=$searchrequest['from_location']; /*--}}
        {{--*/ $from_loaction_id=$searchrequest['from_location_id']; /*--}}
        {{--*/ $from_date=$searchrequest['valid_from']; /*--}}
        {{--*/ $to_date=$searchrequest['valid_to']; /*--}}
    @endif
@endif
        <!-- Page top navigation Starts Here-->
@include('partials.page_top_navigation')

    <div class="main">
		@if(Session::has('message_create_post_duplicate') && Session::get('message_create_post_duplicate')!='')
			<div class="flash">
				<p class="text-success col-sm-12 text-center flash-txt alert-success">
					{{ Session::get('message_create_post_duplicate') }}
				</p>
			</div>
		@endif
        <div class="container">
            <span class="pull-left"><h1 class="page-title">Post (Relocation Office)</h1><a class="change-service" data-toggle="modal" data-target="#change-service">Change Service</a></span>
            @if ($url_search_search == 'buyersearchresults')
            <span class="pull-right"><a href="/sellersearchbuyers" class="back-link">Back to Search</a></span>
			@endif

            <div class="clearfix"></div>

            <div class="col-md-12 padding-none">
                <div class="main-inner">


                    <!-- Right Section Starts Here -->

                  <div class="main-right">
						{!! Form::open(['url' => 'relocationsellerpostcreation','id'=>'posts-form_relocation_office', 'autocomplete'=>'off']) !!}
						{!! Form::hidden('sellerpoststatus', '1', array('id' => 'sellerpoststatus')) !!}

									<div class="gray-bg">
										<div class="col-md-3 form-control-fld">
											<div class="input-prepend">
												<span class="add-on"><i class="fa fa-calendar-o"></i></span>
												{!! Form::text('valid_from', $from_date, ['id' => 'datepicker','class' => 'calendar form-control from-date-control','readonly' => true, 'placeholder' => 'Valid From*']) !!}
												 
											</div>
										</div>
										<div class="col-md-3 form-control-fld">
											<div class="input-prepend">
												<span class="add-on"><i class="fa fa-calendar-o"></i></span>
												{!! Form::text('valid_to', $to_date, ['id' => 'datepicker_to_location','class' => 'calendar form-control to-date-control','readonly' => true, 'placeholder' => 'Valid To*']) !!}
											</div>
										</div>

										<div class="clearfix"></div>

										<div class="col-md-3 form-control-fld">
											<div class="input-prepend">
												<span class="add-on"><i class="fa fa-map-marker"></i></span>
												{!! Form::text('from_location', $from_loaction, ['id' => 'from_location','class' => 'form-control', 'placeholder' => 'City*']) !!}
                                        		{!! Form::hidden('from_location_id', '', array('id' => 'from_location_id')) !!}
                                        		{!! Form::hidden('seller_district_id', '', array('id' => 'seller_district_id')) !!}
											</div>
										</div>


										<div class="col-md-3 form-control-fld">
											<div class="input-prepend">
												{!! Form::text('rate_per_cft',null,['class'=>'form-control form-control1 clsROMODChargespCFT','id'=>'rate_per_cft','placeholder'=>'O & D Charges (Rate / CFT)*']) !!}
											</div>
										</div>

										
										
									

									<div class="clearfix"></div>

									<div class="col-md-12 padding-none">
								<!-- Table Starts Here -->

								<div class="table-div table-style1">
									
									<!-- Table Head Starts Here -->

									<div class="table-heading inner-block-bg">
										<div class="col-md-3 padding-left-none">Min (KM)</div>
										<div class="col-md-3 padding-left-none">Max (KM)</div>
										<div class="col-md-3 padding-left-none">Transport Charges (Rate/KM)</div>
									</div>

									<!-- Table Head Ends Here -->

									<div class="table-data form-control-fld padding-none slabtable price-slap-add">
										

										<!-- Table Row Starts Here -->

										<div class="add-price-slap table-row inner-block-bg">
    										<div class="price-slap">
    											<div class="col-md-3 form-control-fld">
                                                        <div class="input-prepend">
                                                    {!! Form::text('min_distance_slab',0,['class'=>'form-control form-control1 clsROMMinKm','id'=>'min_distance_slab','placeholder'=>'Min Distane','readonly'=>true,'required'=>true]) !!}
                                                    </div>
                                                </div>
    											<div class="col-md-3 form-control-fld">
                                                        <div class="input-prepend">
                                                    {!! Form::text('max_distance_slab',null,['class'=>'form-control form-control1 clsROMMaxKm','id'=>'max_distance_slab','placeholder'=>'Max Distance','required'=>true]) !!}
                                                        </div>
                                                </div>
    											<div class="col-md-3 form-control-fld">
                                                    <div class="input-prepend">
                                                    {!! Form::text('transport_charges_slab',null,['class'=>'form-control form-control1 clsROMTransportChargespKm','id'=>'transport_charges_slab','placeholder'=>'Transport Charges','required'=>true]) !!}
                                                    </div>
                                                </div>
    											<div class="col-md-1 form-control-fld padding-left-none">
    											    <button type="button" class="btn add-btn slab-box">Add</button>
    										    </div>
    										</div>
										</div>
										<input type="hidden" name ='price_slap_hidden_value' id='price_slap_hidden_value' value='0'>	
										<!-- Table Row Ends Here -->
									</div>
									
								</div>	

								<!-- Table Starts Here -->
							</div>

							</div>

							<div class="col-md-12 inner-block-bg inner-block-bg1">
								<div class="col-md-12 padding-none filter">
                                <h2 class="filter-head1 margin-bottom">Additional Charges</h2>
                                
                                <div class="terms-and-conditions-block">
                                    {!! Form::hidden('next_terms_count_search', '0', array('id' => 'next_terms_count_search')) !!}
                                    <div class="col-md-3 form-control-fld">
                                        <div class="input-prepend">
                                            {!! Form::text('cancellation_charge_price','',['class'=>'form-control form-control1 clsROMCancelCharges','id'=>'cancellation_charge_price','placeholder'=>'Cancellation Charges']) !!}
                                            <span class="add-on unit1 manage">Rs</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3 tc-block-btn"></div>
                                </div>
                                <div class="my-form">
                                    <div class=" text-box form-control-fld terms-and-conditions-block padding-none">
                                        <div class="col-md-3 form-control-fld">
                                            <div class="input-prepend">
                                                {!! Form::text('docket_charge_price','',['class'=>'form-control form-control1 clsROMOtherCharges','id'=>'docket_charge_price','placeholder'=>'Other Charges']) !!}
                                                <span class="add-on unit1 manage">Rs</span>
                                            </div>
                                        </div>
                                        <div class="col-md-3 tc-block-btn"><input type="button" class="add-box btn add-btn" value="Add"></div>
                                    </div>
                                </div>
                                <div class="col-md-6 form-control-fld">
                                    {!! Form::textarea('terms_conditions',null,['class'=>'form-control form-control1 clsTermsConditions','id'=>'terms_conditions', 'rows' => 5,'placeholder' => 'Notes to Terms & Conditions (Optional)']) !!}
                                </div>

                            </div>
								</div>


								<div class="col-md-12 inner-block-bg inner-block-bg1">

										  <div class="col-md-3 form-control-fld margin-top">
                                <div class="normal-select">
                                    {!! Form::select('tracking',(['' => 'Tracking*']+$trackingtypes), null, ['id' => 'tracking_ptl','class' => 'selectpicker']) !!}
                                </div>
                            </div>

                            <div class="clearfix"></div>

                            <h2 class="filter-head1">Payment Terms</h2>

                            <div class="col-md-3 form-control-fld">
                                <div class="normal-select">
                                    {!! Form::select('paymentterms', ($paymentterms), null, ['class' => 'selectpicker','id' => 'payment_options']) !!}
                                </div>
                            </div>

                            <div class="col-md-12 form-control-fld" id = 'show_advanced_period'>
                                <div class="check-block">
                                    <div class="checkbox_inline">
                                        {!! Form::checkbox('accept_payment_ptl[]', 1, '', false, ['class' => 'accept_payment_ptl']) !!} <span class="lbl padding-8">NEFT/RTGS</span>
                                    </div>
                                    <div class="checkbox_inline">
                                        {!! Form::checkbox('accept_payment_ptl[]', 2, '', false, ['class' => 'accept_payment_ptl']) !!} <span class="lbl padding-8">Credit Card</span>
                                    </div>
                                    <div class="checkbox_inline">
                                        {!! Form::checkbox('accept_payment_ptl[]', 3, '', false, ['class' => 'accept_payment_ptl']) !!} <span class="lbl padding-8">Debit Card</span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12 form-control-fld" style ='display: none;' id = 'show_credit_period'>
                                <div class="col-md-3 form-control-fld padding-left-none">

                                    <div class="col-md-7 padding-none">
                                        <div class="input-prepend">
                                            {!! Form::text('credit_period_ptl',null,['class'=>'form-control form-control1 clsIDCredit_period clsCreditPeriod','placeholder'=>'Credit Period']) !!}
                                        </div>
                                    </div>
                                    <div class="col-md-5 padding-none">
                                        <div class="input-prepend">
								<span class="add-on unit-days manage">
											<div class="normal-select">
                                                {!! Form::select('credit_period_units',['Days' => 'Days','Weeks' => 'Weeks'], null, ['class' => 'selectpicker clsSelPaymentCreditType bs-select-hidden']) !!}
                                            </div>
										</span>
                                        </div>
                                    </div>


                                </div>
                                <div class="col-md-12 padding-none">
                                    <div class="checkbox_inline">

                                        {!! Form::checkbox('accept_credit_netbanking[]', 1, false) !!} <span class="lbl padding-8">Net Banking</span>
                                    </div>
                                    <div class="checkbox_inline">
                                        {!! Form::checkbox('accept_credit_netbanking[]', 2, false) !!} <span class="lbl padding-8">Cheque / DD</span>
                                    </div>

                                </div>
                            </div>



                            <div class="clearfix"></div>

                        </div>



                        <div class="col-md-12 inner-block-bg inner-block-bg1">
                            <div class="col-md-12 form-control-fld margin-none">
                                <div class="radio-block">
                                    <div class="radio_inline"><input type="radio" name="optradio" id="post-public" value="1" checked="checked" class="create-posttype-service" /> <label for="post-public"><span></span>Post Public</label></div>
                                    <div class="radio_inline"><input type="radio" name="optradio" id="post-private" value="2" class="create-posttype-service" /> <label for="post-private"><span></span>Post Private</label></div>
                                </div>
                            </div>
                            <div class="col-md-3 form-control-fld demo-input_buyers" style="display:none">
                                <div class="input-prepend">
                                    <input type="hidden" id="demo-input" name="buyer_list_for_sellers" class="form-control" />
                                </div>
                            </div>


                            <div class="clearfix"></div>
                            <div class="check-box form-control-fld margin-none">
                                {!! Form::checkbox('agree', 1, false,array('id'=>'agree')) !!}<span class="lbl padding-8">Accept Terms & Conditions (Digital Contract)</span>
                            </div>
                        </div>

                        <div class="col-md-12 padding-none">
                            <div class="col-md-12 padding-none">
                                {!! Form::submit('Confirm', ['class' => 'btn theme-btn flat-btn pull-right','name' => 'confirm','id' => 'add_quote_seller_id_relocation_office','onclick'=>"updatepoststatus(1)"]) !!}
                                {!! Form::submit('Save as draft', ['class' => 'btn add-btn flat-btn pull-right','name' => 'draft','id' => 'add_quote_seller_relocation_office','onclick'=>"updatepoststatus(0)"]) !!}
                            </div>
                        </div>
                        {!! Form::close() !!}
						<!-- Right Section Ends Here -->

                </div>
            </div>

            <div class="clearfix"></div>

        </div>
    </div>










@include('partials.footer')
@endsection



