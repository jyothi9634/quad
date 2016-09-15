<footer>
    
    <div class="container footer-links">
         Logistiks.com &copy; 2016. <a href="{{url('privacypolicy')}}">Privacy Policy</a>  | <a href="{{url('disclaimer')}}">Disclaimer</a>
        {{--*/ $routeName = strtolower(substr(class_basename(Route::currentRouteAction()), (strpos(class_basename(Route::currentRouteAction()), '@') + 1))) /*--}}
        <?php /*    
        @if (Auth::guest()!= '' && $routeName == 'home')    
        <div class="disclaimer">
            <p style="margin: 10px 0 0;">The current version of the portal logistiks.com you are viewing is under development and testing stage. The transactions being carried out on the portal logistiks.com are also the test transactions carried out upon due approvals from the buyer and the seller of the same. Logistiks.com reserves right to revisit the portal and make improvements to the same on regular interval basis. </p>
            <p style="margin: 10px 0 0;">In case of any changes or updations, the buyer and seller are required to make necessary disclosures in the sign up page. It is the responsibility of the buyer and the seller to update their profile at regular intervals, until that the information updated will hold good for all the transactions or services offered or availed through the logistiks.com portal.</p>
        </div>
        @endif */ ?>
        </div>    
	
</footer>