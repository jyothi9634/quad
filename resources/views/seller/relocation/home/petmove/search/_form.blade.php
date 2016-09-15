<div class="col-md-12 padding-none">
    <div class="col-md-4 form-control-fld">
        <div class="input-prepend">
            <span class="add-on"><i class="fa fa-map-marker"></i></span>
                {!! Form::text('from_location', request('from_location') , ['id' => 'from_location','class' => 'form-control', 'placeholder' => 'From Location*']) !!}
                {!! Form::hidden('from_location_id', request('from_location_id'), array('id' => 'from_location_id')) !!}
                {!! Form::hidden('is_search',1,array('class'=>'form-control')) !!}
        </div>
    </div>
    
    <div class="col-md-4 form-control-fld">
        <div class="input-prepend">
            <span class="add-on"><i class="fa fa-map-marker"></i></span>
            {!! Form::text('to_location', request('to_location'),  ['id' => 'to_location','class' => 'form-control', 'placeholder' => 'To Location*']) !!}
            {!! Form::hidden('to_location_id', request('to_location_id') , array('id' => 'to_location_id')) !!}
        </div>
    </div>

    <div class="col-md-4 form-control-fld">
        <div class="input-prepend">
            <span class="add-on"><i class="fa fa-calendar-o"></i></span>
            {!! Form::text('valid_from', request('valid_from'),  ['id' => 'datepicker_search','class' => 'calendar form-control from-date-control','readonly' => true, 'placeholder' => 'Dispatch Date*']) !!}
            <input type="hidden" name="dispatch_flexible_hidden" id="dispatch_flexible_hidden" value="<?php echo isset($_REQUEST['dispatch_flexible_hidden']) ? $_REQUEST['dispatch_flexible_hidden'] : ""; ?>">
        </div>
    </div>

    <div class="clearfix"></div>
    
    <div class="col-md-4 form-control-fld">
        <div class="input-prepend">
            <span class="add-on"><i class="fa fa-calendar-o"></i></span>
            {!! Form::text('valid_to', request('valid_to'),  ['id' => 'datepicker_to_location_search','class' => 'calendar form-control to-date-control','readonly' => true, 'placeholder' => 'Delivery Date']) !!}
            <input type="hidden" name="delivery_flexible_hidden" id="delivery_flexible_hidden" value="<?php echo isset($_REQUEST['delivery_flexible_hidden']) ? request('delivery_flexible_hidden'): ""; ?>">
        </div>
    </div>

    <div class="col-md-4 form-control-fld">
         <div class="input-prepend">
         <span class="add-on"><i class="fa fa-paw"></i></span>
         {!! Form::select('pet_type',(['' => 'Pet Type']+ $getAllPetTypes), request('pet_type'), ['class' =>'selectpicker','id'=>'pet_type' ]) !!}
         </div>
     </div>

</div>