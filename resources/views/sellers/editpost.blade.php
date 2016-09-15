@extends('app')
@section('content')
 <h1>Create Book</h1>
    {!! Form::open(['url' => 'seller','id'=>'posts-form-lines']) !!}
    
	
    {!! Form::close() !!}
@endsection
