@extends('app') @section('content')
<!-- Page top navigation Starts Here-->
@include('partials.page_top_navigation')

<div class="main">
	<div class="container">
		@if(Session::has('sumsg')) 
        <div class="flash">
		<p class="text-success col-sm-12 text-center flash-txt alert-success">
		{{ Session::get('sumsg') }}
		</p>
		</div>
		@endif
                @if(Session::has('success')) 
                <div class="flash">
                    <p class="text-success col-sm-12 text-center flash-txt alert-success">
                        {{ Session::get('success') }}
                    </p>
		</div>
		@endif
		<span class="pull-left">				
                    <h1 class="page-title">Sent Messages</h1>
		</span>
		<a href="{{ url('/messages') }}"><button class="btn post-btn pull-right flat-btn">Inbox</button></a>

        
                
            <div class="clearfix"></div>
                    {{--*/ $message_types = '0' /*--}}
                    {{--*/ $message_services = '0' /*--}}
                    {{--*/ $from_message = '' /*--}}
                    {{--*/ $to_message = '' /*--}}
                    {{--*/ $message_keywords = '' /*--}}
                    
                @if (isset($_GET['message_types']) && $_GET['message_types'] != '')
                {{--*/ $message_types = $_GET['message_types'] /*--}}
                @endif
                @if (isset($_GET['message_services']) && $_GET['message_services'] != '')
                {{--*/ $message_services = $_GET['message_services'] /*--}}
                @endif
                @if (isset($_GET['from_message']) &&  $_GET['from_message'] != '')
                {{--*/ $from_message = $_GET['from_message'] /*--}}
                @endif
                @if (isset($_GET['to_message']) && $_GET['to_message'] != '')
                {{--*/ $to_message = $_GET['to_message'] /*--}}
                @endif
                @if (isset($_GET['message_keywords']) && $_GET['message_keywords'] != '')
                {{--*/ $message_keywords = $_GET['message_keywords'] /*--}}
                @endif
			<div class="col-md-12 padding-none">
				<div class="main-inner"> 
					<div class="main-right">
						<div class="gray-bg">
							<div class="col-md-12 padding-none filter">
									{!! Form::open(array('url' => '#', 'id'
					=>'messages-search', 'class'=>'form-inline' ,'method'=>'get')) !!}
									
									<div class="col-md-3 form-control-fld">
										<div class="normal-select">
											{!! Form::select('message_types', $allMessageTypes,
                                                    $message_types, ['id' => 'message_types', 'class' => 'selectpicker'])!!}
                                                                                </div>
									</div>
									<div class="col-md-3 form-control-fld">
										<div class="normal-select">
											{!! Form::select('message_services', $allServices,
                                                    $message_services, ['id' => 'message_services', 'class' => 'selectpicker'])!!}
										</div>
									</div>
									
									<div class="col-md-3 form-control-fld">
										<div class="input-prepend">
                                                                                    <span class="add-on">
                                                                                    <i class="fa fa-calendar-o"></i>
                                                                                    </span>
											{!! Form::text('from_message', $from_message,['id' => 'from_message','class'=>'form-control calendar', 'placeholder' => 'From']) !!}
										</div>
									</div>
									<div class="col-md-3 form-control-fld">
										<div class="input-prepend">
                                                                                    <span class="add-on">
                                                                                    <i class="fa fa-calendar-o"></i>
                                                                                    </span>
											{!! Form::text('to_message', $to_message,['id' => 'to_message','class'=>'form-control calendar', 'placeholder' => 'To']) !!}
										</div>
									</div>

									<div class="col-md-3 form-control-fld">
										<div class="input-prepend">
											{!! Form::text('message_keywords', $message_keywords,['id' => 'message_keywords','class'=>'top-text-fld form-control form-control1', 'placeholder' => 'Search']) !!}
										</div>
									</div>
									<div class="col-md-3 form-control-fld">
										<div class="input-prepend">
										{!! Form::submit('Go', array( 'class'=>'btn add-btn pull-left')) !!} 
                        
                                                                                	
										</div>
									</div>
									<div class="col-md-6 form-control-fld">
										<span class="btn add-btn pull-right"><a href="#" class="new_message">New Message</a></span>
									</div>
									 {!! Form::close() !!} 
									
								</div>

						</div> <!--gray-bg -->
						
						<div class="table-div">
							<div class="table-data">
								{!! $grid !!}
							</div>
						</div>
					</div> <!-- main-right -->	


				</div> <!-- main-inner -->
			</div> <!-- 		col-md-12 padding-none -->
			
	
	</div><!-- container div -->
</div> <!-- Main div -->	


	
@include('partials.footer')
@endsection
