@extends('community_app')
@section('content')
{{--*/ $search=(isset($_REQUEST['search'])) ? $_REQUEST['search']:'' /*--}}
<!-- Page Center Content Starts Here -->
	<div class="col-lg-7 col-md-6 col-sm-12 col-xs-12 main-middle">
		<div class="block">
			<div class="tab-nav underline">
				@include('partials.community_page_top_navigation')
			</div>
		</div>
	</div>	
         <!-- Inner Menu Ends Here -->
     <div class="main">
     
        <div class="container community">
            <div class="crum-2">
               <i class="fa  fa-angle-right"></i> Seller <i class="fa  fa-angle-right"></i> <a href="/home">Home</a> <i class="fa  fa-angle-right"></i> Community <i class="fa  fa-angle-right"></i> Groups
            </div>
            <span class="pull-left">
               <h1 class="page-title">Groups</h1>
            </span>
            <a href="/community/creategroup" class="btn post-btn pull-right submit-data1">Create Group</a>
            <div class="gray-bg">
                {!! Form::open(array('url' => '#', 'id' => 'group_search', 'name' => 'group_search','method'=>'GET')) !!}
               <div class="col-md-12 text-center padding-none margin-bottom">
                  
                  <div class="col-md-4 col-md-offset-4 form-control-fld margin-bottom-none">
                         
                        <div class="col-md-11 padding-none">
                            <div class="input-prepend">
                                {!! Form::text('search',$search ,['id' => 'search','class'=>'form-control form-control1']) !!}
                            </div>
                        </div>
                        <div class="col-md-1 padding-none"> 
                            <button class="btn add-btn-search btn-sm" type="submit"><i class="fa fa-search"></i></button>
                        </div>
                     </div>

               </div>
               <div class="clearfix"></div>
               
               {!! Form::close() !!}
            </div>
            <!-- Table Starts Here -->
            @if(isset($_REQUEST['search']))
            <div class="table-div">
               <div class="table-data community">
                  
                 {!! $grid !!}
                 <input type="hidden" name='search' value='{{$search}}'>
               </div>
            </div>
            @endif
            <!-- Table Starts Here -->
         </div>
         
      </div>
         
@include('partials.footer')
@endsection