@extends('app')
@section('content')
@inject('common', 'App\Components\CommonComponent')

{{--*/ $slabs =   $common->getRelocationIntSlabs(); /*--}}		
{{--*/ $serviceId = Session::get('service_id') /*--}}

@if(URL::previous() == URL::current())      
      {{--*/  $backToPostsUrl = url('/sellerlist') /*--}}
@else
      {{--*/  $backToPostsUrl = URL::previous() /*--}}
@endif

        <!-- Page top navigation Starts Here-->
@include('partials.page_top_navigation')

    <div class="main">

			<div class="container">
			
			@if(Session::has('transId_updated') && Session::get('transId_updated')!='')
	     	{{--*/ $transactionId = Session::get('transId_updated') /*--}}
			<script>
			$(document).ready(function(){
			$("#erroralertmodal .modal-body").html("Your post has been saved successfully. Your transacton id is <?php echo $transactionId;?>. You would be getting the quotes from the buyers online.");
     	   	$("#erroralertmodal").modal({
               show: true
          	 }).one('click','.ok-btn',function (e){
        	   window.location="/sellerlist";
        	 
           		});
			 });
			</script>
		@endif
			
			
			
				<span class="pull-left"><h1 class="page-title">Edit Post (RELOCATION AIRINTERNATIONAL)</h1><a class="change-service" data-toggle="modal" data-target="#change-service">Change Service</a></span>
				<span class="pull-right"><a href="{{$backToPostsUrl}}" class="back-link">Back to Posts</a></span>
				<div class="clearfix"></div>
				
				<div class="col-md-12 padding-none">
					<div class="main-inner"> 
						
						
						<!-- Right Section Starts Here -->

						<div class="main-right">

{!! Form::open(['url' => 'relocation/updatesellerpost/'.$seller_post_edit->id,'id'=>'posts-form-lines_int']) !!}
							<div class="gray-bg">
								<div class="col-md-12 padding-none filter">

									<div class="col-md-12 form-control-fld">
										<div class="radio-block">
											<input type="radio" name="main_post_type" id="spot" checked="true">
											<label for="spot"><span></span>Air</label>
												
											<input type="radio" name="main_post_type" disabled id="term"/>
											<label for="term"><span></span>Ocean</label>
										</div>
									</div>

										<div class="col-md-6 form-control-fld padding-none">

											<div class="col-md-6 form-control-fld margin-none">
												<div class="input-prepend">
													<span class="add-on"><i class="fa fa-map-marker"></i></span>
													{!! Form::text('from_location', $common->getCityName($seller_post_edit->from_location_id), ['id' => 'from_location','class' => 'form-control', 'placeholder' => 'From Location*','disabled' => true]) !!}
                                        			{!! Form::hidden('from_location_id', $seller_post_edit->from_location_id, array('id' => 'from_location_id')) !!}
                                        			{!! Form::hidden('seller_district_id', '', array('id' => 'seller_district_id')) !!}
                                        			{!! Form::hidden('sellerpoststatus_previous', $seller_post_edit->lkp_post_status_id, array('id' => 'sellerpoststatus_previous')) !!}
                        							{!! Form::hidden('sellerpoststatus', $seller_post_edit->lkp_post_status_id, array('id' => 'sellerpoststatus')) !!}
                        
												</div>
											</div>

											<div class="col-md-6 form-control-fld margin-none">
												<div class="input-prepend">
													<span class="add-on"><i class="fa fa-map-marker"></i></span>
													{!! Form::text('to_location', $common->getCityName($seller_post_edit->to_location_id), ['id' => 'to_location','class' => 'form-control', 'placeholder' => 'To Location*','disabled' => true]) !!}
                                       				{!! Form::hidden('to_location_id', $seller_post_edit->to_location_id, array('id' => 'to_location_id')) !!}
												</div>
											</div>
											<div class="clearfix"></div>
											

										</div>
										
										<div class="col-md-3 form-control-fld">
											<div class="input-prepend">
												<span class="add-on"><i class="fa fa-calendar-o"></i></span>
												{!! Form::text('valid_from', $common->checkAndGetDate($seller_post_edit->from_date), ['id' => 'datepicker','class' => 'calendar form-control from-date-control','readonly' => true, 'placeholder' => 'Valid From*','disabled' => true]) !!}
                                        		{!! Form::hidden('valid_from_hidden', $seller_post_edit->from_date, array('id' => 'valid_from_hidden')) !!}
											</div>
										</div>
										<div class="col-md-3 form-control-fld">
											<div class="input-prepend">
												<span class="add-on"><i class="fa fa-calendar-o"></i></span>
												{!! Form::text('valid_to', $common->checkAndGetDate($seller_post_edit->to_date), ['id' => 'datepicker_to_location','class' => 'calendar form-control  to-date-control','readonly' => true, 'placeholder' => 'Valid To*']) !!}
											</div>
										</div>
										<div class="clearfix"></div>
										<div class="col-md-3 form-control-fld">
										<div class="col-md-7 padding-none">
											<div class="input-prepend">
													<span class="add-on"><i class="fa fa-hourglass-1"></i></span>
													{!! Form::text('transitdays',$seller_post_edit->transitdays,['class'=>'form-control clsIDtransitdays clsCOURTransitDays','id'=>'transitdays','placeholder'=>'Transit Days*']) !!}
													
												</div>
										</div>
										<div class="col-md-5 padding-none">
											<div class="input-prepend">
												<span class="add-on unit-days">
																<div class="normal-select">
																	{!! Form::select('units',['Days' => 'Days','Weeks' => 'Weeks'], $seller_post_edit->units, ['class' => 'selectpicker clsSelTransitDaysType','id'=>'transitdays_units', 'data-serviceId' => $serviceId]) !!}	
																</div>
															</span>
											</div>
										</div>
											
										</div>

									</div>
								</div>

								<div class="gray-bg">
	
									<div class="table-div table-style1 margin-top form-control-grid">
									
									<!-- Table Head Starts Here -->

									<div class="table-heading inner-block-bg">
										<div class="col-md-6 padding-left-none">Weight Bracket (KGs)</div>
										<div class="col-md-3 padding-left-none">Freight Charges (per KG)</div>
										<div class="col-md-3 padding-none">O & D Charges (Rate per CFT)</div>
									</div>

									<!-- Table Head Ends Here -->

									<div class="table-data">
										

									
									<?php $i = 1 ?>
										@foreach($slabs as $slab)
										<div class="table-row inner-block-bg">
											<div class="col-md-6 padding-left-none padding-top">{{$slab->min_slab_weight}}-{{$slab->max_slab_weight}}</div>
											
											<div class="col-md-3 padding-left-none">
												<div class="input-prepend">
													{!! Form::text("freightcharge_$i",$seller_slabs[$i-1]->freight_charges,['class'=>'form-control form-control1 clsRIASFreightChargespKG','placeholder'=>'Freight Charge *']) !!}
													<span class="add-on unit1 manage">per KG</span>
												</div>
											</div>
											<div class="col-md-3 padding-left-none">
												<div class="input-prepend">
													{!! Form::text("odcharges_$i",$seller_slabs[$i-1]->od_charges,['class'=>'form-control form-control1 clsRIASODChargespCFT','placeholder'=>'O & D Charges *']) !!}
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

								<h2 class="filter-head1 margin-bottom">Additional Charges</h2>	

								<div class="my-form">
									<div class="form-control-fld terms-and-conditions-block">
										<div class="col-md-3 padding-none tc-block-fld">
											@if(Session::get('service_id') == RELOCATION_INTERNATIONAL)
											{!! Form::text('storage_charges',$seller_post_edit->storage_charges,['class'=>'form-control form-control1 update_txt clsRIASStorageCharges', 'placeholder' => 'Storage Charges']) !!}
											@else
											{!! Form::text('storage_charges',$seller_post_edit->storage_charge_price,['class'=>'form-control form-control1 update_txt clsRIASStorageCharges', 'placeholder' => 'Storage Charges']) !!}
											@endif
										</div>
										<div class="col-md-3 tc-block-btn"></div>
									</div>
								</div>
								
								
								<div class="my-form">
									<div class="form-control-fld terms-and-conditions-block">
										<div class="col-md-3 padding-none tc-block-fld">
											{!! Form::text('terms_condtion_types1',$seller_post_edit->cancellation_charge_price,['class'=>'form-control form-control1 update_txt clsRIASCancelCharges', 'placeholder' => 'Cancellation Charges']) !!}
										</div>
										<div class="col-md-3 tc-block-btn"></div>
									</div>
								</div>	

									<div class="my-form">
										<div class="text-box form-control-fld terms-and-conditions-block">
											<div class="col-md-3 padding-none tc-block-fld">
												{!! Form::text('terms_condtion_types2',$seller_post_edit->other_charge_price,['class'=>'form-control form-control1 update_txt clsRIASOtherCharges', 'placeholder' => 'Other Charges']) !!}
											</div>
											<div class="col-md-3 tc-block-btn">
													<input type="button" class="add-box btn add-btn" value="Add">
											</div>
										</div>
										@for ($i = 1; $i <= 3; $i++)
											{{--*/ $text =  "other_charge{$i}_text" /*--}}
											{{--*/ $price = "other_charge{$i}_price" /*--}}
											@if(($seller_post_edit->$text != "" || $seller_post_edit->$price != "") && ($seller_post_edit->$text != "" || $seller_post_edit->$price != "0.00"))
												<div class="text-box form-control-fld terms-and-conditions-block" style="">
													<div class="col-md-3 padding-none">
													<div class="input-prepend">
														{!! Form::text("labeltext_$i",$seller_post_edit->$text,['placeholder' => 'Other Charges','class'=>'form-control form-control1 labelcharges dynamic_labelcharges']) !!}
													</div>
													</div>
													<div class="col-md-3">
													<div class="input-prepend">
														{!! Form::text("terms_condtion_types_$i",$seller_post_edit->$price,['placeholder' => '0.00','class'=>'form-control form-control1 pricebox update_txt_test update_txt dynamic_validations clsRIASOtherCharges']) !!}
														<span class="add-on unit">Rs</span>
													</div>
													</div>
													@if($seller_post_edit->lkp_post_status_id == 1)
														<a href="#" class="remove-box col-md-2 margin-top-6" data-string="'+num+'"><i class="fa fa-trash red" title="Delete"></i></a></a>
													@endif
												</div>
											@endif
										@endfor
										<input type="hidden" name ='next_terms_count_search' id='next_terms_count_search' value='{{$i-1}}'>
						
									</div>	

								<div class="col-md-6 form-control-fld">
									{!! Form::textarea('terms_conditions', $seller_post_edit->terms_conditions,['class'=>'form-control form-control1 clsTermsConditions','id'=>'terms_conditions', 'rows' => 5,($seller_post_edit->lkp_post_status_id == 2) ? 'readonly' : '']) !!}
								</div>
			
						</div>
								
								
								

								<div class="col-md-12 inner-block-bg inner-block-bg1">
									<div class="col-md-3 form-control-fld">
										<div class="normal-select">

                                                       {!! Form::select('tracking',(['' => 'Tracking*']+$trackingtypes), $seller_post_edit->tracking, ['id' => 'tracking_vechile','class' => 'selectpicker form-control', ($seller_post_edit->lkp_post_status_id == 2) ? 'disabled' : '']) !!}
											@if($seller_post_edit->lkp_post_status_id == 2)
												<input type="hidden" value="{!! $seller_post_edit->tracking !!}" name="tracking">
											@endif
										</div>
									</div>

									<div class="clearfix"></div>

									<h2 class="filter-head1">Payment Terms</h2>

									<div class="col-md-3 form-control-fld">
										<div class="normal-select">
											{!! Form::select('payment_methods', ($paymentterms), $seller_post_edit->lkp_payment_mode_id, ['class' => 'selectpicker','id' => 'payment_options',($seller_post_edit->lkp_post_status_id == 2) ? 'disabled' : '']) !!}
											@if($seller_post_edit->lkp_post_status_id == 2)
												<input type="hidden" value="{!! $seller_post_edit->lkp_payment_mode_id !!}" name="payment_methods">
											@endif
										</div>
									</div>

								<div class="col-md-12 form-control-fld" id='show_advanced_period' style='display: <?php echo ($seller_post_edit->lkp_payment_mode_id == 1) ? "block" : "none"; ?>'>
									<div class="check-block">
										<div class="checkbox_inline">
											<span class="erorradio">
												@if($seller_post_edit->lkp_post_status_id == 2)
												<input type="hidden" value="{!! $seller_post_edit->accept_payment_netbanking !!}" name="accept_payment[]">
													@if($seller_post_edit->accept_payment_netbanking == 1)
													{!! Form::checkbox('accept_payment[]', 1,  true, ['class' => 'accept_payment','disabled'=>'disabled']) !!}<span class="lbl padding-8">NEFT/RTGS</span>
													@else
													{!! Form::checkbox('accept_payment[]', 1, false, ['class' => 'accept_payment','disabled'=>'disabled']) !!}<span class="lbl padding-8">NEFT/RTGS</span>
													@endif
												@else
												{!! Form::checkbox('accept_payment[]', 1, $seller_post_edit->accept_payment_netbanking, false, ['class' => 'accept_payment']) !!}<span class="lbl padding-8">NEFT/RTGS</span>
												@endif
											</span>
										</div>
					
										<div class="checkbox_inline">
											<span class="erorradio"> 
												@if($seller_post_edit->lkp_post_status_id == 2)
												<input type="hidden" value="{!! $seller_post_edit->accept_payment_credit !!}" name="accept_payment[]">
													@if($seller_post_edit->accept_payment_credit == 1)
													{!! Form::checkbox('accept_payment[]', 2,true, ['class' => 'accept_payment','disabled'=>'disabled']) !!}<span class="lbl padding-8">Credit Card</span>
													@else
													{!! Form::checkbox('accept_payment[]', 2, false, ['class' => 'accept_payment','disabled'=>'disabled']) !!}<span class="lbl padding-8">Credit Card</span>
													@endif
												@else
													{!! Form::checkbox('accept_payment[]', 2, $seller_post_edit->accept_payment_credit, false, ['class' => 'accept_payment']) !!}<span class="lbl padding-8">Credit Card</span>
												@endif
											</span>
										</div>
					
										<div class="checkbox_inline">
											<span class="erorradio">
												@if($seller_post_edit->lkp_post_status_id == 2)
												<input type="hidden" value="{!! $seller_post_edit->accept_payment_debit !!}" name="accept_payment[]">
													@if($seller_post_edit->accept_payment_debit == 1)
													{!! Form::checkbox('accept_payment[]', 3, true, ['class' => 'accept_payment','disabled'=>'disabled']) !!}<span class="lbl padding-8">Debit Card</span>
													@else
													{!! Form::checkbox('accept_payment[]', 3, false, ['class' => 'accept_payment','disabled'=>'disabled']) !!}<span class="lbl padding-8">Debit Card</span>
													@endif
												@else
													{!! Form::checkbox('accept_payment[]', 3, $seller_post_edit->accept_payment_debit, false, ['class' => 'accept_payment']) !!}<span class="lbl padding-8">Debit Card</span>
													@endif
											</span>
										</div>
									</div>
								</div>
                                             
                                             @if($seller_post_edit->credit_period_units == 'Days')
                                                {{--*/ $creditPeriodClass = 'form-control form-control1 clsIDCredit_period clsCreditPeriod' /*--}}
                                            @elseif($seller_post_edit->credit_period_units == 'Weeks')
                                                {{--*/ $creditPeriodClass = 'form-control form-control1 clsIDCredit_period clsCreditPeriodWeeks' /*--}}
                                            @else
                                                {{--*/ $creditPeriodClass = 'form-control form-control1 clsIDCredit_period clsCreditPeriod' /*--}}
                                            @endif

								<div class="col-md-12 form-control-fld" style='display: <?php echo ($seller_post_edit->lkp_payment_mode_id == 4) ? "block" : "none"; ?>' id = 'show_credit_period'>
									<div class="col-md-3 form-control-fld padding-left-none">
									<div class="col-md-7 padding-none">
										<div class="input-prepend">
										{!! Form::text('credit_period',$seller_post_edit->credit_period,['class'=>$creditPeriodClass,'placeholder'=>'',($seller_post_edit->lkp_post_status_id == 2) ? 'readonly' : '']) !!}
										
									</div>
									</div>
									<div class="col-md-5 padding-none">
										<div class="input-prepend">
											<span class="add-on unit-days">
																<div class="normal-select">
																	{!! Form::select('credit_period_units',['Days' => 'Days','Weeks' => 'Weeks'], $seller_post_edit->credit_period_units, ['class' => 'selectpicker clsSelPaymentCreditType bs-select-hidden',($seller_post_edit->lkp_post_status_id == 2) ? 'disabled' : '']) !!}
											@if($seller_post_edit->lkp_post_status_id == 2)
											<input type="hidden" value="{!! $seller_post_edit->credit_period_units !!}" name="credit_period_units">
											@endif		
																</div>
															</span>					
										</div>
									</div>
									
									
									
									</div>
									<!-- div class="col-md-2 padding-right-none">
										<div class="normal-select">
											
										</div>	
									</div> -->
									<div class="col-md-12 padding-none">
					
										<div class="checkbox_inline">
											@if($seller_post_edit->lkp_post_status_id == 2)
											<input type="hidden" value="{!! $seller_post_edit->accept_credit_netbanking !!}" name="accept_credit_netbanking">
												@if($seller_post_edit->accept_credit_netbanking == 1)
												{!! Form::checkbox('accept_credit_netbanking[]', 1, true, ['disabled'=>'disabled']) !!}<span class="lbl padding-8">Net Banking</span>
												@else
												{!! Form::checkbox('accept_credit_netbanking[]', 1, false, ['disabled'=>'disabled']) !!}<span class="lbl padding-8">Net Banking</span>
												@endif
											@else
												{!! Form::checkbox('accept_credit_netbanking[]', 1,$seller_post_edit->accept_credit_netbanking, false) !!}<span class="lbl padding-8">Net Banking</span>
											@endif
										</div>
					
										<div class="checkbox_inline">
											@if($seller_post_edit->lkp_post_status_id == 2)
											<input type="hidden" value="{!! $seller_post_edit->accept_credit_cheque !!}" name="accept_credit_cheque">
												@if($seller_post_edit->accept_credit_cheque == 1)
												{!! Form::checkbox('accept_credit_netbanking[]', 2, true, ['disabled'=>'disabled']) !!}<span class="lbl padding-8">Cheque / DD</span>
												@else
												{!! Form::checkbox('accept_credit_netbanking[]', 2, false, ['disabled'=>'disabled']) !!}<span class="lbl padding-8">Cheque / DD</span>
												@endif
											@else
												{!! Form::checkbox('accept_credit_netbanking[]', 2,$seller_post_edit->accept_credit_cheque, false) !!}<span class="lbl padding-8">Cheque / DD</span>
											@endif
										</div>		
					
					
									</div>
								</div>
						</div>



								<div class="col-md-12 inner-block-bg inner-block-bg1">
								<div class="col-md-12 form-control-fld">
									@if($seller_post_edit->lkp_post_status_id == 1)
									<div class="radio-block">
										<div class="radio_inline">
										@if($seller_post_edit->lkp_access_id == 1)
											{!! Form::radio('optradio', 1, true, ['id' => 'post-public','class' => 'create-posttype-service']) !!} <label for="post-public"><span></span>Post Public</label>
											@else
											{!! Form::radio('optradio', 1, false, ['id' => 'post-public','class' => 'create-posttype-service']) !!} <label for="post-public"><span></span>Post Public</label>
										@endif
										</div>
										<div class="radio_inline">
										@if($seller_post_edit->lkp_access_id == 2)
											{!! Form::radio('optradio', 2, true, ['id' => 'post-private','class' => 'create-posttype-service']) !!} <label for="post-private"><span></span>Post Private</label>
											@else
											{!! Form::radio('optradio', 2, false, ['id' => 'post-private','class' => 'create-posttype-service']) !!} <label for="post-private"><span></span>Post Private</label>
										@endif
										</div>
									</div>
									@endif
					
									 @if($seller_post_edit->lkp_post_status_id == 2)
									<div class="radio-block">
										<div class="radio_inline">
										@if($seller_post_edit->lkp_access_id == 1)
											{!! Form::radio('optradio', 1, true, ['id' => 'post-public','class' => 'create-posttype-service', 'disabled' => true]) !!} <label for="post-public"><span></span>Post Public</label>
											@else
											{!! Form::radio('optradio', 1, false, ['id' => 'post-public','class' => 'create-posttype-service', 'disabled' => true]) !!} <label for="post-public"><span></span>Post Public</label>
										@endif
										</div>
										<div class="radio_inline">
										@if($seller_post_edit->lkp_access_id == 2)
											{!! Form::radio('optradio', 2, true, ['id' => 'post-private','class' => 'create-posttype-service', 'disabled' => true]) !!} <label for="post-private"><span></span>Post Private</label>
										@else
											{!! Form::radio('optradio', 2, false, ['id' => 'post-private','class' => 'create-posttype-service', 'disabled' => true]) !!} <label for="post-private"><span></span>Post Private</label>
										@endif
										</div>
									</div>
									@endif
					
								</div>

					            @if($seller_post_edit->lkp_post_status_id == 2)
									<input type="hidden" value="{!! $seller_post_edit->lkp_access_id !!}" name="optradio">
								@endif



								<div class="col-md-3 form-control-fld">
									<div class="demo-input_buyers" style='display:<?php echo ($private == true) ? "block" : "none"; ?>'>
										<?php 
										$selected  = "";
										foreach($selectedbuyers as $selectedbuyer){ 
											$selected .= ",".$selectedbuyer->buyer_id;
										} ?>
										<input type="hidden" id="demo_input_select_hidden" name="buyer_list_for_sellers_hidden" value="<?php echo $selected; ?>" />
										<select id="demo_input_select" class="tokenize-sample" name="buyer_list_for_sellers" multiple="multiple">
											<?php foreach($selectedbuyers as $selectedbuyer){ ?>
											 @if($selectedbuyer->principal_place != '')
												<option value="<?php echo $selectedbuyer->buyer_id ?>" selected="selected"><?php echo $selectedbuyer->username.' '.$selectedbuyer->principal_place.' '.$selectedbuyer->buyer_id; ?></option>
												@else
												<option value="<?php echo $selectedbuyer->buyer_id ?>" selected="selected"><?php echo $selectedbuyer->username.' '.$selectedbuyer->buyer_id; ?></option>
											@endif
											<?php } ?>
										</select>
					
									</div>
								</div>

								<div class="clearfix"></div>
								<div class="check-box form-control-fld">
									<div class="spacing erorradio space-margin terms_chk_error">
										@if($seller_post_edit->lkp_post_status_id == 1)
											{!! Form::checkbox('agree', 1, false,array('id'=>'agree')) !!}<span class="lbl padding-8">Accept Terms & Conditions (Digital Contract)</span>
										@else
											{!! Form::checkbox('agree', 1, true,array('id'=>'agree',($seller_post_edit->lkp_post_status_id == 2) ? 'disabled' : '')) !!}<span class="lbl padding-8">Accept Terms & Conditions (Digital Contract)</span>
										@endif
										<div class="clearfix"></div>
										<img src="{{asset('/images/loading_buyers.png')}}" style ='display:none;' id = 'img-responsive-buyer' class="img-responsive">
									</div>
								</div>
		
							</div>

								<div class="col-md-12 padding-none">
									{!! Form::submit('Confirm', ['class' => 'btn theme-btn flat-btn pull-right','name' => 'confirm','id' => 'add_quote_seller_id_relocation_int','onclick'=>"updatepoststatus(1)"]) !!}
                                @if($seller_post_edit->lkp_post_status_id == 1)
                                    {!! Form::submit('Save as draft', ['class' => 'btn add-btn flat-btn pull-right','name' => 'draft','id' => 'add_quote_seller_relocation_int','onclick'=>"updatepoststatus(0)"]) !!}
                                @endif
								</div>
							
				{!! Form::close() !!}
						</div>

						<!-- Right Section Ends Here -->

					</div>
				</div>

				<div class="clearfix"></div>

			</div>
		</div>










@include('partials.footer')
@endsection



