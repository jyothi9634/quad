{!! Form::open(['url' =>'#','id' => 'relocationair_term_firstform' , 'autocomplete'=>'off']) !!}
{!! Form::hidden('term_check_valid', '', array('id' => 'term_check_valid')) !!}

<input type="hidden" name="update_relocterm_line" id="update_relocterm_line" value="">
<input type="hidden" name="update_relocterm_row_count" id="update_relocterm_row_count" value=""> 
<div class="col-md-12 inner-block-bg inner-block-bg1 border-top-none padding-top-none border-bottom-none padding-bottom-none margin-none">
<div class="col-md-3 form-control-fld">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-calendar-o"></i></span>
									{!! Form::text('term_dispatch_date', '', ['id' => 'term_dispatch_date','class' => 'form-control calendar  from-date-control', 'placeholder' => 'Valid From *','readonly'=>"readonly"]) !!}
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
									{!! Form::text('term_from_location', '', ['id' => 'term_from_location', 'class'=>'form-control', 'placeholder' => 'From Location *']) !!}
								   	{!! Form::hidden('term_from_location_id', '', array('id' => 'term_from_location_id')) !!}
								</div>
							</div>
							<div class="col-md-3 form-control-fld">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-map-marker"></i></span>
									{!! Form::text('term_to_location', '', ['id' => 'term_to_location', 'class'=>'form-control','placeholder' => 'To Location *']) !!}
									{!! Form::hidden('term_to_location_id', '', array('id' => 'term_to_location_id')) !!}
								</div>
							</div>
                                                                        <div class="col-md-3 form-control-fld relocation_air_field">
										<div class="input-prepend">
											<input type="text" class="form-control form-control1 clsRIATNoofMoves" placeholder="No of Moves should be less than 200 *" name="relocation_term_noofmoves" id="relocation_termair_noofmoves" />
											
										</div>
									</div>	
    
    
                                                                        <div class="col-md-3 form-control-fld relocation_ocean_field" style="display:none">
										<div class="input-prepend">
											<input type="text" class="form-control form-control1 clsRIATNoofMoves" placeholder="No of Moves should be less than 200 *" name="relocation_term_noofmoves" id="relocation_termocean_noofmoves" />
											
										</div>
									</div>	

									<div class="col-md-3 form-control-fld relocation_air_field">
										<div class="input-prepend">
											<input type="text" placeholder="Average KG per Move should be less than 200 *" class="form-control form-control1 clsRIATAvgKgPerMove" name="relocation_term_kg_move" id="relocation_term_kg_move">
											<!--span class="add-on unit1 manage">Days</span-->
										</div>
									</div>	
									
									<div class="col-md-3 form-control-fld relocation_ocean_field" style="display:none">
										<div class="input-prepend">
											<input type="text" placeholder="Average CBM per Move *" class="form-control form-control1 clsRIOTAvgCBMpMove" name="relocation_term_cbm_move" id="relocation_term_cbm_move">
											<!--span class="add-on unit1 manage">Days</span-->
										</div>
									</div>	

									<div class="col-md-3 form-control-fld">
									<input type="submit" value="Add" class="btn add-btn" id="term_add_relocationair">
								    <div id="error-relocation-term-add-item" class="error "></div>
									</div>
									
				{!! Form::close() !!}
	</div>	
<div class="clearfix"></div>
{!! Form::open(['url' =>'relocationbuyertermcreate','id' => 'term_relocbuyer_quote', 'files'=>true, 'autocomplete'=>'off']) !!}
{!! Form::hidden('spot_term_value', '2', array('id' => 'spot_term_value')) !!}
{!! Form::hidden('post_type_term', '1', array('id' => 'post_type_term')) !!}
{!! Form::hidden('check_post_valid_type', 1, array('id' => 'check_post_valid_type')) !!}
<div class="inner-block-bg inner-block-bg1 border-top-none padding-top-none">
<div class="table-div table-style1 margin-none">
<div class="table-heading inner-block-bg">
									<div class="col-md-3 padding-left-none">From Location</div>
									<div class="col-md-3 padding-left-none">To Location</div>
									<div class="col-md-3 padding-left-none">No of Moves</div>
									<div class="col-md-3 padding-left-none" id="check_air_ocean_sel">Average KG/Move</div>
									</div>
									<div class="table-data relocation_term_request_rows">										
									<!-- Table Row Starts Here -->
									<input type="hidden" id='next_term_add_relocation_buyer_more_id' value='0'>
									<!-- Table Row Ends Here -->
									</div>
							</div>
									<div class="col-md-3 form-control-fld">
									<div class="radio-block">
									<input type="checkbox" name="source_storage" id="source_storage"> <span class="lbl padding-8">Storage</span></div>
									<div class="radio-block relocation_ocean_field" style="display:none"><input type="checkbox" name="source_handyman" id="source_handyman"> <span class="lbl padding-8">Handyman Services</span></div>
									</div>
	
									<div class="col-md-3 form-control-fld">
										<div class="radio-block"><input type="checkbox" name="destination_storage" id="destination_storage"> <span class="lbl padding-8">Storage</span></div>
										<div class="radio-block relocation_ocean_field" style="display:none"><input type="checkbox" name="destination_handyman" id="destination_handyman"> <span class="lbl padding-8">Handyman Services</span></div>
									</div>	
 </div>															
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
										<span class="add-on" ><i class="fa fa-clock-o"></i></span>
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
							<textarea  class="form-control form-control1" name="buyer_notes" id="buyer_notes" placeholder="Comments"></textarea>	
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
						{!! Form::submit('Float RFP', ['name' => 'confirm','class'=>'btn theme-btn flat-btn pull-right term_relocation_add_buyer_quote','id' => 'term_relocationint_add_buyer_quote']) !!}
						{!! Form::submit('Save As Draft', ['name' => 'draft','class'=>'btn add-btn flat-btn pull-right term_relocation_add_buyer_quote','id' => 'term_relocationint_add_buyer_quote_draft']) !!}
					
				</div>
{!! Form::close() !!}																																			