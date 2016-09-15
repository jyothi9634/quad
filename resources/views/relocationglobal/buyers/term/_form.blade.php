{!! Form::open(['url' =>'relocationbuyertermcreate','id' => 'term_relocgmbuyer_quote', 'files'=>true, 'autocomplete'=>'off']) !!}
{!! Form::hidden('spot_term_value', '2', array('id' => 'spot_term_value')) !!}
{!! Form::hidden('term_check_valid', '', array('id' => 'term_check_valid')) !!}
	<input type="hidden" id="term_post_rate_card_type" name="term_post_rate_card_type" class="termratetype_selection_buyer" value="1">
	<div class="col-md-12 inner-block-bg inner-block-bg1 border-top-none ">
		<div class="col-md-12 padding-none inner-form margin-bottom-none">
			
			<div class="col-md-3 form-control-fld">
					<div class="input-prepend">
						<span class="add-on"><i class="fa fa-calendar-o"></i></span>
						{!! Form::text('term_dispatch_date', '', ['id' => 'term_dispatch_date','class' => 'calender form-control calendar  from-date-control', 'placeholder' => 'Valid From *','readonly'=>"readonly"]) !!}
					</div>
				</div>
				<div class="col-md-3 form-control-fld">
					<div class="input-prepend">
						<span class="add-on"><i class="fa fa-calendar-o"></i></span>
						{!! Form::text('term_delivery_date', '', ['id' => 'term_delivery_date','class' => 'form-control calendar  to-date-control', 'placeholder' => 'Valid To *','readonly'=>"readonly"]) !!}
					</div>
				</div>
				<div class="col-md-3 form-control-fld">
					<div class="input-prepend">
						<span class="add-on"><i class="fa fa-map-marker"></i></span>
						{!! Form::text('from_location', '', ['id' => 'from_location', 'class'=>'form-control', 'placeholder' => 'Location *']) !!}
					   	{!! Form::hidden('from_location_id', '', array('id' => 'from_location_id')) !!}
					</div>
					
				</div>
		</div>
	</div>

<!-- Start : service partial-->
	@include('relocationglobal.buyers.buyer_services')	
<!-- End : service partial-->

		<div class="col-md-12 inner-block-bg inner-block-bg1">
			<!-- bid type section starts-->
			<div class="col-md-12 padding-none inner-form margin-bottom-none">
			<div class="margin-top"></div>
			
					
				<div class="col-md-3 form-control-fld">
						<div class="input-prepend">
							<span class="add-on"><i class="fa fa-calendar-o"></i></span>
							{!! Form::text('last_bid_date', '', ['id' => 'last_bid_date','class' => 'form-control calendar', 'placeholder' => 'Bid Closure Date *','readonly'=>"readonly"]) !!}
						</div>
			 	 </div>
					
				<div class="col-md-3 form-control-fld">
					<div class="input-prepend date clsbid_close_time" id="bid_time_icon_add"> 
						<span class="add-on"><i class="fa fa-clock-o"></i></span>
						{!! Form::text('bid_close_time', '', ['id' => 'bid_close_time','class' => 'form-control clock timepicker', 'placeholder' => 'Bid Closure Time *', 'readonly'=>"readonly"]) !!}
					</div>
	                <label for="bid_close_time" id="err_bid_close_time" class="error"></label>
				</div>
					
				<div class="clearix"></div>
				<div class="col-md-12 form-control-fld"><span>Bid Terms & Conditions</span></div>
			
			</div>
			<!-- 	bid type section ends-->
			
			<!--file upload div starts-->
			<input type="hidden" name ='term_next_terms_count_search' id='term_next_terms_count_search' value='0'>
			<div class="documents-terms">
			<div class="col-md-12 padding-none text-box">
			<span style='display:none;' class="box-number-delete">2</span>
				<div class="col-md-4 form-control-fld">
					 	              
	<!-- 	                			<input type="text" class="form-control form-control1 upload-control" readonly>  -->
								<div class="upload-fld">
									<button class="btn add-btn upload-browse-btn pull-right">Browse</button>
									<input type="file" name="terms_condtion_types_term_defualt" class="form-control form-control1 update_txt" value="" id="terms_condtion_types_term_defualt" />
					            </div>
							</div>
							<div class="col-md-3 form-control-fld">	
						           		 									
				
						<input type="button" class="documents-add btn add-btn" value="Add +">
					</div>	
					<div class="clearfix"></div>													
			</div>
			</div>
			
			<div class="col-md-6 form-control-fld">								
					<textarea  class="form-control form-control1" name="buyer_notes" id="buyer_notes" placeholder="Comments" maxlength="500"></textarea>	
			</div>	
			

		</div>
		
			<!--file upload div ends-->
		
		
		<div class="col-md-12 inner-block-bg inner-block-bg1">	
			<div class="col-md-12 form-control-fld margin-none padding-none">
				<div class="radio-block">
	            <div class="radio_inline"><input type="radio" name="quoteaccess_id" value="1" id="term_relocation_post_public" checked> <label for="term_relocation_post_public"><span></span>Post Public</label></div>
				<div class="radio_inline"><input type="radio" name="quoteaccess_id" value="2" id="term_relocation_post_private" class="create-posttype-service-ftl-term"> <label for ='term_relocation_post_private' class="create-posttype-service-ftl lbl padding-8"><span></span>Post Private</label></div>
	        </div>
			</div>
			
			<div class="clearfix"></div>
			
               <input type="hidden" name="service_global_id" id="service_global_id" value="19">
			
			<div class="col-md-3 form-control-fld" id="showhidepost" style="display:none;">
			<input type="text" class="form-control form-control1" id="term_seller_list" name="term_seller_list" placeholder="Seller Name (Auto Search)"/>
			</div>
			<div class="clearfix"></div>
			<div class="normal-checkbox">
			   {!! Form::checkbox('agree', '', '', ['class' => 'field','id'=>'agree']) !!} <span class="lbl padding-8">Accept Terms & Conditions (Digital Contract)</span>
			   </div>
		</div>


	<div class="clearfix"></div>


		<div class="col-md-12 padding-none">
		<input type="hidden" name="confirm_but" id="confirm_but" value="">
			{!! Form::submit('Float RFP', ['name' => 'confirm','class'=>'btn theme-btn flat-btn pull-right term_relocationgm_add_buyer_quote','id' => 'term_relocationgm_add_buyer_quote']) !!}
			{!! Form::submit('Save As Draft', ['name' => 'draft','class'=>'btn add-btn flat-btn pull-right term_relocationgm_add_buyer_quote','id' => 'term_relocationgm_add_buyer_quote_draft']) !!}
		
	</div>
{!! Form::close() !!}
