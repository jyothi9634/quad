@foreach ($networkFeeds as $feed)

    {{-- Single Feed Block --}}
    <div class="clsFeedSingleBlk">
      <div class="col-md-1 padding-none">
         <div class="profile-pic">
            @if($feed->lkp_role_id == 2)
            {{--*/ $url = "uploads/seller/$feed->userid/" /*--}}
            @else
            {{--*/ $url = "uploads/buyer/$feed->userid/" /*--}}
            @endif
            {{--*/ $getuserpic = $url.$feed->user_pic /*--}}
            {{--*/ $userpic =  $CComponent::str_replace_last( '.' , '_94_92.' , $getuserpic ) /*--}}
            @if(file_exists($getuserpic))
            <img src="{{url($userpic)}}"/>
            @else
            <i class="fa fa-user"></i>
            @endif
         </div>
      </div>
      <div class="col-md-11 padding-right-none">
         <div class="col-md-12 feed-info">
            <span class="user-name">
            {{-- ********************* Shared feed ********************* --}}
               @if($feed->feed_type == 'share')
               <strong>Shared by <a href="{{ URL::to('network/profile',$feed->user_id)}}">{{ucwords($feed->username)}}</a></strong> : {{$feed->feed_description}}<br>
               @elseif($feed->feed_type == 'recomend')
                  <strong>{!! $feed->feed_title !!}</strong><br>
               @endif
               @if($feed->feed_type != 'recomend')
                  <strong><a href="{{ URL::to('network/profile',$feed->user_id) }}">{{ucwords($feed->username)}}</a></strong><br>
               @endif 
            </span>
            
            @if($feed->feed_type == 'share')
               <p>{{ucfirst($feed->share_feed_title)}} <br />
               <?php
               $sanitized = htmlspecialchars($feed->share_feed_description, ENT_QUOTES);
					 echo str_replace(array("\r\n", "\n"), array("<br />", "<br />"), $sanitized);
               ?>
               </p>
            @else
               <p><strong>{{ucfirst($feed->feed_title)}}</strong></p>
               <p><?php
                 $sanitized = htmlspecialchars($feed->feed_description, ENT_QUOTES);
					  echo str_replace(array("\r\n", "\n"), array("<br />", "<br />"), $sanitized);
               ?></p>
            @endif
            <div class="feed-links" id="feedLinksBlk{{$feed->id}}" data-id="{{ ($feed->feed_type == 'share')? $feed->share_feed_id:$feed->id }}">
               {{--*/ $feedComCnt = $CComponent::feedComents($feed->id, 'count') /*--}}
               {{--*/ $fdLikes = $CComponent::feedLikes($feed->id, 'count') /*--}}
               {{--*/ $fdLikeCheck = $CComponent::feedLikes($feed->id, 'check') /*--}}
               <span class="clsLike" data-url="{{ URL::to('network/ajxfeedlike') }}">{{ $fdLikeCheck? 'Unlike':'Like' }}</span> 
               <span class="clsShare" data-url="{{ URL::to('network/ajxsharefeed') }}" data-toggle="modal" data-target="#popupfeedShare">Share</span> 
               <span class="clsComments">Comment</span>
               <span class="likes" data-likecnt="{{$fdLikes}}">
                  <i class="fa fa-thumbs-o-up"></i> <span>{{$fdLikes}}</span>
               </span>
               <span class="comments" data-comcnt="{{$feedComCnt}}">
                  <i class="fa fa-comment-o"></i> <span>{{$feedComCnt}}</span>
               </span>
            </div>
         </div>
         
         {{-- Comments Section: Start --}}
         <div class="col-md-12 feed-info comments-block" id="feedComBlk{{$feed->id}}">
            {{--*/ $feedComments = $CComponent::feedComents($feed->id) /*--}}
            {{--*/ $feedCommentsCnt = $CComponent::feedComents($feed->id, 'count') /*--}}
            
            @if($feedCommentsCnt > 5)   
               <a data-url="{{ URL::to('network/ajxloadcomm')}}" data-pageno="1" data-feedid="{{$feed->id}}" href="javascript:void(0)" id="loadMoreComments{{$feed->id}}" class="load_more_comments">View previous comments</a>
            @endif
            
            <div id="ajxLoadFeedCom{{$feed->id}}">

            @if(count($feedComments) > 0)   
               @foreach ($feedComments as $comm)
               <div class="col-md-12 padding-none form-control-fld">
                  <div class="user-pic pull-left">
                     <a href="{{ URL::to('network/profile',$comm->comment_user_id)}}">
                     <i class="fa fa-user"></i>
                     </a>
                  </div>
                  
                  <span class="user-name pull-left">
                     <strong><a href="{{ URL::to('network/profile',$comm->comment_user_id)}}">{{ucwords($comm->username)}}</a> : </strong>
                     <span id="commentid_{{$comm->id}}">{{ $comm->comments }}</span>
                  </span>

                  <div class="pull-right">
                     @if($feed->created_by == Auth::User()->id || $comm->created_by == Auth::User()->id )
                     <a href="javascript:void(0)" data-target="#editpostcomment" data-toggle="modal" onclick="editcomment({{$feed->id}},{{$comm->id}},{{$comm->created_by}})" ><i class="fa fa-edit" title="Edit"></i></a>&nbsp;&nbsp;
                     <a href="javascript:void(0)" data-target="#cancelpostcomment" data-toggle="modal" onclick="cancelcomment({{$comm->id}},{{$comm->created_by}})" ><i class="fa fa-trash" title="Delete"></i></a>&nbsp;&nbsp;
                     @elseif($feed->created_by == Auth::User()->id && $comm->created_by != Auth::User()->id )  
                        <a href="javascript:void(0)" data-target="#cancelpostcomment" data-toggle="modal" onclick="cancelcomment({{$comm->id}},{{$comm->created_by}})" ><i class="fa fa-trash" title="Delete"></i></a>&nbsp;&nbsp;
                     @endif
                     {{ date('d/m/Y',strtotime($comm->created_at))}}
                  </div>
                     
               </div>
               @endforeach
            @endif   
            </div>
            <div class="col-md-12 padding-none" >
               <input type="text" placeholder="Add a comment" class="form-control form-control1" name="txtFeedComment" id="txtFeedComment{{ $feed->id }}" class="clsFeedComment" data-feedid="{{ $feed->id }}" data-curuser="{{ Auth::User()->username}}" data-url="{{ URL::to('network/ajxpostcomment') }}" maxlength="200"><label id="lblErrComment{{ $feed->id }}" class="error"></label>
            </div>
         </div>
         {{-- Comments Section: End --}}
         
      </div>
    </div>
      
@endforeach