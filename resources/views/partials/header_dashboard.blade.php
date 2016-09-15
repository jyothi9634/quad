@inject('commoncomponent', 'App\Components\CommonComponent')
{{--*/ $userId = Auth::User ()->id /*--}}
@if($type == "messages")
<!--messages start-->

{{--*/ $headerMessages = $commoncomponent::getHeaderMessages($userId) /*--}}
@if(count($headerMessages) > 0)
<div class="message-dropdown">
    <ul>
        <li>
            <table class="table message-area">
                @foreach($headerMessages as $headerMessage)
                    <tr>
                        <td class="message-pic">
                            {{--*/ $logoimage = $commoncomponent::getProfileLogo($headerMessage->sender_id) /*--}}
                            @if(\File::exists($logoimage))
                                <img src="{{url($logoimage)}}" width="50" />
                            @else
                                <i class="fa fa-user"></i>
                            @endif

                        </td>
                        <td class="message-content">
                            <p class="messagesender">{{$commoncomponent::getUsername($headerMessage->sender_id)}}<span class="message-date">{{date("M d", strtotime($headerMessage->created_at))}}</span></p>
                            <p class="message-title"><a href="/getmessagedetails/{{$headerMessage->id}}/0/{{$headerMessage->is_term}}/">{{strip_tags($headerMessage->subject)}}</a></p>
                            @if($headerMessage->is_read == 0)
                                <p class="dashboard_unread">{{$commoncomponent::getMessageShortBody($headerMessage->message)}} </p>
                            @else
                                <p class="dashboard_read">{{$commoncomponent::getMessageShortBody($headerMessage->message)}} </p>
                            @endif

                        </td>
                    </tr>
                @endforeach
            </table>
        </li>

    </ul>
</div>
@endif
<!--messages end-->
@elseif($type == "posts")
<!--posts start-->

<div class="post-dropdown">
    <ul>
        <li>
            <table class="table post-area">

                <tr>
                    <td class="post-pic">
                        <img src="{{url('images/log-icons/FTL.png')}}" width="50" title="{{FTL_IMAGE_TITLE}}"/>
                    </td>
                    <td class="post-content">
                        <div class="info-links">
                            {{--*/ $linkpath = (Auth::user()->lkp_role_id== SELLER) ? '/sellerlist' : '/buyerposts' /*--}}
                            <a href="#" onclick="return subcriptionuserservice({{ROAD_FTL}},'{{$linkpath}}')"><i class="fa fa-envelope-o" title="Messages"></i> <span class="badge">{{$commoncomponent::getMessagesCount($userId,ROAD_FTL,2)}}</span></a>
                            @if((Auth::user()->lkp_role_id == SELLER && (Session::get('last_login_role_id')== 0)) || (Session::get('last_login_role_id')== SELLER))
                                <a href="#" onclick="return subcriptionuserservice({{ROAD_FTL}},'/sellerlist')"><i class="fa fa-file-text-o" title="Enquiries"></i> <span class="badge">{{$commoncomponent::getHeaderDashboardCount($userId,SELLER,ROAD_FTL,1)}}</span></a>
                                <a href="#" onclick="return subcriptionuserservice({{ROAD_FTL}},'/sellerlist')"><i class="fa fa-thumbs-o-up" title="Leads"></i> <span class="badge">{{$commoncomponent::getHeaderDashboardCount($userId,SELLER,ROAD_FTL,2)}}</span></a>
                            @else
                                <a href="#" onclick="return subcriptionuserservice({{ROAD_FTL}},'/buyerposts')"><i class="fa fa-file-text-o" title="Quotes"></i> <span class="badge">{{$commoncomponent::getHeaderDashboardCount($userId,BUYER,ROAD_FTL,2)}}</span></a>
                                <a href="#" onclick="return subcriptionuserservice({{ROAD_FTL}},'/buyerposts')"><i class="fa fa-thumbs-o-up" title="Leads"></i> <span class="badge">{{$commoncomponent::getHeaderDashboardCount($userId,BUYER,ROAD_FTL,1)}}</span></a>
                            @endif
                            <a href="#" onclick="return subcriptionuserservice({{ROAD_FTL}},'{{$linkpath}}')"><i class="fa fa-line-chart" title="Market Analytics"></i> <span class="badge">0</span></a>
                            <a href="#" onclick="return subcriptionuserservice({{ROAD_FTL}},'{{$linkpath}}')"><i class="fa fa-file-text-o" title="Documentation"></i> <span class="badge">0</span></a>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="post-pic">
                        <img src="{{url('images/log-icons/LTL.png')}}" width="50" title="{{LTL_IMAGE_TITLE}}"/>
                    </td>
                    <td class="post-content">
                        <div class="info-links">
                            <a href="#" onclick="return subcriptionuserservice({{ROAD_PTL}},'{{$linkpath}}')"><i class="fa fa-envelope-o" title="Messages"></i> <span class="badge">{{$commoncomponent::getMessagesCount($userId,ROAD_PTL,2)}}</span></a>
                            @if((Auth::user()->lkp_role_id == SELLER && (Session::get('last_login_role_id')== 0)) || (Session::get('last_login_role_id')== SELLER))
                                <a href="#" onclick="return subcriptionuserservice({{ROAD_PTL}},'/sellerlist')"><i class="fa fa-file-text-o" title="Enquiries"></i> <span class="badge">{{$commoncomponent::getHeaderDashboardCount($userId,SELLER,ROAD_PTL,1)}}</span></a>
                                <a href="#" onclick="return subcriptionuserservice({{ROAD_PTL}},'/sellerlist')"><i class="fa fa-thumbs-o-up" title="Leads"></i> <span class="badge">{{$commoncomponent::getHeaderDashboardCount($userId,SELLER,ROAD_PTL,2)}}</span></a>
                            @else
                                <a href="#" onclick="return subcriptionuserservice({{ROAD_PTL}},'/buyerposts')"><i class="fa fa-file-text-o" title="Quotes"></i> <span class="badge">{{$commoncomponent::getHeaderDashboardCount($userId,BUYER,ROAD_PTL,2)}}</span></a>
                                <a href="#" onclick="return subcriptionuserservice({{ROAD_PTL}},'/buyerposts')"><i class="fa fa-thumbs-o-up" title="Leads"></i> <span class="badge">{{$commoncomponent::getHeaderDashboardCount($userId,BUYER,ROAD_PTL,1)}}</span></a>
                            @endif
                            <a href="#" onclick="return subcriptionuserservice({{ROAD_PTL}},'{{$linkpath}}')"><i class="fa fa-line-chart" title="Market Analytics"></i> <span class="badge">0</span></a>
                            <a href="#" onclick="return subcriptionuserservice({{ROAD_PTL}},'{{$linkpath}}')"><i class="fa fa-file-text-o" title="Documentation"></i> <span class="badge">0</span></a>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="post-pic">
                        <img src="{{url('images/log-icons/rail.png')}}" width="50" title="{{RAIL_IMAGE_TITLE}}" />
                    </td>
                    <td class="post-content">
                        <div class="info-links">
                            <a href="#" onclick="return subcriptionuserservice({{RAIL}},'{{$linkpath}}')"><i class="fa fa-envelope-o" title="Messages"></i> <span class="badge">{{$commoncomponent::getMessagesCount($userId,RAIL,2)}}</span></a>
                            @if((Auth::user()->lkp_role_id == SELLER && (Session::get('last_login_role_id')== 0)) || (Session::get('last_login_role_id')== SELLER))
                                <a href="#" onclick="return subcriptionuserservice({{RAIL}},'/sellerlist')"><i class="fa fa-file-text-o" title="Enquiries"></i> <span class="badge">{{$commoncomponent::getHeaderDashboardCount($userId,SELLER,RAIL,1)}}</span></a>
                                <a href="#" onclick="return subcriptionuserservice({{RAIL}},'/sellerlist')"><i class="fa fa-thumbs-o-up" title="Leads"></i> <span class="badge">{{$commoncomponent::getHeaderDashboardCount($userId,SELLER,RAIL,2)}}</span></a>
                            @else
                                <a href="#" onclick="return subcriptionuserservice({{RAIL}},'/buyerposts')"><i class="fa fa-file-text-o" title="Quotes"></i> <span class="badge">{{$commoncomponent::getHeaderDashboardCount($userId,BUYER,RAIL,2)}}</span></a>
                                <a href="#" onclick="return subcriptionuserservice({{RAIL}},'/buyerposts')"><i class="fa fa-thumbs-o-up" title="Leads"></i> <span class="badge">{{$commoncomponent::getHeaderDashboardCount($userId,BUYER,RAIL,1)}}</span></a>
                            @endif
                            <a href="#" onclick="return subcriptionuserservice({{RAIL}},'{{$linkpath}}')"><i class="fa fa-line-chart" title="Market Analytics"></i> <span class="badge">0</span></a>
                            <a href="#" onclick="return subcriptionuserservice({{RAIL}},'{{$linkpath}}')"><i class="fa fa-file-text-o" title="Documentation"></i> <span class="badge">0</span></a>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="post-pic">
                        <img src="{{url('images/log-icons/air_dom.png')}}" width="50" title="{{AIRDOM_IMAGE_TITLE}}"/>
                    </td>
                    <td class="post-content">
                        <div class="info-links">
                            <a href="#"  onclick="return subcriptionuserservice({{AIR_DOMESTIC}},'{{$linkpath}}')"><i class="fa fa-envelope-o" title="Messages"></i> <span class="badge">{{$commoncomponent::getMessagesCount($userId,AIR_DOMESTIC,2)}}</span></a>
                            @if((Auth::user()->lkp_role_id == SELLER && (Session::get('last_login_role_id')== 0)) || (Session::get('last_login_role_id')== SELLER))
                                <a href="#" onclick="return subcriptionuserservice({{AIR_DOMESTIC}},'/sellerlist')"><i class="fa fa-file-text-o" title="Enquiries"></i> <span class="badge">{{$commoncomponent::getHeaderDashboardCount($userId,SELLER,AIR_DOMESTIC,1)}}</span></a>
                                <a href="#" onclick="return subcriptionuserservice({{AIR_DOMESTIC}},'/sellerlist')"><i class="fa fa-thumbs-o-up" title="Leads"></i> <span class="badge">{{$commoncomponent::getHeaderDashboardCount($userId,SELLER,AIR_DOMESTIC,2)}}</span></a>
                            @else
                                <a href="#" onclick="return subcriptionuserservice({{AIR_DOMESTIC}},'/buyerposts')"><i class="fa fa-file-text-o" title="Quotes"></i> <span class="badge">{{$commoncomponent::getHeaderDashboardCount($userId,BUYER,AIR_DOMESTIC,2)}}</span></a>
                                <a href="#" onclick="return subcriptionuserservice({{AIR_DOMESTIC}},'/buyerposts')"><i class="fa fa-thumbs-o-up" title="Leads"></i> <span class="badge">{{$commoncomponent::getHeaderDashboardCount($userId,BUYER,AIR_DOMESTIC,1)}}</span></a>
                            @endif
                            <a href="#"  onclick="return subcriptionuserservice({{AIR_DOMESTIC}},'{{$linkpath}}')"><i class="fa fa-line-chart" title="Market Analytics"></i> <span class="badge">0</span></a>
                            <a href="#"  onclick="return subcriptionuserservice({{AIR_DOMESTIC}},'{{$linkpath}}')"><i class="fa fa-file-text-o" title="Documentation"></i> <span class="badge">0</span></a>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="post-pic">
                        <img src="{{url('images/log-icons/air_intl.png')}}" width="50" title="{{AIRINT_IMAGE_TITLE}}"/>
                    </td>
                    <td class="post-content">
                        <div class="info-links">
                            <a href="#" onclick="return subcriptionuserservice({{AIR_INTERNATIONAL}},'{{$linkpath}}')"><i class="fa fa-envelope-o" title="Messages"></i> <span class="badge">{{$commoncomponent::getMessagesCount($userId,AIR_INTERNATIONAL,2)}}</span></a>
                            @if((Auth::user()->lkp_role_id == SELLER && (Session::get('last_login_role_id')== 0)) || (Session::get('last_login_role_id')== SELLER))
                                <a href="#" onclick="return subcriptionuserservice({{AIR_INTERNATIONAL}},'/sellerlist')"><i class="fa fa-file-text-o" title="Enquiries"></i> <span class="badge">{{$commoncomponent::getHeaderDashboardCount($userId,SELLER,AIR_INTERNATIONAL,1)}}</span></a>
                                <a href="#" onclick="return subcriptionuserservice({{AIR_INTERNATIONAL}},'/sellerlist')"><i class="fa fa-thumbs-o-up" title="Leads"></i> <span class="badge">{{$commoncomponent::getHeaderDashboardCount($userId,SELLER,AIR_INTERNATIONAL,2)}}</span></a>
                            @else
                                <a href="#" onclick="return subcriptionuserservice({{AIR_INTERNATIONAL}},'/buyerposts')"><i class="fa fa-file-text-o" title="Quotes"></i> <span class="badge">{{$commoncomponent::getHeaderDashboardCount($userId,BUYER,AIR_INTERNATIONAL,2)}}</span></a>
                                <a href="#" onclick="return subcriptionuserservice({{AIR_INTERNATIONAL}},'/buyerposts')"><i class="fa fa-thumbs-o-up" title="Leads"></i> <span class="badge">{{$commoncomponent::getHeaderDashboardCount($userId,BUYER,AIR_INTERNATIONAL,1)}}</span></a>
                            @endif
                            <a href="#"  onclick="return subcriptionuserservice({{AIR_INTERNATIONAL}},'{{$linkpath}}')"><i class="fa fa-line-chart" title="Market Analytics"></i> <span class="badge">0</span></a>
                            <a href="#"  onclick="return subcriptionuserservice({{AIR_INTERNATIONAL}},'{{$linkpath}}')"><i class="fa fa-file-text-o" title="Documentation"></i> <span class="badge">0</span></a>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="post-pic">
                        <img src="{{url('images/log-icons/ocean.png')}}" width="50" title="{{OCEAN_IMAGE_TITLE}}"/>
                    </td>
                    <td class="post-content">
                        <div class="info-links">
                            <a href="#" onclick="return subcriptionuserservice({{OCEAN}},'{{$linkpath}}')"><i class="fa fa-envelope-o" title="Messages"></i> <span class="badge">{{$commoncomponent::getMessagesCount($userId,OCEAN,2)}}</span></a>
                            @if((Auth::user()->lkp_role_id == SELLER && (Session::get('last_login_role_id')== 0)) || (Session::get('last_login_role_id')== SELLER))
                                <a href="#" onclick="return subcriptionuserservice({{OCEAN}},'/sellerlist')"><i class="fa fa-file-text-o" title="Enquiries"></i> <span class="badge">{{$commoncomponent::getHeaderDashboardCount($userId,SELLER,OCEAN,1)}}</span></a>
                                <a href="#" onclick="return subcriptionuserservice({{OCEAN}},'/sellerlist')"><i class="fa fa-thumbs-o-up" title="Leads"></i> <span class="badge">{{$commoncomponent::getHeaderDashboardCount($userId,SELLER,OCEAN,2)}}</span></a>
                            @else
                                <a href="#" onclick="return subcriptionuserservice({{OCEAN}},'/buyerposts')"><i class="fa fa-file-text-o" title="Quotes"></i> <span class="badge">{{$commoncomponent::getHeaderDashboardCount($userId,BUYER,OCEAN,2)}}</span></a>
                                <a href="#" onclick="return subcriptionuserservice({{OCEAN}},'/buyerposts')"><i class="fa fa-thumbs-o-up" title="Leads"></i> <span class="badge">{{$commoncomponent::getHeaderDashboardCount($userId,BUYER,OCEAN,1)}}</span></a>
                            @endif
                            <a href="#" onclick="return subcriptionuserservice({{OCEAN}},'{{$linkpath}}')"><i class="fa fa-line-chart" title="Market Analytics"></i> <span class="badge">0</span></a>
                            <a href="#" onclick="return subcriptionuserservice({{OCEAN}},'{{$linkpath}}')"><i class="fa fa-file-text-o" title="Documentation"></i> <span class="badge">0</span></a>
                        </div>
                    </td>
                </tr>
                @if(Auth::user()->lkp_role_id!= SELLER)
                    <tr>
                        <td class="post-pic">
                            <img src="{{url('images/log-icons/intracity.png')}}" width="50" title="{{INTRACITY_IMAGE_TITLE}}"/>
                        </td>
                        <td class="post-content">
                            <div class="info-links">
                                @if((Auth::user()->lkp_role_id == SELLER && (Session::get('last_login_role_id')== 0)) || (Session::get('last_login_role_id')== SELLER))

                                @else
                                    <a href="#" onclick="return subcriptionuserservice({{ROAD_INTRACITY}},'{{$linkpath}}')"><i class="fa fa-envelope-o" title="Messages"></i> <span class="badge">{{$commoncomponent::getMessagesCount($userId,ROAD_INTRACITY,2)}}</span></a>
                                    <a href="#" onclick="return subcriptionuserservice({{ROAD_INTRACITY}},'/buyerposts')"><i class="fa fa-file-text-o" title="Quotes"></i> <span class="badge">{{$commoncomponent::getHeaderDashboardCount($userId,BUYER,ROAD_INTRACITY,2)}}</span></a>
                                    <a href="#" onclick="return subcriptionuserservice({{ROAD_INTRACITY}},'/buyerposts')"><i class="fa fa-thumbs-o-up" title="Leads"></i> <span class="badge">{{$commoncomponent::getHeaderDashboardCount($userId,BUYER,ROAD_INTRACITY,1)}}</span></a>
                                    <a href="#" onclick="return subcriptionuserservice({{ROAD_INTRACITY}},'/buyerposts')"><i class="fa fa-line-chart" title="Market Analytics"></i> <span class="badge">0</span></a>
                                    <a href="#" onclick="return subcriptionuserservice({{ROAD_INTRACITY}},'/buyerposts')"><i class="fa fa-file-text-o" title="Documentation"></i> <span class="badge">0</span></a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endif
                <tr>
                    <td class="post-pic">
                        <img src="{{url('images/log-icons/courier.png')}}" width="35" title="{{COURIER_IMAGE_TITLE}}" />
                    </td>
                    <td class="post-content">
                        <div class="info-links">
                            <a href="#" onclick="return subcriptionuserservice({{COURIER}},'{{$linkpath}}')"><i class="fa fa-envelope-o" title="Messages"></i> <span class="badge">{{$commoncomponent::getMessagesCount($userId,COURIER,2)}}</span></a>
                            @if((Auth::user()->lkp_role_id == SELLER && (Session::get('last_login_role_id')== 0)) || (Session::get('last_login_role_id')== SELLER))
                                <a href="#" onclick="return subcriptionuserservice({{COURIER}},'/sellerlist')"><i class="fa fa-file-text-o" title="Enquiries"></i> <span class="badge">{{$commoncomponent::getHeaderDashboardCount($userId,SELLER,COURIER,1)}}</span></a>
                                <a href="#" onclick="return subcriptionuserservice({{COURIER}},'/sellerlist')"><i class="fa fa-thumbs-o-up" title="Leads"></i> <span class="badge">{{$commoncomponent::getHeaderDashboardCount($userId,SELLER,COURIER,2)}}</span></a>
                            @else
                                <a href="#" onclick="return subcriptionuserservice({{COURIER}},'/buyerposts')"><i class="fa fa-file-text-o" title="Quotes"></i> <span class="badge">{{$commoncomponent::getHeaderDashboardCount($userId,BUYER,COURIER,2)}}</span></a>
                                <a href="#" onclick="return subcriptionuserservice({{COURIER}},'/buyerposts')"><i class="fa fa-thumbs-o-up" title="Leads"></i> <span class="badge">{{$commoncomponent::getHeaderDashboardCount($userId,BUYER,COURIER,1)}}</span></a>
                            @endif
                            <a href="#" onclick="return subcriptionuserservice({{COURIER}},'{{$linkpath}}')"><i class="fa fa-line-chart" title="Market Analytics"></i> <span class="badge">0</span></a>
                            <a href="#" onclick="return subcriptionuserservice({{COURIER}},'{{$linkpath}}')"><i class="fa fa-file-text-o" title="Documentation"></i> <span class="badge">0</span></a>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="post-pic">
                        <img src="{{url('images/log-icons/truck_haul.png')}}" width="50" title="{{TRUCK_HAUL_IMAGE_TITLE}}"/>
                    </td>
                    <td class="post-content">
                        <div class="info-links">
                            <a href="#"  onclick="return subcriptionuserservice({{ROAD_TRUCK_HAUL}},'{{$linkpath}}')"><i class="fa fa-envelope-o" title="Messages"></i> <span class="badge">{{$commoncomponent::getMessagesCount($userId,ROAD_TRUCK_HAUL,2)}}</span></a>
                            @if((Auth::user()->lkp_role_id == SELLER && (Session::get('last_login_role_id')== 0)) || (Session::get('last_login_role_id')== SELLER))
                                <a href="#" onclick="return subcriptionuserservice({{ROAD_TRUCK_HAUL}},'/sellerlist')"><i class="fa fa-file-text-o" title="Enquiries"></i> <span class="badge">{{$commoncomponent::getHeaderDashboardCount($userId,SELLER,ROAD_TRUCK_HAUL,1)}}</span></a>
                                <a href="#" onclick="return subcriptionuserservice({{ROAD_TRUCK_HAUL}},'/sellerlist')"><i class="fa fa-thumbs-o-up" title="Leads"></i> <span class="badge">{{$commoncomponent::getHeaderDashboardCount($userId,SELLER,ROAD_TRUCK_HAUL,2)}}</span></a>
                            @else
                                <a href="#" onclick="return subcriptionuserservice({{ROAD_TRUCK_HAUL}},'/buyerposts')"><i class="fa fa-file-text-o" title="Quotes"></i> <span class="badge">{{$commoncomponent::getHeaderDashboardCount($userId,BUYER,ROAD_TRUCK_HAUL,2)}}</span></a>
                                <a href="#" onclick="return subcriptionuserservice({{ROAD_TRUCK_HAUL}},'/buyerposts')"><i class="fa fa-thumbs-o-up" title="Leads"></i> <span class="badge">{{$commoncomponent::getHeaderDashboardCount($userId,BUYER,ROAD_TRUCK_HAUL,1)}}</span></a>
                            @endif
                            <a href="#" onclick="return subcriptionuserservice({{ROAD_TRUCK_HAUL}},'{{$linkpath}}')"><i class="fa fa-line-chart" title="Market Analytics"></i> <span class="badge">0</span></a>
                            <a href="#" onclick="return subcriptionuserservice({{ROAD_TRUCK_HAUL}},'{{$linkpath}}')"><i class="fa fa-file-text-o" title="Documentation"></i> <span class="badge">0</span></a>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="post-pic">
                        <img src="{{url('images/log-icons/truck_lease.png')}}" width="50" title="{{TRUCK_LEASE_IMAGE_TITLE}}"/>
                    </td>
                    <td class="post-content">
                        <div class="info-links">
                            <a href="#" onclick="return subcriptionuserservice({{ROAD_TRUCK_LEASE}},'{{$linkpath}}')"><i class="fa fa-envelope-o" title="Messages"></i> <span class="badge">{{$commoncomponent::getMessagesCount($userId,ROAD_TRUCK_LEASE,2)}}</span></a>
                            @if((Auth::user()->lkp_role_id == SELLER && (Session::get('last_login_role_id')== 0)) || (Session::get('last_login_role_id')== SELLER))
                                <a href="#" onclick="return subcriptionuserservice({{ROAD_TRUCK_LEASE}},'/sellerlist')"><i class="fa fa-file-text-o" title="Enquiries"></i> <span class="badge">{{$commoncomponent::getHeaderDashboardCount($userId,SELLER,ROAD_TRUCK_LEASE,1)}}</span></a>
                                <a href="#" onclick="return subcriptionuserservice({{ROAD_TRUCK_LEASE}},'/sellerlist')"><i class="fa fa-thumbs-o-up" title="Leads"></i> <span class="badge">{{$commoncomponent::getHeaderDashboardCount($userId,SELLER,ROAD_TRUCK_LEASE,2)}}</span></a>
                            @else
                                <a href="#" onclick="return subcriptionuserservice({{ROAD_TRUCK_LEASE}},'/buyerposts')"><i class="fa fa-file-text-o" title="Quotes"></i> <span class="badge">{{$commoncomponent::getHeaderDashboardCount($userId,BUYER,ROAD_TRUCK_LEASE,2)}}</span></a>
                                <a href="#" onclick="return subcriptionuserservice({{ROAD_TRUCK_LEASE}},'/buyerposts')"><i class="fa fa-thumbs-o-up" title="Leads"></i> <span class="badge">{{$commoncomponent::getHeaderDashboardCount($userId,BUYER,ROAD_TRUCK_LEASE,1)}}</span></a>
                            @endif
                            <a href="#" onclick="return subcriptionuserservice({{ROAD_TRUCK_LEASE}},'{{$linkpath}}')"><i class="fa fa-line-chart" title="Market Analytics"></i> <span class="badge">0</span></a>
                            <a href="#" onclick="return subcriptionuserservice({{ROAD_TRUCK_LEASE}},'{{$linkpath}}')"><i class="fa fa-file-text-o" title="Documentation"></i> <span class="badge">0</span></a>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="post-pic">
                        <img src="{{url('images/log-icons/domestic.png')}}" width="50" title="{{RELOCATION_DOMESTIC_IMAGE_TITLE}}" />
                    </td>
                    <td class="post-content">
                        <div class="info-links">
                            <a href="#" onclick="return subcriptionuserservice({{RELOCATION_DOMESTIC}},'{{$linkpath}}')"><i class="fa fa-envelope-o" title="Messages"></i> <span class="badge">{{$commoncomponent::getMessagesCount($userId,RELOCATION_DOMESTIC,2)}}</span></a>
                            @if((Auth::user()->lkp_role_id == SELLER && (Session::get('last_login_role_id')== 0)) || (Session::get('last_login_role_id')== SELLER))
                                <a href="#" onclick="return subcriptionuserservice({{RELOCATION_DOMESTIC}},'/sellerlist')"><i class="fa fa-file-text-o" title="Enquiries"></i> <span class="badge">{{$commoncomponent::getHeaderDashboardCount($userId,SELLER,RELOCATION_DOMESTIC,1)}}</span></a>
                                <a href="#" onclick="return subcriptionuserservice({{RELOCATION_DOMESTIC}},'/sellerlist')"><i class="fa fa-thumbs-o-up" title="Leads"></i> <span class="badge">{{$commoncomponent::getHeaderDashboardCount($userId,SELLER,RELOCATION_DOMESTIC,2)}}</span></a>
                            @else
                                <a href="#" onclick="return subcriptionuserservice({{RELOCATION_DOMESTIC}},'/buyerposts')"><i class="fa fa-file-text-o" title="Quotes"></i> <span class="badge">{{$commoncomponent::getHeaderDashboardCount($userId,BUYER,RELOCATION_DOMESTIC,2)}}</span></a>
                            @endif
                            <a href="#" onclick="return subcriptionuserservice({{RELOCATION_DOMESTIC}},'{{$linkpath}}')"><i class="fa fa-line-chart" title="Market Analytics"></i> <span class="badge">0</span></a>
                            <a href="#" onclick="return subcriptionuserservice({{RELOCATION_DOMESTIC}},'{{$linkpath}}')"><i class="fa fa-file-text-o" title="Documentation"></i> <span class="badge">0</span></a>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="post-pic">
                        <img src="{{url('images/log-icons/pet_move.png')}}" width="50" title="{{RELOCATION_PETMOVE_IMAGE_TITLE}}"/>
                    </td>
                    <td class="post-content">
                        <div class="info-links">
                            <a href="#" onclick="return subcriptionuserservice({{RELOCATION_PET_MOVE}},'{{$linkpath}}')"><i class="fa fa-envelope-o" title="Messages"></i> <span class="badge">{{$commoncomponent::getMessagesCount($userId,RELOCATION_PET_MOVE,2)}}</span></a>
                            @if((Auth::user()->lkp_role_id == SELLER && (Session::get('last_login_role_id')== 0)) || (Session::get('last_login_role_id')== SELLER))
                                <a href="#" onclick="return subcriptionuserservice({{RELOCATION_PET_MOVE}},'/sellerlist')"><i class="fa fa-file-text-o" title="Enquiries"></i> <span class="badge">{{$commoncomponent::getHeaderDashboardCount($userId,SELLER,RELOCATION_PET_MOVE,1)}}</span></a>
                                <a href="#" onclick="return subcriptionuserservice({{RELOCATION_PET_MOVE}},'/sellerlist')"><i class="fa fa-thumbs-o-up" title="Leads"></i> <span class="badge">{{$commoncomponent::getHeaderDashboardCount($userId,SELLER,RELOCATION_PET_MOVE,2)}}</span></a>
                            @else
                                <a href="#" onclick="return subcriptionuserservice({{RELOCATION_PET_MOVE}},'/buyerposts')"><i class="fa fa-file-text-o" title="Quotes"></i> <span class="badge">{{$commoncomponent::getHeaderDashboardCount($userId,BUYER,RELOCATION_PET_MOVE,2)}}</span></a>
                            @endif
                            <a href="#" onclick="return subcriptionuserservice({{RELOCATION_PET_MOVE}},'{{$linkpath}}')"><i class="fa fa-line-chart" title="Market Analytics"></i> <span class="badge">0</span></a>
                            <a href="#" onclick="return subcriptionuserservice({{RELOCATION_PET_MOVE}},'{{$linkpath}}')"><i class="fa fa-file-text-o" title="Documentation"></i> <span class="badge">0</span></a>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="post-pic">
                        <img src="{{url('images/log-icons/international.png')}}" width="50" title="{{RELOCATION_INTERNATIONAL_IMAGE_TITLE}}" />
                    </td>
                    <td class="post-content">
                        <div class="info-links">
                            <a href="#" onclick="return subcriptionuserservice({{RELOCATION_INTERNATIONAL}},'{{$linkpath}}')"><i class="fa fa-envelope-o" title="Messages"></i> <span class="badge">{{$commoncomponent::getMessagesCount($userId,RELOCATION_INTERNATIONAL,2)}}</span></a>
                            @if((Auth::user()->lkp_role_id == SELLER && (Session::get('last_login_role_id')== 0)) || (Session::get('last_login_role_id')== SELLER))
                                <a href="#" onclick="return subcriptionuserservice({{RELOCATION_INTERNATIONAL}},'/sellerlist')"><i class="fa fa-file-text-o" title="Enquiries"></i> <span class="badge">{{$commoncomponent::getHeaderDashboardCount($userId,SELLER,RELOCATION_INTERNATIONAL,1)}}</span></a>
                                <a href="#" onclick="return subcriptionuserservice({{RELOCATION_INTERNATIONAL}},'/sellerlist')"><i class="fa fa-thumbs-o-up" title="Leads"></i> <span class="badge">{{$commoncomponent::getHeaderDashboardCount($userId,SELLER,RELOCATION_INTERNATIONAL,2)}}</span></a>
                            @else
                                <a href="#" onclick="return subcriptionuserservice({{RELOCATION_INTERNATIONAL}},'/buyerposts')"><i class="fa fa-file-text-o" title="Quotes"></i> <span class="badge">{{$commoncomponent::getHeaderDashboardCount($userId,BUYER,RELOCATION_INTERNATIONAL,2)}}</span></a>
                            @endif
                            <a href="#" onclick="return subcriptionuserservice({{RELOCATION_INTERNATIONAL}},'{{$linkpath}}')"><i class="fa fa-line-chart" title="Market Analytics"></i> <span class="badge">0</span></a>
                            <a href="#" onclick="return subcriptionuserservice({{RELOCATION_INTERNATIONAL}},'{{$linkpath}}')"><i class="fa fa-file-text-o" title="Documentation"></i> <span class="badge">0</span></a>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="post-pic">
                        <img src="{{url('images/log-icons/global_mobility.png')}}" width="50" title="{{RELOCATION_GLOBAL_MOBILITY_IMAGE_TITLE}}"/>
                    </td>
                    <td class="post-content">
                        <div class="info-links">
                            <a href="#" onclick="return subcriptionuserservice({{RELOCATION_GLOBAL_MOBILITY}},'{{$linkpath}}')"><i class="fa fa-envelope-o" title="Messages"></i> <span class="badge">{{$commoncomponent::getMessagesCount($userId,RELOCATION_GLOBAL_MOBILITY,2)}}</span></a>
                            @if((Auth::user()->lkp_role_id == SELLER && (Session::get('last_login_role_id')== 0)) || (Session::get('last_login_role_id')== SELLER))
                                <a href="#" onclick="return subcriptionuserservice({{RELOCATION_GLOBAL_MOBILITY}},'/sellerlist')"><i class="fa fa-file-text-o" title="Enquiries"></i> <span class="badge">{{$commoncomponent::getHeaderDashboardCount($userId,SELLER,RELOCATION_GLOBAL_MOBILITY,1)}}</span></a>
                                <a href="#" onclick="return subcriptionuserservice({{RELOCATION_GLOBAL_MOBILITY}},'/sellerlist')"><i class="fa fa-thumbs-o-up" title="Leads"></i> <span class="badge">{{$commoncomponent::getHeaderDashboardCount($userId,SELLER,RELOCATION_GLOBAL_MOBILITY,2)}}</span></a>
                            @else
                                <a href="#" onclick="return subcriptionuserservice({{RELOCATION_GLOBAL_MOBILITY}},'/buyerposts')"><i class="fa fa-file-text-o" title="Quotes"></i> <span class="badge">{{$commoncomponent::getHeaderDashboardCount($userId,BUYER,RELOCATION_GLOBAL_MOBILITY,2)}}</span></a>
                            @endif
                            <a href="#" onclick="return subcriptionuserservice({{RELOCATION_GLOBAL_MOBILITY}},'{{$linkpath}}')"><i class="fa fa-line-chart" title="Market Analytics"></i> <span class="badge">0</span></a>
                            <a href="#" onclick="return subcriptionuserservice({{RELOCATION_GLOBAL_MOBILITY}},'{{$linkpath}}')"><i class="fa fa-file-text-o" title="Documentation"></i> <span class="badge">0</span></a>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td class="post-pic">
                        <img src="{{url('images/log-icons/office_domestic.png')}}" width="50" title="{{RELOCATION_OFFICE_DOMESTIC_IMAGE_TITLE}}" />
                    </td>
                    <td class="post-content">
                        <div class="info-links">
                            <a href="#" onclick="return subcriptionuserservice({{RELOCATION_OFFICE_MOVE}},'{{$linkpath}}')"><i class="fa fa-envelope-o" title="Messages"></i> <span class="badge">{{$commoncomponent::getMessagesCount($userId,RELOCATION_OFFICE_MOVE,2)}}</span></a>
                            @if((Auth::user()->lkp_role_id == SELLER && (Session::get('last_login_role_id')== 0)) || (Session::get('last_login_role_id')== SELLER))
                                <a href="#" onclick="return subcriptionuserservice({{RELOCATION_OFFICE_MOVE}},'/sellerlist')"><i class="fa fa-file-text-o" title="Enquiries"></i> <span class="badge">{{$commoncomponent::getHeaderDashboardCount($userId,SELLER,RELOCATION_OFFICE_MOVE,1)}}</span></a>
                                <a href="#" onclick="return subcriptionuserservice({{RELOCATION_OFFICE_MOVE}},'/sellerlist')"><i class="fa fa-thumbs-o-up" title="Leads"></i> <span class="badge">{{$commoncomponent::getHeaderDashboardCount($userId,SELLER,RELOCATION_OFFICE_MOVE,2)}}</span></a>
                            @else
                                <a href="#" onclick="return subcriptionuserservice({{RELOCATION_OFFICE_MOVE}},'/buyerposts')"><i class="fa fa-file-text-o" title="Quotes"></i> <span class="badge">{{$commoncomponent::getHeaderDashboardCount($userId,BUYER,RELOCATION_OFFICE_MOVE,2)}}</span></a>
                            @endif
                            <a href="#" onclick="return subcriptionuserservice({{RELOCATION_OFFICE_MOVE}},'{{$linkpath}}')"><i class="fa fa-line-chart" title="Market Analytics"></i> <span class="badge">0</span></a>
                            <a href="#" onclick="return subcriptionuserservice({{RELOCATION_OFFICE_MOVE}},'{{$linkpath}}')"><i class="fa fa-file-text-o" title="Documentation"></i> <span class="badge">0</span></a>
                        </div>
                    </td>
                </tr>

            </table>
        </li>

    </ul>
</div>
<!--posts end-->
@elseif($type == "orders")
<!--orders start-->

<div class="order-dropdown">
    <ul>
        <li>
            <table class="table order-area">
                {{--*/ $linkpath = (Auth::user()->lkp_role_id== SELLER) ? '/orders/seller_orders' : '/orders/buyer_orders' /*--}}
                <tr>
                    <td class="order-pic">
                        <img src="{{url('images/log-icons/FTL.png')}}" width="50" title="{{FTL_IMAGE_TITLE}}" />
                    </td>
                    <td class="order-content">
                        <div class="info-links">
                            <a href="#"  onclick="return subcriptionuserservice({{ROAD_FTL}},'{{$linkpath}}')"><i class="fa fa-envelope-o" title="Messages"></i> <span class="badge">{{$commoncomponent::getMessagesCount($userId,ROAD_FTL,3)}}</span></a>
                            <a href="#" onclick="return subcriptionuserservice({{ROAD_FTL}},'{{$linkpath}}')"><i class="fa fa-file-text-o" title="Documentation"></i> <span class="badge">0</span></a>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="order-pic">
                        <img src="{{url('images/log-icons/LTL.png')}}" width="50" title="{{LTL_IMAGE_TITLE}}" />
                    </td>
                    <td class="order-content">
                        <div class="info-links">
                            <a href="#"  onclick="return subcriptionuserservice({{ROAD_PTL}},'{{$linkpath}}')"><i class="fa fa-envelope-o" title="Messages"></i> <span class="badge">{{$commoncomponent::getMessagesCount($userId,ROAD_PTL,3)}}</span></a>
                            <a href="#" onclick="return subcriptionuserservice({{ROAD_PTL}},'{{$linkpath}}')"><i class="fa fa-file-text-o" title="Documentation"></i> <span class="badge">0</span></a>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="order-pic">
                        <img src="{{url('images/log-icons/rail.png')}}" width="50" title="{{RAIL_IMAGE_TITLE}}" />
                    </td>
                    <td class="order-content">
                        <div class="info-links">
                            <a href="#"  onclick="return subcriptionuserservice({{RAIL}},'{{$linkpath}}')"><i class="fa fa-envelope-o" title="Messages"></i> <span class="badge">{{$commoncomponent::getMessagesCount($userId,RAIL,3)}}</span></a>
                            <a href="#" onclick="return subcriptionuserservice({{RAIL}},'{{$linkpath}}')"><i class="fa fa-file-text-o" title="Documentation"></i> <span class="badge">0</span></a>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="order-pic">
                        <img src="{{url('images/log-icons/air_dom.png')}}" width="50" title="{{AIRDOM_IMAGE_TITLE}}"/>
                    </td>
                    <td class="order-content">
                        <div class="info-links">
                            <a href="#"  onclick="return subcriptionuserservice({{AIR_DOMESTIC}},'{{$linkpath}}')"><i class="fa fa-envelope-o" title="Messages"></i> <span class="badge">{{$commoncomponent::getMessagesCount($userId,AIR_DOMESTIC,3)}}</span></a>
                            <a href="#" onclick="return subcriptionuserservice({{AIR_DOMESTIC}},'{{$linkpath}}')"><i class="fa fa-file-text-o" title="Documentation"></i> <span class="badge">0</span></a>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="order-pic">
                        <img src="{{url('images/log-icons/air_intl.png')}}" width="50" title="{{AIRINT_IMAGE_TITLE}}" />
                    </td>
                    <td class="order-content">
                        <div class="info-links">
                            <a href="#"  onclick="return subcriptionuserservice({{AIR_INTERNATIONAL}},'{{$linkpath}}')"><i class="fa fa-envelope-o" title="Messages"></i> <span class="badge">{{$commoncomponent::getMessagesCount($userId,AIR_INTERNATIONAL,3)}}</span></a>
                            <a href="#" onclick="return subcriptionuserservice({{AIR_INTERNATIONAL}},'{{$linkpath}}')"><i class="fa fa-file-text-o" title="Documentation"></i> <span class="badge">0</span></a>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="order-pic">
                        <img src="{{url('images/log-icons/ocean.png')}}" width="50" title="{{OCEAN_IMAGE_TITLE}}"/>
                    </td>
                    <td class="order-content">
                        <div class="info-links">
                            <a href="#"  onclick="return subcriptionuserservice({{OCEAN}},'{{$linkpath}}')"><i class="fa fa-envelope-o" title="Messages"></i> <span class="badge">{{$commoncomponent::getMessagesCount($userId,OCEAN,3)}}</span></a>
                            <a href="#" onclick="return subcriptionuserservice({{OCEAN}},'{{$linkpath}}')"><i class="fa fa-file-text-o" title="Documentation"></i> <span class="badge">0</span></a>
                        </div>
                    </td>
                </tr>
                @if(Auth::user()->lkp_role_id!= SELLER)
                    <tr>
                        <td class="order-pic">
                            <img src="{{url('images/log-icons/intracity.png')}}" width="50" title="{{INTRACITY_IMAGE_TITLE}}"/>
                        </td>
                        <td class="order-content">
                            <div class="info-links">
                                <a href="#"  onclick="return subcriptionuserservice({{ROAD_INTRACITY}},'{{$linkpath}}')"><i class="fa fa-envelope-o" title="Messages"></i> <span class="badge">{{$commoncomponent::getMessagesCount($userId,ROAD_INTRACITY,3)}}</span></a>
                                <a href="#" onclick="return subcriptionuserservice({{ROAD_INTRACITY}},'{{$linkpath}}')"><i class="fa fa-file-text-o" title="Documentation"></i> <span class="badge">0</span></a>
                            </div>
                        </td>
                    </tr>
                @endif
                <tr>
                    <td class="order-pic">
                        <img src="{{url('images/log-icons/courier.png')}}" width="35" title="{{COURIER_IMAGE_TITLE}}"/>
                    </td>
                    <td class="order-content">
                        <div class="info-links">
                            <a href="#"  onclick="return subcriptionuserservice({{COURIER}},'{{$linkpath}}')"><i class="fa fa-envelope-o" title="Messages"></i> <span class="badge">{{$commoncomponent::getMessagesCount($userId,COURIER,3)}}</span></a>
                            <a href="#" onclick="return subcriptionuserservice({{COURIER}},'{{$linkpath}}')"><i class="fa fa-file-text-o" title="Documentation"></i> <span class="badge">0</span></a>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="order-pic">
                        <img src="{{url('images/log-icons/truck_haul.png')}}" width="50" title="{{TRUCK_HAUL_IMAGE_TITLE}}"/>
                    </td>
                    <td class="order-content">
                        <div class="info-links">
                            <a href="#"  onclick="return subcriptionuserservice({{ROAD_TRUCK_HAUL}},'{{$linkpath}}')"><i class="fa fa-envelope-o" title="Messages"></i> <span class="badge">{{$commoncomponent::getMessagesCount($userId,ROAD_TRUCK_HAUL,3)}}</span></a>
                            <a href="#" onclick="return subcriptionuserservice({{ROAD_TRUCK_HAUL}},'{{$linkpath}}')"><i class="fa fa-file-text-o" title="Documentation"></i> <span class="badge">0</span></a>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="order-pic">
                        <img src="{{url('images/log-icons/truck_lease.png')}}" width="50" title="{{TRUCK_LEASE_IMAGE_TITLE}}"/>
                    </td>
                    <td class="order-content">
                        <div class="info-links">
                            <a href="#"  onclick="return subcriptionuserservice({{ROAD_TRUCK_LEASE}},'{{$linkpath}}')"><i class="fa fa-envelope-o" title="Messages"></i> <span class="badge">{{$commoncomponent::getMessagesCount($userId,ROAD_TRUCK_LEASE,3)}}</span></a>
                            <a href="#" onclick="return subcriptionuserservice({{ROAD_TRUCK_LEASE}},'{{$linkpath}}')"><i class="fa fa-file-text-o" title="Documentation"></i> <span class="badge">0</span></a>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="order-pic">
                        <img src="{{url('images/log-icons/domestic.png')}}" width="50" title="{{RELOCATION_DOMESTIC_IMAGE_TITLE}}"/>
                    </td>
                    <td class="order-content">
                        <div class="info-links">
                            <a href="#"  onclick="return subcriptionuserservice({{RELOCATION_DOMESTIC}},'{{$linkpath}}')"><i class="fa fa-envelope-o" title="Messages"></i> <span class="badge">{{$commoncomponent::getMessagesCount($userId,RELOCATION_DOMESTIC,3)}}</span></a>
                            <a href="#" onclick="return subcriptionuserservice({{RELOCATION_DOMESTIC}},'{{$linkpath}}')"><i class="fa fa-file-text-o" title="Documentation"></i> <span class="badge">0</span></a>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="order-pic">
                        <img src="{{url('images/log-icons/pet_move.png')}}" width="50" title="{{RELOCATION_PETMOVE_IMAGE_TITLE}}"/>
                    </td>
                    <td class="order-content">
                        <div class="info-links">
                            <a href="#"  onclick="return subcriptionuserservice({{RELOCATION_PET_MOVE}},'{{$linkpath}}')"><i class="fa fa-envelope-o" title="Messages"></i> <span class="badge">{{$commoncomponent::getMessagesCount($userId,RELOCATION_PET_MOVE,3)}}</span></a>
                            <a href="#" onclick="return subcriptionuserservice({{RELOCATION_PET_MOVE}},'{{$linkpath}}')"><i class="fa fa-file-text-o" title="Documentation"></i> <span class="badge">0</span></a>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td class="order-pic">
                        <img src="{{url('images/log-icons/international.png')}}" width="50" title="{{RELOCATION_INTERNATIONAL_IMAGE_TITLE}}"/>
                    </td>
                    <td class="order-content">
                        <div class="info-links">
                            <a href="#"  onclick="return subcriptionuserservice({{RELOCATION_INTERNATIONAL}},'{{$linkpath}}')"><i class="fa fa-envelope-o" title="Messages"></i> <span class="badge">{{$commoncomponent::getMessagesCount($userId,RELOCATION_INTERNATIONAL,3)}}</span></a>
                            <a href="#" onclick="return subcriptionuserservice({{RELOCATION_INTERNATIONAL}},'{{$linkpath}}')"><i class="fa fa-file-text-o" title="Documentation"></i> <span class="badge">0</span></a>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td class="order-pic">
                        <img src="{{url('images/log-icons/global_mobility.png')}}" width="50" title="{{RELOCATION_GLOBAL_MOBILITY_IMAGE_TITLE}}"/>
                    </td>
                    <td class="order-content">
                        <div class="info-links">
                            <a href="#"  onclick="return subcriptionuserservice({{RELOCATION_GLOBAL_MOBILITY}},'{{$linkpath}}')"><i class="fa fa-envelope-o" title="Messages"></i> <span class="badge">{{$commoncomponent::getMessagesCount($userId,RELOCATION_GLOBAL_MOBILITY,3)}}</span></a>
                            <a href="#" onclick="return subcriptionuserservice({{RELOCATION_GLOBAL_MOBILITY}},'{{$linkpath}}')"><i class="fa fa-file-text-o" title="Documentation"></i> <span class="badge">0</span></a>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td class="order-pic">
                        <img src="{{url('images/log-icons/office_domestic.png')}}" width="50" title="{{RELOCATION_OFFICE_DOMESTIC_IMAGE_TITLE}}"/>
                    </td>
                    <td class="order-content">
                        <div class="info-links">
                            <a href="#"  onclick="return subcriptionuserservice({{RELOCATION_OFFICE_MOVE}},'{{$linkpath}}')"><i class="fa fa-envelope-o" title="Messages"></i> <span class="badge">{{$commoncomponent::getMessagesCount($userId,RELOCATION_OFFICE_MOVE,3)}}</span></a>
                            <a href="#" onclick="return subcriptionuserservice({{RELOCATION_OFFICE_MOVE}},'{{$linkpath}}')"><i class="fa fa-file-text-o" title="Documentation"></i> <span class="badge">0</span></a>
                        </div>
                    </td>
                </tr>


            </table>
        </li>

    </ul>
</div>
<!--orders end-->
@endif