@inject('common', 'App\Components\CommonComponent')
<div class="col-md-12 padding-none">
   <div class="col-md-6 padding-none">
      <div class="profile-views">
         <span class="red">Profile Views:</span> {{ $common->profileViewCount() }} views since last login
      </div>
   </div>
   <div class="col-md-6 padding-none">
      <div class="potential-partners">
         <span><span class="red">Potential Partners:</span> ***
      </div>
   </div>
</div>