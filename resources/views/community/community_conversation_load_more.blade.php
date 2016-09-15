@inject('commonComponent', 'App\Components\CommonComponent')
@inject('communityComponent', 'App\Components\community\CommunityComponent')

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
     <a id="edit_comment_text_{{ $getPostcomment->id }}" edit-id="{{ $getPostcomment->id }}" edit_tect_val="{!! $getPostcomment->id !!}" class="edit_cmnt_text"><i class="fa fa-edit red" title="Edit"></i></a>
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

        @if($logo!='' && file_exists($logo))
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

