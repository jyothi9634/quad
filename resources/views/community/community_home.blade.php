@extends('community_app')
@section('content')

<div class="container-inner">
	
	<!-- Page Center Content Starts Here -->
	<div class="col-lg-7 col-md-6 col-sm-12 col-xs-12 main-middle">
		<div class="block">
			<div class="tab-nav underline">
				@include('partials.community_page_top_navigation')
			</div>
		</div>
	</div>
		
	 <div class="main">
	     <div class="container community">
	     	 <div class="crum-2">                  
	           <i class="fa  fa-angle-right"></i> Seller <i class="fa  fa-angle-right"></i> <a href="/home">Home</a> <i class="fa  fa-angle-right"></i> 
                   <a href="/home">Community</a>
              </div>
                  <span class="pull-left">
                     <h1 class="page-title">Community</h1>
                  </span>
	         <div class="col-md-12 landing-page padding-none">
	               <div class="profile-banner">
	                  <div class="col-md-12 banner padding-none"><span>Our <br>Community</span><img src="../images/community-banner.jpg" class="img-responsive"></div>
	                  <div class="clearfix"></div>
	               </div>
	               <div>
	                  <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p>
	               </div>
	          </div>
	            <div class="clearfix"></div>
	         </div>
	 </div>
</div>
<div class="clearfix"></div>	

@include('partials.footer')
@endsection

