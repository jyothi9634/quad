@extends('app')
@section('content')
<!-- Page top navigation Starts Here-->
@include('partials.page_top_navigation')

<div class="main">
	<div class="container">
		<!-- Left Nav Starts Here -->
		<div class="home-search gray-bg margin-bottom-none padding-bottom-none border-bottom-none margin-top-none">
			<div class="col-md-3 padding-none text-center">
				<div class="col-md-12 form-control-fld">

					<div class="radio-block">
						<div class="radio_inline"><input type="radio" name="lead_type" id="spot_lead_type" value="1" checked="checked" /><label for="spot_lead_type"><span></span>Spot</label></div>
						<div class="radio_inline"><input type="radio" name="lead_type" id="term_lead_type" value="2" /><label for="term_lead_type"> <span></span>Term</label></div>
					</div>
				</div>
			</div>

			@include('seller.relocation.home.domestic.search._form')
			
		</div>
		<div class="clearfix"></div>

	</div>
</div>

@include('partials.footer')
@endsection