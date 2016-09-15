@inject('common', 'App\Components\CommonComponent')
{{--*/ $serviceId = Session::get('service_id') /*--}}
{{--*/ $from_date=''; /*--}}
{{--*/ $to_date=''; /*--}}
{{--*/ $from_loaction=''; /*--}}
{{--*/ $from_loaction_id=''; /*--}}  
{{--*/ $to_loaction=''; /*--}}
{{--*/ $to_loaction_id=''; /*--}}  
{{--*/ $seller_district_id = ''; /*--}}

@if(Session::has('seller_searchrequest_relocationint_type'))
    {{--*/ $search_relocation_inttype = Session::get('seller_searchrequest_relocationint_type'); /*--}}
    {{--*/ $searchrequest = Session::get('seller_searchrequest_relint_ocean'); /*--}}
    @if($search_relocation_inttype != ""  && $search_relocation_inttype == 2)
        {{--*/ $from_loaction=$searchrequest['from_location']; /*--}}
        {{--*/ $from_loaction_id=$searchrequest['from_location_id']; /*--}}
        {{--*/ $from_date=$searchrequest['valid_from']; /*--}}
        {{--*/ $to_date=$searchrequest['valid_to']; /*--}}
        {{--*/ $to_loaction=$searchrequest['to_location']; /*--}}
        {{--*/ $to_loaction_id=$searchrequest['to_location_id']; /*--}}        
        @if(isset($searchrequest['seller_district_id']))
        {{--*/ $seller_district_id=$searchrequest['seller_district_id']; /*--}}
        @endif
    @endif
@endif
<div class="col-md-12 inner-block-bg inner-block-bg1  border-top-none padding-top-none margin-none border-bottom-none padding-bottom-none">
{!! Form::open(['url' => 'addseller_init-air','id'=>'posts-form-lines-int-ocean']) !!}
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
<div class="col-md-3 form-control-fld">
	
	<div class="input-prepend">
		<span class="add-on"><i class="fa fa-map-marker"></i></span>
		{!! Form::text('from_location', $from_loaction, ['id' => 'from_location','class' => 'form-control', 'placeholder' => 'From Location*']) !!}
        {!! Form::hidden('from_location_id', $from_loaction_id, array('id' => 'from_location_id')) !!}
        {!! Form::hidden('seller_district_id', $seller_district_id, array('id' => 'seller_district_id')) !!}
	</div>
	
	
	
</div>
<div class="col-md-3 form-control-fld">
	<div class="input-prepend">
		<span class="add-on"><i class="fa fa-map-marker"></i></span>
			{!! Form::text('to_location', $to_loaction, ['id' => 'to_location','class' => 'form-control', 'placeholder' => 'To Location*']) !!}
            {!! Form::hidden('to_location_id', $to_loaction_id, array('id' => 'to_location_id')) !!} 
	</div>
</div>
										
<div class="clearfix"></div>
<div class="col-md-3 form-control-fld">
	<div class="normal-select">
	{{--*/ $getRelocationAllShipmentTypes = $common->getRelocationAllShipmentType() /*--}}
	 {!! Form::select('shipment_types', (['' => 'Shipment Type *'] + $getRelocationAllShipmentTypes), '', ['class' => 'selectpicker form-control','id' => 'shipment_types']) !!}
	
		
	</div>
</div>
<div class="col-md-3 form-control-fld">
	<div class="normal-select">
	{{--*/ $getRelocationAllVolumeTypes = $common->getRelocationAllVolumeTypes() /*--}}
	 {!! Form::select('volumetype', (['' => 'Volume Type *'] + $getRelocationAllVolumeTypes), '', ['class' => 'selectpicker form-control','id' => 'volumetype']) !!}
	</div>
</div>
<div class="col-md-3 form-control-fld">
	<div class="input-prepend">
		{!! Form::text('Odcharges',null,['class'=>'form-control form-control1 clsRIOSODChargespCBM','id'=>'Odcharges','placeholder'=>'O & D Charges (Rs/CBM)*']) !!}
		</div>
</div>

<div class="col-md-3 form-control-fld">
	<div class="input-prepend">
	{!! Form::text('freightcharge',null,['class'=>'form-control form-control1 clsRIOSFreightFlat','id'=>'freightcharge','placeholder'=>'Freight *']) !!}
		
	</div>
</div>
  <div class="clearfix"></div>
<div class="col-md-3 form-control-fld">
	 <div class="col-md-8 padding-none">
		<div class="input-prepend">
			{!! Form::text('oceantransitdays',null,['class'=>'form-control form-control1 clsIDtransitdaysOcean clsCOURTransitDays','id'=>'oceantransitdays','placeholder'=>'Transit Days*']) !!}
         </div>
     </div>
     <div class="col-md-4 padding-none">
     	<div class="input-prepend">
        	<span class="add-on unit-days manage">
            	<div class="normal-select">
                	{!! Form::select('oceanunits',['Days' => 'Days','Weeks' => 'Weeks'], null, ['class' => 'selectpicker clsSelTransitDaysTypeOcean','id'=>'oceantransitdays_units', 'data-serviceId' => $serviceId, 'data-posttype' => 'Ocean']) !!}    
                </div>
            </span>
        </div>
    </div>
</div>


<div class="col-md-3 form-control-fld">
	 <input type="button" id="add_more_int_ocean" value="Add" class="btn add-btn">
</div>
{!!	Form::hidden('update_ftl_seller_line',0,array('class'=>'','id'=>'update_ftl_seller_line'))!!}
{!!	Form::hidden('update_ftl_seller_row_count','',array('class'=>'','id'=>'update_ftl_seller_row_count'))!!}
{!! Form::close() !!}
</div>
<div class="clearfix"></div>
{!! Form::open(['url' => 'relocationsellerpostcreation','id'=>'posts-form-lines_oceanint']) !!}
<div class="col-md-12 inner-block-bg inner-block-bg1 border-top-none padding-top-none padding-bottom-none border-bottom-none padding-bottom-none margin-none">
	<div class="main-inner">
    <!-- Right Section Starts Here -->
    	<div class="main-right">
    		 <div class="table-div table-style1 margin-none">
				<!-- Table Head Starts Here -->
                <div class="table-heading inner-block-bg">
				<!-- Table Head Starts Here -->
					<div class="col-md-3 padding-left-none">Shipment Type</div>
					<div class="col-md-2 padding-left-none">Volume</div>
					<div class="col-md-2 padding-left-none">O & D Charges</div>
					<div class="col-md-2 padding-left-none">Freight</div>
					<div class="col-md-2 padding-left-none">Transit Days</div>
				</div>

				<!-- Table Head Ends Here -->

				<div class="table-data">
				<!-- Table Row Starts Here -->

					<input type="hidden" id='next_add_more_id' value='0'>
					<div id ="multi-line-itemes">
			        	<div class="table-data request_rows" id=""></div>
			        </div>
					<!-- Table Row Ends Here -->
				</div>

					<!-- Table Ends Here -->
			</div>   
		</div>
	</div>
    <div class="clearfix"></div>
 </div>



					{!! Form::hidden('int_air_coean', '2', array('id' => 'int_air_coean')) !!}
                    {!! Form::hidden('ocen_service_id', '18', array('id' => 'ocen_service_id')) !!}
                    {!! Form::hidden('ocen_valid_from_val', '', array('id' => 'ocean_valid_from_val')) !!}
                     {!! Form::hidden('ocen_valid_to_val', '', array('id' => 'ocean_valid_to_val')) !!}
                    {!! Form::hidden('oceansellerpoststatus', '', array('id' => 'oceansellerpoststatus')) !!}
                    {!! Form::hidden('ocen_subscription_start_date_start', $subscription_start_date_start, array('id' => 'ocen_subscription_start_date_start')) !!}
                    {!! Form::hidden('ocen_subscription_end_date_end', $subscription_end_date_end, array('id' => 'ocen_subscription_end_date_end')) !!}
                    {!! Form::hidden('ocen_current_date_seller', $current_date_seller, array('id' => 'ocen_current_date_seller')) !!}
                    <input type="hidden" name ='next_terms_count_search_ocen' id='next_terms_count_search_ocen' value='0'>

<div class="col-md-12 inner-block-bg inner-block-bg1 border-top-none padding-top-none">
<div class="col-md-3 form-control-fld">
	<div class="input-prepend">
		<input class="form-control form-control1 clsRIOSCratingChargespCFT" id="crating_charges" name="crating_charges" type="text" placeholder="Crating Charges ">
		<span class="add-on unit1 manage">
			per CFT
		</span>
	</div>
</div>

<div class="clearfix"></div>

<div class="col-md-3 form-control-fld">
	
    <div class="radio-block">
    {!! Form::checkbox('origin_storage', '1', '',array('id'=>'origin_storage')) !!}
    <span class="lbl padding-8">Storage</span></div>
 	<div class="radio-block">
 	{!! Form::checkbox('origin_handyman_services', '1', '',array('id'=>'origin_handyman_services')) !!}
 	<span class="lbl padding-8">Handyman Services</span></div>
                                    
</div>

<div class="col-md-3 form-control-fld">
	<div class="radio-block">
	{!! Form::checkbox('destination_storage','1', '',array('id'=>'destination_storage')) !!}
	<span class="lbl padding-8">Storage</span></div>
    <div class="radio-block">
    {!! Form::checkbox('destination_handyman_services', '1', '',array('id'=>'destination_handyman_services')) !!}
    <span class="lbl padding-8">Handyman Services</span></div>
                                    
</div>
</div>



											<div class="col-md-12 inner-block-bg inner-block-bg1">

							                    <h2 class="filter-head1">Additional Charges</h2>
							
							                    <div class="form-control-fld terms-and-conditions-block">
							                        <div class="col-md-3 padding-none tc-block-fld">
							                            <div class="input-prepend">
							                                <input type="text" name="terms_condtion_types1"  class="form-control form-control1 clsRIOSCancelCharges"  placeholder ='Cancellation Charges' id="cancellation1" />
							                                <span class="add-on unit">Rs</span>
							                            </div>
							                        </div>
							                        <div class="col-md-3 tc-block-btn"></div>
							                    </div>
							
							                    <div class="my-form-ocen">
							                        <div class="text-box form-control-fld terms-and-conditions-block">
							                            <div class="col-md-3 padding-none tc-block-fld">
							                                <div class="input-prepend">
							                                    <input type="text" name="terms_condtion_types2"  placeholder ='Other Charges' class="form-control form-control1 clsRIOSOtherCharges"  id="cancellation2" />
							                                    <span class="add-on unit">Rs</span>
							                                </div>
							                            </div>
							                            <div class="col-md-3 tc-block-btn"><input type="button" class="add-box-ocen btn add-btn" value="Add"></div>
							                        </div>
							                    </div>
							
							
							
							                    <div class="col-md-6 form-control-fld">
							                          {!! Form::textarea('terms_conditions',null,['class'=>'form-control form-control1 clsTermsConditions','id'=>'terms_conditions', 'rows' => 5,'placeholder' => 'Notes to Terms & Conditions (Optional)']) !!}
							                    </div>
							                    <div class="clearfix"></div>
												</div>

											<div class="col-md-12 inner-block-bg inner-block-bg1">
					
						                    <div class="col-md-3 form-control-fld margin-none">
						                        <div class="normal-select">
						                            {!! Form::select('ocen_tracking',(['' => 'Tracking*']+$trackingtypes), null, ['id' => 'ocen_tracking_vechile','class' => 'selectpicker form-control']) !!}
						                        </div>
						                    </div>
					                   
					                   		<div class="clearfix"></div>
					
						                    <h2 class="filter-head1">Payment Terms</h2>
						
						                    <div class="col-md-3 form-control-fld">
						                        <div class="normal-select">
						                            {!! Form::select('ocean_paymentterms', ($paymentterms), null, ['class' => 'selectpicker','id' => 'oceanpayment_options']) !!}
						                        </div>
						                    </div>
					
						                    <div class="col-md-12 form-control-fld" id="oceanshow_advanced_period">
						                        <div class="check-block">
						                            <div class="checkbox_inline">{!! Form::checkbox('accept_payment_ocen[]', 1, '', false, ['class' => 'accept_payment_ocen']) !!}<span class="lbl padding-8">NEFT/RTGS</span></div>
						                            <div class="checkbox_inline">{!! Form::checkbox('accept_payment_ocen[]', 2, '', false, ['class' => 'accept_payment_ocen']) !!}<span class="lbl padding-8">Credit Card</span></div>
						                            <div class="checkbox_inline">{!! Form::checkbox('accept_payment_ocen[]', 3, '', false, ['class' => 'accept_payment_ocen']) !!}<span class="lbl padding-8">Debit Card</span></div>
						
						                        </div>
						                    </div>
					
						                    <div class="col-md-12 form-control-fld" style="display: none;" id="oceanshow_credit_period">
						                        <div class="col-md-3 form-control-fld padding-left-none">
						                        	
						                        	<div class="col-md-7 padding-none">
						                        		<div class="input-prepend">
						                                {!! Form::text('credit_period_ocen',null,['class'=>'form-control form-control1 clsIDCredit_periodOcean clsCreditPeriod','placeholder'=>'Credit Period']) !!}
						                                
						                            </div>
						                        	</div>
						                        	<div class="col-md-5 padding-none">
						                        		<div class="input-prepend">
						                        			<span class="add-on unit-days">
						                                            <div class="normal-select">
						                                                {!! Form::select('credit_period_units_ocen',['Days' => 'Days','Weeks' => 'Weeks'], null, ['class' => 'selectpicker clsSelPaymentCreditTypeOcean bs-select-hidden', 'data-posttype' => 'Ocean']) !!}       
						                                            </div>
						                                        </span>
						                        		</div>
						                        	</div>
						                        
						                            
						                            
						                        </div>
						
						                        <div class="col-md-12 padding-none">
						                            <div class="checkbox_inline">
						                                {!! Form::checkbox('accept_credit_netbanking_ocen[]', 1, false) !!}<span class="lbl padding-8">Net Banking</span>
						                                {!! Form::checkbox('accept_credit_netbanking_ocen[]', 2, false) !!}<span class="lbl padding-8">Cheque / DD</span>
						                            </div>
						                        </div>
						                    </div>
					
					                    <div class="clearfix"></div>
					          </div>
							
							
							
							<div class="col-md-12 inner-block-bg inner-block-bg1">
					                    <div class="col-md-12 form-control-fld">
					                        <div class="radio-block">
					                            <div class="radio_inline"><input type="radio" name="optradio_ocen" id="post-public-ocen" value="1" checked="checked" class="create-posttype-service-ocen" /> <label for="post-public-ocen"><span></span>Post Public</label></div>
					                            <div class="radio_inline"><input type="radio" name="optradio_ocen" id="post-private-ocen" value="2" class="create-posttype-service-ocen" /> <label for="post-private-ocen"><span></span>Post Private</label></div>
					                        </div>
					                    </div>
				
					                    <div class="col-md-3 form-control-fld demo-input_buyers_ocen padding-right-none" style="display:none">
						                    <div class="input-prepend">
						                        <input type="hidden" id="demo-input-ocen" name="buyer_list_for_sellers_ocen" />
						                    </div>
					                    </div>
				
				                    	<div class="clearfix"></div>
					                    <div class="check-box form-control-fld margin-none">
					                    {!! Form::checkbox('agree', 1, false,array('id'=>'agree')) !!}<span class="lbl padding-8">Accept Terms & Conditions (Digital Contract)</span>
					                    </div>
				              </div>


                <div class="col-md-12 padding-none">
                    {!! Form::submit('Confirm', ['class' => 'btn theme-btn flat-btn pull-right','name' => 'confirm','id' => 'add_quote_seller_id_oceanint','onclick'=>"oceanupdatepoststatus(1)"]) !!}
			        {!! Form::submit('Save as Draft', ['class' => 'btn add-btn flat-btn pull-right','name' => 'draft','id' => 'add_quote_seller_oceanint','onclick'=>"oceanupdatepoststatus(0)"]) !!}
                   
                </div>
							
<!-- Right Section Ends Here -->

  {!! Form::close() !!}				

