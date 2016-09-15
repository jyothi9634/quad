@extends('app')
@section('content')
<!-- Page top navigation Starts Here-->
@include('partials.page_top_navigation')


<div class="main">
	<div class="container">
            
            {!! Form::open(['url' => 'buyersearchresults','id'=>'posts_form_sellersearch_relocationpet','method'=>'get']) !!}
            <div class="home-search gray-bg margin-top-none">
            
                <div class="col-md-12 padding-none">
                    <div class="col-md-4 form-control-fld">
                        <div class="input-prepend">
                            <span class="add-on"><i class="fa fa-map-marker"></i></span>
                            {!! Form::text('from_location', '' , ['id' => 'from_location','class' => 'form-control', 'placeholder' => 'From City *']) !!}
                            {!! Form::hidden('from_location_id', '' , array('id' => 'from_location_id')) !!}
                            {!! Form::hidden('seller_district_id', '', array('id' => 'seller_district_id')) !!}
                        </div>
                    </div>
                    
                    <div class="col-md-4 form-control-fld">
                        <div class="input-prepend">
                            <span class="add-on"><i class="fa fa-map-marker"></i></span>
                            {!! Form::text('to_location', '',  ['id' => 'to_location','class' => 'form-control', 'placeholder' => 'To City *']) !!}
                            {!! Form::hidden('to_location_id', '' , array('id' => 'to_location_id')) !!}
                        </div>
                    </div>
                    
                    <div class="col-md-4 form-control-fld">
                        <div class="input-prepend">
                            <span class="add-on"><i class="fa fa-calendar-o"></i></span>
                            {!! Form::text('valid_from', '',  ['id' => 'datepicker_search','class' => 'calendar form-control from-date-control','readonly' => true, 'placeholder' => 'Dispatch Date *']) !!}
                            <input type="hidden" name="dispatch_flexible_hidden" id="dispatch_flexible_hidden" value="0">
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-md-4 form-control-fld">
                        <div class="input-prepend">
                            <span class="add-on"><i class="fa fa-calendar-o"></i></span>
                            {!! Form::text('valid_to', '',  ['id' => 'datepicker_to_location_search','class' => 'calendar form-control to-date-control','readonly' => true, 'placeholder' => 'Delivery Date ']) !!}
                            <input type="hidden" name="delivery_flexible_hidden" id="delivery_flexible_hidden" value="0">
                        </div>
                    </div>

                    <div class="col-md-4 form-control-fld">
                        <div class="input-prepend">
                            <span class="add-on"><i class="fa fa-paw"></i></span>
                            {!!	Form::select('pet_type',(['' => 'Pet Type']+ $getAllPetTypes), '', ['class' =>'selectpicker','id'=>'pet_type' ]) !!}
                        </div>
                    </div>
                </div>
            </div> 
            
            <div class="col-md-4 col-md-offset-4">
                <button class="btn theme-btn btn-block">Search</button>
            </div>
            
            {!! Form::close() !!}
            	
            <div class="clearfix"></div>

	</div>
</div>

@include('partials.footer')
@endsection



