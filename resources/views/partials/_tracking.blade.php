{{--*/ $currentDate = date('Y-m-d')/*--}}
{{--*/ $currentTime = date('h:i')/*--}}

@if(isset($orderDetails))
    {{--*/ $confirmed_on = explode(' ',$orderDetails->vehicle_confirmed_on); /*--}}
    {{--*/ $placement_date = $confirmed_on[0] /*--}}
    {{--*/ $placement_time = $confirmed_on[1] /*--}}
    {{--*/ $order_status = $orderDetails->lkp_order_status_id /*--}}
    {{--*/ $delivery_on  = explode(' ',$orderDetails->seller_delivery_date)/*--}}
    {{--*/ $delivery_date = $delivery_on[0]/*--}}
    {{--*/ $delivery_time = $delivery_on[1]/*--}}
@endif

@if(isset($order))
    {{--*/ $confirmed_on = explode(' ',$order->vehicle_confirmed_on); /*--}}
    {{--*/ $placement_date = $confirmed_on[0] /*--}}
    {{--*/ $placement_time = $confirmed_on[1] /*--}}
    {{--*/ $order_status = $order->lkp_order_status_id /*--}}
    {{--*/ $delivery_on  = explode(' ',$order->seller_delivery_date)/*--}}
    {{--*/ $delivery_date = $delivery_on[0]/*--}}
    {{--*/ $delivery_time = $delivery_on[1]/*--}}
@endif


@if(isset($tracking) && $tracking==2)
    @if($vehicle->volty_register)
        @if($order_status == ORDER_DELIVERED)
            <button onclick="HISTORY('{{$vehicle->vehicle_no}}','{{$placement_date}}','{{$placement_time}}','{{$delivery_date}}','{{LIVE_CURRENT_END_DATE}}');" data-toggle="modal" data-target="#mapmodel" class="btn red-btn flat-btn">Historical Tracking</button>
        @else
            <button onclick="GET_RECENT_POINT('{{$vehicle->vehicle_no}}','RECENT');" data-toggle="modal" data-target="#mapmodel" class="btn red-btn flat-btn">Current Location</button>
            <button onclick="LIVE('{{$vehicle->vehicle_no}}','{{$placement_date}}','{{$placement_time}}','{{$currentDate}}','{{LIVE_CURRENT_END_DATE}}');" data-toggle="modal" data-target="#mapmodel" class="btn red-btn flat-btn">Live Tracking</button>
        @endif
    @else
        Real time pickup not available for this vehicle 
    @endif
@endif    


@if(isset($tracking_order) && $tracking_order==2)
    @if($vehicle->volty_register)
        @if($order_status == ORDER_DELIVERED)
            <button onclick="HISTORY('{{$vehicle->vehicle_no}}','{{$placement_date}}','{{$placement_time}}','{{$delivery_date}}','{{LIVE_CURRENT_END_DATE}}');" data-toggle="modal" data-target="#mapmodel" class="btn red-btn flat-btn">Historical Tracking</button>
        @else
            <button onclick="GET_RECENT_POINT('{{$vehicle->vehicle_no}}','RECENT');" data-toggle="modal" data-target="#mapmodel" class="btn red-btn flat-btn">Current Location</button>
            <button onclick="LIVE('{{$vehicle->vehicle_no}}','{{$placement_date}}','{{$placement_time}}','{{$currentDate}}','{{LIVE_CURRENT_END_DATE}}');" data-toggle="modal" data-target="#mapmodel" class="btn red-btn flat-btn">Live Tracking</button>
        @endif
    @else
        <span class="pull-right">Real time pickup not available for this vehicle</span>
    @endif
@endif

{{--
    @if($vehicles[0]->volty_register)
        <button onclick="GET_RECENT_POINT('{{$vehicle->vehicle_no}}','RECENT');" data-toggle="modal" data-target="#mapmodel" class="btn red-btn flat-btn">Historical Tracking</button>
        <button onclick="HISTORY('{{$vehicle->vehicle_no}}','RECENT');" data-toggle="modal" data-target="#mapmodel" class="btn red-btn flat-btn">Live Tracking</button>
    @else
        Real time pickup not available for this vehicle 
    @endif
--}}

@include('partials.volty_gps_includes')



