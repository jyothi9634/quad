@extends('app')
@section('content')
@inject('commonComponent', 'App\Components\CommonComponent')
<!-- Page top navigation Starts Here-->
@include('partials.page_top_navigation')

		<div class="clearfix"></div>

		<div class="main">

			<div class="container">
			
			<h1 class="page-title">Search Results (Relocation Global Mobility)</h1><a href="#" class="change-service" data-toggle="modal" data-target="#change-service">Change Service</a>
				
				<!-- Search Block Starts Here -->

				<div class="search-block inner-block-bg">
					<div class="from-to-area">
						<span class="search-result">
							<i class="fa fa-map-marker"></i>
							<span class="location-text">{{ $request['to_location'] }}</span>
						</span>
					</div>
					<div class="date-area">
						<div class="col-md-6 padding-none">
							<p class="search-head">Date</p>
							<span class="search-result">
								<i class="fa fa-calendar-o"></i>
								{{ $request['from_date'] }}
							</span>
						</div>
						
					</div>
					
					<div>
						<p class="search-head">Servce</p>
						<span class="search-result">{{ $commonComponent->getAllGMServiceTypesById($request['relgm_service_type']) }}</span>
					</div>
					<div>
						<p class="search-head">NOS</p>
						<span class="search-result">{{ $request['measurement'] }}</span>
					</div>
					
					
					
					<div class="search-modify" data-toggle="modal" data-target="#modify-search">
						<span>Modify Search +</span>
					</div>
				</div>

				<!-- Search Block Ends Here -->



				<h2 class="side-head pull-left">Filter Results </h2>
				
				<a href="{{'/relocation/creatbuyerrpost?search=1'}}"><button class="btn post-btn pull-right">Post &amp; get Quote</button></a>

				<div class="clearfix"></div>
				
				<div class="col-md-12 padding-none">
					<div class="main-inner"> 
						
						<!-- Left Section Starts Here -->
					
						<div class="main-left">
						{!! Form::open(['url' =>'#','id' => 'buyer_results_form','class'=>'filter_form','method'=>'get']) !!}
	                        {!! Form::hidden('filter_set', 1) !!}
	                        {!! Form::hidden('to_location_id', request('to_location_id')) !!}
	                        {!! Form::hidden('to_location', request('to_location')) !!}		
	                        {!! Form::hidden('from_date', request('from_date')) !!}                		
	                        {!! Form::hidden('relgm_service_type', request('relgm_service_type')) !!}
	                        {!! Form::hidden('measurement', request('measurement')) !!}
	                        
	                        @include("partials.filter._price")
	                                
	                        {{--*/ $selectedPayment = isset($_REQUEST['selected_payments']) ? $_REQUEST['selected_payments'] : array(); /*--}}
	                        @if (Session::has('show_layered_filter')&& Session::get('show_layered_filter')!="")
								@if (Session::has('layered_filter_payments')&& Session::get('layered_filter_payments')!="")
			                        <h2 class="filter-head">Payment Mode</h2>
			                        <div class="payment-mode inner-block-bg">
		                                @foreach (Session::get('layered_filter_payments') as $paymentId => $paymentName)
			                                {{--*/ $selected = in_array($paymentId, $selectedPayment) ? 'checked="checked"' : ""; /*--}}
			                                <div class="check-box">
			                                	<input type="checkbox" class="filtercheckbox" value="{{$paymentId}}" {{$selected}} name="selected_payments[]" onClick="this.form.submit()"/>
			                                	<span class="lbl padding-8"> 
				                                    @if ($paymentName == 'Advance') 
					                                    {{--*/ $paymentType = 'Online Payment' /*--}}
				                                    @else
					                                    {{--*/ $paymentType = $paymentName /*--}}
				                                    @endif
				                                    {{$paymentType}}
			                                    </span>
			                                </div>
		                                @endforeach
			                        </div>
		                        @endif
	                        @endif

	                        {{--*/ $selectedSellers = isset($_REQUEST['selected_users']) ? $_REQUEST['selected_users'] : array(); /*--}}
	                               

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
						{!! Form::close() !!}	
						</div>
					
						<div class="main-right">
							<div class="table-div">								
								{!! $gridBuyer !!}
							</div>	
						</div>

					</div>
				</div>
				
		
                        <div class="clearfix"></div>
			<a href="{{'/relocation/creatbuyerrpost?search=1'}}"><button class="btn post-btn pull-right">Post &amp; get Quote</button></a>	
			</div>
	</div>
		
@include('partials.footer')

	<!-- Modal -->
	  <div class="modal fade" id="modify-search" role="dialog">
	    <div class="modal-dialog">	    
		
                <div class="modal-content">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <div class="modal-body">
                        <div class="col-md-12 modal-form">
                            
                            @include('buyer.relocation.home.global_mobility.search._form')	

                        </div>
                    </div>
	        </div>
	      </div>
	      
	    </div>
	  </div>
@endsection