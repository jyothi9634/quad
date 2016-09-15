@extends('app')
@section('content')

	<div class="main-container">	
		<div class="container container-inner">		
		<!-- Left Nav Starts Here -->
		@include('partials.leftnav')
		<!-- Left Nav Ends Here -->		
			<div class="col-lg-7 col-md-6 col-sm-12 col-xs-12 main-middle">
				<div class="bread-crumb">Home &nbsp;<i class="fa fa-angle-right"></i> &nbsp; Enquiries &nbsp;<i class="fa fa-angle-right"></i> &nbsp;  Road PTL &nbsp;<i class="fa fa-angle-right"></i> &nbsp;  Spot Get Quote</div>
				<div class="block">
				{!! Form::open(['url' =>'#','id' => 'ptlBuyerQuotelineitemsForm']) !!}
					<div class="tab-nav underline">
					@include('partials.page_top_navigation')
					</div>
					
					<div class="col-md-12 col-sm-12 col-xs-12 padding-none margin-bottom" >Enquiry Type : Spot | Term Contract</div>
					<div class="col-md-12 col-sm-12 col-xs-12 padding-none" >
						<div class="col-md-4 col-sm-4 col-xs-12 padding-none">							
							{!! Form::text('ptlFromLocation', '' , ['id' => 'ptlFromLocation', 'class'=>'form-control form-group', 'placeholder' => 'From']) !!}
							{!! Form::hidden('ptlFromLocationId', '' , array('id' => 'ptlFromLocationId')) !!}
					    </div>
					    <div class="col-md-4 col-sm-4 col-xs-12 padding-right-none mobile-padding-none">
							{!! Form::text('ptlToLocation', '' , ['id' => 'ptlToLocation', 'class'=>'form-control form-group', 'placeholder' => 'To']) !!}
							{!! Form::hidden('ptlToLocationId', '' , array('id' => 'ptlToLocationId')) !!}
					    </div>
					    <div class="clearfix"></div>
					    <div class="col-md-4 col-sm-4 col-xs-12 padding-none">
					    	<div class="form-group">					    		
					    	{!! Form::text('ptlDispatchDate','', ['id' => 'ptlDispatchDate','class' => 'calendar form-control from-date-control', 'placeholder' => 'Dispatch Date']) !!}
					    	</div>
						    	<div class="col-sm-12 col-xs-12 padding-none">
									<div class="form-group">
										{!! Form::checkbox('ptlFlexiableDispatch', 1, null, ['class' => '']) !!}
										Flexible Dispatch Dates
							    	</div>
						    	</div>
						    	<div class="col-sm-12 col-xs-12 padding-none">
									<div class="form-group">
										{!! Form::checkbox('ptlDoorpickup', 1, null, ['class' => '']) !!}
										Door Pickup
									</div>
					    		</div>
					    </div>
					    <div class="col-md-4 col-sm-4 col-xs-12 padding-right-none mobile-padding-none">
					    	<div class="form-group">								
								{!! Form::text('ptlDeliveryhDate','', ['id' => 'ptlDeliveryhDate','class' => 'calendar form-control to-date-control', 'placeholder' => 'Delivery Date (Optional)']) !!}
					    	</div>
					    	<div class="col-sm-12 col-xs-12 padding-none">
									<div class="form-group">
										{!! Form::checkbox('ptlFlexiableDelivery', 1, null, ['class' => '']) !!}
										Flexible Delivery Dates
							    	</div>
						    </div>
						    <div class="col-sm-12 col-xs-12 padding-none">
									<div class="form-group">
										{!! Form::checkbox('ptlDoorDelivery', 1, null, ['class' => '']) !!}
										Door Delivery
									</div>
					    	</div>
					    </div>
						<div class="clearfix"></div>
						<div class="clearfix"></div>
							
						<div class="heading padding-top">Add Item Details</div>
						<div class="col-md-12 col-sm-12  border-al-inner margin-top mobile-padding-none">
							<div class="col-md-3 col-sm-3 col-xs-12 padding-none">							
							{!!	Form::select('ptlLoadType',(['' => 'Load Type'] +$loadTypes), '' ,['class' =>'selectpicker','id'=>'ptlLoadType']) !!}
					    </div>
					    <div class="col-md-3 col-sm-3 col-xs-12 padding-right-none mobile-padding-none">							
							{!!	Form::select('ptlPackageType',(['' => 'Packaging Type'] +$packageTypes), '' ,['class' =>'selectpicker','id'=>'ptlPackageType']) !!}
					    </div>
					    <div class="clearfix"></div>
					     <div class="col-md-12 col-sm-12 col-xs-12 padding-none mobile-margin-bottom">
							Package Size (Volumetric Weight)
					    </div>
					    <div class="col-md-12 col-sm-12 col-xs-12 padding-top mobile-padding-none">
						<div class="col-md-1 col-sm-1 col-xs-3 padding-none">
							<div class="form-group">							
							{!!	Form::text('ptlLength','',array('class'=>'form-control','placeholder'=>'L','id'=>'ptlLength')) !!}
					    </div>
					</div>
					    <div class="col-md-1 col-sm-1 col-xs-3 padding-none margin-left">
					    	<div class="form-group">
							{!!	Form::text('ptlWidth','',array('class'=>'form-control','placeholder'=>'B','id'=>'ptlWidth')) !!}
					    </div>
					</div>
					    <div class="col-md-1 col-sm-1 col-xs-3 padding-none margin-left">
					    	<div class="form-group">
							{!!	Form::text('ptlHeight','',array('class'=>'form-control','placeholder'=>'H','id'=>'ptlHeight')) !!}
					    </div>
					</div>
					    <div class="col-md-2 col-sm-2 col-xs-6 padding-none margin-left mobile-margin-none">
					    	<div class="form-group">
							<select class="selectpicker">
							   <option>Inches</option>
							</select>
					    </div>
					</div>
					    <div class="col-md-3 col-sm-3 col-xs-6 padding-right-none">							
							{!!	Form::text('ptlVolumeWeight','',array('class'=>'form-control','placeholder'=>'Display Vol. Weight','id'=>'ptlVolumeWeight')) !!}
					    </div>
					    </div>
					    
					    <div class="clearfix"></div>
					    <div class="col-md-3 col-sm-3 col-xs-6 padding-none">							
							{!!	Form::text('ptlUnitsWeight','',array('class'=>'form-control','placeholder'=>'Unit Weight','id'=>'ptlUnitsWeight')) !!}
					    </div>
					    <div class="col-md-3 col-sm-3 col-xs-6 padding-right-none">
							<select class="selectpicker">
							   <option>Kgs</option>
							</select>
					    </div>
					    <div class="clearfix"></div>
					    <input type="button" value="&nbsp; ADD &nbsp;" class="btn btn-black pull-right" id="ptlAddMoreItems">

					</div>					
					{!! Form::close() !!}
						
					    <div class="col-md-12 col-sm-12 col-xs-12 padding-top padding-none line-height">
							Location: Hyderabad (500082)
					    </div>
					     <div class="col-md-2 col-sm-2 col-xs-3 padding-none">Load Type</div>
					     <div class="col-md-2 col-sm-2 col-xs-2 padding-none">Package Type</div>
					     <div class="col-md-2 col-sm-2 col-xs-3 padding-none">Volumetric weight</div>
					     <div class="col-md-2 col-sm-2 col-xs-2 padding-none">Unit Weight</div>
					     <div class="col-md-2 col-sm-2 col-xs-2 padding-none">No. of Packages</div>

					    <div class="clearfix"></div>
						
					     <div class="col-md-2 col-sm-2 col-xs-3 padding-none line-height">Electronic 1</div>
					     <div class="col-md-2 col-sm-2 col-xs-2 padding-none line-height">Carton</div>
					     <div class="col-md-2 col-sm-2 col-xs-3 padding-none line-height">3 CFT</div>
					     <div class="col-md-2 col-sm-2 col-xs-2 padding-none line-height">15 kgs</div>
					     <div class="col-md-2 col-sm-2 col-xs-2 padding-none line-height">10</div>

					    <div class="clearfix"></div>
					    <div class="col-md-2 col-sm-2 col-xs-3 padding-none line-height">Electronic 2</div>
					     <div class="col-md-2 col-sm-2 col-xs-2 padding-none line-height">Carton</div>
					     <div class="col-md-2 col-sm-2 col-xs-3 padding-none line-height">5 CFT</div>
					     <div class="col-md-2 col-sm-2 col-xs-2 padding-none line-height">20 kgs</div>
					     <div class="col-md-2 col-sm-2 col-xs-2 padding-none line-height">12</div>

					    <div class="clearfix"></div>
					    <div class="col-md-2 col-sm-2 col-xs-3 padding-none line-height">Electronic 3</div>
					     <div class="col-md-2 col-sm-2 col-xs-2 padding-none line-height">Carton</div>
					     <div class="col-md-2 col-sm-2 col-xs-3 padding-none line-height">3 CFT</div>
					     <div class="col-md-2 col-sm-2 col-xs-2 padding-none line-height">5 kgs</div>
					     <div class="col-md-2 col-sm-2 col-xs-2 padding-none line-height">14</div>

					    <div class="clearfix"></div>
					    
					    <input type="hidden" id='ptlBuyerAddMoreItems' value='0'>

					    <div class="col-md-12 col-sm-12 col-xs-12 padding-top padding-none line-height">
							To Location: Hyderabad (500085)
					    </div>
					    
					    	<input type="button" value="&nbsp; Add New Location &nbsp;" class="btn btn-black margin-top">
					    </div>
					
					<div class="col-md-12 col-sm-12 padding-none  margin-top">
						<div class="col-md-3 col-sm-3 col-xs-12 padding-none padding-s-top">
							<input type="radio" name="quoteaccess_id" value="1" id="post_public" >&nbsp; &nbsp;Post Public
					    </div>
					    <div class="col-md-3 col-sm-3 col-xs-12 padding-none padding-s-top">
							<input type="radio" name="quoteaccess_id" value="2" id="post_private" >&nbsp; &nbsp;Post Private 
						</div>
						<div class="clearfix"></div>	
						<div class="col-md-4 col-sm-4 col-xs-12 padding-none margin-top" id="hideseller" style="display:none;">
							<input type="text" id="demo-input-local" name="seller_list" />
					    </div>						    
						<div class="clearfix"></div>	
						<div class="spacing space-margin"></div>
						<div class="clearfix"></div>
						
						<div class="col-md-12 col-sm-12 col-xs-12 padding-none margin-top">
					   {!! Form::checkbox('agree', 1, 1, ['class' => 'field','id'=>'agree'])!!}&nbsp; &nbsp; Accept Terms &amp; Conditions ( Digital Contract) &nbsp;					   
					    {!! Form::submit('Confirm', ['name' => 'confirm','class'=>'btn btn-black margin-top','id' => 'ptlAddBuyerQuote']) !!}	
					    </div>
					    <div class="clearfix"></div>
					</div>

				</div>
			</div>
			
		<!-- Right Starts Here -->
		@include('partials.right')
		<!-- Right Ends Here -->		

@endsection