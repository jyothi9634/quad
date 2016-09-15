@extends('app')
@section('content')
<div class="main-container">	
		<div class="container container-inner">
		  @if(Session::has('message_create_post')) 
         	<div class="flash">
			<p class="text-success col-sm-12 text-center flash-txt alert-success">
				{{ Session::get('message_create_post') }}
			</p>
			</div>
		@endif
			<!-- Left Nav Starts Here -->
			@include('partials.leftnav')
			<!-- Left Nav Ends Here -->
			
			<div class="col-lg-7 col-md-6 col-sm-12 col-xs-12 main-middle">
				<div class="bread-crumb">Home &nbsp;<i class="fa fa-angle-right"></i> &nbsp; Posts &nbsp;<i class="fa fa-angle-right"></i> &nbsp; Road PTL &nbsp;<i class="fa fa-angle-right"></i> &nbsp;  Post Form </div>
				<div class="block">
                                    {!! Form::open(['url' => 'addseller','id'=>'posts-form-lines']) !!}
					<div class="tab-nav underline">
						<ul id="tabs">
				            <li><a href="#">Message<span class="red superscript">9</span></a></li>
				            <li class="active"><a href="#">Posts9<span class="red superscript">9</span></a></li>
				            <li><a href="#">Orders<span class="red superscript">9</span></a></li>
				            <li><a href="#">Network<span class="red superscript">9</span></a></li>
				            <span class="post-but pull-right post-button"><a href="#">Post & Get Quote </a></span>
				        </ul>
					</div>
					<p>Post Type : Spot</p>
					<div class="col-md-3 col-sm-3 col-xs-12 padding-none form-group">
                                            {!! Form::radio('option_wise_ptl', 'Zone wise', false, ['id' => 'zone_wise_ptl']) !!}Zone wise
					</div>
					<div class="col-md-3 col-sm-3 col-xs-12 padding-none form-group">
                                            {!! Form::radio('option_wise_ptl', 'Location wise', false, ['id' => 'location_wise_ptl']) !!}Location wise
                                        </div>
					<div class="clearfix"></div>
					<div class="col-md-3 col-sm-3 col-xs-12 padding-none form-group">
                                            {!! Form::text('valid_from', '', ['id' => 'datepicker','class' => 'calendar form-control', 'placeholder' => 'Valid From']) !!}
					</div>
					<div class="col-md-3 col-sm-3 col-xs-12 padding-right-none mobile-padding-none form-group">
                                            {!! Form::text('valid_to', '', ['id' => 'datepicker_to_location','class' => 'calendar form-control', 'placeholder' => 'Valid To']) !!}
					</div>
					<div class="clearfix"></div>
					<div class="col-md-3 col-sm-3 col-xs-12 padding-none form-group">
                                            {!! Form::text('from_location', '', ['id' => 'from_location_ptl','class' => 'form-control', 'placeholder' => 'From Location with pin code / Zone']) !!}
					</div>
					<div class="col-md-3 col-sm-3 col-xs-12 padding-right-none mobile-padding-none form-group">
                                            {!! Form::text('to_location', '', ['id' => 'to_location_ptl','class' => 'form-control', 'placeholder' => 'To Location with pin code / Zone']) !!}
                                        </div>
                                            
					<div class="clearfix"></div>
					 <div class="col-md-3 col-sm-3 col-xs-4 padding-none form-group">
                                            {!! Form::text('price',null,['class'=>'form-control','id'=>'price_ptl','placeholder'=>'Rate per kg (Rs)']) !!}
					</div>
					<div class="col-md-3 col-sm-3 col-xs-4 padding-right-none form-group">
                                            {!! Form::text('transitdays',null,['class'=>'form-control','id'=>'transitdays_ptl','placeholder'=>'Transit Time']) !!}
					</div>
					<div class="col-md-3 col-sm-3 col-xs-4 padding-right-none ">
                                            {!! Form::select('units',['Days' => 'Days','Weeks' => 'Weeks'], null, ['class' => 'selectpicker','id'=>'transitdays_units_ptl']) !!}
					</div>
                                        <div class="col-md-4 col-sm-4 col-xs-2 text-right padding-right-none pull-right margin-bottom">
                                            <input type="button" value="Bulk Upload" class="btn margin-top">
                                            <input type="button" value="Add" class="btn margin-top">
					</div>
                                        {!! Form::close() !!}
					<div class="clearfix"></div>
                                        {!! Form::open(['url' => 'addseller','id'=>'posts-form']) !!}
					    <div class="table table-head">
					    <div class="col-md-12 padding-none">
					    <div class="col-md-3 col-sm-3 col-xs-4 padding-none">From Location</div>
					    <div class="col-md-3 col-sm-3 col-xs-4 padding-none">To Location</div>
					    <div class="col-md-3 col-sm-3 col-xs-4 padding-none">Rate per kg</div>
					    <div class="col-md-3 col-sm-3 col-xs-4 padding-none hidden-xs">Transit Time</div>
					    </div>
					    </div>
					    <div class="col-md-3 col-sm-3 col-xs-12 padding-none form-group">
                                                {!! Form::text('kgpercft',null,['class'=>'form-control','id'=>'kgpercft_ptl','placeholder'=>'Kg per CFT']) !!}
                                            </div>
					    <div class="col-md-3 col-sm-3 col-xs-12 padding-right-none mobile-padding-none form-group">Additional Charges</div>
					    <div class="col-md-2 col-sm-2 col-xs-12 mobile-padding-none form-group">
                                                {!! Form::text('pickup',null,['class'=>'form-control','id'=>'pickup_ptl','placeholder'=>'Pick Up']) !!}
                                            </div>
					    <div class="col-md-2 col-sm-2 col-xs-12 mobile-padding-none form-group">
                                                {!! Form::text('delivery',null,['class'=>'form-control','id'=>'delivery_ptl','placeholder'=>'Delivery']) !!}
                                            </div>
					    <div class="col-md-2 col-sm-2 col-xs-12 mobile-padding-none form-group padding-right-none">
                                                {!! Form::text('oda',null,['class'=>'form-control','id'=>'oda_ptl','placeholder'=>'ODA']) !!}
                                            </div>
					    <div class="clearfix"></div>
					    <div class="col-md-3 col-sm-3 col-xs-12 padding-none">
                                                {!! Form::select('tracking',['' => 'Tracking','1' => 'Milestone','2' => 'Real time'], null, ['id' => 'tracking_ptl','class' => 'selectpicker']) !!}
					    </div>
					    <div class="clearfix"></div>

					<div class="col-md-12 col-sm-12 padding-none border-top">
						<div class="heading">Payment Terms</div>
						<div class="col-md-6 col-sm-6 col-xs-12 padding-none form-group">
							
							<div class="col-md-6 col-sm-6 col-xs-12 padding-top mobile-padding-none tb-padding-none form-group">
                                                                {!! Form::select('paymentterms', ($paymentterms), null, ['class' => 'selectpicker','id' => 'payment_options']) !!}
                                                        </div>
							<div class="clearfix"></div>
                                                        <div class="checkbox-group" id = 'show_advanced_period'>
                                                            <div class="margin-bottom col-md-6 col-sm-6 col-xs-12 padding-none">
                                                                    {!! Form::checkbox('accept_payment_ptl[]', 1, '', false, ['class' => 'accept_payment_ptl']) !!}&nbsp;NEFT/RTGS
                                                            </div>

                                                            <div class="margin-bottom col-md-6 col-sm-6 col-xs-12 padding-none">
                                                                    {!! Form::checkbox('accept_payment_ptl[]', 2, '', false, ['class' => 'accept_payment_ptl']) !!}&nbsp;Credit Card
                                                            </div>


                                                            <div class="margin-bottom col-md-6 col-sm-6 col-xs-12 padding-none">
                                                                    {!! Form::checkbox('accept_payment_ptl[]', 3, '', false, ['class' => 'accept_payment_ptl']) !!}&nbsp;Debit Card
                                                            </div>

                                                        </div>	
						</div>
						<div class="col-md-6 col-sm-6 col-xs-12 padding-right-none mobile-padding-none" style ='display: none;' id = 'show_credit_period'>
							<div class="padding-top font-bold form-group">Credit </div>
							<div class="padding-top form-group col-xs-4 mobile-padding-none mobile-margin-top">Credit Period </div>
							<div class="padding-none col-sm-3 col-xs-4 mobile-padding-none">
                                                                {!! Form::text('credit_period_ptl',null,['class'=>'form-control','placeholder'=>'']) !!}
							</div>
							<div class="padding-none col-sm-3 col-xs-3 margin-left mobile-padding-none form-group">
                                                            {!! Form::select('credit_period_units',['Days' => 'Days','Weeks' => 'Weeks'], null, ['class' => 'selectpicker bs-select-hidden']) !!}
							</div>
							<div class="clearfix"></div>	
							<div class="col-md-5 col-sm-4 col-xs-12 padding-none form-group">
                                                           {!! Form::checkbox('accept_credit_netbanking', 1, false) !!}&nbsp;Net Banking
							</div>	
							<div class="col-md-5 col-sm-4 col-xs-12 padding-none form-group">
                                                            {!! Form::checkbox('accept_credit_cheque', 1, false) !!}&nbsp;Cheque / DD
							</div>
						</div>
						<div class="clearfix"></div>
					</div>
					<div class="col-md-12 col-sm-12 padding-none border-top">
                                                    <div class="">Terms &amp; Conditions</div>
							
								 
							  <div class="form-group form-group-width">
                                                                <div class="col-md-3 col-sm-4 col-xs-8 padding-top mobile-padding-none">
							  	<!--input type="checkbox"-->&nbsp;Cancellation Charges<span class="pull-right">Rs</span>
                                                                </div>
								<div class="col-md-2 col-sm-2 col-xs-6  mobile-margin-top">
							    		<input type="text" name = 'charges[]' class="form-control">
							    	 </div>
							    	<div class="col-md-2 col-sm-2 col-xs-2 mobile-margin-top">
							    			<input type="button" id ='terms_contion_add' value="Add" class="btn btn-black">
							    	</div>
                                                                <div class="clearfix"></div>
							  </div>
							  <div class="clearfix"></div>
                                                            
                                                            <div id ="multi-line-itemes">
                                                                <input type="hidden" id='terms_contion_add_id' value='0'>
                                                                <div class="terms_contion_add_rows" id="testDiv4_ptl"></div>
                                                            </div>
							  {!! Form::textarea('terms_conditions',null,['class'=>'form-control margin-bottom clsTermsConditions','placeholder'=>'Notes to Terms &amp; Conditions (optional)','id'=>'terms_conditions_ptl', 'rows' => 2, 'cols' => 57]) !!}
					    
					    
					</div>
					<div class="col-md-12 col-sm-12 padding-none border-top">
						<div class="col-md-3 col-sm-3 col-xs-6 padding-none">
                                                        {!! Form::radio('optradio', 'Public', false, ['id' => 'post-public']) !!}Post Public
					    </div>
					    <div class="col-md-3 col-sm-3 col-xs-6 padding-none">
                                                        {!! Form::radio('optradio', 'Private', false, ['id' => 'post-private']) !!}Post Private
						</div>
						<div class="clearfix"></div>
                                                <div class="col-md-4 col-sm-4 col-xs-12 padding-none demo-input_buyers" style='display:none'>
						<input type="hidden" id="demo-input" name="buyer_list_for_sellers" class="form-control" />
                                                </div>
						<div class="clearfix"></div>	
						<div class="spacing margin-top">
                                                        {!! Form::checkbox('agree', 1, true,array('id'=>'agree_ptl')) !!}&nbsp;Accept Terms &amp; Conditions ( Digital Contract )
                                                </div>
						<div class="clearfix"></div>
						{!! Form::submit('Save as draft', ['class' => 'btn margin-top','name' => 'draft','id' => 'add_quote_seller_ptl']) !!}
						{!! Form::submit('Confirm', ['class' => 'btn margin-top','name' => 'confirm','id' => 'add_quote_seller_id_ptl']) !!}

					</div>
					
						
					

				</div>
			</div>
			{!! Form::close() !!}
			<!-- Right Starts Here -->
			@include('partials.right')
			<!-- Right Ends Here -->
		</div>
	</div>
</div>
@endsection
