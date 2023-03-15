<?php
defined('BASEPATH') OR exit('No direct script access allowed');


define('URL_AUTH_CHANGE_APPROVEMENT_STATUS', SITEURL2."/auth/change_approvement_status");

/*
Home Module
//URLS
*/

define('URL_HOME_ABOUT_US', SITEURL2."/about-us");
define('URL_HOME_FAQS', SITEURL2."/faqs");
define('URL_HOME_CONTACT_US', SITEURL2."/contact-us");
define('URL_HOME_ALL_BOOKS', SITEURL2."/books");
define('URL_HOME_LOAD_MORE_BOOKS', SITEURL2."/home/load_more_books");
define('URL_HOME_SEARCH_SELLER', SITEURL2."/search-seller");
define('URL_HOME_LOAD_MORE_SELLERS', SITEURL2."/home/load_more_sellers");
define('URL_HOME_SEARCH_INSTITUTE', SITEURL2."/search-institute");
define('URL_HOME_LOAD_MORE_INSTITUTES', SITEURL2."/home/load_more_institutes");
define('URL_HOME_SEARCH_BUYER_LEADS', SITEURL2."/search-buyer-leads");
define('URL_HOME_LOAD_MORE_BUYER_LEADS', SITEURL2."/home/load_more_buyer_leads");
define('URL_HOME_SELLER_PROFILE', SITEURL2."/seller-profile");
define('URL_HOME_AJAX_GET_SELLER_BOOK_DETAILS', SITEURL2."/home/ajax_get_seller_book_details");
define('URL_HOME_INSTITUTE_PROFILE', SITEURL2."/institute-profile");
define('URL_HOME_SEND_MESSAGE', SITEURL2."/send-message");

define('URL_HOME_BUY_BOOKS', SITEURL2."/buy-books");
define('URL_HOME_BUY_BOOK', SITEURL2."/buy-book");
define('URL_HOME_LOAD_MORE_SELLING_BOOKS', SITEURL2."/home/load_more_selling_books");
define('URL_HOME_CHECKOUT', SITEURL2."/checkout");

define('URL_HOME_GET_FREE', SITEURL2."/home/get_free");


define('URL_PAY', SITEURL2."/pay");



define('URL_BLOG_INDEX', SITEURL2."/blog/index");
define('URL_BLOG_SINGLE', SITEURL2."/blog/single");


//Constants
define('OUR_POPULAR_BOOKS', 'Our Popular <span>Books</span>');
define('CHECK_ALL_BOOKS', 'Check <strong>All Books</strong>');
define('LIMIT_BOOK_LIST', 16);
define('LIMIT_PROFILES_LIST', 4);
define('MAX_DISPLAY_CATS_MENU', 6);


//Tables
define('TBL_SELLER_BLOGS', TBL_PREFIX.'seller_blogs');
define('TBL_CATEGORIES', TBL_PREFIX.'categories');
define('TBL_BOOK_CATEGORIES', TBL_PREFIX.'book_categories');
define('TBL_SELLER_BOOKS', TBL_PREFIX.'seller_books');
define('TBL_LOCATIONS', TBL_PREFIX.'locations');
define('TBL_SELLER_LOCATIONS', TBL_PREFIX.'seller_locations');
define('TBL_TEACHING_TYPES', TBL_PREFIX.'teaching_types');
define('TBL_SELLER_TEACHING_TYPES', TBL_PREFIX.'seller_teaching_types');
define('TBL_BUYER_LEADS', TBL_PREFIX.'buyer_leads');
define('TBL_USER_CREDIT_TRANSACTIONS', TBL_PREFIX.'user_credit_transactions');
define('TBL_BOOKINGS', TBL_PREFIX.'bookings');
define('TBL_BOOKING_QUESTIONS', TBL_PREFIX.'booking_questions');
define('TBL_BOOKING_ANSWERS', TBL_PREFIX.'booking_answers');
define('TBL_SUBSCRIPTIONS1', TBL_PREFIX.'subscriptions');
define('TBL_INST_OFFERED_BOOKS', TBL_PREFIX.'inst_offered_books');
define('TBL_INST_LOCATIONS', TBL_PREFIX.'inst_locations');
define('TBL_INST_TEACHING_TYPES', TBL_PREFIX.'inst_teaching_types');
define('TBL_INST_ENROLLED_BUYERS', TBL_PREFIX.'inst_enrolled_buyers');
define('TBL_BLOGS', TBL_PREFIX.'seller_blogs');
define('TBL_CHAT', TBL_PREFIX.'chat');

define('TBL_POINTSYSTEM', TBL_PREFIX.'user_credit_transactions');



//Paths
define('URL_UPLOADS_CATEGORIES_PHYSICAL', RESOURCES . DS . 'uploads' . DS .'categories' . DS);
define('URL_UPLOADS_BOOKS_PHYSICAL', RESOURCES . DS . 'uploads' . DS .'books' . DS);

define('URL_UPLOADS_CATEGORIES', URL_PUBLIC_UPLOADS2.'categories' . DS);
define('URL_UPLOADS_BOOKS', URL_PUBLIC_UPLOADS2.'books' . DS);
define('URL_UPLOADS_BOOKS_DEFAULT', URL_PUBLIC_UPLOADS2);


define('URL_UPLOADS_BOOKS_DEFAULT_IMAGE', URL_UPLOADS_BOOKS_DEFAULT.'default-img.jpg');


define('URL_UPLOADS_PROFILES_PHYSICAL', RESOURCES . DS . 'uploads' . DS .'profiles' . DS);
define('URL_UPLOADS_PROFILES', URL_PUBLIC_UPLOADS2.'profiles' . DS);

define('URL_UPLOADS_GALLERY_PHYSICAL', RESOURCES . DS . 'uploads' . DS .'gallery' . DS);
define('URL_UPLOADS_GALLERY', URL_PUBLIC_UPLOADS2.'gallery' . DS);

define('URL_UPLOADS_PROFILES_SELLER_MALE_DEFAULT_IMAGE', URL_UPLOADS_PROFILES.'default-seller-male.jpg');
define('URL_UPLOADS_PROFILES_SELLER_FEMALE_DEFAULT_IMAGE', URL_UPLOADS_PROFILES.'default-seller-female.jpg');
define('URL_UPLOADS_PROFILES_INSTITUTE_DEFAULT_IMAGE', URL_UPLOADS_PROFILES.'default-institute.jpg');

define('URL_UPLOADS_PROFILES_BUYER_MALE_DEFAULT_IMAGE', URL_UPLOADS_PROFILES.'default-buyer-male.png');
define('URL_UPLOADS_PROFILES_BUYER_FEMALE_DEFAULT_IMAGE', URL_UPLOADS_PROFILES.'default-buyer-female.png');





/*
Buyer Module
//URLS
*/
define('URL_BUYER_PROFILE', SITEURL2."/buyer/index");
define('URL_BUYER_BOOK_SELLER', SITEURL2."/book-seller");
define('URL_BUYER_POST_REQUIREMENT', SITEURL2."/buyer/post-requirement");
define('URL_BUYER_ENQUIRIES', SITEURL2."/enquiries");
define('URL_BUYER_ENROLL_IN_INSTITUTE', SITEURL2."/enroll-in-institute");
define('URL_BUYER_RATE_SELLER', SITEURL2."/rate-seller");
define('URL_BUYER_BOOK_PURCHASES', SITEURL2."/buyer/book-purchases");
define('URL_BUYER_DOWNLOAD_BOOK', SITEURL2."/buyer/download-book");
define('URL_BUYER_BOOK_DOWNLOAD_HISTORY', SITEURL2."/buyer/book-download-history");
define('URL_BUYER_BOOK_DOWNLOAD_EXAM_QUIZ_RESULT_CERTIFICATE', SITEURL2."/buyer/book-download-exam-quiz-result-certificate");






/*
Seller Module
//URLS
*/
define('URL_SELLER_PROFILE', SITEURL2."/seller/index");
define('URL_SELLER_BLOGS', SITEURL2."/seller/blogs");
define('URL_SELLER_MANAGE_BOOKS', SITEURL2."/seller/manage-books");
define('URL_SELLER_USER_REVIEWS', SITEURL2."/user-reviews");
define('URL_SELLER_BUYER_ENQUIRIES', SITEURL2."/buyer-enquiries");
define('URL_SELLER_MY_BATCHES', SITEURL2."/my-batches");
define('URL_SELLER_SEND_CREDITS_CONVERSION_REQUEST', SITEURL2."/send-credits-conversion-request");
define('URL_SELLER_CREDIT_CONVERSION_REQUESTS', SITEURL2."/credit-conversion-requests");
define('URL_SELLER_INITIATE_BATCH_SESSION', SITEURL2."/initiate-batch-session");
define('URL_SELLER_COMPLETE_BATCH_SESSION', SITEURL2."/complete-batch-session");
define('URL_SELLER_PURCHASED_BOOKS', SITEURL2."/seller/purchased-books");





/*
Institute Module
//URLS
*/

define('URL_INSTITUTE_APPROVE_BATCH_BUYERS', SITEURL2."/approve-batch-buyers");
define('URL_INSTITUTE_SEND_CREDITS_CONVERSION_REQUEST', SITEURL2."/institute/send-credits-conversion-request");




define('URL_ADMIN_VIEW_PURCHASED_BOOKS', SITEURL2."/admin/view-purchased-books");
define('URL_ADMIN_VIEW_BOOK_DOWNLOAD_HISTORY', SITEURL2."/admin/view-book-download-history");
define('URL_SECTIONS_INDEX', SITEURL2."/sections/index");


define('URL_ADMIN_VIEW_SELLERS_BLOGS', SITEURL2."/admin/view-sellers-blogs");


define("VIDEO_FORMATS", serialize (array ("mp4", "3gp", "webm", "wmv", "flv", "avi", "ogg")));
define("AUDIO_FORMATS", serialize (array ("mp2", "mp3", "aac", "wav")));
define("IMAGE_FORMATS", serialize (array ("jpg", "jpeg", "png", "gif", "svg", "bmp")));
define("OTHER_FILE_FORMATS", serialize (array ("pdf", "ppt", "pptx", "doc", "docx", "rtf", "rtx", "txt", "text")));

//AWS S3 Credentials
define("S3_SECRET_KEY", "AKIARBG2V74LHONZXJLD");
define("S3_ACCESSS_KEY", "MiYbNEe3dSwVvp/kyrgBlemhO1iJVaqKx7wTsu+t");
define("S3_VERSION", "latest");
define("S3_REGION", "eu-west-2");
define("S3_SIGNATUREVERSION", "s3v4");
define("S3_BUCKET_NAME", "ema-itbsh");

define('S3_BOOKS_FILE_UPLOAD_PATH', 'books.itbsh.com/assets/uploads/book_curriculum_files/');


/*****************************
05-12-2018
******************************/
define('URL_HOME_LIST_BLOGS', SITEURL2."/blogs");
define('URL_HOME_LOAD_MORE_BLOGS', SITEURL2."/home/load_more_blogs");
define('URL_HOME_VIEW_BLOG_DETAILS', SITEURL2."/view-blog");
define('LIMIT_BLOG_LIST',4);
/**
 * check - If not exist define constant Virtual Class
*/
define('URL_VIRTUAL_CLASS', SITEURL2."/virtual-class");
define('URL_SELLER_ISSUE_CERTIFICATE', SITEURL2."/seller/issue-certificate");
define('URL_BUYER_GET_CERTIFICATE', SITEURL2."/buyer/get-certificate");


define('URL_SELLER_LOCATIONS', SITEURL2."/seller/locations");
define('URL_SELLER_BOOKS', SITEURL2."/seller/books");

define('APPLICATION_VERSION',2);
