@extends('default') @section('content')
@include('partials.page_top_navigation')
<div class="container-fluid">
<div class="login-head">
					<h1>
						Welcome to <span>LOGISTIKS.COM</span>
						<p>Please enter your email to reset your password</p>
					</h1>
				</div>

	<div class="login-block">
		<div class="social-login margin-none">
			<a href="{{ url('/facebook?key=login') }}"> <img
				src="../images/facebook.jpg" alt="">
			</a> <a href="{{ url('/google?key=login') }}"> <img
				src="../images/google.jpg" alt="">
			</a> <a href="{{ url('/linkedin?key=login') }}"> <img
				src="../images/linkedin.jpg" alt="">
			</a>
		</div>
		<br>
		<div class="text-center size">
		-------------------------- Or --------------------------
		</div>

		
		<h1 class="page-title btn-block text-center border-none">Forgot Password</h1>
		<div class="login-form forgot-details-form">
			<div class="forgot-password center-align">
				<div class="panel panel-default">
					<div class="panel-heading">Reset Password</div>
					<div class="panel-body">
						@if (session('status'))
						<div class="alert alert-success">{{ session('status') }}</div>
						@endif @if (count($errors) > 0)
						<div class="alert alert-danger">
							<strong>Whoops!</strong> There were some problems with your
							input.<br>
							
								@foreach ($errors->all() as $error)
								{{ $error }}
								 @endforeach
							
						</div>
						@endif

						<form class="form-horizontal" role="form" method="POST"
							action="{{ url('/password/email') }}">
							<input type="hidden" name="_token" value="{{ csrf_token() }}">

							<div class="col-xs-12 padding-none">
								<div class=" margin-top">
									<input type="email" class="form-control form-control1"
										placeholder="E-Mail Address" name="email" autofocus
										value="{{ old('email') }}">
								</div>
							</div>
				<div class="clearfix"></div>
							
							<div class="form-group">
								<div class="margin-top text-center center-align">
									<button type="submit" class="btn add-btn">Send Password Reset Link</button>
								</div>
							</div>
						</form>
					</div>
				</div>

			</div>
		</div>
	</div>

	@include('partials.footer')
</div>

@endsection
