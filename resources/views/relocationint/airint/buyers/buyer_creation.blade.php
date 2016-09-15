@inject('commonComponent', 'App\Components\CommonComponent')

{{--*/ $cartons =   $commonComponent->getCartons(); /*--}}

{{--*/ $searchrequest=array(); /*--}}
  {{--*/ $from_loaction=''; /*--}}
  {{--*/ $to_loaction=''; /*--}}
  {{--*/ $from_loaction_id=''; /*--}}
  {{--*/ $to_loaction_id=''; /*--}}
  {{--*/ $from_date=''; /*--}}
  {{--*/ $to_date=''; /*--}}
  {{--*/ $dispatch_flexible_hidden=0; /*--}}
  {{--*/ $delivery_flexible_hidden=0; /*--}}
  {{--*/ $cart[1]=''; /*--}}
  {{--*/ $cart[2]=''; /*--}}
  {{--*/ $cart[3]=''; /*--}}
@if(Session::has('searchMod'))
 {{--*/ $searchrequest=Session::get('searchMod'); /*--}}
 @if($searchrequest['service_type_buyer']==1)
  {{--*/ $from_loaction=$searchrequest['from_location_buyer']; /*--}}
  {{--*/ $to_loaction=$searchrequest['to_location_buyer']; /*--}}
  {{--*/ $from_loaction_id=$searchrequest['from_city_id_buyer']; /*--}}
  {{--*/ $to_loaction_id=$searchrequest['to_city_id_buyer']; /*--}}
  {{--*/ $from_date=$searchrequest['dispatch_date_buyer']; /*--}}
  {{--*/ $to_date=$searchrequest['delivery_date_buyer']; /*--}}
  {{--*/ $dispatch_flexible_hidden=$searchrequest['dispatch_flexible_hidden']; /*--}}
  {{--*/ $delivery_flexible_hidden=$searchrequest['delivery_flexible_hidden']; /*--}}
  {{--*/ $cart[1]=$searchrequest['cartons_1']; /*--}}
  {{--*/ $cart[2]=$searchrequest['cartons_2']; /*--}}
  {{--*/ $cart[3]=$searchrequest['cartons_3']; /*--}}
  @endif
@endif


<div class="col-md-12 inner-block-bg inner-block-bg1 border-top-none padding-top-none">

<div class="col-md-3 form-control-fld">
        <div class="input-prepend">
                <span class="add-on"><i class="fa fa-map-marker"></i></span>
                {!! Form::text('from_location',$from_loaction , ['id' => 'from_location','class' => 'form-control', 'placeholder' => 'From Location (Only Major Cities) *']) !!}
                {!! Form::hidden('from_location_id', $from_loaction_id, array('id' => 'from_location_id')) !!}
                {!! Form::hidden('seller_district_id', '', array('id' => 'seller_district_id')) !!}
                
        </div>
</div>
<div class="col-md-3 form-control-fld">
        <div class="input-prepend">
                <span class="add-on"><i class="fa fa-map-marker"></i></span>
                {!! Form::text('to_location', $to_loaction, ['id' => 'to_location','class' => 'form-control', 'placeholder' => 'To Location (Only Major Cities) *']) !!}
		            {!! Form::hidden('to_location_id', $to_loaction_id, array('id' => 'to_location_id')) !!}
        </div>
</div>
<div class="col-md-3 form-control-fld">
        <div class="input-prepend">
                <span class="add-on"><i class="fa fa-calendar-o"></i></span>
                {!! Form::text('valid_from', $from_date, ['id' => 'dispatch_date1','class' => 'calendar form-control from-date-control','readonly' => true, 'placeholder' => 'Dispatch Date*']) !!}
                <input type="hidden" name="is_dispatch_flexible_hidden" id="dispatch_flexible_hidden" value="{{$dispatch_flexible_hidden}}">
        </div>
</div>
<div class="col-md-3 form-control-fld">
        <div class="input-prepend">
                <span class="add-on"><i class="fa fa-calendar-o"></i></span>
                {!! Form::text('valid_to', $to_date, ['id' => 'delivery_date1','class' => 'calendar form-control to-date-control','readonly' => true, 'placeholder' => 'Delivery Date']) !!}
                <input type="hidden" name="delivery_flexible_hidden" id="delivery_flexible_hidden" value="{{$delivery_flexible_hidden}}">
        </div>
</div>


<div class="clearfix"></div>
<div class="advanced-search-details" style="display: block;">
        <!-- Table Starts Here -->
        <div class="table-div table-style1">
            <!-- Table Head Starts Here -->
            <div class="table-heading inner-block-bg">
                    <div class="col-md-8 padding-left-none">Carton Type</div>
                    <div class="col-md-4 padding-left-none">Nos</div>
            </div>
            <!-- Table Head Ends Here -->
            <div class="table-data">
                    <!-- Table Row Starts Here -->
                    @foreach($cartons as $carton)
                    <div class="table-row inner-block-bg">
                            <div class="col-md-8 padding-left-none">{{ $carton->carton_type }} ({{ $carton->carton_description }})</div>
                            <div class="col-md-4 padding-left-none">
<!--                                    <input type="text" class="form-control form-control1 input-short pull-left">-->
                                    <input type="text" class="cartons form-control form-control1 input-short pull-left clsRIASNoOfCartons" name="cartons_{{ $carton->id}}" value="<?php echo $cart["$carton->id"]; ?>" />
                            </div>
                    </div>
                    @endforeach
                    <!-- Table Row Ends Here -->
                    
            </div>
    </div>
</div>
<div class="clearfix"></div>
<div class="col-md-12 form-control-fld text-right">
        <span class="red spl-link advanced-search-link">
            <span class="more-search" style="display: none;">+</span>
            <span class="less-search" style="display: inline;">-</span> Inventory Details</span>
</div>
</div>



<div class="col-md-12 inner-block-bg inner-block-bg1">
        <div class="col-md-12 form-control-fld margin-top margin-bottom-none">
                <div class="radio-block">
                <div class="radio_inline">
                <input type="radio" name="ptlQuoteaccessId" value="1" id="post-public" checked="checked" class="create-posttype-service crete-relocationair" /> 
                <label for="post-public"><span></span>Post Public</label></div>
                <div class="radio_inline"><input type="radio" name="ptlQuoteaccessId" value="2" id="post-private" class="create-posttype-service crete-relocationair"/> 
                <label for="post-private"><span></span>Post Private</label></div>
                </div>
        </div>

        <div class="col-md-3 form-control-fld" id="hideseller" style="display:none;">
                <input type="text" id="demo-input-local" class="form-control form-control1" name="seller_list" />
        </div>
        
        <div class="clearfix"></div>
        <div class="check-box form-control-fld">
            {!! Form::checkbox('agree', 1, false,array('id'=>'agree')) !!}<span class="lbl padding-8">Accept Terms & Conditions (Digital Contract)</span>
        </div>
</div>
<div class="clearfix"></div>

        <div class="col-md-4 col-md-offset-4">
                <input  type="submit" class="btn theme-btn btn-block"  name="getquote" id="getquote" value="Get Quote">
        </div>
