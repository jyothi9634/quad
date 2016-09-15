@extends('app')
@section('content')

<!-- Page top navigation Starts Here-->
@include('partials.page_top_navigation')
{{--*/ $serviceId = Session::get('service_id') /*--}}
        <div class="main">

            <div class="container">
                <span class="pull-left"><h1 class="page-title">Post (Truck Haul)</h1><a class="change-service" data-toggle="modal" data-target="#change-service">Change Service</a></span>
                @if ($url_search_search == 'buyersearchresults')
                <span class="pull-right"><a href="{{ $serverpreviUrL  }}" class="back-link">Back to Search</a></span>
                @endif

                <div class="clearfix"></div>

<div class="col-md-12 inner-block-bg single-layout padding-none margin-bottom-none">
                    
                   <div class="col-md-12 inner-block-bg inner-block-bg1 margin-bottom-none border-bottom-none padding-bottom-none">
                    <div class="col-md-12 padding-none inner-form margin-top">
					{!! Form::open(['url' => 'truckhaul/addsellerpost','id'=>'truckhaul-posts-form-lines']) !!}
                            <div class="col-md-3 form-control-fld padding-left-none">
                                <div class="input-prepend">
                                    <span class="add-on"><i class="fa fa-calendar-o"></i></span>
                                    {!! Form::text('valid_from', $session_search_values_create[1], ['id' => 'datepicker','class' => 'calendar form-control from-date-control clsTHValidfrom','readonly' => true, 'placeholder' => 'Valid From*']) !!}
                                </div>
                            </div>
                            <div class="col-md-3 form-control-fld">
                                <div class="input-prepend">
                                    <span class="add-on"><i class="fa fa-calendar-o"></i></span>
                                    {!! Form::text('valid_to', $session_search_values_create[0], ['id' => 'datepicker_to_location','class' => 'calendar form-control to-date-control clsTHValidTo','readonly' => true, 'placeholder' => 'Valid To*']) !!}
                                </div>
                            </div>

                            <div class="clearfix"></div>

                            <div class="col-md-3 form-control-fld padding-left-none">
                                <div class="input-prepend">
                                    <span class="add-on"><i class="fa fa-map-marker"></i></span>
                                    {!! Form::text('from_location', $session_search_values_create[6], ['id' => 'from_location','class' => 'form-control clsTHFromLocation', 'placeholder' => 'From Location*']) !!}
                                    {!! Form::hidden('from_location_id', $session_search_values_create[4], array('id' => 'from_location_id')) !!}
                                    {!! Form::hidden('seller_district_id', $session_search_values_create[8], array('id' => 'seller_district_id')) !!}
                                </div>
                            </div>
                            <div class="col-md-3 form-control-fld">
                                <div class="input-prepend">
                                    <span class="add-on"><i class="fa fa-map-marker"></i></span>
                                    {!! Form::text('to_location', $session_search_values_create[7], ['id' => 'to_location','class' => 'form-control clsTHToLocation', 'placeholder' => 'To Location*']) !!}
                                    {!! Form::hidden('to_location_id', $session_search_values_create[5], array('id' => 'to_location_id')) !!}
                                </div>
                            </div>
                           
                            <div class="col-md-3 form-control-fld">
                                <div class="input-prepend">
                                    <span class="add-on"><i class="fa fa-archive"></i></span>
                                    {!! Form::select('LoadTypeMasters', (['11'=>'Load Type (Any)'] + $loadtypemasters), $session_search_values_create[3], ['class' => 'selectpicker form-control','id' => 'load_type']) !!}
                                </div>
                            </div>

                            <div class="col-md-3 form-control-fld padding-right-none">
                                <div class="input-prepend">
                                    <span class="add-on"><i class="fa fa-truck"></i></span>
                                    {!! Form::select('VehicleTypeMasters', (['' => 'Select Vehicle Type*'] + $vehicletypemasters), $session_search_values_create[2], ['class' => 'selectpicker form-control','id' => 'vechile_type']) !!}
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <div class="col-md-3 form-control-fld">
                                <div class="input-prepend">
                                    <span class="add-on"><i class="fa fa-truck "></i></span>
                                    {!! Form::text('vehicle_number',$session_search_values_create[11],['class'=>'form-control clsVehicleno','id'=>'vehicle_number','placeholder'=>'Vehicle Number*']) !!}
                                </div>
                            </div>
                            <div class="col-md-3 form-control-fld padding-left-none">
                                <div class="col-md-8 padding-none">
                                    <div class="input-prepend">
                                        <span class="add-on"><i class="fa fa-hourglass-1"></i></span>
                                        {!! Form::text('transitdays',$session_search_values_create[10],['class'=>'form-control clsIDtransitdays clsCOURTransitDays','id'=>'transitdays','placeholder'=>'Transit Days*']) !!}
                                       
                                    </div>
                                </div>
                                <div class="col-md-4 padding-none">
                                    <div class="input-prepend">
                                        <span class="add-on unit-days manage">
                                                <div class="normal-select">
                                                    {!! Form::select('units',['Days' => 'Days','Weeks' => 'Weeks'], null, ['class' => 'selectpicker clsSelTransitDaysType','id'=>'transitdays_units','data-serviceId' => $serviceId]) !!}    
                                                </div>
                                        </span>
                                    </div>
                                </div>
                                
                            </div>

                                <div class="col-md-2 form-control-fld">
                                    <div class="input-prepend">
                                        <span class="add-on"><i class="fa fa-rupee "></i></span>
                                        {!! Form::text('price',$session_search_values_create[9],['class'=>'form-control clsTHRate','id'=>'price','placeholder'=>'Rate*']) !!}
                                    </div>   
                                </div>
                               


                           
                            <div class="col-md-3 form-control-fld">
                                <input type="button" id="add_more_th" value="Add" class="btn add-btn">
                            </div>
                            {!!	Form::hidden('update_ftl_seller_line',0,array('class'=>'','id'=>'update_ftl_seller_line'))!!}
							{!!	Form::hidden('update_ftl_seller_row_count','',array('class'=>'','id'=>'update_ftl_seller_row_count'))!!}
                            {!! Form::close() !!}
                    </div>
                    
                    </div>
                    
                    
 					<div class="clearfix"></div>
                   {!! Form::open(['url' => 'truckhaul/addsellerpost','id'=>'truckhaul-posts-form']) !!}
                    <div class="col-md-12 inner-block-bg inner-block-bg1 border-top-none padding-top-none padding-bottom-none">
                        <div class="main-inner">
                            <!-- Right Section Starts Here -->
                            <div class="main-right">
                                <!-- Table Starts Here -->
                                <div class="table-div table-style1 padding-none">
                                    <!-- Table Head Starts Here -->
                                    <div class="table-heading inner-block-bg">
                                        <div class="col-md-2 padding-left-none">From</div>
                                        <div class="col-md-2 padding-left-none">To</div>
                                        <div class="col-md-2 padding-left-none">Load Type</div>
                                        <div class="col-md-2 padding-left-none">Vehicle Type</div>
                                        <div class="col-md-2 padding-left-none">Vehicle Number</div>
                                        <div class="col-md-1 padding-left-none">Price</div>
                                        <div class="col-md-1 padding-left-none"></div>
                                    </div>
                                    <!-- Table Head Ends Here -->
                                    <div class="table-data">
                                      
                                        <input type="hidden" id='next_add_more_id' value='0'>
                                        <div id ="multi-line-itemes">
                                            <div class="table-data request_rows" id=""></div>
                                        </div>

                                    </div>
                                </div>   
                            </div>
                        </div>
                    <div class="clearfix"></div>
                   </div>
                    {!! Form::hidden('ftl_order_id', $session_search_values_create[12], array('id' => 'ftl_order_id')) !!}
                    {!! Form::hidden('ftl_flag_set', $session_search_values_create[13], array('id' => 'ftl_flag_set')) !!}
                    {!! Form::hidden('service_id', '1', array('id' => 'service_id')) !!}
                    {!! Form::hidden('valid_from_val', '', array('id' => 'valid_from_val')) !!}
                    {!! Form::hidden('labeltext[]', 'Cancellation Charges', array('id' => '')) !!}
                    {!! Form::hidden('labeltext[]', 'Other Charges', array('id' => '')) !!}
                    {!! Form::hidden('valid_to_val', '', array('id' => 'valid_to_val')) !!}
                    {!! Form::hidden('sellerpoststatus', '', array('id' => 'sellerpoststatus')) !!}
                    {!! Form::hidden('subscription_start_date_start', $subscription_start_date_start, array('id' => 'subscription_start_date_start')) !!}
                    {!! Form::hidden('subscription_end_date_end', $subscription_end_date_end, array('id' => 'subscription_end_date_end')) !!}
                    {!! Form::hidden('current_date_seller', $current_date_seller, array('id' => 'current_date_seller')) !!}
                    <input type="hidden" name ='next_terms_count_search' id='next_terms_count_search' value='0'>
        

            <div class="col-md-12 inner-block-bg inner-block-bg1">

                    <br>
                    <div class="col-md-3 form-control-fld margin-none">
                        <div class="normal-select">
                            {!! Form::select('tracking',(['' => 'Tracking*']+$trackingtypes), null, ['id' => 'tracking_vechile','class' => 'selectpicker form-control']) !!}
                        </div>
                    </div>
                   
                   <div class="clearfix"></div>

                    <h2 class="filter-head1">Payment Terms</h2>

                    <div class="col-md-3 form-control-fld">
                        <div class="normal-select">
                            {!! Form::select('Payment Terms', ($PaymentTerms), null, ['class' => 'selectpicker','id' => 'payment_options']) !!}
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
                                {!! Form::text('credit_period',null,['class'=>'form-control form-control1 numericvalidation','placeholder'=>'Credit Period']) !!}
                                
                            </div>
                        	</div>
                        	<div class="col-md-5 padding-none">
                        		<div class="input-prepend">
                        			<span class="add-on unit-days">
                                            <div class="normal-select">
                                                {!! Form::select('credit_period_units',['Days' => 'Days','Weeks' => 'Weeks'], null, ['class' => 'selectpicker bs-select-hidden']) !!}       
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

                    <h2 class="filter-head1">Additional Charges</h2>

                    <div class="form-control-fld terms-and-conditions-block">
                        <div class="col-md-3 padding-none tc-block-fld">
                            <div class="input-prepend">
                                <input type="text" name="terms_condtion_types1"  class="form-control form-control1 clsTHCancelCharges" placeholder ='Cancellation Charges' id="cancellation1" />
                                <span class="add-on unit">Rs</span>
                            </div>
                        </div>
                        <div class="col-md-3 tc-block-btn"></div>
                    </div>

                    <div class="my-form">
                        <div class="text-box form-control-fld terms-and-conditions-block">
                            <div class="col-md-3 padding-none tc-block-fld">
                                <div class="input-prepend">
                                    <input type="text" name="terms_condtion_types2"  placeholder ='Other Charges' class="form-control form-control1 clsTHOtherCharges" id="cancellation2" />
                                    <span class="add-on unit">Rs</span>
                                </div>
                            </div>
                            <div class="col-md-3 tc-block-btn"><input type="button" class="add-box btn add-btn" value="Add"></div>
                        </div>
                    </div>



                    <div class="col-md-6 form-control-fld">
                          {!! Form::textarea('terms_conditions',null,['class'=>'form-control form-control1 clsTermsConditions','id'=>'terms_conditions', 'rows' => 5,'placeholder' => 'Notes to Terms & Conditions (Optional)']) !!}
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
                    {!! Form::checkbox('agree', '', '',array('id'=>'agree')) !!}<span class="lbl padding-8">Accept Terms & Conditions (Digital Contract)</span>
                    </div>
                </div>

                <div class="clearfix"></div>

                <div class="col-md-12 padding-none">
                    {!! Form::submit('Confirm', ['class' => 'btn theme-btn flat-btn pull-right','name' => 'confirm','id' => 'add_quote_seller_id_th','onclick'=>"updatepoststatus(1)"]) !!}
                    {!! Form::submit('Save as Draft', ['class' => 'btn add-btn flat-btn pull-right','name' => 'draft','id' => 'add_quote_seller_th','onclick'=>"updatepoststatus(0)"]) !!}
                   
                </div>


            </div>
        </div>
        </div>
        {!! Form::close() !!}

        @include('partials.footer')
@endsection