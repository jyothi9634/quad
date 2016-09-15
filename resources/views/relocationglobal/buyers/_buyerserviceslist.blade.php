@inject('commonComponent', 'App\Components\CommonComponent')
@if(isset($leadtype) && $leadtype='term')
{{--*/ $ltype = 'term' /*--}}
@else
{{--*/ $ltype = 'spot' /*--}}
@endif
{{--*/ $buyerservices = $commonComponent->getBuyerPostServicesList($buyerpost_id,$ltype) /*--}}

{{--*/ $buyerServicesCount= count($buyerservices) /*--}}
@if($buyerServicesCount>0)

<div class="col-md-12"><div class="clearfix"></div>
	<div class="table-div table-style1">
		<!-- Table Head Starts Here -->
		<div class="table-heading inner-block-bg">
			<div class="col-md-6 padding-left-none">Service</div>
			<div class="col-md-6 padding-left-none">Numbers</div>
		</div>
		<!-- Table Head Ends Here -->
			<div class="table-data">
				@if(isset($buyerservices))
					@foreach($buyerservices as $buyerServiceData)
						{{--*/ $measure_val = '' /*--}}
						{{--*/ $service_type_id  = $buyerServiceData->lkp_gm_service_id /*--}}
						@if($service_type_id  == 1 || $service_type_id  == 2 || $service_type_id  == 6)
							{{--*/ $measure_val = (int)$buyerServiceData->measurement.' Days' /*--}}
						@elseif($service_type_id == 4 || $service_type_id == 5)
							{{--*/ $measure_val = (int)$buyerServiceData->measurement.' Persons'  /*--}}
						@elseif($service_type_id == 7)
							{{--*/ $measure_val = 'Rs. '.$buyerServiceData->measurement.'/-'  /*--}}
						@elseif($service_type_id == 3)
							{{--*/ $measure_val = 'Rs. '.$buyerServiceData->measurement.' /-'  /*--}}
						@endif
						<div class="table-row inner-block-bg">
							<div class="col-md-6 padding-left-none">{{$buyerServiceData->service_type}}</div>
							<div class="col-md-6 padding-left-none"> @if($service_type_id!=7){{$measure_val}}@endif</div>
						</div>
					@endforeach
				@elseif(isset($seller_quote_items_list))
                                
					@foreach($seller_quote_items_list as $items)
						<div class="table-row inner-block-bg">
							<div class="col-md-6 padding-left-none">{{$items->service_type}}</div>
							<div class="col-md-6 padding-left-none"> {{(int)$items->measurement.' '.$items->buyer_munits}}</div>
						</div>
					@endforeach
				@endif
			</div>
		<!-- Table Ends Here -->
	</div>
</div>
@endif