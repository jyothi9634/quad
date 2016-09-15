@extends('app') 
@section('content')
<div class="main-container">
	<div class="container container-inner">
	    @if (Session::has('success_message'))
<div class="flash ">
	<p class="text-success col-sm-12 text-center flash-txt alert-success">{{
		Session::get('success_message') }}</p>
</div>
@endif
@if (Session::has('error'))
<div class="flash ">
	<p class="text-alert col-sm-12 text-center flash-txt alert-danger">{{
		Session::get('error') }}</p>
</div>
@endif

		<div
			class="col-md-9 col-sm-9 col-xs-12 div-center col-md-offset-2 col-sm-offset-2">

			<h4>
				Create Seller quote <span class="red-text"> LOGISTIKS.COM</span>
			</h4>
			<p>Please fill the form below to quote a price for buyer's intracity post</p>

			<div class="col-md-8 col-sm-8 steps-registration padding-none">

				<div class="staps">
					
					{!! Form::open(array('url' =>
					'msp/seller_quotes', 'id' =>
					'create-seller-intracity-quote', 'class'=>'form-inline margin-top',
					'enctype' => 'multipart/form-data' )) !!}
					
						<div class="clearfix"></div>
						<br>
						<p>Fill the Form</p>
						<div class="border-al-inner">
							<div class="form-group">
								<label class="col-sm-3 padding-none">Buyer Intracity Post Id</label>
							{!! Form::select('buyer_quote_id',
								array('' => 'Select Buyer Post Id') + $buyerPostsList,'',['class'=>'selectpicker col-xs-4','id'=>'buyer_quote_id']) !!}
							</div>
							<div class="form-group">
								<label class="col-sm-3 padding-none">Intracity Vehicle</label>
								{!! Form::select('lkp_ict_vehicle_id',
								array('' => 'Select Intracity Vehicle') + $vehiclesList,'',['class'=>'selectpicker col-xs-4','id'=>'lkp_ict_vehicle_id']) !!}
						
							</div>
							<div class="form-group">
								<label class="col-sm-3 padding-none">Quote a Price</label> {!! Form:: text
							('initial_quote_price','', array(
							'class'=>'form-control margin-bottom margin-left','id'=>'initial_quote_price'
							,'placeholder'=>'Initial price in Rs/-' , 'maxlength'=>'20' )) !!}
							</div>
							
						</div>
						<div class="clearfix"></div>
						<br> {!! Form::submit('Submit Quote', array( 'class'=>'red-btn pull-right',
							'id'=>'submitSellercorporate')) !!}
				
				
				</div>


					{!! Form::close() !!}
			</div>
		</div>


	</div>
</div>
</div>
</div>
@endsection
