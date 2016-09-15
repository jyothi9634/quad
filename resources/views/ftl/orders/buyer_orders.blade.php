@extends('app')
@section('content')
        <!-- Page top navigation Starts Here-->
@include('partials.page_top_navigation')

<div class="main">

    <div class="container">
        
        <span class="pull-left">
        <h1 class="page-title">
        
        @if(Session::get('service_id')==ROAD_FTL)
        Orders (FTL)
        @elseif(Session::get('service_id')==ROAD_PTL)
        Orders (LTL)
        @elseif(Session::get('service_id')==RAIL)
        Orders (RAIL)
        @elseif(Session::get('service_id')==ROAD_INTRACITY)
        Orders (INTRACITY)
        @elseif(Session::get('service_id')==OCEAN)
        Orders (OCEAN)
        @elseif(Session::get('service_id')==AIR_INTERNATIONAL)
        Orders (AIR INTERNATIONAL)
        @elseif(Session::get('service_id')==AIR_DOMESTIC)
        Orders (AIR DOMESTIC)
        @elseif(Session::get('service_id')==COURIER)
        Orders (COURIER)
        @elseif(Session::get('service_id')==RELOCATION_DOMESTIC)
        Orders (RELOCATION)     
            @elseif(Session::get('service_id')==ROAD_TRUCK_HAUL)
            Orders (Haul)
            @elseif(Session::get('service_id')==ROAD_TRUCK_LEASE)
            Orders (Truck Lease)
            @elseif(Session::get('service_id')==RELOCATION_OFFICE_MOVE)
            Orders (Relocation Office)
            @elseif(Session::get('service_id')==RELOCATION_PET_MOVE)
            Orders (Relocation Pet)
            @elseif(Session::get('service_id')==RELOCATION_INTERNATIONAL)
                Orders (Relocation International)
          @elseif(Session::get('service_id')==RELOCATION_GLOBAL_MOBILITY)
                Orders (Relocation Global Mobility)
        @else
        Orders (FTL)
        @endif
        </h1><a class="change-service" data-toggle="modal" data-target="#change-service">Change Service</a></span>
        <!--button class="btn post-btn pull-right">Post & get Quote</button-->

        <!-- Page top navigation Starts Here-->
        @include('partials.content_top_navigation_links')
                    <div class="clearfix"></div>

                    <div class="col-md-12 padding-none">
                        <div class="main-inner">

                            <div class="main-right">


                                {!! Form::open(array('url' => 'buyerordersearch', 'id' =>'seller-order-search','class'=>'form-inline')) !!}
                                <div class="gray-bg">
                                    <div class="col-md-12 padding-none filter">
                                        <div class="col-md-3 form-control-fld">
                                            <div class="normal-select">
                                            @if(Session::get('service_id') == ROAD_TRUCK_HAUL || Session::get('service_id') == ROAD_TRUCK_LEASE || Session::get('service_id') == RELOCATION_OFFICE_MOVE || Session::get('service_id') == RELOCATION_PET_MOVE)
                                                <select class="selectpicker" id="lkp_order_type_id" name="lkp_order_type_id">
                                                    <option value="1">Spot</option>                                                   
                                                </select>
                                                @else
                                                {!! Form::select('lkp_order_type_id',$order_types,$order_type ,['class'=>'selectpicker','id'=>'order_types']) !!}                                                
                                            @endif
                                            </div>
                                        </div>
                                        @if($order_type==2)
                                         <div class="col-md-3 form-control-fld">
                                            <div class="normal-select">
                                            {!! Form::select('status_id',[''=>'Status (All)','10'=> 'Pending Acceptance','11'=> 'Contract Accepted','12'=> 'Contract Cancelled'],$order_status,['class'=>'selectpicker','id'=>'post_status']) !!}                                           
                                            </div>
                                        </div>
                                        @elseif(Session::get('service_id') == ROAD_TRUCK_HAUL || Session::get('service_id') == ROAD_TRUCK_LEASE)
                                         <div class="col-md-3 form-control-fld">
                                            <div class="normal-select">
                                              {!! Form::select('status_id',[''=>'Status (All)','2'=> 'Placement Due','3'=> 'Placed','6'=> 'Reported'],$order_status,['class'=>'selectpicker','id'=>'post_status']) !!}                                            
                                            </div>
                                        </div>
                                        @elseif(Session::get('service_id') == RELOCATION_GLOBAL_MOBILITY )
                                         <div class="col-md-3 form-control-fld">
                                            <div class="normal-select">
                                              {!! Form::select('status_id',[''=>'Status (All)','2'=> 'Commencement Due','3'=> 'Commencement Started','6'=> 'Commencement Completed'],$order_status,['class'=>'selectpicker','id'=>'post_status']) !!}                                            
                                            </div>
                                        </div>
                                        @else
                                        <div class="col-md-3 form-control-fld">
                                            <div class="normal-select">
                                               {!! Form::select('status_id',array('' => 'Status (All)') + $status,$order_status,['class'=>'selectpicker','id'=>'post_status']) !!}
                                            </div>
                                        </div>
                                        @endif
                                        
                                        @if(Session::get('service_id') == RELOCATION_INTERNATIONAL)
                                        <div class="col-md-3 form-control-fld">
                                            <div class="normal-select">                                                
                                                <select class="selectpicker" id="order_int_type" name="order_int_type">
                                                    <option <?php if (isset ( $_REQUEST ['order_int_type'] ) && $_REQUEST ['order_int_type'] == 1) { ?> selected="selected" value="1" <?php } else { ?>value="1"<?php } ?> >Air</option>
                                                    <option <?php if (isset ( $_REQUEST ['order_int_type'] ) && $_REQUEST ['order_int_type'] == 2) { ?> selected="selected" value="2" <?php } else { ?>value="2"<?php } ?> >Ocean</option>
                                                </select>               
                                            </div>
                                        </div>
                                        @endif
                                        
                                         @if(Session::get('service_id') == COURIER)
                                        <div class="col-md-3 form-control-fld">
                                                <div class="normal-select">
                                                {{--*/ $domestic_selected = "" /*--}}
                                                {{--*/ $international_selected = "" /*--}}
                                                @if(Session::get('delivery_type') == '1') 
                                                {{--*/ $domestic_selected = "selected" /*--}}
                                                @else
                                                 {{--*/ $international_selected = "selected" /*--}}
                                                @endif
                                                <select class="selectpicker" id="delivery_type" name="delivery_type">
                                                        <option value="1" {{ $domestic_selected }}>Domestic</option>
                                                        <option value="2" {{ $international_selected }}>International</option>
                                                </select>
                                                </div>
                                        </div>
                                        @endif
                                        @if(Session::get('service_id') == COURIER)
                                        <div class="col-md-3 form-control-fld">
                                        @elseif(Session::get('service_id') == RELOCATION_INTERNATIONAL)
                                        <div class="col-md-3 form-control-fld">
                                        @else
                                        <div class="col-md-6 form-control-fld">
                                        @endif
                                            {!! Form::submit(' GO ', array('class'=>'btn add-btn pull-right')) !!}
                                        </div>                                        
                                    </div>
                                </div>
                                {!! Form :: close() !!}
                                @if($order_type==2)
                {!! $filter->open !!}
                                <div class="gray-bg">
                                    <div class="col-md-12 padding-none filter">
                                        <div class="col-md-3 form-control-fld">
                                            <div class="input-prepend">
                                                <span class="add-on"><i class="fa fa-map-marker"></i></span>
                                                {!! $filter->field('tbqi.from_location_id') !!}                                           
                                            </div>
                                        </div>
                                          @if(Session::get('service_id') != RELOCATION_GLOBAL_MOBILITY )
                                        <div class="col-md-3 form-control-fld">
                                            <div class="input-prepend">
                                                <span class="add-on"><i class="fa fa-map-marker"></i></span>
                                                {!! $filter->field('tbqi.to_location_id') !!}                                           
                                            </div>
                                        </div>
                                          @endif
                                        <div class="col-md-3 form-control-fld">
                                            <div class="input-prepend">
                                                <span class="add-on"><i class="fa fa-calendar-o"></i></span>
                                                @if(isset($_GET['start_dispatch_date']))
                                                    {!! Form::text('start_dispatch_date', $_GET['start_dispatch_date'],['id' => 'start_dispatch_date','class'=>'form-control dateRangeFormat', 'placeholder' => 'From Date','readonly' => true]) !!}
                                                @else
                                                    {!! Form::text('start_dispatch_date', '',['id' => 'start_dispatch_date','class'=>'form-control dateRangeFormat', 'placeholder' => 'From Date','readonly' => true]) !!}
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-3 form-control-fld">
                                            <div class="input-prepend">
                                                <span class="add-on"><i class="fa fa-calendar-o"></i></span>
                                                @if(isset($_GET['end_dispatch_date']))
                                                    {!! Form::text('end_dispatch_date', $_GET['end_dispatch_date'],['id' => 'end_dispatch_date','class'=>'form-control dateRangeFormat', 'placeholder' => 'To Date','readonly' => true]) !!}
                                                @else
                                                    {!! Form::text('end_dispatch_date','',['id' => 'end_dispatch_date','class'=>'form-control dateRangeFormat', 'placeholder' => 'To Date','readonly' => true]) !!}
                                                @endif
                                                 @if(Session::get('service_id') == RELOCATION_INTERNATIONAL)
                                                <input type="hidden" name="order_int_type" id="order_int_type" value="<?php if (isset ( $_REQUEST ['order_int_type'] ) && $_REQUEST ['order_int_type']) { echo $_REQUEST ['order_int_type'];}else{ echo "1";} ?>">
                                                @endif
                                                <input type="hidden" name="lkp_order_type_id" id="lkp_order_type_id" value="{{ $order_type }}">
                        <input type="hidden" name="status_id" id="status_id" value="{{$order_status}}"> 
                                            </div>
                                        </div>                                       
                                    </div>

                                </div>
                                {!! $filter->close !!}
                                @else
                                {!! $filter->open !!}
                                <div class="gray-bg">
                                    <div class="col-md-12 padding-none filter">
                                        @if(Session::get('service_id') != RELOCATION_GLOBAL_MOBILITY)
                                        <div class="col-md-3 form-control-fld">
                                            <div class="input-prepend">
                                                <span class="add-on"><i class="fa fa-map-marker"></i></span>
                                                {!!  $filter->field('from_city_id') !!}                                      
                                            </div>
                                        </div>
                                        @endif
                                       @if(Session::get('service_id') != ROAD_TRUCK_LEASE && Session::get('service_id') !=RELOCATION_OFFICE_MOVE)
                                        <div class="col-md-3 form-control-fld">
                                            <div class="input-prepend">
                                                <span class="add-on"><i class="fa fa-map-marker"></i></span>
                                                {!! $filter->field('to_city_id') !!}                                           
                                            </div>
                                        </div>
                                        @endif
                                        @if(Session::get('service_id') == RELOCATION_GLOBAL_MOBILITY)
                                        <div class="col-md-3 form-control-fld">
                                            <div class="input-prepend">
                                                <span class="add-on"><i class="fa fa-map-marker"></i></span>
                                                {!! $filter->field('service_type') !!}                                           
                                            </div>
                                        </div>
                                        @endif
                                        <div class="col-md-3 form-control-fld">
                                            <div class="input-prepend">
                                                <span class="add-on"><i class="fa fa-calendar-o"></i></span>
                                                {!! $filter->field('start_dispatch_date') !!}
                                            </div>
                                        </div>
                                        <div class="col-md-3 form-control-fld">
                                            <div class="input-prepend">
                                                <span class="add-on"><i class="fa fa-calendar-o"></i></span>
                                                {!! $filter->field('end_dispatch_date') !!}
                                            </div>
                                        </div>
                                        <div class="col-md-3 form-control-fld">
                                            <div class="normal-select">
                                                {!! $filter->field('username') !!}
                                            </div>
                                        </div>
                                        @if(Session::get('service_id') != ROAD_TRUCK_LEASE)
                                        <div class="col-md-3 form-control-fld">
                                            @if(Session::get('service_id') == ROAD_TRUCK_HAUL)
                                            <div class="input-prepend">
                                                <span class="add-on"><i class="fa fa-truck"></i></span>
                                                {!! $filter->field('vehicle_number') !!}
                                            </div>
                                            @elseif(Session::get('service_id') == RELOCATION_GLOBAL_MOBILITY)
                                            <div class="normal-select">
                                                {!! $filter->field('buyer_consignor_name') !!}
                                            </div>
                                            @else
                                            <div class="normal-select">
                                                {!! $filter->field('buyer_consignee_name') !!}
                                            </div>
                                            @endif
                                        </div>
                                        @endif
                                        <div class="col-md-3 form-control-fld">
                                            <div class="input-prepend">
                                                <i class="fa fa-search"></i>
                                                {!! $filter->field('order_no') !!}
                                                <input type="hidden" name="status_id" id="status_id" value="{{$order_status}}">
                                            </div>
                                        </div>
                                        @if(Session::get('service_id') == RELOCATION_INTERNATIONAL)
                                          <input type="hidden" name="order_int_type" id="order_int_type" value="<?php if (isset ( $_REQUEST ['order_int_type'] ) && $_REQUEST ['order_int_type']) { echo $_REQUEST ['order_int_type'];}else{ echo "1";} ?>">
                                        @endif
                                    </div>
                                    @if(Session::get('service_id') == COURIER)
                                    <input type="hidden" name="delivery_type" id="delivery_type" value="{{ Session::get('delivery_type') }}">
                                    @endif
                                </div>
                                {!! $filter->close !!}
                                @endif

                                <div class="table-div">
                                    <div class="table-data">
                                        {!! $grid !!}
                                    </div>
                                </div>

                            </div>    <!-- Right section -->



                        </div> <!-- col-md-12 padding-none -->
                    </div>   <!-- main-inner -->

                    <div class="clearfix"></div>


    </div> <!-- Container -->
</div>   <!-- main -->

@include('partials.footer')



@endsection