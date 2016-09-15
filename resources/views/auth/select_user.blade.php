@extends('app')

@section('content')
<div class="main-container">


	<div class="container container-inner">
		@if (Session::has('message')  && Session::get('message')!='')
	<div class="flash">
	<p class="text-success col-sm-12 text-center flash-txt alert-success">{{ Session::get('message') }}</p>
	</div>
	@endif
		<div
			class="col-md-6 col-sm-6 col-xs-12 div-center col-md-offset-2 col-sm-offset-2 text-center">
			<h5 class="normal-text">
				Welcome to <span class="red-text">LOGISTIKS.COM</span>
			</h5>
			<h4 class="text-margin">like to associate with logistiks.com as</h4>
			<div class="col-md-4">
					<button class="circle circle1 margin-bottom roleSelector"  value="1">Buyer</button>
					<p>
						I/we are interested in logistiks.com <br>as customer
					</p>
					<div class="free-hover"><img src="{{url('images/member.png')}}"></div>
				</div>
			<div class="col-md-4">
						<button class="circle circle2 margin-bottom roleSelector " value="2">Seller</button>
					<p>
						I/we offer our services to customer through logistiks.com
					</p>
					<div class="free-hover2"><img src="{{url('images/member2.png')}}"></div>
				</div>
				<div class="col-md-4">
						<button class="circle circle3 margin-bottom roleSelector" value="2">Both</button>
					<p>
						I/we are interested in associating as buyer and seller in logistiks.com.
					</p>
					<div class="free-hover3"><img src="{{url('images/member3.png')}}"></div>
				</div>
				
				
			
		</div>
	</div>
</div>
@endsection