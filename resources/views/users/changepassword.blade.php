@extends('app') @section('content')
@include('partials.page_top_navigation') 


<div class="main-container">
<div class="main"> 
	<div class="container reg_crumb changePassword">
		<span class="pull-left"><h1 class="page-title">Change Password</h1></span>	   
		@if (Session::has('message') && Session::get('message')!='')
			<div class="flash">
				<p class="text-success col-sm-12 text-center flash-txt alert-success">{{ Session::get('message') }}</p>
			</div>
		@endif	
		@if (Session::has('error') && Session::get('error')!='')
		    <div class="flash">
		        <p class="text-danger col-sm-12 text-center flash-txt alert-danger">{{ Session::get('error') }}</p>
		    </div>
		@endif
		<div class="home-block home-block-login">
			<div class="tabs">

				<div class="tab-content">
					<div id="buyer" class="tab-pane fade in active">
						<div class="login-block changepwd-block">
							<div class="login-form login-form-2">
								<div class="center-width">
											
									{!! Form::open(array('url' => array('changepassword'), 'id' => 'changepasswordform', 'method' => 'post' )) !!}	

										<div class="col-md-4 form-control-fld">
											<label for="">Old Password</label>
											<div class="input-prepend">
												@if ($errors->has('old_password'))
												    <input type="password" value="" name="old_password" id="old_password" class="form-control form-control1" autofocus="autofocus"> 
													<p class="error-msg">{!!$errors->first('old_password')!!}</p>
												@else
													<input type="password" value="" name="old_password" id="old_password" class="form-control form-control1"> 
												@endif
											</div>
										</div>
										
										<div class="col-md-4 form-control-fld">
											<label for="">New Password</label>
											<div class="input-prepend">
												@if ($errors->has('password'))
												    <input type="password" value="" name="password" id="password" class="form-control form-control1" autofocus="autofocus"> 
													<p class="error-msg">{!!$errors->first('password')!!}</p>
												@else
													<input type="password" value="" name="password" id="password" class="form-control form-control1"> 
												@endif
											</div>
										</div>

										<div class="col-md-4 form-control-fld">
											<label for="">Confirm New Password</label>
											<div class="input-prepend">
												@if ($errors->has('password_confirmation'))
													<input type="password" value="" name="password_confirmation" id="password_confirmation" class="form-control" autofocus="autofocus">
													<p class="error-msg">{!!$errors->first('password_confirmation')!!}</p>
												@else
													<input type="password" value="" name="password_confirmation" id="password_confirmation" class="form-control form-control1">
													@endif								
											</div>
										</div>

										<div class="col-md-4 col-md-offset-4 form-control-fld space-top">
										{!! Form::submit('Submit', array( 'class'=>'btn
										add-btn-2','id'=>'submitChangePassword' )) !!}
												
										</div>
									{!! Form::close() !!}   
								</div>
							</div>
						</div>
					</div>
				</div>
			
			</div>
		</div>

</div>
	</div>
</div>
@endsection
