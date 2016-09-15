@extends('default') @section('content')
@include('partials.page_top_navigation')
		<div class="clearfix"></div>
<div class="main">

	<div class="container">
		@if (Session::has('message') && Session::get('message')!='')
	<div class="flash">
		<p class="text-success col-sm-12 text-center flash-txt alert-success">{{
			Session::get('message') }}</p>
	</div>
	@endif
		<div class="login-head">
			<h1> Thank You For Joining <span>LOGISTIKS.COM</span>
				<p>Confirmation mail has been sent to registered mail id.</br> Please verify email-id by clicking on activation link to complete registration process.</p>
			</h1>
			<div class="conform">
				<img src="../images/confirm.jpg" alt="Logistiks" />
				<div class="clearfix"></div>
				{{--<button class="btn add-btn green margin-top">Confirm email</button>--}}
			</div>
		</div>


	</div>
</div>

@include('partials.footer')
</div>
@endsection
