@extends('app')
@section('content') 

<div class="main-container">
	<div class="container container-inner">
		
		<!-- Left Nav Starts Here -->
		@include('partials.leftnav')
		<!-- Left Nav Ends Here -->
		{!! Form::open(['url' =>'#','id' => 'buyer_results_form','class'=>'filter_form']) !!}
		<div class="col-lg-7 col-md-6 col-sm-12 col-xs-12 main-middle">
				
				<div class="block">
					<!-- Navigation links Starts Here -->
					@include('partials.search_navigation_links')
					<!-- Navigation links Starts Here -->
					
					{!! $filter->open !!}
					<div class="col-md-12 col-sm-12 col-xs-12 padding-top" >
						<div class="gray_bg">
							<div class="col-md-3 col-sm-3 col-xs-12 padding-none ">
								{!! $filter->field('spi.from_location_id') !!}
							</div>
							<div class="col-md-3 col-sm-3 col-xs-12 padding-right-none mobile-padding-none">
								{!! $filter->field('spi.to_location_id') !!}
							</div>
							<div class="col-md-6 col-sm-6 col-xs-12 margin-top mobile-margin-none mobile-padding-none ">
								Full Truck Load
							</div>
							<div class="clearfix"></div>
							<div class="col-md-3 col-sm-3 col-xs-12 padding-none  mobile-padding-none mobile-margin-top">
								{!! $filter->field('spi.lkp_load_type_id') !!}
							</div>

							<div class="col-md-3 col-sm-3 col-xs-12 padding-right-none mobile-padding-none">
								{!! $filter->field('spi.lkp_vehicle_type_id') !!}
							</div>
							<div class="clearfix"></div>

							<div class="col-md-3 col-sm-3 col-xs-12 padding-none form-group">
<!-- 								<input type="text" class="calendar" placeholder="15-05-2015"> -->
								{!! $filter->field('sp.from_date') !!}
							</div>
							<div class="col-md-3 col-sm-3 col-xs-12 padding-right-none form-group mobile-padding-none">
<!-- 								<input type="text" class="calendar" placeholder="To"> -->
							{!! $filter->field('sp.to_date') !!}
							</div>
							<div class="clearfix"></div>
							
						</div>
						
						
						<div class="col-md-12 col-sm-12 gray_bg">
						<div class="col-md-2 col-sm-12 col-xs-12 padding-none form-group">Filters</div>

						<div class="col-md-4 col-sm-6 col-xs-12 padding-left-none mobile-padding-none form-group">Price Band (Rs)
							<div class="clearfix"></div>
							<div class="text-center pad-lr-7">
								<div id="slider-range" class="margin-top"></div>
								<p class="margin-top">
								<input type="text" id="amount" readonly name="price">
								<?php
										$price_from = isset($_REQUEST['price_from']) ? $_REQUEST['price_from'] : 1000;
										$price_to = isset($_REQUEST['price_to']) ? $_REQUEST['price_to'] : 2000;
								?>
								<input type="hidden" id="price_from" value="<?php echo $price_from; ?>" />
								<input type="hidden" id="price_to" value="<?php echo $price_to; ?>" />
								</p>
							</div>
						</div>

						<div class="col-md-6 col-sm-6 col-xs-12 padding-none">			
							<div class="col-md-12 col-sm-12 col-xs-12 padding-none form-group">Tracking</div>
							<div class="col-md-6 col-sm-6 col-xs-12 padding-none">
								<div class="form-group">
									<input type="checkbox" name="tracking" value="1" onChange="this.form.submit()" <?php if(isset($_REQUEST['tracking']) && $_REQUEST['tracking']) { echo "checked='checked'"; } ?>  >&nbsp; &nbsp;Milestone
								</div>
								<div class="form-group">
									<input type="checkbox" name="tracking1" value="2" onChange="this.form.submit()" <?php if(isset($_REQUEST['tracking1']) && $_REQUEST['tracking1']) { echo "checked='checked'"; } ?>>&nbsp; &nbsp;Real Time
								</div>
							</div>
							
							<div class="col-md-6 col-sm-6 col-xs-12 padding-none">
								<div class="form-group">
									<input type="checkbox">&nbsp; &nbsp;Top Sellers (Orders)
								</div>
								<div class="form-group">
									<input type="checkbox">&nbsp; &nbsp;Top Sellers (Rated)
								</div>
							</div>
						</div>
						<div class="col-md-3 col-sm-3 col-xs-12 padding-none form-group">
								<?php if((isset($_REQUEST['dispatch_flexible_hidden']) && $_REQUEST['dispatch_flexible_hidden']) || (isset($_REQUEST['date_flexiable']) && ($_REQUEST['date_flexiable']!=""))) { ?>
								 <?php
								 //echo "<pre>"; print_R($_REQUEST); echo "</pre>";
								$flexdate = (isset($_REQUEST['from_date']) && !empty($_REQUEST['from_date'])) ? $_REQUEST['from_date'] : (isset($_REQUEST['date_flexiable']) ? $_REQUEST['date_flexiable'] : "");
								  echo "<select name='date_flexiable' onChange='this.form.submit()' class='selectpicker' >";
									for($i=-3;$i<=3;$i++){
										$selected = "";
										if($i<0){
											$date1 = new DateTime($flexdate);
											$date1=$date1->modify("$i day");
										}else if($i>0){
											$date1 = new DateTime($flexdate);
											$date1=$date1->modify("$i day");
										}else{
											$date1 = new DateTime($flexdate);
											//$selected = "selected='selected'";
										}
										if(isset($_REQUEST['date_flexiable'])){
											if(($_REQUEST['date_flexiable'] == $date1->format('Y-m-d'))){
												$selected = "selected='selected'";
											}	
										}else {
											if(isset($_REQUEST['from_date']) == $date1->format('Y-m-d')){
												$selected = "selected='selected'";
											}
										} 


									   echo "<option ".$selected." value='".$date1->format('Y-m-d')."'>".$date1->format('Y-m-d')."</option>";
									}
									echo "</select>";									
								 ?>
							<?php } ?>
						</div>	
						</div>
						<?php if(isset($_REQUEST['from_date'])) { ?>
						<input type="hidden" name="from_date" value="<?php echo $_REQUEST['from_date']; ?>">
						<div class="clearfix"></div>
						<?php } ?>						
					</div>
					{!! $filter->close !!}				
				
					<div class="clearfix"></div>
					<div class='table-data' id="booknow_buyer_form">
						
                        
						{!! $gridBuyer !!}			
						
						
								
					</div>
					<div class="clearfix"></div>
					
				</div>
			</div>
			{!! Form::close() !!}
		<!-- Right Starts Here -->
		@include('partials.right')
		<!-- Right Ends Here -->
	</div>
</div>		
@endsection