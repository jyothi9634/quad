<div class="col-md-12 padding-none">
    <div class="col-md-3 padding-none text-center">
        <div class="radio-block">
            <div class="radio_inline">
                <input type="radio" name="lead_type" id="spot_lead_type" value="1" {{ (request('lead_type')==1 || !request('lead_type'))? 'checked="checked"':'' }} />
                <label for="spot_lead_type"><span></span>Spot</label>
            </div>
            <div class="radio_inline">
                <input type="radio" name="lead_type" id="term_lead_type" value="2" {{ (request('lead_type')==2)? 'checked="checked"':'' }}/>
                <label for="term_lead_type"> <span></span>Term</label>
            </div>
        </div>
    </div>
</div>    

{{-- Seller/Home/Relocation/International/Search/Spot/  --}}
<div class="showhide_spot" id="showhide_spot">
    {!! Form::open(['url' => 'buyersearchresults','id'=>'relocation_international_sellersearch_buyers_spot','method'=>'get']) !!}
        {!! Form::hidden('lead_type', '1', array('id' => 'post_type')) !!}
    <div class="col-md-12 padding-none">
        <div class="home-search-form">
            <div class="clearfix"></div>
            <div class="col-md-12 padding-none">
                <div class="col-md-12 form-control-fld">
                    <div class="radio-block">
                        {!! Form::radio('service_type', '1', (request('service_type')==1 || !request('service_type')), ['id' => 'spot_service_air']) !!}
                        <label for="spot_service_air"><span></span>Air</label>
                            
                        {!! Form::radio('service_type', '2', (request('service_type')==2), ['id' => 'spot_service_ocean']) !!}
                        <label for="spot_service_ocean"><span></span>Ocean</label>
                    </div>
                </div>
                <div class="col-md-4 form-control-fld">
                    <div class="input-prepend">
                        <span class="add-on"><i class="fa fa-map-marker"></i></span>
                        {!! Form::text('from_location', request('from_location') , ['id' => 'from_location','class' => 'form-control', 'placeholder' => 'From Location*']) !!}
                        {!! Form::hidden('from_location_id', request('from_location_id') , array('id' => 'from_location_id')) !!}
                        {!! Form::hidden('seller_district_id', '', array('id' => 'seller_district_id')) !!}
                    </div>
                </div>
                <div class="col-md-4 form-control-fld">
                    <div class="input-prepend">
                        <span class="add-on"><i class="fa fa-map-marker"></i></span>
                        {!! Form::text('to_location',request('to_location'),  ['id' => 'to_location','class' => 'form-control', 'placeholder' => 'To Location*']) !!}
                        {!! Form::hidden('to_location_id',  request('to_location_id') , array('id' => 'to_location_id')) !!}
                    </div>
                </div>

                <div class="col-md-4 form-control-fld">
                    <div class="input-prepend">
                        <span class="add-on"><i class="fa fa-calendar-o"></i></span>
                        {!! Form::text('valid_from', request('valid_from'),  ['id' => 'datepicker_search','class' => 'calendar form-control from-date-control','readonly' => true, 'placeholder' => 'Dispatch Date*']) !!}
                        <input type="hidden" name="dispatch_flexible_hidden" id="dispatch_flexible_hidden" value="0">
                        
                    </div>
                </div>
                <div class="clearfix"></div>
                {!! Form::hidden('is_search',1,array('class'=>'form-control')) !!}
                {!! Form::hidden('spot_or_term',1,array('class'=>'form-control')) !!}
                <div class="col-md-4 form-control-fld">
                    <div class="input-prepend">
                        <span class="add-on"><i class="fa fa-calendar-o"></i></span>
                        {!! Form::text('valid_to', request('valid_to') , ['id' => 'datepicker_to_location_search','class' => 'calendar form-control to-date-control','readonly' => true, 'placeholder' => 'Delivery Date ']) !!}
                        <input type="hidden" name="delivery_flexible_hidden" id="delivery_flexible_hidden" value="0">
                    </div>
                </div>
                <div class="clearfix"></div>
                  
            </div>
        </div>
    </div>
    <div class="col-md-4 col-md-offset-4">
        <button class="btn theme-btn btn-block">Search</button>
    </div>
    {!! Form::close() !!}
</div>
<div class="clearfix"></div>
{{-- Seller/Home/Relocation/International/Search/Term/  --}}
                        

<div class="showhide_term" id="showhide_term"  style="display:none">
    {!! Form::open(['url' =>'termsellersearchresults','method'=>'GET','id'=>'term_relocint_air_ocean']) !!}
    {!! Form::hidden('lead_type', '1', array('id' => 'post_type')) !!}
    <div class="">
        <div class="home-search-form">
            <div class="clearfix"></div>
            <div class="col-md-12 padding-none">
                <div class="col-md-12 form-control-fld">
                    <div class="radio-block">
                        <input type="radio" {{ (request('term_service_type')==1 || !request('term_service_type')) }} name="term_service_type" id="term_air" value="1">
                        <label for="term_air"><span></span>Air</label>
                            
                        <input type="radio" name="term_service_type" id="term_ocean" value="2" {{ (request('term_service_type')==2)? 'checked="checked"':'' }} >
                        <label for="term_ocean"><span></span>Ocean</label>
                    </div>
                </div>
                <div class="col-md-4 form-control-fld">
                    <div class="input-prepend">
                        <span class="add-on"><i class="fa fa-map-marker"></i></span>
                        {!! Form::text('term_from_location', request('term_from_location'), ['id' => 'term_from_location','class' => 'top-text-fld form-control', 'placeholder' => 'From Location*']) !!}
                        {!! Form::hidden('term_from_city_id', request('term_from_city_id')  , array('id' => 'term_from_location_id')) !!}
                        {!! Form::hidden('seller_district_id', request('seller_district_id') , array('id' => 'seller_district_id')) !!}
                        {!! Form::hidden('is_search',1,array('class'=>'form-control')) !!}
                        {!! Form::hidden('spot_or_term',2,array('class'=>'form-control')) !!}
                    </div>
                </div>
                <div class="col-md-4 form-control-fld">
                    <div class="input-prepend">
                        <span class="add-on"><i class="fa fa-map-marker"></i></span>
                        {!! Form::text('term_to_location', request('term_to_location'), ['id' => 'term_to_location','class' => 'top-text-fld form-control', 'placeholder' => 'To Location*']) !!}
                        {!! Form::hidden('term_to_city_id', request('term_to_city_id') , array('id' => 'term_to_location_id')) !!}
                    </div>
                </div>

                <div class="clearfix"></div>
            </div>
        </div>
    </div>
    <div class="submit_container">
        <div class="col-md-4 col-md-offset-4">
            <input type="submit" id ='buyersearchresults' value="&nbsp; Search &nbsp;" class="btn theme-btn btn-block">
        </div>
    </div>
    {!! Form::close() !!}
</div>
                