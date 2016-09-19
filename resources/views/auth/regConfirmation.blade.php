@extends('app')

@section('content')

	@include('partials.page_top_navigation')

	<div class="main" style="padding:15px 19px 235px;">

		

		<div class="container">
			
			<div class="container-1">
  <div class="registration-main-1">
    <h1>Thank you for <span>REGISTRATION</span></h1>
    
       <h2></br></h2>
        <div class="registration-individual-row2 text-center">
        <a href="/marketplaceRegistration" class="button-red-1">Update PROFILE</a>
        <a href="/auth/login" class="button-gray-1">ASK ME LATER</a>
     	  
      </div>
       
      <br>

  </div>
</div>

			<div class="clearfix"></div>

		</div>
	</div>

@include('partials.footer')
@endsection
