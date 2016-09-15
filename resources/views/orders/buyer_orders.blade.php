@extends('app')
@section('content')


<!--div class="responsive_sidebars">
    <div class="container">
        <div class="left-bar">
            <i class="fa fa-bars"></i>
        </div>
        <div class="right-bar">
            <i class="fa fa-angle-down"></i> <span class="right-bar-title">Quick
                Search</span>
        </div>
    </div>
</div-->

<div class="main-container">
    <div class="container container-inner">
        <!-- Left Nav Starts Here -->
        @include('partials.leftnav')
        <!-- Left Nav Ends Here -->
        <div class="col-lg-7 col-md-6 col-sm-12 col-xs-12 main-middle">

            <div class="block">
                <div class="tab-nav underline">
                    @include('partials.page_top_navigation')
                </div>
                <div class="col-md-12 col-sm-12 col-xs-12 padding-top">

                    <div class="gray_bg">


                        {!! Form::open(array('url' => 'buyerordersearch', 'id' =>'seller-order-search',
                        'class'=>'form-inline')) !!}

                        <div class="col-md-3 col-sm-3 col-xs-12 padding-none">
                            {!! Form::select('lkp_order_type_id',array('' => 'Select Order Type') + $order_types,$order_type ,['class'=>'selectpicker','id'=>'order_types']) !!}

                        </div>
                        <div class="col-md-3 col-sm-3 col-xs-12 padding-right-none mobile-padding-none">
                            {!! Form::select('service_id',array('' => 'Service Type (All)') + $services,$service_id ,['class'=>'selectpicker','id'=>'service_offered']) !!}
                        </div>
                        <div class="col-md-3 col-sm-3 col-xs-12 padding-right-none mobile-padding-none">
                            {!! Form::select('status_id',array('' => 'Status (All)') + $status,$order_status,['class'=>'selectpicker','id'=>'post_status']) !!}
                        </div>
                        <div class="col-md-3 col-sm-3 col-xs-12 padding-right-none mobile-padding-none margin-bottom">
                            {!! Form::submit('Go', array('class'=>'btn btn-info ')) !!}
                            {!! Form :: close() !!}		

                        </div>
                        <div class="clearfix"></div>
                    </div>

                    <div class="gray_bg">
                        {!! $filter->open !!}
                        <div class="col-md-3 col-sm-3 col-xs-12 padding-none">
                            
                            {!! $filter->field('orders.from_city_id') !!}
                        </div>
                        <div class="col-md-3 col-sm-3 col-xs-12 padding-right-none mobile-padding-none">
                            
                            {!! $filter->field('orders.to_city_id') !!}
                        </div>

                        <div class="col-md-3 col-sm-3 col-xs-12 padding-right-none mobile-padding-none">
                            <div class="form-group">
                            {!! $filter->field('orders.start_dispatch_date') !!}
                        </div>
                            </div>
                        <div class="col-md-3 col-sm-3 col-xs-12 padding-right-none mobile-padding-none">
                            
                           <div class="form-group"> {!! $filter->field('orders.end_dispatch_date') !!}
                           </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-md-3 col-sm-3 col-xs-12 padding-none">
                            {!! $filter->field('orders.buyer_consignor_name') !!}
                        </div>

                        <div class="col-md-3 col-sm-3 col-xs-12 padding-right-none mobile-padding-none">
                            {!! $filter->field('orders.buyer_consignee_name') !!}
                        </div>
                        <div class="col-md-3 col-sm-3 col-xs-12 padding-right-none mobile-padding-none pull-right">
                            <div class="form-group">{!! $filter->field('orders.order_no') !!}
                                </div>
                        </div>
                        <div class="clearfix"></div>

                        {!! $filter->close !!}    
                    </div>

                    {!! $grid !!}


                    


                </div>


            </div>
        </div>
        <!-- Right Starts Here -->
        @include('partials.right')
        <!-- Right Ends Here -->
    </div>
</div>

@endsection
