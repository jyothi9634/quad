@inject('commonComponent', 'App\Components\CommonComponent')
@inject('buyerCommonComponent', 'App\Components\BuyerComponent')
@extends('app')
@section('content')
<!-- Page top navigation Starts Here-->
@include('partials.page_top_navigation')
<!-- Page top navigation ends Here-->
{{--*/ $serviceId = Session::get('service_id') /*--}} 
 @if(isset($allBuyerQuoteDetails) && !empty($allBuyerQuoteDetails))
            @foreach ($allBuyerQuoteDetails as $data)
                {{--*/ $id = $data->id /*--}}
                {{--*/ $transactionId = $data->transaction_id /*--}}
                {{--*/ $buyer_quote_id = $data->buyer_quote_id /*--}}
                {{--*/ $name = $data->username /*--}}
                {{--*/ $quoteAccessType = $data->quote_access /*--}}
                @if($serviceId != AIR_INTERNATIONAL && $serviceId != OCEAN && $serviceId != COURIER)
                {{--*/ $isDoorPickup = ($data->is_door_pickup == 1) ? 'Yes' : 'No' /*--}}
                {{--*/ $idDoorDelivery = ($data->is_door_delivery == 1) ? 'Yes' : 'No' /*--}}
                @endif
                {{--*/ $isCancelled = $data->lkp_post_status_id /*--}}
                {{--*/ $postStatus = $data->lkp_post_status_id /*--}}
                {{--*/ $exactDispatchDate = ($data->dispatch_date == '0000-00-00') ? '' : $data->dispatch_date /*--}}
                {{--*/ $exactDeliveryDate = ($data->delivery_date == '0000-00-00') ? '' : $data->delivery_date /*--}}
                {{--*/ $fromLocationId = $data->from_location_id /*--}}
                {{--*/ $toLocationId = $data->to_location_id /*--}}
                @if($serviceId == AIR_INTERNATIONAL || $serviceId == OCEAN)
                {{--*/ $product_made= $data->product_made /*--}}
                {{--*/ $shipment_type = $data->shipment_type /*--}}
                {{--*/ $sender_identity = $data->sender_identity /*--}}
                {{--*/ $ie_code = $data->ie_code /*--}}
                @endif
            @endforeach
        @else
            {{--*/ $id = '' /*--}}
            {{--*/ $transactionId = '' /*--}}
            {{--*/ $buyer_quote_id = '' /*--}}
            {{--*/ $name = '' /*--}}
            {{--*/ $quoteAccessType = '' /*--}}
            @if($serviceId != AIR_INTERNATIONAL && $serviceId != OCEAN)
            {{--*/ $isDoorPickup = '' /*--}}
            {{--*/ $idDoorDelivery = '' /*--}}
            @endif
            {{--*/ $isCancelled = '' /*--}}
            {{--*/ $postStatus = '' /*--}}
            {{--*/ $exactDispatchDate = '' /*--}}
            {{--*/ $exactDeliveryDate = '' /*--}}
            
            {{--*/ $product_made= '' /*--}}
            {{--*/ $shipment_type = '' /*--}}
            {{--*/ $sender_identity = '' /*--}}
            {{--*/ $ie_code = '' /*--}}
        @endif
         @if(isset($fromLocation) && !empty($fromLocation))
                {{--*/ $fromCity = $fromLocation /*--}}
        @else
            {{--*/ $fromCity = '' /*--}}
        @endif
        @if(isset($toLocation) && !empty($toLocation))
                {{--*/ $toCity = $toLocation /*--}}
        @else
            {{--*/ $toCity = '' /*--}}
        @endif
        @if(isset($deliveryDate) && !empty($deliveryDate))
            {{--*/ $deliveryDate = $deliveryDate /*--}}
        @else
            {{--*/ $deliveryDate = '' /*--}}
        @endif
        @if(isset($dispatchDate) && !empty($dispatchDate))
            {{--*/ $dispatchDate = $dispatchDate /*--}}
        @else
            {{--*/ $dispatchDate = '' /*--}}
        @endif
{!! Form::open(['url'=>'/ptlupdateseller','id' => 'ptl_update_seller']) !!}

{!! Form::hidden('service_id',$serviceId ,['id' =>'service_id', 'class' => 'service_id']) !!}
{{--*/ $str='' /*--}} {{--*/ $str_service='' /*--}}
@if($serviceId==ROAD_PTL || $serviceId==RAIL)
{{--*/ $str=' CFT' /*--}} 
@elseif($serviceId==AIR_DOMESTIC || $serviceId==AIR_INTERNATIONAL)
{{--*/ $str=' CCM' /*--}} 
@elseif($serviceId==OCEAN)
{{--*/ $str=' CBM' /*--}} 
@endif
@if($serviceId == ROAD_PTL)
{{--*/ $str_service="Lessthan Truck Load" /*--}}
@elseif($serviceId == RAIL)
{{--*/ $str_service="Rail" /*--}}
@elseif($serviceId == AIR_DOMESTIC)
{{--*/ $str_service="Air Domestic" /*--}}
@elseif($serviceId == AIR_INTERNATIONAL)
{{--*/ $str_service="Air International" /*--}}
@elseif($serviceId == COURIER)
{{--*/ $str_service="Courier" /*--}}
@elseif($serviceId == OCEAN)
{{--*/ $str_service="Ocean" /*--}}
@endif

<input type="hidden" name="from_location[]" value="{!! $fromLocationId !!}" class="from_location">
<input type="hidden" name="to_location[]"	value="{!! $toLocationId !!}" class="to_location">
<input type="hidden" name="quoteid"	value="{!! $id !!}" id="ptl_buyer_quote_id" >

	<div class="main">
        <div class="container">
		
		@if(Session::has('ptlfailupdate')) 
        <div class="flash">
		<p class="text-success col-sm-12 text-center flash-txt alert-success">
		{{ Session::get('ptlfailupdate') }}
		</p>
		</div>
		@endif
		<span class="pull-left"><h1 class="page-title"><b>{{$str_service}} - {!! $transactionId !!}</b></h1></span>
        @include('partials.content_top_navigation_links')
        <div class="clearfix"></div>
                
                <div class="col-md-12 padding-none">
                    <div class="main-inner"> 
                        <!-- Right Section Starts Here -->
                        <div class="main-right">                     

                            <div class="inner-block-bg inner-block-bg1">
                                <div class="col-md-12 tab-modal-head">
                                    <h3>
                                        <i class="fa fa-map-marker"></i> 
                                        @if($fromCity)
                                        {!! $fromCity !!}
                                        @else &nbsp;
                                        @endif to 
                                        @if($toCity)
                                        {!! $toCity !!}
                                        @else &nbsp;
                                        @endif
                                    </h3>
                                </div>
                               

                                <div class="col-md-12 data-div">                                   

                                    <div class="col-md-4 padding-left-none data-fld">
                                        <span class="data-head">Buyer Name</span>
                                        <span class="data-value">
                                            {!! $name !!}                                           
                                        </span>
                                    </div>                   
                                    
                                    <div class="col-md-4 padding-left-none data-fld">
                                        <span class="data-head">Dispatch Date</span>
                                        <span class="data-value">
                                            @if(isset($dispatchDate))
                                            {!! $dispatchDate !!}
                                            @else 
                                            &nbsp;
                                            @endif                                            
                                        </span>
                                    </div>

                                    <div class="col-md-4 padding-left-none data-fld">
                                        <span class="data-head">Delivery Date<span>
                                        <span class="data-value">
                                             @if($deliveryDate== "0000-00-00" || $deliveryDate== "" )
                                             NA
                                             @else {!! $deliveryDate !!}
                                             @endif
                                        </span>
                                    </div>
                                   
                                    <div class="col-md-12 padding-left-none data-fld">
                                        <span class="data-head">
                                            <span class="pull-right spot_transaction_details hidden-xs">Details 
                                                <span class="show_details" style="display: inline;">+</span>
                                                <span class="hide_details" style="display: none;">-</span>
                                            </span>
                                        </span>
                                        
                                    </div>

                                    <div class="col-md-12 col-sm-12 col-xs-12 padding-none spot_transaction_details_view">
                                    <div class="colmd-4 col-sm-4 col-xs-6 padding-none">
                                                                        @if($serviceId == AIR_INTERNATIONAL || $serviceId == OCEAN)
                                                                        <p>Shipment Type</p>
                                                                        <p>Sender Identity</p>
                                                                        <p>IE Code</p>
                                                                        <p>Product Made</p>
                                                                        @endif      
                                            @if($serviceId != AIR_INTERNATIONAL && $serviceId != OCEAN && $serviceId != COURIER)
                                                                                    <p>Door Pickup</p>
                                                                                <p>Door Delivery</p>
                                                                                @endif
                                                                                <p>Status</p>
                                                                                <p>Documents</p>
                                        </div>
                                        <div class="colmd-8 col-sm-8 col-xs-6 padding-none">
                                                                                @if($serviceId == AIR_INTERNATIONAL || $serviceId == OCEAN)
                                                                                <p>@if($shipment_type)
                                                                                    {!! $shipment_type!!}
                                                                                    @else &nbsp;
                                                                                    @endif</p>
                                                                                <p>@if($sender_identity)
                                                                                    {!! $sender_identity!!}
                                                                                    @else &nbsp;
                                                                                    @endif</p>
                                                                                <p>@if($ie_code)
                                                                                    {!! $ie_code!!}
                                                                                    @else &nbsp;
                                                                                    @endif</p>
                                                                                <p>@if($product_made)
                                                                                    {!! $product_made!!}
                                                                                    @else &nbsp;
                                                                                    @endif</p>
                                                                            @endif
                                                                                @if($serviceId != AIR_INTERNATIONAL && $serviceId != OCEAN && $serviceId != COURIER)
                                            									<p>{!! $isDoorPickup !!}</p>
                                                                                <p>{!! $idDoorDelivery !!}</p>
                                                                                @endif
                                                <!--                                <p>Advance&nbsp; <i class="fa fa-credit-card"></i> &nbsp; Online</p>
                                                                                <p>Real Time</p>-->
                                                                                <p>{!! $quoteAccessType !!}</p>
                                        <p><i class="fa fa-lg fa-file-text"></i>&nbsp;<sup class="red">0</sup></p>
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>                                   
                                    
                                    <div class="col-md-12 padding-left-none data-fld">
                                        <div class="table-heading inner-block-bg" width="100%">
                                        	@if($serviceId == COURIER)
                                            <div class="col-md-1 padding-left-none">S. No</div>
                                            @else
                                            <div class="col-md-2 padding-left-none">S. No</div>
                                            @endif
                                            @if($serviceId != COURIER)
                                            <div class="col-md-2 padding-left-none">Load Type</div>
                                            <div class="col-md-2 padding-left-none">Package</div>
                                            @endif
                                            @if($serviceId == COURIER)
                                            <div class="col-md-2 padding-left-none">Courier Purpose</div>
                                            <div class="col-md-2 padding-left-none">courier Delivery Type</div>
                                            <div class="col-md-2 padding-left-none">Courier Type</div>
                                            @endif
                                            @if($serviceId == COURIER)
                                            <div class="col-md-1 padding-left-none">Weight</div>
                                            @else
                                            <div class="col-md-2 padding-left-none">Weight</div>
                                            @endif
                                            <div class="col-md-2 padding-left-none">Volume</div>
                                            <div class="col-md-2 padding-left-none">No. of packages </div>
                                            <div class="clearfix"></div>
                                        </div>
                                        <div class="table-data">
                                            @if(isset($arraySellerDetails) && !empty($arraySellerDetails))
                                                {{--*/ $slNumber = '1' /*--}}
                                                @foreach($arraySellerDetails as $key=>$sellersQuotesDetails)
                                                    <div class="table-row inner-block-bg">
                                                    	@if($serviceId == COURIER)
                                                        <div class="col-md-1 padding-left-none">{!! $slNumber !!}</div>
                                                        @else
                                                        <div class="col-md-2 padding-left-none">{!! $slNumber !!}</div>
                                                        @endif
                                                        @if($serviceId == COURIER)
                                            			<div class="col-md-2 padding-left-none">{!! $sellersQuotesDetails->courier_purpose !!}</div>
                                            			<div class="col-md-2 padding-left-none">{!! $sellersQuotesDetails->courier_delivery_type !!}</div>
                                            			<div class="col-md-2 padding-left-none">{!! $sellersQuotesDetails->courier_type !!}</div>
                                            			@endif
                                                        @if($serviceId != COURIER)
                                                        <div class="col-md-2 padding-left-none">{!! $sellersQuotesDetails->load_type !!}</div>
                                                        <div class="col-md-2 padding-left-none">{!! $sellersQuotesDetails->packaging_type_name !!}</div>
                                                        @endif
                                                        @if($serviceId == COURIER)
                                                        <div class="col-md-1 padding-left-none">
                                                        @else
                                                        <div class="col-md-2 padding-left-none">
                                                        @endif
                                                        {!! $sellersQuotesDetails->buyerQuoteUnits !!}<br class="hidden-lg hidden-md hidden-sm"> {{ $sellersQuotesDetails->weight_type }}
                                                        </div>
                                                        <div class="col-md-2 padding-left-none">{!! round($sellersQuotesDetails->calculated_volume_weight,4) !!} {{$str}}</div>
                                                        <div class="col-md-2 padding-left-none">{!! $sellersQuotesDetails->number_packages !!}</div>
                                                        <div class="clearfix"></div>
                                                    </div>
                                                    {{--*/ $slNumber++ /*--}}
                                                @endforeach
                                            @endif
                                        </div>
                                    </div>                                             
                                    

                                </div>
                               
                            </div>

                            <div class="col-md-12 inner-block-bg inner-block-bg1">
                                <div class="col-md-4 padding-none">
                                    Selected Sellers :
                                    <div>
                                        <ul class="token-input-list">
                                            <?php foreach($buyer_post_edit_seller as $seller_list) { ?>
                                            <li class="token-input-token">
                                                <p>{!! $seller_list->username !!} {!! $seller_list->id !!}</p>
                                                <!-- <p>{!! Form::hidden('seller_id[]',$seller_list->id)!!}</p>  -->
                                            </li>
                                            <?php } ?>
                                        </ul>
                                        <input type="text" id="demo-input-local" name="seller_list[]" class="form-control form-control1"/>
                                </div>                              
                            </div>   

                            </div>
                            <div class="col-md-4 col-md-offset-4">
                                {!! Form::submit('Update', ['class' => 'btn theme-btn btn-block','name' =>'update','id' => 'quote_update',  'onclick'=>'return val()']) !!}</div>
                            </div>

                        </div>
                    </div>
                </div>          
        </div>
    </div>
	<script>
function val()
{
	var selerId = document.getElementById("demo-input-local").value;
	if (selerId == null || selerId == "") {
        $("#erroralertmodal .modal-body").html("Please enter seller name");
        $("#erroralertmodal").modal({
            show: true
        });
        return false;
    }
}
$(document).ready(function() {		
			/*var from_location=$("#from_location").val();
			alert(from_location);*/
			var seller_id_list = new Array();
			$.each( $( ".from_location" ), function() {
				var from_location_value =$(this).val();
				seller_id_list.unshift(from_location_value);
			    
			});	
			
			var ptl_buyer_quote_id = $('#ptl_buyer_quote_id').val();
				
			$('.token-input-delete-token').click(function(){
					$(this).parent().remove();
				});						
			$.ajax({
	            url: '/getPtlEditSellerList',
	            type: "post",
	            data: {'seller_list':seller_id_list,'_token': $('input[name=_token]').val(),'ptl_buyer_quote_id':ptl_buyer_quote_id},
	            success: function(data){
	            //alert(data);
	            if(data!="")
	            	{
	            		$("#demo-input-local").tokenInput(data);
	            	}
	            else
	            	{
	            		//alert("No Sellers Available");
	            		$('#post_private').prop('checked', false);
	            		$("#hideseller").css("display","none");	
	            		return false;
	            		
	            	}	            
	            },
	            error : function(request, status, error) {
	            $('#post_private').val(null);
	            alert(error);
	            },
	        });	
});
			
		
</script>
{!! Form::close() !!}
@include('partials.footer')
@stop
@endsection