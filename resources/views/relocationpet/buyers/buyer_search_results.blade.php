@extends('app')
@section('content')
@inject('commonComponent', 'App\Components\CommonComponent')
<!-- Page top navigation Starts Here-->
@include('partials.page_top_navigation')
{{--*/$getAllPetTypes = $commonComponent->getAllPetTypes() /*--}}
{{--*/$getAllCageTypes = $commonComponent->getAllCageTypes() /*--}}
{{--*/$getAllBreedTypes = $commonComponent->getAllBreedTypesList() /*--}}

		<div class="clearfix"></div>

		<div class="main">

			<div class="container">
			
			<h1 class="page-title">Search Results (Relocation Pet)</h1><a href="#" class="change-service" data-toggle="modal" data-target="#change-service">Change Service</a>
				
				<!-- Search Block Starts Here -->

				<div class="search-block inner-block-bg">
					<div class="from-to-area">
						<span class="search-result">
							<i class="fa fa-map-marker"></i>
							<span class="location-text">{{ $request['from_location'] }} to {{ $request['to_location'] }}</span>
						</span>
					</div>
					<div class="date-area">
						<div class="col-md-6 padding-none">
							<p class="search-head">Dispatch Date</p>
							<span class="search-result">
								<i class="fa fa-calendar-o"></i>
								{{ $request['from_date'] }}
							</span>
						</div>
						<div class="col-md-6 padding-none">
							<p class="search-head">Delivery Date</p>
							<span class="search-result">
								<i class="fa fa-calendar-o"></i>
								@if(isset($request['to_date']) && $request['to_date']!='')
								{{ $request['to_date'] }}
                                                                @else
                                                                NA
                                                                @endif
							</span>
						</div>
					</div>
					<div>
						<p class="search-head">Pet Type</p>
						<span class="search-result">{{ $commonComponent->getPetType($request['selPettype']) }}</span>
					</div>
                                        
					<div>
						<p class="search-head">Breed</p>
						<span class="search-result">
                                                @if($request['selBreedtype']!=0)
                                                {{ $commonComponent->getBreedType($request['selBreedtype']) }}
                                                @else
                                                NA
                                                @endif
                                            </span>
					</div>
                                    
					<div>
						<p class="search-head">Cage Type</p>
						<span class="search-result">{{ $commonComponent->getCageType($request['selCageType']) }}</span>
					</div>
                                    
                                         <div>
						<p class="search-head">Cage Weight</p>
						<span class="search-result">{{ $commonComponent->getCageWeight($request['selCageType']) }} KGs</span>
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
                                                {!! Form::hidden('from_location_id', $from_location_id) !!}
                                                {!! Form::hidden('to_location_id', $to_location_id) !!}
                                                {!! Form::hidden('from_location', $from_location) !!}
                                                {!! Form::hidden('to_location', $to_location) !!}
                                                {!! Form::hidden('from_date', $request['from_date']) !!}
                                                {!! Form::hidden('to_date', $request['to_date']) !!}
                                                {!! Form::hidden('selPettype', $request['selPettype']) !!}
                                                {!! Form::hidden('selBreedtype', $request['selBreedtype']) !!}
                                                {!! Form::hidden('selCageType', $request['selCageType']) !!}
                                
                                                        <input type="hidden" name="filter_set" id="filter_set" value="1">
							
							@if ((Session::has('show_layered_filter')&& Session::get('show_layered_filter')!="") || (!isset($_REQUEST['is_search'])))
								@include("partials.filter._price")
                            @endif  
                                                        
							<?php $selectedPayment = isset($_REQUEST['selected_payments']) ? $_REQUEST['selected_payments'] : array(); ?>
							@if (Session::has('show_layered_filter')&& Session::get('show_layered_filter')!="")
								@if (Session::has('layered_filter_payments')&& Session::get('layered_filter_payments')!="")
							<h2 class="filter-head">Payment Mode</h2>
							<div class="payment-mode inner-block-bg">
								@foreach (Session::get('layered_filter_payments') as $paymentId => $paymentName)
								<?php $selected = in_array($paymentId, $selectedPayment) ? 'checked="checked"' : ""; ?>
								<div class="check-box"><input type="checkbox" class="filtercheckbox" value="{{$paymentId}}" {{$selected}} name="selected_payments[]" onClick="this.form.submit()"/><span class="lbl padding-8"> 
                                                                    @if ($paymentName == 'Advance') 
                                                                    {{--*/ $paymentType = 'Online Payment' /*--}}
                                                                    @else
                                                                    {{--*/ $paymentType = $paymentName /*--}}
                                                                    @endif
                                                                    {{$paymentType}}
                                                                    </span></div>
								@endforeach
							</div>
							@endif
							@endif
                                                        
                                                        <h2 class="filter-head">Tracking</h2>
							<div class="tracking inner-block-bg">
								<div class="check-box">
									<input type="checkbox" name="tracking" value="1" onClick="this.form.submit()" <?php if(isset($_REQUEST['tracking']) && $_REQUEST['tracking']!="") { echo "checked='checked'"; } ?>  ><span class="lbl padding-8">{{TRACKING_MILE_STONE}}</span>
									</div>
								<div class="check-box"><input type="checkbox" name="tracking1" value="2" onClick="this.form.submit()" <?php if(isset($_REQUEST['tracking1']) && $_REQUEST['tracking1']!="") { echo "checked='checked'"; } ?>><span class="lbl padding-8">{{TRACKING_REAL_TIME}}</span></div>
							</div>


							<div class="tracking inner-block-bg">
								<div class="check-box"><input type="checkbox" name="ftltopseller_orders"  <?php //if(isset($_REQUEST['ftltopseller_orders']) && $_REQUEST['ftltopseller_orders']) { echo "checked='checked'"; } ?>><span class="lbl padding-8">Top Sellers (Orders) </span></div>
								<div class="check-box"><input type="checkbox" name="ftltopseller_rated"  <?php //if(isset($_REQUEST['ftltopseller_rated']) && $_REQUEST['ftltopseller_rated']) { echo "checked='checked'"; } ?>><span class="lbl padding-8">Top Sellers (Rated) </span></div>
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
                                                        <?php if((isset($_REQUEST['dispatch_flexible_hidden']) && $_REQUEST['dispatch_flexible_hidden']) || (isset($_REQUEST['date_flexiable']) && ($_REQUEST['date_flexiable']!=""))) { ?>

							<h2 class="filter-head">Preferred Dispatch Date</h2>
							<div class="seller-list inner-block-bg">
								 <?php
								$flexdate = (isset($_REQUEST['from_date']) && !empty($_REQUEST['from_date'])) ? $_REQUEST['from_date'] : (isset($_REQUEST['date_flexiable']) ? $_REQUEST['date_flexiable'] : "");
							
									for($i=-3;$i<=3;$i++){
										$selected = "";
										if($i<0){
											$date1 = date('Y-m-d', strtotime(str_replace('/', '-',$flexdate)));//new DateTime($flexdate);
											$date1 = new DateTime($date1);
											$date1=$date1->modify("$i day");
										}else if($i>0){
											$date1 = date('Y-m-d', strtotime(str_replace('/', '-',$flexdate)));
											$date1 = new DateTime($date1);
											$date1=$date1->modify("$i day");
										}else{
											$date1 = date('Y-m-d', strtotime(str_replace('/', '-',$flexdate)));
											$date1 = new DateTime($date1);
										}
										if(isset($_REQUEST['date_flexiable'])){
											if(($_REQUEST['date_flexiable'] == $date1->format('Y-m-d'))){
												$selected = "checked='checked'";
											}
										}else {
											if(isset($_REQUEST['from_date'])){
											//if($_REQUEST['from_date'] == $date1->format('Y-m-d')){
                                                                                            if($_REQUEST['from_date'] == $date1->format('d/m/Y')){
												$selected = "checked='checked'";
											}
											}
										}
										if($date1->format('Y-m-d') >= date('Y-m-d')){
											echo "<div class='check-box'><input type='radio' id ='date_flexiable_$i' name='date_flexiable' onChange='this.form.submit()' ".$selected." value='".$date1->format('Y-m-d')."' /><label for='date_flexiable_$i'><span></span>".$date1->format('d-m-Y')."</label></div>";
										}
									}
									
								 ?>
                                                            
                                                            

							</div>
							<?php } ?>      
							                
							
						{!! Form::close() !!}	
						</div>
					
						<!-- Left Section Ends Here -->


						<!-- Right Section Starts Here -->

						<div class="main-right">
							<!-- Table Starts Here -->
							<div class="table-div">								
								{!! $gridBuyer !!}
							</div>	
						</div>

						<!-- Right Section Ends Here -->

					</div>
				</div>
				
		
     <div class="clearfix"></div>
				<div class="clearfix"></div>
			<a href="{{'/relocation/creatbuyerrpost?search=1'}}"><button class="btn post-btn pull-right">Post &amp; get Quote</button></a>	
                        {!! Form::hidden('is_search',1,array('class'=>'form-control')) !!}
			</div>
	</div>
                
                
                
                <!-- Model Window starts -->
		<div class="modal fade" id="modify-search" role="dialog">
	    <div class="modal-dialog">

	      <!-- Modal content-->
	      <div class="modal-content">
	        <button type="button" class="close" data-dismiss="modal">&times;</button>
	        {!! Form::open(['url' =>'byersearchresults','id' => 'posts-form_buyer_relocationpet' , 'autocomplete'=>'off','method'=>'get']) !!}
	        <div class="modal-body">
	          <div class="col-md-12 padding-none">
                            <div class="col-md-4 form-control-fld">
                                    <div class="input-prepend">
                                            <span class="add-on"><i class="fa fa-map-marker"></i></span>
                                            {!! Form::text('from_location', Session::get('session_from_location_buyer'), ['id' => 'from_location', 'class'=>'form-control','placeholder' => 'From Location *']) !!}
                                            {!! Form::hidden('from_location_id', Session::get('session_from_city_id_buyer'), array('id' => 'from_location_id')) !!}
                                    </div>
                            </div>
                            <div class="col-md-4 form-control-fld">
                                    <div class="input-prepend">
                                            <span class="add-on"><i class="fa fa-map-marker"></i></span>
                                            {!! Form::text('to_location', Session::get('session_to_location_buyer'), ['id' => 'to_location', 'class'=>'form-control', 'placeholder' => 'To Location *']) !!}
                                            {!! Form::hidden('to_location_id', Session::get('session_to_city_id_buyer'), array('id' => 'to_location_id')) !!}
                                    </div>
                            </div>
                                <div>
                                    <div class="col-md-4 form-control-fld">
                                        <div class="input-prepend">
                                                <span class="add-on"><i class="fa fa-calendar-o"></i></span>
                                                {!! Form::text('from_date', Session::get('session_dispatch_date_buyer'), ['id' => 'dispatch_date1','class' => 'calendar form-control from-date-control', 'placeholder' => 'Dispatch Date *','readonly'=>"readonly"]) !!}
                                                <input type="hidden" name="dispatch_flexible_hidden" id="dispatch_flexible_hidden" value="<?php echo isset($_REQUEST['dispatch_flexible_hidden']) ? $_REQUEST['dispatch_flexible_hidden'] : ""; ?>">
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="col-md-4 form-control-fld">
                                        <div class="input-prepend">
                                                <span class="add-on"><i class="fa fa-calendar-o"></i></span>
                                                {!! Form::text('to_date', Session::get('session_delivery_date_buyer'), ['id' => 'delivery_date1','class' => 'calendar form-control to-date-control', 'placeholder' => 'Delivery Date','readonly'=>"readonly"]) !!}
                                                <input type="hidden" name="delivery_flexible_hidden" id="delivery_flexible_hidden" value="<?php echo isset($_REQUEST['delivery_flexible_hidden']) ? $_REQUEST['delivery_flexible_hidden'] : ""; ?>">
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-3 form-control-fld">
                                        <div class="input-prepend">
                                                <span class="add-on"><i class="fa fa-paw"></i></span>
                                                {!! Form::select('selPettype',(['' => 'Pet Type *'] +$getAllPetTypes), Session::get('session_pet_type_reslocation'), ['class' =>'selectpicker','id'=>'selPettype','data-purl' => URL::to('relocationpet/ajxbreedtypes') ]) !!}
                                        </div>
                                    </div>
                                   
                                    <div class="col-md-3 form-control-fld">
                                        <div class="input-prepend">
                                            <span class="add-on"><i class="fa fa-paw"></i></span>
                                            {!! Form::select('selBreedtype',(['' => 'Breed'] +$getAllBreedTypes), $request['selBreedtype'], ['class' =>'selectpicker','id'=>'selBreedtype' ]) !!}                                            
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-2 form-control-fld">
                                        <div class="input-prepend">
                                                <span class="add-on"><i class="fa fa-chain"></i></span>
                                                {!! Form::select('selCageType',(['' => 'Cage Type'] +$getAllCageTypes), Session::get('session_cage_type_reslocation') ,['class' =>'selectpicker','id'=>'selCageType']) !!}
                                        </div>
                                    </div>                                    
                                    
                                    {!! Form::hidden('is_search',1,array('class'=>'form-control')) !!}
                                    
                            </div>
                    </div>
	        </div>
			<div class="container">
				<div class="col-md-4 col-md-offset-4">
					{!! Form::submit('&nbsp; Search &nbsp;', ['name' => 'Search','class'=>'btn theme-btn btn-block','id' => 'Search']) !!}
				</div>
			</div>
	        {!! Form::close() !!}

	      </div>

	    </div>
	  </div>

<!-- Modal Window ends here --> 
                
		
@include('partials.footer')
    
@endsection