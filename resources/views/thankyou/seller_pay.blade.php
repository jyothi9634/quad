@extends('default') @section('content')

@include('partials.page_top_navigation')
<div class="container">
	<div class="login-head">
		<h1>
			Thank You For Joining <span>LOGISTIKS.COM</span>
			<p>Last step to complete Registration</p>
		</h1>
	</div>
	<div class="home-block home-block-login">
		<div class="tabs">

			<div class="tab-content">
				<div id="seller" class="tab-pane fade in active">
					<div class="login-block">


						@if (Session::has('message') && Session::get('message')!='')
						<div class="flash ">
							<p
								class="text-success col-sm-12 text-center flash-txt alert-success">{{
								Session::get('message') }}</p>
						</div>
						@endif
						<div class="clearfix"></div>



						<div class="col-md-12 col-sm-12 steps-registration padding-none">




							<div class="staps">
								<form class="form-inline margin-top" role="form"
									id="subscriptionForm">
									<h4>Choose your subscription *</h4>
									
									<div class="col-sm-4 padding-none">
										<input type="radio" name="selectPeriod" value='freeTrail' id="freeTrail" checked>
										<label for="freeTrail">
										<span></span>Free Trail&nbsp;&nbsp;(Up to December 31st)</label>

									</div>
									
									<div class="col-sm-4 padding-none">
										<input type="radio" name="selectPeriod" value='quarterPeriod' id="quarterPeriod" disabled>
										<label for="quarterPeriod">
										
										<span></span>Three Months  Rs.500.00</label>

									</div>
									<div class="col-sm-4 padding-none">
										<input type="radio" name="selectPeriod"
											value='halfannualPeriod' id="halfannualPeriod" disabled>
											
										<label for="halfannualPeriod">
										
										<span></span>Six Months  Rs.750.00</label>
									</div>

									<div class="col-sm-4 padding-none">

										<input type="radio" name="selectPeriod" value='annualPeriod' id="annualPeriod" disabled>
										<label for="annualPeriod">
										<span></span>One Year  Rs.1000.00</label>

									</div>
									<div class="col-sm-4 padding-none">

										<input type="radio" name="selectPeriod" value='phantomPeriod' id="phantomPeriod" disabled>
										<label for="phantomPeriod">
										<span></span>Five Years  Rs.4000.00</label>

									</div>
									<br> <br>
									<p class="error" id="time-selection-error"></p>

								</form>
								<div class="clearfix"></div>
								<h4 class="margin-top">Logistiks.com Terms and conditions *</h4>
								<span class="pull-left"> <input type="checkbox"
									id="termCheckbox"><span class="lbl padding-8"></span>I/We accept the terms of use
								</span> <br> <br>
								<p class="error" id="term-error"></p>
								<!-- 					<input type="button" class="gry-btn" value="Cancel"> -->
								<div class="clearfix"></div>
								<h4 class="">Payment Gateway</h4>
								<h5>On payment success, you will be redirect to seller home page
									where in you need to register your vehicles/warehouse/services</h5>
							</div>
							<button class="btn add-btn green" id="pay_success">Payment
								Successful</button>
						</div>


					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@include('partials.footer');
</div>
@endsection
