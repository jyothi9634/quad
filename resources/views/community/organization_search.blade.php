@extends('community_app')
@section('content')
{{--*/ $search=(isset($_REQUEST['search'])) ? $_REQUEST['search']:'' /*--}}
{{--*/ $category=(isset($_REQUEST['category'])) ? $_REQUEST['category']:'' /*--}}
{{--*/ $service_id=(isset($_REQUEST['service_id'])) ? $_REQUEST['service_id']:'' /*--}}
{{--*/ $speciality_id=(isset($_REQUEST['speciality'])) ? $_REQUEST['speciality']:'' /*--}}
{{--*/ $location_id=(isset($_REQUEST['location'])) ? $_REQUEST['location']:'' /*--}}
{{--*/ $industry_id=(isset($_REQUEST['industry_id'])) ? $_REQUEST['industry_id']:'' /*--}}
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
               <i class="fa  fa-angle-right"></i> Seller <i class="fa  fa-angle-right"></i> <a href="/home">Home</a> <i class="fa  fa-angle-right"></i> Community <i class="fa  fa-angle-right"></i> Individual
            </div>
            <span class="pull-left">
               <h1 class="page-title">Organizations</h1>
            </span>
            <div class="gray-bg">
                {!! Form::open(array('url' => '#', 'id' => 'organization_search', 'name' => 'organization_search','method'=>'GET')) !!}
               <div class="col-md-12 text-center padding-none margin-bottom">

                  
                  <div class="col-md-6 col-md-offset-3 form-control-fld margin-bottom-none">
                     
                          <div class="col-md-3 padding-none">
                             
                           
                              <div class="normal-select search-type">
                                {!! Form::select('category',[''=>'Search','1'=> 'Name','2'=> 'Company','3'=> 'Location','4'=> 'Industry'],$category,['class'=>'selectpicker','id'=>'category' ]) !!}
                                 
                              </div>
                         </div>
                      
                        <div class="col-md-7 padding-none">
                            <div class="input-prepend">
                            {!! Form::text('search',$search ,['id' => 'search','class'=>'form-control form-control1']) !!}
                            </div>
                        </div>
                        <div class="col-md-2 padding-none"> 
                            <button class="btn add-btn-search btn-sm" type="submit"><i class="fa fa-search"></i></button>
                        </div>
                     
                  </div>
                  
                  <div class="col-md-3"></div>
               </div>
                {!! Form::close() !!}
               <div class="clearfix"></div>
               {!! Form::open(array('url' => '#', 'id' => 'individual_filter', 'name' => 'individual_filter','method'=>'GET')) !!}
               <input type="hidden" name='category' value="{{$category}}" >
               <input type="hidden" name='search' value="{{$search}}" >
               @if(isset($_REQUEST['search']))
               <div class="col-md-12 padding-none">
                  <div>
                    @if (Session::has('filter_services')&& Session::get('filter_services')!="")  
                     <div class="col-md-3 form-control-fld">
                        <div class="normal-select">
                           {!! Form::select('service_id',[''=>'Services']+Session::get('filter_services'),$service_id,['class'=>'selectpicker','onChange'=>'this.form.submit()']) !!}
                        </div>
                     </div>
                    @endif
                    @if (Session::has('filter_speciality')&& Session::get('filter_speciality')!="")
                     <div class="col-md-2 form-control-fld">
                        <div class="normal-select">
                           {!! Form::select('speciality',[''=>'Speciality']+Session::get('filter_speciality'),$speciality_id,['class'=>'selectpicker','onChange'=>'this.form.submit()']) !!}
                        </div>
                     </div>
                    @endif
                    @if (Session::has('filter_location')&& Session::get('filter_location')!="")
                     <div class="col-md-2 form-control-fld">
                        <div class="normal-select">
                           {!! Form::select('location',[''=>'Location']+Session::get('filter_location'),$location_id,['class'=>'selectpicker','onChange'=>'this.form.submit()']) !!}
                        </div>
                     </div>
                    @endif
                    @if (Session::has('filter_industry')&& Session::get('filter_industry')!="")
                     <div class="col-md-2 form-control-fld">
                        <div class="normal-select">
                           {!! Form::select('industry_id',[''=>'Industry']+Session::get('filter_industry'),$industry_id,['class'=>'selectpicker','onChange'=>'this.form.submit()']) !!}
                        </div>
                     </div>
                    @endif
                     <div class="col-md-3 form-control-fld">
                        <div class="normal-select">
                           <select class="selectpicker">
                              <option value="0">Rating</option>
                              <option value="0">Sample1</option>
                              <option value="0">Sample2</option>
                           </select>
                        </div>
                     </div>
                  </div>
               </div>
               @endif
               {!! Form::close() !!}
            </div>
            <!-- Table Starts Here -->
            @if(isset($_REQUEST['search']))
            <div class="table-div">
               <div class="table-data community">
                 
                  {!! $grid !!}
                  
               </div>
            </div>
             @endif
            <!-- Table Starts Here -->
         </div>
         
      </div>
         
@include('partials.footer')
@endsection