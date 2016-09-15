@extends('app')
@section('content')
@include('partials.page_top_navigation')

	<div class="main">
		@if(Session::has('message_create_post_ptl') && Session::get('message_create_post_ptl')!='')
			<div class="flash">
				<p class="text-success col-sm-12 text-center flash-txt alert-success">
					{{ Session::get('message_create_post_ptl') }}
				</p>
			</div>
		@endif
                @if(Session::has('success') && Session::get('success')!='')
			<div class="flash">
				<p class="text-success col-sm-12 text-center flash-txt alert-success">
					{{ Session::get('success') }}
				</p>
			</div>
		@endif

		@if(Session::has('message_update_post') && Session::get('message_update_post')!='')
			<div class="flash">
				<p class="text-success col-sm-12 text-center flash-txt alert-success">
					{{ Session::get('message_update_post') }}
				</p>
			</div>
		@endif
			<div class="container">
				<span class="pull-left"><h1 class="page-title">Posts (Relocation Global Mobility)</h1><a class="change-service" data-toggle="modal" data-target="#change-service">Change Service</a></span>
					<a onclick="return checkSession(19,'/relocation/createsellerpost');" href="#"><button class="btn post-btn pull-right">+ Post</button></a>
				<div class="clearfix"></div>				
				<div class="col-md-12 padding-none">
					<div class="main-inner"> 	
						<!-- Right Section Starts Here -->
						<div class="main-right">
							<div class="gray-bg">
							{!! Form::open(array('url' => 'sellerlist','method'=>'get')) !!}
								<div class="col-md-12 padding-none filter">
										<div class="col-md-3 form-control-fld">
											<div class="normal-select">
												<select class="selectpicker" name="post_type" id="post_type">													
													<option value="1" <?php if (isset($_REQUEST['post_type']) && $_REQUEST['post_type']=="1") echo ' selected';?>>My Posts</option>
													<option value="2" <?php if (isset($_REQUEST['post_type']) && $_REQUEST['post_type']=="2") echo ' selected';?>>Market leads</option>													
												</select>
											</div>
										</div>			
										
										@if(isset($_REQUEST['post_type']) && $_REQUEST['post_type']=="2")
										<div class="col-md-3 form-control-fld">
											<div class="normal-select">
												<select class="selectpicker" name="service_type" id="service_type">	
													<option value="2" <?php if (isset($_REQUEST['service_type']) && $_REQUEST['service_type']=="2") echo ' selected';?>>Term</option>												
													<option value="1" <?php if (isset($_REQUEST['service_type']) && $_REQUEST['service_type']=="1") echo ' selected';?>>Spot</option>
																										
												</select>
											</div>
										</div>		
										@endif	
											
										<div class="col-md-3 form-control-fld">
											<div class="normal-select">
											{{--*/   $post_status = $statusSelected /*--}}
                                            <select name="status" id="status" class="selectpicker">
                                                <option value="0" {{ ($post_status==0)? 'selected="selected"':'' }}>Status (All)</option>
                                            @foreach($posts_status_list as $key => $st)
                                                <?php 
                                                if($key == 4) continue;                                                
                                                if($typeSelected==2 && $key==1):
                                                    continue;
                                                endif;
                                                ?>
                                                @if(request('status_id') == $key || $post_status == $key)
                                                <option value="{{$key}}" selected="selected">{{$st}}</option>
                                                @elseif($post_status == '')
                                                <option value="{{$key}}" selected="selected">{{$st}}</option>
                                                @else
                                                <option value="{{$key}}">{{$st}}</option>
                                                @endif  
                                            @endforeach
                                            </select>
                                            
											</div>
											</div>
										
															
										<div class="col-md-3 form-control-fld pull-right">											
											{!! Form::submit(' GO ', array( 'class'=>'btn add-btn pull-right')) !!}											
										</div>
									</div>
									{!! Form :: close() !!}
							</div>
							
							
					 		@if(isset($_REQUEST['post_type']) && $_REQUEST['post_type'] == 2)
							<input id="from_search_page" value="1" type="hidden"/>
                                                        <div class="gray-bg">
							@if (isset($_REQUEST['service_type']) && $_REQUEST['service_type']=="1")
							
							{!! $filter->open !!}
					 		{!! $filter->field('src') !!} 		
								<div class="col-md-12 padding-none filter">										
                                                                    <div class="col-md-3 form-control-fld">
                                                                            <div class="input-prepend">
                                                                                    <span class="add-on"><i class="fa fa-map-marker"></i></span>
                                                                                    {!!	$filter->field('rbp.location_id') !!}
                                                                            </div>
                                                                    </div>
                                                                    <div class="col-md-3 form-control-fld">
                                                                            <div class="input-prepend">
                                                                                    <span class="add-on"><i class="fa fa-map-marker"></i></span>
                                                                                    {!!	$filter->field('rbqi.lkp_gm_service_id') !!}
                                                                            </div>
                                                                    </div>
										
										
								   <div class="col-md-3 form-control-fld">
                                                                        <div class="input-prepend">
                                                                            <span class="add-on"><i class="fa fa-calendar-o"></i></span>
                                                                            @if(isset($_GET['from_date']))
                                                                            {!! Form::text('from_date', $_GET['from_date'],['id' => 'start_dispatch_date','class'=>'form-control dateRange', 'placeholder' => 'From Date']) !!}
                                                                            @else
                                                                            {!! Form::text('from_date', '',['id' => 'start_dispatch_date','class'=>'form-control dateRange', 'placeholder' => 'From Date']) !!}
                                                                            @endif
                                                                            </div>
                                                                    </div>
                                                                    <div class="col-md-3 form-control-fld">
                                                                        <div class="input-prepend">
                                                                            <span class="add-on"><i class="fa fa-calendar-o"></i></span>
                                                                            @if(isset($_GET['to_date']))
                                                                            {!! Form::text('to_date', $_GET['to_date'],['id' => 'end_dispatch_date','class'=>'form-control dateRange dateRangeTo', 'placeholder' => 'To Date']) !!}
                                                                            @else
                                                                            {!! Form::text('to_date', '',['id' => 'end_dispatch_date','class'=>'form-control dateRange dateRangeTo', 'placeholder' => 'To Date']) !!}
                                                                            @endif
                                                                        </div>
                                                                    </div>
																		
									</div>
									{!! Form::hidden('post_type', 2 , array('id' => 'post_type')) !!}
                                     <input type="hidden" name="status"  value="<?php if(isset ( $_REQUEST ['status'] )) echo  $_REQUEST['status']; ?>">
									{!! $filter->close !!}
							
							@else
							 {!! $filter->open !!}
					 		{!! $filter->field('src') !!} 	
							<div class="col-md-3 form-control-fld">
                                                            <div class="input-prepend">
                                                                    <span class="add-on">
                                                                            <i class="fa fa-map-marker"></i>
                                                                    </span>
                                                                    {!! $filter->field('bqit.from_location_id') !!}
                                                            </div>
							</div>
								
								
                                                        <div class="col-md-3 form-control-fld">
                                                            <div class="input-prepend">
                                                                    <span class="add-on">
                                                                            <i class="fa fa-calendar-o"></i>
                                                                    </span>
                                                                    @if(isset($_GET['from_date']))
                                                                    {!! Form::text('from_date', $_GET['from_date'],['id' => 'start_dispatch_date','class'=>'dateRange dateRangeFrom form-control', 'placeholder' => 'From Date']) !!}
                                                                    @else
                                                                    {!! Form::text('from_date', '',['id' => 'start_dispatch_date','class'=>'dateRange dateRangeFrom form-control', 'placeholder' => 'From Date']) !!}
                                                                    @endif

                                                            </div>
                                                        </div>
                                                        <div class="col-md-3 form-control-fld">
                                                                <div class="input-prepend">
                                                                        <span class="add-on">
                                                                                <i class="fa fa-calendar-o"></i>
                                                                        </span>
                                                                        @if(isset($_GET['to_date']))
                                                                                {!! Form::text('to_date', $_GET['to_date'],['id' => 'end_dispatch_date','class'=>'form-control dateRange dateRangeTo', 'placeholder' => 'To Date']) !!}
                                                                        @else
                                                                                {!! Form::text('to_date', '',['id' => 'end_dispatch_date','class'=>'form-control dateRange dateRangeTo', 'placeholder' => 'To Date']) !!}
                                                                        @endif
                                                                </div>
                                                        </div>
                                                        <input type="hidden" name="post_type" id="post_type" value="2">
                                                        <input type="hidden" name="service_type" id="service_type" value="2">
                                                         <input type="hidden" name="status"  value="<?php if(isset ( $_REQUEST ['status'] )) echo  $_REQUEST['status']; ?>">
                                                        {!! $filter->close !!} 
							@endif
							</div>
							@else							
							<div class="gray-bg">
							{!! $filter->open !!}
					 		{!! $filter->field('src') !!} 	
								<div class="col-md-12 padding-none filter">										
                                                                    <div class="col-md-3 form-control-fld">
                                                                            <div class="input-prepend">
                                                                                    <span class="add-on"><i class="fa fa-map-marker"></i></span>
                                                                                    {!!	$filter->field('rsp.location_id') !!}
                                                                            </div>
                                                                    </div>

                                                                    <div class="col-md-3 form-control-fld">
                                                                            <div class="input-prepend">
                                                                                <span class="add-on"><i class="fa fa-calendar-o"></i></span>
                                                                                @if(isset($_GET['from_date']))
                                                                                {!! Form::text('from_date', $_GET['from_date'],['id' => 'start_dispatch_date','class'=>'form-control dateRange', 'placeholder' => 'From Date']) !!}
                                                                                @else
                                                                                {!! Form::text('from_date', '',['id' => 'start_dispatch_date','class'=>'form-control dateRange', 'placeholder' => 'From Date']) !!}
                                                                                @endif
                                                                            </div>
                                                                    </div>
                                                                    <div class="col-md-3 form-control-fld">
                                                                            <div class="input-prepend">
                                                                                <span class="add-on"><i class="fa fa-calendar-o"></i></span>
                                                                                @if(isset($_GET['to_date']))
                                                                                {!! Form::text('to_date', $_GET['to_date'],['id' => 'end_dispatch_date','class'=>'form-control dateRange dateRangeTo', 'placeholder' => 'To Date']) !!}
                                                                                @else
                                                                                {!! Form::text('to_date', '',['id' => 'end_dispatch_date','class'=>'form-control dateRange dateRangeTo', 'placeholder' => 'To Date']) !!}
                                                                                @endif
                                                                            </div>
                                                                    </div>
																		
                                                                         <input type="hidden" name="status"  value="<?php if(isset ( $_REQUEST ['status'] )) echo  $_REQUEST['status']; ?>">		
							</div>
							{!! $filter->close !!} 
							</div>
							@endif
							

							<!-- Table Starts Here -->					
							<div class="table-div">
							{!! $grid !!}
							</div>	
							<!-- Table Starts Here -->

						</div>

						<!-- Right Section Ends Here -->

					</div>
				</div>

				<div class="clearfix"></div>

			</div>
		</div>

@include('partials.footer')
@endsection
