<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,width=device-width,height=device-height,target-densitydpi=device-dpi,user-scalable=yes" />
	<title>Logistiks</title>
	<script src="{{ asset('/js/jquery.min.js') }}"></script>
	<link rel="stylesheet" type="text/css" media="screen and (min-width: 992px)" href="{{ asset('/css/sass/stylesheets/style.css') }}">
	<link rel="stylesheet" href="{{ asset('/css/sass/stylesheets/font-awesome.css') }}">
	<link rel="stylesheet" type="text/css" media="screen and (min-width: 992px)" href="{{ asset('/css/sass/stylesheets/stylesheet.css') }}"  />
        <link rel="stylesheet" href="{{ asset('/css/sass/stylesheets/bootstrap.css') }}">
        
	<link rel="stylesheet" href="{{ asset('/css/sass/stylesheets/custom.css') }}">
    <link rel="stylesheet" type="text/css" media="screen and (min-width: 768px) and (max-width: 1139px)" href="{{ asset('/css/sass/stylesheets/tablet.css') }}">
    <link rel="stylesheet"  type="text/css" media="screen and (min-width: 250px) and (max-width: 767px)" href="{{ asset('/css/sass/stylesheets/mobile.css') }}">


	<script src="{{ asset('/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('/js/jquery-ui.js') }}"></script>
      <script src="{{ asset('/js/jquery.blockUI.js') }}"></script>

	<script src="{{ asset('/js/jquery.slimscroll.js') }}"></script>


	
	

</head>
<body>
	
	<!-- Main Container Starts Here-->
		
			@yield('content')
		
	<!-- Main Container Ends Here-->		
	
	
	
	
</body>
</html>