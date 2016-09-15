<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0" />
	<title>NFCL</title>
        <link rel="stylesheet" type="text/css" media="screen and (min-width: 992px)" href="{{ asset('/css/sass/stylesheets/style.css') }}">
	<link rel="stylesheet" href="{{ asset('/css/sass/stylesheets/font-awesome.css') }}">
	<link rel="stylesheet" type="text/css" media="screen and (min-width: 992px)" href="{{ asset('/css/sass/stylesheets/stylesheet.css') }}"  />
        <link rel="stylesheet" href="{{ asset('/css/sass/stylesheets/bootstrap.css') }}">
        

	<link rel="stylesheet" href="{{ asset('/css/sass/stylesheets/custom.css') }}">
    <link rel="stylesheet" type="text/css" media="screen and (min-width: 768px) and (max-width: 1139px)" href="{{ asset('/css/sass/stylesheets/tablet.css') }}">
    <link rel="stylesheet"  type="text/css" media="screen and (min-width: 250px) and (max-width: 767px)" href="{{ asset('/css/sass/stylesheets/mobile.css') }}">


	<script src="{{ asset('/js/bootstrap.min.js')}}"></script>

</head>

<body>

	<div class="col-md-12 col-sm-12 col-xs-12 main-header">	
		<div class="row">
			<div class="container header-inner">
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 mobile-padding-none">
					<a href="index.html"><img src="{{ asset('/images/logo.png') }}" alt="" /></a>	
				</div>

				
			</div>
		</div>
	</div>
	
	<div class="main-container">	
		<div class="container container-inner">
			<div class="col-lg-12 col-md-12 col-sm-10 col-xs-12 main-middle">
				<h4><b>Invoice for LTL</b></h4>	
				<br>
				<div class="block block_new">
				 <div class="margin-bottom">	
			  <div class="col-md-8 col-xs-10 cool-xs-12 padding-none">	
				  <div class="col-md-6 col-sm-6 col-xs-12">
					<label for="">Vendor Name</label>
					<input type="text" class="form-control"  placeholder="Vendor Name"/>
				  </div>
				  <div class="col-md-6 col-sm-6 col-xs-12 pull-right">
					<label for="">Invoice Number</label>						
					<input type="text" class="form-control"  placeholder="Invoice Number" readonly="readonly" />
				  </div>
				<div class="clearfix"></div>
				<div class="col-md-4 col-sm-4 col-xs-12 margin-bottom">
					<label for="">Vendor Address</label>
					<textarea class="form-control">Vendor Address</textarea>
				</div>
			  </div>	

				<div class="clearfix"></div>
			  </div>
				<div class="col-md-8 col-sm-8 col-xs-12 padding-none margin-bottom">
						<div class="col-md-4 col-sm-4 col-xs-12 margin-bottom">
							<label for="">VAT / TIN Number</label>
							<input type="text" class="form-control"  placeholder="VAT / TIN Number" readonly="readonly" />
						</div>
						<div class="col-md-4 col-sm-4 col-xs-12 margin-bottom">
							<label for="">CST Number</label>
							<input type="text" class="form-control"  placeholder="CST Number" readonly="readonly" />
						</div>
						<div class="col-md-4 col-sm-4 col-xs-12 margin-bottom">
							<label for="">GST Number</label>
							<input type="text" class="form-control"  placeholder="GST Number" readonly="readonly" />
						</div>
					</div>				 
					<div class="clearfix"></div>
				  <div class="margin-bottom col-sm-12">
				  	Reference Order Number : <span><b>FTL05101986</b></span>
				  </div>
				  <div class="col-md-4 col-sm-4 col-xs-12 paddin-left-none margin-bottom">
					<label for="">Billing Address (As Specified by Buyer)</label>						
					<textarea class="form-control">Billing Address (As Specified by Buyer)</textarea>
				  </div>
				  <div class="col-md-4 col-sm-4 col-xs-12 paddin-left-none margin-bottom">
					<label for="">Shipping Address</label>					
					<textarea class="form-control">Shipping Address</textarea>
				  </div>
	

				
				<div class="clearfix"></div>
				<br>
				<div class="col-sm-8">
		            <div class="table-data">
		                <div class="col-md-12 col-sm-12 col-xs-12 padding-left-none padding-none table-row">
		                	<div class="col-md-12 col-sm-12 col-xs-12 padding-none">
					            <h5><b>Road - LTL</b></h5>
					            <p class="margin-bottom">From : <b>Hyderabad</b> &nbsp;&nbsp; To : <b>Nellore</b>
		                		<div width="100%" class="table table-head">
									<div class="col-md-3 col-sm-3 col-xs-3 padding-none">Load Type</div>
									<div class="col-md-3 col-sm-3 col-xs-3 padding-none">Package Type</div>
									<div class="col-md-3 col-sm-3 col-xs-3 padding-none">Unit Weight</div>
									<div class="col-md-3 col-sm-3 col-xs-3 padding-none">Vehicle Type</div>
									<div class="clearfix"></div>
								</div>
					            <div class="table-data">
					                <div class="col-md-12 col-sm-12 col-xs-12 padding-left-none padding-none table-row">
					                	<div class="clearfix"></div>
					                	<div class="col-md-3 col-sm-3 col-xs-3 padding-none">Value</div>
										<div class="col-md-3 col-sm-3 col-xs-3 padding-none">Value</div>
										<div class="col-md-3 col-sm-3 col-xs-3 padding-none">Value</div>
										<div class="col-md-3 col-sm-3 col-xs-3 padding-none">Value</div>
										<div class="clearfix"></div>
									</div>
									<div class="col-md-12 col-sm-12 col-xs-12 padding-left-none padding-none table-row">
					                	<div class="col-md-3 col-sm-3 col-xs-3 padding-none">Value</div>
										<div class="col-md-3 col-sm-3 col-xs-3 padding-none">Value</div>
										<div class="col-md-3 col-sm-3 col-xs-3 padding-none">Value</div>
										<div class="col-md-3 col-sm-3 col-xs-3 padding-none">Value</div>
										<div class="clearfix"></div>
									</div>
				            	</div>
		                	</div>

							<div class="col-md-6 col-sm-6 col-xs-12 pull-right text-right padding-none margin-bottom">
								<div class="col-md-3 col-sm-5 col-xs-5 padding-none pull-right">1500 /-</div>
								<div class="col-md-9 col-sm-7 col-xs-7 padding-none pull-right">Sub Total</div>
							</div>
							<div class="clearfix"></div>
							<div class="col-md-6 col-sm-6 col-xs-12 pull-right text-right padding-none margin-bottom">
								<div class="col-md-3 col-sm-5 col-xs-5 padding-none pull-right">250/-</div>
								<div class="col-md-9 col-sm-7 col-xs-7 padding-none pull-right">Service Tax [14%]</div>
							</div>
							<div class="clearfix"></div>
							<div class="col-md-6 col-sm-6 col-xs-12 pull-right text-right padding-none margin-bottom">
								<div class="col-md-3 col-sm-5 col-xs-5 padding-none pull-right">1750/-</div>
								<div class="col-md-9 col-sm-7 col-xs-7 padding-none pull-right">Total Amount</div>
							</div>
							<div class="clearfix"></div>
						</div>


						</div>						


	            	</div>
				</div>
			</div>


			  
				</div>
			
		</div>
	</div>
</div>			
</body>
</html>