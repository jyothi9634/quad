@inject('commoncomponent', 'App\Components\CommonComponent')

<div class="col-md-12 network-info">
   <div class="col-md-1 padding-none">
      <div class="profile-pic">
        @if($profiledetails->lkp_role_id == 2)
			{{--*/ $url = "uploads/seller/$profiledetails->id/" /*--}}
        @else
        	{{--*/ $url = "uploads/buyer/$profiledetails->id/" /*--}}
        @endif
        {{--*/ $getuserpic = $url.$profiledetails->user_pic /*--}}
        {{--*/ $userpic =  $commoncomponent::str_replace_last( '.' , '_94_92.' , $getuserpic ) /*--}}
        @if($profiledetails->user_pic != '')
	          @if(\File::exists($userpic))
	                     <img src="{{url($userpic)}}"/>
	         @else   
	            <i class="fa fa-user"></i>
	         @endif  
         
         @else   
            <i class="fa fa-user"></i>
         @endif 
      </div>
   </div>
   <div class="col-md-11 padding-right-none">
      <div class="col-md-5 title padding-none">
      
      	
         <span><a href="/network/profile/{{$profiledetails->id }}">{{ ucwords($profiledetails->username) }}</a></span>
         <div class="red">
            <i class="fa fa-star"></i>
            <i class="fa fa-star"></i>
            <i class="fa fa-star"></i>
         </div>
      </div>
      <div class="pull-right text-right">
         <div class="info-links">
            <a class="show-data-link">
               <span class="show-icon">+</span><span class="hide-icon">-</span> Details
            </a>
         </div>
      </div>
      <div class="col-md-12 padding-none show-data-div details more-details">
      
      {{-- // Buyer Section --}}   
      @if( Auth::user()->lkp_role_id == BUYER )
         
         {{-- // Seller Business Account --}}
         
         @if($profiledetails->is_business)
         <ul>
            <li>Year Joined - <span>{{ $userDetails->joining_year or 'n/a' }}</span><br>
               Followers - <span>{{$followerCount}}</span><br>
               Partners - <span>{{$partnerCount}}</span><br>
               Recomendations - <span>{{$recommendationCount}}</span>
            </li>

            <li>Business Type - <span>{{ $userDetails->business_type_name or 'n/a' }}</span><br>
               Main Products/Services - <span>***</span><br>
               Industry - <span>{{ $userDetails->industry_name or 'n/a' }}</span><br>
               Location - <span>{{ $userDetails->principal_place or 'n/a' }}</span>
            </li>

            <li>Year Established - <span>{{ $userDetails->established_year or 'n/a' }}</span><br>
               Employees - <span>{{ $userDetails->employee_strength or 'n/a' }}</span><br>
               Annual Turnover - <span>{{ $userDetails->current_turnover or 'n/a' }}</span>
               <br>Main Markets - <span>{{ $userDetails->main_markets or 'n/a' }}</span>
            </li>
         </ul>
         @else
         <ul>
            <li>Year Joined - <span>***</span><br>
               Followers - <span>{{$followerCount}}</span><br>
               Partners - <span>{{$partnerCount}}</span><br>
               Recomendations - <span>{{$recommendationCount}}</span>
            </li>

            <li>Business Type - <span>***</span><br>
               Main Products/Services - <span>***</span><br>
               Industry - <span>{{ $userDetails->industry_name or 'n/a' }}</span><br>
               Location - <span>{{ $userDetails->principal_place or 'n/a' }}</span>
            </li>

            <li>Year Established - <span>***</span><br>
               Employees - <span>***</span><br>
               Annual Turnover - <span>***</span>
               <br>Main Markets - <span>***</span>
            </li>
         </ul>
         @endif

      @else
      {{-- // Seller Section--}}

         {{-- // Seller Business Account --}}
         
         @if($profiledetails->is_business)
         <ul>
            <li>Year Joined - <span>{{ $userDetails->joining_year or 'n/a' }}</span><br>
               Followers - <span>{{$followerCount}}</span><br>
               Partners - <span>{{$partnerCount}}</span><br>
               Recomendations - <span>{{$recommendationCount}}</span>
            </li>

            <li>Business Type - <span>{{ $userDetails->business_type_name or 'n/a' }}</span><br>
               Main Products/Services - <span>***</span><br>
               Industry - <span>{{ $userDetails->industry_name or 'n/a' }}</span><br>
               Location - <span>{{ $userDetails->principal_place or 'n/a' }}</span>
            </li>

            <li>Year Established - <span>{{ $userDetails->established_year or 'n/a' }}</span><br>
               Employees - <span>{{ $userDetails->employee_strength or 'n/a' }}</span><br>
               Annual Turnover - <span>{{ $userDetails->current_turnover or 'n/a' }}</span>
               <br>Main Markets - <span>{{ $userDetails->main_markets or 'n/a' }}</span>
            </li>
         </ul>
         @else
         <ul>
            <li>Year Joined - <span>{{ $userDetails->joining_year or 'n/a' }}</span><br>
               Followers - <span>{{$followerCount}}</span><br>
               Partners - <span>{{$partnerCount}}</span><br>
               Recomendations - <span>{{$recommendationCount}}</span>
            </li>

            <li>Business Type - <span>***</span><br>
               Main Products/Services - <span>***</span><br>
               Industry - <span>{{ $userDetails->industry_name or 'n/a' }}</span><br>
               Location - <span>{{ $userDetails->principal_place or 'n/a' }}</span>
            </li>
			
            <li>Year Established - <span>{{ $userDetails->established_year or 'n/a' }}</span><br>
               Employees - <span>{{ $userDetails->employee_strength or 'n/a' }}</span><br>
               Annual Turnover - <span>{{ $userDetails->current_turnover or 'n/a' }}</span>
               <br>Main Markets - <span>{{ $userDetails->main_markets or 'n/a' }}</span>
            </li>
         </ul>   
         @endif

      @endif
      </div>
   </div>
</div>
<div class="clearfix"></div>