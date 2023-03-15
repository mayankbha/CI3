<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Admin extends MY_Controller 
{
	function __construct()
	{
		parent::__construct();
		$this->load->library(array('session'));
		$this->load->library(array('ion_auth','form_validation'));

	}
	
	/*** Displays the Index Page ***/
	function index()
	{	

		check_access('admin');

		$this->data['activemenu'] 	= "dashboard";
		$this->data['message'] 		= $this->session->flashdata('message');
		$usersCount = $this->base_model->get_usersCount();
		$this->data['usersCount']   = $usersCount; 

		$packageNames = array();
		$packageSubscriptions = array();
		$packagePayments = array();
		$Buyers = array();
		$Sellers = array();
		$Institutes = array();

		$packages_data = $this->base_model->get_packages_subscriptions();
		$this->data['packages_data'] = $packages_data;
		foreach($packages_data as $row){
			array_push($packageNames, $row->package_name);
			array_push($packageSubscriptions, $row->total_subscriptions);
			array_push($packagePayments, $row->total_payments);
			array_push($Buyers, $row->Buyers);
			array_push($Sellers, $row->Sellers);
			array_push($Institutes, $row->Institutes);

		}
		
		$treeCredits = 0;

		if($this->config->item('site_settings')->enable_moretrees_api == 'YES') {
			$this->load->library('moretreesapi');
			$creditBalRes = $this->moretreesapi->getCreditBalance();

			if(isset($creditBalRes['result']) && isset($creditBalRes['result']['status']) && isset($creditBalRes['result']['response']) && $creditBalRes['result']['response'] == 'successful') {
				$treeCredits = $creditBalRes['result']['data']['credits'];
			}
		}

		$this->data['packageNames'] = $packageNames;
		$this->data['pagetitle']	= get_languageword('seller_System');
		$this->data['packageSubscriptions'] = $packageSubscriptions;
		$this->data['packagePayments'] = $packagePayments;
		$this->data['Buyers'] = $Buyers;
		$this->data['Sellers'] = $Sellers;
		$this->data['Institutes'] = $Institutes;
		$this->data['content'] 		= 'dashboard';
		$this->data['treeCredits'] = $treeCredits;
		$this->_render_page('template/admin/admin-template', $this->data);
	}

	/**
	 * [changepassword description]
	 * @return [type] [description]
	 */
	function changepassword()
	{	
		if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin()) {
			$this->prepare_flashmessage(get_languageword('You dont have permission to access this page'), 1);
			redirect('auth/login', 'refresh');
		}

		$this->data['message'] 	 = $this->session->flashdata('message');
		if($this->input->post( 'submitbutt' ))
		{
			$this->form_validation->set_rules('current_password',get_languageword('current_password'),'trim|required');
			$this->form_validation->set_rules('new_password',get_languageword('new_password'),'trim|required');
			$this->form_validation->set_rules('retype_password',get_languageword('retype_password'),'trim|required|matches[new_password]');

			$this->form_validation->set_error_delimiters('<div class="error">', '</div>');

			if ($this->form_validation->run() == TRUE)
			{
				$identity = $this->session->userdata('identity');
				$change = $this->ion_auth->change_password($identity, $this->input->post('current_password'), $this->input->post('new_password'));
				if ($change)
				{
					$this->prepare_flashmessage(get_languageword('password_changed_successfully'), 0);
					redirect(URL_ADMIN_CHANGEPASSWORD);
				}
				else
				{
					//$this->data['message'] = prepare_message(validation_errors(),1);
					$this->prepare_flashmessage($this->ion_auth->errors(), 1);
					redirect(URL_ADMIN_CHANGEPASSWORD);
				}
			}

		}	
		$this->data['activemenu']= "dashboard";

		$this->data['pagetitle'] = 'Change Password';
		$this->data['content']   = 'changepassword';
		$this->_render_page('template/admin/admin-template', $this->data);
	}

	/**
	 * [all_leads description]
	 * @return [type] [description]
	 */
	function all_leads()
	{
		if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin()) {
			$this->prepare_flashmessage(get_languageword('You dont have permission to access this page'), 1);
			redirect('auth/login', 'refresh');
		}
		

		$this->load->library(array('grocery_CRUD'));
		$crud = new grocery_CRUD();
		$crud_state = $crud->getState();
		$crud->unset_jquery();
		$crud->set_table($this->db->dbprefix('buyer_leads'));
		$crud->set_relation('teaching_type_id','teaching_types','teaching_type');
		$crud->set_relation('location_id','locations','location_name');
		$crud->set_relation('book_id','categories','name');
		$crud->set_relation('user_id','users','username');
		$crud->set_subject( get_languageword('buyer_leads') );
		
		$crud->unset_add();
		$crud->unset_delete();
		$crud->unset_edit();
		$crud->unset_read();
		 
    			
		$crud->columns('user_id','book_id','teaching_type_id','location_id','title_of_requirement','priority_of_requirement','updated_at','duration_needed', 'no_of_views','status');
		
		//####### Changing column names #######
		$crud->display_as('updated_at',get_languageword('last_updated'));
		$crud->display_as('book_id',get_languageword('book_name'));
		$crud->display_as('teaching_type_id',get_languageword('teaching_type'));
		$crud->display_as('location_id',get_languageword('location_name'));
		$crud->display_as('duration_needed',get_languageword('duration'));
		$crud->display_as('user_id',get_languageword('buyer_name'));
			

		$crud->callback_column('priority_of_requirement',array($this,'callback_humanize_priority_of_requirement'));

		//#### Invisible fileds in reading ####
		if ($crud->getState() == 'read') {
		    $crud->field_type('user_id', 'invisible');
		    $crud->field_type('priority_of_requirement','invisible');
		}


		if ($crud_state=="list") {
			//update notification
			$view_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http")."://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
			update_notification($view_link);
		}


		$output = $crud->render();
		
		$this->data['activemenu'] 	= "myleads";
		$this->data['activesubmenu'] ="all_Leads";
		$this->data['content'] 		= 'admin_leads';
		$this->data['pagetitle'] = get_languageword('All_Leads');
		$this->data['grocery_output'] = $output;
		$this->data['grocery'] = TRUE;
		$this->grocery_output($this->data);
	}

	function callback_humanize_priority_of_requirement($primarykey, $row)
	{
		return ucfirst(str_replace('_', ' ', $row->priority_of_requirement));
	}

	/**
	 * [opened_leads description]
	 * @return [type] [description]
	 */
	function opened_leads()
	{
		if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin()) {
			$this->prepare_flashmessage(get_languageword('You dont have permission to access this page'), 1);
			redirect('auth/login', 'refresh');
		}
		

		$this->load->library(array('grocery_CRUD'));
		$crud = new grocery_CRUD();
		$crud_state = $crud->getState();
		$crud->unset_jquery();
		$crud->set_table($this->db->dbprefix('buyer_leads'));
		$crud->where('pre_buyer_leads.status', 'opened');
		$crud->set_relation('teaching_type_id','teaching_types','teaching_type');
		$crud->set_relation('location_id','locations','location_name');
		$crud->set_relation('book_id','categories','name');
		$crud->set_relation('user_id','users','username');
		$crud->set_subject( get_languageword('buyer_leads') );
	
		// unset actions	
		$crud->unset_add();
		$crud->unset_delete();
		$crud->unset_edit();
		$crud->unset_read();
		 
		 
    	// display columns		
		$crud->columns('book_id','user_id','teaching_type_id','location_id','title_of_requirement','priority_of_requirement','updated_at','duration_needed', 'no_of_views','status');
		
		$crud->callback_column('priority_of_requirement',array($this,'callback_humanize_priority_of_requirement'));
		// Changing column names 
		$crud->display_as('updated_at',get_languageword('last_updated'));
		$crud->display_as('book_id',get_languageword('book_name'));
		$crud->display_as('teaching_type_id',get_languageword('teaching_type'));
		$crud->display_as('location_id',get_languageword('location_name'));
		$crud->display_as('duration_needed',get_languageword('duration'));
		$crud->display_as('user_id',get_languageword('buyer_name'));
		
		
		// Invisible fileds in reading 
		if ($crud->getState() == 'read') {
		    $crud->field_type('user_id', 'invisible');
		}


		$output = $crud->render();
		
		$this->data['activemenu'] 	= "myleads";
		$this->data['activesubmenu'] = "opened_Leads";
		$this->data['content'] 		= 'admin_leads';
		$this->data['pagetitle'] = get_languageword('Opened Leads');
		$this->data['grocery_output'] = $output;
		$this->data['grocery'] = TRUE;
		// $this->_render_page('template/admin/admin-template-grocery', $this->data);
		$this->grocery_output($this->data);
	}

	/**
	 * [closed_leads description]
	 * @return [type] [description]
	 */
	function closed_leads()
	{
		if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin()) {
			$this->prepare_flashmessage(get_languageword('You dont have permission to access this page'), 1);
			redirect('auth/login', 'refresh');
		}


		$this->load->library(array('grocery_CRUD'));
		$crud = new grocery_CRUD();
		$crud_state = $crud->getState();
		$crud->unset_jquery();
		$crud->set_table($this->db->dbprefix('buyer_leads'));
		$crud->where('pre_buyer_leads.status', 'closed');
		$crud->set_relation('teaching_type_id','teaching_types','teaching_type');
		$crud->set_relation('location_id','locations','location_name');
		$crud->set_relation('book_id','categories','name');
		$crud->set_relation('user_id','users','username');
		$crud->set_subject( get_languageword('buyer_leads') );
		
		$crud->unset_add();
		$crud->unset_delete();
		$crud->unset_edit();
		$crud->unset_read();
		 
    			
		$crud->columns('book_id','user_id','teaching_type_id','location_id','title_of_requirement','priority_of_requirement','updated_at','duration_needed', 'no_of_views','status');
				

		$crud->callback_column('priority_of_requirement',array($this,'callback_humanize_priority_of_requirement'));
		//####### Changing column names #######
		$crud->display_as('updated_at',get_languageword('last_updated'));
		$crud->display_as('book_id',get_languageword('book_name'));
		$crud->display_as('teaching_type_id',get_languageword('teaching_type'));
		$crud->display_as('location_id',get_languageword('location_name'));
		$crud->display_as('duration_needed',get_languageword('duration'));
		$crud->display_as('user_id',get_languageword('buyer_name'));

		//#### Invisible fileds in reading ####
		if ($crud->getState() == 'read') {
		    $crud->field_type('user_id', 'invisible');
		}


		$output = $crud->render();
		
		$this->data['activemenu'] 	= "myleads";
		$this->data['activesubmenu'] = "closed_Leads";
		$this->data['content'] 		= 'admin_leads';
		$this->data['pagetitle'] = get_languageword('closed Leads');
		$this->data['grocery_output'] = $output;
		$this->data['grocery'] = TRUE;
		$this->grocery_output($this->data);	
	}

	/**
	 * [faqs description]
	 * @return [type] [description]
	 */
	function faqs()
	{
		if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin()) {
			$this->prepare_flashmessage(get_languageword('You dont have permission to access this page'), 1);
			redirect('auth/login', 'refresh');
		}
		

		$this->load->library(array('grocery_CRUD'));
		$crud = new grocery_CRUD();
		$crud_state = $crud->getState();
		$crud->unset_jquery();
		$crud->set_table($this->db->dbprefix('faqs'));
		$crud->set_subject( get_languageword('FAQs') );
		
		$crud->required_fields('question','answer');
		
		//display columns    			
		$crud->columns('question','answer','status');
		
		// Changing column names 
		$crud->display_as('updated_at',get_languageword('last_updated'));
		
		$output = $crud->render();
		
		if($crud_state == 'read')
			$crud_state ='View';

		$this->data['activemenu'] 	= "pages";
		$this->data['activesubmenu'] 	= "faqs";
		$this->data['pagetitle'] = get_languageword($crud_state) .' '. get_languageword('faqs');
		$this->data['grocery_output'] = $output;
		$this->data['grocery'] = TRUE;
		$this->grocery_output($this->data);	
	}

	/**
	 * [dynamic_pages description]
	 * @return [type] [description]
	 */
	function dynamic_pages()
	{
		if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin()) {
			$this->prepare_flashmessage(get_languageword('You dont have permission to access this page'), 1);
			redirect('auth/login', 'refresh');
		}
		

		$this->load->library(array('grocery_CRUD_extended'));
		$crud = new grocery_CRUD_extended();
		$crud_state = $crud->getState();
		$crud->unset_jquery();
		$crud->set_table($this->db->dbprefix('pages'));
		$crud->set_subject( get_languageword('pages') );
		
		$crud->required_fields('name','description','slug');

		$crud->unique_fields('name');

		$crud->unset_delete();

		//display columns    			
		$crud->columns('name','description','slug','status');
		
		$crud->display_as('name', get_languageword('page_title'));

		if($crud_state == 'list'){
			echo '<style>.fbutton{display: none!important;}</style>'; 
		}

		if($crud_state == 'read'){
			$crud_state ='View';
		}

		

		$crud->callback_before_insert(array($this,'callback_dynapage_before_insert'));
		$crud->callback_before_update(array($this,'callback_dynapage_before_update'));

		$output = $crud->render();

		
		
		// print_r($crud_state);
		// die();

		$this->data['activemenu'] 	= "pages";
		$this->data['activesubmenu'] 	= "dynamic_pages";
		$this->data['pagetitle'] = get_languageword($crud_state).' '. get_languageword('dynamic_pages');
		if($crud_state == "list")
			$this->data['pagetitle'] = get_languageword($crud_state).' '. get_languageword('dynamic_pages').' (<small><code>*'.get_languageword('Please_do_not_delete_first_4_rows_as_they_are_deafult_pages_in_the_system').'</code></small>)';
		$this->data['grocery_output'] = $output;
		$this->data['grocery'] = TRUE;
		$this->grocery_output($this->data);	
	}


	function callback_dynapage_before_insert($post_array) {

		$post_array['slug'] = prepare_slug($post_array['slug'], 'slug', 'pages');

		return $post_array;
	}

	function callback_dynapage_before_update($post_array, $primary_key) {

		$prev_name = $this->base_model->fetch_value('pages', 'slug', array('id' => $primary_key));

		//If updates the name
		if($prev_name != $post_array['slug']) {
			$post_array['slug'] = prepare_slug($post_array['slug'], 'slug', 'pages');
		}
		return $post_array;
	}

	/**
	 * [buyer_bookings description]
	 * @return [type] [description]
	 */
	function buyer_bookings()
	{
		if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin()) {
			$this->prepare_flashmessage(get_languageword('You dont have permission to access this page'), 1);
			redirect('auth/login', 'refresh');
		}


		$this->load->library(array('grocery_CRUD_extended'));
		$crud = new grocery_CRUD_extended();
		$crud_state = $crud->getState();
		$crud->unset_jquery();
		$crud->set_table($this->db->dbprefix('bookings'));
		$crud->set_relation('buyer_id','users','{username} - (Ph: +{phone_code} {phone})');
		$crud->set_relation('seller_id','users','{username} - (Ph: +{phone_code} {phone})');
		$crud->set_relation('book_id','categories','name');
		$crud->set_relation('updated_by',TBL_USERS, 'username');
		$crud->set_subject( get_languageword('buyer_Bookings') );

		$crud->unset_add();
		$crud->unset_delete();


		//display columns
		$crud->columns('booking_id','buyer_id','seller_id','book_id','content','fee','book_duration','start_date','end_date','days_off','preferred_location','admin_commission','admin_commission_val','status');


		$status = array('pending' => get_languageword('pending'), 'approved' => get_languageword('approved'), 'cancelled_before_book_started' => get_languageword('cancelled_before_book_started'), 'cancelled_when_book_running' => get_languageword('cancelled_when_book_running'), 'cancelled_after_book_completed' => get_languageword('cancelled_after_book_completed'), 'session_initiated' => get_languageword('session_initiated'), 'running' => get_languageword('running'), 'completed' => get_languageword('completed'), 'called_for_admin_intervention' => get_languageword('claim_for_admin_intervention'), 'closed' => get_languageword('closed'));

		$crud->field_type('status', 'dropdown', $status);


		$crud->callback_column('book_duration',array($this,'call_back_book_duration'));
		$crud->callback_column('status',array($this,'call_back_status'));

		//Form fields for Edit Record
		$crud->edit_fields('status', 'status_desc', 'updated_at', 'prev_status');

		//Hidden Fields
		$crud->field_type('updated_at', 'hidden', date('Y-m-d H:i:s'));

		$crud->display_as('buyer_id', get_languageword('buyer_name').' - '.get_languageword('Phone_Num'));
		$crud->display_as('seller_id', get_languageword('seller_name').' - '.get_languageword('Phone_Num'));
		$crud->display_as('book_id', get_languageword('book_Booked'));
		$crud->display_as('start_date', get_languageword('batch_start_date'));
		$crud->display_as('end_date', get_languageword('batch_end_date'));
		$crud->display_as('content', get_languageword('book_content'));
		$crud->display_as('fee',get_languageword('fee').' ('.get_languageword('in_credits').')');
		$crud->display_as('admin_commission',get_languageword('admin_commission_percentage'));


		if($crud_state == "edit") {

			$p_key = $this->uri->segment(4);

			$booking_det = $this->base_model->fetch_records_from('bookings', array('booking_id' => $p_key));

			if(!empty($booking_det)) {

				$booking_det = $booking_det[0];

				$booking_status = $booking_det->status;

				$crud->field_type('prev_status', 'hidden', $booking_status);

				if($booking_status == "called_for_admin_intervention") {

					$crud->edit_fields('status', 'status_desc', 'refund_credits_to_buyer', 'tranfer_credits_to_seller', 'updated_at', 'prev_status');

				} 

			} else {

				$this->prepare_flashmessage(get_languageword('invalid_request'), 1);
	    		redirect(URL_ADMIN_BUYER_BOOKINGS);
			}

		}


		if($crud_state == "read") {

			//update notification
			$view_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http")."://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
			update_notification($view_link);


			$crud->field_type('updated_at', 'visibile');
		}

		$crud->callback_update(array($this,'callback_buyer_bookings_update'));

		$output = $crud->render();

		$this->data['activemenu'] 	= "bookings";
		$this->data['activesubmenu'] 	= "buyer_bookings";
		$this->data['pagetitle'] = get_languageword('buyer Bookings');
		$this->data['grocery_output'] = $output;
		$this->data['grocery'] = TRUE;
		$this->grocery_output($this->data);	
	}


	function callback_buyer_bookings_update($post_array, $primary_key)
	{
		$post_array['updated_by'] = $this->ion_auth->get_user_id();

		if(!empty($post_array['refund_credits_to_buyer']))
			$no_of_credits_for_buyer = $post_array['refund_credits_to_buyer'];
		if(!empty($post_array['tranfer_credits_to_seller']))
			$no_of_credits_for_seller   = $post_array['tranfer_credits_to_seller'];

		$booking_det = $this->base_model->fetch_records_from('bookings', array('booking_id' => $primary_key));

		if(empty($booking_det))
			return FALSE;

		$booking_det = $booking_det[0];

		if($post_array['prev_status'] == "called_for_admin_intervention" && !empty($no_of_credits_for_seller)) {
			$post_array['fee'] 					= $no_of_credits_for_seller;
			$post_array['admin_commission']		= $booking_det->admin_commission;
			$post_array['admin_commission_val'] = round($no_of_credits_for_seller * ($post_array['admin_commission'] / 100));
		}


		unset($post_array['refund_credits_to_buyer']);
		unset($post_array['tranfer_credits_to_seller']);

		if($this->base_model->update_operation($post_array, 'bookings', array('booking_id' => $primary_key))) {

			if($post_array['prev_status'] == "called_for_admin_intervention") {

				$buyer_rec = getUserRec($booking_det->buyer_id);
				$seller_rec 	 = getUserRec($booking_det->seller_id);

				if(!empty($no_of_credits_for_buyer)) {

					//Log Credits transaction data & update Buyer net credits - Start
					$log_data = array(
									'user_id' => $booking_det->buyer_id,
									'credits' => $no_of_credits_for_buyer,
									'per_credit_value' => $booking_det->per_credit_value,
									'action'  => 'credited',
									'purpose' => 'Credits refunded by Admin for the booking id "'.$primary_key.'" ',
									'date_of_action	' => date('Y-m-d H:i:s'),
									'reference_table' => 'bookings',
									'reference_id' => $primary_key,
								);

					log_user_credits_transaction($log_data);

					update_user_credits($booking_det->buyer_id, $no_of_credits_for_buyer, 'credit');
					//Log Credits transaction data & update Buyer net credits - End
				}

				if(!empty($no_of_credits_for_seller)) {

					//Log Credits transaction data & update Seller net credits - Start
					$log_data = array(
									'user_id' => $booking_det->seller_id,
									'credits' => $no_of_credits_for_seller,
									'per_credit_value' => $booking_det->per_credit_value,
									'action'  => 'credited',
									'purpose' => 'Credits refunded by Admin for the booking id "'.$primary_key.'" ',
									'date_of_action	' => date('Y-m-d H:i:s'),
									'reference_table' => 'bookings',
									'reference_id' => $primary_key,
								);

					log_user_credits_transaction($log_data);

					update_user_credits($booking_det->seller_id, $no_of_credits_for_seller, 'credit');
					//Log Credits transaction data & update Seller net credits - End
				}

			}

			return TRUE;

		} else return FALSE;
	}


	function call_back_book_duration($primarykey, $row)
	{
		return $row->duration_value .' '. $row->duration_type;
	}

	function call_back_status($primarykey, $row)
	{
		if($row->status == "called_for_admin_intervention")
			return '<font color="red">'.humanize($row->status).'</font>';
		else
			return humanize($row->status);
	}

	/**
	 * [inst_batches description]
	 * @return [type] [description]
	 */
	function inst_batches()
	{
		if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin()) {
			$this->prepare_flashmessage(get_languageword('You dont have permission to access this page'), 1);
			redirect('auth/login', 'refresh');
		}


		$this->load->library(array('grocery_CRUD_extended'));
		$crud = new grocery_CRUD_extended();
		$crud_state = $crud->getState();
		$crud->unset_jquery();
		$crud->set_table($this->db->dbprefix('inst_batches'));
		$crud->set_relation('inst_id','users','username');
		$crud->set_relation('seller_id','users','username');
		$crud->set_relation('book_id','categories','name');

		$crud->set_subject( get_languageword('inst_batches') );

		$crud->unset_add();
		$crud->unset_delete();
		$crud->unset_edit();
		$crud->unset_read();


		//display columns
		$crud->columns('batch_id','batch_code','batch_name','inst_id','book_id','seller_id','total_enrolled_buyers','batch_max_strength','book_content','fee','book_duration','batch_start_date','batch_end_date','time_slot','days_off','book_offering_location');

		$crud->display_as('inst_id', get_languageword('institute_Name'));
		$crud->display_as('seller_id', get_languageword('assigned_Seller'));
		$crud->display_as('book_id', get_languageword('book_Name'));

		$crud->callback_column('total_enrolled_buyers',array($this,'callback_batch_enrolled_buyers_cnt'));
		$crud->callback_column('book_duration',array($this,'callback_column_book_duration'));
		$crud->callback_column('batch_max_strength',array($this,'call_back_batch_strength_color'));

		$crud->add_action(get_languageword('view_Enrolled_Buyers'), URL_FRONT_IMAGES.'magnifier-grocery.png', URL_ADMIN_INST_BATCH_ENROLLED_BUYERS.'/');


		if($crud_state == "list") {
			//update notification
			$view_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http")."://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
			update_notification($view_link);
		}

		$output = $crud->render();

		$this->data['activemenu'] 	= "bookings";
		$this->data['activesubmenu'] 	= "inst_batches";
		$this->data['pagetitle'] = get_languageword('Institute Batches');
		$this->data['grocery_output'] = $output;
		$this->data['grocery'] = TRUE;
		$this->grocery_output($this->data);	
	}


	function callback_batch_enrolled_buyers_cnt($primary_key, $row)
	{
		$batch_id = $row->batch_id;
		$this->load->model('institute/institute_model');
		$total_enrolled_buyers = $this->institute_model->get_batch_enrolled_buyers_cnt($batch_id);
		return '<font color="red">'.$total_enrolled_buyers.'</font>';
	}

	function callback_column_book_duration($prinmarykey, $row)
	{
		return $row->duration_value .' '. $row->duration_type;
	}

	function call_back_batch_strength_color($primarykey, $row)
	{
		return '<font color="green">'.$row->batch_max_strength.'</font>';
	}

	/**
	 * [inst_batche_enrolled_buyers description]
	 * @param  string $batch_id [description]
	 * @return [type]           [description]
	 */
	function inst_batche_enrolled_buyers($batch_id = "")
	{
		if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin()) {
			$this->prepare_flashmessage(get_languageword('You dont have permission to access this page'), 1);
			redirect('auth/login', 'refresh');
		}
		

		$this->load->library(array('grocery_CRUD_extended'));
		$crud = new grocery_CRUD_extended();
		$crud_state = $crud->getState();
		$crud->unset_jquery();
		$crud->set_table($this->db->dbprefix('inst_enrolled_buyers'));
		$crud->where('batch_id',$batch_id);
		$crud->set_relation('inst_id','users','{username} - (Ph: +{phone_code} {phone})');
		$crud->set_relation('buyer_id','users','{username} - (Ph: +{phone_code} {phone})');
		$crud->set_relation('seller_id','users','{username} - (Ph: +{phone_code} {phone})');
		$crud->set_relation('book_id','categories','name');
		
		$crud->set_subject( get_languageword('buyer_enrollment_details') );
		
		$crud->unset_add();
		$crud->unset_delete();

		//display columns
		$crud->columns('batch_id','book_id','batch_name','batch_code','buyer_id','seller_id','inst_id','time_slot','batch_start_date','batch_end_date','book_duration','fee','admin_commission','admin_commission_val','status');

		$crud->display_as('inst_id', get_languageword('institute_name'));
		$crud->display_as('buyer_id', get_languageword('buyer_name'));
		$crud->display_as('seller_id', get_languageword('seller_name'));
		$crud->display_as('book_id', get_languageword('book_name'));
		$crud->display_as('fee',get_languageword('fee').' ('.get_languageword('in_credits').')');
		$crud->display_as('admin_commission',get_languageword('admin_commission_percentage').' ('.get_languageword('in_credits').')');

		$crud->callback_column('book_duration',array($this,'call_back_book_duration'));
		$crud->callback_column('status',array($this,'call_back_status'));

		//Form fields for Edit Record
		$crud->edit_fields('status', 'status_desc', 'updated_at', 'prev_status');

		$status = array('pending' => get_languageword('pending'), 'approved' => get_languageword('approved'), 'cancelled_before_book_started' => get_languageword('cancelled_before_book_started'), 'session_initiated' => get_languageword('session_initiated'), 'running' => get_languageword('running'), 'completed' => get_languageword('completed'), 'called_for_admin_intervention' => get_languageword('claim_for_admin_intervention'), 'closed' => get_languageword('closed'));

		$crud->field_type('status', 'dropdown', $status);

		//Hidden Fields
		$crud->field_type('updated_at', 'hidden', date('Y-m-d H:i:s'));

		if($crud_state == "edit") {

			$p_key = $this->uri->segment(5);

			$enroll_det = $this->base_model->fetch_records_from('inst_enrolled_buyers', array('enroll_id' => $p_key));

			if(!empty($enroll_det)) {

				$enroll_det = $enroll_det[0];

				$enroll_status = $enroll_det->status;

				$crud->field_type('prev_status', 'hidden', $enroll_status);

				if($enroll_status == "called_for_admin_intervention") {

					$crud->edit_fields('status', 'status_desc', 'refund_credits_to_buyer', 'tranfer_credits_to_institute', 'updated_at', 'prev_status');

				} 

			} else {

				$this->prepare_flashmessage(get_languageword('invalid_request'), 1);
	    		redirect(URL_ADMIN_INST_BATCH_ENROLLED_BUYERS.'/'.$batch_id);
			}

		}

		
		if($crud_state == "read") {

			$state = $this->uri->segment(4);
			$enroll_id = $this->uri->segment(5);

			if ($state=="read" && $enroll_id>0) {
				//update notification
				$view_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http")."://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
					
				update_notification($view_link);
			}

			$crud->field_type('updated_at', 'visibile');
			$crud->set_relation('updated_by','users', 'username');
		}

		$crud->callback_update(array($this,'callback_buyer_enrollment_update'));


		
		$output = $crud->render();
		
		$this->data['activemenu'] 	= "bookings";
		$this->data['activesubmenu'] 	= "inst_enrolled_buyers";
		$this->data['maintitle_link'] = base_url().'admin/inst-batches/2';
		$this->data['pagetitle'] = get_languageword('enrolled_buyers');
		$this->data['grocery_output'] = $output;
		$this->data['grocery'] = TRUE;
		$this->grocery_output($this->data);	
	}


	function callback_buyer_enrollment_update($post_array, $primary_key)
	{
		$post_array['updated_by'] = $this->ion_auth->get_user_id();

		if(!empty($post_array['refund_credits_to_buyer']))
			$no_of_credits_for_buyer = $post_array['refund_credits_to_buyer'];
		if(!empty($post_array['tranfer_credits_to_institute']))
			$no_of_credits_for_inst   = $post_array['tranfer_credits_to_institute'];

		$enroll_det = $this->base_model->fetch_records_from('inst_enrolled_buyers', array('enroll_id' => $primary_key));

		if(empty($enroll_det))
			return FALSE;

		$enroll_det = $enroll_det[0];

		if($post_array['prev_status'] == "called_for_admin_intervention" && !empty($no_of_credits_for_inst)) {
			$post_array['fee'] 	= ($enroll_det->admin_commission_val)+($no_of_credits_for_inst);
		}


		unset($post_array['refund_credits_to_buyer']);
		unset($post_array['tranfer_credits_to_institute']);

		if($this->base_model->update_operation($post_array, 'inst_enrolled_buyers', array('enroll_id' => $primary_key))) {

			if($post_array['prev_status'] == "called_for_admin_intervention") {

				$buyer_rec = getUserRec($enroll_det->buyer_id);
				$inst_rec 	 = getUserRec($enroll_det->inst_id);

				if(!empty($no_of_credits_for_buyer)) {

					//Log Credits transaction data & update Buyer net credits - Start
					$log_data = array(
									'user_id' => $enroll_det->buyer_id,
									'credits' => $no_of_credits_for_buyer,
									'per_credit_value' => $enroll_det->per_credit_value,
									'action'  => 'credited',
									'purpose' => 'Credits refunded by Admin for the enroll id "'.$primary_key.'" ',
									'date_of_action	' => date('Y-m-d H:i:s'),
									'reference_table' => 'inst_enrolled_buyers',
									'reference_id' => $primary_key,
								);

					log_user_credits_transaction($log_data);

					update_user_credits($enroll_det->buyer_id, $no_of_credits_for_buyer, 'credit');
					//Log Credits transaction data & update Buyer net credits - End
				}

				if(!empty($no_of_credits_for_inst)) {

					//Log Credits transaction data & update Institute net credits - Start
					$credits_to_be_debted = ($enroll_det->fee-$enroll_det->admin_commission_val)-($no_of_credits_for_inst);
					$log_data = array(
									'user_id' => $enroll_det->inst_id,
									'credits' => $credits_to_be_debted,
									'per_credit_value' => $enroll_det->per_credit_value,
									'action'  => 'debited',
									'purpose' => 'Credits debited by Admin for the enroll id "'.$primary_key.'" as Buyer claimed for Admin intervention',
									'date_of_action	' => date('Y-m-d H:i:s'),
									'reference_table' => 'inst_enrolled_buyers',
									'reference_id' => $primary_key,
								);

					log_user_credits_transaction($log_data);

					update_user_credits($enroll_det->inst_id, $credits_to_be_debted, 'debit');
					//Log Credits transaction data & update Institute net credits - End
				}

			}

			return TRUE;

		} else return FALSE;
	}


	/**
	 * [view_certificates description]
	 * @param  string $id [description]
	 * @return [type]     [description]
	 */
	function view_certificates($id="")
 	{
	 	if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin()) {
				$this->prepare_flashmessage(get_languageword('You dont have permission to access this page'), 1);
				redirect('auth/login', 'refresh');
			}


			$username = getUserRec($id)->username;

			$this->load->library(array('grocery_CRUD_extended'));
			$crud = new grocery_CRUD_extended();
			$crud_state = $crud->getState();
			$crud->unset_jquery();
			$crud->set_table($this->db->dbprefix('users_certificates'));
			$crud->where('user_id',$id);

			$crud->set_relation('admin_certificate_id','certificates','title');

		
			$crud->unset_add();
			$crud->unset_delete();
			$crud->unset_edit();
			$crud->unset_read();
			
			
			//display columns    			
			$crud->columns('admin_certificate_id','certificate_name');

			$crud->callback_column('certificate_name',array($this,'showFile'));

			$crud->display_as('admin_certificate_id',get_languageword('certificate_type'));

			$output = $crud->render();

			$this->data['activemenu'] 	= "users";
			$this->data['pagetitle'] = get_languageword('certificates_of').' "'.$username.'"';
			$this->data['grocery_output'] = $output;
			$this->data['grocery'] = TRUE;
			$this->grocery_output($this->data);	
	}

	function showFile($row) {  
	   return "<a href='".URL_PUBLIC_UPLOADS_CERTIFICATES . $row."' target='_blank'>".$row." </a> ";
	}


	//Seller money conversion requests
	function seller_money_conversion_requests($param = "Pending")
	{
		if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin()) {
			$this->prepare_flashmessage(get_languageword('You dont have permission to access this page'), 1);
			redirect('auth/login', 'refresh');
		}

		$this->load->library(array('grocery_CRUD_extended'));
		$crud = new grocery_CRUD_extended();
		$crud_state = $crud->getState();
		$crud->unset_jquery();
		$crud->set_table($this->db->dbprefix('admin_money_transactions'));
		$crud->where('user_type','seller');
		if(!empty($param))
			$crud->where('status_of_payment',$param);

		//unset actions	
		$crud->unset_add();
		$crud->unset_delete();


		//display columns
		$crud->columns('user_id','user_name','user_paypal_email','no_of_credits_to_be_converted', 'per_credit_cost', 'total_amount','status_of_payment');

		$crud->required_fields('status_of_payment');

		$currency_symbol = $this->config->item('site_settings')->currency_symbol;
		$crud->display_as('per_credit_cost', get_languageword('per_credit_cost')." (".$currency_symbol.")");
		$crud->display_as('total_amount', get_languageword('total_amount')." (".$currency_symbol.")");

		//edit fields
		$crud->edit_fields('status_of_payment', 'transaction_details', 'updated_at', 'updated_by');

		$pmt_status = array();
		if($param == "Pending")
			$pmt_status = array('Done' => get_languageword('Done'));
		else if($param == "Done")
			$pmt_status = array('Pending' => get_languageword('Pending'));
		$crud->field_type('status_of_payment', 'dropdown', $pmt_status);


		$crud->field_type('updated_at', 'hidden',date('Y-m-d H:i:s'));
		$crud->field_type('updated_by', 'hidden',$this->ion_auth->get_user_id());

		// $crud->callback_column('booking_id',array($this,'_call_back_column_booking_id'));
		$crud->callback_column('user_name',array($this,'_call_back_column_user_name'));

		$crud->callback_after_update(array($this, 'callback_log_user_credits'));

		if ($crud_state == "read") {

			//update notification
			$view_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http")."://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
			update_notification($view_link);

			$crud->field_type('updated_at', 'visibile');
		}

		$output = $crud->render();

		$this->data['activemenu'] 	= "seller_money_reqs";
		$this->data['activesubmenu'] 	= "seller_".$param;
		$this->data['pagetitle'] = get_languageword('money_conversion_requests_from_seller')." - ".get_languageword($param);
		$this->data['grocery_output'] = $output;
		$this->data['grocery'] = TRUE;
		$this->grocery_output($this->data);	

	}


	function _call_back_column_booking_id($primarykey, $row) {  
		return "<a href=".URL_ADMIN_BUYER_BOOKINGS."/".$crud_state='read'."/".$row->booking_id.">".$row->booking_id."</a>";
	}

	function _call_back_column_user_name($primarykey, $row) {  
		return "<a href=".URL_AUTH_INDEX."/".$crud_state='read'."/".$row->user_id.">".$row->user_name."</a>";
	}


	function callback_log_user_credits($post_array, $primary_key)
	{

		$req_det = $this->base_model->fetch_records_from('admin_money_transactions', array('id' => $primary_key));

		if(!empty($req_det)) {

			if($post_array['status_of_payment'] == "Done") {

				$action  = "debited";
				$action1 = "debit";

			} else if($post_array['status_of_payment'] == "Pending") {

				$action  = "credited";
				$action1 = "credit";
			}

			$req_det = $req_det[0];
			//Log Credits transaction data & update user net credits - Start
			$log_data = array(
							'user_id' => $req_det->user_id,
							'credits' => $req_det->no_of_credits_to_be_converted,
							'per_credit_value' => $req_det->per_credit_cost,
							'action'  => $action,
							'purpose' => 'Withdrawal (Credits to Money)',
							'date_of_action	' => date('Y-m-d H:i:s'),
							'reference_table' => 'admin_money_transactions',
							'reference_id' => $primary_key,
						);

			log_user_credits_transaction($log_data);

			update_user_credits($req_det->user_id, $req_det->no_of_credits_to_be_converted, $action1);
			//Log Credits transaction data & update user net credits - End

			return TRUE;

		} else return FALSE;
	}
	

	//Institute money conversion requests
	function inst_money_conversion_requests($param = "Pending")
	{
		if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin()) {
			$this->prepare_flashmessage(get_languageword('You dont have permission to access this page'), 1);
			redirect('auth/login', 'refresh');
		}

		$this->load->library(array('grocery_CRUD_extended'));
		$crud = new grocery_CRUD_extended();
		$crud_state = $crud->getState();
		$crud->unset_jquery();
		$crud->set_table($this->db->dbprefix('admin_money_transactions'));
		$crud->where('user_type','institute');
		if(!empty($param))
			$crud->where('status_of_payment',$param);

		//unset actions			
		$crud->unset_add();
		$crud->unset_delete();


		//display columns    			
		$crud->columns('user_id','booking_id','user_name','user_paypal_email','no_of_credits_to_be_converted', 'per_credit_cost', 'total_amount','user_bank_ac_details','status_of_payment');

		$crud->required_fields('status_of_payment');

		$currency_symbol = $this->config->item('site_settings')->currency_symbol;
		$crud->display_as('per_credit_cost', get_languageword('per_credit_cost')." (".$currency_symbol.")");
		$crud->display_as('total_amount', get_languageword('total_amount')." (".$currency_symbol.")");

		//edit fields
		$crud->edit_fields('status_of_payment', 'transaction_details', 'updated_at', 'updated_by');

		$pmt_status = array();
		if($param == "Pending")
			$pmt_status = array('Done' => get_languageword('Done'));
		else if($param == "Done")
			$pmt_status = array('Pending' => get_languageword('Pending'));
		$crud->field_type('status_of_payment', 'dropdown', $pmt_status);

		$crud->field_type('updated_at', 'hidden',date('Y-m-d H:i:s'));
		$crud->field_type('updated_by', 'hidden',$this->ion_auth->get_user_id());

		$crud->callback_column('booking_id',array($this,'_call_back_column_batch_id'));
		$crud->callback_column('user_name',array($this,'_call_back_column_user_name'));

		$crud->callback_after_update(array($this, 'callback_log_user_credits'));

		if($crud_state == "read") {

			$crud->field_type('updated_at', 'visibile');
		}

		$output = $crud->render();

		$this->data['activemenu'] 	= "inst_money_reqs";
		$this->data['activesubmenu'] 	= "inst_".$param;
		$this->data['pagetitle'] = get_languageword('money_conversion_requests_from_isntitute')." - ".get_languageword($param);
		$this->data['grocery_output'] = $output;
		$this->data['grocery'] = TRUE;
		$this->grocery_output($this->data);	

	}

	function _call_back_column_batch_id($primarykey, $row) {  
		return "<a href='".URL_ADMIN_INST_BATCH_ENROLLED_BUYERS."/".$row->booking_id."'>".$row->booking_id."</a>";
	}



	//view inst-sellers
	function view_inst_sellers($id="")
 	{
	 	if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin()) {
				$this->prepare_flashmessage(get_languageword('You dont have permission to access this page'), 1);
				redirect('auth/login', 'refresh');
			}

			$user_name = getUserRec($id)->username;
			$this->load->library(array('grocery_CRUD_extended'));
			$crud = new grocery_CRUD_extended();
			$crud_state = $crud->getState();
			$crud->unset_jquery();
			$crud->set_table($this->db->dbprefix('users'));
			$crud->where('parent_id',$id);
					
			$crud->unset_add();
			$crud->unset_delete();
			$crud->unset_edit();
			$crud->unset_read();

			//display columns    			
			$crud->columns('email','first_name','last_name','active');

			$crud->display_as('admin_approved', get_languageword('is_approved'));

			$output = $crud->render();

			$this->data['activemenu'] 	= "users";
			$this->data['pagetitle'] = get_languageword('institute_sellers_of').' "'.$user_name.'"';
			$this->data['grocery_output'] = $output;
			$this->data['grocery'] = TRUE;
			$this->grocery_output($this->data);	
	}

	/**
	 * [scroll_news description]
	 * @return [type] [description]
	 */
	function scroll_news()
	{
		if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin()) {
				$this->prepare_flashmessage(get_languageword('You dont have permission to access this page'), 1);
				redirect('auth/login', 'refresh');
			}

			
			$this->load->library(array('grocery_CRUD_extended'));
			$crud = new grocery_CRUD_extended();
			$crud_state = $crud->getState();
			$crud->unset_jquery();
			$crud->set_table($this->db->dbprefix('scroll_news'));		
			
			//display columns    			
			$crud->columns('title','url','status');
			$crud->callback_add_field('url',array($this,'call_back_url_format'));	
			$crud->required_fields('title','url');
			$output = $crud->render();

			if($crud_state == 'read')
			$crud_state ='View';

			$this->data['activemenu'] 	= "pages";
			$this->data['activesubmenu'] 	= "scroll_news";
			$this->data['pagetitle'] = get_languageword($crud_state) .' '. get_languageword('scroll_news');
			$this->data['grocery_output'] = $output;
			$this->data['grocery'] = TRUE;
			$this->grocery_output($this->data);
	}

	function call_back_url_format()
	{
		return '<input type="text" class="form-control" maxlength="100" name="url" placeholder="http://www.sitename.com">';
	}
	
	/**
	 * [payments description]
	 * @param  [type] $param [description]
	 * @return [type]        [description]
	 */
	function payments( $param = NULL )
	{
		if(!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin()){
			$this->prepare_flashmessage(get_languageword('You do not have permission to access this page'),1);
			redirect('auth/login','refresh');
		}
		
		$this->data['message'] = $this->session->flashdata('message');

		$user_id = $this->ion_auth->get_user_id();
		$this->load->library(array('grocery_CRUD'));
		$crud = new grocery_CRUD();
		$crud_state = $crud->getState();
		$crud->set_table($this->db->dbprefix('subscriptions'));
		if ( $param != NULL && $param == 'pending' ) {
			$crud->where('payment_received', 0);
		}
		$crud->set_subject( get_languageword('subscriptions') );
		
		$crud->unset_add();
		$crud->unset_delete();
		$crud->unset_edit();
		$crud->unset_read();
		
		
		$crud->columns('subscribe_date', 'user_name', 'user_type', 'package_name','transaction_no', 'payment_type','credits','amount_paid');

		$crud->callback_column('subscribe_date',array($this,'callback_subscribe_date'));
		$crud->callback_column('amount_paid',array($this,'callback_amount_paid'));

		$crud->display_as('subscribe_date', get_languageword("purchase_date"));
		

		if ($crud_state=='list')
		{
			//update notification
			$view_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http")."://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
			update_notification($view_link);
		}

		$output = $crud->render();
		
		$this->data['activemenu'] 	= "payments";
		$this->data['activesubmenu'] ="payments";
		$this->data['content'] 		= 'payments';
		$this->data['pagetitle'] = get_languageword('payments');
		$this->data['grocery_output'] = $output;
		$this->data['grocery'] = TRUE;
		$this->grocery_output($this->data);
	}
	
	function callback_subscribe_date($value, $row)
	{
		return date('d/m/Y', strtotime($value));
	}
	function callback_amount_paid( $value, $row ) {
		if ( $row->payment_received == 1 ) {
			$value .= '&nbsp;<img src="'.URL_FRONT_IMAGES . 'checked.png">';
		} else {
			if ( $row->payment_type == 'manual' ) {
				
				$value .= '&nbsp;<a class="btn btn-info btn-xs" href="' . site_url( 'admin/manual_payment_status/' . $row->id ) . '" title="Update Payment Received">Update</a>';
			} else {
				$value .= '&nbsp;<img src="'.URL_FRONT_IMAGES . 'error.png">';
			}
		}
		return $value;
	}
	
	/**
	 * [manual_payment_status description]
	 * @param  [type] $payment_id [description]
	 * @return [type]             [description]
	 */
	function manual_payment_status( $payment_id ) {

		$this->data['message'] = $this->session->flashdata('message');

		if ( ! empty( $payment_id ) ) {

			$check = $this->db->query( 'SELECT * FROM `'.$this->db->dbprefix('subscriptions').'` s INNER JOIN `'.$this->db->dbprefix('users').'` u ON s.user_id = u.id AND s.payment_received = 0 WHERE s.id = ' . $payment_id )->result();
			if ( empty( $check ) ) {
				safe_redirect( $this->ion_auth->get_user_id() );
			} else {

				if(isset($_POST['submitbutt']))
				{
					$this->form_validation->set_rules('payment_updated_admin_message', get_languageword('Enter you comments'), 'trim|required|xss_clean');
					$this->form_validation->set_rules('is_received', get_languageword('Payment Received?'), 'trim|required|xss_clean');
					if ( $this->input->post('is_received') == 'yes' ) {
						$this->form_validation->set_rules('transaction_no', get_languageword('Reference No'), 'trim|required|xss_clean');
					}
					$this->form_validation->set_error_delimiters('<div class="error">', '</div>');			
					if ($this->form_validation->run() == TRUE)
					{
						$inputdata = array();						
						if ( $this->input->post('is_received') == 'yes' ) {
							$inputdata['payment_updated_admin'] = 'settled';
							$inputdata['payment_received'] = '1';
							$inputdata['transaction_no'] = $this->input->post('transaction_no');
						} else {
							$inputdata['payment_updated_admin'] = 'yes';
						}
						$inputdata['payment_updated_admin_time'] = date('Y-m-d H:i:s');
						$inputdata['payment_updated_admin_message'] = $this->input->post('payment_updated_admin_message');

						$inputdata['amount_paid']	 = $this->input->post('amount_paid');

						$this->base_model->update_operation($inputdata, 'subscriptions', array('id' => $payment_id));
						
						if( $this->input->post('is_received') == 'yes' ) {
							$user_id =  $check[0]->user_id;
							$package_details 	= $this->db->get_where('packages',array('id' => $check[0]->package_id))->result();
							$subscription_details 	= $package_details[0];
							$user_data['subscription_id'] 		= $payment_id;
							$this->base_model->update_operation($user_data, 'users', array('id' => $user_id));

							// Log Credits transaction data & update user net credits - Start
							$log_data = array(
								'user_id' => $user_id,
								'credits' => $subscription_details->credits,
								'per_credit_value' => get_system_settings('per_credit_value'),
								'action'  => 'credited',
								'purpose' => 'Package "'.$subscription_details->package_name.'" subscription',
								'date_of_action	' => date('Y-m-d H:i:s'),
								'reference_table' => 'subscriptions',
								'reference_id' => $payment_id,
							);
							log_user_credits_transaction($log_data);
							update_user_credits($user_id, $subscription_details->credits, 'credit');
							// Log Credits transaction data & update user net credits - End
							

							//send email to user
							$this->subscription_email($payment_id);

						}
						$this->prepare_flashmessage(get_languageword('record updated successfully'), 0);
						redirect('admin/payments');	
					}
					$this->data['message'] = $this->prepare_message(validation_errors(), 1);
				}

				$this->data['activemenu'] 	 = "packages";
				$this->data['activesubmenu'] = "mysubscriptions";
				$this->data['content'] 		 = 'manual_payment_status';
				$this->data['pagetitle'] 	 = get_languageword('manual_payment_status');
				$this->data['profile'] = $check[0];
				$this->_render_page('template/admin/admin-template', $this->data);
			}
		} else {
			$this->safe_redirect( site_url( 'admin/payments' ), 'Wrong operation' );
		}
	}

	/**
	 * [plant_tree description]
	 * @return [type]             [description]
	 */
	function plant_tree() {
		if (isset($_GET['pid']) && !empty($_GET['pid'])) {
			$purchaseRecords = $this->base_model->fetch_records_from('book_purchases', array('purchase_id'=>$_GET['pid']));

			

			if(count($purchaseRecords) > 0) {
				$purRec        = $purchaseRecords[0];

				$buyer_details = getUserRec($purRec->user_id);
				
				if ($buyer_details->first_name == '') {
					$buyer_name = $buyer_details->username;
				}else{
					$buyer_name = $buyer_details->first_name.' '.$buyer_details->last_name;
				}

				if($purRec->api_val > 0 && $purRec->moretrees_success == '0' && !empty($buyer_details)) {
					$this->load->library('moretreesapi');
					$plantRes = $this->moretreesapi->plantATree([
						'first_name' => $buyer_name,
						'email' => $buyer_details->email
					]);
					if(isset($plantRes['result']) && !empty($plantRes['result']) && isset($plantRes['result']['status']) && $plantRes['result']['status'] == 1  && isset($plantRes['result']['response']) && $plantRes['result']['response'] == 'successful') {
						$certUrl = (isset($plantRes['result']['data']['certificates']) && count($plantRes['result']['data']['certificates']) > 0) ? $plantRes['result']['data']['certificates'][0]['certificateURL'] : '';
						$this->base_model->update_operation(array('moretrees_success' => '1', 'moretrees_cert_url' => $certUrl), 'book_purchases', array('purchase_id' => $_GET['pid']));
						$this->prepare_flashmessage(get_languageword('record updated successfully'), 0);
						redirect('admin/view-purchased-books/edit/'.$_GET['pid']);
					} else {
						$errMsg = (isset($plantRes['result']['errors']) && isset($plantRes['result']['errors'][0])) ? $plantRes['result']['errors'][0]['msg'] : 'Moretrees API returned an error, please try again later';
						$this->safe_redirect( site_url( 'admin/view-purchased-books/edit/'.$_GET['pid'] ), $errMsg );
						exit;
					}
				} else {
					$this->safe_redirect( site_url( 'admin/view-purchased-books/edit/'.$_GET['pid'] ), 'Something went wrong, please try again later' );
				}
			} else {
				$this->safe_redirect( site_url( 'admin/view-purchased-books/edit/'.$_GET['pid'] ), 'Invalid purchase ID' );
			}
		} else {
			$this->safe_redirect( site_url( 'admin/view-purchased-books' ), 'Invalid purchase ID' );
		}
	}


	/**
	 * [view_purchased_books description]
	 * @return [type] [description]
	 */
	function view_purchased_books()
	{
		if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin()) {
			$this->prepare_flashmessage(get_languageword('You dont have permission to access this page'), 1);
			redirect('auth/login', 'refresh');
		}


		$this->load->library(array('grocery_CRUD'));
		$crud = new grocery_CRUD();
		$crud->unset_jquery();
		$crud_state = $crud->getState();
		$crud->set_table(TBL_PREFIX.'book_purchases');
		$crud->set_relation('sc_id',TBL_PREFIX.'seller_selling_books','book_title');
		$crud->set_relation('seller_id',TBL_PREFIX.'users','username');
		$crud->set_relation('user_id',TBL_PREFIX.'users','username');
		// $crud->where('payment_status', 'Completed');

		$where = "total_amount > 0  AND payment_status = 'Completed' ";
		$crud->where($where);
		// $crud->order_by('id','desc');

		// if($crud_state == 'list' || $crud_state == 'success'){
		// 	echo '<style>.crud-action{display: none!important;}</style>'; 
		// }

		$crud->set_subject( get_languageword('Purchased_Books') );

		$crud->unset_add();
		$crud->unset_delete();
		$crud->unset_read();

		// $crud->columns('sc_id','seller_id','user_id','total_amount','admin_commission_val','paid_date', 'status_of_payment_to_seller','paid_to_seller','fee');

		$crud->columns('purchase_id','transaction_id','paid_date','sc_id','seller_id','user_id','total_amount','api_val', 'tree_planted','item_price','admin_commission_val','payable','status_of_payment_to_seller');

		if($crud_state == 'edit'){

			echo '<style>input[name=payer_email]{pointer-events: none;}#field-payer_email{background: #f5f5f5;}</style>';

			echo '<style>input[name=payment_status]{pointer-events: none;}#field-payment_status{background: #f5f5f5;}</style>'; 

			echo '<style>input[name=transaction_id]{pointer-events: none;}#field-transaction_id{background: #f5f5f5;}</style>'; 
			
			echo '<style>input[name=total_seller_due]{pointer-events: none;}#field-total_seller_due{background: #f5f5f5;}</style>'; 

		}

		$crud->edit_fields('payer_email','payment_status','transaction_id' );

		$crud->callback_column('payable', function ($value, $row) {
			$payable = (float)$row->item_price - (float)$row->admin_commission_val - (float)$row->fee;
			return round($payable, 2);
		});

		$crud->callback_column('due', function ($value, $row) {
			$due_amt = (float)$row->item_price - (float)$row->admin_commission_val - (float)$row->fee - (float)$row->paid_to_seller;
			$due_amt = round($due_amt, 2);
			return $due_amt;
		});

		$crud->callback_column('tree_planted', function ($value, $row) {
			return ($row->moretrees_success == '1') ? 'Yes' : 'No';
		});

		$base_model = $this->base_model;

		$crud->callback_edit_field('total_seller_due', function($fieldValue, $primaryKeyValue) use ($base_model) {
			$purchaseRecords = $base_model->fetch_records_from('book_purchases', array('purchase_id'=>$primaryKeyValue));
			$due_amt = $fieldValue;
			$item_price = '';
			$admin_commission_val = '';
			if(!empty($purchaseRecords)) {
				$record = $purchaseRecords[0];
				$due_amt = (float)$record->item_price - (float)$record->admin_commission_val - (float)$record->fee - (float)$record->paid_to_seller;
				$item_price = (float)$record->item_price;
				$admin_commission_val = (float)$record->admin_commission_val;
				$due_amt = round($due_amt, 2);
				$plantTreeLink = '';
				if($record->api_val > 0 && $record->moretrees_success == '0') {
					$plantTreeLink = base_url('admin/plant-tree?pid='.$record->purchase_id);
				}
			}
			return '
				<input type="hidden" id="plant_tree_link" value="'.$plantTreeLink.'">
				<input type="hidden" id="item_price" value="'.$item_price.'">
				<input type="hidden" id="admin_commission_val" value="'.$admin_commission_val.'">
				<input id="field-total_seller_due" class="form-control" name="total_seller_due" type="text" value="'.$due_amt.'" maxlength="12">
			';
		});

		$crud->display_as('sc_id', get_languageword('Book_Title'));
		$crud->display_as('seller_id', get_languageword('seller_name'));
		$crud->display_as('user_id', get_languageword('buyer_name'));
		$crud->display_as('paid_date', get_languageword('Purchased_On'));
		$crud->display_as('payment_status', get_languageword('Paypal_Payment_Status'));
		// $crud->display_as('status_of_payment_to_seller', get_languageword('Payment_from_Admin'));
		// $crud->display_as('fee', get_languageword('Trans._Fee'));
		$crud->display_as('api_val', get_languageword('Tree_API'));
		$crud->display_as('paid_to_seller', get_languageword('paid_to_seller'));
		$crud->display_as('purchase_id', get_languageword('no.'));
		$crud->display_as('admin_commission_val', 'Admin + Transaction Fee');
		$crud->display_as('payable', 'Paid');
		

		$crud->add_action(get_languageword('View_Download_History'), URL_FRONT_IMAGES.'magnifier-grocery.png', URL_ADMIN_VIEW_BOOK_DOWNLOAD_HISTORY.'/');


		if ($crud_state=="list") {
			//update notification
			$view_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http")."://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
			update_notification($view_link);

		}

		$output = $crud->render();

		$this->data['activemenu'] 		= "purchased_books";
		$this->data['pagetitle'] 		= get_languageword('Purchased_Books');
		$this->data['grocery_output'] 	= $output;
		$this->data['grocery'] 			= TRUE;
		$this->grocery_output($this->data);
	}

	/**
	 * [view_book_download_history description]
	 * @param  string $purchase_id [description]
	 * @return [type]              [description]
	 */
	function view_book_download_history($purchase_id = "")
	{

		if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin()) {
			$this->prepare_flashmessage(get_languageword('You dont have permission to access this page'), 1);
			redirect('auth/login', 'refresh');
		}


		if(empty($purchase_id)) {

			$this->prepare_flashmessage(get_languageword('invalid_request'), 1);
			redirect(URL_ADMIN_VIEW_PURCHASED_BOOKS);
		}


		$this->load->library(array('grocery_CRUD'));
		$crud = new grocery_CRUD();
		$crud->unset_jquery();
		$crud_state = $crud->getState();
		$crud->set_table(TBL_PREFIX.'book_downloads');
		$crud->set_relation('sc_id',TBL_PREFIX.'seller_selling_books','book_title');
		$crud->set_relation('seller_id',TBL_PREFIX.'users','username');
		$crud->set_relation('user_id',TBL_PREFIX.'users','username');
		$crud->where('purchase_id', $purchase_id);

		$crud->set_subject( get_languageword('Book_Download_History') );

		$crud->unset_add();
		$crud->unset_delete();
		$crud->unset_edit();
		$crud->unset_read();

		$crud->columns('sc_id','seller_id','user_id','ip_address','browser','browser_version', 'platform', 'downloaded_date');

		$crud->display_as('sc_id', get_languageword('Book_Title'));
		$crud->display_as('seller_id', get_languageword('seller_name'));
		$crud->display_as('user_id', get_languageword('buyer_name'));

		$output = $crud->render();

		$this->data['activemenu'] 		= "purchased_books";
		$this->data['pagetitle'] 		= get_languageword('Book_Download_History');
		$this->data['grocery_output'] 	= $output;
		$this->data['grocery'] 			= TRUE;
		$this->grocery_output($this->data);
	}
	
	/**
	 * [view_sellers_blogs description]
	 * @return [type] [description]
	 */
	function view_sellers_blogs()
	{
		if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin()) {
			$this->prepare_flashmessage(get_languageword('You dont have permission to access this page'), 1);
			redirect('auth/login', 'refresh');
		}


		$this->load->library(array('grocery_CRUD'));
		$crud = new grocery_CRUD();
		$crud->unset_jquery();
		$crud_state = $crud->getState();
		$crud->set_table(TBL_PREFIX.'seller_blogs');
		
		$crud->set_relation('seller_id',TBL_PREFIX.'users','username');
		
		$crud->set_subject( get_languageword('Sellers_Blogs') );

		$crud->unset_add();
		
		$crud->columns('seller_id','title','related_to','created','updated','blog_status', 'admin_approved', 'approved_datetime');


		$crud->edit_fields('admin_approved','approved_datetime','blog_status','updated');

		$crud->field_type('created',date('Y-m-d H:i:s'));

		$crud->field_type('approved_datetime','hidden',date('Y-m-d H:i:s'));
		$crud->field_type('updated','hidden',date('Y-m-d H:i:s'));

		$crud->display_as('seller_id', get_languageword('seller_name'));

		
		if ($crud_state == "read") {
			//update notification
			$view_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http")."://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
			update_notification($view_link);
		}
		
		$output = $crud->render();

		$this->data['activemenu'] 		= "sellers_blogs";
		$this->data['pagetitle'] 		= get_languageword('Sellers_Blogs');
		$this->data['grocery_output'] 	= $output;
		$this->data['grocery'] 			= TRUE;
		$this->grocery_output($this->data);

	}

	

	/****************************
	05-12-2018
	*****************************/
	/**
	 * [notifications description]
	 * @return [type] [description]
	 */
	function notifications()
	{	
		if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin()) {
			$this->prepare_flashmessage(get_languageword('You dont have permission to access this page'), 1);
			redirect('auth/login', 'refresh');
		}

		$this->data['message'] 	 = $this->session->flashdata('message');
			
		$this->load->library(array('grocery_CRUD'));
		$crud = new grocery_CRUD();
		$crud->unset_jquery();
		$crud_state = $crud->getState();
		$crud->set_table(TBL_PREFIX.'notifications');
		
		$crud->set_relation('user_id',TBL_PREFIX.'users','username');
		$crud->order_by('notification_id','desc');

		$crud->set_subject( get_languageword('Notifications') );

		$crud->unset_add();
		$crud->unset_edit();
		$crud->unset_delete();
		$crud->unset_read();

		$crud->columns('notification_id','user_id','title','content','datetime','admin_read','admin_read_date', 'page_link');

		

		$crud->display_as('user_id', get_languageword('user'));
		

		$crud->field_type('datetime', 'hidden', date('Y-m-d H:i:s'));
		$crud->field_type('admin_read_date', 'hidden', date('Y-m-d H:i:s'));


		$crud->display_as('admin_read_date', get_languageword('admin_viewed'));



		$crud->display_as('notification_id', get_languageword('id'));

		$crud->callback_column('page_link',array($this,'_callback_webpage_url'));
		$crud->callback_column('admin_read',array($this,'_callback_admin_read'));
		

		$output = $crud->render();

		$this->data['activemenu'] 		= "notifications";
		$this->data['pagetitle'] 		= get_languageword('Notifications');
		$this->data['grocery_output'] 	= $output;
		$this->data['grocery'] 			= TRUE;
		$this->grocery_output($this->data);
	}


	public function _callback_webpage_url($value, $row)
	{
		return "<a class='btn btn-info btn-xs' href='".$row->page_link."' style='color:#fff;' >View</a>";
	}
 

	public function _callback_admin_read($value, $row)
	{
		$val="No";
		if ($row->admin_read==1)
			$val="Yes";
		return $val;
	}


	/****************************************
	Email to User when admin received payment
	*****************************************/
	public function subscription_email($subscription_id)
	{
		//subscription record
		$record = $this->base_model->fetch_records_from(TBL_SUBSCRIPTIONS, array('id'=>$subscription_id));

		if (!empty($record)) {

			$record = $record[0];

			//send mail to user
			$email_tpl = $this->base_model->fetch_records_from('email_templates', array('template_status' => 'Active', 'email_template_id' => '22'));

			if (!empty($email_tpl)) {

				$email_tpl = $email_tpl[0];

				$user 	 = getUserRec($record->user_id);
				$from 	 = $this->config->item('site_settings')->portal_email;
				$to 	 = $user->email;

				if (!empty($email_tpl->template_subject)) {
					$sub = $email_tpl->template_subject;
				} else {
					$sub = get_languageword("Payment Received to Admin");
				}

				if (!empty($email_tpl->template_content)) {

					$logo_img='<img src="'.get_site_logo().'" class="img-responsive" width="120px" height="50px">';

					$site_title = $this->config->item('site_settings')->site_title;

					$currency = $this->config->item('site_settings')->currency_symbol;


					$original_vars  = array($logo_img, $site_title, $user->username, $record->package_name, $currency.$record->package_cost, $record->discount_type, $record->discount_amount, $record->discount_value, $currency.$record->amount_paid, $record->credits, $record->payment_type, $currency.$record->amount_paid, $record->transaction_no);

					$temp_vars		= array('__SITE_LOGO__', '__SITE_TITLE__', '__USER_NAME__', '__PACKAGE_NAME__', '__PACKAGE_COST__', '__DISCOUNT_TYPE__', '__DISCOUNT_AMOUNT__', '__DISCOUNT_VALUE__', '__AFTER_DISCOUNT__', '__CREDITS__', '__PAYMENT_TYPE__', '__AMOUNT_PAID__', '__TRANSACTION_NO__');


					$msg = str_replace($temp_vars, $original_vars, $email_tpl->template_content);

					sendEmail($from, $to, $sub, $msg);
				}
				return TRUE;
			} 
			return FALSE;
		} 
		return FALSE;

	}
}
?>
