@inject('commonComponent', 'App\Components\CommonComponent')
{{--*/ $room_types=$commonComponent::getAllRoomTypes()  /*--}}
{{--*/ $property_types = $commonComponent::getAllPropertyTypes() /*--}}

  {{--*/ $searchrequest=array(); /*--}}
  {{--*/ $from_loaction=''; /*--}}
  {{--*/ $to_loaction=''; /*--}}
  {{--*/ $from_loaction_id=''; /*--}}
  {{--*/ $to_loaction_id=''; /*--}}
  {{--*/ $from_date=''; /*--}}
  {{--*/ $to_date=''; /*--}}
  {{--*/ $property_type=''; /*--}}
 
 @if(Session::has('searchMod'))    
  {{--*/ $searchrequest=Session::get('searchMod'); /*--}}
  @if($searchrequest['post_type_buyer']==2)
  {{--*/ $from_loaction=$searchrequest['from_location_buyer']; /*--}}
  {{--*/ $to_loaction=$searchrequest['to_location_buyer']; /*--}}
  {{--*/ $from_loaction_id=$searchrequest['from_city_id_buyer']; /*--}}
  {{--*/ $to_loaction_id=$searchrequest['to_city_id_buyer']; /*--}}
  {{--*/ $from_date=$searchrequest['dispatch_date_buyer']; /*--}}
  {{--*/ $to_date=$searchrequest['delivery_date_buyer']; /*--}}
  {{--*/ $property_type=$searchrequest['property_type_buyer'];  /*--}}
  @endif
  @endif

 

<div class="col-md-12 inner-block-bg inner-block-bg1 border-top-none padding-top-none">
					
                        <div class="col-md-12 padding-none">

                                <div class="col-md-3 form-control-fld">
                                        <div class="input-prepend">
                                                <span class="add-on"><i class="fa fa-map-marker"></i></span>                                                
                                                {!! Form::text('from_location_intre',$from_loaction , ['id' => 'from_location_intre','class' => 'form-control', 'placeholder' => 'From Location (Only Major Cities) *']) !!}
                                                {!! Form::hidden('from_location_id_intre', $from_loaction_id, array('id' => 'from_location_id_intre')) !!}
                                                {!! Form::hidden('seller_district_id_intre', '', array('id' => 'seller_district_id_intre')) !!}
                                        </div>
                                </div>
                                <div class="col-md-3 form-control-fld">
                                        <div class="input-prepend">
                                                <span class="add-on"><i class="fa fa-map-marker"></i></span>                                                
                                                {!! Form::text('to_location_intre', $to_loaction, ['id' => 'to_location_intre','class' => 'form-control', 'placeholder' => 'To Location (Only Major Cities) *']) !!}
                                                {!! Form::hidden('to_location_id_intre', $to_loaction_id, array('id' => 'to_location_id_intre')) !!}
                                        </div>
                                </div>
                                <div class="col-md-3 form-control-fld">
                                        <div class="input-prepend">
                                                <span class="add-on"><i class="fa fa-calendar-o"></i></span>
                                                {!! Form::text('valid_from', $from_date, ['id' => 'ptlDispatchDate','class' => 'flexible_dispatch_date form-control calendar from-date-control', 'placeholder' => 'Dispatch Date *','readonly'=>"readonly"]) !!}
						<input type="hidden" name="dispatch_flexible_hidden_relocint" id="ptlFlexiableDispatch_hidden" value="0">
                                        </div>
                                </div>
                                <div class="col-md-3 form-control-fld">
                                        <div class="input-prepend">
                                                <span class="add-on"><i class="fa fa-calendar-o"></i></span>
                                                {!! Form::text('valid_to', $to_date, ['id' => 'ptlDeliveryhDate','class' => 'flexible_delivery_date form-control calendar to-date-control', 'placeholder' => 'Delivery Date','readonly'=>"readonly"]) !!}
						<input type="hidden" name="delivery_flexible_hidden_relocint" id="ptlFlexiableDelivery_hidden" value="0">
                                        </div>
                                </div>
                             <div class="clearfix"></div>
                                <div class="col-md-3 form-control-fld">
                                    <div class="input-prepend">
                                        <span class="add-on"><i class="fa fa-home"></i></span>
                                        {!! Form::select('property_type',(['' => 'Property Type *'] +$property_types), $property_type ,['class' =>'selectpicker','id'=>'property_type','onchange'=>'return getPropertyCft()']) !!}
                                    </div>

                                </div>

                                <div class="clearfix"></div>

                                <div class="advanced-search-details">

                                        <div class="col-md-3 form-control-fld">
                                            <div class="radio-block"><input type="checkbox" checked name="origin_storage_serivce" id="origin_storage_serivce" /> <span class="lbl padding-8" >Storage</span></div>
                                            <div class="radio-block"><input type="checkbox" name="origin_handy_serivce" id="origin_handy_serivce"> <span class="lbl padding-8">Handyman Services</span></div>
                                            <div class="radio-block"><input type="checkbox" name="insurance_serivce" id="insurance_serivce"> <span class="lbl padding-8">Insurance</span></div>                                            
                                        </div>
                                        <div class="col-md-3 form-control-fld">
                                            <div class="radio-block"><input type="checkbox" name="destination_storage_serivce" id="destination_storage_serivce"> <span class="lbl padding-8">Storage</span></div>
                                            <div class="radio-block"><input type="checkbox" name="destination_handy_serivce" id="destination_handy_serivce"> <span class="lbl padding-8">Handyman Services</span></div>
                                        </div>

                                        <div class="clearfix"></div>

                                <h2 class="filter-head1 margin-bottom">Complete Inventory</h2>

                                <div class="col-md-3 form-control-fld margin-top">
                                    <div class="normal-select">
                                           {!! Form::select('room_type',(['' => 'Select Inventory *'] +$room_types), '' ,['class' =>'selectpicker select-inventory','id'=>'room_type','onchange'=>'return getRelIntOceanRoomParticulars()']) !!}
                                    </div>
                                </div>	
                                <div class="clearfix"></div>
                                        <!-- Table Starts Here -->

            <div class="table-div table-style1 inventory-block margin-bottom-none">
                    <div class="table-div table-style1 inventory-table">									
                            <!-- Table Head Starts Here -->
                            <div class="table-heading inner-block-bg">
                                <div class="col-md-6 padding-left-none">&nbsp;</div>
                                <div class="col-md-2 padding-left-none text-center">No of Items</div>
                                <div class="col-md-2 padding-left-none text-center">Packing Required</div>
                                <div class="col-md-2 padding-left-none text-center">Crating Required</div>									
                            </div>
                        <!-- Table Head Ends Here -->
                        <div id="inventory_data" name="inventory_data"></div>									
                    </div>	

                                    <!-- Table Starts Here -->
                    <div class="col-md-12 form-control-fld">
                    <input type="button" class="btn add-btn pull-right save-continue-rel-intocean" name="savecontinue" id="savecontinue" value="Save & Continue">								
                    </div>							
                    <div class="clearfix"></div>
                    <div class="after-inventory-block margin-top">								
                        <div class="table-div table-style1">									
                            <div name="inventory_count_div" id="inventory_count_div"></div>
                        </div>									
                    </div>		
            </div>	
    </div>
            <div class="col-md-12 form-control-fld text-right margin-none">
                <span class="red spl-link advanced-search-link"><span class="more-search">+</span><span class="less-search">-</span> Inventory Details</span>
            </div>
</div>
				
</div>

<div class="col-md-12 inner-block-bg inner-block-bg1">


    <div class="col-md-12 form-control-fld margin-none">
       <div class="radio-block">
        <div class="radio_inline">
        <input type="radio" name="ptlQuoteaccessId" value="1" id="post-public-relocean" checked="checked" class="create-relocationint-ocean" /> 
        <label for="post-public-relocean"><span></span>Post Public</label></div>
        <div class="radio_inline"><input type="radio" name="ptlQuoteaccessId" value="2" id="post-private-relocean" class="create-relocationint-ocean"/> 
        <label for="post-private-relocean"><span></span>Post Private</label></div>
        </div>
    </div>

    <div class="col-md-3 form-control-fld" id="hideseller_relocean" style="display:none;">
         <input type="text" id="demo-input-local-relocean" class="form-control form-control1" name="seller_list" />
    </div>
    <div class="clearfix"></div>

    <div class="clearfix"></div>
    <div class="check-box form-control-fld">
        {!! Form::checkbox('agree', 1, false, ['class' => 'field','id'=>'agree']) !!} <span class="lbl padding-8">Accept Terms & Conditions (Digital Contract)</span>
</div>
</div>
<div class="clearfix"></div>


    <div class="col-md-4 col-md-offset-4">
            <button class="btn theme-btn btn-block">Get Quote</button>
    </div>

                                