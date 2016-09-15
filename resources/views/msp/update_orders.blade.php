@extends('app') 
@section('content')
<div class="main-container">
	<div class="container container-inner">
	    @if (Session::has('message'))
<div class="flash ">
	<p class="text-success col-sm-12 text-center flash-txt alert-success">{{
		Session::get('message') }}</p>
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
						<span class="pull-left margin-right">Fill the Form</span>&nbsp; &nbsp;<h6 class="pull-left margin-none" id="updateOrderError">&nbsp;</h6>
						<div class="clearfix"></div><div class="clearfix"></div><div class="clearfix"></div><div class="border-al-inner">
							<div class="form-group">
								<label class="col-sm-3 padding-none">Buyer Order</label>
							{!! Form::select('order_id',
								array('' => 'Select Order#') + $ordersList,'',['class'=>'selectpicker col-xs-5 padding-none','id'=>'intra_order_id']) !!}
							<label class="padding-none error" id="intra_order_error">&nbsp;</label>
						
							</div>
							<div class="form-group">
								<label class="col-sm-3 padding-none">Update Status</label>
								{!! Form::button('Picked Up', array( 'class'=>'custom-button btn-success','id'=>'pickupBtn')) !!}
								{!! Form::button('Delivered', array( 'class'=>'custom-button btn-info','id'=>'deliverBtn')) !!}
							</div>
							
							
						</div>
						<div class="clearfix"></div>
					
				</div>


					{!! Form::close() !!}
			</div>
		</div>


	</div>
</div>

@endsection
