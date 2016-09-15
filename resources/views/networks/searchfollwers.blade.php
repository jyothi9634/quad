<div class="carousel-inner" role="listbox">
                                                 {{--*/ $f = 1; $fa=1; /*--}}
                                             @foreach($getLogoFollowers as $key=>$getLogoFollower)
                                          	 {{--*/ $getLogo = $getLogoFollower->logo; $active=''; /*--}}
                                          	 {{--*/ $follower_user_id_profile = "network/profile/$getLogoFollower->follower_user_id" /*--}}
                                          	 @if($getLogoFollower->lkp_role_id == 2)
                                          		{{--*/ $url = "uploads/seller/$getLogoFollower->follower_user_id/" /*--}}
                                          		@else
                                          		{{--*/ $url = "uploads/buyer/$getLogoFollower->follower_user_id/" /*--}}
                                          		@endif
                                          		{{--*/ $getlogofollower = $url.$getLogo /*--}}
                                          	 
                                          	 
                                          		@if($fa==1)
                                          			{{--*/ $active = 'active' /*--}}
                                          		@endif
                                          		
                                          		
                                          		 @if ($f%5 == 1)
											      
											     <div class="item {{$active}}">
                                                <ul>   
											    @endif
											    
											    @if($getLogo != '')
												     @if(\File::exists($getlogofollower))
												     
												      {{--*/ $getlogofollower =  $commoncomponent::str_replace_last( '.' , '_124_73.' , $getlogofollower ) /*--}}
												      
												     <a href='{{url($follower_user_id_profile)}}'>
												    <li class ='descriptionfollows' id ='{{ $getLogoFollower->follower_user_id }}'>
												    <img id ='{{ $getLogoFollower->follower_user_id }}' src="{{url($getlogofollower)}}"></li>
												     </a>
												     @else
												     <a href='{{url($follower_user_id_profile)}}'>
												    <li class ='descriptionfollows' id ='{{ $getLogoFollower->follower_user_id }}'>
												    <img id ='{{ $getLogoFollower->follower_user_id }}' src="../images/profile-pic.png">
												    </li>
												    </a>
												    @endif
											    @else
											     <a href='{{url($follower_user_id_profile)}}'>
											    <li class ='descriptionfollows' id ='{{ $getLogoFollower->follower_user_id }}'>
											    <img id ='{{ $getLogoFollower->follower_user_id }}' src="../images/profile-pic.png">
											    </li>
											    </a>
											    @endif
											   @if ($f%5 == 0)
											    
											    </ul>
											    </div>
											    @endif
											    {{--*/ $f++; $fa++/*--}}
											  @endforeach
											
											@if ($f%5 != 1)
												</ul>
                                                </div>
                                             @endif
                                             </div>
                                             <!-- Left and right controls -->
                                             <a class="left carousel-control" href="#myCarousel2" role="button" data-slide="prev">
                                             <i class="fa fa-chevron-left"></i>
                                             </a>
                                             <a class="right carousel-control" href="#myCarousel2" role="button" data-slide="next">
                                             <i class="fa fa-chevron-right"></i>
                                             </a>
                              </div>
                                         