$(document).ready(function() {

	/* Courier validation: added on 29 jun @shriram  */
    $('.clsSelMaxwgtAptType, .clsSelTermMaxwgtAptType').change(function(){
        var currValue = $(this+" option:selected").text().toLowerCase();
        var postType = $(this).attr('data-posttype');
        if (typeof postType !== typeof undefined && postType !== false) {
            postType = '0';
        }

        switch(currValue){
            case 'gms':  addReqClass = 'clsCOURMaxWeightGms'; break;
            case 'kgs':  addReqClass = 'clsCOURMaxWeightKgs'; break;
            case 'mts':  addReqClass = 'clsCOURMaxWeightMts'; break;
            default:     addReqClass = 'clsCOURMaxWeightGms';
        }

        /* Checking Old Existing Classes */
        if($(".clsIDmax_weight_accepted"+postType).hasClass('clsCOURMaxWeightMts')){
            $(".clsIDmax_weight_accepted"+postType).val('').removeClass('clsCOURMaxWeightMts').addClass(addReqClass);
        }else if($(".clsIDmax_weight_accepted"+postType).hasClass('clsCOURMaxWeightKgs')){
            $(".clsIDmax_weight_accepted"+postType).val('').removeClass('clsCOURMaxWeightKgs').addClass(addReqClass);
        }else{
            $(".clsIDmax_weight_accepted"+postType).val('').removeClass('clsCOURMaxWeightGms').addClass(addReqClass);
        }
    });
    
    // Transit Days
    $('.clsSelTransitDaysType').change(function(){
        var currValue = $(".clsSelTransitDaysType option:selected").text().toLowerCase();
        var targetElement = $(".clsIDtransitdays");
        switch(currValue){
            case 'days':  addReqClass = 'clsCOURTransitDays'; break;
            case 'weeks':  addReqClass = 'clsCOURTransitWeeks'; break;
            default:     addReqClass = 'clsCOURTransitDays';
        }

        /* Checking Old Existing Classes */
        if(targetElement.hasClass('clsCOURTransitDays')){
            targetElement.val('').removeClass('clsCOURTransitDays').addClass(addReqClass);
        }else if(targetElement.hasClass('clsCOURTransitWeeks')){
            targetElement.val('').removeClass('clsCOURTransitWeeks').addClass(addReqClass);
        }else{
            targetElement.val('').removeClass('clsCOURTransitDays').addClass(addReqClass);
        }
    });

    // Relocation Domestic, pet move - Transit Days
    $('.clsSelTransitDaysTypeHHG, .clsSelTransitDaysTypeVEH').change(function(){
        var postType = $(this).attr('data-posttype');
        if(postType == 'HHG') {
            var targetElement = $(".clsIDtransitdaysHHG");
            var currValue = $(".clsSelTransitDaysTypeHHG option:selected").text().toLowerCase();
        }else{
            var targetElement = $(".clsIDtransitdaysVEH");
            var currValue = $(".clsSelTransitDaysTypeVEH option:selected").text().toLowerCase();
        }
        
        switch(currValue){
            case 'days':  addReqClass = 'clsCOURTransitDays'; break;
            case 'weeks':  addReqClass = 'clsCOURTransitWeeks'; break;
            default:     addReqClass = 'clsCOURTransitDays';
        }

        /* Checking Old Existing Classes */
        if(targetElement.hasClass('clsCOURTransitDays')){
            targetElement.val('').removeClass('clsCOURTransitDays').addClass(addReqClass);
        }else if(targetElement.hasClass('clsCOURTransitWeeks')){
            targetElement.val('').removeClass('clsCOURTransitWeeks').addClass(addReqClass);
        }else{
            targetElement.val('').removeClass('clsCOURTransitDays').addClass(addReqClass);
        }
    });
    
    // Relocation INternation Air, Ocean - Transit Days
    $('.clsSelTransitDaysTypeAir, .clsSelTransitDaysTypeOcean').change(function(){
        var postType = $(this).attr('data-posttype');
        if(postType == 'Air') {
            var targetElement = $(".clsIDtransitdaysAir");
            var currValue = $(".clsSelTransitDaysTypeAir option:selected").text().toLowerCase();
        }else{
            var targetElement = $(".clsIDtransitdaysOcean");
            var currValue = $(".clsSelTransitDaysTypeOcean option:selected").text().toLowerCase();
        }
        
        switch(currValue){
            case 'days':  addReqClass = 'clsCOURTransitDays'; break;
            case 'weeks':  addReqClass = 'clsCOURTransitWeeks'; break;
            default:     addReqClass = 'clsCOURTransitDays';
        }

        /* Checking Old Existing Classes */
        if(targetElement.hasClass('clsCOURTransitDays')){
            targetElement.val('').removeClass('clsCOURTransitDays').addClass(addReqClass);
        }else if(targetElement.hasClass('clsCOURTransitWeeks')){
            targetElement.val('').removeClass('clsCOURTransitWeeks').addClass(addReqClass);
        }else{
            targetElement.val('').removeClass('clsCOURTransitDays').addClass(addReqClass);
        }
    });

    // Courier - Buyer: Package Weight (Volumetric Weight) 
    $('#ptlCheckVolWeightCourier').change(function(){
        var currValue = $("#ptlCheckVolWeightCourier option:selected").text().toLowerCase();
        var tarLengthElement = $("#ptlLengthCourier");
        switch(currValue){
            
            case 'cm':  
                legthClass = 'clsCOURLengthCM';
                breadthClass = 'clsCOURBreadthCM';
                heightClass = 'clsCOURHeightCM';
                break;
            
            case 'feet':  
                legthClass = 'clsCOURLengthFt';
                breadthClass = 'clsCOURBreadthFt';
                heightClass = 'clsCOURHeightFt';
                break;
            
            case 'inches':
                legthClass = 'clsCOURLengthInchs';
                breadthClass = 'clsCOURBreadthInchs';
                heightClass = 'clsCOURHeightInchs';
                break;    
            
            case 'meter':
                legthClass = 'clsCOURLengthMeter';
                breadthClass = 'clsCOURBreadthMeter';
                heightClass = 'clsCOURHeightMeter';
                break;    
            
            default:  
                legthClass = 'clsCOURLengthCM';
                breadthClass = 'clsCOURBreadthCM';
                heightClass = 'clsCOURHeightCM';
        }

        /* Checking Old Existing Classes */
        if(tarLengthElement.hasClass('clsCOURLengthMeter')){
            $("#ptlLengthCourier").val('').removeClass('clsCOURLengthMeter').addClass(legthClass);
            $("#ptlWidthCourier").val('').removeClass('clsCOURBreadthMeter').addClass(breadthClass);
            $("#ptlHeightCourier").val('').removeClass('clsCOURHeightMeter').addClass(heightClass);
        }else if(tarLengthElement.hasClass('clsCOURLengthFt')){
            $("#ptlLengthCourier").val('').removeClass('clsCOURLengthFt').addClass(legthClass);
            $("#ptlWidthCourier").val('').removeClass('clsCOURBreadthFt').addClass(breadthClass);
            $("#ptlHeightCourier").val('').removeClass('clsCOURHeightFt').addClass(heightClass);
        
        }else if(tarLengthElement.hasClass('clsCOURLengthInchs')){
            $("#ptlLengthCourier").val('').removeClass('clsCOURLengthInchs').addClass(legthClass);
            $("#ptlWidthCourier").val('').removeClass('clsCOURBreadthInchs').addClass(breadthClass);
            $("#ptlHeightCourier").val('').removeClass('clsCOURHeightInchs').addClass(heightClass);    
        }else{
            $("#ptlLengthCourier").val('').removeClass('clsCOURLengthCM').addClass(legthClass);
            $("#ptlWidthCourier").val('').removeClass('clsCOURBreadthCM').addClass(breadthClass);
            $("#ptlHeightCourier").val('').removeClass('clsCOURHeightCM').addClass(heightClass);
        }
    });
    /* End */

    // Payment terms - Credit ( Days / weeks )
    $('.clsSelPaymentCreditType').change(function(){
        var currValue = $(".clsSelPaymentCreditType option:selected").text().toLowerCase();
        var targetElement = $(".clsIDCredit_period");
        switch(currValue){
            case 'days':  addReqClass = 'clsCreditPeriod'; break;
            case 'weeks':  addReqClass = 'clsCreditPeriodWeeks'; break;
            default:     addReqClass = 'clsCreditPeriod';
        }

        /* Checking Old Existing Classes */
        if(targetElement.hasClass('clsCreditPeriod')){
            targetElement.val('').removeClass('clsCreditPeriod').addClass(addReqClass);
        }else if(targetElement.hasClass('clsCreditPeriodWeeks')){
            targetElement.val('').removeClass('clsCreditPeriodWeeks').addClass(addReqClass);
        }else{
            targetElement.val('').removeClass('clsCreditPeriod').addClass(addReqClass);
        }
    });

    // Relocation Air, Ocean
    $('.clsSelPaymentCreditTypeAir, .clsSelPaymentCreditTypeOcean').change(function(){
        var postType = $(this).attr('data-posttype');
        if(postType == 'Air') {
            var targetElement = $(".clsIDCredit_periodAir");
            var currValue = $(".clsSelPaymentCreditTypeAir option:selected").text().toLowerCase();
        }else{
            var targetElement = $(".clsIDCredit_periodOcean");
            var currValue = $(".clsSelPaymentCreditTypeOcean option:selected").text().toLowerCase();
        }
        
        switch(currValue){
            case 'days':  addReqClass = 'clsCreditPeriod'; break;
            case 'weeks':  addReqClass = 'clsCreditPeriodWeeks'; break;
            default:     addReqClass = 'clsCreditPeriod';
        }

        /* Checking Old Existing Classes */
        if(targetElement.hasClass('clsCreditPeriod')){
            targetElement.val('').removeClass('clsCreditPeriod').addClass(addReqClass);
        }else if(targetElement.hasClass('clsCreditPeriodWeeks')){
            targetElement.val('').removeClass('clsCreditPeriodWeeks').addClass(addReqClass);
        }else{
            targetElement.val('').removeClass('clsCreditPeriod').addClass(addReqClass);
        }
    });

    // LTL4 - Buyer: Package Weight (Volumetric Weight) 
    $('#ptlCheckVolWeight').change(function(){
        var currValue = $("#ptlCheckVolWeight option:selected").text().toLowerCase();
        var tarLength = $("#ptlLength");
        var tarBreadth = $("#ptlWidth");
        var tarHeight = $("#ptlHeight");
        switch(currValue){
            
            case 'cm':  
                legthClass = 'clsLTL4LengthCM';
                breadthClass = 'clsLTL4BreadthCM';
                heightClass = 'clsLTL4HeightCM';
                break;
            
            case 'feet':  
                legthClass = 'clsLTL4LengthFt';
                breadthClass = 'clsLTL4BreadthFt';
                heightClass = 'clsLTL4HeightFt';
                break;
            
            case 'inches':
                legthClass = 'clsLTL4LengthInchs';
                breadthClass = 'clsLTL4BreadthInchs';
                heightClass = 'clsLTL4HeightInchs';
                break;    
            
            case 'meter':
                legthClass = 'clsLTL4LengthMeter';
                breadthClass = 'clsLTL4BreadthMeter';
                heightClass = 'clsLTL4HeightMeter';
                break;    
            
            default:  
                legthClass = 'clsLTL4LengthCM';
                breadthClass = 'clsLTL4BreadthCM';
                heightClass = 'clsLTL4HeightCM';
        }

        /* Checking Old Existing Classes */
        if(tarLength.hasClass('clsLTL4LengthCM')){
            $("#displayVolumeW").html('');
            tarLength.val('').removeClass('clsLTL4LengthCM').addClass(legthClass);
            tarBreadth.val('').removeClass('clsLTL4BreadthCM').addClass(breadthClass);
            tarHeight.val('').removeClass('clsLTL4HeightCM').addClass(heightClass);

        }else if(tarLength.hasClass('clsLTL4LengthFt')){
            $("#displayVolumeW").html('');
            tarLength.val('').removeClass('clsLTL4LengthFt').addClass(legthClass);
            tarBreadth.val('').removeClass('clsLTL4BreadthFt').addClass(breadthClass);
            tarHeight.val('').removeClass('clsLTL4HeightFt').addClass(heightClass);
        
        }else if(tarLength.hasClass('clsLTL4LengthInchs')){
            $("#displayVolumeW").html('');
            tarLength.val('').removeClass('clsLTL4LengthInchs').addClass(legthClass);
            tarBreadth.val('').removeClass('clsLTL4BreadthInchs').addClass(breadthClass);
            tarHeight.val('').removeClass('clsLTL4HeightInchs').addClass(heightClass);    
        }else{
            $("#displayVolumeW").html('');
            tarLength.val('').removeClass('clsLTL4LengthMeter').addClass(legthClass);
            tarBreadth.val('').removeClass('clsLTL4BreadthMeter').addClass(breadthClass);
            tarHeight.val('').removeClass('clsLTL4HeightMeter').addClass(heightClass);
        }
    });

    /* LTL4 - Validation: added on 30 jun @shriram  */
    $('#ptlCheckUnitWeight').change(function(){
        var currValue = $("#ptlCheckUnitWeight option:selected").text().toLowerCase();
        var postType = $(this).attr('data-posttype');
        var serviceID = $(this).attr('data-servicetype');

        /* Checking Post type Attr set or not */
        if (typeof postType !== typeof undefined && postType !== false) {
        }else{ postType = '0'; }

        /* Checking Service Attr set or not */
        if (typeof serviceID !== typeof undefined && serviceID !== false) {
        }else{ serviceID = 0; }

        /* If Service 21, then it is Courier */
        if(serviceID == 0)
        {
            switch(currValue){
                case 'gms':  addReqClass = 'clsLTL4MaxWeightGms'; break;
                case 'kgs':  addReqClass = 'clsLTL4MaxWeightKgs'; break;
                case 'mts':  addReqClass = 'clsLTL4MaxWeightMts'; break;
                default:     addReqClass = 'clsLTL4MaxWeightGms';
            }
            
            /* Checking Old Existing Classes */
            if($(".clsIDptlUnitsWeight"+postType).hasClass('clsLTL4MaxWeightMts')){
                $(".clsIDptlUnitsWeight"+postType).val('').removeClass('clsLTL4MaxWeightMts').addClass(addReqClass);
            }else if($(".clsIDptlUnitsWeight"+postType).hasClass('clsLTL4MaxWeightKgs')){
                $(".clsIDptlUnitsWeight"+postType).val('').removeClass('clsLTL4MaxWeightKgs').addClass(addReqClass);
            }else{
                $(".clsIDptlUnitsWeight"+postType).val('').removeClass('clsLTL4MaxWeightGms').addClass(addReqClass);
            }

        }else{

            switch(currValue){
                case 'gms':  addReqClass = 'clsCOURMaxWeightGms'; break;
                case 'kgs':  addReqClass = 'clsCOURMaxWeightKgs'; break;
                case 'mts':  addReqClass = 'clsCOURMaxWeightMts'; break;
                default:     addReqClass = 'clsCOURMaxWeightGms';
            }

            /* Checking Old Existing Classes */
            if($(".clsIDmax_weight_accepted"+postType).hasClass('clsCOURMaxWeightMts')){
                $(".clsIDmax_weight_accepted"+postType).val('').removeClass('clsCOURMaxWeightMts').addClass(addReqClass);
            }else if($(".clsIDmax_weight_accepted"+postType).hasClass('clsCOURMaxWeightKgs')){
                $(".clsIDmax_weight_accepted"+postType).val('').removeClass('clsCOURMaxWeightKgs').addClass(addReqClass);
            }else{
                $(".clsIDmax_weight_accepted"+postType).val('').removeClass('clsCOURMaxWeightGms').addClass(addReqClass);
            }
        }   

    });
    
    /* Intracity validation: added on 30 jun @shriram  */
    $('.clsSelMaxIntraWeight').change(function(){
        var currValue = $(".clsSelMaxIntraWeight option:selected").text().toLowerCase();
        var tarElement = $(".clsIDWeight_accepted");
        
        switch(currValue){
            case 'gms':  addReqClass = 'clsIntraWeightGms'; break;
            case 'kgs':  addReqClass = 'clsIntraWeightKgs'; break;
            case 'mts':  addReqClass = 'clsIntraWeightMts'; break;
            default:     addReqClass = 'clsIntraWeightGms';
        }

        /* Checking Old Existing Classes */
        if(tarElement.hasClass('clsIntraWeightMts')){
            tarElement.val('').removeClass('clsIntraWeightMts').addClass(addReqClass);
        }else if(tarElement.hasClass('clsIntraWeightKgs')){
            tarElement.val('').removeClass('clsIntraWeightKgs').addClass(addReqClass);
        }else{
            tarElement.val('').removeClass('clsIntraWeightGms').addClass(addReqClass);
        }
    });

    /* Relocation: Avg Volume/ Shipment added on 30 jun @shriram  */
    $('.clsSelAvgVolShipment').change(function(){
        var currValue = $(".clsSelAvgVolShipment option:selected").text().toLowerCase();
        var tarElement = $("#relocation_term_weighttype");
        
        switch(currValue){
            case 'cft':  addReqClass = 'clsRelocationHomeCFT'; break;
            case 'ccm':  addReqClass = 'clsRelocationHomeCCM'; break;
            default:     addReqClass = 'clsRelocationHomeCFT';
        }

        /* Checking Old Existing Classes */
        if(tarElement.hasClass('clsRelocationHomeCCM')){
            tarElement.val('').removeClass('clsRelocationHomeCCM').addClass(addReqClass);
        }else{
            tarElement.val('').removeClass('clsRelocationHomeCFT').addClass(addReqClass);
        }
    });
});