@extends('network_app')
@inject('commoncomponent', 'App\Components\CommonComponent')

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
                  <a href="/network">Network</a><i class="fa  fa-angle-right"></i> <a href="javascript:void(0)">Profile</a></div>
                  <span class="pull-left">
                     <h1 class="page-title">Profile</h1>
                  </span>
                  <div class="gray-bg network network-profile">
                    
                   
                     {{-- Network Header file --}}
            			@include('networks/header')
                     
                     <div class="clearfix"></div>
                     <div class="col-md-12">
                        <div class="profile-banner">
                        @if($profiledetails->lkp_role_id == 2)
							{{--*/ $url = "uploads/seller/$profiledetails->id/" /*--}}
				        @else
				        	{{--*/ $url = "uploads/buyer/$profiledetails->id/" /*--}}
				        @endif
				        {{--*/ $getlogo = $url.$profiledetails->logo /*--}}
				        {{--*/ $logo =  $commoncomponent::str_replace_last( '.' , '_986_280.' , $getlogo ) /*--}}
				        
				        <div class="col-md-10 banner padding-none">
				        
				        @if($profiledetails->logo != '')
					        @if(file_exists($logo))
					        <img src="{{url($logo)}}"/>
					        @else
					         <img src="{{ asset('/images/not-available.jpg') }}" width="100%" />
					        @endif
					    @else
				         <img src="{{ asset('/images/not-available.jpg') }}" width="100%" />
				        @endif
				        </div>
                          
                           <div class="col-md-2">
                              <ul>
                              	@if($profiledetails->id != Auth::User()->id)
                                 <li id="followingid">
                                 @if($follower == 1)
                                  <a href="javascript:void(0)" data-target="#followprofile" data-toggle="modal" onclick="followNetworkProfile({{ $profiledetails->id }},0)">Following </a><i class="fa fa-check"></i> 
                                 @else
                                 <a href="javascript:void(0)" data-target="#followprofile" data-toggle="modal" onclick="followNetworkProfile({{ $profiledetails->id }},1)">Follow</a>
                                 @endif
                                </li>
                                 <li>
                                 @if($partners == 0)
                                 <a href="javascript:void(0)" data-target="#partnerRequest" data-toggle="modal" onclick="partnerRequest({{ $profiledetails->id }})">Partner</a>
                                 @elseif($partners == 1)
                                 Partner Request is sent  
                                 @else
                                 Partner <i class="fa fa-check"></i> 
                                 @endif
                                 </li>
                                 <li>
                                 @if(count($recomendations)==0)
                                 <a href="#"  data-toggle="modal" data-target="#popup4">Recommend</a>
                                 @elseif($recomendations[0]->is_approved == 0)
                                 Recommendation sent
                                 @else
                                 Recommended
                                 @endif
                                 </li>
                                 <li><a href="#"  data-toggle="modal" data-target="#popup5">Share</a></li>
                                 <li><a href="#"  data-toggle="modal" data-target="#popup2">Message</a></li>
                                 <li><a href="#"  data-toggle="modal" data-target="#popup1">Contact Details</a></li>
                                @else
                                 <li><a href="#"  data-toggle="modal" data-target="#popup3">Partner({{count($partnersrequestlist)}})</a></li>
                                 <li><a href="#"  data-toggle="modal" data-target="#popup1">Contact Details</a></li>
                                @endif
                              </ul>
                           </div>
                           <div class="clearfix"></div>
                        </div>
                        <div>
                           <p>{{$profiledetails->description}}</p>
                        </div>
                        <div class="col-md-12 padding-none activities">
                           <div class="col-md-3">
                           	<div class="service-count">
                              <div class="total-available">{{count($buyerRequriedlist) + count($sellerservices) }}</div>
                              <ul>
                                 <li class="head">Marketplace Activity</li>
                                 @if(count($buyerRequriedlist)>0)
                                 <li>Buying:</li>
                                 @foreach($buyerRequriedlist as  $key=>$buyerlist)
                                 <li class="child1">
                                 @if($id == Auth::User()->id)
                                 <a onclick="return buyersetservice({{ $key }},'/buyerposts')">{{$buyerlist}}</a>
                                 @else
                                 {{$buyerlist}}
                                 @endif
                                 </li>
                                 @endforeach
                                 @endif
                                 @if(count($sellerservices)>0)
                                 <li>Selling:</li>
                                 @foreach($sellerservices as  $key=>$sellerlist)
                                 <li class="child1">
                                 @if($id == Auth::User()->id)
                                 <a onclick="return subcriptionuserservice({{ $key }},'/sellerlist')">{{$sellerlist}}</a>
                                 @else
                                 {{$sellerlist}}
                                 @endif
                                 </li>
                                 @endforeach
                                 @endif
                              </ul>
                             </div>
                           </div>
                           <div class="col-md-3">
                           	<div class="service-count">
                              <div class="total-available">
                              {!! $commoncomponent::getPartnerApproved($profiledetails->id) + $commoncomponent::getpersonalPartnerApproved($profiledetails->id) + $commoncomponent::getFollowingList($profiledetails->id) + count($recomendationlist) + count($recomendationpersonallist) !!}
                              
                              </div>
                              <ul>
                                 <li class="head">Network Activity</li>
                                 <li><a href="/network/partnerslist/{{$profiledetails->id}}">Partnerships</a></li>
                                 <li><a href="/network/followlist/{{$profiledetails->id}}">Following</a></li>
                                 <li><a href="/network/recomendationslist/{{$profiledetails->id}}">Recommendations</a></li>
                              </ul>
                             </div>
                           </div>
                           <div class="col-md-3">
                           	<div class="service-count">
                              <div class="total-available">
                              {{ $profileJobCount }}
                              </div>
                              <ul>
                                 <li class="head"><a href="/network/jobslist/{{$profiledetails->id}}">Jobs</a></li>
                                 @foreach($profileJobs as $jobslist)
                                 <li>{{ ucfirst($jobslist->feed_title) }}</li>
                                 @endforeach
                              </ul>
                             </div>
                           </div>
                           <div class="col-md-3 padding-right-none">
                           	<div class="service-count">
                              <div class="total-available">
                              {{ $commoncomponent::getArticlesList($profiledetails->id,'count') }}
                              </div>
                              <ul>
                                 <li class="head"><a href="/network/articleslist/{{$profiledetails->id}}">Articles</a></li>
                                 {{--*/ $recentArticles = $commoncomponent::getArticlesList($profiledetails->id,'last5') /*--}}
                                 @foreach($recentArticles as $art)
                                 <li>{{ ucfirst($art->feed_title) }}</li>
                                 @endforeach
                              </ul>
                             </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         <div class="clearfix"></div>
          <!-- POPUP - 1 -->
            <div class="modal fade" id="popup1" role="dialog">
               <div class="modal-dialog">
                  <!-- Modal content-->
                  <div class="modal-content">
                     <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Contact Details</h4>
                     </div>
                     <div class="modal-body">
                        <div class="popup-gray-bg">
                           <p><strong class="black">Name :</strong> {{$profiledetails->username}}</p>
                           <p><strong class="black">Email :</strong> {{$profiledetails->email}}</p>
                           <p><strong class="black">Phone :</strong> {{$profiledetails->phone}}</p>
                           <p><strong class="black">Address :</strong> 
                           {{--*/ $address =  $commoncomponent::getUserDetails($profiledetails->id) /*--}}
                           {{$address->address}}
                           </p>
                        </div>
                     </div>
                     <div class="modal-footer">
                     </div>
                  </div>
               </div>
            </div>
            <!-- POPUP - 2 -->
            <div class="modal fade" id="popup2" role="dialog">
            {!! Form::open(array('url' => 'profilemessage', 'id' => 'profilemessage', 'name' => 'profilemessage', 'method'=>'POST')) !!}
               <input type='hidden' name='profileid' id='profileid'' value="{{ $profiledetails->id }}">
               <div class="modal-dialog">
                  <!-- Modal content-->
                  <div class="modal-content">
                     <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Message</h4>
                     </div>
                     <div class="modal-body">
                        <div class="margin-bottom">
                           <input name="message_subject" id="message_subject" class="form-control form-control1" size="5" placeholder="Subject *">
                           <span class= "red" id="messagesubject_error"></span>
                        </div>
                        <div>
                           <textarea id="message_body" class="form-control form-control1 message_body" placeholder="Message *" name="message_body" cols="50" rows="10"></textarea>
                            <span class= "red" id="messagebody_error"></span>
                        </div>
                     </div>
                     <div class="modal-footer">
                        <button type="button" class="btn add-btn flat-btn" data-dismiss="modal">Cancel</button>
                        {!! Form::button('Submit', array('id'=>'addmessage','class'=>'btn red-btn pull-right flat-btn addmessage' )) !!}
                     </div>
                  </div>
               </div>
               {!! Form::close() !!}
            </div>
		<!-- POPUP - 3 -->
		
		
            <div class="modal fade" id="popup3" role="dialog">
               <div class="modal-dialog" style="width: 800px;">
                  <!-- Modal content-->
                  
                  <div class="modal-content">
                     <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Partners with {{$profiledetails->username}}</h4>
                     </div>
                     <div class="modal-body">
                        <div class="network">
                           <div class="col-md-12 network-info">
                              <div class="col-md-12 padding-right-none">
                                 <div class="pull-right list-info"><span class="black">Received ({{count($partnersrequestlist)}}) </span></div>
                              </div>
                           </div>
                           
                           <div class="col-md-12 news-feed partners-list padding-left-none">
                           @foreach($partnersrequestlist as $partnerlist)
                              <div class="col-md-6 padding-right-none">
                                 <div class="partner">
                                    <div class="col-md-4 padding-none">
                                       <div class="profile-pic"><i class="fa fa-user"></i></div>
                                    </div>
                                    <div class="col-md-8 padding-right-none">
                                       <div class="col-md-12 padding-left-none">
                                          <span class="user-name"><strong>{{$partnerlist->username}}</strong><br>
                                          <span class="sub-link">Cource Director at DCC</span>
                                          </span>
                                       </div>
                                       @if($partnerlist->is_approved ==0)
                                       <a href="javascript:void(0)" data-target="#partnerRequest" data-toggle="modal" onclick="acceptpartner({{ $partnerlist->user_id }})"class="btn red-btn flat-btn pull-left">Accept</a>
                                       @else
                                       <a href="javascript:void(0)" data-target="#partnerRequest" data-toggle="modal" class="btn red-btn pull-left">Accepted</a>
                                       @endif
                                    </div>
                                 </div>
                              </div>
                               @endforeach
                           </div>
                          
                        </div>
                     </div>
                  </div>
                    
               </div>
            </div>
          
            <!-- POPUP - 3 -->
             <!-- POPUP - 4 -->
           
            <div class="modal fade" id="popup4" role="dialog">
           	   {!! Form::open(array('url' => 'addrecomendation', 'id' => 'addrecommend', 'name' => 'addrecommend', 'method'=>'POST')) !!}
               <input type='hidden' name='profileid' id='profileid'' value="{{ $profiledetails->id }}">
               <div class="modal-dialog">
                  <!-- Modal content-->
                  <div class="modal-content">
                     <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Recommend with {{$profiledetails->username}}</h4>
                     </div>
                     <div class="modal-body">
                        <div>
                           <textarea id="recomendation_body" class="form-control form-control1 recomendation_body" placeholder="Write a Recommendation *" name="recomendation_body" cols="50" rows="10"></textarea>
                           <span class= "red" id="recomendation_body_error"></span>
                        </div>
                     </div>
                     <div class="modal-footer">
                        <button type="button" class="btn add-btn flat-btn" data-dismiss="modal">Cancel</button>                        
                       {!! Form::button('Submit', array('id'=>'recommendation','class'=>'btn red-btn pull-right flat-btn recommendation' )) !!}
                     </div>
                  </div>
               </div>
               {!! Form::close() !!}
            </div>
            <!-- POPUP - 4 -->
            <!-- POPUP - 5 -->
            <div class="modal fade" id="popup5" role="dialog">
            {!! Form::open(array('url' => 'shareprofile', 'id' => 'shareprofile', 'name' => 'shareprofile', 'method'=>'POST')) !!}
               <div class="modal-dialog">
                  <!-- Modal content-->
                  <div class="modal-content">
                     <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Share To {{$profiledetails->username}}</h4>
                     </div>
                     <div class="modal-body">
                     	<div class="margin-bottom" >
		                    <div class="input-prepend">
		                        <input type="hidden" id="share_id" name="share_id" placeholder="To" />
		                    </div>
		                    <span class= "red" id="shareid__error"></span>
	                    </div>
                        
                        <div class="margin-bottom">
                           <input name="share_subject" id="share_subject" class="form-control form-control1" size="5" placeholder="Subject *">
                           <span class= "red" id="subject_error"></span>
                        </div>
                        <div class="margin-bottom">
                           <textarea id="share_body" class="form-control form-control1 share_body" placeholder="Message *" name="share_body" cols="50" rows="10"></textarea>
                            <span class= "red" id="sharebody__error"></span>
                        </div>
                        <div class="margin-bottom">
                           <input readonly name="user_link" id="user_link" class="form-control form-control1" size="5" value="{{Request::url()}}">
                        </div>
                     </div>
                     <div class="modal-footer">
                     <input type="hidden" name="profid" id="profid" value="{{$id}}">
                        <button type="button" class="btn add-btn flat-btn" data-dismiss="modal">Cancel</button>
                        {!! Form::button('Submit', array('id'=>'sharesubmit','class'=>'btn red-btn pull-right flat-btn sharesubmit' )) !!}
                     </div>
                  </div>
               </div>
               {!! Form::close() !!}
            </div>
            <!-- POPUP - 5 -->
             
	
</div>
@include('partials.footer')
@endsection

