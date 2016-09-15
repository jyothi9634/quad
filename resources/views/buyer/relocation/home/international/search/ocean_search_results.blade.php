@extends('app')
@section('content')
@inject('commonComponent', 'App\Components\CommonComponent')
@inject('sellerComponent', 'App\Components\RelocationInt\AirInt\RelocationAirSellerComponent')
<!-- Page top navigation Starts Here-->
@include('partials.page_top_navigation')
<div class="clearfix"></div>

<div class="main">
	<div class="container">
		
		<h1 class="page-title">Search Results (Relocation Int)</h1><a href="#" class="change-service" data-toggle="modal" data-target="#change-service">Change Service</a>
		
		<!-- Search Block Starts Here -->
		<?php 
		$from_city_name = $commonComponent->getCityName(request('from_location_id_intre'));
		$to_city_name = $commonComponent->getCityName(request('to_location_id_intre'));
		?>
		<div class="search-block inner-block-bg">
			<div class="from-to-area">
				<span class="search-result">
					<i class="fa fa-map-marker"></i>
					<span class="location-text">{{ $from_city_name }} to {{ $to_city_name }}</span>
				</span>
			</div>
			<div class="date-area">
				<div class="col-md-6 padding-none">
					<p class="search-head">Dispatch Date</p>
					<span class="search-result">
						<i class="fa fa-calendar-o"></i>
						{{ request('valid_from') }}
					</span>
				</div>
				<div class="col-md-6 padding-none">
					<p class="search-head">Delivery Date</p>
					<span class="search-result">
						<i class="fa fa-calendar-o"></i>
						{{ request('valid_to') }}
					</span>
				</div>
			</div>
			<div>
				<p class="search-head">Property Type</p>
				<span class="search-result">{{ $commonComponent->getPropertyType(request('property_type')) }}</span>
			</div>
			<div>
				<p class="search-head">Type</p>
				<span class="search-result">{{ (request('post_type')==2)? 'Ocean':'N/A'}}</span>
			</div>

			<div class="search-modify" data-toggle="modal" data-target="#modify-search">
				<span>Modify Search +</span>
			</div>
		</div>

		<!-- Search Block Ends Here -->



		<h2 class="side-head pull-left">Filter Results</h2>
		<div class="page-results pull-left col-md-2 padding-none">
			<div class="form-control-fld">
				<div class="normal-select">
					<select class="selectpicker">
						<option value="0">10 Records Per page</option>
					</select>
				</div>
			</div>
		</div>
		
		<a href="{{'/relocation/creatbuyerrpost?search=1'}}"><button class="btn post-btn pull-right">Post &amp; get Quote</button></a>

		<div class="clearfix"></div>
		
		<div class="col-md-12 padding-none">
			<div class="main-inner"> 
				
				<!-- Left Section Starts Here -->

				<div class="main-left">
					{!! Form::open(['url' =>'#','id' => 'buyer_results_form','class'=>'filter_form','method'=>'get']) !!}
					
					{!! Form::hidden('from_location_id_intre', (int)request('from_location_id_intre')) !!}
					{!! Form::hidden('to_location_id_intre', (int)request('to_location_id_intre')) !!}
					{!! Form::hidden('post_type', (int)request('post_type')) !!}
					{!! Form::hidden('property_type', (int)request('property_type') ) !!}
					{!! Form::hidden('tot_items', (int)request('tot_items') ) !!}
					{!! Form::hidden('from_date', $from_date) !!}
					{!! Form::hidden('to_date', $to_date) !!}
					{!! Form::hidden('chkOrgServ', (int)request('chkOrgServ') ) !!}
					{!! Form::hidden('origin_handy_serivce', (int)request('origin_handy_serivce') ) !!}
					{!! Form::hidden('insurance_serivce', (int)request('insurance_serivce') ) !!}
					{!! Form::hidden('destination_storage_serivce', (int)request('destination_storage_serivce') ) !!}
					{!! Form::hidden('destination_handy_serivce', (int)request('destination_handy_serivce') ) !!}

                    {!! Form::hidden('delivery_flexible_hidden_relocint', Session::get('session_fdelivery_date_buyer')) !!}
                    {!! Form::hidden('dispatch_flexible_hidden_relocint', Session::get('session_fdispatch_date_buyer')) !!}

                    <input type="hidden" name="filter_set" id="filter_set" value="1">
                    
					<h2 class="filter-head">Form Filter</h2>
					<div class="seller-list inner-block-bg">
						<div class="form-control-fld margin-top">
							<div class="input-prepend">
								<span class="add-on"><i class="fa fa-map-marker"></i></span>
								<input type="text" class="form-control" placeholder="From Location" readonly value="{{$from_city_name}}" />
							</div>
						</div>
						<div class="form-control-fld">
							<div class="input-prepend">
								<span class="add-on"><i class="fa fa-map-marker"></i></span>	
								<input type="text" class="form-control" placeholder="To Location" readonly value="{{$to_city_name}}" />
							</div>
						</div>
						<div class="form-control-fld">
							<div class="input-prepend">
								<span class="add-on"><i class="fa fa-calendar-o"></i></span>
								<input type="text" name="valid_from" class="form-control" placeholder="Dispatch Date" value="{{ request('valid_from') }}" readonly />
							</div>
						</div>
						<div class="form-control-fld">
							<div class="input-prepend">
								<span class="add-on"><i class="fa fa-calendar-o"></i></span>
								<input type="text" name="valid_to" class="form-control" placeholder="Delivery Date" value="{{ request('valid_to') }}" readonly />
							</div>
						</div>
					</div>

					@include("partials.filter._price")


				<?php
					$selectedPayment = isset($_REQUEST['selected_payments']) ? $_REQUEST['selected_payments'] : array();
					?>
					@if(Session::has('show_layered_filter')&& Session::get('show_layered_filter')!="")
						@if (Session::has('layered_filter_payments')&& Session::get('layered_filter_payments')!="")
						<h2 class="filter-head">Payment Mode</h2>
						<div class="payment-mode inner-block-bg">
							@foreach (Session::get('layered_filter_payments') as $paymentId => $paymentName)
							<?php $selected = in_array($paymentId, $selectedPayment) ? 'checked="checked"' : ""; ?>
							<div class="check-box">
								<input type="checkbox" class="filtercheckbox" value="{{$paymentId}}" {{$selected}} name="selected_payments[]" onClick="this.form.submit()"/><span class="lbl padding-8"> 
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

					<?php
					$selectedSellers = isset($_REQUEST['selected_users']) ? $_REQUEST['selected_users'] : array();
					?>
					@if(Session::has('show_layered_filter')&& Session::get('show_layered_filter')!="")
						@if (Session::has('layered_filter')&& Session::get('layered_filter')!="")
						<h2 class="filter-head"><?php echo (Auth::user()->lkp_role_id == 1) ? "Sellers" : "Buyers"; ?> List</h2>
						<div class="seller-list inner-block-bg">
							@foreach (Session::get('layered_filter') as $userId => $userName)
							<?php $selected = in_array($userId, $selectedSellers) ? 'checked="checked"' : ""; ?>
							<div class="check-box"><input type="checkbox"  class="filtercheckbox" value="{{$userId}}" {{$selected}} name="selected_users[]" onClick="this.form.submit()" /><span class="lbl padding-8"> {{ $userName }}</span></div>
							@endforeach
						</div>
						@endif
					@endif
					
					{!! Form::close() !!}
				</div>

				<!-- Left Section Ends Here -->


				<!-- Right Section Starts Here -->
				<div class="main-right">
					<div class="table-div">								
						{!! $gridBuyer !!}
					</div>
				</div>
				<!-- Right Section Ends Here -->

			</div>
		</div>

		<div class="clearfix"></div>
		
	</div>
</div>

<!-- Model Window starts -->
<div class="modal fade" id="modify-search" role="dialog">
    <div class="modal-dialog">
	      <!-- Modal content-->
	      <div class="modal-content">
	        <button type="button" class="close" data-dismiss="modal">&times;</button>
	        <div class="home-search gray-bg">
				<div class="col-md-12 padding-none">
	        		<div class="col-md-12 form-control-fld">
						<div class="radio-block">
							<input type="radio" name="post_type" id="relocation_air"  value="1"  /> 
								<label for="relocation_air"><span></span>Air</label>
							<input type="radio" name="post_type" id="relocation_ocean" value="2" checked/>
							 <label for="relocation_ocean"><span></span>Ocean</label>
						</div>
					</div>
	
					<!-- Start - Spot [Air/Ocean] -->
					<div class="relocation_spot_show">
	                    <div class="relocation_air_show"  style="display:none">
							{!! Form::open(['url' => 'byersearchresults','id'=>'posts-form_buyer_relocationair','method'=>'get']) !!}
							{!!	Form::hidden('is_search',1,array('class'=>'form-control')) !!}
	
								{!! Form::hidden('post_type', '1', array('id' => 'post_type')) !!}
	
									@include('buyer.relocation.home.international.search.airint._form')
	
							{!! Form::close() !!}	
	                    </div>
	                     <div class="relocation_ocean_show">
							{!! Form::open(['url' => 'byersearchresults','id'=>'relocationint_ocean_getquote','method'=>'get']) !!}
							{!!	Form::hidden('is_search',1,array('class'=>'form-control')) !!}
								{!! Form::hidden('post_type', '2', array('id' => 'post_type')) !!}
	
								@include('buyer.relocation.home.international.search.ocean._form')
	
							{!! Form::close() !!}	
						 </div>
		        	</div>
		        </div>
		    </div>
		</div>
	</div>
</div>
<!-- Modal Window ends here --> 
                		
@include('partials.footer')
    
@endsection