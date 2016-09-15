<h2 class="filter-head">Price Band (Rs)</h2>
<div class="price-range inner-block-bg">
    <div id="slider-range" class="margin-top"></div>
    <p class="margin-top">
        <input type="hidden" id="amount" class="form-control form-control1" readonly name="price">
        <?php
            $price_from = (isset($_REQUEST['price_from']) ? $_REQUEST['price_from'] : (request('price_from') ? request('price_from') : 2000));
            $price_to = (isset($_REQUEST['price_to']) ? $_REQUEST['price_to'] : (request('price_to') ? request('price_to') : 2000));
            $rang_slider = $price_from.','.$price_to;
        ?>

        <input type="hidden" id="price_from" name="price_from" value="{{$price_from }}" />
        <input type="hidden" id="price_to" name="price_to" value="{{$price_to }}" />
        
        <?php
            $filter_price_from = request('filter_price_from') ? request('filter_price_from') : (isset($_REQUEST['filter_price_from']) ? $_REQUEST['filter_price_from'] : "");
            $filter_price_to = request('filter_price_to') ? request('filter_price_to') : (isset($_REQUEST['filter_price_to']) ? $_REQUEST['filter_price_to'] : "");
        ?>

        @if(!empty($filter_price_from))
            <input type="hidden" id="filter_price_from" name="filter_price_from" value="{{$filter_price_from }}" />
            <input type="hidden" id="filter_price_to" name="filter_price_to" value="{{$filter_price_to }}" />
        @endif
        
        <span id="price_filter_pull_left" class="pull-left"><?php echo $commonComponent::getPriceType($price_from, false); ?></span>
        <span id="price_filter_pull_right"  class="pull-right"><?php echo $commonComponent::getPriceType($price_to, false); ?></span>

    </p>
</div>