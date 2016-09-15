@extends('community_app')
@section('content')

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
     
    	@if(Session::has('gsumsg')) 
        <div class="flash">
		<p class="text-success col-sm-12 text-center flash-txt alert-success">
		{{ Session::get('gsumsg') }}
		</p>
		</div>
		@endif
     
         <div class="container community">
            <div class="crum-2">
               <i class="fa  fa-angle-right"></i> Seller <i class="fa  fa-angle-right"></i> <a href="/home">Home</a> <i class="fa  fa-angle-right"></i> Community</div>
            <span class="pull-left">
               <h1 class="page-title">Community</h1>
            </span>
            
            {!! Form::open(['url' =>'createcommunitynewgroup','id' => 'community_create_group_valid' ,'files'=>true,  'autocomplete'=>'off']) !!}     
            <div class="gray-bg">
               <div class="col-md-12 padding-none">
                  <div class="col-md-6 form-control-fld">  
                    <div class="input-prepend">                  
                     {!! Form::text('community_group_name',null,array('class'=>'form-control form-control1','placeholder'=>'Group Name *','id'=>'community_group_name', 'maxlength' => '200')) !!}
                    </div>
                     <span id="check_group_exists" class="error"></span>
                  </div>
                  <div class="col-md-6 form-control-fld"> 
                    <span class="pull-left">                   
                        {!! Form::checkbox('community_private_check','', null, ['id' => 'community_private_check','class' => 'community_private_check']) !!}
                        <span class="lbl padding-8"></span>
                     </span>
                      <p class="pull-left padding-top-5">Private <span class="font12">(your group will be public by default)</span></p>
                  </div>
               </div>
               <div class="col-md-6 form-control-fld pic-upload-fld">
                  <div class="input-prepend">
                    <span class="add-on"><i class="fa fa-camera"></i></span>
<!--                      <input type="text" placeholder="Upload Logo" id="" class="form-control"> -->
                     <div class="upload-fld">			
						            <input type="file" name="community_group_logo" class="form-control form-control1 update_txt" value="" id="community_group_logo" />
		                  </div>
                  </div>
               </div>
               <div class="col-md-6 form-control-fld">
                  <div class="input-prepend">   
                      <span class="pull-left">
                          <input type="checkbox" name="community_logo_agree" id="community_logo_agree" >
                          <span class="lbl padding-8"></span>
                      </span>
                      <span class="chk-align"> *I acknowledge and agree that the logo/image I am uploading does not infringe upon any third party copyrights, trademarks, or other proprietary rights or otherwise violate the User Agreement.</span>
                  </div>
               </div>
               <div class="col-md-12 form-control-fld margin-top">
                <div class="input-prepend">                      
                  {!! Form::textarea('community_description', null, ['id' => 'community_description', 'class' => 'form-control form-control1', 'placeholder' => 'Description *', 'cols' => '20', 'rows' => '6','maxlength' => '3000']) !!}
                </div>
               </div>
               <div class="col-md-6 form-control-fld">
                <div>   
                    <div>   
                  {!! Form::checkbox('community_terms_agree',1, null, ['id' => 'community_terms_agree']) !!}   
                  <span class="lbl padding-8"></span>
                  Check to confirm you have read and accept the <span class="red">Terms of Service.</span>
                </div>
                    </div>
               </div>
               <div class="clearfix"></div>
               <div class="col-md-12 form-control-fld">
        				<input type="submit" value="Submit" class="btn red-btn pull-right" >						
        			</div>               
            </div>  
            {!! Form::close() !!}          
         </div>
         
      </div>
         
@include('partials.footer')
@endsection