@if(count($feedComments) > 0)
<?php 
   $feedComments = json_decode(json_encode($feedComments),TRUE);
   $comments = array_reverse($feedComments, true);
?>
   @foreach ($comments as $comm)
      {{--*/ $comm = (object)$comm /*--}}
   <div class="col-md-12 padding-none form-control-fld">
      <div class="user-pic pull-left">
         @if($comm->lkp_role_id == 2)
            {{--*/ $url = "uploads/seller/$comm->userid/" /*--}}
         @else
            {{--*/ $url = "uploads/buyer/$comm->userid/" /*--}}
         @endif
         {{--*/ $getprofile = $url.$comm->user_pic /*--}}
         {{--*/ $userpic =  $CComponent::str_replace_last( '.' , '_40_40.' , $getprofile ) /*--}}
     
        <a href="{{ URL::to('network/profile',$comm->comment_user_id)}}">
         @if(file_exists($getprofile))
            <img src="{{url($userpic)}}"/>
         @else
          <i class="fa fa-user"></i>
         @endif
        </a>
      </div>
      
      <span class="user-name pull-left">
         <strong><a href="{{ URL::to('network/profile',$comm->comment_user_id)}}">{{ucwords($comm->username)}}</a> : </strong>
         <span id="commentid_{{$comm->id}}">{{ $comm->comments }}</span>
      </span>

      <div class="comment-details pull-right">
         @if($feedInfo->created_by == Auth::User()->id || $comm->created_by == Auth::User()->id )
         <a href="javascript:void(0)" data-target="#editpostcomment" data-toggle="modal" onclick="editcomment({{$feedInfo->id}},{{$comm->id}},{{$comm->created_by}})" ><i class="fa fa-edit" title="Edit"></i></a>&nbsp;&nbsp;
         <a href="javascript:void(0)" data-target="#cancelpostcomment" data-toggle="modal" onclick="cancelcomment({{$comm->id}},{{$comm->created_by}})" ><i class="fa fa-trash" title="Delete"></i></a>&nbsp;&nbsp;
         @elseif($feedInfo->created_by == Auth::User()->id && $comm->created_by != Auth::User()->id )  
            <a href="javascript:void(0)" data-target="#cancelpostcomment" data-toggle="modal" onclick="cancelcomment({{$comm->id}},{{$comm->created_by}})" ><i class="fa fa-trash" title="Delete"></i></a>&nbsp;&nbsp;
         @endif
         {{ date('d/m/Y',strtotime($comm->created_at))}}
      </div>
         
   </div>
   @endforeach
@endif 