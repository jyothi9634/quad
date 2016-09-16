<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

/*Route::get('/', function () {
    return view('default');
});*/

Route::get('/', 'RegisterController@home');

Route::controllers(['auth' => 'Auth\AuthController','password' => 'Auth\PasswordController']);
Route::get('/home', 'HomeController@index');
Route::post('/home', 'HomeController@index');
Route::resource('downloadtemplate', 'SellerController@downloadTemplate');
Route::resource('downloaderrorstemplate', 'SellerController@downloadErrorsTemplate');

Route::resource('getdescriptionuser', 'NetworkController@getDescription');
Route::resource('getdescriptiongroup', 'NetworkController@getCommunityGroupDescription');


Route::get('/messages','HomeController@messages');
Route::post('/messages','HomeController@messages');

Route::get('/sentmessages','HomeController@sentMessages');
Route::post('/sentmessages','HomeController@sentMessages');

Route::get('/home/{service_id}', 'HomeController@index');
Route::get('getmessagedetails/{messageId}/{ordid}/{term}', 'HomeController@getMessageDetails');
Route::post('setmessagedetails/', 'HomeController@setMessageDetails');
Route::get('setmessagedetails/', 'HomeController@setMessageDetails');
Route::get('/getnamelist', 'HomeController@getNameList');
Route::post('/getnamelist', 'HomeController@getNameList');
Route::resource('/getprincipalplace', 'RegisterController@getPrincipalPlace');


Route::get('/getfiledownload/{path}', 'HomeController@getfiledownload');

Route::post('settermbuyerbooknow', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'BuyerTermController@setTermBuyerBooknow',
		'roles' => [BUYER,SELLER] // Only a buyer can access this route
		]);

 Route::get('settermbuyerbooknow', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'BuyerTermController@setTermBuyerBooknow',
		'roles' => [BUYER,SELLER] // Only a buyer can access this route
		]);
/*
 |
 |--------------------------------------------------------------------------------
 |
 |REGISTRATION PAGES ROUTES
 |
 |---------------------------------------------------------------------------------
 |
 |
 |
 |
 */

//Route::get('/', 'RegisterController@register');
Route::get('/register', 'RegisterController@register');
Route::get('/individualRegistration', 'RegisterController@individualRegistration');
Route::get('/register/buyer', 'RegisterController@buyer');
Route::post('checkunique', 'RegisterController@checkUnique');
Route::any('checkExistence', 'RegisterController@checkExistence');
Route::post('buyerregister', 'RegisterController@registerBuyer');
Route::resource('userregister','RegisterController@register');
Route::get('register/buyer_business', 'RegisterController@buyerBusiness');
Route::post('register/buyer_business', 'RegisterController@registerBusinessBuyer');
Route::get('register/select_user', 'RegisterController@selectUser');
Route::resource('select_role', 'RegisterController@selectRole');
Route::get('register/seller_business', 'RegisterController@sellerBusiness');
Route::post('register/seller_business', 'RegisterController@registerSellerBusiness');
Route::post('home/uploadLogo', 'HomeController@uploadLogo');
Route::post('home/askmeLater', 'HomeController@askmeLater');
Route::resource('/editmyprofile', 'HomeController@myProfile');
Route::post('thankyou/sellerConfirm', 'ThankyouController@sellerConfirm');
Route::resource('thankyou', 'ThankyouController@index');
Route::resource('thankyou_seller', 'ThankyouController@seller_thanx');
Route::resource('user_activation', 'ActivationController@index');
Route::get('edit/buyer','RegisterController@viewEditBuyer');
Route::get('register/edit/buyer_business','RegisterController@viewEditBuyerBusiness');
Route::post('register/edit_buyer/{id}', 'RegisterController@editBuyer');
Route::post('register/edit_buyer_business/{id}', 'RegisterController@editBuyerBusiness');
Route::post('register/getState', 'RegisterController@getState');
Route::post('register/getIntraLocality', 'RegisterController@getIntraLocality');
Route::post('register/getpmCity', 'RegisterController@getPaMCity');
Route::post('register/edit_seller_business/{id}', 'RegisterController@editSellerBusiness');
Route::get('register/edit/seller_business','RegisterController@viewEditSellerBusiness');
Route::post('register/otp', 'RegisterController@createOtp');
Route::post('/register/validateotp', 'RegisterController@validateotp');

Route::resource('socialregister','RegisterController@socialRegister');
//SELLERS ORDER LIST
Route::get('orders/seller_orders', [
	'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
	'uses' => 'OrdersController@sellerOrders',
	'roles' => [BUYER,SELLER] // Only a seller can access this route
]);
Route::get('orders/buyer_orders', [
	'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
	'uses' => 'OrdersController@buyerOrders',
	'roles' => [BUYER,SELLER] // Only a buyer can access this route
]);
Route::get('buyerordersearch', [
	'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
	'uses' => 'OrdersController@buyerOrders',
	'roles' => [BUYER,SELLER] // Only a seller can access this route
]);
Route::post('buyerordersearch', [
	'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
	'uses' => 'OrdersController@buyerOrders',
	'roles' => [BUYER,SELLER] // Only a buyer can access this route
]);
Route::get('/orders/details/{id}', [
	'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
	'uses' => 'OrdersController@showDetails',
	'roles' => [BUYER,SELLER] // Only a seller can access this route
]);
Route::get('/orders/buyer_orderdetails/{id}', [
	'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
	'uses' => 'OrdersController@buyerOrderShowDetails',
	'roles' => [BUYER,SELLER] // Only a buyer can access this route
]);

Route::get('sellerorderSearch', [
'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
'uses' => 'OrdersController@sellerOrders',
'roles' => [BUYER,SELLER] // Only a buyer can access this route
]);
Route::post('sellerorderSearch', [
'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
'uses' => 'OrdersController@sellerOrders',
'roles' => [BUYER,SELLER] // Only a buyer can access this route
]);

Route::get('/register/seller', 'RegisterController@seller');
Route::post('/register/seller', 'RegisterController@registerSeller');
Route::get('/register/edit_seller', 'RegisterController@viewEditSeller');
Route::post('/register/edit_seller/{id}', 'RegisterController@editSeller');



Route::get('facebook', 'RegisterController@facebook_redirect');
Route::get('account/facebook', 'RegisterController@facebook');

Route::get('linkedin', 'RegisterController@linkedin_redirect');
Route::get('account/linkedin', 'RegisterController@linkedin');
Route::get('google', 'RegisterController@google_redirect');
Route::get('account/google', 'RegisterController@google');

Route::get('facebook?key=login', 'RegisterController@facebook_redirect');
Route::get('linkedin?key=login', 'RegisterController@linkedin_redirect');
Route::get('google?key=login', 'RegisterController@google_redirect');

/*
|--------------------------------------------------------------------------
| Buyer Routes
|--------------------------------------------------------------------------
|
| Here is the buyer get quote routes.
|
*/


Route::get('createbuyerquote', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'BuyerController@CreateBuyerQuote',
		'roles' => [BUYER,SELLER] // Only a buyer can access this route
		]);
Route::post('createbuyerquote', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'BuyerController@CreateBuyerQuote',
		'roles' => [BUYER,SELLER] // Only a buyer can access this route
		]);

Route::get('getNoofLoads', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'BuyerController@getNoofLoads',
		'roles' => [BUYER,SELLER] // Only a buyer can access this route
		]);
Route::post('getNoofLoads', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'BuyerController@getNoofLoads',
		'roles' => [BUYER,SELLER] // Only a buyer can access this route
		]);
Route::post('getvolumetype', [
	'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
	'uses' => 'SellerController@getVolumeType',
	'roles' => [BUYER,SELLER] // Only a buyer can access this route
	]);
Route::get('getvolumetype', [
	'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
	'uses' => 'SellerController@getVolumeType',
	'roles' => [BUYER,SELLER] // Only a buyer can access this route
	]);
Route::get('getSellerslist', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'BuyerController@getSellerslist',
		'roles' => [BUYER,SELLER] // Only a buyer can access this route
		]);
Route::post('getSellerslist', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'BuyerController@getSellerslist',
		'roles' => [BUYER,SELLER] // Only a buyer can access this route
		]);

Route::get('getTermSellerList', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'BuyerTermController@getTermSellerList',
		'roles' => [BUYER,SELLER] // Only a buyer can access this route
		]);
Route::post('getTermSellerList', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'BuyerTermController@getTermSellerList',
		'roles' => [BUYER,SELLER] // Only a buyer can access this route
		]);

Route::get('getEditSellerslist', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'BuyerController@getEditSellerslist',
		'roles' => [BUYER,SELLER] // Only a buyer can access this route
		]);
Route::post('getEditSellerslist', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'BuyerController@getEditSellerslist',
		'roles' => [BUYER,SELLER] // Only a buyer can access this route
		]);

Route::get('editbuyerquote/{mainid}/{itemid}', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'BuyerController@editBuyerquote',
		'roles' => [BUYER,SELLER] // Only a buyer can access this route
		]);
Route::post('editbuyerquote/{mainid}/{itemid}', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'BuyerController@editBuyerquote',
		'roles' => [BUYER,SELLER] // Only a buyer can access this route
		]);

Route::get('updateBuyer', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'BuyerController@updateBuyer',
		'roles' => [BUYER,SELLER] // Only a buyer can access this route
		]);
Route::post('updateBuyer', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'BuyerController@updateBuyer',
		'roles' => [BUYER,SELLER] // Only a buyer can access this route
		]);

Route::get('byersearchresults', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'BuyerListingController@buyerSearchResults',
		'roles' => [BUYER,SELLER] // Only a buyer can access this route
		]);
Route::post('byersearchresults', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'BuyerListingController@buyerSearchResults',
		'roles' => [BUYER,SELLER] // Only a buyer can access this route
		]);

Route::get('buyersearch', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'BuyerListingController@buyerSearch',
		'roles' => [BUYER,SELLER] // Only a buyer can access this route
		]);
Route::post('buyersearch', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'BuyerListingController@buyerSearch',
		'roles' => [BUYER,SELLER] // Only a buyer can access this route
		]);


Route::get('buyerposts', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'BuyerListingController@buyerPostsList',
		'roles' => [BUYER,SELLER] // Only a buyer can access this route
		]);
Route::post('buyerposts', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'BuyerListingController@buyerPostsList',
		'roles' => [BUYER,SELLER] // Only a buyer can access this route
		]);

Route::resource('setbuyerbooknow', 'BuyerController@setBuyerBooknow');

/*Route::get('setbuyerbooknow', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'BuyerController@setBuyerBooknow',
		'roles' => [BUYER,SELLER] // Only a buyer can access this route
		]);
Route::post('setbuyerbooknow', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'BuyerController@setBuyerBooknow',
		'roles' => [BUYER,SELLER] // Only a buyer can access this route
		]);*/

Route::resource('getVehicles', 'BuyerController@getVehicles');


Route::get('termviewcountupdate', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'BuyerTermController@termViewCount',
		'roles' => [BUYER,SELLER] // Only a seller can access this route
		]);
Route::post('termviewcountupdate', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'BuyerTermController@termViewCount',
		'roles' => [BUYER,SELLER] // Only a buyer can access this route
		]);

Route::resource('sellerViewCountUpdate', 'BuyerController@sellerViewCountUpdate');

Route::resource('getCapacity', 'BuyerController@getCapacity');
Route::resource('getNoofLoads', 'BuyerController@getNoofLoads');
Route::resource('autocomplete', 'BuyerController@autocomplete');
Route::resource('autocompletevehicles', 'OrdersController@autocompleteVehicles');
Route::resource('getvehicledetails', 'OrdersController@getVehicleDetails');

//Route::resource('createbuyerquote', 'BuyerController@CreateBuyerQuote');
//Route::resource('getSellerslist', 'BuyerController@getSellerslist');
//Route::get('editbuyerquote/{mainid}/{itemid}', 'BuyerController@editBuyerquote');
//Route::resource('updateBuyer', 'BuyerController@updateBuyer');
//Route::get('buyersearch', 'BuyerController@buyerSearch');
//Route::resource('byersearchresults', 'BuyerController@buyerSearchResults');
//Route::get('buyerposts', 'BuyerListingController@buyerPostsList');
//Route::post('setbuyerbooknow', 'BuyerController@setBuyerBooknow');

/*** buyer-post-counter-offer page ***/
Route::get('buyerbooknow/{id}/{quoteItemId}', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'BuyerController@buyerBooknow',
		'roles' => [BUYER,SELLER] // Only a buyer can access this route
		]);
Route::post('buyerbooknow/{quoteItemId}/{buyerQuoteSellerPriceId}', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'BuyerController@buyerBooknow',
		'roles' => [BUYER,SELLER] // Only a buyer can access this route
		]);
Route::get('buyerbooknowforleads/{quoteItemId}/{buyerQuoteSellerPriceId}', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'BuyerController@buyerBooknowForLeads',
		'roles' => [BUYER,SELLER] // Only a buyer can access this route
		]);
Route::post('buyerbooknowforleads/{quoteItemId}/{buyerQuoteSellerPriceId}', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'BuyerController@buyerBooknowForLeads',
		'roles' => [BUYER,SELLER] // Only a buyer can access this route
		]);
Route::get('buyerbooknowforsearch/{quoteItemId}', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'BuyerController@buyerBooknowFromSearchList',
		'roles' => [BUYER,SELLER] // Only a buyer can access this route
		]);
Route::post('buyerbooknowforsearch/{quoteItemId}}', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'BuyerController@buyerBooknowFromSearchList',
		'roles' => [BUYER,SELLER] // Only a buyer can access this route
		]);
Route::get('getbuyercounteroffer/{id}', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'BuyerController@getPostBuyerCounterOffer',
		'roles' => [BUYER,SELLER] // Only a buyer can access this route
		]);
//Route::post('getbuyercounteroffer/{id}', 'BuyerController@getPostBuyerCounterOffer');
Route::post('getbuyercounteroffer/{id}', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'BuyerController@getPostBuyerCounterOffer',
		'roles' => [BUYER,SELLER] // Only a buyer can access this route
		]);
//Route::get('getbuyercounteroffer/{id}/{comparisonType}', 'BuyerController@getPostBuyerCounterOffer');
Route::get('getbuyercounteroffer/{id}/{comparisonType}', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'BuyerController@getPostBuyerCounterOffer',
		'roles' => [BUYER,SELLER] // Only a buyer can access this route
		]);
//Route::post('getbuyercounteroffer/{id}/{comparisonType}', 'BuyerController@getPostBuyerCounterOffer');
Route::post('getbuyercounteroffer/{id}/{comparisonType}', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'BuyerController@getPostBuyerCounterOffer',
		'roles' => [BUYER,SELLER] // Only a buyer can access this route
		]);

Route::get('getbuyercounteroffer/{id}/{comparisonType}/{priceVal}', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'BuyerController@getPostBuyerCounterOffer',
		'roles' => [BUYER,SELLER] // Only a buyer can access this route
]);
//Route::post('getbuyercounteroffer/{id}/{comparisonType}', 'BuyerController@getPostBuyerCounterOffer');
Route::post('getbuyercounteroffer/{id}/{comparisonType}/{priceVal}', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'BuyerController@getPostBuyerCounterOffer',
		'roles' => [BUYER,SELLER] // Only a buyer can access this route
]);

Route::get('getbuyercounteroffer/{id}/{comparisonType}/{priceVal}/{checkIds}', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'BuyerController@getPostBuyerCounterOffer',
		'roles' => [BUYER,SELLER] // Only a buyer can access this route
]);
//Route::post('getbuyercounteroffer/{id}/{comparisonType}', 'BuyerController@getPostBuyerCounterOffer');
Route::post('getbuyercounteroffer/{id}/{comparisonType}/{priceVal}/{checkIds}', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'BuyerController@getPostBuyerCounterOffer',
		'roles' => [BUYER,SELLER] // Only a buyer can access this route
]);
//Buyer Posts List
//Route::resource('buyerposts/search', 'BuyerListingController@buyerPostsList');
Route::get('buyerposts/search', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'BuyerListingController@buyerPostsList',
		'roles' => [BUYER,SELLER] // Only a buyer can access this route
		]);
Route::post('buyerposts/search', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'BuyerListingController@buyerPostsList',
		'roles' => [BUYER,SELLER] // Only a buyer can access this route
		]);
//Route::post('setbuyercounteroffer/', 'BuyerController@setPostBuyerCounterOffer');
Route::post('setbuyercounteroffer/', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'BuyerController@setPostBuyerCounterOffer',
		'roles' => [BUYER,SELLER] // Only a buyer can access this route
		]);
Route::post('getfreightdetails/', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'BuyerController@getFreightDetailsForPtl',
		'roles' => [BUYER,SELLER] // Only a buyer can access this route
		]);
Route::post('getSellerfreightdetails/', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'BuyerController@getSellerFreightDetailsForPtl',
		'roles' => [BUYER,SELLER] // Only a buyer can access this route
]);
Route::post('getcouriertermfreightdetails/', [
'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
'uses' => 'BuyerTermController@getCourierTermFreightDetails',
'roles' => [BUYER] // Only a buyer can access this route
]);
//Route::get('cancelenquiry/{buyerQuoteItemId}', 'BuyerController@cancelEnquiry');
Route::get('cancelenquiry/{buyerQuoteItemId}', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'BuyerController@cancelEnquiry',
		'roles' => [BUYER,SELLER] // Only a buyer can access this route
		]);
//Route::post('cancelenquiry/$buyerQuoteItemId', 'BuyerController@cancelEnquiryForBuyer');
//Route::get('getbooknowdetails', 'BuyerController@getbooknowdetails');
Route::get('getbooknowdetails', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'BuyerController@getbooknowdetails',
		'roles' => [BUYER,SELLER] // Only a buyer can access this route
		]);
/*
 |--------------------------------------------------------------------------
 | Application Routes For Seller
 |--------------------------------------------------------------------------
 |Here we can call all methods for seller post actions
 */


Route::resource('getvehicletype', 'SellerController@getVehicleType');
Route::resource('autocomplete', 'SellerController@autocomplete');
Route::resource('autocompleteto', 'SellerController@autocompleteto');
Route::resource('lineitemscheck', 'SellerController@lineItemsCheck');
Route::resource('truckleaselineitemscheck', 'TruckLeaseSellerController@truckLeaseLineItemsCheck');
Route::resource('lineitemscheckptl', 'SellerController@lineItemsCheckPtl');
Route::resource('lineitemscheckrelocation', 'SellerController@lineItemsCheckRelocation');
Route::resource('checksubcriptionuser', 'SellerController@checkSubcriptionUser');

Route::get('createseller', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'SellerController@CreateSeller',
		'roles' => [BUYER,SELLER] // Only a seller can access this route
]);

Route::get('addseller', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'SellerController@addSeller',
		'roles' => [BUYER,SELLER] // Only a seller can access this route
]);

Route::post('addseller', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'SellerController@addSeller',
		'roles' => [BUYER,SELLER] // Only a seller can access this route
]);

//Truck Lease seller 
Route::get('truckleaseaddseller', [
'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
'uses' => 'TruckLeaseSellerController@addSeller',
'roles' => [BUYER,SELLER] // Only a seller can access this route
]);
Route::post('truckleaseaddseller', [
'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
'uses' => 'TruckLeaseSellerController@addSeller',
'roles' => [BUYER,SELLER] // Only a seller can access this route
]);


//Seller Posts List


Route::post('sellerposts', [
	'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
	'uses' => 'SellerListingController@sellerPostsList',
	'roles' => [BUYER,SELLER] // Only a seller can access this route
]);
Route::post('sellerposts/cancelsellerpost', 'SellerListingController@cancelSellerPost');
//Route::post('sellerposts', 'SellerListingController@sellerPostsList');

//Route::get('sellerposts', 'SellerListingController@sellerPostsList');
Route::post('sellerposts/{id}', [
	'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
	'uses' => 'SellerListingController@sellerPostsList',
	'roles' => [BUYER,SELLER] // Only a seller can access this route
]);
Route::get('sellerposts/{id}', [
	'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
	'uses' => 'SellerListingController@sellerPostsList',
	'roles' => [BUYER,SELLER] // Only a seller can access this route
]);
Route::get('sellerlist', [
	'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
	'uses' => 'SellerListingController@sellerLists',
	'roles' => [BUYER,SELLER] // Only a seller can access this route
]);
Route::post('sellerlist', [
	'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
	'uses' => 'SellerListingController@sellerLists',
	'roles' => [BUYER,SELLER] // Only a seller can access this route
]);
//Route::post('sellerposts/{id}', 'SellerListingController@sellerPostsList');
//Route::get('sellerposts/{id}', 'SellerListingController@sellerPostsList');
//Route::get('sellerlist', 'SellerController@sellerLists');
//Route::post('sellerlist', 'SellerController@sellerLists');

Route::resource('buyerlist', 'SellerController@buyerList');
//Route::get('/updateseller/{sid}', 'SellerController@updateSeller');
//Route::post('/updateseller/{sid}', 'SellerController@updateSeller');


Route::get('/updateseller/{sid}', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'SellerController@updateSeller',
		'roles' => [BUYER,SELLER] // Only a seller can access this route
]);


Route::post('/updateseller/{sid}', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'SellerController@updateSeller',
		'roles' => [BUYER,SELLER] // Only a seller can access this route
]);

Route::get('/updateseller/{sid}/{lineitem}', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'SellerController@updateSeller',
		'roles' => [BUYER,SELLER] // Only a seller can access this route
]);


Route::post('/updateseller/{sid}/{lineitem}', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'SellerController@updateSeller',
		'roles' => [BUYER,SELLER] // Only a seller can access this route
]);

//Route::resource('/updatesellerpost', 'SellerController@updateSellerPost');


Route::get('/updatesellerpost', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'SellerController@updateSellerPost',
		'roles' => [BUYER,SELLER] // Only a seller can access this route
]);

Route::post('/updatesellerpost', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'SellerController@updateSellerPost',
		'roles' => [BUYER,SELLER] // Only a seller can access this route
]);

//seller post details
Route::get('sellerpostdetail/{id}', [
	'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
	'uses' => 'SellerController@sellerPostDetails',
	'roles' => [BUYER,SELLER] // Only a seller can access this route
]);
Route::post('sellersubmitquote', [
	'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
	'uses' => 'SellerController@sellerQuoteSubmit',
	'roles' => [BUYER,SELLER] // Only a seller can access this route
]);
Route::post('sellersearchsubmitquote', [
	'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
	'uses' => 'SellerController@sellerSearchQuoteSubmit',
	'roles' => [BUYER,SELLER] // Only a seller can access this route
]);
Route::post('sellerfinalquotesubmit', [
	'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
	'uses' => 'SellerController@sellerFinalQuoteSubmit',
	'roles' => [BUYER,SELLER] // Only a seller can access this route
]);
Route::get('selleraccept/{bid}/{bqid}/{spqi}', [
	'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
	'uses' => 'SellerController@sellerQuoteAcceptance',
	'roles' => [BUYER,SELLER] // Only a seller can access this route
]);
Route::get('selleraccept/{bid}/{bqid}', [
	'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
	'uses' => 'SellerController@sellerQuoteAcceptance',
	'roles' => [BUYER,SELLER] // Only a seller can access this route
]);

Route::post('selleraccept', [
'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
'uses' => 'SellerController@sellerCounterAcceptance',
'roles' => [BUYER,SELLER] // Only a seller can access this route
]);
Route::get('sellerQuotePublicAcceptance/{bid}/{bqid}/{spqi}/{quote}/{pid}', [
	'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
	'uses' => 'SellerController@sellerQuotePublicAcceptance',
	'roles' => [BUYER,SELLER] // Only a seller can access this route
]);
Route::post('sellerfirmacceptance', [
'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
'uses' => 'SellerController@sellerAcceptance',
'roles' => [BUYER,SELLER] // Only a seller can access this route
]);



Route::get('sellerpublicaccept/{bid}/{bqid}/{frmcity}/{tocity}/{quote}/{search}', [
	'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
	'uses' => 'SellerController@sellerQuotePublicSearchAcceptance',
	'roles' => [BUYER,SELLER] // Only a seller can access this route
]);

Route::post('sellerpublicaccept', [
'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
'uses' => 'SellerController@sellerSearchAcceptance',
'roles' => [BUYER,SELLER] // Only a seller can access this route
]);

Route::post('sellercounterquotesubmit', [
'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
'uses' => 'SellerController@sellerCounterAcceptence',
'roles' => [BUYER,SELLER] // Only a seller can access this route
]);

Route::get('selleraccept/{bid}/{bqid}/{frmcity}/{tocity}/{quote}/{search}', [
	'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
	'uses' => 'SellerController@sellerQuotePublicSearchAcceptance',
	'roles' => [BUYER,SELLER] // Only a seller can access this route
]);
//equipment routes
Route::get('equipmentregister', [
	'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
	'uses' => 'EquipmentController@create',
	'roles' => [BUYER,SELLER] // Only a seller can access this route
]);
Route::post('equipmentregister', [
	'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
	'uses' => 'EquipmentController@create',
	'roles' => [BUYER,SELLER] // Only a seller can access this route
]);
Route::get('list', [
	'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
	'uses' => 'EquipmentController@index',
	'roles' => [BUYER,SELLER] // Only a seller can access this route
]);
Route::get('equip_destroy/{id}', [
	'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
	'uses' => 'EquipmentController@destroy',
	'roles' => [BUYER,SELLER] // Only a seller can access this route
]);
/*Route::post('equip_destroy', [
	'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
	'uses' => 'EquipmentController@destroy',
	'roles' => [BUYER,SELLER] // Only a seller can access this route
]);*/
Route::get('equip_edit/{id}', [
	'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
	'uses' => 'EquipmentController@edit',
	'roles' => [BUYER,SELLER] // Only a seller can access this route
]);
Route::post('equip_update/{id}', [
	'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
	'uses' => 'EquipmentController@update',
	'roles' => [BUYER,SELLER] // Only a seller can access this route
]);
Route::post('getcity', [
	'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
	'uses' => 'EquipmentController@getCity',
	'roles' => [BUYER,SELLER] // Only a seller can access this route
]);
Route::post('getlocality', [
	'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
	'uses' => 'EquipmentController@getLocality',
	'roles' => [BUYER,SELLER] // Only a seller can access this route
]);
Route::post('getdistrict', [
	'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
	'uses' => 'EquipmentController@getDistrict',
	'roles' => [BUYER,SELLER] // Only a seller can access this route
]);
//Route::resource('equipmentregister', 'EquipmentController@create');
//Route::get('list', 'EquipmentController@index');
//Route::resource('equip_destroy', 'EquipmentController@destroy');
//Route::get('equip_edit/{id}', 'EquipmentController@edit');
//Route::post('equip_update/{id}', 'EquipmentController@update');
//Route::post('getcity', 'EquipmentController@getCity');
//Route::post('getlocality', 'EquipmentController@getLocality');
//Route::post('getdistrict', 'EquipmentController@getDistrict');

//warehouse routes
Route::get('warehouseregister', [
	'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
	'uses' => 'WarehouseController@create',
	'roles' => [BUYER,SELLER] // Only a seller can access this route
]);
Route::post('warehouseregister', [
	'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
	'uses' => 'WarehouseController@create',
	'roles' => [BUYER,SELLER] // Only a seller can access this route
]);
Route::get('warehouselist', [
	'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
	'uses' => 'WarehouseController@index',
	'roles' => [BUYER,SELLER] // Only a seller can access this route
]);
Route::get('warehouse_destroy/{id}', [
	'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
	'uses' => 'WarehouseController@destroy',
	'roles' => [BUYER,SELLER] // Only a seller can access this route
]);
/*Route::post('warehouse_destroy', [
	'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
	'uses' => 'WarehouseController@destroy',
	'roles' => [BUYER,SELLER] // Only a seller can access this route
]);*/
Route::get('warehouse_edit/{id}', [
	'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
	'uses' => 'WarehouseController@edit',
	'roles' => [BUYER,SELLER] // Only a seller can access this route
]);
/*Route::post('warehouse_edit/{id}', [
	'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
	'uses' => 'WarehouseController@edit',
	'roles' => [BUYER,SELLER] // Only a seller can access this route
]);*/
Route::post('warehouse_update/{id}', [
	'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
	'uses' => 'WarehouseController@update',
	'roles' => [BUYER,SELLER] // Only a seller can access this route
]);
//Route::resource('warehouseregister', 'WarehouseController@create');
//Route::get('warehouselist', 'WarehouseController@index');
//Route::resource('warehouse_destroy', 'WarehouseController@destroy');
//Route::resource('warehouse_edit', 'WarehouseController@edit');
//Route::post('warehouse_update/{id}', 'WarehouseController@update');

//vehicle routes
Route::get('vehicleregister', [
	'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
	'uses' => 'VehicleController@create',
	'roles' => [BUYER,SELLER] // Only a seller can access this route
]);
Route::post('vehicleregister', [
	'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
	'uses' => 'VehicleController@create',
	'roles' => [BUYER,SELLER] // Only a seller can access this route
]);
Route::get('vehiclelist', [
	'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
	'uses' => 'VehicleController@index',
	'roles' => [BUYER,SELLER] // Only a seller can access this route
]);
Route::get('vehicle_destroy/{id}', [
	'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
	'uses' => 'VehicleController@destroy',
	'roles' => [BUYER,SELLER] // Only a seller can access this route
]);
/*Route::post('vehicle_destroy', [
	'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
	'uses' => 'VehicleController@destroy',
	'roles' => [BUYER,SELLER] // Only a seller can access this route
]);*/
Route::get('vehicle_edit/{id}', [
	'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
	'uses' => 'VehicleController@edit',
	'roles' => [BUYER,SELLER] // Only a seller can access this route
]);
/*Route::post('vehicle_edit/{id}', [
	'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
	'uses' => 'VehicleController@edit',
	'roles' => [BUYER,SELLER] // Only a seller can access this route
]);*/
Route::post('vehicle_update/{id}', [
	'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
	'uses' => 'VehicleController@update',
	'roles' => [BUYER,SELLER] // Only a seller can access this route
]);
//Route::resource('vehicleregister', 'VehicleController@create');
//Route::get('vehiclelist', 'VehicleController@index');
//Route::resource('vehicle_destroy', 'VehicleController@destroy');
//Route::resource('vehicle_edit', 'VehicleController@edit');
//Route::post('vehicle_update/{id}', 'VehicleController@update');

//equipment routes
Route::get('equipmentupload', [
	'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
	'uses' => 'EquipmentController@upload',
	'roles' => [BUYER,SELLER] // Only a seller can access this route
]);
Route::post('equipmentupload', [
	'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
	'uses' => 'EquipmentController@upload',
	'roles' => [BUYER,SELLER] // Only a seller can access this route
]);
Route::get('vehicleupload', [
	'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
	'uses' => 'VehicleController@upload',
	'roles' => [BUYER,SELLER] // Only a seller can access this route
]);
Route::post('vehicleupload', [
	'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
	'uses' => 'VehicleController@upload',
	'roles' => [BUYER,SELLER] // Only a seller can access this route
]);
Route::get('warehouseupload', [
	'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
	'uses' => 'WarehouseController@upload',
	'roles' => [BUYER,SELLER] // Only a seller can access this route
]);
Route::post('warehouseupload', [
	'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
	'uses' => 'WarehouseController@upload',
	'roles' => [BUYER,SELLER] // Only a seller can access this route
]);
//Route::resource('equipmentupload', 'EquipmentController@upload');
//Route::resource('vehicleupload', 'VehicleController@upload');
//Route::resource('warehouseupload', 'WarehouseController@upload');

//consignment routes
Route::get('consignment_pickup/{id}', [
	'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
	'uses' => 'OrdersController@consignmentPickup',
	'roles' => [BUYER,SELLER] // Only a seller can access this route
]);
Route::post('addvehicle', [
	'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
	'uses' => 'OrdersController@addVehicle',
	'roles' => [BUYER,SELLER] // Only a seller can access this route
]);
Route::post('consignment_pickup/{id}', [
	'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
	'uses' => 'OrdersController@consignmentPickup',
	'roles' => [BUYER,SELLER] // Only a seller can access this route
]);
Route::post('addlocation', [
	'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
	'uses' => 'OrdersController@addLocation',
	'roles' => [BUYER,SELLER] // Only a seller can access this route
]);
//Route::get('consignment_pickup/{id}', 'OrdersController@consignmentPickup');
//Route::post('addvehicle', 'OrdersController@addVehicle');
//Route::post('consignment_pickup/{id}', 'OrdersController@consignmentPickup');
//Route::post('addlocation', 'OrdersController@addLocation');
//Route::resource('sellersearchbuyers', 'SellerController@SellerSearchBuyers');

Route::get('sellersearchbuyers', [
	'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
	'uses' => 'SellerListingController@SellerSearchBuyers',
	'roles' => [BUYER,SELLER] // Only a seller can access this route
]);
Route::post('sellersearchbuyers', [
	'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
	'uses' => 'SellerListingController@SellerSearchBuyers',
	'roles' => [BUYER,SELLER] // Only a seller can access this route
]);


//Route::get('buyersearchresults', 'SellerListingController@BuyerSearchResults');
//Route::post('buyersearchresults', 'SellerListingController@BuyerSearchResults');



Route::get('buyersearchresults', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'SellerListingController@SellerSearchResults',
		'roles' => [BUYER,SELLER] // Only a seller can access this route
]);

Route::post('buyersearchresults', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'SellerListingController@SellerSearchResults',
		'roles' => [BUYER,SELLER] // Only a seller can access this route
]);

Route::get('termsellersearchresults', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'SellerTermController@TermSellerSearchResults',
		'roles' => [BUYER,SELLER] // Only a seller can access this route
		]);

Route::post('termsellersearchresults', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'SellerTermController@TermSellerSearchResults',
		'roles' => [BUYER,SELLER] // Only a seller can access this route
		]);

//cart actions
Route::resource('cart', 'CheckoutController@index');
Route::resource('cart/deleteitem', 'CheckoutController@delete');
Route::resource('clearcart', 'CheckoutController@clear');
Route::get('checkout', 'CheckoutController@makePayment');
Route::post('payment', 'CheckoutController@postPayment');
Route::post('confirmpayment', 'CheckoutController@confirmPayment');
Route::get('confirmorder/{id}', 'CheckoutController@confirmOrder');


Route::resource('buyerpostcancel', 'BuyerController@buyerpostcancel');
Route::post('relocationsellerpostcancel/{id}', 'RelocationSellerController@deleteSellerRelocationPost');

//seller Cancel Posts
Route::get('seller/cancel_post', 'CancelPostController@index');
//buyer order cancel
Route::get('/orders/cancel/{id}', 'OrdersController@cancelOrder');

/**
 * 
 * INTRACITY LINKS
 * 
 * 
 */

//create seller post
Route::get('intracity/create_post', 'IntracitySellerController@index');
Route::post('sellerintracity/loadlocality', 'IntracitySellerController@loadIntraLocality');


Route::get('intracity/buyer_post', [
'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
'uses' => 'IntracityBuyerController@buyerPost',
'roles' => [BUYER,SELLER] // Only a seller can access this route
]);

Route::post('intracity/create_buyer_post', [
'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
'uses' => 'IntracityBuyerController@createBuyerPost',
'roles' => [BUYER,SELLER] // Only a seller can access this route
]);

Route::get('intracity/buyerpostslist', [
'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
'uses' => 'IntracityBuyerController@viewBuyerPostsList',
'roles' => [BUYER,SELLER] // Only a seller can access this route
]);

Route::get('intracity/buyerpostdetails/{id}', [
'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
'uses' => 'IntracityBuyerController@buyerPostDetails',
'roles' => [BUYER,SELLER] // Only a seller can access this route
]);

//Ajax call for cancel intracity buyer post
Route::post('/intracitybuyer/cancelpost', 'IntracityBuyerController@cancelBuyerPost');


Route::get('intracity/buyerpostsearch', [
'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
'uses' => 'IntracityBuyerController@viewBuyerPostsList',
'roles' => [BUYER,SELLER] // Only a buyer can access this route
]);
Route::post('intracity/buyerpostsearch', [
'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
'uses' => 'IntracityBuyerController@viewBuyerPostsList',
'roles' => [BUYER,SELLER] // Only a buyer can access this route
]);

/*
|--------------------------------------------------------------------------
| Seller Routes For PTL
|--------------------------------------------------------------------------
|
| Here is the Seller routes for PTL.
| Seller Create Post, Update post, Posts List, Search quotes
|
*/


Route::get('ptl/createsellerpost', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'PtlSellerController@ptlCreateSellerPost',
		'roles' => [BUYER,SELLER] // Only a seller can access this route
]);

Route::post('ptl/createsellerpost', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'PtlSellerController@ptlCreateSellerPost',
		'roles' => [BUYER,SELLER] // Only a seller can access this route
]);
Route::get('sellerpostcreation', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'PtlSellerController@ptlPostCreation',
		'roles' => [BUYER,SELLER] // Only a seller can access this route
]);
Route::post('sellerpostcreation', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'PtlSellerController@ptlPostCreation',
		'roles' => [BUYER,SELLER] // Only a seller can access this route
]);
Route::get('/ptl/updatesellerpost/{sid}', [
	'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
	'uses' => 'PtlSellerController@ptlUpdateSellerPost',
	'roles' => [BUYER,SELLER] // Only a seller can access this route
]);


Route::post('/ptl/updatesellerpost/{sid}', [
	'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
	'uses' => 'PtlSellerController@ptlUpdateSellerPost',
	'roles' => [BUYER,SELLER] // Only a seller can access this route
]);

Route::get('ptl/sellerlist', [
'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
'uses' => 'PtlSellerListingController@sellerLists',
'roles' => [BUYER,SELLER] // Only a seller can access this route
]);


/*Route::get('ptl/sellersearchbuyers', [
	'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
	'uses' => 'PtlSellerListingController@SellerSearchBuyers',
	'roles' => [BUYER,SELLER] // Only a seller can access this route
]);
Route::post('ptl/sellersearchbuyers', [
	'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
	'uses' => 'PtlSellerListingController@SellerSearchBuyers',
	'roles' => [BUYER,SELLER] // Only a seller can access this route
]);

Route::get('ptl/byersearchresults', [
	'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
	'uses' => 'PtlBuyerListingController@buyerSearchResults',
	'roles' => [BUYER,SELLER] // Only a buyer can access this route
]);
Route::post('ptl/byersearchresults', [
	'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
	'uses' => 'PtlBuyerListingController@buyerSearchResults',
	'roles' => [BUYER,SELLER] // Only a buyer can access this route
]);*/



/*
|--------------------------------------------------------------------------
| Buyer Routes For PTL
|--------------------------------------------------------------------------
|
| Here is the buyer get quote routes for PTL.
| Below application route is create getquote for buyers
|
*/
Route::resource('intracityautocomplete', 'IntracityBuyerController@autocomplete');
Route::resource('intracityautocompleteto', 'IntracityBuyerController@autocompleteto');

Route::get('ptl/createbuyerquote', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'PtlBuyerController@ptlCreateBuyerQuote',
		'roles' => [BUYER,SELLER] // Only a buyer can access this route
		]);
Route::post('ptl/createbuyerquote', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'PtlBuyerController@ptlCreateBuyerQuote',
		'roles' => [BUYER,SELLER] // Only a buyer can access this route
		]);
Route::get('getintrabooknowdetails', 'BuyerController@getIntraBookNowDetails');

Route::get('getPtlSellerList', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'PtlBuyerController@getPtlSellerList',
		'roles' => [BUYER,SELLER] // Only a buyer can access this route
		]);
Route::post('getPtlSellerList', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'PtlBuyerController@getPtlSellerList',
		'roles' => [BUYER,SELLER] // Only a buyer can access this route
		]);

Route::get('getPtlEditSellerList', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'PtlBuyerController@getPtlEditSellerList',
		'roles' => [BUYER,SELLER] // Only a buyer can access this route
		]);
Route::post('getPtlEditSellerList', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'PtlBuyerController@getPtlEditSellerList',
		'roles' => [BUYER,SELLER] // Only a buyer can access this route
		]);

Route::get('ptl/buyerposts', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'BuyerListingController@buyerPostsList',
		'roles' => [BUYER,SELLER] // Only a buyer can access this route
		]);
Route::post('ptl/buyerposts', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'BuyerListingController@buyerPostsList',
		'roles' => [BUYER,SELLER] // Only a buyer can access this route
		]);

Route::get('editseller/{quoteid}', [
'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
'uses' => 'PtlBuyerController@editBuyerquoteSeller',
'roles' => [BUYER,SELLER] // Only a buyer can access this route
]);
Route::post('editseller/{quoteid}', [
'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
'uses' => 'PtlBuyerController@editBuyerquoteSeller',
'roles' => [BUYER,SELLER] // Only a buyer can access this route
]);


Route::get('ptlupdateseller', [
'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
'uses' => 'PtlBuyerController@updatePtlSeller',
'roles' => [BUYER,SELLER] // Only a buyer can access this route
]);
Route::post('ptlupdateseller', [
'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
'uses' => 'PtlBuyerController@updatePtlSeller',
'roles' => [BUYER,SELLER] // Only a buyer can access this route
]);



Route::get('ptl/byersearchresults', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'BuyerListingController@buyerSearchResults',
		'roles' => [BUYER,SELLER] // Only a buyer can access this route
		]);
Route::post('ptl/byersearchresults', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'BuyerListingController@buyerSearchResults',
		'roles' => [BUYER,SELLER] // Only a buyer can access this route
		]);

Route::get('buyermainposts', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'BuyerListingController@buyerMainLists',
		'roles' => [BUYER,SELLER] // Only a seller can access this route
		]);
Route::post('buyermainposts', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'BuyerListingController@buyerMainLists',
		'roles' => [BUYER,SELLER] // Only a seller can access this route
		]);

Route::resource('ptlPincodesAutocomplete', 'PtlBuyerController@ptlPincodesAutocomplete');
Route::resource('ptlToPincodesCheckout', 'PtlBuyerController@ptlToPincodesCheckout');
Route::resource('ptlPincodesAutocompleteCourier', 'PtlBuyerController@ptlPincodesAutocompleteCourier');
Route::resource('ptlZoneAutocomplete', 'PtlSellerController@ptlZoneAutocomplete');
Route::resource('ptlZoneAutocompleteCourier', 'PtlSellerController@ptlZoneAutocompleteCourier');
Route::resource('ptlZoneAutocompleteCourierSearch', 'PtlSellerController@ptlZoneAutocompleteCourierSearch');
Route::resource('ptlTransitAutofill', 'PtlSellerController@ptlTransitAutofill');
Route::resource('ptlZoneAutocompletesearch', 'PtlSellerController@ptlZoneAutocompleteSearch');
Route::resource('getVolumeWeight', 'PtlBuyerController@getVolumeWeight');
Route::resource('getPinlocationInItems', 'PtlBuyerController@getPinlocationInItems');

/*
 * |
 * |MSP (Mobile service provider) ROUTES
 * |
 */
Route::resource('msp/seller_quotes', 'MspController@createSellerQuotes');

Route::resource('msp/order_confirmation', 'MspController@confirmOrders');

Route::get('msp/update_orders', 'MspController@viewUpdateOrders');
//ajax call
Route::post('/intracity/updateorder', 'MspController@updateOrders');


Route::post('sellerpostcancel', 'SellerListingController@sellerPostCancel');
/*
 * |
 * |PLT MASTERS ROUTES
 * |
 */
Route::get('ptlmasters/tier', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'PtlSellerController@viewTier',
		'roles' => [BUYER,SELLER] // Only a buyer can access this route
		]);
Route::get('ptlmasters/zone', [
'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
'uses' => 'PtlSellerController@viewZone',
'roles' => [BUYER,SELLER] // Only a buyer can access this route
]);
Route::get('ptlmasters/pincode', [
'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
'uses' => 'PtlSellerController@viewPincode',
'roles' => [BUYER,SELLER] // Only a buyer can access this route
]);
Route::get('ptlmasters/sector', [
'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
'uses' => 'PtlSellerController@viewSector',
'roles' => [BUYER,SELLER] // Only a buyer can access this route
]);
Route::get('ptlmasters/transit_matrix', [
'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
'uses' => 'PtlSellerController@viewTransitMatrix',
'roles' => [BUYER,SELLER] // Only a buyer can access this route
]);
Route::get('ptlmasters/add_pincode', [
'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
'uses' => 'PtlSellerController@viewAddPincode',
'roles' => [BUYER,SELLER] // Only a buyer can access this route
]);

Route::post('ptlmasters/tier', [
'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
'uses' => 'PtlSellerController@viewTier',
'roles' => [BUYER,SELLER] // Only a buyer can access this route
]);
Route::post('ptlmasters/zone', [
'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
'uses' => 'PtlSellerController@viewZone',
'roles' => [BUYER,SELLER] // Only a buyer can access this route
]);
Route::post('ptlmasters/pincode', [
'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
'uses' => 'PtlSellerController@viewPincode',
'roles' => [BUYER,SELLER] // Only a buyer can access this route
]);
Route::post('ptlmasters/sector', [
'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
'uses' => 'PtlSellerController@viewSector',
'roles' => [BUYER,SELLER] // Only a buyer can access this route
]);
Route::post('ptlmasters/transit_matrix', [
'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
'uses' => 'PtlSellerController@viewTransitMatrix',
'roles' => [BUYER,SELLER] // Only a buyer can access this route
]);
Route::post('ptlmasters/add_pincode', [
'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
'uses' => 'PtlSellerController@viewAddPincode',
'roles' => [BUYER,SELLER] // Only a buyer can access this route
]);


//ajax calls for editable zone master
Route::resource('ptlmasters/editPtlZone', 'PtlSellerController@editPtlZone');
//Route::resource('ptlmasters/editableZones', 'PtlSellerController@editPtlZone');
Route::resource('ptlmasters/deletePtlZone', 'PtlSellerController@deletePtlZone');

//ajax calls for editable tier master
Route::resource('ptlmasters/editPtlTier', 'PtlSellerController@editPtlTier');
//Route::resource('ptlmasters/editableTiers', 'PtlSellerController@editPtlTier');
Route::resource('ptlmasters/deletePtlTier', 'PtlSellerController@deletePtlTier');

//ajax calls for editable sector master
Route::resource('ptlmasters/editPtlSector', 'PtlSellerController@editPtlSector');
//Route::resource('ptlmasters/editableSectors', 'PtlSellerController@editPtlSector');
Route::resource('ptlmasters/deletePtlSector', 'PtlSellerController@deletePtlSector');

//ajax calls for editable pincode master
Route::resource('ptlmasters/editPtlPincode', 'PtlSellerController@editPtlPincode');
Route::resource('ptlmasters/fillEditPtlSector', 'PtlSellerController@fillEditPtlSector');
Route::resource('ptlmasters/deletePtlPincode', 'PtlSellerController@deletePtlPincode');
Route::resource('ptlmasters/checkPtlPincode', 'PtlSellerController@checkPtlPincode');

//ajax calls for editable transitdays
Route::resource('ptlmasters/editTransits', 'PtlSellerController@editPtlTransit');
Route::resource('zipautocomplete', 'PtlSellerController@autocompletePincodes');
Route::resource('ptlmasters/fillform', 'PtlSellerController@autoFillForm');


Route::resource('testaction', 'TestController@index');
Route::resource('dailymisreport', 'TestController@misreport');
Route::get('updateenquiries/{postid}', 'TestController@updateenquiries');

Route::resource('setbuyerpost','BuyerController@CreateSearchBuyerQuote');
/*Route::get('setbuyerpost', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'BuyerController@CreateSearchBuyerQuote',
		'roles' => [BUYER,SELLER] // Only a buyer can access this route
		]);
Route::post('setbuyerpost', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'BuyerController@CreateSearchBuyerQuote',
		'roles' => [BUYER,SELLER] // Only a buyer can access this route
		]);*/
//Check Session and get its value
Route::post('pincodeupload', [
'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
'uses' => 'PtlSellerController@upload',
'roles' => [BUYER,SELLER] // Only a buyer can access this route
]);

Route::get('/processing-status', function()
{
    return Session::get('service_id');
});

//schedular for post status updation
Route::get('updatepoststatus', 'BuyerController@updatePostStatus');

//Check Session and get its value
Route::post('/set_session_service', function()
{
    Session::put('service_id', $_REQUEST['service']);
    return "1";
});


Route::resource('temp_role', 'RegisterController@tempRole');

/* Term buyer posts listing page */

Route::get('buyertermposts', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'BuyerTermController@buyerTermPosts',
		'roles' => [BUYER,SELLER] // Only a buyer can access this route
]);

Route::get('gettermbuyercounteroffer/{id}', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'BuyerTermController@getTermPostBuyerCounterOffer',
		'roles' => [BUYER,SELLER] // Only a buyer can access this route
]);

Route::get('termbiddatedit/{id}', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'BuyerTermController@BidDateEditForm',
		'roles' => [BUYER,SELLER] // Only a buyer can access this route
]);
Route::post('termbiddatedit/{id}', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'BuyerTermController@BidDateEditForm',
		'roles' => [BUYER,SELLER] // Only a buyer can access this route
]);

Route::get('termupdateBiddate', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'BuyerTermController@UpdateBidDate',
		'roles' => [BUYER,SELLER] // Only a buyer can access this route
]);
Route::post('termupdateBiddate', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'BuyerTermController@UpdateBidDate',
		'roles' => [BUYER,SELLER] // Only a buyer can access this route
]);

//Draft edit functionality
Route::get('termdraftedit/{id}', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'BuyerTermController@BidEditDraftForm',
		'roles' => [BUYER,SELLER] // Only a buyer can access this route
]);
Route::post('termdraftedit/{id}', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'BuyerTermController@BidEditDraftForm',
		'roles' => [BUYER,SELLER] // Only a buyer can access this route
]);


Route::get('sellertermposts', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'SellerTermController@sellerTermPosts',
		'roles' => [BUYER,SELLER] // Only a buyer can access this route
		]);
Route::post('termintialquoteseller', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'SellerTermController@termIntialQuoteSeller',
		'roles' => [BUYER,SELLER] // Only a seller can access this route
		]);
Route::post('couriertermintialquoteseller', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'SellerTermController@courierTermIntialQuoteSeller',
		'roles' => [BUYER,SELLER] // Only a seller can access this route
		]);

Route::post('generatecontractbuyer', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'BuyerTermController@generateContractBuyer',
		'roles' => [BUYER,SELLER] // Only a buyer can access this route
]);


Route::get('setcontractstatus/{quoteid}', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'SellerController@setcontractstatus',
		'roles' => [BUYER,SELLER] // Only a buyer can access this route
]);
Route::post('setcontractstatus/{quoteid}', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'SellerController@setcontractstatus',
		'roles' => [BUYER,SELLER] // Only a buyer can access this route
]);
Route::get('contract/details/{quoteid}', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'SellerTermController@showContractDetails',
		'roles' => [BUYER,SELLER] // Only a seller can access this route
]);
Route::get('getcontractdownload/{quoteid}', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'SellerController@getcontractdownload',
		'roles' => [SELLER,BUYER] // Only a buyer can access this route
]);
Route::get('cancelbuyerterm/{buyerQuoteId}', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'BuyerTermController@cancelBuyerTerm',
		'roles' => [BUYER,SELLER] // Only a buyer can access this route
]);
Route::get('contract/buyerdetails/{buyerQuoteId}', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'BuyerTermController@showBuyerContractDetails',
		'roles' => [BUYER,SELLER] // Only a seller can access this route
]);

Route::post('termbooknow', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'BuyerTermController@TermBooknow',
		'roles' => [BUYER,SELLER] // Only a buyer can access this route
]);
Route::get('termbooknow', [
	'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
	'uses' => 'BuyerTermController@TermBooknow',
	'roles' => [BUYER,SELLER] // Only a buyer can access this route
]);

Route::get('comparesellerquotes/{buyerQuoteId}', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'BuyerTermController@CompareSellerQuotes',
		'roles' => [BUYER,SELLER] // Only a buyer can access this route
]);

Route::get('downloadbuyerbids/{buyerQuoteId}', [
	'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
	'uses' => 'BuyerTermController@DownloadBuyerBids',
	'roles' => [BUYER,SELLER] // Only a buyer can access this route
]);

Route::get('/confirmation', 'OrdersController@orderConfirmation');
Route::get('/getusername/{userid}', 'HomeController@getusername');
Route::post('/getusername', 'HomeController@getusername');
Route::post('/getorderno', 'HomeController@getOrderno');
Route::post('/getcontractno', 'HomeController@getContractno');
Route::post('/termpostitemupdate', 'BuyerTermController@termPostItemupdate');
Route::post('/termpostitemdelete', 'BuyerTermController@termPostItemDelete');
Route::post('/addgsaterms', 'OrdersController@addGsaTerms');
/**
 * Relocation routes - start
 */

Route::get('relocation/createsellerpost', [
	'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
	'uses' => 'RelocationSellerController@relocationCreateSellerPost',
	'roles' => [BUYER,SELLER] // Only a seller can access this route
]);
Route::post('relocation/createsellerpost', [
	'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
	'uses' => 'RelocationSellerController@relocationCreateSellerPost',
	'roles' => [BUYER,SELLER] // Only a seller can access this route
]);

Route::get('relocationsellerpostcreation', [
	'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
	'uses' => 'RelocationSellerController@relocationPostCreation',
	'roles' => [BUYER,SELLER] // Only a seller can access this route
]);
Route::post('relocationsellerpostcreation', [
	'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
	'uses' => 'RelocationSellerController@relocationPostCreation',
	'roles' => [BUYER,SELLER] // Only a seller can access this route
]);
Route::get('/relocation/updatesellerpost/{sid}', [
	'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
	'uses' => 'RelocationSellerController@relocationUpdateSellerPost',
	'roles' => [BUYER,SELLER] // Only a seller can access this route
]);


Route::post('/relocation/updatesellerpost/{sid}', [
	'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
	'uses' => 'RelocationSellerController@relocationUpdateSellerPost',
	'roles' => [BUYER,SELLER] // Only a seller can access this route
]);



Route::get('relocation/creatbuyerrpost', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'RelocationBuyerController@relocationCreateBuyerPost',
		'roles' => [BUYER,SELLER] // Only a seller can access this route
]);

Route::post('relocation/creatbuyerrpost', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'RelocationBuyerController@relocationCreateBuyerPost',
		'roles' => [BUYER,SELLER] // Only a seller can access this route
]);

Route::get('relocationbuyertermcreate', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'RelocationBuyerController@createRelocationTerm',
		'roles' => [BUYER,SELLER] // Only a seller can access this route
		]);

Route::post('relocationbuyertermcreate', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'RelocationBuyerController@createRelocationTerm',
		'roles' => [BUYER,SELLER] // Only a seller can access this route
		]);

Route::get('getpropertycft', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'RelocationBuyerController@getPropertyCft',
		'roles' => [BUYER,SELLER] // Only a seller can access this route
]);

Route::post('getpropertycft', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'RelocationBuyerController@getPropertyCft',
		'roles' => [BUYER,SELLER] // Only a seller can access this route
]);

Route::get('getpropertyparticulars', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'RelocationBuyerController@getPropertyParticulars',
		'roles' => [BUYER,SELLER] // Only a seller can access this route
]);

Route::post('getpropertyparticulars', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'RelocationBuyerController@getPropertyParticulars',
		'roles' => [BUYER,SELLER] // Only a seller can access this route
]);

Route::get('saveinventorydetails', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'RelocationBuyerController@saveInventoryDetails',
		'roles' => [BUYER,SELLER] // Only a seller can access this route
]);

Route::get('savesearchinventorydetails', [
	'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
	'uses' => 'RelocationBuyerController@savesearchinventorydetails',
	'roles' => [BUYER,SELLER] // Only a seller can access this route
]);

Route::post('saveinventorydetails', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'RelocationBuyerController@saveInventoryDetails',
		'roles' => [BUYER,SELLER] // Only a seller can access this route
]);


Route::post('saveinventorydetailsrelocean', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'RelocationBuyerController@saveinventorydetailsRelocationOcean',
		'roles' => [BUYER,SELLER] // Only a seller can access this route
]);
Route::get('saveinventorydetailsrelocean', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'RelocationBuyerController@saveinventorydetailsRelocationOcean',
		'roles' => [BUYER,SELLER] // Only a seller can access this route
]);


Route::post('savesearchinventorydetails', [
	'middleware' => ['auth', 'roles'], // A 'roles' middleware must be
	'uses' => 'RelocationBuyerController@savesearchinventorydetails',
	'roles' => [BUYER,SELLER] // Only a seller can access this route
]);
Route::get('relocationbuyerpostcreation', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'RelocationBuyerController@relocationBuyerPostcreation',
		'roles' => [BUYER,SELLER] // Only a seller can access this route
]);

Route::post('relocationbuyerpostcreation', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'RelocationBuyerController@relocationBuyerPostcreation',
		'roles' => [BUYER,SELLER] // Only a seller can access this route
]);

Route::get('editrelocationbuyerquote/{buyer_post_id}', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'RelocationBuyerController@editBuyerQuote',
		'roles' => [BUYER,SELLER] // Only a seller can access this route
]);

Route::post('editrelocationbuyerquote/{buyer_post_id}', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'RelocationBuyerController@editBuyerQuote',
		'roles' => [BUYER,SELLER] // Only a seller can access this route
]);

Route::get('updaterelocationbuyer', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'RelocationBuyerController@updateRelocationBuyer',
		'roles' => [BUYER,SELLER] // Only a seller can access this route
]);

Route::post('updaterelocationbuyer', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'RelocationBuyerController@updateRelocationBuyer',
		'roles' => [BUYER,SELLER] // Only a seller can access this route
]);


Route::get('updateptldistricts', 'BuyerController@updatePtlDistricts');

/**
 * Relocation routes - start
 */
//New route for updateviewcount
Route::post('updatesellerpostview', 'SellerListingController@updateSellerPostView');

Route::get('/updatbuyertermpost/{id}', [
	'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
	'uses' => 'BuyerTermController@UpdateTermPost',
	'roles' => [BUYER,SELLER] // Only a seller can access this route
]);


Route::post('/updatbuyertermpost/{id}', [
	'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
	'uses' => 'BuyerTermController@UpdateTermPost',
	'roles' => [BUYER,SELLER] // Only a seller can access this route
]);


Route::get('/updatetermseller/{id}', [
	'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
	'uses' => 'BuyerTermController@UpdateTermPostSeller',
	'roles' => [BUYER,SELLER] // Only a seller can access this route
]);


Route::post('/updatetermseller/{id}', [
	'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
	'uses' => 'BuyerTermController@UpdateTermPostSeller',
	'roles' => [BUYER,SELLER] // Only a seller can access this route
]);

/**
 * SMS routes - start
 */

Route::post('sendsms', [
	'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
	'uses' => 'SmsController@send',
	'roles' => [BUYER,SELLER] // Only a seller can access this route
]);
Route::get('sendsms', [
	'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
	'uses' => 'SmsController@send',
	'roles' => [BUYER,SELLER] // Only a seller can access this route
]);

Route::get('changepassword', [
	'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
	'uses' => 'RegisterController@changeUserPassword',
	'roles' => [BUYER,SELLER] // All Roles can access this route
]);

Route::post('changepassword', [
	'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
	'uses' => 'RegisterController@changeUserPassword',
	'roles' => [BUYER,SELLER] // All Roles can access this route
]);

Route::get('smsstatus', [
	'uses' => 'SmsController@smsstatus',
]);

Route::post('smsstatus', [
	'uses' => 'SmsController@smsstatus',
]);

/**
 * Schedule routes - start
 */
Route::get('storesmsstatus', [
	'uses' => 'ScheduleController@storeSmsStatus',
]);

Route::post('storesmsstatus', [
	'uses' => 'ScheduleController@storeSmsStatus',
]);

//seller-buyer toggle URL
Route::post('/check_toggle_role', 'RegisterController@checkToggleRole');
Route::post('/toggle_role', 'RegisterController@toggleUserRole');
Route::post('register/buyer_toggle_seller_business', 'RegisterController@buyerToggleSellerBusiness');
Route::post('/switch_roles', 'RegisterController@switchRoles');
Route::resource('register/buyer_switch_seller', 'RegisterController@buyerSwitchSeller');
Route::resource('register/switch_seller', 'RegisterController@switchSeller');
Route::resource('/fill_seller_details', 'RegisterController@getBuyerData');
Route::resource('/switch_buyer', 'RegisterController@switchBuyer');

// Payment-gateway return URLS
Route::post('hdfcconfirm', [
	'uses' => 'CheckoutController@hdfcresponse',
]);


//market leads
Route::post('buyermarketleads/{id}', [
	'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
	'uses' => 'SellerListingController@sellerMarketleads',
	'roles' => [BUYER,SELLER] // Only a seller can access this route
]);
Route::get('buyermarketleads/{id}', [
	'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
	'uses' => 'SellerListingController@sellerMarketleads',
	'roles' => [BUYER,SELLER] // Only a seller can access this route
]);

 
//Community starts here - 6-04-2016 .
Route::post('/community/home', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'CommunityController@communityHome',
		'roles' => [BUYER,SELLER] // Only a seller can access this route
		]);
Route::get('/community/home', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'CommunityController@communityHome',
		'roles' => [BUYER,SELLER] // Only a seller can access this route
		]);

Route::post('/community/creategroup', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'CommunityController@communityCreateGroupForm',
		'roles' => [BUYER,SELLER] // Only a seller can access this route
		]);
Route::get('/community/creategroup', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'CommunityController@communityCreateGroupForm',
		'roles' => [BUYER,SELLER] // Only a seller can access this route
		]);
Route::get('individualsearch', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'CommunityController@communityIndividualSearch',
		'roles' => [BUYER,SELLER] // Only a seller can access this route
		]);

Route::post('individualsearch', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'CommunityController@communityIndividualSearch',
		'roles' => [BUYER,SELLER] // Only a seller can access this route
		]);

Route::get('groupsearch', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'CommunityController@communityGroupSearch',
		'roles' => [BUYER,SELLER] // Only a seller can access this route
		]);

Route::post('groupsearch', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'CommunityController@communityGroupSearch',
		'roles' => [BUYER,SELLER] // Only a seller can access this route
		]);

Route::get('organizationsearch', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'CommunityController@communityOrganizationSearch',
		'roles' => [BUYER,SELLER] // Only a seller can access this route
		]);

Route::post('organizationsearch', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'CommunityController@communityOrganizationSearch',
		'roles' => [BUYER,SELLER] // Only a seller can access this route
		]);

Route::get('createcommunitynewgroup', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'CommunityController@createCommunityNewGroup',
		'roles' => [BUYER,SELLER] // Only a buyer can access this route
		]);
Route::post('createcommunitynewgroup', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'CommunityController@createCommunityNewGroup',
		'roles' => [BUYER,SELLER] // Only a buyer can access this route
		]);

Route::get('community/editgroup/{id}', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'CommunityController@editCommunityNewGroupForm',
		'roles' => [BUYER,SELLER] // Only a buyer can access this route
		]);
Route::post('community/editgroup/{id}', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'CommunityController@editCommunityNewGroupForm',
		'roles' => [BUYER,SELLER] // Only a buyer can access this route
		]);

Route::get('updatecommunitygroup', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'CommunityController@updateCommunityGroup',
		'roles' => [BUYER,SELLER] // Only a buyer can access this route
		]);
Route::post('updatecommunitygroup', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'CommunityController@updateCommunityGroup',
		'roles' => [BUYER,SELLER] // Only a buyer can access this route
		]);

Route::get('community/groupdetails/{groupid}', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'CommunityController@displayGroupDetails',
		'roles' => [BUYER,SELLER] // Only a buyer can access this route
		]);
Route::post('community/groupdetails/{groupid}', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'CommunityController@displayGroupDetails',
		'roles' => [BUYER,SELLER] // Only a buyer can access this route
		]);

Route::get('communityconversationinsert', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'CommunityController@insertCommunityGroupCoversation',
		'roles' => [BUYER,SELLER] // Only a buyer can access this route
		]);
Route::post('communityconversationinsert', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'CommunityController@insertCommunityGroupCoversation',
		'roles' => [BUYER,SELLER] // Only a buyer can access this route
		]);

Route::resource("checkGroupNameExists", "CommunityController@checkGroupNameExists");

//Route::resource("insertCommunityPostMainComment", "CommunityController@insertCommunityPostMainComment");
Route::get('insertCommunityPostMainComment', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'CommunityController@insertCommunityPostMainComment',
		'roles' => [BUYER,SELLER] // Only a buyer can access this route
		]); 

Route::resource("communitypostcommentinsert", "CommunityController@insertCommunityPostMainComment");
Route::resource("insertPostLikes", "CommunityController@insertPostLikes");
Route::resource('deleteGroupComment', 'CommunityController@deleteGroupComment');

Route::get('becomeamember', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'CommunityController@becomeMember',
		'roles' => [BUYER,SELLER] // Only a buyer can access this route
		]);

Route::get('member_activation', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'CommunityController@activate_member',
		'roles' => [BUYER,SELLER] // Only a buyer can access this route
		]);

Route::get('admin_member_active/{gid}/{uid}', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'CommunityController@adminMemberActive',
		'roles' => [BUYER,SELLER] // Only a buyer can access this route
		]);

Route::get('community_deactivited_member/{gid}/{uid}/{gstatus}', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'CommunityController@GroupDeActivated',
		'roles' => [BUYER,SELLER] // Only a buyer can access this route
		]);

Route::get('/getpartners', 'CommunityController@getPartnerList');

Route::post('invite_member', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'CommunityController@inviteMember',
		'roles' => [BUYER,SELLER] // Only a buyer can access this route
		]);

Route::resource("postLikesTextChange", "CommunityController@postLikesTextChange");
Route::get('group/deletemember/{gid}/{uid}', 'CommunityController@delete');


Route::get('loadmorecomments', [
	'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
	'uses' => 'CommunityController@loadMoreComments',
	'roles' => [BUYER,SELLER] // Only a buyer can access this route
]);
Route::post('loadmorecomments', [
	'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
	'uses' => 'CommunityController@loadMoreComments',
	'roles' => [BUYER,SELLER] // Only a buyer can access this route
]);


// Volty GPS Routes
Route::post('/gpsregister', 'GpsController@register');
Route::get('/gpsregister', 'GpsController@register');
Route::post('/gpstrack', 'GpsController@track');
Route::get('/gpstrack', 'GpsController@track');
Route::post('/gpstrackhistory', 'GpsController@trackHistory');
Route::get('/gpstrackhistory', 'GpsController@trackHistory');
Route::post('/gpsstore', 'GpsController@store');
Route::get('/gpsstore', 'GpsController@store');


//Network
Route::get('network/network_feeds', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
	 	'uses' => 'NetworkController@networkFeeds',
		'roles' => [BUYER,SELLER] // Only a seller can access this route
		]);


//Network starts here - 6-04-2016 .
Route::get('network', [
	'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
	'uses' => 'NetworkController@index',
	'roles' => [BUYER,SELLER] // Only a seller can access this route
]);
//@Shriram
Route::post('network/ajxpostfeed', [
	'middleware' => ['auth', 'roles'], 
	'uses' => 'NetworkController@ajxpostfeed',
	'roles' => [BUYER,SELLER] // Only a seller can access this route
]);
//@Shriram
Route::post('network/ajxpostcomment', [
	'middleware' => ['auth', 'roles'], 
	'uses' => 'NetworkController@ajxpostcomment',
	'roles' => [BUYER,SELLER] // Only a seller can access this route
]);
//@Shriram
Route::post('network/ajxshowpost', [
	'middleware' => ['auth', 'roles'], 
	'uses' => 'NetworkController@ajx_showpost',
	'roles' => [BUYER,SELLER] // Only a seller can access this route
]);
//@Shriram - Feed like action
Route::post('network/ajxfeedlike', [
	'middleware' => ['auth', 'roles'], 
	'uses' => 'NetworkController@ajxfeedlike',
	'roles' => [BUYER,SELLER] // Only a seller can access this route
]);
//@Shriram - Feed share html & action 
Route::post('network/ajxsharefeed', [
	'middleware' => ['auth', 'roles'], 
	'uses' => 'NetworkController@ajx_sharepost',
	'roles' => [BUYER,SELLER] // Only a seller can access this route
]);
//@Shriram - Load more comments
Route::post('network/ajxloadcomm', [
	'middleware' => ['auth', 'roles'], 
	'uses' => 'NetworkController@ajx_load_more_comments',
	'roles' => [BUYER,SELLER] // Only a seller can access this route
]);
Route::get('network/profile/{id}', [
	'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
	'uses' => 'NetworkController@networkProfile',
	'roles' => [BUYER,SELLER] // Only a seller can access this route
]);
Route::get('/network/partnerslist/{id}', [
	'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
	'uses' => 'NetworkController@listOfPartners',
	'roles' => [BUYER,SELLER] // Only a seller can access this route
]);
Route::get('/network/followlist/{id}', [
'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
'uses' => 'NetworkController@listOffollowing',
'roles' => [BUYER,SELLER] // Only a seller can access this route
]);
Route::get('/network/recomendationslist/{id}', [
	'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
	'uses' => 'NetworkController@listOfRecomendations',
	'roles' => [BUYER,SELLER] // Only a seller can access this route
]);
Route::get('/network/jobslist/{id}', [
	'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
	'uses' => 'NetworkController@listOfJobs',
	'roles' => [BUYER,SELLER] // Only a seller can access this route
]);
Route::get('/network/articleslist/{id}', [
	'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
	'uses' => 'NetworkController@listOfArticles',
	'roles' => [BUYER,SELLER] // Only a seller can access this route
]);
Route::post('/follow', [
	'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
	'uses' => 'NetworkController@followProfile',
	'roles' => [BUYER,SELLER] // Only a seller can access this route
]);
Route::post('/partnerrequest', [
'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
'uses' => 'NetworkController@partnerRequestSend',
'roles' => [BUYER,SELLER] // Only a seller can access this route
]);
Route::post('/acceptpartner', [
'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
'uses' => 'NetworkController@partnerRequestAcceptnece',
'roles' => [BUYER,SELLER] // Only a seller can access this route
]);
Route::post('/acceptrecomendation', [
'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
'uses' => 'NetworkController@partnerRecomendation',
'roles' => [BUYER,SELLER] // Only a seller can access this route
]);
Route::post('/addrecomendation', [
'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
'uses' => 'NetworkController@addReccomendation',
'roles' => [BUYER,SELLER] // Only a seller can access this route
]);
Route::post('deletepostcomment', 'NetworkController@cancelPostComment');
Route::post('/addmessagetoprofile', [
'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
'uses' => 'NetworkController@addProfileMessage',
'roles' => [BUYER,SELLER] // Only a seller can access this route
]);
Route::resource('sharelistofusers', 'NetworkController@listOfUsers');
Route::post('/shareprofile', [
'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
'uses' => 'NetworkController@sharingProfile',
'roles' => [BUYER,SELLER] // Only a seller can access this route
]);

Route::post('/forgetinventorysession', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'BuyerTermController@forgetInventorySession',
		'roles' => [BUYER] // Only a seller can access this route
]);


Route::post('/searchfollwingusers', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'NetworkController@searchFollwers',
		'roles' => [BUYER,SELLER] // Only a seller can access this route
]);
Route::post('/searchpartnerusers', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'NetworkController@searchPartners',
		'roles' => [BUYER,SELLER] // Only a seller can access this route
]);
Route::post('/searchgroupusers', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'NetworkController@searchGroups',
		'roles' => [BUYER,SELLER] // Only a seller can access this route
]);


/*
 |--------------------------------------------------------------------------
 | Application Routes For Truck Haul
 |--------------------------------------------------------------------------
 |Here we can call all methods for Buyer/seller post/Get actions
 */
Route::get('truckhaul/createbuyerquote', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'TruckHaulBuyerController@CreateBuyerQuote',
		'roles' => [BUYER,SELLER] // Only a buyer can access this route
		]);
Route::post('truckhaul/createbuyerquote', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'TruckHaulBuyerController@CreateBuyerQuote',
		'roles' => [BUYER,SELLER] // Only a buyer can access this route
		]);

Route::get('truckhaul/createsellerpost', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'TruckHaulSellerController@CreateSellerPost',
		'roles' => [BUYER,SELLER] // Only a seller can access this route
]);

Route::get('truckhaul/addsellerpost', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'TruckHaulSellerController@addSellerPost',
		'roles' => [BUYER,SELLER] // Only a seller can access this route
]);

Route::post('truckhaul/addsellerpost', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'TruckHaulSellerController@addSellerPost',
		'roles' => [BUYER,SELLER] // Only a seller can access this route
]);
Route::resource('truckhaul/lineitemscheck', 'TruckHaulSellerController@lineItemsCheck');

/*
 |--------------------------------------------------------------------------
| Application Routes For Truck Haul
|--------------------------------------------------------------------------
|Here we can call all methods for Buyer/seller post/Get actions
*/
Route::get('trucklease/createbuyerquote', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'TruckLeaseBuyerController@CreateBuyerQuote',
		'roles' => [BUYER,SELLER] // Only a buyer can access this route
		]);
Route::post('trucklease/createbuyerquote', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'TruckLeaseBuyerController@CreateBuyerQuote',
		'roles' => [BUYER,SELLER] // Only a buyer can access this route
		]);

Route::get('trucklease/createsellerpost', [
'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
'uses' => 'TruckLeaseSellerController@CreateSellerPost',
'roles' => [BUYER,SELLER] // Only a buyer can access this route
]);
Route::post('trucklease/createsellerpost', [
'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
'uses' => 'TruckLeaseSellerController@CreateSellerPost',
'roles' => [BUYER,SELLER] // Only a buyer can access this route
]);


Route::post('getbuyerviewcount', [
'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
'uses' => 'SellerController@viewCountForBuyerFromSeller',
'roles' => [BUYER,SELLER] // Only a buyer can access this route
]);

Route::get('getbuyerviewcount', [
'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
'uses' => 'SellerController@viewCountForBuyerFromSeller',
'roles' => [BUYER,SELLER] // Only a buyer can access this route
]);

Route::get('getcageweight', [
	'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
	'uses' => 'RelocationSellerController@getcageweight',
	'roles' => [BUYER,SELLER] // Only a buyer can access this route
]);


/*
|--------------------------------------------------------------------------
| Application Routes For Relocation Pet move
|--------------------------------------------------------------------------
|Here we can call all methods for Buyer/seller post/Get actions
*/
//@Shriram: Relocation pet move ajax breed types 
Route::post('relocationpet/ajxbreedtypes', [
	'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
	'uses' => 'RelocationBuyerController@ajxBreedTypes',
	'roles' => [BUYER,SELLER] // Only a buyer can access this route
]);

Route::post('chekckbuyerpost', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'RelocationBuyerController@chekckbuyerofficepost',
		'roles' => [BUYER,SELLER] // Only a buyer can access this route
]);
Route::get('chekckbuyerpost', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'RelocationBuyerController@chekckbuyerofficepost',
		'roles' => [BUYER,SELLER] // Only a buyer can access this route
]);

Route::post('chekcksellerofficepost', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'RelocationSellerController@chekcksellerofficepost',
		'roles' => [BUYER,SELLER] // Only a buyer can access this route 
]);
Route::get('chekcksellerofficepost', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'RelocationSellerController@chekcksellerofficepost',
		'roles' => [BUYER,SELLER] // Only a buyer can access this route
]);


Route::get('getuserdetbooknow', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'SellerController@getUserDetBooknow',
		'roles' => [BUYER,SELLER] // Only a buyer can access this route
]);

Route::post('getuserdetbooknow', [
		'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
		'uses' => 'SellerController@getUserDetBooknow',
		'roles' => [BUYER,SELLER] // Only a buyer can access this route
]);

//@Shriram: Truck lease items
Route::post('trucklease/ajxleaseterms', [
	'middleware' => ['auth', 'roles'], // A 'roles' middleware must be specified
	'uses' => 'TruckLeaseSellerController@ajx_leaseterms',
	'roles' => [BUYER,SELLER] // Only a buyer can access this route
]);

Route::get('/disclaimer', 'RegisterController@termsAndConditions');
Route::get('/privacypolicy', 'RegisterController@privacyPolicy');

Route::resource('getServiceTypeMeasurementUnit', 'BuyerController@getServiceTypeMeasurementUnit');
Route::post('checkuniquechasis', 'VehicleController@checkUniqueChasis');
Route::post('checkuniqueengine', 'VehicleController@checkUniqueEngine');
Route::post('getpackages', 'BuyerController@getPackages');
