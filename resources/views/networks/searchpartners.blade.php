<div class="carousel-inner" role="listbox">
                                                {{--*/ $g =1; $ga=1; /*--}}
                                             @foreach($getLogoPartners as $key=>$getLogoPartner)
                                             
                                             @if($getLogoPartner->user_id == Auth::user()->id)
                                             {{--*/ $getLogoPartner->partner_user_id = $getLogoPartner->partner_user_id /*--}}
                                             @else
                                             {{--*/ $getLogoPartner->partner_user_id = $getLogoPartner->user_id /*--}} 
                                             @endif
                                             
                                          		{{--*/ $getLogo = $getLogoPartner->logo; $active=''; /*--}}
                                          		{{--*/ $partner_id_profile = "network/profile/$getLogoPartner->partner_user_id" /*--}}
                                          		@if($getLogoPartner->lkp_role_id == 2)
                                          		{{--*/ $url = "uploads/seller/$getLogoPartner->partner_user_id/" /*--}}
                                          		@else
                                          		{{--*/ $url = "uploads/buyer/$getLogoPartner->partner_user_id/" /*--}}
                                          		@endif
                                          		{{--*/ $getlogopartner = $url.$getLogo /*--}}
                                          		
                                          		@if($ga==1)
                                          			{{--*/ $active = 'active' /*--}}
                                          		@endif
                                          		
                                          		
                                          		 @if ($g%5 == 1)
											      
											     <div class="item {{$active}}">
                                                <ul>   
											    @endif
											     @if($getLogo != '')
											    
														    @if(\File::exists($getlogopartner))
														    
														    {{--*/ $getlogopartner =  $commoncomponent::str_replace_last( '.' , '_124_73.' , $getlogopartner ) /*--}}
														    
														    <a href='{{url($partner_id_profile)}}'>
														    <li class ='descriptionuser' id ='{{ $getLogoPartner->partner_user_id }}'>
														    <img id ='{{ $getLogoPartner->partner_user_id }}' src="{{url($getlogopartner)}}"></li>
														    </a>
														    @else
														    <a href='{{url($partner_id_profile)}}'>
														    <li class ='descriptionuser' id ='{{ $getLogoPartner->partner_user_id }}'>
														    <img id ='{{ $getLogoPartner->partner_user_id }}' src="../images/profile-pic.png">
														    </li>
														    </a>
														    @endif
												@else
											    <a href='{{url($partner_id_profile)}}'>
											    <li class ='descriptionuser' id ='{{ $getLogoPartner->partner_user_id }}'>
											    <img id ='{{ $getLogoPartner->partner_user_id }}' src="../images/profile-pic.png">
											    </li>
											    </a>
											    @endif
											   @if ($g%5 == 0)
											    
											    </ul>
											    </div>
											    @endif
											    {{--*/ $g++; $ga++/*--}}
											  @endforeach
											
											@if ($g%5 != 1)
												</ul>
                                                </div>
                                             @endif
                                             </div>
                                             <!-- Left and right controls -->
                                             <a class="left carousel-control" href="#myCarousel1" role="button" data-slide="prev">
                                             <i class="fa fa-chevron-left"></i>
                                             </a>
                                             <a class="right carousel-control" href="#myCarousel1" role="button" data-slide="next">
                                             <i class="fa fa-chevron-right"></i>
                                             </a>
                                          </div>