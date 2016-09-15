@extends('error')

@section('content')

	<!-- Center Div starts-->
	<div class="col-sm-12 ">       
		<div class="content">
			<div class="title">
				@if (Session::has('message'))
					<div class="flash alert-info authorized-msg bgand-border">
						<p>{{ Session::get('message') }}</p>
					</div>
				@endif
			</div>
		</div>
	</div>
@endsection        
