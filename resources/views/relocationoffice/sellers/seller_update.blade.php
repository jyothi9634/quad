@extends('app')
@section('content')
@inject('common', 'App\Components\CommonComponent')
        <!-- Page top navigation Starts Here-->
@include('partials.page_top_navigation')

    <div class="main">

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
						{!! Form::open(['url' => 'relocation/updatesellerpost/'.$post_details->id,'id'=>'posts-form_relocation_office', 'autocomplete'=>'off']) !!}
						{!! Form::hidden('sellerpoststatus', '1', array('id' => 'sellerpoststatus')) !!}
                        {!! Form::hidden('update_id', $post_details->id, array('id' => 'update_id')) !!}

									<div class="gray-bg">
										<div class="col-md-3 form-control-fld">
											<div class="input-prepend">
												<span class="add-on"><i class="fa fa-calendar-o"></i></span>
												{!! Form::text('valid_from', $common->checkAndGetDate($post_details->from_date), ['id' => 'datepicker','class' => 'calendar form-control from-date-control','readonly' => true, 'placeholder' => 'Valid From*','disabled' => true]) !!}
												{!! Form::hidden('valid_from', $common->checkAndGetDate($post_details->from_date), array('id' => 'valid_from')) !!} 
											</div>
										</div>
										<div class="col-md-3 form-control-fld">
											<div class="input-prepend">
												<span class="add-on"><i class="fa fa-calendar-o"></i></span>
												{!! Form::text('valid_to', $common->checkAndGetDate($post_details->to_date), ['id' => 'datepicker_to_location','class' => 'calendar form-control to-date-control','readonly' => true, 'placeholder' => 'Valid To*','disabled' => true]) !!}
                                                {!! Form::hidden('valid_to', $common->checkAndGetDate($post_details->to_date), array('id' => 'valid_to')) !!} 
											</div>
										</div>

										<div class="clearfix"></div>

										<div class="col-md-3 form-control-fld">
											<div class="input-prepend">
												<span class="add-on"><i class="fa fa-map-marker"></i></span>
												{!! Form::text('from_location', $common->getCityName($post_details->from_location_id), ['id' => 'from_location','class' => 'form-control', 'placeholder' => 'City*','disabled' => true]) !!}
                                                {!! Form::hidden('from_location', $common->getCityName($post_details->from_location_id), array('id' => 'from_location')) !!} 
                                        		{!! Form::hidden('from_location_id', $post_details->from_location_id, array('id' => 'from_location_id')) !!}
                                        		{!! Form::hidden('seller_district_id', '', array('id' => 'seller_district_id')) !!}
											</div>
										</div>


										<div class="col-md-3 form-control-fld">
											<div class="input-prepend">
												{!! Form::text('rate_per_cft',$post_details->rate_per_cft,['class'=>'form-control form-control1 clsROMODChargespCFT','id'=>'rate_per_cft','placeholder'=>'O & D Charges (Rate / CFT)*']) !!}
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
                                        {{--*/ $s = 0 /*--}}
                                        @foreach($post_slabs as $slab)
                                            @if($s==0)
                                                {{--*/ $index = ""/*--}}
                                                {{--*/ $remove = ""/*--}}
                                            @else
                                                {{--*/ $index = "_".$s/*--}}
                                                {{--*/ $remove = "remove_item_".$s/*--}}
                                            @endif    
                                            <div id="{{$remove}}" class="add-price-slap table-row inner-block-bg">
                                                <div class="price-slap">
                                                    <div class="col-md-3 form-control-fld">
                                                        <div class="input-prepend">
                                                            {!! Form::text('min_distance_slab'.$index,$slab->slab_min_km,['class'=>'form-control form-control1 clsROMMinKm','id'=>'min_distance_slab'.$index,'placeholder'=>'Min Distane','readonly'=>true,'required'=>true]) !!}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3 form-control-fld">
                                                         <div class="input-prepend">
                                                            {!! Form::text('max_distance_slab'.$index,$slab->slab_max_km,['class'=>'form-control form-control1 clsROMMaxKm','id'=>'max_distance_slab'.$index,'placeholder'=>'Max Distance','required'=>true]) !!}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3 form-control-fld">
                                                         <div class="input-prepend">
                                                            {!! Form::text('transport_charges_slab'.$index,$slab->transport_price,['class'=>'form-control form-control1 clsROMTransportChargespKm','id'=>'transport_charges_slab'.$index,'placeholder'=>'Transport Charges','required'=>true]) !!}
                                                        </div>
                                                    </div>
                                                    @if($s==0)
                                                        @if($post_details->lkp_post_status_id != 2)
                                                            <div class="col-md-1 form-control-fld padding-left-none">
                                                                <button type="button" class="btn add-btn slab-box">Add</button>
                                                            </div>
                                                        @endif
                                                    @elseif((($s+1)==count($post_slabs) && $post_details->lkp_post_status_id != 2))
                                                        <div class="col-md-1 form-control-fld padding-left-none  padding-top-7">
                                                            <a class="remove-box-prices" href="javascript:void(0)"><i class="fa fa-trash red" title="Delete"></i></a>
                                                        </div>                                                        
                                                    @endif
                                                </div>
                                            </div>
                                            {{--*/ $s++ /*--}}
                                        @endforeach
										<input type="hidden" name ='price_slap_hidden_value' id='price_slap_hidden_value' value='{{$s-1}}'>	
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
                                            {!! Form::text('cancellation_charge_price',$post_details->cancellation_charge_price,['class'=>'form-control form-control1 clsROMCancelCharges','id'=>'cancellation_charge_price','placeholder'=>'Cancellation Charges']) !!}
                                            <span class="add-on unit1 manage">Rs</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3 tc-block-btn"></div>
                                </div>
                                <div class="my-form">
                                    <div class=" text-box form-control-fld terms-and-conditions-block padding-none">
                                        <div class="col-md-3 form-control-fld">
                                            <div class="input-prepend">
                                                {!! Form::text('docket_charge_price',$post_details->docket_charge_price,['class'=>'form-control form-control1 clsROMOtherCharges','id'=>'docket_charge_price','placeholder'=>'Other Charges']) !!}
                                                <span class="add-on unit1 manage">Rs</span>
                                            </div>
                                        </div>
                                        @if($post_details->lkp_post_status_id != 2)
                                            <div class="col-md-3 tc-block-btn"><input type="button" class="add-box btn add-btn" value="Add"></div>
                                        @endif    
                                    </div>
                                </div>
                                    @for ($i = 1; $i <= 3; $i++)
                                        {{--*/ $text =  "other_charge{$i}_text" /*--}}
                                        {{--*/ $price = "other_charge{$i}_price" /*--}}
                                        @if(($post_details->$text != "" || $post_details->$price != "") && ($post_details->$text != "" || $post_details->$price != "0.00"))
                                            <div class="text-box form-control-fld terms-and-conditions-block" style="">
                                                <div class="col-md-3 padding-none">
                                                    <div class="input-prepend">
                                                        {!! Form::text("labeltext_$i",$post_details->$text,['placeholder' => 'Other Charges','class'=>'form-control form-control1 labelcharges dynamic_labelcharges',($post_details->lkp_post_status_id == 2) ? '' : '']) !!}
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="input-prepend">
                                                        {!! Form::text("terms_condtion_types_$i",$post_details->$price,['placeholder' => '0.00','class'=>'form-control form-control1 pricebox update_txt_test update_txt dynamic_validations numberVal',($post_details->lkp_post_status_id == 2) ? '' : '']) !!}
                                                        <span class="add-on unit">Rs</span>
                                                    </div>
                                                </div>
                                                @if($post_details->lkp_post_status_id == 1)
                                                    <a href="#" class="remove-box col-md-2 margin-top-6" data-string="{{$i}}"><i class="fa fa-trash red" title="Delete"></i></a></a>
                                                @endif
                                            </div>
                                        @endif
                                    @endfor
                                <div class="col-md-6 form-control-fld">
                                    {!! Form::textarea('terms_conditions',$post_details->terms_conditions,['class'=>'form-control form-control1 clsTermsConditions','id'=>'terms_conditions', 'rows' => 5,'placeholder' => 'Notes to Terms & Conditions (Optional)']) !!}
                                </div>

                            </div>
								</div>


								<div class="col-md-12 inner-block-bg inner-block-bg1">

										  <div class="col-md-3 form-control-fld margin-top">
                                <div class="normal-select">

                                    {!! Form::select('tracking',(['' => 'Tracking*']+$trackingtypes), $post_details->tracking, ['id' => 'tracking','class' => 'selectpicker form-control', ($post_details->lkp_post_status_id == 2) ? 'disabled' : '']) !!}
                                </div>
                            </div>

                            <div class="clearfix"></div>

                            <h2 class="filter-head1">Payment Terms</h2>

                            <div class="col-md-3 form-control-fld">
                                <div class="normal-select">
                                    {!! Form::select('paymentterms', ($paymentterms), $post_details->lkp_payment_mode_id, ['class' => 'selectpicker','id' => 'payment_options',($post_details->lkp_post_status_id == 2) ? 'disabled' : '']) !!}
                                </div>
                            </div>
                            <div class="col-md-12 form-control-fld" id = 'show_advanced_period' style='display: @if($post_details->lkp_payment_mode_id == 1) block @else none @endif'>
                                <div class="check-block">
                                    <div class="checkbox_inline">
                                        {{--*/ $checked = ($post_details->accept_payment_netbanking == 1) ? true: false; /*--}}
                                        {!! Form::checkbox('accept_payment_ptl[]', 1, $checked, ['class' => 'accept_payment_ptl',($post_details->lkp_post_status_id == 2) ? 'disabled' : '']) !!} <span class="lbl padding-8">NEFT/RTGS</span>
                                    </div>
                                    <div class="checkbox_inline">
                                        {{--*/ $checked = ($post_details->accept_payment_credit == 1) ? true: false; /*--}}
                                        {!! Form::checkbox('accept_payment_ptl[]', 2, $checked, ['class' => 'accept_payment_ptl',($post_details->lkp_post_status_id == 2) ? 'disabled' : '']) !!} <span class="lbl padding-8">Credit Card</span>
                                    </div>
                                    <div class="checkbox_inline">
                                        {{--*/ $checked = ($post_details->accept_payment_debit == 1) ? true: false; /*--}}
                                        {!! Form::checkbox('accept_payment_ptl[]', 3, $checked, ['class' => 'accept_payment_ptl',($post_details->lkp_post_status_id == 2) ? 'disabled' : '']) !!} <span class="lbl padding-8">Debit Card</span>
                                    </div>
                                </div>
                            </div>
                            @if($post_details->credit_period_units == 'Days')
                              {{--*/ $creditPeriodClass = 'form-control form-control1 clsIDCredit_period clsCreditPeriod' /*--}}
                          @elseif($post_details->credit_period_units == 'Weeks')
                              {{--*/ $creditPeriodClass = 'form-control form-control1 clsIDCredit_period clsCreditPeriodWeeks' /*--}}
                          @else
                              {{--*/ $creditPeriodClass = 'form-control form-control1 clsIDCredit_period clsCreditPeriod' /*--}}
                          @endif
                            
                            <div class="col-md-12 form-control-fld" style ='display: @if($post_details->lkp_payment_mode_id == 4) block @else none @endif ;' id = 'show_credit_period'>
                                <div class="col-md-3 form-control-fld padding-left-none">

                                    <div class="col-md-7 padding-none">
                                        <div class="input-prepend">
                                            {!! Form::text('credit_period_ptl',$post_details->credit_period,['class'=>$creditPeriodClass,'placeholder'=>'Credit Period',($post_details->lkp_post_status_id == 2) ? 'disabled' : '']) !!}
                                        </div>
                                    </div>
                                    <div class="col-md-5 padding-none">
                                        <div class="input-prepend">
								<span class="add-on unit-days manage">
											<div class="normal-select">
                                                {!! Form::select('credit_period_units',['Days' => 'Days','Weeks' => 'Weeks'], $post_details->credit_period_units, ['class' => 'selectpicker clsSelPaymentCreditType bs-select-hidden',($post_details->lkp_post_status_id == 2) ? 'disabled' : '']) !!}
                                            </div>
										</span>
                                        </div>
                                    </div>


                                </div>
                                <div class="col-md-12 padding-none">
                                    <div class="checkbox_inline">
                                        {{--*/ $checked = ($post_details->accept_credit_netbanking == 1) ? true: false; /*--}}
                                        {!! Form::checkbox('accept_credit_netbanking[]', 1, $checked,false,[($post_details->lkp_post_status_id == 2) ? 'disabled' : '']) !!} <span class="lbl padding-8">Net Banking</span>
                                    </div>
                                    <div class="checkbox_inline">
                                        {{--*/ $checked = ($post_details->accept_credit_cheque == 1) ? true: false; /*--}}
                                        {!! Form::checkbox('accept_credit_netbanking[]', 2, $checked,false,[($post_details->lkp_post_status_id == 2) ? 'disabled' : '']) !!} <span class="lbl padding-8">Cheque / DD</span>
                                    </div>

                                </div>
                            </div>



                            <div class="clearfix"></div>

                        </div>



                        <div class="col-md-12 inner-block-bg inner-block-bg1">
                            <div class="col-md-12 form-control-fld margin-none">
                                <div class="radio-block">
                                    <div class="radio_inline">
                                        {{--*/ $checked = ($post_details->lkp_access_id == 1) ? true: false; /*--}}
                                        {!! Form::radio('optradio', 1, $checked, ['id' => 'post-public','class' => 'create-posttype-service',($post_details->lkp_post_status_id == 2) ? 'disabled' : '']) !!}
                                        <label for="post-public"><span></span>Post Public</label>
                                    </div>
                                    <div class="radio_inline">
                                        {{--*/ $checked = ($post_details->lkp_access_id == 2) ? true: false; /*--}}
                                        {!! Form::radio('optradio', 2, $checked, ['id' => 'post-private','class' => 'create-posttype-service',($post_details->lkp_post_status_id == 2) ? 'disabled' : '']) !!}
                                         <label for="post-private"><span></span>Post Private</label>
                                    </div>
                                    @if($post_details->lkp_post_status_id == 2)
                                        <input type="hidden" value="{!! $post_details->lkp_access_id !!}" name="optradio">
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-3 form-control-fld demo-input_buyers" style="display: @if($private) block @else none @endif">
                                <div class="input-prepend">
                                    {{--*/ $selected = ""; /*--}}
                                    @foreach($selectedbuyers as $selectedbuyer)
                                        {{--*/ $selected .= ",".$selectedbuyer->buyer_id; /*--}}
                                    @endforeach
                                        <input type="hidden" id="demo_input_select_hidden" name="buyer_list_for_sellers_hidden" value="{{$selected}}" />
                                        <select id="demo_input_select" class="tokenize-sample" name="buyer_list_for_sellers" multiple="multiple">
                                            @foreach($selectedbuyers as $selectedbuyer)
                                                @if($selectedbuyer->principal_place != '')
                                                    <option value="{{$selectedbuyer->buyer_id}}" selected="selected">{{$selectedbuyer->username.' '.$selectedbuyer->principal_place.' '.$selectedbuyer->buyer_id}}</option>
                                                @else
                                                    <option value="{{$selectedbuyer->buyer_id}}" selected="selected">{{$selectedbuyer->username.' '.$selectedbuyer->buyer_id}}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                </div>
                            </div>


                            <div class="clearfix"></div>
                            <div class="check-box form-control-fld margin-none">
                                {!! Form::checkbox('agree', 1, false,array('id'=>'agree')) !!}<span class="lbl padding-8">Accept Terms & Conditions (Digital Contract)</span>
                            </div>
                        </div>

                        <div class="col-md-12 padding-none">
                            <div class="col-md-12 padding-none">
                                {!! Form::submit('Confirm', ['class' => 'btn theme-btn flat-btn pull-right','name' => 'confirm','id' => ($post_details->lkp_post_status_id == 1)? 'update_quote_seller_id_relocation_office':'','onclick'=>"updatepoststatus(1)"]) !!}
                                @if($post_details->lkp_post_status_id == 1)
                                    {!! Form::submit('Save as draft', ['class' => 'btn add-btn flat-btn pull-right','name' => 'draft','id' => 'add_quote_seller_relocation_office_update','onclick'=>"updatepoststatus(0)"]) !!}
                                @endif
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



