<!--  if(isset($update_buyer)){
	$model->attributes=$update_buyer->attributes;

}-->
@extends('app') @section('content')
@include('partials.page_top_navigation')
<div class="clearfix"></div>
<div class="login-head">
					<h1 class='margin-bottom-none'>
						<span>LOGISTIKS.COM</span>
						<p>Edit Profile</p>
					</h1>
				</div>
<div class="main">
			<div class="container reg_crumb">
				
					    @if (Session::has('success_message'))
							<div class="flash ">
								<p class="text-success col-sm-12 text-center flash-txt alert-success">
								{{ Session::get('success_message') }}</p>
							</div>
							@endif
							@if (Session::has('error_message'))
							<div class="flash ">
								<p class="text-alert col-sm-12 text-center flash-txt alert-danger">
								{{ Session::get('error_message') }}</p>
							</div>
							@endif

				<div class="home-block home-block-login">
					<div class="tabs">
						
						  <div class="tab-content">
							<div id="buyer" class="tab-pane fade in active">
								<div class="login-block">
									<div class="login-form login-form-2">
										<div class="center-width">
										{!! Form::open(array('url' => 'register/edit_buyer/'.$buyer_id,'id' =>'buyer-details-form', 'class'=>'form-inline margin-top','enctype' => 'multipart/form-data' )) !!}
										
										
											<div class="col-md-12 padding-none">
												<label for="" class="col-md-12 padding-none">Name of the Person</label>
												<div class="col-md-6 form-control-fld">
													<div class="input-prepend">
														{!! Form:: text ('firstname', $buyer_details->firstname , array( 'class'=>'form-control letterValdiation form-control1','id'=>'txt_user_first_name','placeholder'=>'First Name*', 'maxlength'=>'30' )) !!}

													</div>
												</div>
												<div class="col-md-6 form-control-fld">
													<div class="input-prepend">
													{!! Form:: text ('lastname',  $buyer_details->lastname , array( 'class'=>'form-control letterValdiation form-control1', 'id'=>'txt_user_last_name', 'placeholder'=>'Last Name*', 'maxlength'=>'30' )) !!}
													</div>
												</div>
											</div>
											<div class="col-md-12 padding-none space-top">
												<label for="" class="col-md-12 padding-none">Personal Details</label>
												<div class="col-md-12 form-control-fld">
													<div class="input-prepend">
													{!! Form::textarea('address', $buyer_details->address ,array( 'class'=>'form-control form-control1', 'id'=>'txt_user_address','placeholder'=>'Address*', 'cols'=>'48', 'rows'=>'3','maxlength'=>'350' )) !!}
							
													</div>
												</div>
												<div class="col-md-6 form-control-fld">
													<div class="input-prepend">
													{!! Form:: text ('pincode', $buyer_details->pincode ,array( 'class'=>'form-control form-control1 numericvalidation','id'=>'txt_user_pincode', 'placeholder'=>'Pincode*','maxlength'=>'6' )) !!}
													{!! Form:: hidden ('pincode_hidden', $buyer_details->principal_place,array( 'class'=>'form-control form-control1','id'=>'hidden_user_pincode' )) !!}
													</div>
												</div>
												<div class="col-md-6 form-control-fld">
													<div class="input-prepend">
													{!! Form:: text ('landline',$buyer_details->landline , array( 'class'=>'form-control numericvalidation form-control1','id'=>'txt_user_landline', 'placeholder'=>'Lanline Number','maxlength'=>'15' )) !!}
														
													</div>
												</div>
												<div class="col-md-6 form-control-fld">
													<div class="input-prepend">
													{!! Form:: text ('mobile', $buyer_details->mobile , array( 'class'=>'form-control numericvalidation form-control1','id'=>'txt_user_mobile', 'placeholder'=>'Mobile Number*','maxlength'=>'10' )) !!}
													</div>
												</div>
												<div class="col-md-6 form-control-fld">
													<div class="input-prepend">
													{!! Form:: text('contact_email', $buyer_details->contact_email, array( 'class'=>'form-control form-control1', 'id'=>'txt_user_email_id', 'placeholder'=>'E-Mail Id*' )) !!}
													</div>
												</div>
												<div class="clearfix"></div>
												<div class="col-md-6 form-control-fld">
													<div class="input-prepend">
													{!! Form:: text('principal_place', $buyer_details->principal_place, array( 'class'=>'form-control form-control1','readonly','id'=>'principal_place','placeholder'=>'Principal Place of business' )) !!}
													</div>
												</div>
												<div class="col-md-6 form-control-fld">
													<div class="input-prepend">
													{!! Form:: text ('company_name',$buyer_details->name, array( 'class'=>'form-control form-control1','id'=>'company_name', 'placeholder'=>'Company Name*','maxlength'=>'50' )) !!}
													</div>
												</div>
												<div class="clearfix"></div>
												<div class="col-md-6 form-control-fld">
													 <div class="normal-select">
													{!! Form::select('lkp_industry',(['' => 'Select Industry Type*'] + $lkp_industry), $buyer_details->lkp_industry_id, ['class' => 'selectpicker','id' => 'lkp_industry']) !!}
													</div>
												</div>
												
												<div class="col-md-6 form-control-fld">
													<div class="input-prepend">{!! Form:: text ('pannumber',
													$buyer_details->pannumber ,array('class'=>'form-control
													form-control1', 'id'=>'txt_company_pannumber','maxlength'=>'10',
													'placeholder'=>'PAN Number*')) !!}
													</div>
												</div>
												
												
												<div class="col-md-12 padding-none space-top">
												<label for="" class="col-md-12 padding-none">Business Description</label>
													<div class="col-md-12 form-control-fld">
														<div class="input-prepend">{!! Form:: textarea ('description_user',
															$buyer_details->description, array( 'class'=>'form-control form-control1',
															'id'=>'txt_description','placeholder'=>'Description',
															'maxlength'=>'240', 'rows'=>'5' )) !!}</div>
													</div>
												</div>
												<div class="col-md-12 padding-none space-top">
													<label for="" class="col-md-12 padding-none">User Upload Documents</label>
													<div class="col-md-6">	
														@if($buyer_details->user_pic)
															{{--*/ $user_pic_ext = pathinfo($buyer_details->user_pic, PATHINFO_EXTENSION) /*--}}
															@if(file_exists(BUYERUPLOADPATH.$buyer_details->user_id.'/'.str_replace('.'.$user_pic_ext,'_124_73.'.$user_pic_ext,$buyer_details->user_pic)))															
																<img class="img-responsive" src="{{URL::to(BUYERUPLOADPATH.$buyer_details->user_id.'/'.str_replace('.'.$user_pic_ext,'_124_73.'.$user_pic_ext,$buyer_details->user_pic))}}">
															@elseif(file_exists(SELLERUPLOADPATH.$buyer_details->user_id.'/'.str_replace('.'.$user_pic_ext,'_124_73.'.$user_pic_ext,$buyer_details->user_pic)))															
																<img class="img-responsive" src="{{URL::to(SELLERUPLOADPATH.$buyer_details->user_id.'/'.str_replace('.'.$user_pic_ext,'_124_73.'.$user_pic_ext,$buyer_details->user_pic))}}">
															@endif
														@endif
													</div>
													<div class="col-md-6">	
														@if($buyer_details->logo)
															{{--*/ $logo_ext = pathinfo($buyer_details->logo, PATHINFO_EXTENSION) /*--}}
															@if(file_exists(BUYERUPLOADPATH.$buyer_details->user_id.'/'.str_replace('.'.$logo_ext,'_124_73.'.$logo_ext,$buyer_details->logo)))
																<img class="img-responsive" src="{{URL::to(BUYERUPLOADPATH.$buyer_details->user_id.'/'.str_replace('.'.$logo_ext,'_124_73.'.$logo_ext,$buyer_details->logo))}}">
															@elseif(file_exists(SELLERUPLOADPATH.$buyer_details->user_id.'/'.str_replace('.'.$logo_ext,'_124_73.'.$logo_ext,$buyer_details->logo)))
																<img class="img-responsive" src="{{URL::to(SELLERUPLOADPATH.$buyer_details->user_id.'/'.str_replace('.'.$logo_ext,'_124_73.'.$logo_ext,$buyer_details->logo))}}">
															@endif
														@endif
													</div>
													<div class="clearfix"></div>
													<div class="col-md-6 form-control-fld">
														<div class="input-prepend">
															<span class="btn btn-default btn-file btn-upload">
															 Profile Picture {!! Form:: file('profile_picture',array('class'=>'fileInput','id'=>'txt_profile_picture','placeholder'=>'','accept'=>'jpg|jpeg|png|PNG|JPEG|JPG')) !!} 
															 </span>
														</div>
														<p class="form-group pull-left overflow-hide"
															id="profile_picture"></p>
													</div>

													<div class="col-md-6 form-control-fld">
														<div class="input-prepend">
															<span class="btn btn-default btn-file btn-upload">
															 Logo {!! Form:: file ('logo_user',array('class'=>'form-control margin-bottom input-sm fileInput','id'=>'txt_logo_user','placeholder'=>'','accept'=>'jpg|jpeg|png|PNG|JPEG|JPG')) !!} 
															</span>
														</div>
														<p class="form-group pull-left overflow-hide"
															id="logo_user"></p>

													</div>
												</div>
												
											</div>
											<div class="col-md-4 form-control-fld space-top pull-right">
											{!! Form::submit('Update', array( 'class'=>'btn add-btn-2 pull-right',
											'onclick'=>'return buyerRegistration();')) !!}
												
											</div>
											
{!! Form::close() !!}
										</div>
									</div>
								</div>     
							</div>
							    
						  </div>
					</div>
					<div class="clearfix"></div>
				</div>
				
			</div>
			</div>
			@include('partials.footer')
</div>

@endsection
