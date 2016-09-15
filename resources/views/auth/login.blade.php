{{--*/ $status = Request::input('status') /*--}} 
@extends('default')

@section('content')
@include('partials.page_top_navigation')
<div class="container-fluid">
@if (Session::has('message')  && Session::get('message')!='')
                <div class="flash">
                    <p class="text-success col-sm-12 text-center flash-txt alert-success pad-10">{{ Session::get('message') }}</p>
                </div>
                @endif

                <div class="login-head">
                    <h1>
                        Welcome to <span>LOGISTIKS.COM</span>
                        <p>Please fill the details below to Login</p>
                    </h1>
                </div>

    <div class="login-top">
 <div class="login-block">
        <div class="social-login margin-none">
            <a href="{{ url('/facebook?key=login') }}">
                <img src="../images/facebook.jpg" alt="">
            </a>
        
            <a href="{{ url('/google?key=login') }}">
                <img src="../images/google.jpg" alt="">
            </a>

            <a href="{{ url('/linkedin?key=login') }}">
                <img src="../images/linkedin.jpg" alt="">
            </a>
        </div>
        <br>
        <div class="text-center size"> -------------------------- Or -------------------------- </div>
        <div class="login-form">
            <div class="center-width">

                

                <div class="col-md-12 form-control-fld margin-bottom-none">
                    <div class="input-prepend">
                    </div>
                    <span class="error">
                            @if (count($errors) > 0)
                            <div class="alert alert-danger">
                                <strong>Whoops!</strong> There were some problems with your input.<br><br>
                                <ul>
                                    @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif
                    </span> 
                </div>

  <form class="form-horizontal" role="form" method="POST" action="{{ url('/auth/login') }}">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">


                <div class="col-md-12 form-control-fld margin-bottom">
                    <div class="input-prepend">
                        <input type="text" class="form-control form-control1 copy_email_to_phone" name="email" value="{{ old('email') }}" placeholder="Email Address *">
                        <input type="hidden" class="form-control form-control1" name="phone" value="" placeholder="Phone *" id="login_phone">
                    </div>
                </div>

                <div class="col-md-12 form-control-fld margin-bottom">
                    <div class="input-prepend">
                        <input type="password" class="form-control form-control1" name="password" placeholder="Password *">
                    </div>
                    
                </div>

                <div class="col-md-12 form-control-fld margin-bottom-none">
                    <div class="input-prepend">
                    </div>
                </div>

                <div class="col-md-12 form-control-fld margin-bottom">
                    <div class="input-prepend  margin-bottom-5">
                        <input type="hidden" name="is_active" id="is_active" value="1"/>
                        <button type="submit" class="btn add-btn-2">Login</button>  
                    </div>
                    <input type="checkbox" name="remember"> <span class="lbl padding-8">Remember Me</span>
                    <!--&nbsp;/&nbsp;-->
                    <a class="pull-right" href="{{ url('/password/email') }}">Forgot Your Password?</a>         
                </div>
     </form>                       

            </div>
        </div>
    </div>




    </div>
</div>
@include('partials.footer')
@if($status == 'success')
	
<script type="text/javascript">
	 $(document).ready(function(){ 
		 $('#login-modal').modal('show');
	 });
</script>

	@endif


@endsection
