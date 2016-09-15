@extends('default') @section('content')
@include('partials.page_top_navigation')
<div class="container-fluid">
	@if (Session::has('message') && Session::get('message')!='')
	<div class="flash">
		<p
			class="text-success col-sm-12 text-center flash-txt alert-success pad-10">{{
			Session::get('message') }}</p>
	</div>
	@endif
	<div class="">
		<div class="login-block">
			<div class="social-login margin-none">
				<a href="{{ url('/facebook?key=login') }}"> <img
					src="../../images/facebook.jpg" alt="">
				</a> <a href="{{ url('/google?key=login') }}"> <img
					src="../../images/google.jpg" alt="">
				</a> <a href="{{ url('/linkedin?key=login') }}"> <img
					src="../../images/linkedin.jpg" alt="">
				</a>
			</div>
			<br>
			<div class="text-center size">-------------------------- Or
				--------------------------</div>
			<div class="login-form">
				<div class="center-width">

						<div class="login-form login-form-2">
							<div class="panel panel-default">
								<div class="panel-heading">Reset Password</div>
								<div class="panel-body">
									@if (count($errors) > 0)
									<div class="alert alert-danger">
										<strong>Whoops!</strong> There were some problems with your
										input.<br>
										<br>
										<ul>
											@foreach ($errors->all() as $error)
											<li>{{ $error }}</li> @endforeach
										</ul>
									</div>
									@endif

									<form class="form-horizontal" role="form" method="POST"
										action="{{ url('/password/reset') }}">
										<input type="hidden" name="_token" value="{{ csrf_token() }}">
										<input type="hidden" name="token" value="{{ $token }}">

										<div class="col-md-12 padding-none">
											<label for="" class="col-md-12 padding-none">E-Mail Address</label>


											<div class="col-md-12 form-control-fld">
												<div class="input-prepend">
													<input type="email" class="form-control form-control1"
														name="email" value="{{ old('email') }}">
												</div>
											</div>
										</div>

										<div class="col-md-12 padding-none">
											<label for="" class="col-md-12 padding-none">Password</label>
											<div class="col-md-12 form-control-fld">
												<div class="input-prepend">
													<input type="password" class="form-control form-control1"
														name="password">
												</div>
											</div>
										</div>

										<div class="col-md-12 padding-none">
											<label for="" class="col-md-12 padding-none">Confirm Password</label>
											<div class="col-md-12 form-control-fld">
												<div class="input-prepend">
													<input type="password" class="form-control form-control1"
														name="password_confirmation">
												</div>
											</div>
										</div>

										
										<div class="col-md-12 padding-none">
											<div class="col-md-6 col-md-offset-4">
												<button type="submit" class="btn btn-primary">Reset Password
												</button>
											</div>
										</div>
									</form>
								</div>
						
						</div>
					</div>


				</div>
			</div>
		</div>




	</div>
</div>
@include('partials.footer')
</div>
@endsection
