@extends('app')
@section('content')
<!-- Page top navigation Starts Here-->
@include('partials.page_top_navigation')
<div class="main">
	<div class="container">
		<!-- Search Form Partial -->
		@include('relocationglobal.sellers._searchform')
		<div class="clearfix"></div>
	</div>
</div>
@include('partials.footer')
@endsection