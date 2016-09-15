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

Route::any('memberRegistration','RegistrationController@index');
Route::any('Registration/store','RegistrationController@store');
Route::any('individualRegistration','RegistrationController@indvReg');
Route::any('individualRegistration/store','RegistrationController@storeIndvReg');
Route::any('logistiks/Buyersearch','LogistiksController@buyerSearch');

Route::any('/buyer/search' ,'BuyerSearchController@index');  
Route::any('/buyer/srchPost' ,'BuyerSearchController@buyerSearch');  
Route::any('/buyer/bookNow/{seller_user_id}/{post_id}' ,'BuyerSearchController@bookNow');  
Route::any('/buyer/Cart/{buyer_user_id}/{seller_user_id}/{post_id}' ,'BuyerSearchController@Cart');  
Route::any('/buyer/buyerGsa/{buyer_user_id}/{seller_user_id}' ,'BuyerSearchController@buyerGsa');  
Route::any('/buyer/buyerConfirmation/{buyer_user_id}' ,'BuyerSearchController@buyerConfirmation');  
Route::any('/buyer/buyerBilling' ,'BuyerSearchController@buyerBilling');  


//Route::get('ftlSearchList', 'BuyerSearchController@ftlSearchList');
Route::any('buyerFtlFilter', 'BuyerSearchController@buyerFtlFilter');
Route::any('getUserDeatils', 'BuyerSearchController@getUserDeatils');
Route::any('newMail', 'BuyerSearchController@newMail');
Route::get('test', 'BuyerSearchController@test');

Route::any('/form','SellerPostController@index');
Route::any('/check','SellerPostController@check');



