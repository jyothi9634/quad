 <div class="carousel-inner" role="listbox">
                                             {{--*/ $id = Auth::User()->id /*--}}
                                             {{--*/ $p = 1; $pa=1; $url = "uploads/community/groups/$id/" /*--}}
                                             @foreach($getLogoCommunitygroups as $key=>$getLogoCommunitygroup)
                                          	 	{{--*/ $getLogogroup = $getLogoCommunitygroup->logo_file_name; $active=''; /*--}}
                                          	 	{{--*/ $getLogogroup = $url.$getLogogroup /*--}}
                                          	 	{{--*/ $group_user_id_profile = "community/groupdetails/$getLogoCommunitygroup->id" /*--}}
                                          		@if($pa==1)
                                          			{{--*/ $active = 'active' /*--}}
                                          		@endif
                                          		@if ($p%5 == 1)
											         <div class="item {{$active}}">
                                                	 	<ul>   
											    @endif
											    @if($getLogogroup != '')
												    @if(\File::exists($getLogogroup))
												    <a href='{{url($group_user_id_profile)}}'>
												    <li class ='descriptiongroups' id ='{{ $getLogoCommunitygroup->id }}'>
												    <img id ='{{ $getLogoCommunitygroup->id }}' src="{{url($getLogogroup)}}"></li>
												    </a>
												     @else
												     <a href='{{url($group_user_id_profile)}}'>
												    <li class ='descriptiongroups' id ='{{ $getLogoCommunitygroup->id }}'>
												    <img id ='{{ $getLogoCommunitygroup->id }}' src="../images/profile-pic.png">
												    </li>
												    </a>
												    @endif
											     @else
											     <a href='{{url($group_user_id_profile)}}'>
											    <li class ='descriptiongroups' id ='{{ $getLogoCommunitygroup->id }}'>
											    <img id ='{{ $getLogoCommunitygroup->id }}' src="../images/profile-pic.png">
											    </li>
											    </a>
											    @endif
											    
											    @if ($p%5 == 0)
											    </ul>
                                                </div>
											    @endif
											    
											    {{--*/ $p++; $pa++/*--}}
											@endforeach
											
											@if ($p%5 != 1)
													</ul>
                                                </div>
                                             @endif
                                             </div>
                                             <!-- Left and right controls -->
                                             <a class="left carousel-control" href="#myCarousel3" role="button" data-slide="prev">
                                             <i class="fa fa-chevron-left"></i>
                                             </a>
                                             <a class="right carousel-control" href="#myCarousel3" role="button" data-slide="next">
                                             <i class="fa fa-chevron-right"></i>
                                             </a>
                                          </div>