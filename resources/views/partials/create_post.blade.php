{{--*/ $routeName = strtolower(substr(class_basename(Route::currentRouteAction()), (strpos(class_basename(Route::currentRouteAction()), '@') + 1))) /*--}}
        <!-- LeftNav Content Starts Here -->
@inject('common', 'App\Components\CommonComponent')

{{--*/ $pageServiceId = Session::get('service_id')  /*--}}
@if($pageServiceId == 0 || $pageServiceId == '')
    {{--*/ $pageServiceId = '0'  /*--}}
@endif


   <span class="post-but pull-right post-button">
								@if((Auth::user()->lkp_role_id == SELLER && Session::get('last_login_role_id')== 0) || (Session::get('last_login_role_id')== SELLER))
                                    @if(Session::get('service_id') == ROAD_FTL)
                                    	@if($routeName != 'createseller')
								<a class="btn post-btn pull-right" href="#" onclick="return checkSession({{$pageServiceId}},'/createseller');">+ Post </a>
                                        @endif
                                    @elseif(Session::get('service_id') == ROAD_PTL 
                                            || Session::get('service_id') == RAIL 
                                            || Session::get('service_id') == AIR_DOMESTIC
                                            || Session::get('service_id') == AIR_INTERNATIONAL
                                            || Session::get('service_id') == OCEAN
                                            || Session::get('service_id') == COURIER)
                                    	@if($routeName != 'ptlcreatesellerpost')
                                       <a class="btn post-btn pull-right" href="#" onclick="return checkSession({{$pageServiceId}},'/ptl/createsellerpost');">+ Post </a>
                                        @endif
                                    @endif
                                @elseif((Auth::user()->lkp_role_id == BUYER && (Session::get('last_login_role_id')== 0)) || (Session::get('last_login_role_id')== BUYER))
                                    @if(Session::get('service_id') == ROAD_FTL)
                                  		@if($routeName != 'createbuyerquote')
                                        <a class="btn post-btn pull-right" href="#" onclick="return checkSession({{$pageServiceId}},'/createbuyerquote');"> Post & Get Quote </a>
                                        @endif
                                    @elseif(Session::get('service_id') == ROAD_PTL
                                            || Session::get('service_id') == RAIL 
                                            || Session::get('service_id') == AIR_DOMESTIC
                                            || Session::get('service_id') == AIR_INTERNATIONAL
                                            || Session::get('service_id') == OCEAN   
                                            || Session::get('service_id') == COURIER)                                   
                                    	@if($routeName != 'ptlcreatebuyerquote')
                                    	<a class="btn post-btn pull-right" href="#" onclick="return checkSession({{$pageServiceId}},'/ptl/createbuyerquote');"> Post & Get Quote </a>
               							@endif
                                    @elseif(Session::get('service_id') == ROAD_INTRACITY)
                                    @if($routeName != 'buyerpost')
                                        <a class="btn post-btn pull-right" href="#" onclick="return checkSession({{$pageServiceId}},'/intracity/buyer_post');"> Post & Get Quote </a>
                                    @endif
                                    @endif
                                @endif
    </span>
