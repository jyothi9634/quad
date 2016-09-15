{{--*/ $routeName = strtolower(substr(class_basename(Route::currentRouteAction()), (strpos(class_basename(Route::currentRouteAction()), '@') + 1))) /*--}}
            <!-- Code for post and get quote --> 
            @if((Auth::user()->lkp_role_id == BUYER && (Session::get('last_login_role_id')== 0)) || (Session::get('last_login_role_id')== BUYER))
                @if(Session::get('service_id') == ROAD_FTL)
                    @if($routeName != 'buyersearch')
                        <a href="{{ url('createbuyerquote') }}"><span class="btn post-btn pull-right">Post & Get Quote</span></a>
                    @endif
                @elseif(Session::get('service_id') == ROAD_PTL
                        || Session::get('service_id') == RAIL 
                        || Session::get('service_id') == AIR_DOMESTIC
                        || Session::get('service_id') == AIR_INTERNATIONAL
                        || Session::get('service_id') == OCEAN
                        || Session::get('service_id') == COURIER)
                     @if($routeName != 'buyersearch')
                        <a href="{{ url('ptl/createbuyerquote') }}"><span class="btn post-btn pull-right">Post & Get Quote</span></a>
                     @endif
                @elseif(Session::get('service_id') == ROAD_INTRACITY)
                    @if($routeName != 'buyersearch')
                    <a href="{{ url('intracity/buyer_post') }}"><span class="btn post-btn pull-right">Post & Get Quote</span></a>
                    @endif
                @elseif(Session::get('service_id') == ROAD_TRUCK_HAUL)
                    @if($routeName != 'buyersearch')
                        <a href="{{ url('truckhaul/createbuyerquote') }}"><span class="btn post-btn pull-right"> Post & Get Quote</span></a>
                    @endif
                @elseif(Session::get('service_id') == ROAD_TRUCK_LEASE)
                    @if($routeName != 'buyersearch')
                        <a href="{{ url('trucklease/createbuyerquote') }}"><span class="btn post-btn pull-right"> Post & Get Quote</span></a>
                    @endif
                @elseif(Session::get('service_id') == RELOCATION_DOMESTIC)
                    @if($routeName != 'buyersearch')
                        <a href="{{ url('relocation/creatbuyerrpost') }}"><span class="btn post-btn pull-right"> Post & Get Quote</span></a>
                    @endif
                @elseif(Session::get('service_id') == RELOCATION_PET_MOVE)
                    @if($routeName != 'buyersearch')
                        <a href="{{ url('relocation/creatbuyerrpost') }}"><span class="btn post-btn pull-right"> Post & Get Quote</span></a>
                    @endif
                @elseif(Session::get('service_id') == RELOCATION_OFFICE_MOVE)
                    @if($routeName != 'buyersearch')
                        <a href="{{ url('relocation/creatbuyerrpost') }}"><span class="btn post-btn pull-right"> Post & Get Quote</span></a>
                    @endif
                @elseif(Session::get('service_id') == RELOCATION_INTERNATIONAL)
                    @if($routeName != 'buyersearch')
                        <a href="{{ url('relocation/creatbuyerrpost') }}"><span class="btn post-btn pull-right"> Post & Get Quote</span></a>
                    @endif
                @elseif(Session::get('service_id') == RELOCATION_GLOBAL_MOBILITY)
                    @if($routeName != 'buyersearch')
                        <a href="{{ url('relocation/creatbuyerrpost') }}"><span class="btn post-btn pull-right"> Post & Get Quote</span></a>
                    @endif                    
                @endif                          
            @elseif((Auth::user()->lkp_role_id == SELLER && Session::get('last_login_role_id')== 0) || (Session::get('last_login_role_id')== SELLER))        
                <!-- li class="red-border bold-text"><a href="{{ url('/sellersearchbuyers') }}">Search</a></li-->
                @if(Session::get('service_id') == ROAD_FTL)
                     @if($routeName != 'sellersearchbuyers')
                        <a href="{{ url('createseller') }}"><span class="btn post-btn pull-right">+ Post</a></span>
                     @endif
                @elseif(Session::get('service_id') == ROAD_PTL
                        || Session::get('service_id') == RAIL 
                        || Session::get('service_id') == AIR_DOMESTIC
                        || Session::get('service_id') == AIR_INTERNATIONAL
                        || Session::get('service_id') == OCEAN
                        || Session::get('service_id') == COURIER)
                     @if($routeName != 'sellersearchbuyers')
                        <a href="{{ url('ptl/createsellerpost') }}"><span class="btn post-btn pull-right">+ Post</a></span>
                     @endif
                @elseif(Session::get('service_id') == RELOCATION_DOMESTIC || Session::get('service_id') == RELOCATION_PET_MOVE)
                    <a href="{{ url('relocation/createsellerpost') }}"><span class="btn post-btn pull-right">+ Post</span></a>
                @elseif(Session::get('service_id') == RELOCATION_OFFICE_MOVE)
                    <a href="{{ url('relocation/createsellerpost') }}"><span class="btn post-btn pull-right">+ Post</span></a>
                @elseif(Session::get('service_id') == RELOCATION_GLOBAL_MOBILITY)
                    <a href="{{ url('relocation/createsellerpost') }}"><span class="btn post-btn pull-right">+ Post</span></a>
                @endif
                @elseif(Session::get('service_id') == ROAD_TRUCK_HAUL)
                     @if($routeName != 'sellersearchbuyers')
                        <a href="{{ url('truckhaul/createsellerpost') }}"><span class="btn post-btn pull-right">+ Post</span></a>
                     @endif
            @endif
            
            @if (Session::has('success') && Session::get('success')!='')
                    <div class="flash">
                        <p class="text-success col-sm-12 text-center flash-txt alert-success">
                            {{ Session::get('success') }}</p>
                    </div>
                @endif
            <!-- end of code for post and get quote --> 