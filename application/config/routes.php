<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'home/index';
$route['404_override'] = '';
$route['translate_uri_dashes'] = TRUE;

$route['admin']				= 'auth/login';

$route['terms-and-conditions']	= 'home/terms_and_conditions';
$route['privacy-policy']	= 'home/privacy_policy';
$route['refund-policy']	= 'home/refund_policy';
$route['cookies-policy']	= 'home/cookies_policy';
$route['disclaimer']			= 'home/disclaimer';


$route['books']			 		= 'home/all_books';
$route['books/(:any)']	 		= 'home/all_books/$1';

$route['buy-books']			 	= 'home/buy_books';
$route['buy-books/(:any)']	 	= 'home/buy_books/$1';

$route['books-api']			 	= 'home/api';
$route['books-api/(:any)']	 	= 'home/api/$1';

$route['buy-book']			 	= 'home/buy_book';
$route['buy-book/(:any)']             = 'home/buy_book/$1';
$route['buy-book/(:any)/(:any)']             = 'home/buy_book/$1/$2';

$route['checkout']			 		= 'home/checkout';
$route['checkout/(:any)']	 		= 'home/checkout/$1';
$route['checkout/(:any)/(:any)']	= 'home/checkout/$1/$2';




$route['search-seller']		 			= 'home/search_seller';
$route['search-seller/(:any)'] 			= 'home/search_seller/$1';
$route['search-seller/(:any)/(:any)']	= 'home/search_seller/$1/$2';
$route['search-seller/(:any)/(:any)/(:any)']	= 'home/search_seller/$1/$2/$3';

$route['search-institute']		 			= 'home/search_institute';
$route['search-institute/(:any)'] 			= 'home/search_institute/$1';
$route['search-institute/(:any)/(:any)']	= 'home/search_institute/$1/$2';
$route['search-institute/(:any)/(:any)/(:any)']	= 'home/search_institute/$1/$2/$3';

$route['search-buyer-leads']				 = 'home/search_buyer_leads';
$route['search-buyer-leads/(:any)']		 = 'home/search_buyer_leads/$1';
$route['search-buyer-leads/(:any)/(:any)'] = 'home/search_buyer_leads/$1/$2';
$route['search-buyer-leads/(:any)/(:any)/(:any)']	= 'home/search_buyer_leads/$1/$2/$3';


$route['seller-profile']				= 'home/seller_profile';
$route['seller-profile/(:any)']		= 'home/seller_profile/$1';

$route['institute-profile']				= 'home/institute_profile';
$route['institute-profile/(:any)']		= 'home/institute_profile/$1';


$route['book-seller']				= 'buyer/book_seller';
$route['book-seller/(:any)']			= 'buyer/book_seller/$1';

$route['enroll-in-institute']				= 'buyer/enroll_in_institute';
$route['enroll-in-institute/(:any)']		= 'buyer/enroll_in_institute/$1';


//Buyer - Enquiries made over Sellers
$route['enquiries']					= 'buyer/enquiries';
$route['enquiries/(:any)']			= 'buyer/enquiries/$1';
$route['enquiries/(:any)/(:any)']	= 'buyer/enquiries/$1/$2';
$route['enquiries/(:any)/(:any)/(:any)']	= 'buyer/enquiries/$1/$2/$3';

$route['rate-seller']				= 'buyer/rate_seller';
$route['rate-seller/(:any)']			= 'buyer/rate_seller/$1';

$route['user-reviews']				= 'seller/user_reviews';
$route['user-reviews/(:any)/(:any)']= 'seller/user_reviews/$1/$2';



$route['send-credits-conversion-request']	= 'seller/send_credits_conversion_request';
$route['send-credits-conversion-request/(:any)']= 'seller/send_credits_conversion_request/$1';

$route['credit-conversion-requests']= 'seller/credit_conversion_requests';
$route['credit-conversion-requests/(:any)']= 'seller/credit_conversion_requests/$1';
$route['credit-conversion-requests/(:any)/(:any)']= 'seller/credit_conversion_requests/$1/$2';
$route['credit-conversion-requests/(:any)/(:any)/(:any)']= 'seller/credit_conversion_requests/$1/$2/$3';

//Seller - View Buyer's Enquiries
$route['buyer-enquiries']			= 'seller/buyer_enquiries';
$route['buyer-enquiries/(:any)']	= 'seller/buyer_enquiries/$1';
$route['buyer-enquiries/(:any)/(:any)']	= 'seller/buyer_enquiries/$1/$2';
$route['buyer-enquiries/(:any)/(:any)/(:any)']	= 'seller/buyer_enquiries/$1/$2/$3';

$route['my-batches']				= 'seller/my_batches';
$route['my-batches/(:any)']			= 'seller/my_batches/$1';
$route['my-batches/(:any)/(:any)']	= 'seller/my_batches/$1/$2';
$route['my-batches/(:any)/(:any)/(:any)']	= 'seller/my_batches/$1/$2/$3';

$route['approve-batch-buyers']		= 'institute/approve_batch_buyers';
$route['approve-batch-buyers/(:any)']	= 'institute/approve_batch_buyers/$1';
$route['approve-batch-buyers/(:any)/(:any)']	= 'institute/approve_batch_buyers/$1/$2';

$route['initiate-batch-session']		= 'seller/initiate_batch_session';
$route['initiate-batch-session/(:any)']	= 'seller/initiate_batch_session/$1';
$route['initiate-batch-session/(:any)/(:any)']	= 'seller/initiate_batch_session/$1/$2';

$route['complete-batch-session']		= 'seller/complete_batch_session';
$route['complete-batch-session/(:any)']	= 'seller/complete_batch_session/$1';
$route['complete-batch-session/(:any)/(:any)']	= 'seller/complete_batch_session/$1/$2';


$route['send-message']				= 'home/send_message';



/*
| -------------------------------------------------------------------------
| Sample REST API Routes
| -------------------------------------------------------------------------
*/
//$route['example/users/(:num)'] = 'example/users/id/$1'; // Example 4
//$route['example/users/(:num)(\.)([a-zA-Z0-9_-]+)(.*)'] = 'example/users/id/$1/format/$3$4'; // Example 8
