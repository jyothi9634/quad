@extends('app')

@section('content')
<div class="main-container">

    @if (Session::has('message')  && Session::get('message')!='')
    <div class="flash">
        <p class="text-success col-sm-12 text-center flash-txt alert-success">{{ Session::get('message') }}</p>
    </div>
    @endif
    <div class="container container-inner">
        <div
            class="col-md-4 col-sm-4 col-xs-12 div-center col-md-offset-4 col-sm-offset-4 text-center">
            <h5 class="normal-text">
                Welcome to <span class="red-text">LOGISTIKS.COM</span>
            </h5>
            <h4 class="text-margin">like to associate with logistiks.com as</h4>
            {!! Form::open(array('url' => 'socialregister', 'name'=>'registerForm',
            'id' =>'login-form', 'class'=>'form-inline input-full' )) !!}
            <div class="col-md-6">
                <button class="circle margin-bottom is_business" value="0">
                    Individual
                </button>

            </div>
            <div class="col-md-6">
                <button class="circle margin-bottom is_business text-margin" value="1">Business</button>
                <input type="hidden" name="is_business" id="is_business" value="">
                <input type="hidden" name="user_email" value="{{$email}}">
                <input type="hidden" name="password" value="{{rand()}}">
                <input type="hidden" name="identifier" value="{{$identifier}}">
                <input type="hidden" name="provider" value="{{$provider}}">
                <input type="hidden" name="username" value="{{$username}}">
            </div>
            <div >
                {!! Form::submit('Sign Up', array(
                'name'=>'submitRegister','id'=>'submitRegister', 'style'=>'display:none;' )) !!}
            </div>
            {!! Form:: close() !!}
        </div>
    </div>
</div>
@endsection