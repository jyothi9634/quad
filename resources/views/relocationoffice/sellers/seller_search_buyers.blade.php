@extends('app')
@section('content')
<!-- Page top navigation Starts Here-->
@include('partials.page_top_navigation')

<div class="main">
    <div class="container">
        <!-- Left Nav Starts Here -->
        <div class="home-search gray-bg margin-bottom-none padding-bottom-none border-bottom-none margin-top-none">
            <div class="col-md-3 padding-none text-center">
            </div>
        </div>
        <div class="clearfix"></div>

        {{-- Seller/Home/Transportation/FTL/Search/Spot --}}
        <div class="showhide_spot" id="showhide_spot">

            {!! Form::open(['url' => 'buyersearchresults','id'=>'relocation_domestic_office_sellersearch_buyers','method'=>'get']) !!}
                <div class="home-search gray-bg margin-top-none border-top-none padding-top-none">
                    <div class="col-md-12 padding-none">
                        <div class="col-md-4 form-control-fld">
                            <div class="input-prepend">
                                <span class="add-on"><i class="fa fa-map-marker"></i></span>
                                {!! Form::text('from_location', '' , ['id' => 'from_location','class' => 'form-control', 'placeholder' => 'City *']) !!}
                                {!! Form::hidden('from_location_id', '' , array('id' => 'from_location_id')) !!}
                                {!! Form::hidden('seller_district_id', '', array('id' => 'seller_district_id')) !!}
                            </div>
                        </div>
                        <div class="col-md-4 form-control-fld">
                            <div class="input-prepend">
                                <span class="add-on"><i class="fa fa-calendar-o"></i></span>
                                {!! Form::text('valid_from', '',  ['id' => 'datepicker_search','class' => 'calendar form-control from-date-control','readonly' => true, 'placeholder' => 'Dispatch Date*']) !!}
                                <input type="hidden" name="dispatch_flexible_hidden" id="dispatch_flexible_hidden" value="0">
                                
                            </div>
                        </div>
                        {{-- {!! Form::hidden('is_search',1,array('class'=>'form-control')) !!}
                        {!! Form::hidden('spot_or_term',1,array('class'=>'form-control')) !!} --}}
                        <div class="col-md-4 form-control-fld">
                            <div class="input-prepend">
                                <span class="add-on"><i class="fa fa-calendar-o"></i></span>
                                {!! Form::text('valid_to', '' , ['id' => 'datepicker_to_location_search','class' => 'calendar form-control to-date-control','readonly' => true, 'placeholder' => 'Delivery Date ']) !!}
                                <input type="hidden" name="delivery_flexible_hidden" id="delivery_flexible_hidden" value="0">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 col-md-offset-4">
                    <button class="btn theme-btn btn-block">Search</button>
                </div>
            {!! Form::close() !!}
        </div>

    </div>
</div>

@include('partials.footer')
@endsection



