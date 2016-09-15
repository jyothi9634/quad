@extends('app')
@section('content')
        <!-- Page top navigation Starts Here-->
@include('partials.page_top_navigation')
<div class="main">
    <div class="container">
        @include('seller.relocation.home.gblmobility.search._form')
        <div class="clearfix"></div>    
    </div>
</div>

@include('partials.footer')
@endsection