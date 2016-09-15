@extends('community_app')
@section('content')
@inject('commonComponent', 'App\Components\CommonComponent')
@inject('communityComponent', 'App\Components\community\CommunityComponent')
{{--*/ $members =   $commonComponent->getMembers($displayGroupDetails->id) /*--}}

<!-- Page Center Content Starts Here -->
	<div class="col-lg-7 col-md-6 col-sm-12 col-xs-12 main-middle">
		<div class="block">
			<div class="tab-nav underline">
				@include('partials.community_page_top_navigation')
			</div>
		</div>
	</div>
         <!-- Inner Menu Ends Here -->

            <div class="main">
            @if(Session::has('gcmsg'))
	        <div class="flash">
			<p class="text-success col-sm-12 text-center flash-txt alert-success">
			{{ Session::get('gcmsg') }}
			</p>
			</div>
			@endif
            @if(Session::has('message') && Session::get('message')!='')
	        <div class="flash">
            <p class="text-success col-sm-12 text-center flash-txt alert-success">
            {{ Session::get('message') }}
            </p>
          	</div>
            @endif

               <div class="container">
                  <div class="crum-2">
                     <i class="fa  fa-angle-right"></i> Seller <i class="fa  fa-angle-right"></i> <a href="/home">Home</a> <i class="fa  fa-angle-right"></i> Community <i class="fa  fa-angle-right"></i> Group Detail
                  </div>
                  <span class="pull-left">
                     <h1 class="page-title">Group Detail</h1>
                  </span>
                  <div class="gray-bg network community">
                     <div class="col-md-12 network-info">
                        <div class="col-md-1 padding-none">
                        {{--*/ $userId = Auth::id() /*--}}
                        @if($displayGroupDetails->logo_file_name!='' && file_exists("uploads/community/groups/".$displayGroupDetails->created_by."/".$displayGroupDetails->logo_file_name))
                           <div class="profile-pic">
                          	 <img class="img-responsive" src="{{ asset('uploads/community/groups/'.$displayGroupDetails->created_by.'/'.$displayGroupDetails->logo_file_name) }}">
                           </div>
                           @else
                            <div class="profile-pic">
                          	 <img class="img-responsive" src="{{URL::asset('images/org-logo.png')}}">
                          	</div>
                           @endif
                        </div>
                        <div class="col-md-11 padding-right-none">
                           <div class="col-md-5 title padding-none">
                              <span>{!! $displayGroupDetails->group_name !!}</span><br>
                              <span class="sub-link">{{count($members)}} members</i>
                              </span>
                           </div>
                           <div class="pull-right">
                               @if($displayGroupDetails->created_by==Auth::id())
                                <a href="/community_deactivited_member/{{ $displayGroupDetails->id }}/{{$displayGroupDetails->created_by}}/1" class="btn red-btn"> Activate</a>
                               @endif
                              <div class="clearfix"></div>
                            </div>
                        </div>
                     </div>
                     <div class="clearfix"></div>
                     <div class="col-md-8 padding-none displayNone">
                     
                        <div class="col-md-12 news-feed group-detail">
                           <div class="clearfix"></div>
                            @if(count($getconversationGroupsData)!=0)
                           <ul class="nav-tabs group-detail-tabs">
                              <li class="active"><a data-toggle="tab" href="#feed-menu1" class="conv_show">Conversations</a></li>
                              <li><a data-toggle="tab" href="#feed-menu2" class="jobs_hide">Jobs</a></li>
                           </ul>
                            @endif
                           <div class="clearfix"></div>

                           <div class="tab-content feed-tab-content">
                              <div id="feed-menu1" class="tab-pane fade in active">
                                  <div class="col-md-12 padding-none" id="article_posts">
                                     @foreach($getconversationGroupsData as $getallConverseData)
                                        {{--*/ $timestamp = $getallConverseData->created_at  /*--}}
                                        {{--*/  $splits =  explode(" ",$timestamp) /*--}}
                                        {{--*/  $get_craetd_date = $splits[0]	/*--}}
                                        {{--*/  $getCreatedDate = $commonComponent->checkAndGetDate($get_craetd_date) /*--}}

                                        {{--*/  $postLikesCount = $communityComponent->postLikesCount($getallConverseData->id) /*--}}
                                        {{--*/  $postLikesUsers = $communityComponent->postLikesNames($getallConverseData->id) /*--}}
                                        {{--*/ $names='' /*--}}
                                        @foreach($postLikesUsers as $postLikesUser)
                                        <?php  $names   .="\n$postLikesUser->username" ?>
                                        @endforeach
                                        <?php   //$postComCnt = $communityComponent->postCommCount($getallConverseData->id) ?>
                                        {{--*/  $postLikestextChange = $communityComponent->postLikesTextChangeFun($getallConverseData->id) /*--}}
                                        {{--*/  $postComCnt = $communityComponent->postCommCount( $displayGroupDetails->id,$getallConverseData->id ) /*--}}
                                        
                                    <div class="col-md-12 feed-info">
                                       <div class="post-title">
                                          <div class="col-md-12 padding-none">

                                            @if($getallConverseData->logo!='' && file_exists($getallConverseData->logo))
                                             <div class="user-pic pull-left">
                                            	 <img class="img-responsive" src="{{ asset($getallConverseData->logo) }}">
                                             </div>
	                                        @else
	                                          <div class="user-pic pull-left">
	                                         	  <i class="fa fa-user"></i>
	                                           </div>
	                                        @endif

                                             <span class="user-name pull-left"><strong>{!! $getallConverseData->username !!}</strong></span>
                                              <div class="pull-right close community_close_converse_div" close_div_id="{!! $getallConverseData->id !!}"><i class="fa fa-times"></i></div>
                                          </div>
                                          <div class="clearfix"></div>
                                          <h2><br>{!! $getallConverseData->title !!}</h2>
                                       </div>
                                       <div class="post-text middle_chat_div_hideshow_{!! $getallConverseData->id !!}">
                                          <div class="accordion-group text">
                                             <div class="accordion-heading">
                                                {{--*/   $comments = substr($getallConverseData->comments,0,250) /*--}}
                           			{!! $comments !!}
                                             </div>
                                              <div class="accordion-heading-hide" style="display: none;">
                                                {{--*/   $comments = substr($getallConverseData->comments,0,250) /*--}}
                           			{!! $comments !!}
                                             </div>
                                             <div id="collapseOne" class="accordion-body collapse showmore_desc_{!! $getallConverseData->id !!}">
                                                {{--*/   $comments1 = substr($getallConverseData->comments,0,100000) /*--}}
                                                {!! $comments1 !!}
                                             </div>
                                              @if(strlen($getallConverseData->comments)>250)
                                             <span class="more-text community_converstion_shomore_desc SeeMore2" show_more_id="{!! $getallConverseData->id !!}">
                                                 <!--button class="SeeMore2" data-toggle="" href="#collapseTwo"-->Show More
                                                 <!--/button-->
                                             </span>
                                              @endif
                                          </div>
                                          <div class="feed-links" id="feed-links_{!! $getallConverseData->id !!}">
                                             <div class="pull-right gray">{!! $getCreatedDate !!}</div>
                                             <span >
                                             @if($postLikestextChange==0)
                                             Like
                                             @else
                                             Unlike
                                             @endif
                                             </span> 
                                             <span id="comment_focus_id_{!! $getallConverseData->id !!}" class="comment_focus_textbox" focus_id="{!! $getallConverseData->id !!}">Comment</span>

                                             <span class="likes" ><i class="fa fa-thumbs-o-up"></i> <span id="post_likes_count_{!! $getallConverseData->id !!}" data-toggle="tooltip" data-placement="top" title="{{$names}}" >{!! $postLikesCount !!}</span> </span>

                                             <span class="comments" data-comCnt="{{$postComCnt}}"><i class="fa fa-comment-o"></i> {{$postComCnt}}</span>
                                             
                                          </div>
                                          <div class="col-md-12 comments-block">
                                              {{--*/  $getAllPostCommentsCount = count($communityComponent->getMainPostComments( $displayGroupDetails->id,$getallConverseData->id,0,false )) /*--}}
                                              @if($getAllPostCommentsCount > 5)
                                                <a class="load_more_comments" id="load_more_comments{{$getallConverseData->id}}"  href="javascript:void(0)" post_id="{{$getallConverseData->id}}" iteration="1">View previous comments</a>
                                              @endif
                                           <div id="ajxLoadPostCom{{$getallConverseData->id}}">
                                           {{--*/  $getAllPostComments = $communityComponent->getMainPostComments( $displayGroupDetails->id,$getallConverseData->id ) /*--}}
                                           @foreach($getAllPostComments as $getPostcomment)
	                                       {{--*/ $timestamp = $getPostcomment->created_at  /*--}}
	                             		   {{--*/  $splits =  explode(" ",$timestamp) /*--}}
	                              		   {{--*/  $get_craetd_date = $splits[0]	/*--}}
	                              		   {{--*/  $getPostCreationDate = $commonComponent->checkAndGetDate($get_craetd_date) /*--}}
	                              		   {{--*/  $postSubCommentLikesCount = $communityComponent->postLikesCount($getPostcomment->id) /*--}}
	                                       {{--*/  $postSubLikestextChange = $communityComponent->postLikesTextChangeFun($getPostcomment->id) /*--}}


                                             <div class="col-md-12 padding-none form-control-fld" id="hide_delete_post_dev_{{$getPostcomment->id}}">
                                                

                                                @if($getPostcomment->logo!='' && file_exists($getPostcomment->logo))
	                                          	<div class="user-pic pull-left">
	                                            <img class="img-responsive" src="{{ asset($getPostcomment->logo) }}">
	                                         	</div>
		                                        @else
		                                        <div class="user-pic pull-left"><i class="fa fa-user"></i></div>
		                                        @endif

                                                <span class="user-name pull-left">
                                                   <strong>{!! $getPostcomment->username !!} </strong>
                                                   <span id="update_cmnt_{{ $getPostcomment->id }}">{!! $getPostcomment->comments !!}</span>
                                               <div class="feed-links">

                                                 <span >
	                                             @if($postSubLikestextChange==0)
	                                             Like
	                                             @else
	                                             Unlike
	                                             @endif
	                                             </span>

                                                <span class="likes"><i class="fa fa-thumbs-o-up"></i> <span id="post_likes_count_{!! $getPostcomment->id !!}">{!! $postSubCommentLikesCount !!}</span></span>
                                                <span class="time">-
                                              {{ date('j F \a\t G:i', strtotime($getPostcomment->created_at)) }}
                                                </span>

                                               </div>

                                                </span>
                                             </div>

                                             @endforeach

                                             </div>

                                             {!! Form::open(['id' => 'community_post_comment_fgf_valid' ,'files'=>true,  'autocomplete'=>'off']) !!}
                                     		 {!! Form::hidden('community_group_id', $displayGroupDetails->id ,array('class'=>'form-control form-control1', 'id' => 'community_group_id')) !!}
                                             <div class="col-md-12 padding-none community_post_main_comment" post-id="{!! $getallConverseData->id !!}" data-curuser="{{$getallConverseData->username}}">
                                                 
                                             </div>
                                             {!! Form::close() !!}
                                          </div>
                                          <div class="clearfix"></div>
                                       </div>
                                    </div>
                                     @endforeach
                                 </div>
                                  
                              </div>
                           </div>

                        </div>

                        <div class="article_navigation" style="display:none;">
                           <?php
                              $pagescount = $totalcount/5+1;
                              $currenturl = Request::url();
                              for($iterator=1;$iterator<=$pagescount;$iterator++){
                           ?>
                               <a class="next" href="{{$currenturl}}?ajax=1&page=<?php echo $iterator; ?>">Next</a>
                           <?php } ?>
                        </div>
                        <div id="loader" style="display: none;" style="text-align: center; display: none;" class="ias-spinner">
                           <img src="data:image/gif;base64,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" style="display:inline">
                           <em>Loading the next set of articles...</em>
                        </div>

                         <div class="tab-content inner-block-bg jobs_hide_div" style="display:none; text-align:center">
                            <div id="feed-menu1" class="tab-pane fade in active">
                                No Jobs Found
                            </div>                             
                        </div>

                     </div>
                     <div class="col-md-12 about-group">
                        <h3>About This Group</h3>
                        <div class="accordion-group about-text group_desc">
                           <div class="accordion-heading">
                             {{--*/   $desc = substr($displayGroupDetails->description,0,250) /*--}}
                             {!! $desc !!}
                           </div>
                            <div id="collapseOne_hide" class="displayNone" >
                              {!! $desc !!}
                           </div>
                           <div id="collapseOne_show" class="displayNone">
                              <br>{!! $displayGroupDetails->description !!}
                           </div>
                            @if(strlen($displayGroupDetails->description)>250)
                           <span class="more-text about_group SeeMore">Show More</span>
                           @endif
                        </div>
                        <div class="col-md-12 padding-none">
                           <h3>
                            <span class="pull-left">Members</span>
                            <span class="pull-right mem-count">{{count($members)}} members</span>
                          </h3>
                           
                           <div class="members-list">
                              <ul>
                              @foreach($grpmemberpartners as $partner)
                                 <li>
                                    <div class="user-pic">
                                        <a href="/network/profile/{{$partner->id}}">
                                        {{--*/  $profiledetails = $commonComponent->getUserDetails($partner->id) /*--}}
                                            
                                            @if($profiledetails->lkp_role_id == 2)
                                            {{--*/ $url = "uploads/seller/$profiledetails->id/" /*--}}
                                            @else
                                            {{--*/ $url = "uploads/buyer/$profiledetails->id/" /*--}}
                                            @endif
                                             
                                            {{--*/ $getlogo = $url.$profiledetails->user_pic /*--}}
                                            {{--*/ $logo =  $commonComponent->str_replace_last( '.' , '_40_40.' , $getlogo ) /*--}}
                                                 
                                        @if(isset($partner->logo) && $partner->logo!='' && file_exists($logo))
                                        <img src="{{ asset($logo) }} ">
                                        @else
                                        <i class="fa fa-user"></i>
                                        @endif
                                    </a>
                                    </div>
                                 </li>
                                 @endforeach
                              </ul>
                           </div>
                           
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         <div class="clearfix"></div>


@include('partials.footer')
@endsection