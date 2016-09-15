@inject('commonComponent', 'App\Components\CommonComponent')
@inject('communityComponent', 'App\Components\community\CommunityComponent')
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
                                            {{--*/  $profiledetails = $commonComponent->getUserDetails($getallConverseData->created_by) /*--}}
                                            @if($profiledetails->lkp_role_id == 2)
                                            {{--*/ $url = "uploads/seller/$profiledetails->id/" /*--}}
                                            @else
                                            {{--*/ $url = "uploads/buyer/$profiledetails->id/" /*--}}
                                            @endif
                                            
                                            {{--*/ $getlogo = $url.$profiledetails->user_pic /*--}}
                                            {{--*/ $logo =  $commonComponent->str_replace_last( '.' , '_40_40.' , $getlogo ) /*--}}
                                            
                                            @if($profiledetails->user_pic!='' && file_exists($logo))
                                             <div class="user-pic pull-left">
                                            	 <img class="img-responsive" src="{{ asset($logo) }}">
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
                                          <h2><br></h2>
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
                                             <span id="post_like_{!! $getallConverseData->id !!}" class="post_likes change_likes_text" like-id="{!! $getallConverseData->id !!}">
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
                                                <div class="pull-right gray">
                                                 {!! $getPostCreationDate !!}
                                                 @if($getPostcomment->user_id==Auth::id())
                                                 <a id="edit_comment_text_{{ $getPostcomment->id }}" edit-id="{{ $getPostcomment->id }}" edit_tect_val="{!! $getallConverseData->id !!}" class="edit_cmnt_text"><i class="fa fa-edit red" title="Edit"></i></a>
                                                 <a id="post_cmnt_delete_{{ $getPostcomment->id }}" class="delete_post_comment" del-id="{{ $getPostcomment->id }}" data-target='#deletepostcomment' data-toggle='modal'><i class="fa fa-trash red" title="Delete"></i></a>
                                                @elseif($getallConverseData->created_by == Auth::id())
                                                <a id="post_cmnt_delete_{{ $getPostcomment->id }}" class="delete_post_comment" del-id="{{ $getPostcomment->id }}" data-target='#deletepostcomment' data-toggle='modal'><i class="fa fa-trash red" title="Delete"></i></a>
                                                 @endif
                                                </div>
                                                 
                                            {{--*/  $profiledetails = $commonComponent->getUserDetails($getPostcomment->created_by) /*--}}
                                            @if($profiledetails->lkp_role_id == 2)
                                            {{--*/ $url = "uploads/seller/$profiledetails->id/" /*--}}
                                            @else
                                            {{--*/ $url = "uploads/buyer/$profiledetails->id/" /*--}}
                                            @endif
                                            
                                            {{--*/ $getlogo = $url.$profiledetails->user_pic /*--}}
                                            {{--*/ $logo =  $commonComponent->str_replace_last( '.' , '_40_40.' , $getlogo ) /*--}}
                                            
                                            @if($profiledetails->user_pic!='' && file_exists($logo))
                                             <div class="user-pic pull-left">
                                            	 <img class="img-responsive" src="{{ asset($logo) }}">
                                             </div>
	                                        @else
                                            <div class="user-pic pull-left">
                                                  <i class="fa fa-user"></i>
                                            </div>
                                           @endif
                                                 
                                                               

                                                <span class="user-name pull-left">
                                                   <strong>{!! $getPostcomment->username !!} </strong>
                                                   <span id="update_cmnt_{{ $getPostcomment->id }}">{!! $getPostcomment->comments !!}</span>
                                               <div class="feed-links">

                                                 <span id="post_like_{!! $getPostcomment->id !!}" class="post_likes change_likes_text" like-id="{!! $getPostcomment->id !!}">
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
                                                 <input type="text" class="form-control form-control1 update_txt_cmnt_desc" placeholder="Add a comment" id="community_post_comment_{!! $getallConverseData->id !!}" name="community_post_comment_{!! $getallConverseData->id !!}" required maxlength="200">
                                             </div>
                                             {!! Form::close() !!}
                                          </div>
                                          <div class="clearfix"></div>
                                       </div>
                                    </div>
                                     @endforeach