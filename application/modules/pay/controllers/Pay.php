<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Pay extends MY_Controller
{
	function __construct()
	{
		parent::__construct();

		$this->load->library('session');
		$this->load->model(array('base_model', 'home_model'));
		$this->load->library(array('ion_auth', 'form_validation'));

		$group = array('admin', 'seller', 'buyer', 'institute');

		if (!$this->ion_auth->in_group($group)) {
			$this->prepare_flashmessage(get_languageword('MSG_NO_ENTRY'), 2);
		}
	}

	//gets the discounted price for buyer
	function get_discounted_price($record, $user_id = '')
	{
		$actual_price = $record->actual_price;
		$discount_price = $record->book_price;
		if ($this->ion_auth->logged_in() && $this->ion_auth->is_buyer() && $actual_price > 0 && !empty($user_id)) {
			$userDetails = getUserRec($user_id);
			$discount_price = getBuyerDiscountedPrice($record->sc_id, $userDetails->id);
		}
		return $discount_price;
	}



	/** Displays the Index Page**/
	function index()
	{

		$sc_id 		= $this->input->post('sc_id');
		//$gateway_id = 28;
		$gateway_id = $this->input->post('gateway_id');

		if (!($sc_id > 0)) {

			$this->prepare_flashmessage(get_languageword('Invalid_Request'), 1);
			redirect(URL_HOME_BUY_BOOKS);
		}

		$selling_book_slug = $this->base_model->fetch_value('seller_selling_books', 'slug', array('sc_id' => $sc_id));


		if (!($gateway_id > 0)) {
			// $gateway_id = 28;
			$this->prepare_flashmessage(get_languageword('Please_select_payment_gateway'), 2);
			redirect(URL_HOME_CHECKOUT . '/' . $selling_book_slug);
		}


		if (!$this->ion_auth->logged_in()) {

			$this->session->set_userdata('req_from', 'buy_book');
			$this->session->set_userdata('selling_book_slug', $selling_book_slug);
			$this->prepare_flashmessage(get_languageword('please_login_to_continue'), 2);
			redirect(URL_AUTH_LOGIN);
		}



		$user_id = $this->ion_auth->get_user_id();


		$record = get_seller_sellingbook_info($sc_id);

		if (empty($record)) {
			$this->prepare_flashmessage(get_languageword('No Details Found'), 2);
			redirect(URL_HOME_BUY_BOOKS);
		}

		 $gateway_details = $this->base_model->get_payment_gateways(' AND st2.type_id = ' . $gateway_id);


		//echo '<pre> $gateway_details :: '; print_r($gateway_details); die;

		// if (empty($gateway_details)) {

		// 	$this->prepare_flashmessage(get_languageword('Payment Gateway Details Not Found'), 2);
		// 	redirect(URL_HOME_CHECKOUT . '/' . $selling_book_slug);
		// }

		$admin_commission = 0;
		$seller_paypal_id = '';
		$seller = $this->base_model->get_user_details($record->seller_id);

		if (!empty($seller) && isset($seller[0])) {
			$seller_paypal_id = $seller[0]->paypal_email;
			$admin_commission = $seller[0]->admin_commission;
		}

		if (empty($admin_commission)) {
			$admin_commission = $this->config->item('site_settings')->admin_commission_on_book_purchase;
		}

		$seller_point_value_config_int = $this->config->item('site_settings')->seller_point_value;
		$seller_credit_points_sum = get_user_credit_sum() * $seller_point_value_config_int;

		$total_amount 					= $this->get_discounted_price($record, $user_id);


		$admin_commission_percentage 	= $admin_commission;
		$admin_commission_val 			= number_format(($total_amount * ($admin_commission_percentage / 100)), 3);




		$plantTreeAmt = ($this->config->item('site_settings')->enable_moretrees_api == 'YES') ? 1.00 : 0.00;

		$input_data['sc_id']    	                = $sc_id;
		$input_data['seller_id']    	            = $record->seller_id;
		$input_data['user_id']                      = $user_id;
		$input_data['total_amount']                 = $total_amount + $plantTreeAmt;

		$input_data['item_price']                   = $total_amount - $plantTreeAmt;
		$input_data['admin_commission_percentage']  = $admin_commission_percentage;
		$input_data['admin_commission_val']     	= $admin_commission_val;
		$input_data['seller_payment_details']       = $seller_paypal_id;
		$input_data['api_val']                      = $plantTreeAmt;
		$input_data['max_downloads']                = $record->max_downloads;

		$input_data['payment_gateway_id']           = $gateway_id;
		$input_data['paid_date']     		        = date('Y-m-d H:i:s');
		$input_data['last_modified']     	        = date('Y-m-d H:i:s');


		$book_title = $record->book_title;

		$this->session->set_userdata('is_valid_request', 1);
		$this->session->set_userdata(array('book_purchase_data'=>$input_data));
		$this->session->set_userdata('selling_book_slug', $selling_book_slug);
		$this->session->set_userdata(array('selling_book_det'=> $record));
		$this->session->set_userdata(array('gateway_details'=>$gateway_details));
 
		/*$_SESSION['userdata']['is_valid_request'] = 1;
		$_SESSION['userdata']['book_purchase_data'] = $input_data;
		$_SESSION['userdata']['selling_book_slug'] = $selling_book_slug;
		$_SESSION['userdata']['selling_book_det'] = $record;
		$_SESSION['userdata']['gateway_details'] = $gateway_details;*/

		$field_values = $this->db->get_where('system_settings_fields', array('type_id' => $gateway_id))->result();

		$user_details = $this->base_model->fetch_records_from('users', array('id' => $user_id));

		$buyer_details = getUserRec($user_id);
		
		$this->session->set_userdata(array('buyer_details'=>$buyer_details));

		/*$curent_session_data = (array) $this->session->userdata();

		$session_data['user_id'] = $user_id;
		$session_data['data'] = json_encode(array('session_data' => $curent_session_data), JSON_PRETTY_PRINT);

		$get_user_current_session = $this->base_model->fetch_records_from('ci_sessions', array('user_id' => $user_id));

		if(!empty($get_user_current_session)) {
			$whr['user_id'] = $user_id;
			$this->base_model->update_operation($session_data, 'ci_sessions', $whr);
		} else {
			$this->base_model->insert_operation($session_data, 'ci_sessions');
		}*/

		 if ($gateway_details[0]->type_id == PAYPAL_PAYMENT_GATEWAY) //Paypal Settings
		 {
			 	
		 //	Paypal Payment
		 	$config['return'] 				= base_url() . 'pay/payment_success';
		 	$config['cancel_return'] 		= base_url() . 'pay/payment_cancel';
		 	$config['production'] 			= true;
		 	$config['currency_code'] 		= 'USD';

		 	foreach ($field_values as $value) {

		 		if ($value->field_key == 'Paypal_Email') {
		 			$config['business'] = $value->field_output_value;
		 		}
		 		if ($value->field_key == 'Account_Type' && $value->field_output_value == 'sandbox') {
		 			$config['production'] = false;
		 		}
		 		if ($value->field_key == 'Currency_Code') {
		 			$config['currency_code'] = $value->field_output_value;
		 		}
		 		if ($value->field_key == 'Header_Logo' && $value->field_output_value != '' && file_exists(URL_PUBLIC_UPLOADS . 'settings/thumbs/' . $value->field_output_value)) {
		 			$config['cpp_header_image'] = URL_PUBLIC_UPLOADS2 . 'settings/thumbs/' . $value->field_output_value;
		 		}
		 	}

			//echo '<pre> $config :: '; print_r($config); die;
			
		 	$this->load->library('paypal', $config);
		 	$this->paypal->__initialize($config);

	 			if (get_user_credit_sum() > 0 && $total_amount > get_user_credit_sum()) {
					$total_amount = ($total_amount - get_user_credit_sum());
				}


		 	$this->paypal->add($book_title, $total_amount + $plantTreeAmt);
		 	$this->paypal->pay(); /*Process the payment*/
		
		} elseif ( $gateway_details[0]->type_id == TWOCHECKOUT_PAYMENT_GATEWAY ){

			$this->load->helper('2check-payment');
			$config = array();
			$url = 'https://www.2checkout.com/checkout/purchase';

			foreach($field_values as $value) {

				if($value->field_key == '2check_is_demo') {
					if ( strip_tags( trim( $value->field_output_value ) ) == 'yes' ) {
						$url = 'https://sandbox.2checkout.com/checkout/purchase';
						$config['demo'] =	'Y';
					}
				}
				if($value->field_key == '2check_seller_id') {
						$config['sid'] = $value->field_output_value;
				}
			}

			$config['mode'] = '2CO';
			$config['currency_code'] 	= get_system_settings('Currency_Code');
			$config['li_0_type'] 		= 'product'; // Always Lower Case, ‘product’, ‘shipping’, ‘tax’ or ‘coupon’, defaults to ‘product’
			$config['li_0_name'] 		= $book_title;
			$config['li_0_price'] 		= $total_amount;
			$config['li_0_quantity'] 	= 1;
			$config['li_0_tangible'] 	= 'N'; // If( is_virtual || is_downloadable ) Then it is 'Y'

			if(!empty($user_details))
			{
				$config['first_name'] 		= $user_details[0]->username;
				// $config['last_name'] 		= $user_details[0]->last_name;
				// $config['street_address'] 	= $user_details[0]->land_mark;
				// $config['street_address2'] 	= $user_details[0]->land_mark;
				// $config['city'] 			= $user_details[0]->city;
				// $config['state'] 			= '';
				// $config['zip'] 				= $user_details[0]->pin_code;
				// $config['country'] 			= $user_details[0]->country;
				$config['email'] 			= $user_details[0]->email;
				// $config['phone'] 			= $user_details[0]->phone;
			}


			// echo '<pre> $config :: '; print_r($config); die;

			$config['return_url']			= base_url() . 'pay/payment_success';
			$config['x_receipt_link_url'] 	= base_url() . 'pay/payment_success';
			twocheck_redirect( $url, $config );

		} elseif ( $gateway_details[0]->type_id == STRIPE_PAYMENT_GATEWAY ){


			if(!($sc_id > 0)) {
				$this->prepare_flashmessage(get_languageword('Invalid_Request'), 1);
				redirect(URL_HOME_BUY_BOOKS);
			}
			$selling_book_slug = $this->base_model->fetch_value('seller_selling_books', 'slug', array('sc_id' => $sc_id));


			$final_cost = $_POST['final_cost'];
			$name 		= $_POST['name'];
			$content = '';
			$gateway_id = STRIPE_PAYMENT_GATEWAY;

			if(!($gateway_id > 0)) {

				$this->prepare_flashmessage(get_languageword('Please_select_payment_gateway'), 2);
				redirect(URL_HOME_CHECKOUT.'/'.$selling_book_slug);
			}

			if(!$this->ion_auth->logged_in()) {

				$this->session->set_userdata('req_from', 'buy_book');
				$this->session->set_userdata('selling_book_slug', $selling_book_slug);
				$this->prepare_flashmessage(get_languageword('please_login_to_continue'), 2);
				redirect(URL_AUTH_LOGIN);
			}

			$user_id=$this->ion_auth->get_user_id();
			$record = get_seller_sellingbook_info($sc_id);
			if(empty($record)) {

				$this->prepare_flashmessage(get_languageword('No Details Found'), 2);
				redirect(URL_HOME_BUY_BOOKS);
			}

			$gateway_details=$this->base_model->get_payment_gateways(' AND st2.type_id = '.$gateway_id);
			if(empty($gateway_details)) {
				$this->prepare_flashmessage(get_languageword('Payment Gateway Details Not Found'), 2);
				redirect(URL_HOME_CHECKOUT.'/'.$selling_book_slug);
			}

			$total_amount 					= $this->get_discounted_price($record, $user_id);
			$admin_commission_percentage 	= $record->admin_commission_percentage;
			$admin_commission_val 			= number_format(($total_amount * ($admin_commission_percentage / 100)), 0);


			$input_data['sc_id']    	    = $sc_id;
			$input_data['seller_id']    	    = $record->seller_id;
			$input_data['user_id']          = $user_id;
			$input_data['total_amount']     = $total_amount;
			$input_data['item_price']     = $total_amount;
			$input_data['admin_commission_percentage']  = $admin_commission_percentage;
			$input_data['admin_commission_val']     	= $admin_commission_val;

			$input_data['max_downloads']    = $record->max_downloads;

	        $input_data['payment_gateway_id']   = $gateway_id;
	        $input_data['paid_date']     		= date('Y-m-d H:i:s');
	        $input_data['last_modified']     	= date('Y-m-d H:i:s');


	        $book_title = $record->book_title;

	        $this->session->set_userdata('is_valid_request', 1);
			$this->session->set_userdata('book_purchase_data', $input_data);
			$this->session->set_userdata('selling_book_slug', $selling_book_slug);
			$this->session->set_userdata('selling_book_det', $record);
			$this->session->set_userdata('gateway_details', $gateway_details);

			if ( $gateway_id == 41 ) { // Stripe
				$user_info = $this->base_model->get_user_details($user_id);

				$field_values = $this->db->get_where('system_settings_fields',array('type_id' => $gateway_id))->result();


				$key = 'pk_test_H8R3tFH4RiyF0VGzTcXwl8NF';
				$stripe_test_mode = FALSE;
				foreach($field_values as $value) {
					if($value->field_key == 'stripe_test_mode') {
						if ( strip_tags( trim( $value->field_output_value ) ) == 'yes' ) {
							$stripe_test_mode = TRUE;
						}
					}
				}
				foreach($field_values as $value) {
					if( $value->field_key == 'stripe_key_test_publishable' && $stripe_test_mode == TRUE ) {
						$key = $value->field_output_value;
					} elseif ( $value->field_key == 'stripe_key_live_publishable' && $stripe_test_mode == FALSE ) {
						$key = $value->field_output_value;
					}
				}
				//ob_start();
				?>
				<form action="<?php echo site_url('pay/process_stripe/'.$sc_id);?>" method="POST" id="frm_stripe">
				  <script
					src="https://checkout.stripe.com/v2/checkout.js" class="stripe-button" id="stripe_script"
					data-key="<?php echo $key;?>"
					data-amount="<?php echo $final_cost;?>"
					data-name="<?php echo $name;?>"
					data-description="<?php echo $name;?>"
					data-image="<?php  if(isset($this->config->item('site_settings')->logo) && $this->config->item('site_settings')->logo != '') echo substr(URL_PUBLIC_UPLOADS_SETTINGS.''.$this->config->item('site_settings')->logo, 5); else echo substr(URL_FRONT_IMAGES.'Logo.png', 5);?>"
					data-package_id="<?php echo $sc_id;?>"
					data-currency="<?php echo get_system_settings('Currency_Code');?>"
					data-email="<?php echo $user_info[0]->email;?>"
					data-locale="auto"
					>
					$(document).ready(function() {
						alert('Its ready');
					});
				  </script>
				</form>
				<script>
					$(document).ready(function() {
						alert('Its ready');
					});
				</script>
				<?php
				//$content = ob_get_clean();
			}
			//echo $content;

		} else{ // if($gateway_details[0]->type_id == 52){ // wallet

			// echo 'total_amount = '. $total_amount;
			// echo 'get_user_credit_sum = '. get_user_credit_sum($user_id);
			// die();

			$sum_points = get_user_credit_sum($user_id);
			$buyer_point_value = $this->config->item('site_settings')->buyer_point_value;

			$sum_points_total = $sum_points;
			$sum_points_total = $sum_points / $buyer_point_value;

			if (get_user_credit_sum($user_id) > 0 && $sum_points_total > $total_amount) {
				// echo "working if ";

			} else {

				$this->prepare_flashmessage("Checkout failed! You have low balance in your wallet, buy using payment gateway or simply earn credit by doing like or review item(s)", 1);
				redirect(URL_HOME_CHECKOUT.'/'.$selling_book_slug);
			}
			
			echo '<p>Please wait..</p>';
			
			echo '<form method="post" id="post_input_data" action="' . base_url() . 'pay/payment_success">';
			
			foreach($input_data as $input_data_key => $input_data_item){
				echo '<input type="hidden" name="book_purchase_data['.$input_data_key.']" value="' . $input_data_item . '" />';
			}

			foreach($record as $record_key => $record_item){
				echo '<input type="hidden" name="record['.$record_key.']" value="' . $record_item . '" />';
			}
			
			foreach($record as $record_item_key => $record_item){
				echo '<input type="hidden" name="selling_book_det['.$record_item_key.']" value="' . $record_item . '" />';
			}

			$buyer_details = getUserRec($user_id);

			foreach($buyer_details as $buyer_detail_key => $buyer_detail_item){
				echo '<input type="hidden" name="buyer_details['.$buyer_detail_key.']" value="' . $buyer_detail_item . '" />';
			}
			
			echo '<input type="hidden" name="gateway_details" value="wallet" />';

			echo '<input type="hidden" name="paid_amount" value="'.$total_amount.'" />';
			
			echo '</form>';

			echo '<script> setTimeout(function(){ document.getElementById("post_input_data").submit();	}, 1000);  </script>';
		
		/*  } else {

		 	$this->prepare_flashmessage("Please contact us for fully implementation of this Payment Gateway", 2);
		 	redirect(URL_HOME_CHECKOUT . '/' . $selling_book_slug);
			
		 */ }
	}

	function payment_success()
	{
		//$query = "select * from pre_ci_sessions ORDER BY id DESC LIMIT 1";

		//$session_data = $this->base_model->get_query_result($query);

		//echo '<pre> $json_encode :: '; print_r(json_decode($session_data[0]->data));

		//$json_decode_session_data = json_decode($session_data[0]->data);

		//$this->session->set_userdata('userdata', (object) $json_decode_session_data->session_data);

		//echo '<pre> $this->session :: '; print_r($this->session); die;

		//echo '<pre> $this->session :: '; print_r($this->session);
		//echo '<pre> $_REQUEST :: '; print_r($_REQUEST);

		$success = 0;

		// echo "<pre>";print_r($this->input->post());
		// die; 

		//if (ini_get('session.auto_start') == 0) session_start();

		//if($this->session->userdata('book_purchase_data') && $this->session->userdata('is_valid_request')) {
		
		// $postdata = $_REQUEST;
		
		// echo "<pre>";print_r($postdata);
		// die();

		if (isset($_REQUEST["book_purchase_data"])) {

			$input_data = $_REQUEST["book_purchase_data"];
			// $input_data['paid_date']   = $this->input->post('txn_id');
			$input_data['transaction_id'] = rand(1000,1000000000);
			$buyer_details = $_REQUEST["buyer_details"];

			if ($buyer_details->first_name == '') {
            	$buyer_name = $buyer_details->username;
	        }else{
	            $buyer_name = $buyer_details->first_name.' '.$buyer_details->last_name;
	        }



			$input_data['paid_amount'] = $input_data["total_amount"];
			$input_data['payer_id'] = $buyer_details["id"];
			$input_data['payer_email']= $buyer_details["email"];
			$input_data['payer_name'] = $buyer_name;

			$record = $_REQUEST["record"];
			$record['user_id'] = $input_data["user_id"];
			$record = json_decode(json_encode($record)); //Turn it into an object		

			$selling_book_slug = $record->slug;	

			$gateway_details = $_REQUEST["gateway_details"];

		} else {
			
			/*$input_data 		= $_SESSION['userdata']['book_purchase_data'];
			$selling_book_slug 	= $_SESSION['userdata']['selling_book_slug'];
			$record 			= $_SESSION['userdata']['selling_book_det'];
			$gateway_details 	= $_SESSION['userdata']['gateway_details'];
			$buyer_details 		= $_SESSION['userdata']['buyer_details'];*/

			$input_data 		= $this->session->userdata('book_purchase_data');
			$selling_book_slug  = $this->session->userdata('selling_book_slug');
			$record				= $this->session->userdata('selling_book_det');
			$gateway_details 	= $this->session->userdata('gateway_details');
			$buyer_details 		= $this->session->userdata('buyer_details');

			//`paid_amount`, `payer_id`, `payer_email`, `payer_name`

			//die("end here");

			$input_data['paid_date']      	= $this->input->post('payment_date');
			$input_data['transaction_id']   = $this->input->post('txn_id');
			$input_data['paid_amount']   	= $this->input->post('mc_gross');
			$input_data['payer_id']      	= $this->input->post('payer_id');
			$input_data['payer_email']      = $this->input->post('payer_email');
			$input_data['payer_name']      	= $this->input->post('first_name') . " " . $this->input->post('last_name');
		}

		$str_selling_book_slug = str_replace('_', ' ', $selling_book_slug);

		$str_selling_book_slug = ucfirst(str_replace('-', ' ', $str_selling_book_slug));
		
		$input_data['payment_status']   = "Completed"; 
		//$this->input->post('payment_status'); Uncomment this for live

		if ($input_data['payment_status'] == "Completed"){
			$success = 1;
        }


		if ($success == 1) {
			$discount_price = getBuyerDiscountedPrice($record->sc_id, $input_data['user_id']);

			if (get_user_credit_sum($input_data['user_id']) > 0 && $discount_price > get_user_credit_sum($input_data['user_id'])) {
				// echo "working if ";

				$sum_points = get_user_credit_sum($input_data['user_id']);
				$buyer_point_value = $this->config->item('site_settings')->buyer_point_value;

				$sum_points_total = $sum_points;
				$sum_points_total = $sum_points / $buyer_point_value;

				$this->home_model->addupdate_pointsystem($input_data['user_id'], $record->sc_id, "Partial Pay From Wallet", $sum_points_total, 'debited');
			}

			// Tree Api Start
			if($this->config->item('site_settings')->enable_moretrees_api == 'YES') {
				$points = $this->config->item('site_settings')->point_system_refersplanttree;
				$this->home_model->addupdate_pointsystem($input_data['user_id'], $record->sc_id, "Plant A Tree ( ".$str_selling_book_slug." )", $points);

				if ($input_data['api_val'] > 0 && !empty($buyer_details)) {
					$this->load->library('moretreesapi');
					$plantRes = $this->moretreesapi->plantATree([
							'first_name' => $buyer_name,
						'email' => $buyer_details->email
					]);
					if (isset($plantRes['result']) && !empty($plantRes['result']) && isset($plantRes['result']['status']) && $plantRes['result']['status'] == 1  && isset($plantRes['result']['response']) && $plantRes['result']['response'] == 'successful') {
						$input_data['moretrees_success'] = '1';
						$input_data['moretrees_cert_url'] = (isset($plantRes['result']['data']['certificates']) && count($plantRes['result']['data']['certificates']) > 0) ? $plantRes['result']['data']['certificates'][0]['certificateURL'] : '';
					}
				}
			}

			// Tree Api End

			$purchase_id = $this->base_model->insert_operation($input_data, 'book_purchases');

			if ($purchase_id > 0) {

				//05-12-2018 start
				//admin notification
				$purchased_rcrd = $this->base_model->get_query_result("SELECT c.*,s.book_name,t.username FROM pre_book_purchases c INNER JOIN pre_seller_selling_books s ON c.sc_id=s.sc_id INNER JOIN pre_users t ON c.seller_id=t.id WHERE c.purchase_id=" . $purchase_id . " ");

				if (!empty($purchased_rcrd)) {
					$purchased_rcrd = $purchased_rcrd[0];

					$seller_credit_points_sum = get_user_credit_sum($purchased_rcrd->seller_id, 'seller');

					// buyer wallet start
					// $admin_point_value = $this->config->item('site_settings')->admin_commission_on_book_purchase;

					$admin_commission = 0;
					$seller_paypal_id = '';
					$seller = $this->base_model->get_user_details($purchased_rcrd->seller_id);

					if (!empty($seller) && isset($seller[0])) {
						$seller_paypal_id = $seller[0]->paypal_email;
						$admin_commission = $seller[0]->admin_commission;
					}

					if (empty($admin_commission)) {
						$admin_commission = $this->config->item('site_settings')->admin_commission_on_book_purchase;
					}

					$admin_point_value = $admin_commission;

					$buyer_point_value = $this->config->item('site_settings')->buyer_point_value;

					$buyer_grand_total = $input_data['paid_amount'] / $buyer_point_value;

					// echo "<pre> buyer_point_value = ";print_r($buyer_point_value);
					// echo "<pre> buyer_grand_total = ";print_r($buyer_grand_total);

					$this->home_model->addupdate_pointsystem($input_data['user_id'], $record->sc_id, "Purchase ( ".$str_selling_book_slug." )", $buyer_grand_total, 'debited',$purchased_rcrd->transaction_id);

					if ($purchased_rcrd->transaction_id) {
						$this->home_model->addupdate_pointsystem($input_data['user_id'], $record->sc_id, "Credit Purchased Paypal", $buyer_grand_total, 'credited',$purchased_rcrd->transaction_id);
					}

					// buyer wallet end
					//  seller wallet start

					$seller_point_value = $this->config->item('site_settings')->seller_point_value;

					$seller_grand_total = $discount_price;
					
					$admin_commission_grand_total = ($discount_price * $admin_point_value * $seller_point_value);

					$seller_grand_total = ($discount_price * $seller_point_value*100);

					// echo "<pre> sum_points = ";print_r($discount_price);

					// echo "<pre> admin_point_value = ";print_r($admin_point_value);
					// echo "<pre> seller_point_value = ";print_r($seller_point_value);
					// echo "<pre> admin_commission_grand_total = ";print_r($admin_commission_grand_total);
					// echo "<pre> seller_grand_total = ";print_r($seller_grand_total);
					
					// die();

					$this->home_model->addupdate_pointsystem($record->seller_id, $record->sc_id, "Admin Commission For ( ".$str_selling_book_slug." )", $admin_commission_grand_total, 'debited',$purchased_rcrd->transaction_id);

					$this->home_model->addupdate_pointsystem($record->seller_id, $record->sc_id, "Earning From ( ".$str_selling_book_slug." )", $seller_grand_total, 'credited',$purchased_rcrd->transaction_id);

					//  seller wallet  end
					$data = array();
					$data['user_id'] 	= $purchased_rcrd->user_id;
					$data['title'] 		= get_languageword('buyer_purchased_book') . " " . $purchased_rcrd->book_name . " of Seller " . $purchased_rcrd->username;
					$data['content'] 	= "Buyer " . $this->session->userdata('first_name') . " " . $this->session->userdata('last_name') . " has been purchased book " . $purchased_rcrd->book_name . " of Seller " . $purchased_rcrd->username . " ";

					$data['datetime']   = date('Y-m-d H:i:s');
					$data['admin_read'] = 0;
					$data['page_link']  = SITEURL . "admin/view-purchased-books";
					$data['table_name'] = "book_purchases";
					$data['primary_key_column'] = "purchase_id";
					$data['primary_key_value']  = $purchased_rcrd->purchase_id;

					$this->base_model->insert_operation($data, 'notifications');
					unset($data);

					//send email to buyer
					$seller_rec 		= getUserRec($input_data['seller_id']);
					$user_rec 		= getUserRec($purchased_rcrd->user_id);
					$currency = $this->config->item('site_settings')->currency_symbol;
					$plantATreeHtml = '';
					if ((float)$purchased_rcrd->api_val > 0) {
						$plantATreeHtml = '<p>Tree Plantation&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <strong>' . $currency . $purchased_rcrd->api_val . '</strong></p>';
					}
					$email_tpl 		= $this->base_model->fetch_records_from('email_templates', array('template_status' => 'Active', 'email_template_id' => '23'));
					if (!empty($email_tpl)) {

						$email_tpl = $email_tpl[0];


						if (!empty($email_tpl->from_email)) {
							$from = $email_tpl->from_email;
						} else {
							$from 	= get_system_settings('Portal_Email');
						}
						$to 	= $user_rec->email;


						if (!empty($email_tpl->template_subject)) {
							$sub = $email_tpl->template_subject;
						} else {
							$sub = get_languageword("Book_Purchased");
						}

						if (!empty($email_tpl->template_content)) {

							$logo_img = '<img src="' . get_site_logo() . '" class="img-responsive" width="120px" height="50px">';

							$site_title = $this->config->item('site_settings')->site_title;

							$original_vars  = array($logo_img, $site_title, $user_rec->username, $purchased_rcrd->book_name, date('Y-m-d'), $purchased_rcrd->book_name, $seller_rec->username, $currency . round($purchased_rcrd->paid_amount,2) . $plantATreeHtml, $purchased_rcrd->transaction_id, $purchased_rcrd->payer_id, $purchased_rcrd->payer_email, $purchased_rcrd->payer_name, $purchased_rcrd->payment_status);

							$temp_vars		= array('__SITE_LOGO__', '__SITE_TITLE__', '__USER__NAME__', '__BOOK_NAME__', '__PURCHASED_DATE__', '__BOOK_NAME__', '__SELLER_NAME__', '__PAID_AMOUNT__', '__TRANSACTION_ID__', '__PAYER ID__', '__PAYER_EMAIL__', '__PAYER_NAME__', '__PAYMENT_STATUS__');

							$msg = str_replace($temp_vars, $original_vars, $email_tpl->template_content);

							sendEmail($from, $to, $sub, $msg);
						}
					} //send email to buyer end

					//send email to seller
					$email_tpl 		= $this->base_model->fetch_records_from('email_templates', array('template_status' => 'Active', 'email_template_id' => '24'));
					if (!empty($email_tpl)) {

						$email_tpl = $email_tpl[0];


						if (!empty($email_tpl->from_email)) {
							$from = $email_tpl->from_email;
						} else {
							$from 	= get_system_settings('Portal_Email');
						}
						$to 	= $seller_rec->email;


						if (!empty($email_tpl->template_subject)) {
							$sub = $email_tpl->template_subject;
						} else {
							$sub = get_languageword("Buyer_Purchased_Book");
						}

						if (!empty($email_tpl->template_content)) {

							$logo_img = '<img src="' . get_site_logo() . '" class="img-responsive" width="120px" height="50px">';

							$site_title = $this->config->item('site_settings')->site_title;

							$original_vars  = array($logo_img, $site_title, $seller_rec->username, $user_rec->username, $purchased_rcrd->book_name, date('Y-m-d'), $purchased_rcrd->book_name, $currency . $purchased_rcrd->total_amount, $purchased_rcrd->admin_commission_percentage, $purchased_rcrd->admin_commission_val, $currency . round($purchased_rcrd->paid_amount,2), $purchased_rcrd->transaction_id, $purchased_rcrd->payer_id, $purchased_rcrd->payer_email, $purchased_rcrd->payer_name, $purchased_rcrd->payment_status, $purchased_rcrd->status_of_payment_to_seller);

							$temp_vars		= array('__SITE_LOGO__', '__SITE_TITLE__', '__SELLER__NAME__', '__BUYER_NAME__', '__BOOK_NAME__', '__PURCHASED_DATE__', '__BOOK_NAME__', '__TOTAL_AMOUNT__', '__PERCENT__', '__VALUE__', '__PAID_AMOUNT__', '__TRANSACTION_ID__', '__PAYER ID__', '__PAYER_EMAIL__', '__PAYER_NAME__', '__PAYMENT_STATUS__', '__PAYMENT_SELLER__');

							$msg = str_replace($temp_vars, $original_vars, $email_tpl->template_content);

							sendEmail($from, $to, $sub, $msg);
						}
					}
					//send email to seller end

					//send email to admin
					$email_tpl 		= $this->base_model->fetch_records_from('email_templates', array('template_status' => 'Active', 'email_template_id' => '25'));
					if (!empty($email_tpl)) {

						$email_tpl = $email_tpl[0];


						if (!empty($email_tpl->from_email)) {
							$from = $email_tpl->from_email;
						} else {
							$from 	= get_system_settings('Portal_Email');
						}
						$to 	= get_system_settings('Portal_Email');


						if (!empty($email_tpl->template_subject)) {
							$sub = $email_tpl->template_subject;
						} else {
							$sub = get_languageword("Buyer_Purchased_Book");
						}

						if (!empty($email_tpl->template_content)) {

							$logo_img = '<img src="' . get_site_logo() . '" class="img-responsive" width="120px" height="50px">';

							$site_title = $this->config->item('site_settings')->site_title;

							$original_vars  = array($logo_img, $site_title, $user_rec->username, $purchased_rcrd->book_name, date('Y-m-d'), $purchased_rcrd->book_name, $seller_rec->username, $currency . $purchased_rcrd->total_amount, $purchased_rcrd->admin_commission_percentage, $purchased_rcrd->admin_commission_val, $currency . round($purchased_rcrd->paid_amount,2) . $plantATreeHtml, $purchased_rcrd->transaction_id, $purchased_rcrd->payer_id, $purchased_rcrd->payer_email, $purchased_rcrd->payer_name, $purchased_rcrd->payment_status, $purchased_rcrd->status_of_payment_to_seller);

							$temp_vars		= array('__SITE_LOGO__', '__SITE_TITLE__', '__BUYER_NAME__', '__BOOK_NAME__', '__SELLER_NAME__', '__PURCHASED_DATE__', '__BOOK_NAME__', '__TOTAL_AMOUNT__', '__PERCENT__', '__VALUE__', '__PAID_AMOUNT__', '__TRANSACTION_ID__', '__PAYER ID__', '__PAYER_EMAIL__', '__PAYER_NAME__', '__PAYMENT_STATUS__', '__PAYMENT_SELLER__');

							$msg = str_replace($temp_vars, $original_vars, $email_tpl->template_content);

							sendEmail($from, $to, $sub, $msg);
						}
					}
					//send email to admin end
				}

				if ($gateway_details[0]->type_id == CREDITS_PAYMENT) {


					// DEBIT THE CREDITS FROM BUYER && CREDIT THE 

					//Log Credits transaction data & update user net credits - Start
					$buyer_id = $this->ion_auth->get_user_id();

					$log_data = array(
						'user_id' => $buyer_id,
						'credits' => $input_data['book_credits'],
						'per_credit_value' => $input_data['per_credit_value'],
						'action'  => 'debited',
						'purpose' => 'Purchased Book "' . $record->book_name . '" and Purchased Id is ' . $purchase_id,
						'date_of_action	' => date('Y-m-d H:i:s'),
						'reference_table' => 'book_purchases',
						'reference_id' => $purchase_id,
					);

					log_user_credits_transaction($log_data);

					update_user_credits($buyer_id, $input_data['book_credits'], 'debit');
					//Log Credits transaction data & update user net credits - End
					$seller_acquired_credits = $input_data['book_credits'] - $input_data['admin_commission_val'];

					//Log Credits transaction data & update user net credits - Start
					$log_data = array(
						'user_id' => $input_data['seller_id'],
						'credits' => $seller_acquired_credits,
						'per_credit_value' => $input_data['per_credit_value'],
						'action'  => 'credited',
						'purpose' => 'Credits added for the purchase of book "' . $record->book_name . '" ',
						'date_of_action	' => date('Y-m-d H:i:s'),
						'reference_table' => 'book_purchases',
						'reference_id' => $purchase_id,
					);

					log_user_credits_transaction($log_data);

					update_user_credits($input_data['seller_id'], $seller_acquired_credits, 'credit');
				}

				//05-12-2018 end



				if (!empty($record->sellingbook_curriculum)) {

					//Create Zip with all attachments
					$this->load->library('zip');

					$dir 	= $purchase_id . '_' . $input_data['user_id'];

					$data 	= array();

					$sno 	= 1;

					foreach ($record->sellingbook_curriculum as $key => $value) {
						if ($value->source_type == "file") {

							$name 		= $sno . '.' . $value->title . '.' . $value->file_ext;

							$file_name = $value->file_name;
							$file_path = URL_PUBLIC_UPLOADS . 'book_curriculum_files/' . $value->file_name;

							$response = common_s3_function('get', 'file', $file_name, $file_path);

							if(!empty($response) && $response['status'] == 'success') {
								file_put_contents(URL_PUBLIC_UPLOADS . 'book_curriculum_files/' . $value->file_name, base64_decode($response['message']));

								$content = file_get_contents(URL_PUBLIC_UPLOADS . 'book_curriculum_files/' . $value->file_name);

								$data[$name] = $content;
							}

							$this->zip->add_data($data);

							unlink($file_path);
						} else {

							$name 		= $sno . '.' . $value->title . '.txt';
							$content 	= $value->file_name;

							$data[$name] = $content;

							$this->zip->add_data($data);
						}

						$sno++;
					}

					$this->zip->archive(URL_PUBLIC_UPLOADS . 'book_curriculum_files/' . $dir . '.zip');

					$file_path = 'assets/uploads/book_curriculum_files/';
					$file_name = $dir.'.zip';

					$response = common_s3_function('set', 'zip', $file_name, $file_path);

					if(!empty($response) && $response['status'] == 'success') {
						unlink($file_path.$file_name);
					}
				}


				$this->load->model('buyer/buyer_model');
				$this->buyer_model->update_seller_book_purchases($record->seller_id, $record->sc_id);


				//Email Alert to User - Start

				//Email Alert to User - End

				//echo '<pre> Before $this->session :: '; print_r($this->session);

				$unset_valid_request		= $this->session->userdata('is_valid_request');
				$unset_input_data 			= $this->session->userdata('book_purchase_data');
				$unset_selling_book_slug  	= $this->session->userdata('selling_book_slug');
				$unset_record				= $this->session->userdata('selling_book_det');
				$unset_gateway_details 		= $this->session->userdata('gateway_details');
				$unset_buyer_details 		= $this->session->userdata('buyer_details');

				//echo '<pre> $unset_input_data :: '; print_r($unset_input_data);

				$this->session->set_userdata('is_valid_request', null);
				$this->session->set_userdata('book_purchase_data', null);
				$this->session->set_userdata('selling_book_slug', null);
				$this->session->set_userdata('selling_book_det', null);
				$this->session->set_userdata('gateway_details', null);
				$this->session->set_userdata('buyer_details', null);

				//if($this->session->userdata('is_valid_request'))
				//$this->session->unset_userdata('is_valid_request');
				//$this->session->unset_userdata($unset_input_data);
				//$this->session->unset_userdata('selling_book_slug');
				//$this->session->unset_userdata('selling_book_det');
				//$this->session->unset_userdata('gateway_details');

				//echo '<pre> After $this->session :: '; print_r($this->session); die;

				$this->prepare_flashmessage("You purchased Book Successfully", 0);
				// redirect(URL_HOME_BUY_BOOK.'/'.$selling_book_slug);
				redirect(URL_BUYER_BOOK_PURCHASES);
			} else {

				$this->prepare_flashmessage("Purchase Data Not Saved", 2);
				redirect(URL_HOME_BUY_BOOK . '/' . $selling_book_slug);
			}
		} else {

			$this->prepare_flashmessage("Purchase Data not saved due to some technical issue. Please contact Admin", 2);
			redirect(URL_HOME_BUY_BOOK . '/' . $selling_book_slug);
		}

		// } else {

		// 	$this->prepare_flashmessage("Invalid Operation", 1);
		// 	redirect(URL_HOME_BUY_BOOKS);
		// }

	}

	function payment_cancel()
	{
		$this->session->unset_userdata('is_valid_request');
		$this->session->unset_userdata('book_purchase_data');
		$this->session->unset_userdata('selling_book_slug');
		$this->session->unset_userdata('selling_book_det');
		$this->session->unset_userdata('gateway_details');

		$this->prepare_flashmessage('You have cancelled your transaction', 2);
		redirect(URL_HOME_BUY_BOOKS);
	}


	//process stripe
	function get_additional_content() {

		if ( $this->input->is_ajax_request() ) {

			$sc_id 		= $_POST['sc_id'];


			if(!($sc_id > 0)) {
				$this->prepare_flashmessage(get_languageword('Invalid_Request'), 1);
				redirect(URL_HOME_BUY_BOOKS);
			}
			$selling_book_slug = $this->base_model->fetch_value('seller_selling_books', 'slug', array('sc_id' => $sc_id));


			$final_cost = $_POST['final_cost'];
			$name 		= $_POST['name'];
			$content = '';
			$gateway_id = STRIPE_PAYMENT_GATEWAY;

			if(!($gateway_id > 0)) {

				$this->prepare_flashmessage(get_languageword('Please_select_payment_gateway'), 2);
				redirect(URL_HOME_CHECKOUT.'/'.$selling_book_slug);
			}

			if(!$this->ion_auth->logged_in()) {

				$this->session->set_userdata('req_from', 'buy_book');
				$this->session->set_userdata('selling_book_slug', $selling_book_slug);
				$this->prepare_flashmessage(get_languageword('please_login_to_continue'), 2);
				redirect(URL_AUTH_LOGIN);
			}

			$user_id=$this->ion_auth->get_user_id();
			$record = get_seller_sellingbook_info($sc_id);
			if(empty($record)) {

				$this->prepare_flashmessage(get_languageword('No Details Found'), 2);
				redirect(URL_HOME_BUY_BOOKS);
			}

			$gateway_details=$this->base_model->get_payment_gateways(' AND st2.type_id = '.$gateway_id);
			if(empty($gateway_details)) {
				$this->prepare_flashmessage(get_languageword('Payment Gateway Details Not Found'), 2);
				redirect(URL_HOME_CHECKOUT.'/'.$selling_book_slug);
			}

			$total_amount 					= $this->get_discounted_price($record, $user_id);
			$admin_commission_percentage 	= $record->admin_commission_percentage;
			$admin_commission_val 			= number_format(($total_amount * ($admin_commission_percentage / 100)), 2);


			$input_data['sc_id']    	    = $sc_id;
			$input_data['seller_id']    	    = $record->seller_id;
			$input_data['user_id']          = $user_id;
			$input_data['total_amount']     = $total_amount;
			$input_data['item_price']     = $total_amount;
			$input_data['admin_commission_percentage']  = $admin_commission_percentage;
			$input_data['admin_commission_val']     	= $admin_commission_val;

			$input_data['max_downloads']    = $record->max_downloads;

	        $input_data['payment_gateway_id']   = $gateway_id;
	        $input_data['paid_date']     		= date('Y-m-d H:i:s');
	        $input_data['last_modified']     	= date('Y-m-d H:i:s');


	        $book_title = $record->book_title;

	        $this->session->set_userdata('is_valid_request', 1);
			$this->session->set_userdata('book_purchase_data', $input_data);
			$this->session->set_userdata('selling_book_slug', $selling_book_slug);
			$this->session->set_userdata('selling_book_det', $record);
			$this->session->set_userdata('gateway_details', $gateway_details);

			if ( $gateway_id == 41 ) { // Stripe
				$user_info = $this->base_model->get_user_details($user_id);

				$field_values = $this->db->get_where('system_settings_fields',array('type_id' => $gateway_id))->result();


				$key = 'pk_test_H8R3tFH4RiyF0VGzTcXwl8NF';
				$stripe_test_mode = FALSE;
				foreach($field_values as $value) {
					if($value->field_key == 'stripe_test_mode') {
						if ( strip_tags( trim( $value->field_output_value ) ) == 'yes' ) {
							$stripe_test_mode = TRUE;
						}
					}
				}
				foreach($field_values as $value) {
					if( $value->field_key == 'stripe_key_test_publishable' && $stripe_test_mode == TRUE ) {
						$key = $value->field_output_value;
					} elseif ( $value->field_key == 'stripe_key_live_publishable' && $stripe_test_mode == FALSE ) {
						$key = $value->field_output_value;
					}
				}
				ob_start();
				?>
				<form action="<?php echo site_url('pay/process_stripe/'.$sc_id);?>" method="POST" id="frm_stripe">
				  <script
					src="https://checkout.stripe.com/v2/checkout.js" class="stripe-button" id="stripe_script"
					data-key="<?php echo $key;?>"
					data-amount="<?php echo $final_cost;?>"
					data-name="<?php echo $name;?>"
					data-description="<?php echo $name;?>"
					data-image="<?php  if(isset($this->config->item('site_settings')->logo) && $this->config->item('site_settings')->logo != '') echo substr(URL_PUBLIC_UPLOADS_SETTINGS.''.$this->config->item('site_settings')->logo, 5); else echo substr(URL_FRONT_IMAGES.'Logo.png', 5);?>"
					data-package_id="<?php echo $sc_id;?>"
					data-currency="<?php echo get_system_settings('Currency_Code');?>"
					data-email="<?php echo $user_info[0]->email;?>"
					data-locale="auto"
					>
				  </script>
				</form>
				<?php
				$content = ob_get_clean();
			}
			echo $content;
		}
	}

	//stripe payment for purchase book
	function process_stripe($sc_id)
	{
		$user_id = $this->ion_auth->get_user_id();
		$success = 0;

		if ( isset( $_POST['stripeToken'] ) && $user_id != '' ) {

			if($this->session->userdata('book_purchase_data') && $this->session->userdata('is_valid_request')) {

				$sc_id = $this->uri->segment(3);
				$gateway_id = STRIPE_PAYMENT_GATEWAY;
				$token  = $_POST['stripeToken'];


				$gateway_details = $this->base_model->get_payment_gateways(' AND st2.type_id = '.$gateway_id);
				$record 		= get_seller_sellingbook_info($sc_id);//selling book record
				$user_details = $this->base_model->get_user_details( $user_id );
				$user_info = $user_details[0];


				if (count($gateway_details) > 0 && count($record) > 0 && ! empty( $user_info )) {

					$field_values = $this->db->get_where('system_settings_fields',array('type_id' => $gateway_id))->result();

					$total_amount 	= $this->get_discounted_price($record, $user_id);

					$stripeEmail = $_POST['stripeEmail'];

					$config = array('stripe_test_mode' => 'yes', 'stripe_key_test_secret' => 'sk_test_FHxf1NsgaWbAFAGny5zJELqU', 'stripe_key_live_secret' => 'pk_live_wPo6I0iKgXrs9mrk08cfwzc4', 'stripe_verify_ssl' => TRUE); // Default Values


					foreach($field_values as $value) {
						$config[ $value->field_key ] = $value->field_output_value;
					}				
					$this->load->library( 'stripe', $config );
					$customer = $this->stripe->customer_create( $token, $stripeEmail );
					$customer = json_decode( $customer ); // We are receiving data in JSON format so we need to decode it!

					
					if ( isset( $customer->error ) ) {
						
						$this->prepare_flashmessage(get_languageword('Payment failed : '). ": <strong>" . $customer->error->message . "</strong>", 1);
						redirect(URL_HOME_BUY_BOOK.'/'.$selling_book_slug);

					} else {

						
						$charge = $this->stripe->charge_customer( $total_amount, $customer->id, $record->book_title, get_system_settings('Currency_Code') ); // $amount, $customer_id, $desc

						$input_data 		= $this->session->userdata('book_purchase_data');
						$selling_book_slug= $this->session->userdata('selling_book_slug');
						$record				= $this->session->userdata('selling_book_det');
						$gateway_details 	= $this->session->userdata('gateway_details');

				
						$input_data['paid_date']      	= date('Y-m-d H:i:s');
						$input_data['transaction_id']   = $token;
						$input_data['paid_amount']   	= $this->get_discounted_price($record, $user_id);
						$input_data['payer_id']      	= $customer->id;
						$input_data['payer_email']      = $stripeEmail;
						$input_data['payer_name']      	= $user_info->username;
						$input_data['payment_status']   = "Completed";//$this->input->post('payment_status'); Uncomment this for live

						if($input_data['payment_status'] == "Completed")
							$success = 1;
					}

				} else {
					$this->prepare_flashmessage("Invalid Operation", 1);
					redirect(URL_HOME_BUY_BOOK.'/'.$selling_book_slug);
				}




				if ($success == 1) {

					$purchase_id = $this->base_model->insert_operation($input_data, 'book_purchases');



					if($purchase_id > 0) {

						//05-12-2018 start
						//admin notification
						$purchased_rcrd = $this->base_model->get_query_result("SELECT c.*,s.book_name,t.username FROM pre_book_purchases c INNER JOIN pre_seller_selling_books s ON c.sc_id=s.sc_id INNER JOIN pre_users t ON c.seller_id=t.id WHERE c.purchase_id=".$purchase_id." ");

						if (!empty($purchased_rcrd)) {

							$purchased_rcrd = $purchased_rcrd[0];

							$data = array();
							$data['user_id'] 	= $purchased_rcrd->user_id;
							$data['title'] 		= get_languageword('buyer_purchased_book')." ".$purchased_rcrd->book_name." of Seller ".$purchased_rcrd->username;
							$data['content'] 	= "Buyer ".$this->session->userdata('username')." has been purchased book ".$purchased_rcrd->book_name." of Seller ".$purchased_rcrd->username." ";

							$data['datetime']   = date('Y-m-d H:i:s');
							$data['admin_read'] = 0;
							$data['page_link']  = SITEURL."admin/view-purchased-books";
							$data['table_name'] = "book_purchases";
							$data['primary_key_column'] = "purchase_id";
							$data['primary_key_value']  = $purchased_rcrd->purchase_id;

							
							$this->base_model->insert_operation($data,'notifications');	
							unset($data);


							//send email to buyer
							$seller_rec 		= getUserRec($input_data['seller_id']);
							$user_rec 		= getUserRec($purchased_rcrd->user_id);
							$currency = $this->config->item('site_settings')->currency_symbol;
							$email_tpl 		= $this->base_model->fetch_records_from('email_templates', array('template_status' => 'Active', 'email_template_id' => '23'));
							if(!empty($email_tpl)) {

								$email_tpl = $email_tpl[0];
							

								if(!empty($email_tpl->from_email)) {
									$from = $email_tpl->from_email;
								} else {
									$from 	= get_system_settings('Portal_Email');
								}
								$to 	= $user_rec->email;


								if(!empty($email_tpl->template_subject)) {
									$sub = $email_tpl->template_subject;
								} else {
									$sub = get_languageword("Book_Purchased");
								}

								if (!empty($email_tpl->template_content)) {

									$logo_img='<img src="'.get_site_logo().'" class="img-responsive" width="120px" height="50px">';

									$site_title = $this->config->item('site_settings')->site_title;

									$original_vars  = array($logo_img, $site_title, $user_rec->username, $purchased_rcrd->book_name, date('Y-m-d'), $purchased_rcrd->book_name, $seller_rec->username, $currency.$purchased_rcrd->paid_amount, $purchased_rcrd->transaction_id, $purchased_rcrd->payer_id, $purchased_rcrd->payer_email, $purchased_rcrd->payer_name, $purchased_rcrd->payment_status);

									$temp_vars		= array('__SITE_LOGO__', '__SITE_TITLE__', '__USER__NAME__', '__BOOK_NAME__', '__PURCHASED_DATE__', '__BOOK_NAME__', '__SELLER_NAME__', '__PAID_AMOUNT__', '__TRANSACTION_ID__', '__PAYER ID__', '__PAYER_EMAIL__', '__PAYER_NAME__', '__PAYMENT_STATUS__');

									$msg = str_replace($temp_vars, $original_vars, $email_tpl->template_content);

									sendEmail($from, $to, $sub, $msg);
								}
							} //send email to buyer end



							//send email to seller
							$email_tpl 		= $this->base_model->fetch_records_from('email_templates', array('template_status' => 'Active', 'email_template_id' => '24'));
							if(!empty($email_tpl)) {

								$email_tpl = $email_tpl[0];
							

								if(!empty($email_tpl->from_email)) {
									$from = $email_tpl->from_email;
								} else {
									$from 	= get_system_settings('Portal_Email');
								}
								$to 	= $seller_rec->email;


								if(!empty($email_tpl->template_subject)) {
									$sub = $email_tpl->template_subject;
								} else {
									$sub = get_languageword("Buyer_Purchased_Book");
								}

								if (!empty($email_tpl->template_content)) {

									$logo_img='<img src="'.get_site_logo().'" class="img-responsive" width="120px" height="50px">';

									$site_title = $this->config->item('site_settings')->site_title;

									$original_vars  = array($logo_img, $site_title, $seller_rec->username, $user_rec->username, $purchased_rcrd->book_name, date('Y-m-d'), $purchased_rcrd->book_name, $currency.$purchased_rcrd->total_amount, $purchased_rcrd->admin_commission_percentage, $purchased_rcrd->admin_commission_val, $currency.$purchased_rcrd->paid_amount, $purchased_rcrd->transaction_id, $purchased_rcrd->payer_id, $purchased_rcrd->payer_email, $purchased_rcrd->payer_name, $purchased_rcrd->payment_status, $purchased_rcrd->status_of_payment_to_seller);

									$temp_vars		= array('__SITE_LOGO__', '__SITE_TITLE__', '__SELLER__NAME__', '__BUYER_NAME__', '__BOOK_NAME__', '__PURCHASED_DATE__', '__BOOK_NAME__', '__TOTAL_AMOUNT__', '__PERCENT__', '__VALUE__', '__PAID_AMOUNT__', '__TRANSACTION_ID__', '__PAYER ID__', '__PAYER_EMAIL__', '__PAYER_NAME__', '__PAYMENT_STATUS__', '__PAYMENT_SELLER__');

									$msg = str_replace($temp_vars, $original_vars, $email_tpl->template_content);

									sendEmail($from, $to, $sub, $msg);
								}
							}
							//send email to seller end


							//send email to admin
							$email_tpl 		= $this->base_model->fetch_records_from('email_templates', array('template_status' => 'Active', 'email_template_id' => '25'));
							if(!empty($email_tpl)) {

								$email_tpl = $email_tpl[0];
							

								if(!empty($email_tpl->from_email)) {
									$from = $email_tpl->from_email;
								} else {
									$from 	= get_system_settings('Portal_Email');
								}
								$to 	= get_system_settings('Portal_Email');


								if(!empty($email_tpl->template_subject)) {
									$sub = $email_tpl->template_subject;
								} else {
									$sub = get_languageword("Buyer_Purchased_Book");
								}

								if (!empty($email_tpl->template_content)) {

									$logo_img='<img src="'.get_site_logo().'" class="img-responsive" width="120px" height="50px">';

									$site_title = $this->config->item('site_settings')->site_title;

									$original_vars  = array($logo_img, $site_title, $user_rec->username, $purchased_rcrd->book_name, date('Y-m-d'), $purchased_rcrd->book_name, $seller_rec->username, $currency . $purchased_rcrd->total_amount, $purchased_rcrd->admin_commission_percentage, $purchased_rcrd->admin_commission_val, $currency . round($purchased_rcrd->paid_amount,2), $purchased_rcrd->transaction_id, $purchased_rcrd->payer_id, $purchased_rcrd->payer_email, $purchased_rcrd->payer_name, $purchased_rcrd->payment_status, $purchased_rcrd->status_of_payment_to_seller);

									$temp_vars		= array('__SITE_LOGO__', '__SITE_TITLE__', '__BUYER_NAME__', '__BOOK_NAME__','__SELLER_NAME__','__PURCHASED_DATE__', '__BOOK_NAME__', '__TOTAL_AMOUNT__', '__PERCENT__', '__VALUE__', '__PAID_AMOUNT__', '__TRANSACTION_ID__', '__PAYER ID__', '__PAYER_EMAIL__', '__PAYER_NAME__', '__PAYMENT_STATUS__', '__PAYMENT_SELLER__');

									$msg = str_replace($temp_vars, $original_vars, $email_tpl->template_content);

									sendEmail($from, $to, $sub, $msg);
								}
							}
							//send email to admin end
						}

						if($gateway_details[0]->type_id == CREDITS_PAYMENT){

						
							// DEBIT THE CREDITS FROM BUYER && CREDIT THE 

							//Log Credits transaction data & update user net credits - Start
							$buyer_id = $this->ion_auth->get_user_id();
							
							$log_data = array(
											'user_id' => $buyer_id,
											'credits' => $input_data['book_credits'],
											'per_credit_value' => $input_data['per_credit_value'],
											'action'  => 'debited',
											'purpose' => 'Purchased Book "'.$record->book_name.'" and Purchased Id is '.$purchase_id,
											'date_of_action	' => date('Y-m-d H:i:s'),
											'reference_table' => 'book_purchases',
											'reference_id' => $purchase_id,
										);
							
							log_user_credits_transaction($log_data);

							update_user_credits($buyer_id, $input_data['book_credits'], 'debit');
							//Log Credits transaction data & update user net credits - End
							$seller_acquired_credits = $input_data['book_credits'] - $input_data['admin_commission_val'];

							//Log Credits transaction data & update user net credits - Start
							$log_data = array(
											'user_id' => $input_data['seller_id'],
											'credits' => $seller_acquired_credits,
											'per_credit_value' => $input_data['per_credit_value'],
											'action'  => 'credited',
											'purpose' => 'Credits added for the purchase of book "'.$record->book_name.'" ',
											'date_of_action	' => date('Y-m-d H:i:s'),
											'reference_table' => 'book_purchases',
											'reference_id' => $purchase_id,
										);

							log_user_credits_transaction($log_data);

							update_user_credits($input_data['seller_id'], $seller_acquired_credits, 'credit');
						}

						//05-12-2018 end
						


						if(!empty($record->sellingbook_curriculum)) {

							//Create Zip with all attachments
							$this->load->library('zip');

							$dir 	= $purchase_id.'_'.$input_data['user_id'];

							$data 	= array();

							$sno 	= 1;

							foreach ($record->sellingbook_curriculum as $key => $value) {
								
								if($value->source_type == "file") {

									$name 		= $sno.'.'.$value->title.'.'.$value->file_ext;

									$file_name = $value->file_name;
									$file_path = URL_PUBLIC_UPLOADS . 'book_curriculum_files/' . $value->file_name;

									$response = common_s3_function('get', 'file', $file_name, $file_path);

									if(!empty($response) && $response['status'] == 'success') {
										file_put_contents(URL_PUBLIC_UPLOADS . 'book_curriculum_files/' . $value->file_name, base64_decode($response['message']));

										$content = file_get_contents(URL_PUBLIC_UPLOADS . 'book_curriculum_files/' . $value->file_name);

										$data[$name] = $content;
									}

									$this->zip->add_data($data);

									unlink($file_path);
								} else {

									$name 		= $sno.'.'.$value->title.'.txt';
									$content 	= $value->file_name;

									$data[$name] = $content;

									$this->zip->add_data($data);

								}

								$sno++;
							}

							$this->zip->archive(URL_PUBLIC_UPLOADS . 'book_curriculum_files/' . $dir . '.zip');

							$file_path = 'assets/uploads/book_curriculum_files/';
							$file_name = $dir.'.zip';

							$response = common_s3_function('set', 'zip', $file_name, $file_path);

							if(!empty($response) && $response['status'] == 'success') {
								unlink($file_path.$file_name);
							}
						}


						$this->load->model('buyer/buyer_model');
						$this->buyer_model->update_seller_book_purchases($record->seller_id, $record->sc_id);


			            //Email Alert to User - Start

			            //Email Alert to User - End


						$this->session->unset_userdata('is_valid_request');
						$this->session->unset_userdata('book_purchase_data');
						$this->session->unset_userdata('selling_book_slug');
						$this->session->unset_userdata('selling_book_det');
						$this->session->unset_userdata('gateway_details');
						
			            $this->prepare_flashmessage("You purchased Book Successfully", 0);
			            redirect(URL_HOME_BUY_BOOK.'/'.$selling_book_slug);

					} else {

						$this->prepare_flashmessage("Purchase Data Not Saved", 2);
			            redirect(URL_HOME_BUY_BOOK.'/'.$selling_book_slug);
					}

				} else {

					$this->prepare_flashmessage("Purchase Data not saved due to some technical issue. Please contact Admin", 2);
			        redirect(URL_HOME_BUY_BOOK.'/'.$selling_book_slug);
				}

			} else {
				$this->prepare_flashmessage("Invalid Operation", 1);
				redirect(URL_HOME_BUY_BOOK.'/'.$selling_book_slug);
			}
		}
		 else {

			$this->prepare_flashmessage("Bad Request", 1);
			redirect(URL_HOME_BUY_BOOK.'/'.$selling_book_slug);
		}
	}
	
}
