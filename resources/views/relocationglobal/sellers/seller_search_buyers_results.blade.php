@extends('app')
@section('content')
@inject('commonComponent', 'App\Components\CommonComponent')
<!-- Page top navigation Starts Here-->
@include('partials.page_top_navigation')


		<div class="clearfix"></div>

		<div class="main">

			<div class="container">
				<h1 class="page-title">Search Results (Relocation Global Mobility)</h1><a class="change-service" data-toggle="modal" data-target="#change-service">Change Service</a>
				
				<!-- Search Block Starts Here -->

				<div class="search-block inner-block-bg">
					<div class="from-to-area">
						<span class="search-result">
							<i class="fa fa-map-marker"></i>
							<span class="location-text"> {{$request['to_location']}}</span>
						</span>
					</div>
					<div class="date-area">
						<div class="col-md-6 padding-none">
							<p class="search-head">From Date</p>
							<span class="search-result">
								<i class="fa fa-calendar-o"></i>
								{{$request['valid_from']}}
							</span>
						</div>
						<div class="col-md-6 padding-none">
							<p class="search-head">To Date</p>
							<span class="search-result">
								<i class="fa fa-calendar-o"></i>								
                                                                @if(isset($request['valid_to']) && $request['valid_to']!='')
								{{ $request['valid_to'] }}
                                                                @else
                                                                NA
                                                                @endif
							</span>
						</div>
					</div>
					<div>
						<p class="search-head">Service</p>
						<span class="search-result">{{ $commonComponent->getAllGMServiceTypesById($request['relgm_service_type']) }}
						</span>
					</div>
                                        

					<div class="search-modify" data-toggle="modal" data-target="#modify-search">
						<span>Modify Search +</span>
					</div>
				</div>

				<!-- Search Block Ends Here -->



				<h2 class="side-head pull-left">Filter Results </h2>
				<div class="page-results pull-left col-md-2 padding-none">
					<div class="form-control-fld">
						<div class="normal-select">
							<select class="selectpicker">
								<option value="0">10 Records Per page</option>
							</select>
						</div>
					</div>
				</div>
				<a onclick="return checkSession(19,'/relocation/createsellerpost');" href="#"><button class="btn post-btn pull-right">+ Post</button></a>

				<div class="clearfix"></div>
				
				<div class="col-md-12 padding-none">
					<div class="main-inner"> 
						
						<!-- Left Section Starts Here -->

						<div class="main-left">
							
						{!! Form::open(['url' =>'#','id' => 'buyer_results_form','class'=>'filter_form','method'=>'get']) !!}
						{!! Form::hidden('filter_set', 1) !!}
                                                {!! Form::hidden('to_location_id', Session::get('session_to_location_id_relocation')) !!}
                                                {!! Form::hidden('seller_district_id', Session::get('session_seller_district_id_relocation')) !!}
                                                {!! Form::hidden('to_location', Session::get('session_to_location_relocation')) !!}		
                                                {!! Form::hidden('valid_from', Session::get('session_valid_from_relocation')) !!} 
                                                {!! Form::hidden('valid_to', Session::get('session_valid_to_relocation')) !!} 
                                                {!! Form::hidden('relgm_service_type', Session::get('session_service_type_relocation')) !!}
                                                {!! Form::hidden('spot_or_term',1,array('class'=>'form-control')) !!}
							<h2 class="filter-head">Form Filter</h2>
							<div class="seller-list inner-block-bg">
                                                            <?php	$selectedServices = isset($_REQUEST['selected_services']) ? $_REQUEST['selected_services'] : array();
								?>
								<div class="form-control-fld margin-top">
									<div class="input-prepend multi_select">
										<span class="add-on"><i class="fa fa-archive"></i></span>
										<select class="m_select" name="selected_services[]" onChange="this.form.submit()" multiple>
											<option>Services</option>
                                                                                        @if (Session::has('show_layered_filter')&& Session::get('show_layered_filter')!="")
                                                                                            @if (Session::has('layered_services_filter')&& Session::get('layered_services_filter')!="")
                                                                                                @foreach (Session::get('layered_services_filter') as $Id => $serviceName)
                                                                                                <?php $selected = in_array($Id, $selectedServices) ? 'selected="selected"' : ""; ?>
                                                                                                <option value='{{$Id}}' {{$selected}}>{{$serviceName}}</option>
                                                                                                @endforeach
                                                                                            @endif
                                                                                        @endif
										</select>
									</div>
								</div>
								
                                                    <?php	$selectedSellers = isset($_REQUEST['selected_users']) ? $_REQUEST['selected_users'] : array();
								?>
								
								@if (Session::has('show_layered_filter')&& Session::get('show_layered_filter')!="")
									@if (Session::has('layered_filter')&& Session::get('layered_filter')!="")
										<h2 class="filter-head"><?php echo (Auth::user()->lkp_role_id == 1) ? "Sellers" : "Buyers"; ?> List</h2>
										<div class="seller-list inner-block-bg">
											@foreach (Session::get('layered_filter') as $userId => $userName)
												<?php $selected = in_array($userId, $selectedSellers) ? 'checked="checked"' : ""; ?>
												<div class="check-box"><input type="checkbox" class="checkbox pull-left filtercheckbox" value="{{$userId}}" {{$selected}} name="selected_users[]" onChange="this.form.submit()"><span class="lbl padding-8">{{ $userName }}</span></div>
												<div class="col-xs-12 padding-none"> </div>
											@endforeach
										</div>
									@endif
								@endif
						
							</div>				
							
						{!! Form::close() !!}
							
						</div>

						<!-- Left Section Ends Here -->


						<!-- Right Section Starts Here -->

						<div class="main-right">
							<!-- Table Starts Here -->
							<div class="table-div">	
							<input type="hidden" id="from_search_page" name="from_search_page" value="1">							
								{!! $gridBuyer !!}
							</div>	
						</div>

						<!-- Right Section Ends Here -->

					</div>
				</div>

				<div class="clearfix"></div>
				<div class="clearfix"></div>
				<a onclick="return checkSession(19,'/relocation/createsellerpost');" href="#"><button class="btn post-btn pull-right">+ Post</button></a>
			</div>
		</div>

@include('partials.footer')
<div class="modal fade" id="modify-search" role="dialog">
	    <div class="modal-dialog">
	    
	      <!-- Modal content-->
	      <div class="modal-content">
	        <button type="button" class="close" data-dismiss="modal">&times;</button>
	        <div class="modal-body">
	          <div class="col-md-12 modal-form">
				

		@include('relocationglobal.sellers._searchform')
				

				</div>
			</div>
	        </div>
	      </div>
	      
	    </div>
	  </div>
	  @endsection
          
<script type="text/javascript">
		$(document).ready(function(){
			$('.m_select').multiselect({
                enableClickableOptGroups: true,
                nonSelectedText: 'Services',
            });
		});
	</script>          