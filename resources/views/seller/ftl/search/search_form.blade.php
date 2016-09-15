@extends('app')
@section('content')

<!-- Page top navigation Starts Here-->
@include('partials.page_top_navigation')
<div class="main">
	<div class="container container-inner">

		<!-- Left Nav Starts Here -->
		<div class="home-search gray-bg margin-bottom-none padding-bottom-none border-bottom-none margin-top-none">
			
			@include('seller.ftl.search._form')
		
		</div>
		<div class="clearfix"></div>

	</div>
</div>
@include('partials.footer')
<script type="text/javascript">
$(document).ready(function(){
	var ftlSearchType = $("input[name=lead_type]:checked").val();
	if(ftlSearchType == 1){
		$("#showhide_spot").show();
		$("#showhide_term").hide();
	}
	if(ftlSearchType == 2){
		$("#showhide_spot").hide();
		$("#showhide_term").show();
	}
});
</script>
@endsection