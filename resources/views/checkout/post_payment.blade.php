@extends('app')
@section('content')
@inject('common', 'App\Components\CommonComponent')

        <!-- Page top navigation Starts Here-->
@include('partials.page_top_navigation')


<div class="main">
    <div class="container">
        <span class="pull-left"><h1 class="page-title">Post Payment</h1></span>
        <div class="clearfix"></div>
        <div class="col-md-12 padding-none">
            <div class="main-inner">
                <form method="POST" action="{{$PaymentURL}}" accept-charset="UTF-8" id="paymentFrom" name="paymentForm">
                    <span class="center">
                        Please wait, redirecting to process payment..
                    </span>
                    @foreach ($PaymentFields as $key=>$value)
                        <input type="hidden" value="<?php echo $value;?>" name="<?php echo $key;?>"/>
                    @endforeach
               </form>
            </div> <!-- main inner -->
        </div> <!-- col-md-12 padding-none -->
    </div> <!-- container -->
</div> <!-- main -->
<script type="text/javascript">
    document.paymentForm.submit();
</script>
@include('partials.footer')
@endsection