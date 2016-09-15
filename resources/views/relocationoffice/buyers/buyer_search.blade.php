@extends('app')
@section('content')
<!-- Page top navigation Starts Here-->
@include('partials.page_top_navigation')
	
		<!-- Header Starts Here -->		
		<div class="clearfix"></div>
		<div class="main">
			<div class="container">
				<div class="home-search gray-bg margin-top-none">
					{!! Form::open(['url' => 'byersearchresults','id'=>'relocation_domestic_office_buyersearch_sellers','method'=>'get']) !!}
					{!!	Form::hidden('is_search',1,array('class'=>'form-control')) !!}
					<div class="col-md-12 padding-none">
							<div class="col-md-4 form-control-fld">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-map-marker"></i></span>
									{!! Form::text('from_location', '' , ['id' => 'from_location','class' => 'form-control', 'placeholder' => 'City*']) !!}
	                                {!! Form::hidden('from_location_id', '' , array('id' => 'from_location_id')) !!}
								</div>
							</div>
	
							<div class="col-md-4 form-control-fld">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-calendar-o"></i></span>
									{!! Form::text('from_date', '',  ['id' => 'dispatch_date1','class' => 'calendar form-control from-date-control','readonly' => true, 'placeholder' => 'Pickup Date*']) !!}
									<input type="hidden" name="dispatch_flexible_hidden" id="dispatch_flexible_hidden" value="0">
								</div>
							</div>
							<div class="col-md-4 form-control-fld">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-calendar-o"></i></span>
									{!! Form::text('to_date', '' , ['id' => 'delivery_date1','class' => 'calendar form-control to-date-control','readonly' => true, 'placeholder' => 'Delivery Date']) !!}
									<input type="hidden" name="delivery_flexible_hidden" id="delivery_flexible_hidden" value="0">
								</div>
							</div>
							<div class="clearfix"></div>
							
							<div class="relocation_house_hold_buyer_create">
							<div class="col-md-4 form-control-fld">
									<div class="input-prepend">
										<span class="add-on"><i class="fa fa-road"></i></span>
										{!! Form::text('distance', '', ['id' => 'distance','class' => 'form-control clsROMDistanceKM','placeholder' => 'Distance*']) !!}
										<span class="add-on unit1 manage">
											KM
										</span>
									</div>
							</div>
							{{-- <div class="col-md-12 form-control-fld text-right margin-none">
								<span class="red spl-link advanced-search-link"><span class="more-search">+</span><span class="less-search">-</span> Invetory Details</span>
							</div>	 --}}
							
							<div class="advanced-search-details-office">

								<div class="clearfix"></div>

								<!-- Table Starts Here -->

							<div class="table-div table-style1 inventory-block-officemove">
								<div class="table-div table-style1 inventory-table padding-none">
									
									<!-- Table Head Starts Here -->

									<div class="table-heading inner-block-bg">
										<div class="col-md-8 padding-left-none">&nbsp;</div>
										<div class="col-md-4 padding-left-none">No of Items</div>
									</div>

									<!-- Table Head Ends Here -->

									<div class="table-data">
									<!-- Table Row Starts Here -->
											@foreach($particulars as $particular)
													<div class="table-row inner-block-bg">
														<div class="col-md-6 padding-left-none">{{$particular->office_particular_type}}</div>
																								
														 <div class="col-md-6 padding-left-none">
															{!! Form::text('roomitems['.$particular->id.']','' , ['id' => 'roomitems_'.$particular->id,'maxlength'=>3,'class' => 'form-control form-control1 roomitems clsROMNoOfItems', 'onblur' => 'javascript:valuecheck(this.value,this.id)'])  !!}
														</div>
													</div>
											@endforeach
										<!-- Table Row Ends Here -->
									</div>
								</div>	
								</div>	
							</div>
							
							</div>
										
					</div>
					
				</div>
				<div class="col-md-4 col-md-offset-4">
						<input type="hidden" name="total_hidden_volume" id="total_hidden_volume" value="">
						<input  type="submit" class="btn theme-btn btn-block"  name="getquote" id="getquote" value="Search">
					</div>	
	{!! Form::close() !!}	

			</div>
			</div>
			<div class="clearfix"></div>
			
@include('partials.footer')
@endsection