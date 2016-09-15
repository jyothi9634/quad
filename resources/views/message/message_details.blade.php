@inject('buyerCommonComponent', 'App\Components\BuyerComponent')
@inject('commonComponent', 'App\Components\CommonComponent')
@extends('app') @section('content')
<!-- Page top navigation Starts Here-->
@include('partials.page_top_navigation')

@if(isset($actualmessageDetails) && !empty($actualmessageDetails))
    
        {{--*/ $id = $actualmessageDetails[0]->id /*--}}
        {{--*/ $messageType = $actualmessageDetails[0]->message_type /*--}}
        {{--*/ $from_uname = $actualmessageDetails[0]->from /*--}}
        {{--*/ $to_uname = $actualmessageDetails[0]->to /*--}}
        {{--*/ $subject = $actualmessageDetails[0]->subject /*--}}
        {{--*/ $message = $actualmessageDetails[0]->message /*--}}
        {{--*/ $createdTime = $commonComponent->convertDateDisplay($actualmessageDetails[0]->created_at) /*--}}
       
        @if($actualmessageDetails[0]->sender_id!=$user_id)
        {{--*/ $sender_id= $actualmessageDetails[0]->sender_id /*--}}
        @else
        {{--*/ $sender_id= $actualmessageDetails[0]->recepient_id /*--}}
        @endif
    
@else
    {{--*/ $id = '' /*--}}
    {{--*/ $messageType = '' /*--}}
    {{--*/ $username = '' /*--}}
    {{--*/ $subject = '' /*--}}
    {{--*/ $message = '' /*--}}
    {{--*/ $createdTime = '' /*--}}
    {{--*/ $sender_id='' /*--}}
@endif
<div class="main">
    <div class="container">
        
        <?php //print_r($orderDetails); ?>
        <span class="pull-left"><h1 class="page-title">Message</h1></span>

        <!-- Content top navigation Starts Here-->
		@include('partials.content_top_navigation_links')
		<!-- Content top navigation ends Here-->
		
            <div class="clearfix"></div>
                
                <div class="col-md-12 padding-none">
                    <div class="main-inner"> 
                        <!-- Right Section Starts Here -->
                        <div class="main-right">
                            
                                @if(isset($actualmessageDetails) && !empty($actualmessageDetails))
                                <div class="inner-block-bg inner-block-bg1">
                                <div class="col-md-12 tab-modal-head">
                                    <h3>
                                        
                                        @if($subject)
                                            {!! $subject !!}
                                        @else 
                                            &nbsp; 
                                        @endif
                                        @if($actualmessageDetails[0]->lkp_message_type_id==9)
                                                @if($subject == 'Partner Request')										
                                                        {{--*/ $partners = $commonComponent->getPartnerStatus($messageDetails[0]->sender_id) /*--}}
                                                        @if($partners == 0 || $partners == 1)
                                                            <a href="javascript:void(0)" data-target="#partnerRequest" data-toggle="modal" onclick="acceptpartner({{$messageDetails[0]->sender_id}})" class="flat-btn btn red-btn pull-right btn red-btn ">ACCEPT</a>
                                                @endif
					@endif
                                            @if($subject == 'Partner Request' || $subject == 'Recomendation')
                                                <a href="/network/profile/{{$sender_id}}" class="btn add-btn pull-right btn add-btn flat-btn">Visit Profile</a>
                                            @endif
										@else
                                        	<a href="#" class="new_message btn red-btn pull-right margin" data-userid="{{$sender_id}}" data-subject="{{$subject}}" data-msgid="{{$id}}">Reply</a>
										@endif


											
                                        <div class="clearfix"></div>
                                    </h3>
                                </div>
                                <div class="clearfix"></div>
                                <div class="col-md-12">
                                    <div class="col-md-9 padding-none">
                                        <div class="col-md-4 padding-left-none data-fld">
                                            <span class="data-head">Date</span>
                                            <span class="data-value">
                                                @if(isset($createdTime))
                                                {{$createdTime}}
                                                @else 
                                                &nbsp;
                                                @endif
                                            </span>
                                        </div>
                                        <div class="col-md-4 padding-left-none data-fld">
                                            <span class="data-head">From</span>
                                            <span class="data-value">
                                                @if($from_uname)
                                                {!! $from_uname !!}
                                                @else &nbsp;
                                                    @endif
                                            </span>
                                        </div>
                                        <div class="col-md-4 padding-left-none data-fld">
                                            <span class="data-head">To</span>
                                            <span class="data-value">
                                                @if($to_uname)
                                                {!! $to_uname !!}
                                                @else &nbsp;
                                                    @endif
                                            </span>
                                        </div>
    									
                                        <div class="clearfix"></div>

                                        <div class="col-md-12 padding-left-none data-fld">
                                            <span class="data-head">Message Body</span>
                                            <span class="data-value">
                                                @if($message)
                                                {!! $message !!}
                                                @else &nbsp;
                                                    @endif
                                            </span>
                                        </div>
                                    </div>


                                </div>
                                <div class="col-md-4 text-right">
                                    
                                </div>
                                </div>

                                <div class="inner-block-bg inner-block-bg1">
                                <div class="col-md-12 margin-bottom">
                                    <div>
                                        <h4 class="data-head">Conversations</h4>
                                    </div>
                                    @foreach ($messageDetails as $data)
                                    
                                    <div class="accordian-blocks">
                                    <div class="data-head inner-block-bg inner-block-bg1 detail-head">{!! $data->from !!} - {!! $commonComponent->convertDateDisplay($data->created_at) !!}</div>

                                    <div class="data-value detail-data padding-top">
                                        <div class="col-md-12 form-control-fld">
                                            <p class="form-control-fld">{!! $data->message !!}</p>
                                            {{--*/  $uploads=$commonComponent->getMessageAttachments($data->id); /*--}}
                                            @if(!empty($uploads))
                                            <div class="col-md-12 padding-none data-fld">
                                                <span class="data-head">Attachments</span>
                                                
                                                <?php //print_r($uploads);exit;?>
                                                @foreach($uploads as $upload)
                                                <span class="data-value">
                                                    <!--a href="/getfiledownload/{!! $data->id !!}" >{!! $data->name !!}</a-->
                                                    <a target="blank" href="{{url($upload->filepath)}}" class="form-group pull-left overflow-hide">
							{{$upload->name}}
                                                    </a>
                                                </span><div class="clearfix"></div>
                                                @endforeach
                                            </div>
                                            @endif
                                            <div class="clearfix"></div>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                    </div>
                                    @endforeach
                                    
                                </div>
                                </div>
                                <div class="clearfix"></div>
                                @else
                                <div class="inner-block-bg inner-block-bg1 text-center">
                                    No records found.
                                </div>
                                @endif

                           

                        </div>
                        <!-- Right Section Ends Here -->

                    </div>
                </div>

                <div class="clearfix"></div> 
          
    </div> <!-- Container -->
</div> <!-- Main -->
@include('partials.footer')
@endsection
