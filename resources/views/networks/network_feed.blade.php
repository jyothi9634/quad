@extends('network_app')

@section('content')
<div class="container-inner">
	
	<!-- Page Center Content Starts Here -->
	<div class="col-lg-7 col-md-6 col-sm-12 col-xs-12 main-middle">
		<div class="block">
			<div class="tab-nav underline">
				@include('partials.network_page_top_navigation')
			</div>
		</div>
	</div>
		
		
	 <div class="main">
               <div class="container">
                  <div class="crum-2"><i class="fa  fa-angle-right"></i>
                  @if(Auth::User()->lkp_role_id ==2)
                  Seller
                  @else
                  Buyer
                  @endif
                  <i class="fa  fa-angle-right"></i> <a href="/home">Network</a></div>
                  <span class="pull-left">
                     <h1 class="page-title">Network</h1>
                  </span>
                  <div class="gray-bg network">
                     <div class="col-md-12 network-info">
                        <div class="col-md-1 padding-none">
                           <div class="profile-pic"><img class="img-responsive" src="../images/org-logo.png"/></div>
                        </div>
                        <div class="col-md-11 padding-right-none">
                           <div class="col-md-5 title padding-none">
                              <span><a href="/network/profile/{{$profiledetails->id}}">{{$profiledetails->username}}</a></span>
                              <div class="red">
                                 <i class="fa fa-star"></i>
                                 <i class="fa fa-star"></i>
                                 <i class="fa fa-star"></i>
                              </div>
                           </div>
                           <div class="pull-right text-right">
                              <div class="info-links">
                                 <a class="show-data-link"><span class="show-icon">+</span><span class="hide-icon">-</span> Details</a>
                              </div>
                           </div>
                           <div class="col-md-12 padding-none show-data-div details">
                              <ul>
                                 <li>Year Joined - <span>2015</span><br>Followers - <span>1,220</span><br>Partners - <span>1,187</span><br>Recomendations - <span>12,445</span></li>
                                 <li>Business Type - <span>Manufacturing</span><br>Main Products/Services - <span>Consumer Electronics</span><br>Industry - <span>Hi Tech</span><br>Location - <span>Noida</span></li>
                                 <li>Year Established - <span>1995</span><br>Employees - <span>6000+</span><br>Annual Turnover - <span>INR,20,000 Crores</span><br>Main Markets - <span>All India</span></li>
                              </ul>
                           </div>
                        </div>
                     </div>
                     <div class="clearfix"></div>
                     <div class="col-md-12">
                        <ul class="nav-tabs">
                           <li class="active"><a data-toggle="tab" href="#news-feed">News Feed</a></li>
                           <li><a data-toggle="tab" href="#connections">Connections</a></li>
                           <li><a data-toggle="tab" href="#services">Services</a></li>
                        </ul>
                        <div class="col-md-12 padding-none">
                           <div class="col-md-6 padding-none">
                              <div class="profile-views"><span class="red">Profile Views:</span> 32 views since last login</div>
                           </div>
                           <div class="col-md-6 padding-none">
                              <div class="potential-partners"><span><span class="red">Potential Partners:</span> 100</div>
                           </div>
                        </div>
                        <div class="tab-content">
                           <div id="news-feed" class="tab-pane fade in active">
                              <div class="clearfix"></div>
                              <ul class="nav-tabs inner-tabs">
                                 <li class="active"><a data-toggle="tab" href="#feed-menu1">Update</a></li>
                                 <li><a data-toggle="tab" href="#feed-menu2">Post Job</a></li>
                                 <li><a data-toggle="tab" href="#feed-menu3">Publish Article</a></li>
                              </ul>
                              <div class="clearfix"></div>
                              <div class="tab-content feed-tab-content">
                                 <div id="feed-menu1" class="tab-pane fade in active">
                                    <div class="inner-block-bg padding-10">
                                       <div class="col-md-12 form-control-fld padding-none"><textarea rows="4" class="form-control form-control1"></textarea></div>
                                       <button class="btn red-btn pull-right">Update</button>
                                    </div>
                                 </div>
                                 <div id="feed-menu2" class="tab-pane fade">
                                    <div class="inner-block-bg padding-10">
                                       <div class="col-md-12 form-control-fld padding-none"><textarea rows="4" class="form-control form-control1"></textarea></div>
                                       <button class="btn red-btn pull-right">Update</button>
                                    </div>
                                 </div>
                                 <div id="feed-menu3" class="tab-pane fade">
                                    <div class="inner-block-bg padding-10">
                                       <div class="col-md-12 form-control-fld padding-none"><textarea rows="4" class="form-control form-control1"></textarea></div>
                                       <button class="btn red-btn pull-right">Update</button>
                                    </div>
                                 </div>
                              </div>
                              <div class="clearfix"></div>
                              <div class="news-feed">
                                 <div class="col-md-12 padding-none inner-form">
                                    <div class="col-md-4 form-control-fld">
                                       <div class="input-prepend">
                                          <span class="add-on"><i class="fa fa-search"></i></i></span>
                                          <input class="form-control" id="" type="text" placeholder="Search">
                                       </div>
                                    </div>
                                    <div class="col-md-4 form-control-fld">
                                       <div class="normal-select">
                                          <select data-live-search="true" class="selectpicker">
                                             <option>Feed Type</option>
                                          </select>
                                       </div>
                                    </div>
                                    <div class="col-md-2 form-control-fld">
                                       <div class="input-prepend">
                                          <span class="add-on"><i class="fa fa-calendar-o"></i></span>
                                          <input class="form-control" id="" type="text" placeholder="From Date">
                                       </div>
                                    </div>
                                    <div class="col-md-2 form-control-fld">
                                       <div class="input-prepend">
                                          <span class="add-on"><i class="fa fa-calendar-o"></i></span>
                                          <input class="form-control" id="" type="text" placeholder="To Date">
                                       </div>
                                    </div>
                                 </div>
                                 <!-- Feed Block -->
                                 <div class="col-md-12 feed-block padding-right-none">
                                    <div class="col-md-1 padding-none">
                                       <div class="profile-pic"><i class="fa fa-user"></i></div>
                                    </div>
                                    <div class="col-md-11 padding-right-none">
                                       <div class="col-md-12 feed-info">
                                          <button class="btn red-btn pull-right">Follow</button><span class="user-name"><strong>Lauren Hatry</strong><br>Course Director at DCC</span>
                                          <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>
                                          <div class="feed-links"><span>Like</span> <span>Share</span> <span>Comment</span><span class="likes"><i class="fa fa-thumbs-o-up"></i> 12</span><span class="comments"><i class="fa fa-comment-o"></i> 1</span></div>
                                       </div>
                                       <div class="col-md-12 feed-info comments-block">
                                          <div class="col-md-12 padding-none form-control-fld">
                                             <div class="user-pic pull-left"><i class="fa fa-user"></i></div>
                                             <span class="user-name pull-left"><strong>Lauren Hatry </strong> Lorem Ipsum is simply dummy</span>
                                          </div>
                                          <div class="col-md-12 padding-none" ><input type="text" placeholder="Add a comment" class="form-control form-control1"></div>
                                       </div>
                                    </div>
                                 </div>
                                 <!-- Feed Block -->
                                 <div class="col-md-12 feed-block padding-right-none">
                                    <div class="col-md-1 padding-none">
                                       <div class="profile-pic"><i class="fa fa-user"></i></div>
                                    </div>
                                    <div class="col-md-11 padding-right-none">
                                       <div class="col-md-12 feed-info">
                                          <div class="pull-right following">Following</div>
                                          <span class="user-name"><strong>Lauren Hatry</strong><br>Course Director at DCC</span>
                                          <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>
                                          <div class="feed-links"><span>Like</span> <span>Share</span> <span>Comment</span><span class="likes"><i class="fa fa-thumbs-o-up"></i> 12</span><span class="comments"><i class="fa fa-comment-o"></i> 1</span></div>
                                       </div>
                                       <div class="col-md-12 feed-info comments-block">
                                          <div class="col-md-12 padding-none form-control-fld">
                                             <div class="user-pic pull-left"><i class="fa fa-user"></i></div>
                                             <span class="user-name pull-left"><strong>Lauren Hatry </strong> Lorem Ipsum is simply dummy</span>
                                          </div>
                                          <div class="col-md-12 padding-none" ><input type="text" placeholder="Add a comment" class="form-control form-control1"></div>
                                       </div>
                                    </div>
                                 </div>
                                 <div class="clearfix"></div>
                              </div>
                           </div>
                           <div id="connections" class="tab-pane fade">
                              <div class="clearfix"></div>
                              <div class="col-md-3 padding-none">
                                 <ul class="nav-tabs connections-tabs">
                                    <li class="active"><a data-toggle="tab" href="#partners">Partners</a></li>
                                    <li><a data-toggle="tab" href="#following">Following</a></li>
                                    <li><a data-toggle="tab" href="#groups">Groups (Memberships)</a></li>
                                 </ul>
                              </div>
                              <div class="col-md-9 padding-none">
                                 <div class="clearfix"></div>
                                 <div class="tab-content connections-content-tabs">
                                    <div id="partners" class="tab-pane fade in active">
                                       <div class="inner-block-bg">
                                          <div class="col-md-8 form-control-fld padding-none">
                                             <div class="input-prepend">
                                                <span class="add-on"><i class="fa fa-search"></i></span>
                                                <input type="text" placeholder="Search" id="" class="form-control">
                                             </div>
                                          </div>
                                          <div class="clearfix"></div>
                                          <div id="myCarousel1" class="carousel slide" data-ride="carousel">
                                             <!-- Wrapper for slides -->
                                             <div class="carousel-inner" role="listbox">
                                             {{--*/ $g =1; $ga=1; /*--}}
                                             @foreach($getLogoPartners as $key=>$getLogoPartner)
                                          		{{--*/ $getLogo = $getLogoPartner->logo; $active=''; /*--}}
                                          		@if($ga==1)
                                          			{{--*/ $active = 'active' /*--}}
                                          		@endif
                                          		
                                          		
                                          		 @if ($g%5 == 1)
											      
											     <div class="item {{$active}}">
                                                <ul>   
											    @endif
											    
											    <li><img class ='descriptionuser' id ='{{ $getLogoPartner->partner_user_id }}' src="{{url($getLogo)}}"></li>
											    
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
                                          <div class="clearfix"></div>
                                          <h3>Description</h3>
                                          <p id='desctiption_partner'>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages.</p>
                                       </div>
                                    </div>
                                    <div id="following" class="tab-pane fade">
                                       <div class="inner-block-bg">
                                          <div class="col-md-8 form-control-fld padding-none">
                                             <div class="input-prepend">
                                                <span class="add-on"><i class="fa fa-search"></i></span>
                                                <input type="text" placeholder="Search" id="" class="form-control">
                                             </div>
                                          </div>
                                          <div class="clearfix"></div>
                                          <div id="myCarousel2" class="carousel slide" data-ride="carousel">
                                             <!-- Wrapper for slides -->
                                             <div class="carousel-inner" role="listbox">
                                                {{--*/ $f = 1; $fa=1; /*--}}
                                             @foreach($getLogoFollowers as $key=>$getLogoFollower)
                                          	 {{--*/ $getLogo = $getLogoFollower->logo; $active=''; /*--}}
                                          		@if($fa==1)
                                          			{{--*/ $active = 'active' /*--}}
                                          		@endif
                                          		
                                          		
                                          		 @if ($f%5 == 1)
											      
											     <div class="item {{$active}}">
                                                <ul>   
											    @endif
											    
											    <li><img class ='descriptionfollows' id ='{{ $getLogoFollower->follower_user_id }}' src="{{url($getLogo)}}"></li>
											    
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
                                          <div class="clearfix"></div>
                                          <h3>Description</h3>
                                          <p id='desctiption_follows'>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages.</p>
                                       </div>
                                    </div>
                                    <div id="groups" class="tab-pane fade">
                                       <div class="inner-block-bg">
                                          <div class="col-md-8 form-control-fld padding-none">
                                             <div class="input-prepend">
                                                <span class="add-on"><i class="fa fa-search"></i></span>
                                                <input type="text" placeholder="Search" id="" class="form-control">
                                             </div>
                                          </div>
                                          <div class="clearfix"></div>
                                          <div id="myCarousel3" class="carousel slide" data-ride="carousel">
                                             <!-- Wrapper for slides -->
                                             <div class="carousel-inner" role="listbox">
                                             {{--*/ $count_groups = count($getLogoCommunitygroups) /*--}}
                                             {{--*/ $id = Auth::User()->id /*--}}
                                             {{--*/ $p = 1; $pa=1; $url = "uploads/community/groups/$id/" /*--}}
                                             @foreach($getLogoCommunitygroups as $key=>$getLogoCommunitygroup)
                                          	 {{--*/ $getLogogroup = $getLogoCommunitygroup->logo_file_name; $active=''; /*--}}
                                          	 {{--*/ $getLogogroup = $url.$getLogogroup /*--}}
                                          			
                                          		@if($pa==1)
                                          			{{--*/ $active = 'active' /*--}}
                                          		@endif
                                          		
                                          		
                                          		 @if ($p%5 == 1)
											      
											         <div class="item {{$active}}">
                                                		<ul>   
											    @endif
											    
											    <li><img class ='descriptiongroups' id ='{{ $getLogoCommunitygroup->id }}' src="{{url($getLogogroup)}}"></li>
											    
											    

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
                                          <div class="clearfix"></div>
                                          <h3>Description</h3>
                                          <p id='desctiption_groups'>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages.</p>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                           <div id="services" class="tab-pane fade">
                              <ul class="nav-tabs inner-tabs">
                                 <li class="active"><a data-toggle="tab" href="#offered">Services Offered</a></li>
                                 <li><a data-toggle="tab" href="#required">Services Required</a></li>
                              </ul>
                              <div class="clearfix"></div>
                              <div class="tab-content feed-tab-content">
                                 <div id="offered" class="tab-pane fade in active">
                                    <div class="inner-block-bg padding-10">
                                       Services Offered
                                    </div>
                                 </div>
                                 <div id="required" class="tab-pane fade">
                                    <div class="inner-block-bg padding-10">
                                       Services Required
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         <div class="clearfix"></div>
		
	
</div>
@include('partials.footer')
@endsection

