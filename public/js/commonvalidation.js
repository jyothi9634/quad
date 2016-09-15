$(document).ready(function(){

    /* ----------------------- Validations : Start -------------------- */   
    /* 9 Cr Validation */
    $('body').on("keypress keyup", ".clsConsignValue, .clsRIASConsignValue", function () {
        $(this).numeric({ allowPlus:false, allowMinus: false, allowThouSep: false, maxPreDecimalPlaces:8, maxDecimalPlaces:2, min:1, max:90000000 });
        
        var str = $(this).val();
        if(str == 90000000 && str.lastIndexOf('.') === (str.length - 1)){
            str = str.replace(/.\s*$/, "");
            $(this).val(str);
        }
    });

    /* 8-2 Validation */
    $('body').on("keypress", ".clsRIASFreightAmount", function () {
        $(this).numeric({ allowPlus:false, allowMinus: false, allowThouSep: false, min:0.01, maxPreDecimalPlaces:8, maxDecimalPlaces:2 });
    });

    /* 7-2 Validation */
    $('body').on("keypress", ".clsRDSConsignValue", function () {
        $(this).numeric({ allowPlus:false, allowMinus: false, allowThouSep: false, min:0.01, maxPreDecimalPlaces:7, maxDecimalPlaces:2 });
    });

    /* 6-2 Validation */
    $('body').on("keypress", ".clsRIASPrice, .ClsRDSPrice, .clsCounterOffer, .clsFTLCounterOffer", function () {
        $(this).numeric({ allowPlus:false, allowMinus: false, allowThouSep: false, min:0.01, maxPreDecimalPlaces:6, maxDecimalPlaces:2 });
    });

    /* 5-2 Validation */
    $('body').on("keypress", ".clsRIASODChargesFlat, .clsRIASFreightChargesFlat, .clsRIOSFreightFlat, .clsRIOSODChargesFlat, .clsRPetOriginCharges, .clsRPetDestinationCharges, .clsRPetConsignValue, .clsRDVTransportCharges, .clsRDVCost, .clsGMSRateFlat, .clsFTLRate, .clsFTLSPrice, .clsFTLFreightAmount, .clsFTLTQuote, .clsFTLTContractQty, .clsTHPrice, .clsTLCounterOffer, .clsTLDriverCost, .clsTHRate, .clsRDSTransportCharges", function () {
        $(this).numeric({ allowPlus:false, allowMinus: false, allowThouSep: false, min:0.01, maxPreDecimalPlaces:5, maxDecimalPlaces:2 });
    });

    /* 5-3 Validation */
    $('body').on("keypress", ".clsFTLTQuantity, .clsFTLTCurrIndentQty, .clsCOURMaxWeightGms, .clsCOURMaxWeightKgs, .clsAirInitUnitWeight", function () {
        $(this).numeric({ allowPlus:false, allowMinus: false, allowThouSep: false, min:0.001, maxPreDecimalPlaces:5, maxDecimalPlaces:3 });
    });

    /* 4-3 Validation */
    $('body').on("keypress", ".clsAirInitTVolumeCCM, .clsRailKGpCFT, .clsRailVolumepCFT, .clsRailTContractVol, .clsIntraWeightGms, .clsIntraWeightKgs, .clsIntraWeightMts", function () {
        $(this).numeric({ allowPlus:false, allowMinus: false, allowThouSep: false, min:0.001, maxPreDecimalPlaces:4, maxDecimalPlaces:3 });
    });
    
    /* 4-4 Validation */
    $('body').on("keypress", ".clsAirDomKGperCCM, .clsAirIntKGperCCM", function () {
        $(this).numeric({ allowPlus:false, allowMinus: false, allowThouSep: false, min:0.0001, maxPreDecimalPlaces:4, maxDecimalPlaces:3 });
    });

    /* 4-2 Validation. */
    $('body').on("keypress", ".clsRIASWeightBracketpKG, .clsRIASFreightChargespKG, .clsRIASODChargespCFT, .clsRIASCancelCharges, .clsRIASOtherCharges, .clsRIASVolume, .clsRIATFreightChargespKG, .clsRIATODChargespCFT, .clsRIOTStorageCharges, .clsRPetODChargesFlat, .clsRPetFreightFlat, .clsRPetTransportCharges, .clsRPetCancelCharges, .clsRPetDocketCharges, .clsRPetFreightpKG, .clsROMODChargespCFT, .clsROMTransportChargespKm, .clsROMCancelCharges, .clsROMOtherCharges, .clsROMDoor2DoorCharges, .clsRDSVolumeCFT, .clsRDSCancellationCharges, .clsRDTAvgCFTpMove, .clsRDVStorageChargespDay, .clsAllOtherCharges, .clsGMSOtherCharges, .clsCOURMaxWeight, .clsGMSCancelCharges, .clsRIOSStorageCharges, .clsFTLCancelCharges, .clsFTLOtherCharges, .clsTHCancelCharges, .clsTHOtherCharges, .clsTLeasePrice, .clsTLOtherCharges, .clsTLOtherCharges, .clsRelocationAvgVolShip, .clsCOURLengthCM, .clsCOURBreadthCM, .clsCOURHeightCM, .clsCOURLengthFt, .clsCOURBreadthFt, .clsCOURHeightFt, .clsCOURLengthInchs, .clsCOURBreadthInchs, .clsCOURHeightInchs, .clsCOURLengthMeter, .clsCOURBreadthMeter, .clsCOURHeightMeter, .clsTLCancelCharges, .clsRIOSCancelCharges, .clsRIOSOtherCharges, .clsRDSOtherCharges, .clsRDSAdditionalCharges", function () {
        $(this).numeric({ allowPlus:false, allowMinus: false, allowThouSep: false, min:0.01, maxPreDecimalPlaces:4, maxDecimalPlaces:2 });
    });

    /* 4-3 Validation. */
    $('body').on("keypress", ".clsCOURMaxWeightMts", function () {
        $(this).numeric({ allowPlus:false, allowMinus: false, allowThouSep: false, min:0.001, maxPreDecimalPlaces:4, maxDecimalPlaces:3 });
    });

    /* 3-2 Validation */
    $('body').on("keypress", ".clsRIASStorageCharges, .clsRIATStorageCharges, .clsRIOTAvgCBMpMove, .clsRIOTCratingChargespCFT, .clsRPetWeight, .clsRDSODChargespCFT, .clsRDSCratingChargespCFT, .clsRDSStorageChargespCFTpDay", function () {
        $(this).numeric({ allowPlus:false, allowMinus: false, allowThouSep: false, min:0.01, maxPreDecimalPlaces:3, maxDecimalPlaces:2 });
    });

    /* 3-3 Validation */
    $('body').on("keypress", ".clsROMDistanceKM, .clsFTLSQuantity, .clsFTLQuantity, .clsTHQuantity", function () {
        $(this).numeric({ allowPlus:false, allowMinus: false, allowThouSep: false, min:0.001, maxPreDecimalPlaces:3, maxDecimalPlaces:3 });
    });

    /* 3-2 & 1-200 Max Validation */
    $('body').on("keypress", ".clsRIATAvgKgPerMove", function () {
        $(this).numeric({ allowPlus:false, allowMinus: false, allowThouSep: false, min:0.01, max:200, maxPreDecimalPlaces:3, maxDecimalPlaces:2  });
    });

    /* 3-2 & 1-999 Max Validation */
    $('body').on("keypress", ".clsRIOSODChargespCBM, .clsRIOSCratingChargespCFT", function () {
        $(this).numeric({ allowPlus:false, allowMinus: false, allowThouSep: false, min:0.01, max:999, maxPreDecimalPlaces:3, maxDecimalPlaces:2  });
    });

    /* 2-2 Validation */
    $('body').on("keypress", ".clsRIASTrasitTime, .clsRIASCartonTypenos, .clsRIATPlaceIndentNos, .clsRIOSVolumeCBM, .clsRPetCageWeight, .clsRDSVolumeCBM, .clsCOURConvFactor", function () {
        $(this).numeric({ allowPlus:false, allowMinus: false, allowThouSep: false, min:0.01, maxPreDecimalPlaces:2, maxDecimalPlaces:2 });
    });

    /* 1-2 Validation */
    /*$('body').on("keypress", "", function () {
        $(this).numeric({ allowPlus:false, allowMinus: false, allowThouSep: false, min:0.01, maxPreDecimalPlaces:1, maxDecimalPlaces:2 });
    });*/

    /* 1-10 Validation */
    $('body').on("keypress", ".clsGMSNoOfDays", function () {
        $(this).numeric({ allowPlus:false, allowMinus: false, allowThouSep: false, min:1, max:10, allowDecSep:false });
    });

    /* 1-20 Validation */
    $('body').on("keypress", ".clsGMSNoOfPerson, .clsGMTNoOfPerson", function () {
        $(this).numeric({ allowPlus:false, allowMinus: false, allowThouSep: false, min:1, max:20, allowDecSep:false });
    });

    /* 1-90 Validation */
    $('body').on("keypress", ".clsRIOSTransitDays", function () {
        $(this).numeric({ allowPlus:false, allowMinus: false, allowThouSep: false, min:1, max:90, allowDecSep:false });
    });

    /* 1-99 Validation */
    $('body').on("keypress", ".clsRIASNoOfCartons, .clsRIASTrasitTime, .clsRIASCartonTypenos, .clsRIATPlaceIndentNos, .clsRPetTransitDays, .clsROMCreditPeriodWeeks, .clsRDSTransitDays, .clsRDVTransitDays, .clsGMSCreditPeriodWeeks, .clsFTLTransitDays, .clsFTLCreditPeriodWeeks, .clsTHTransitDays, .clsTHTransitWeeks, .clsCreditPeriodWeeks, .clsCOURTransitWeeks", function () {
        $(this).numeric({ allowPlus:false, allowMinus: false, allowThouSep: false, min:1, max:99, allowDecSep:false, maxDigits:3 });
    });

    /* 1-200 Validation */
    $('body').on("keypress", ".clsRIATNoofMoves, .clsRDTNoOfMoves", function () {
        $(this).numeric({ allowPlus:false, allowMinus: false, allowThouSep: false, min:1, max:200, allowDecSep:false });
    });

    /* 1-365 Validation */
    $('body').on("keypress", ".clsGMTNoOfDays, .clsGMSNoOfDaysTerm", function () {
        $(this).numeric({ allowPlus:false, allowMinus: false, allowThouSep: false, min:1, max:365, allowDecSep:false });
    });

    /* 0-999 Validation */
    $('body').on("keypress", ".clsROMMinKm", function () {
        $(this).numeric({ allowPlus:false, allowMinus: false, allowThouSep: false, min:0, max:999, allowDecSep:false });
    });

    /* 1-1000 Validation */
    $('body').on("keypress", ".clsFTLSQuantity, .clsFTSLoads", function () {
        $(this).numeric({ allowPlus:false, allowMinus: false, allowThouSep: false, min:1, max:1000, allowDecSep:false });
    });

    /* 1-999 & Max: 3 Chars Validation */
    $('body').on("keypress", ".clsTransitDays, .clsRInitMinLeasePeriod, .clsFTLCreditPeriod, .clsCreditPeriod, .clsTLMinLeasePeriod", function () {
        $(this).numeric({ allowPlus:false, allowMinus: false, allowThouSep: false, min:1, allowDecSep:false, maxDigits:3 });
    });

    /* 1-999 Validation */
    $('body').on("keypress", ".clsRIASTrasitDays, .clsRIATTransitDays, .clsRIOSNoOfItems, .clsRIOTNoOfMoves, .clsRIOTNoOfItems, .clsROMMaxKm, .clsROMCreditPeriodDays, .clsROMNoOfItems, .clsRDSNoOfItems, .clsRDSNoOfCartons, .clsRDSInventory, .clsRDSHandymanChargespHour, .clsGMSCreditPeriod, .clsCOURTransitDays", function () {
        $(this).numeric({ allowPlus:false, allowMinus: false, allowThouSep: false, min:1, max:999, allowDecSep:false });
    });

    /* 1-9999 Validation */
    $('body').on("keypress", ".clsRDSEscortChargespDay, .clsFTLSLoads", function () {
        $(this).numeric({ allowPlus:false, allowMinus: false, allowThouSep: false, min:1, max:9999, allowDecSep:false });
    });

    /* 1-99999 Validation */
    $('body').on("keypress", ".clsRDSSettlingServicespDay, .clsRDSPropertySearchCharges, .clsRDSBrokerageCharges, .clsGMSRatepService, .clsGMSRatepPerson, .clsGMSRatepDay, .clsGMTRatepService, .clsGMTRatepPerson, .clsGMTRatepDay, .clsAirInitNoOfPackages, .clsPresentKMReading", function () {
        $(this).numeric({ allowPlus:false, allowMinus: false, allowThouSep: false, min:1, max:99999, allowDecSep:false });
    });

    /* 1-999999 Validation */
    $('body').on("keypress", ".clsGMSSubmitQuote, .clsGMAvgRent, .clsGMSRatepRent", function () {
        $(this).numeric({ allowPlus:false, allowMinus: false, allowThouSep: false, min:1, max:999999, allowDecSep:false });
    });

    /* 1-9 Cr Validation */
    $('body').on("keypress", ".clsRIOSConsignValue", function () {
        $(this).numeric({ allowPlus:false, allowMinus: false, allowThouSep: false, min:1, max:99999999, allowDecSep:false });
    });

    /* -------------- Alphabets Validations -------------------- */   
    
    /* 30 length - Aplhabets with space */
    $('body').on("keypress", ".clsReportTo", function () {
        $(this).alpha({allowSpace:true, maxLength:30});
    });

    /* 50 length - Aplhabets with space */
    $('body').on("keypress", ".clsAlphaSpace, .clsConsignorName, .clsDrivername, .clsPrivateSellers, .clsRecipientName, .clsReportto", function () {
        $(this).alpha({allowSpace:true, maxLength:50});
    });

    /* -------------- Alphanumeric Validations -------------------- */   
    
    /* 10 length - AplhaNumeric without space */
    $('body').on("keypress", ".clsPancardNo", function () {
        $(this).alphanum({allowSpace:false, maxLength:10});
    });

    /* 11 length - AplhaNumeric without space */
    $('body').on("keypress", ".clsTRKHVehicleno, .clsVehicleno", function () {
        $(this).alphanum({allowSpace:false, maxLength:11});
    });

    /* 20 length - AplhaNumeric without space */
    $('body').on("keypress", ".clsTinNumber, .clsServiceTaxno", function () {
        $(this).alphanum({allowSpace:false, maxLength:20});
    });

    /* 20 length - AplhaNumeric */
    $('body').on("keypress", ".clsCustomerDoc", function () {
        $(this).alphanum({allowSpace:true, maxLength:20});
    });

    /* 50 length - AplhaNumeric */
    $('body').on("keypress", ".clsEngineNumber, .clsChassisNumber, .clsVehicleInsuranceNo", function () {
        $(this).alphanum({allowSpace:false, maxLength:50});
    });

    /* 50 length - AplhaNumeric with space */
    $('body').on("keypress", ".clsBankName, .clsBranchName", function () {
        $(this).alphanum({allowSpace:true, maxLength:50});
    });

    /* 50 length with / special char - AplhaNumeric */
    $('body').on("keypress", ".clsLRnumber, .clsTransporterBill", function () {
        $(this).alphanum({allowSpace:true, allow:'/', maxLength:50});
    });

    /* 25 length - AplhaNumeric */
    $('body').on("keypress", ".clsOtherText", function () {
        $(this).alphanum({allowSpace:true, allow:'-', maxLength:25});
    });

    /* 50 length - AplhaNumeric with space and allows . */
    $('body').on("keypress", ".clsCustDocs", function () {
        $(this).alphanum({allowSpace:true, allow:'.-_', maxLength:50});
    });


    /* 50 length - AplhaNumeric */
    $('body').on("keypress", ".clsRIASOrdernumber, .clsRIASAwbnumber, .clsRIOSBLnumber, .clsRPetAwbnumber", function () {
        $(this).alphanum({allowSpace:true, allow:'-)(.*%$#@!&', maxLength:50});
    });

    /* 75 length - AplhaNumeric */
    $('body').on("keypress", ".clsPinCodeAlphabets", function () {
        $(this).alphanum({allowSpace:true, allow:'-.,', maxLength:75});
    });

    /* 100 length - AplhaNumeric */
    $('body').on("keypress onpaste paste", ".clsConsignAddInfo", function () {
        var maxAttr = $(this).attr('maxlength');
        if (typeof maxAttr !== typeof undefined && maxAttr !== false) {}else{
            $(this).attr("maxlength",100);
        }    
        $(this).alphanum({allowSpace:true, allow:'-)(.#@&/,:', maxLength:100});
    });

    /* 500 length - AplhaNumeric */
    $('body').on("keypress onpaste paste", ".clsBusiDescription, .clsAddress, .clsRIASAdditionalInfo, .clsTermsConditions, .clsFTLComments, .clsAdditionalInfo, .clsReportingAddr", function () {
        var maxAttr = $(this).attr('maxlength');
        if (typeof maxAttr !== typeof undefined && maxAttr !== false) {}else{
            $(this).attr("maxlength",500);
        }    
        $(this).alphanum({allowSpace:true, allow:'-)(.#@&/,:', maxLength:500});
    });

    /* Password Restriction */
    $('body').on("keypress", ".clsPasswordVal", function () {
        $(this).alphanum({allowSpace:false, allow:'!@#$%^&*()_-+,.:;', maxLength:14});
    });

    /* Email Validation */
    $('body').on("keypress", ".clsEmailAddr, .clsConsignEmail", function () {
        $(this).alphanum({allowSpace:false, allow:'-@._', maxLength:75});
    });

    /* Mobile number */
    $('body').on("keypress", ".clsMobileno, .clsMobile", function () {
        $(this).numeric({allowPlus:false, allowMinus: false, allowThouSep: false, allowDecSep:false, allowLeadingSpaces:false, maxDigits:10});
    });

    /* Landline */
    $('body').on("keypress", ".clsLandline", function () {
        $(this).numeric({allowPlus:false, allowMinus: false, allowThouSep: false, allowDecSep:false, allowLeadingSpaces:false, maxDigits:15});
    });

    /* PinCode */
    $('body').on("keypress", ".clsPinCode", function () {
        $(this).numeric({allowPlus:false, allowMinus: false, allowThouSep: false, allowDecSep:false, allowLeadingSpaces:false, maxDigits:6});
    });

    /* Service Level Validations */
    $('body').on("keypress", ".clsFTLQuote, .clsFTLFinalQuote", function () {
        var serviceType = $(this).attr('data-servicetype');
        if (typeof serviceType !== typeof undefined && serviceType !== false) {
            if(serviceType == 1){
                /* FTL */
                $(this).numeric({ allowPlus:false, allowMinus: false, allowThouSep: false, min:0.01, maxPreDecimalPlaces:6, maxDecimalPlaces:2 });
            }else if(serviceType == 4){
                /* Truck Haul */
                $(this).numeric({ allowPlus:false, allowMinus: false, allowThouSep: false, min:0.01, maxPreDecimalPlaces:5, maxDecimalPlaces:2 });
            }else if(serviceType == 5){
                /* Truck lease */
                var leaseType = $(this).attr('data-leasetype');
                var leaseVal = leaseType.toLowerCase();
                if(leaseVal == 'daily'){
                    $(this).numeric({ allowPlus:false, allowMinus: false, allowThouSep: false, min:0.01, maxPreDecimalPlaces:4, maxDecimalPlaces:2 });
                }else if(leaseVal == 'weekly'){
                    $(this).numeric({ allowPlus:false, allowMinus: false, allowThouSep: false, min:0.01, maxPreDecimalPlaces:5, maxDecimalPlaces:2 });
                }else if(leaseVal == 'monthly'){
                    $(this).numeric({ allowPlus:false, allowMinus: false, allowThouSep: false, min:0.01, maxPreDecimalPlaces:6, maxDecimalPlaces:2 });
                }else{
                    $(this).numeric({ allowPlus:false, allowMinus: false, allowThouSep: false, min:0.01, maxPreDecimalPlaces:7, maxDecimalPlaces:2 });
                }    
            }
        }    
    });


    /* 4-3 LTL-4 Package Weight (Volumetric Weight). */
    $('body').on("keypress", ".clsLTL4LengthCM, .clsLTL4BreadthCM, .clsLTL4HeightCM, .clsLTL4LengthFt, .clsLTL4BreadthFt, .clsLTL4HeightFt, .clsLTL4LengthInchs, .clsLTL4BreadthInchs, .clsLTL4HeightInchs, .clsLTL4LengthMeter, .clsLTL4BreadthMeter, .clsLTL4HeightMeter", function () {
        $(this).numeric({ allowPlus:false, allowMinus: false, allowThouSep: false, min:0.001, maxPreDecimalPlaces:4, maxDecimalPlaces:3 });
    });

    /* 4-3 LTL-4 (Unit Weight). */
    $('body').on("keypress", ".clsLTL4MaxWeightGms, .clsLTL4MaxWeightKgs, .clsLTL4MaxWeightMts", function () {
        $(this).numeric({ allowPlus:false, allowMinus: false, allowThouSep: false, min:0.001, maxPreDecimalPlaces:5, maxDecimalPlaces:3 });
    });

    /* 4-3 LTL-4 (Unit Weight). */
    $('body').on("keypress", ".clsLTL4MaxWeightGms, .clsLTL4MaxWeightKgs, .clsLTL4MaxWeightMts", function () {
        $(this).numeric({ allowPlus:false, allowMinus: false, allowThouSep: false, min:0.001, maxPreDecimalPlaces:5, maxDecimalPlaces:3 });
    });

    /* ----------------------- New Validations : End ---------------------- */

    /* 1 Digits 2 Decimals */
    $('body').on("keypress", ".onedigittwodecimals_deciVal", function (e) {
        $(this).numeric({ maxPreDecimalPlaces:1, maxDecimalPlaces:2 });
    });

    /* 2 Digits 2 Decimals */
    $('body').on("keypress", ".twodigitstwodecimals_deciVal", function (e) {
        $(this).numeric({ maxPreDecimalPlaces:2, maxDecimalPlaces:2 });
    });

    /* 2 Digits 3 Decimals */
    $('body').on("keypress", ".twodigitsthreedecimals_deciVal", function (e) {
        $(this).numeric({ maxPreDecimalPlaces:2, maxDecimalPlaces:3 });
    });

    /* 3 Digits 2 Decimals */
    $('body').on("keypress", ".threedigitstwodecimals_deciVal", function (e) {
        $(this).numeric({ maxPreDecimalPlaces:3, maxDecimalPlaces:2 });
    });

    /* 3 Digits 3 Decimals */
    $('body').on("keypress", ".threedigitsthreedecimals_deciVal", function (e) {
        $(this).numeric({ maxPreDecimalPlaces:3, maxDecimalPlaces:3 });
    });

    /* 4 Digits 2 Decimals */
    $('body').on("keypress", ".fourdigitstwodecimals_deciVal", function (e) {
        $(this).numeric({ maxPreDecimalPlaces:4, maxDecimalPlaces:2 });
    });

    /* 4 Digits 3 Decimals */
    $('body').on("keypress", ".fourdigitsthreedecimals_deciVal", function (e) {
        $(this).numeric({ maxPreDecimalPlaces:4, maxDecimalPlaces:3 });
    });
    
    /* 4 Digits 4 Decimals */
    $('body').on("keypress", ".fourdigitsfourdecimals_deciVal", function (e) {
        $(this).numeric({ maxPreDecimalPlaces:4, maxDecimalPlaces:4 });
    });

    /* 5 Digits 3 Decimals */
    $('body').on("keypress", ".fivedigitsthreedecimals_deciVal", function (e) {
        $(this).numeric({ maxPreDecimalPlaces:5, maxDecimalPlaces:3 });
    });

    /* 5 Digits 2 Decimals */
    $('body').on("keypress", ".fivedigitstwodecimals_deciVal", function (e) {
        $(this).numeric({ maxPreDecimalPlaces:5, maxDecimalPlaces:2 });
    });

    /* 6 Digits 2 Decimals */
    $('body').on("keypress", ".sixdigitstwodecimals_deciVal", function (e) {
        $(this).numeric({ maxPreDecimalPlaces:6, maxDecimalPlaces:2 });
    });

    /* 6 Digits 3 Decimals */
    $('body').on("keypress", ".sixdigitsthreedecimals_deciVal", function (e) {
        $(this).numeric({ maxPreDecimalPlaces:6, maxDecimalPlaces:3 });
    });

    /* 7 Digits 2 Decimals */
    $('body').on("keypress", ".sevendigitstwodecimals_deciVal", function (e) {
        $(this).numeric({ maxPreDecimalPlaces:7, maxDecimalPlaces:2 });
    });

    /* Max limit 3 */
    $('body').on("keypress", ".maxlimitthree_lmtVal", function (e) {
        $(this).numeric({ maxDigits:3,allowDecSep:false });
    });

    /* Max limit 5 */
    $('body').on("keypress", ".maxlimitfive_lmtVal", function (e) {
        $(this).numeric({ maxDigits:5,allowDecSep:false });
    });

    /* Max limit 6 */
/*    $('body').on("keypress", ".maxlimitsix_lmtVal", function (e) {
        $(this).numeric({ maxDigits:6,allowDecSep:false });
    });
*/
    /* Max limit 7 */
    $('body').on("keypress", ".maxlimitseven_lmtVal", function (e) {
        $(this).numeric({ maxDigits:7,allowDecSep:false,allowPlus:false,allowMinus:false });
    });

    /* Only numerberic */
    $('body').on("keypress", ".numericvalidation", function (e) {
        $(this).numeric({allowPlus:false,allowMinus:false,allowDecSep:false});
    });

    /* Alphabets Only */
    $('body').on("keypress", ".alphaonly_strVal", function (e) {
        $(this).alpha();
    });

    /* Alphabets with space */
    $('body').on("keypress", ".alphaspace_strVal", function (e) {
        $(this).alpha({allowSpace:true});
    });

    /* Alpha Numeric with space */
    $('body').on("keypress", ".alphanumeric_withSpace", function (e) {
        $(this).alphanum({allowSpace:true});
    });

    /* Alpha Numeric with out space */
    $('body').on("keypress", ".alphanumeric_strVal", function (e) {
        $(this).alphanum({allowSpace:false});
    });

    /* Alpha Numeric only */
    $('body').on("keypress", ".alphanumericonly_strVal", function (e) {
        $(this).alphanum();
    });

    /* Alpha Numeric only */
    $('body').on("keypress", ".alphanumericonlywithout_strVal", function (e) {
        $(this).alphanum();
    });

    /* Alpha Numeric only */
    $('body').on("keypress", ".numericvalidation_withoutsinglequote", function (e) {
        $(this).numeric({allowPlus:false,allowMinus:false,allowDecSep:false});
    });

    /* Alpha Numeric with space */
    $('body').on("keypress", ".alphanumericspace_strVal", function (e) {
        $(this).alphanum({allowSpace:true});
    });

    $('body').on("keypress", ".tendigitstwodecimals_deciVal", function (e) {
        $(this).numeric({ maxPreDecimalPlaces:10, maxDecimalPlaces:2 });
    });


    $('body').on("keypress", ".termMin2d", function (e) {
        $(this).numeric({ min:0.01,maxPreDecimalPlaces:4, maxDecimalPlaces:2 });
    });

    $('body').on("keypress", ".termMin4d, .clsKGperCCM", function (e) {
        $(this).numeric({ min:0.0001,maxPreDecimalPlaces:4, maxDecimalPlaces:4 });
    });

   /* $('body').on("keypress", ".numericvalidation_autopop", function (e) {
        $(this).alphanum({allowSpace:true});
    });*/
    

    /******* Dont Remove below function ******/
   /* $('body').on("keypress", ".clsAutoDisable", function (e) {
        var keycode = e.keyCode || e.which;
        if(keycode == 8){
            $(this).removeClass("clsAutoDisable")
            return true;
        }else{
            e.preventDefault();    
        }
    }); */

});


$(document).on("keypress", ".numericvalidation_autopop", function (event) {
    var keycode = event.keyCode || event.which;
    if (!(event.shiftKey == false && (keycode == 9 || keycode == 8 || keycode == 37 || keycode == 39 ||  keycode == 46 ||  (keycode >= 48 && keycode <= 57)))) {
        event.preventDefault();
    }

});


$(document).on("keypress", ".maxlimitsix_lmtVal", function (event) {
    if ($(this).val().length < 6 || event.keyCode == 46 || event.keyCode == 8) {
         return true;
    }else{
        return false;
    }
});  

function commaSeparateNo(x, boolType){
    
    x=x.toString();
    var afterPoint = '';
    if(x.indexOf('.') > 0)
       afterPoint = x.substring(x.indexOf('.'),x.length);
    x = Math.floor(x);
    x=x.toString();
    var lastThree = x.substring(x.length-3);
    var otherNumbers = x.substring(0,x.length-3);
    if(otherNumbers != '')
        lastThree = ',' + lastThree;
    var res = otherNumbers.replace(/\B(?=(\d{2})+(?!\d))/g, ",") + lastThree + afterPoint;

    if(!boolType)
        return res; 
    else 
        return res+'/-';
}

/* Load Lease term based on from & to dates */
function loadLeaseterms(from_date, to_date){
    var data = {'from_date': from_date, 'to_date': to_date}
    $("#lease_terms, #lease_type, #lkp_trucklease_lease_term_id").empty().append('<option value="">Lease Term*</option>').selectpicker('refresh');
    if( from_date != '' && to_date != '' ){
        $.ajax({
            type: "POST",
            url: '/trucklease/ajxleaseterms',
            data: data,
            dataType: 'json',
            success: function(resData) {
                if (resData.success == true) {
                    var htmlText = '';
                    $.each(resData.optHtml, function(k, v) {
                        htmlText += '<option value="'+k+'">'+v+'</option>';
                    });
                    $("#lease_terms, #lease_type, #lkp_trucklease_lease_term_id").append(htmlText);
                    $('#lease_terms, #lease_type, #lkp_trucklease_lease_term_id').selectpicker('refresh');
                    /* Checking element exists or not */
                    if ($('#daysDiffCnt').length) {
                        $("#daysDiffCnt").val(resData.daysDiff);   
                    }else{
                        $('#lease_terms, #lease_type').next().append('<input type="hidden" name="daysDiffCnt" id="daysDiffCnt" value="'+ resData.daysDiff +'" />');
                    }
                }
            },
            error: function(request, status, error) {},
        });
    }
}