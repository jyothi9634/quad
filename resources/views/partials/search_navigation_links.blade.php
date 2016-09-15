{{--*/ $routeName = strtolower(substr(class_basename(Route::currentRouteAction()), (strpos(class_basename(Route::currentRouteAction()), '@') + 1))) /*--}}
@inject('common', 'App\Components\CommonComponent')
<div class="breadcrumb_div margin-bottom">
@for($i = 0; $i <= count(Request::segments()); $i++)
	@if(Request::segment($i) == BUYERSEARCH)
        Services > General > Buyer > {!! $common->getServiceGroupName(Session::get('service_id')) !!} > {!! $common->getServiceName(Session::get('service_id')) !!} > Search
	@elseif(Request::segment($i) == BUYERSEARCHRESULTS)
        Services > General > Buyer > {!! $common->getServiceGroupName(Session::get('service_id')) !!} > {!! $common->getServiceName(Session::get('service_id')) !!} > Search List
	@endif
	@if(Request::segment($i) == SELLERSEARCH)
        Services > General > Seller > {!! $common->getServiceGroupName(Session::get('service_id')) !!} > {!! $common->getServiceName(Session::get('service_id')) !!} > Search
	@elseif(Request::segment($i) == SELLERSEARCHRESULTS)
        Services > General > Seller > {!! $common->getServiceGroupName(Session::get('service_id')) !!} > {!! $common->getServiceName(Session::get('service_id')) !!} > Search List
	@endif

@endfor  
</div>
<div class="tab-nav">
	<ul id="tabs">
	
		<li class=""><a href="#"><img class="feeds_icon" src="../images/feeds.png">&nbsp;Feeds</a></li>
		@if(Auth::user()->lkp_role_id == BUYER)
			<li class="red-border bold-text"><a href="{{ url('/buyersearch') }}">Search</a></li>
			@if(Session::get('service_id') == ROAD_FTL)
				  @if($routeName != 'buyersearch')
			<span class="post-but pull-right post-button"><a href="{{ url('createbuyerquote') }}"> Post & Get Quote</a></span>
				 @endif
            @elseif(Session::get('service_id') == ROAD_PTL
            		|| Session::get('service_id') == RAIL 
                    || Session::get('service_id') == AIR_DOMESTIC
                    || Session::get('service_id') == OCEAN)
           		 @if($routeName != 'buyersearch')
			<span class="post-but pull-right post-button"><a href="{{ url('ptl/createbuyerquote') }}"> Post & Get Quote</a></span>
				 @endif
            @elseif(Session::get('service_id') == ROAD_INTRACITY)
            @if($routeName != 'buyersearch')
			<span class="post-but pull-right post-button"><a href="{{ url('intracity/buyer_post') }}"> Post & Get Quote</a></span>
            @endif
            @endif							
		@elseif(Auth::user()->lkp_role_id == SELLER)		
			<li class="red-border bold-text"><a href="{{ url('/sellersearchbuyers') }}">Search</a></li>
			@if(Session::get('service_id') == ROAD_FTL)
				 @if($routeName != 'sellersearchbuyers')
			<span class="post-but pull-right post-button"><a href="{{ url('createseller') }}">+ Post</a></span>
				 @endif
            @elseif(Session::get('service_id') == ROAD_PTL
            		|| Session::get('service_id') == RAIL 
                    || Session::get('service_id') == AIR_DOMESTIC
                    || Session::get('service_id') == OCEAN)
           		 @if($routeName != 'sellersearchbuyers')
			<span class="post-but pull-right post-button"><a href="{{ url('ptl/createsellerpost') }}">+ Post</a></span>
				 @endif
            @endif
		@endif
	</ul>
</div>

