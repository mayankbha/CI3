<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use CodeIgniter\Controller;

class Seller extends MY_Controller 
{
	function __construct()
	{
		parent::__construct();
		
		$this->load->library(array('session'));
		$this->load->library(array('ion_auth','form_validation'));
		$this->load->model('seller_model');

		$this->check_seller_access();

		$this->data['my_profile'] = getUserRec();
	}
	/**
	 * Generate the index page
	 *
	 * @access	public
	 * @return	string
	 */
	function index()
	{

		$this->data['message'] = $this->session->flashdata('message');

		$user_id = $this->ion_auth->get_user_id();
		$seller_dashboard_data = $this->seller_model->get_seller_dashboard_data($user_id);
		$this->data['seller_dashboard_data']	= $seller_dashboard_data;
		$inst_seller_dashboard = $this->seller_model->get_inst_seller_dashboard($user_id);
		$this->data['inst_seller_dashboard']	= $inst_seller_dashboard;
		$profile = getUserRec();
		$this->data['pagetitle'] 	= get_languageword('dashboard');
		$this->data['activemenu'] 	= "dashboard";
		$this->data['activesubmenu'] = "dashboard";
		$this->data['content'] 		= 'index';
		$this->_render_page('template/site/seller-template', $this->data);
	}
	
	/**
	 * Fecilitates to upload gallery pictures
	 *
	 * @access	public
	 * @return	string
	 */
	function my_gallery()
	{
		/*if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_seller()) {
			$this->prepare_flashmessage(get_languageword('You dont have permission to access this page'), 1);
			redirect('auth/login', 'refresh');
		}*/
		$this->data['activemenu'] 	= "account";
		$this->data['pagetitle'] = get_languageword('My Gallery');

		
		$this->load->library('Image_crud');
		$image_crud = new image_CRUD();
		$image_crud->unset_jquery(); //As we are using our jQuery bundle we need to unset default jQuery
		
		$image_crud->set_table($this->db->dbprefix('gallery'));
		$image_crud->set_relation_field('user_id');
		$image_crud->set_ordering_field('image_order');
		
		$image_crud->set_title_field('image_title');
		$image_crud->set_primary_key_field('image_id');
		$image_crud->set_url_field('image_name');
		$image_crud->set_image_path('assets/uploads/gallery');
		$output = $image_crud->render();
		$output->grocery = TRUE;
		$this->data['grocery_output'] = $output;
		$this->data['grocery'] = TRUE;
		$this->data['activesubmenu'] = "gallery";
		$this->grocery_output($this->data);
	}



	/**
	 * Facilitates to update personal information
	 *
	 * @access	public
	 * @return	string
	 */
	function personal_info()
	{
		if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_seller()) {
			$this->prepare_flashmessage(get_languageword('You dont have permission to access this page'), 1);
			redirect('auth/login', 'refresh');
		}
		
		$this->data['message'] = $this->session->flashdata('message');
		
		$user_id = $this->ion_auth->get_user_id();

		if(isset($_POST['submitbutt']))
		{
			$this->form_validation->set_rules('paypal_email', get_languageword('paypal_email'), 'trim|required|valid_email|xss_clean');
			$this->form_validation->set_error_delimiters('<div class="error">', '</div>');

			if ($this->form_validation->run() == TRUE)
			{
				
				$inputdata['paypal_email'] = $this->input->post('paypal_email');
				// $inputdata['bank_ac_details'] = $this->input->post('bank_ac_details');
				
				$language_of_teaching = $this->input->post('language_of_teaching');
				if(!empty($language_of_teaching))
				$inputdata['language_of_teaching'] = implode(', ', $language_of_teaching);
				
				$this->base_model->update_operation($inputdata, 'users', array('id' => $user_id));
				
				$this->prepare_flashmessage(get_languageword('profile updated successfully'), 0);
				redirect('seller/personal-info');				
			}
			else
			{
				$this->data['message'] = $this->prepare_message(validation_errors(), 1);
			}
		}	
		$this->data['profile'] = getUserRec();
		
		//Preparing Language options
		$lng_opts = $this->db->get_where('languages',array('status' => 'Active'))->result();
		$options = array();
		if(!empty($lng_opts))
		{
			foreach($lng_opts as $row):
				$options[$row->name] = $row->name;
			endforeach;
		}

		$this->data['language_options'] = $options;
		$this->data['activemenu'] 	= "account";
		$this->data['activesubmenu'] = "personal_info";
		$this->data['pagetitle'] = get_languageword('personal_information');
		$this->data['content'] 		= 'personal_info';
		$this->_render_page('template/site/seller-template', $this->data);
	}
	
	/**
	 * Fecilitates to update profile information includes profile picture
	 *
	 * @access	public
	 * @return	string
	 */
	function profile_information()
	{
		if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_seller()) {
			$this->prepare_flashmessage(get_languageword('You dont have permission to access this page'), 1);
			redirect('auth/login', 'refresh');
		}
		
		$this->data['message'] = $this->session->flashdata('message');
		
		if(isset($_POST['submitbutt']))
		{
			$this->form_validation->set_rules('experience_desc', get_languageword('experience_description'), 'trim|required|max_length[500]|xss_clean');
			$this->form_validation->set_rules('profile', get_languageword('profile_description'), 'trim|required|max_length[500]|xss_clean');
			$this->form_validation->set_rules('seo_keywords',get_languageword('seo_keywords'), 'trim|max_length[100]|xss_clean');
			$this->form_validation->set_rules('meta_desc',get_languageword('meta_description'),'trim|max_length[100]|xss_clean');
			$this->form_validation->set_rules('teaching_experience', get_languageword('teaching_experience'), 'trim|required|xss_clean');
			
			$this->form_validation->set_rules('qualification', get_languageword('qualification'), 'trim|required|xss_clean');
			$this->form_validation->set_rules('profile_page_title', get_languageword('profile_page_title'), 'trim|required|xss_clean');
			if($_FILES['photo']['name'] != '')
			{
				$this->form_validation->set_rules('photo', get_languageword('Profile Image'), 'trim|callback__image_check');
			}
			
			$this->form_validation->set_error_delimiters('<div class="error">', '</div>');
			
			if ($this->form_validation->run() == TRUE)
			{
				$user_id = $this->ion_auth->get_user_id();
				$inputdata['experience_desc'] = $this->input->post('experience_desc');
				$inputdata['profile'] 		  = $this->input->post('profile');
				$inputdata['profile_page_title'] = $this->input->post('profile_page_title');
				$inputdata['qualification'] = $this->input->post('qualification');
				$inputdata['seo_keywords'] = $this->input->post('seo_keywords');
				$inputdata['meta_desc'] = $this->input->post('meta_desc');
				$inputdata['teaching_experience'] = $this->input->post('teaching_experience');
				$inputdata['duration_of_experience'] = $this->input->post('duration_of_experience');
				
				
				$image 	= $_FILES['photo']['name'];
				//Upload User Photo
				if (!empty($image)) 
				{					
					$ext = pathinfo($image, PATHINFO_EXTENSION);
					$file_name = $user_id.'.'.$ext;
					$config['upload_path'] 		= 'assets/uploads/profiles/';
					$config['allowed_types'] 	= 'jpg|jpeg|png';
					$config['overwrite'] 		= true;
					$config['file_name']        = $file_name;
					$this->load->library('upload', $config);
					
					if($this->upload->do_upload('photo'))
					{
						$inputdata['photo']		= $file_name;
						$this->create_thumbnail($config['upload_path'].$config['file_name'],'assets/uploads/profiles/thumbs/'.$config['file_name'], 200, 200);		
					}
				}				
				$this->base_model->update_operation($inputdata, 'users', array('id' => $user_id));
				
				$this->prepare_flashmessage(get_languageword('profile updated successfully'), 0);
				redirect('seller/profile-information');				
			}
			else
			{
				$this->data['message'] = $this->prepare_message(validation_errors(), 1);
			}
		}	
		$this->data['profile'] = getUserRec();
		$degrees = array();
		$records = $this->base_model->fetch_records_from('terms_data', array('term_type' => 'degree', 'term_status' => 'Active'));
		if(!empty($records))
		{
			foreach($records as $record)
			{
				$degrees[$record->term_id] = $record->term_title;
			}
		}
		$this->data['degrees'] = $degrees;
		
		$years = array();
		for($y = 0; $y < 100; $y++)
		{
			$year = date('Y');
			$years[$year-$y] = $year-$y;
		}
		$this->data['years'] = $years;
		$this->data['activemenu'] 	= "account";
		$this->data['activesubmenu'] = "profile_information";
		$this->data['pagetitle'] = get_languageword('profile_information');;
		$this->data['content'] 		= 'profile_information';
		$this->_render_page('template/site/seller-template', $this->data);
	}
	
	public function _image_check()
	{
		$image = $_FILES['photo']['name'];
		$name = explode('.',$image);
		
		if(count($name)>2 || count($name)<= 0) {
           $this->form_validation->set_message('_image_check', 'Only jpg / jpeg / png images are accepted.');
            return FALSE;
        }
		
		$ext = $name[1];
		
		$allowed_types = array('jpg','jpeg','png');
		
		if (!in_array($ext, $allowed_types))
		{			
			
			$this->form_validation->set_message('_image_check', 'Only jpg / jpeg / png images are accepted.');
			
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}
	
	/**
	 * Fecilitates to add / update education information
	 *
	 * @access	public
	 * @return	string
	 */
	function experience($param1 = null, $param2 = null)
	{
		if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_seller()) {
			$this->prepare_flashmessage(get_languageword('You dont have permission to access this page'), 1);
			redirect('auth/login', 'refresh');
		}
		
		$this->data['message'] = $this->session->flashdata('message');
		
		if($param1 == 'delete' && $param2 != '')
		{
			$this->base_model->delete_record_new('users_experience', array('record_id' => $param2));
			$this->prepare_flashmessage(get_languageword('record deleted successfully'), 0);				
			redirect('seller/experience');	
		}
		
		if(isset($_POST['submitbutt']))
		{
			$this->form_validation->set_rules('company', get_languageword('company_name'), 'trim|required|xss_clean');
			$this->form_validation->set_rules('description', get_languageword('description'), 'trim|required|xss_clean');
			$this->form_validation->set_rules('role', get_languageword('role'), 'trim|required|xss_clean');
			
			$this->form_validation->set_error_delimiters('<div class="error">', '</div>');
			
			if ($this->form_validation->run() == TRUE)
			{
				$inputdata['company'] = $this->input->post('company');
				$inputdata['role'] = $this->input->post('role');
				$inputdata['description'] = $this->input->post('description');
				if($this->input->post('to_month') == 'Present')
					 $year = ' ';
				else
					$year = $this->input->post('to_year');

				$inputdata['from_date'] = $this->input->post('from_month').' '. $this->input->post('from_year');
				$inputdata['to_date'] = $this->input->post('to_month').' '. $year;
			
				$inputdata['user_id'] = $this->ion_auth->get_user_id();
				$update_rec_id = $this->input->post('update_rec_id');
				if($update_rec_id != '')
				{
				$inputdata['updated_at'] = date ("Y-m-d H:i:s");
				$this->base_model->update_operation($inputdata, 'users_experience', array('record_id' => $update_rec_id));
				$this->prepare_flashmessage(get_languageword('record updated successfully'), 0);
				}
				else
				{
			
				$inputdata['created_at'] = date ("Y-m-d H:i:s");
				$inputdata['updated_at'] = $inputdata['created_at'];
				$this->base_model->insert_operation($inputdata,'users_experience');
				$this->prepare_flashmessage(get_languageword('record added successfully'), 0);
				}				
				redirect('seller/experience');				
			}
			else
			{
				$this->data['message'] = $this->prepare_message(validation_errors(), 1);
			}
		}	
		$this->data['profile'] = array();
		if($param1 == 'edit' && $param2 != '')
		{
			$profile = $this->base_model->fetch_records_from('users_experience', array('record_id' => $param2));
			if(!empty($profile))
			$this->data['profile'] = $profile[0];
		}
		$this->data['educations'] = $this->base_model->fetch_records_from('users_experience', array('user_id' => $this->ion_auth->get_user_id()));
			
		$years = array();
		for($y = 0; $y < 100; $y++)
		{
			$year = date('Y');
			$years[$year-$y] = $year-$y;
		}
		
		$months= array("Present"=>"Present","January" => "January","February"=> "February","March" => "March","April" => "April","May" => "May","June" => "June","July" => "July", "August"=> "August","September" => "September","October" => "October","November" => "November","December" => "December");
			
		$this->data['param1'] = $param1;
		$this->data['param2'] = $param2;
		$this->data['months'] = $months;
		$this->data['years'] = $years;
		$this->data['activemenu'] 	= "account";
		$this->data['activesubmenu'] = "experience";
		$this->data['pagetitle'] = get_languageword('experience');
		$this->data['content'] 		= 'experience';
		$this->_render_page('template/site/seller-template', $this->data);
	}
	
	/**
	 * Fecilitates to update contact information
	 *
	 * @access	public
	 * @return	string
	 */
	function update_contact_information()
	{
		if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_seller()) {
			$this->prepare_flashmessage(get_languageword('You dont have permission to access this page'), 1);
			redirect('auth/login', 'refresh');
		}
		
		$this->data['message'] = $this->session->flashdata('message');
		if(isset($_POST['submitbutt']))
		{
			$this->form_validation->set_rules('city', get_languageword('City'), 'trim|required|xss_clean');
			$this->form_validation->set_rules('land_mark', get_languageword('land_mark'), 'trim|required|xss_clean');			
			$this->form_validation->set_rules('country', get_languageword('country'), 'trim|required|xss_clean');
			$this->form_validation->set_rules('pin_code', get_languageword('pin_code'), 'trim|required|xss_clean');
			$this->form_validation->set_rules('phone', get_languageword('phone'), 'trim|required|xss_clean');
			
			$this->form_validation->set_error_delimiters('<div class="error">', '</div>');
			
			if ($this->form_validation->run() == TRUE)
			{
				$inputdata['city'] = $this->input->post('city');
				$inputdata['land_mark'] = $this->input->post('land_mark');
				$code_country = explode('_', $this->input->post('country'));

				$inputdata['country'] = $code_country[1];
				$inputdata['phone_code'] = $code_country[0];
				$inputdata['pin_code'] = $this->input->post('pin_code');
				$inputdata['phone'] = $this->input->post('phone');				
				$inputdata['academic_class'] = isset($_POST['academic_class']) ? 'yes' : 'no';
				$inputdata['non_academic_class'] = isset($_POST['non_academic_class']) ? 'yes' : 'no';
				$inputdata['share_phone_number'] = isset($_POST['share_phone_number']) ? 'yes' : 'no';				
				$this->base_model->update_operation($inputdata, 'users', array('id' => $this->ion_auth->get_user_id()));

				$this->prepare_flashmessage(get_languageword('record updated successfully'), 0);								
				redirect('seller/update-contact-information');				
			}
			else
			{
				$this->data['message'] = $this->prepare_message(validation_errors(), 1);
			}
		}

		$this->data['profile'] = getUserRec();
		$countries = $this->base_model->fetch_records_from('country');
		$countries_opts = array('' => get_languageword('please select country'));
		if(!empty($countries))
		{
			foreach($countries as $country)
			{
				$countries_opts[$country->phonecode.'_'.$country->nicename]  = $country->nicename." +".$country->phonecode;
			}
		}
		$this->data['countries'] 	 = $countries_opts;
		$this->data['activemenu'] 	= "account";
		$this->data['activesubmenu'] = "update_contact_info";
		$this->data['pagetitle'] 	= get_languageword('update_contact_information');
		$this->data['content'] 		= 'update_contact_information';
		$this->_render_page('template/site/seller-template', $this->data);
	}
	
	/**
	 * Fecilitates to view contact information
	 *
	 * @access	public
	 * @return	string
	 */
	function contact_information()
	{
		if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_seller()) {
			$this->prepare_flashmessage(get_languageword('You dont have permission to access this page'), 1);
			redirect('auth/login', 'refresh');
		}
		$this->data['profile'] = getUserRec();
		$this->data['activemenu'] 	= "account";
		$this->data['activesubmenu'] = "update_contact_info";	
		$this->data['content'] 		= 'contact_information';
		$this->data['pagetitle'] = get_languageword('Contact Information');
		$this->_render_page('template/site/seller-template', $this->data);
	}
	
	/**
	 * Fecilitates to add or update seller sellering subjects
	 *
	 * @access	public
	 * @return	string
	 */
	function manage_subjects()
	{
		if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_seller()) {
			$this->prepare_flashmessage(get_languageword('You dont have permission to access this page'), 1);
			redirect('auth/login', 'refresh');
		}
		
		$this->data['message'] = $this->session->flashdata('message');
		$sellerSubjectIds 	= $this->seller_model->get_seller_subject_ids(
		$this->ion_auth->get_user_id()); //Getting seller selected subject ids
		
		if ($this->input->post()) {	
		
			if ($this->input->post('seller_subjects')) {
				if ($this->input->post('seller_subjects') != $sellerSubjectIds) {
					$seller_subjects 	= $this->input->post('seller_subjects');
					if ($this->base_model->delete_record_new('seller_subjects', array(
						'user_id' 	=> $this->ion_auth->get_user_id()))) {
						$data['user_id'] 		= $this->ion_auth->get_user_id();
						foreach($seller_subjects as $subject) {
							if (is_numeric($subject)) {
								$data['subject_id'] = $subject;
								$this->base_model->insert_operation($data, 'seller_subjects');
							}
						}
						$this->prepare_flashmessage(get_languageword('subjects')." " . 
						get_languageword('updated_successfully'), 0);
					}
					else
					{
						$this->prepare_flashmessage(get_languageword('subjects').' '.get_languageword('failed to update'), 1);
					}
				}
				else {
					$this->prepare_flashmessage(get_languageword('You have not done any changes'), 2);
				}
			}
			else {
				$this->prepare_flashmessage(get_languageword('Please select at least on subject'), 1);
			}
			redirect('seller/manage-subjects', 'refresh');
		}
		
		$this->data['subjects'] 	= $this->seller_model->get_subjects();
		$this->data['sellerSubjectIds'] 	= $sellerSubjectIds;
		$this->data['pagetitle'] = $this->data['my_profile']->first_name.' '.$this->data['my_profile']->last_name.' '.get_languageword('Subjects');
		
		$this->data['activemenu'] 	= "manage";
		$this->data['activesubmenu'] = "manage_subjects";	
		$this->data['content'] 		= 'manage_subjects';
		$this->_render_page('template/site/seller-template', $this->data);
	}


	/**
	 * [manage_books description]
	 * @return [type] [description]
	 */
	function manage_books()
	{
		redirect('seller/index', 'refresh');
		die();

		if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_seller()) {
			$this->prepare_flashmessage(get_languageword('You dont have permission to access this page'), 1);
			redirect('auth/login', 'refresh');
		}

		$this->data['message'] = $this->session->flashdata('message');

		$user_id = $this->ion_auth->get_user_id();
		$this->load->library(array('grocery_CRUD'));
		$crud = new grocery_CRUD();
		$crud_state = $crud->getState();
		$crud->set_table(TBL_SELLER_BOOKS);
		$crud->set_relation('book_id',TBL_CATEGORIES, 'name');
		$crud->where('seller_id', $user_id);
		// $crud->where('jea134da7.status', 1);
		$crud->set_subject( get_languageword('sellering_books') );


		//List Table Columns
		$crud->columns('book_id','book_duration','fee','content','time_slots', 'days_off');

		$crud->callback_column('book_duration',array($this,'_callback_book_duration'));

		//Display Alias Names
		$crud->display_as('book_id',get_languageword('book_name'));
		$crud->display_as('fee',get_languageword('fee').' ('.get_languageword('in_credits').')');
		$crud->display_as('per_credit_value',get_languageword('per_credit_value')." (".get_system_settings('currency_symbol').")");

		//From Validations
		$crud->required_fields(array('book_id','duration_value','duration_type', 'fee', 'content', 'time_slots', 'sort_order'));
		$crud->set_rules('fee',get_languageword('fee'),'integer');

		//Form fields for Add Record
		$crud->add_fields('seller_id', 'book_id','duration_type','duration_value', 'fee', 'per_credit_value', 'content', 'time_slots', 'days_off', 'sort_order', 'created_at', 'updated_at');

		//Form fields for Edit Record
		$crud->edit_fields('seller_id', 'book_id','duration_type','duration_value', 'fee', 'per_credit_value', 'content', 'time_slots', 'days_off', 'status', 'sort_order', 'updated_at');

		//Unset Read Fields
		$crud->unset_read_fields('seller_id');

		//Set Custom Filed Types
		$crud->field_type('days_off', 'multiselect', array('SUN' => 'SUN', 'MON' => 'MON', 'TUE' => 'TUE', 'WED' => 'WED', 'THU' => 'THU', 'FRI' => 'FRI', 'SAT' => 'SAT'));
		$crud->field_type('seller_id', 'hidden', $user_id);
		$crud->field_type('per_credit_value', 'hidden', get_system_settings('per_credit_value'));
		$crud->field_type('created_at', 'hidden', date('Y-m-d H:i:s'));
		$crud->field_type('updated_at', 'hidden', date('Y-m-d H:i:s'));

		//Modify fields in Form
		$crud->callback_field('book_id',array($this,'call_back_set_book_dropdown'));
		$crud->callback_field('time_slots',array($this,'call_back_set_time_slots_field'));


		//Authenticate whether Seller editing/viewing his records only
		if($crud_state == "edit" || $crud_state == "read") {

			$p_key = $this->uri->segment(4);
			$seller_id = $this->base_model->fetch_value('seller_books', 'seller_id', array('id' => $p_key));
			if($seller_id != $user_id) {

				$this->prepare_flashmessage(get_languageword('not_authorized'), 1);
    			redirect(URL_SELLER_MANAGE_BOOKS);
			}

		}

		if($crud_state == "read") {

			$crud->field_type('created_at', 'visibile');
			$crud->field_type('updated_at', 'visibile');
			$crud->set_relation('status','user_status_texts','text');
		}

		$crud->callback_after_insert(array($this, 'callback_is_profile_updated1'));

		$output = $crud->render();

		
		$this->data['activemenu'] 	= 'manage';	
		$this->data['activesubmenu'] 	= 'books';	
		$this->data['pagetitle'] = get_languageword('Manage');
		$this->data['grocery_output'] = $output;
		$this->data['grocery'] = TRUE;
		
		$this->grocery_output($this->data);
	}

	function _callback_book_duration($primary_key, $row)
	{
		return $row->duration_value.' '. $row->duration_type;
	}


	function call_back_set_book_dropdown($val)
	{
		//Book Options
		$this->load->model('home_model');
		$books = $this->home_model->get_books();
		$book_opts[''] = get_languageword('select_book');
		foreach ($books as $key => $value) {
			$book_opts[$value->id] = $value->name;
		}

		$val = !empty($val) ? $val : '';

		return form_dropdown('book_id', $book_opts, $val, 'id="book_id" class="chosen-select" ');
	}


	function call_back_set_time_slots_field($value)
	{
		$value = !empty($value) ? $value : '';
		return '<input type="text" name="time_slots" value="'.$value.'" placeholder="'.get_languageword('example_format').' 6-7,13-14,14-16,20.30-21.30">';
	}



	function callback_is_profile_updated1($post_array, $primary_key)
	{	
		//05-12-2018 admin notification start - when seller posted his book
		if (isset($post_array['title'])!="" && $post_array['seller_id']!="") {

			$blog = $this->base_model->get_query_result("SELECT * FROM ".TBL_SELLER_BLOGS." ORDER BY blog_id DESC limit 1");

			if (!empty($blog)) {
				$blog = $blog[0];
			}

			if(!empty($blog)) {

				$data = array();
				$data['user_id'] 	= $post_array['seller_id'];
				$data['title'] 		= get_languageword("seller_posted_his_blog");
				$data['content'] 	= "Seller has been posted his blog ".$blog->title;
				$data['datetime']   = date('Y-m-d H:i:s');
				$data['admin_read'] = 0;
				$data['page_link']  = URL_ADMIN_VIEW_SELLERS_BLOGS.'/read/'.$blog->blog_id;
				$data['table_name'] = "seller_blogs";
				$data['primary_key_column'] = "blog_id";
				$data['primary_key_value']  = $blog->blog_id;

				$this->base_model->insert_operation($data,'notifications');	
				unset($data);
			}
		}
		//admin notification end
		


		$is_profile_updated = $this->ion_auth->user($post_array['seller_id'])->row()->is_profile_update;

		if($is_profile_updated != 1) {

			$tut_pref_teaching_types = $this->base_model->fetch_records_from('seller_teaching_types', array('seller_id' => $post_array['seller_id'], 'status' => 1));
			if(count($tut_pref_teaching_types) > 0)
				$this->base_model->update_operation(array('is_profile_update' => 1), 'users', array('id' => $post_array['seller_id']));
		}

		return TRUE;
	}



	/**
	 * Facilitates to add or update seller locations, where he is sellering
	 *
	 * @access	public
	 * @return	string
	 */
	function manage_locations()
	{
		if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_seller()) {
			$this->prepare_flashmessage(get_languageword('You dont have permission to access this page'), 1);
			redirect('auth/login', 'refresh');
		}
		
		$this->data['message'] = $this->session->flashdata('message');
		$sellerLocationIds 	= $this->seller_model->get_seller_location_ids(
		$this->ion_auth->get_user_id()); //Getting locaiton ids
		
		if ($this->input->post()) 
		{		
			if ($this->input->post('seller_locations')) {
				if ($this->input->post('seller_locations') != $sellerLocationIds) {
					$seller_locations 	= $this->input->post('seller_locations');
					if ($this->base_model->delete_record_new('seller_locations', array(
						'seller_id' 	=> $this->ion_auth->get_user_id()
					))) {
						$data['seller_id'] 	= $this->ion_auth->get_user_id();
						$data['created_at'] = date('Y-m-d H:i:s');
						foreach($seller_locations as $location) {
							if (is_numeric($location)) {
								$data['location_id'] = $location;
								$this->base_model->insert_operation($data, 'seller_locations');
							}
						}

						$this->prepare_flashmessage(get_languageword('Locations') . " " . get_languageword('updated successfully'), 0);
					}
					else
					{
						$this->prepare_flashmessage(get_languageword('Locations') . " " . get_languageword('failed to updated'), 1);
					}						
				}
				else {
					$this->prepare_flashmessage(get_languageword('You have not done any changes'), 2);
				}
			}
			else {
				$this->prepare_flashmessage(
				get_languageword('please_select_atleast_one_preferred_location'), 1);
			}
			redirect('seller/manage-locations');
		}
		
		$this->data['locations'] 				= $this->seller_model->get_locations();
		$this->data['sellerLocationIds'] 		= $sellerLocationIds;
		
		
		$this->data['activemenu'] 	= "manage";
		$this->data['activesubmenu'] = "manage_locations";
		$this->data['pagetitle'] = get_languageword('Manage').'-'.get_languageword('Locations');		
		$this->data['content'] 		= 'manage_locations';
		$this->_render_page('template/site/seller-template', $this->data);
	}
	
	/**
	 * Fecilitates to add or update seller teaching types
	 *
	 * @access	public
	 * @return	string
	 */
	function manage_teaching_types()
	{
		if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_seller()) {
			$this->prepare_flashmessage(get_languageword('You dont have permission to access this page'), 1);
			redirect('auth/login', 'refresh');
		}
		
		$this->data['message'] = $this->session->flashdata('message');
		$sellerSelectedTypeIds 	= $this->seller_model->get_seller_selected_teachingtype_ids(
		$this->ion_auth->get_user_id());
		
		if ($this->input->post()) 
		{
			if ($this->input->post('seller_selected_types')) {
				$user_id = $this->ion_auth->get_user_id();
				if ($this->input->post('seller_selected_types') != $sellerSelectedTypeIds) {
					$seller_selected_types 	= $this->input->post('seller_selected_types');
					if ($this->base_model->delete_record_new('seller_teaching_types', array('seller_id' => $user_id))) {
						$data['seller_id'] 		= $this->ion_auth->get_user_id();
						$data['created_at'] 	= date('Y-m-d H:i:s');
						foreach($seller_selected_types as $seller_type) {
							if (is_numeric($seller_type)) {
								$data['teaching_type_id'] = $seller_type;
								$this->base_model->insert_operation($data, 'seller_teaching_types');
							}
						}

						$is_profile_updated = $this->ion_auth->user($user_id)->row()->is_profile_update;

						if($is_profile_updated != 1) {

							$tut_pref_books = $this->base_model->fetch_records_from('seller_books', array('seller_id' => $user_id, 'status' => 1));

							if(count($tut_pref_books) > 0) {

								$this->base_model->update_operation(array('is_profile_update' => 1), 'users', array('id' => $user_id));

							}
						}

						$this->prepare_flashmessage(get_languageword('Teaching Types'). " " . get_languageword('updated_successfully'), 0);
					}
					else
					{
						$this->prepare_flashmessage(get_languageword('Teaching Types'). " " . get_languageword('failed to update'), 1);
					}
				}
				else {
					$this->prepare_flashmessage(get_languageword('you_have_not_done_any_changes') , 2);
				}
			}
			else {
				$this->prepare_flashmessage(
				get_languageword('please_select_atleast_one_preferred_teaching_type') , 1);
			}
			redirect('seller/manage-teaching-types', 'refresh');
		}
		
		$this->data['seller_types'] 				= $this->seller_model->get_seller_teachingtypes();
		$this->data['sellerSelectedTypeIds']	 	= $sellerSelectedTypeIds;
		
		$this->data['activemenu'] 	= "manage";
		$this->data['activesubmenu'] = "manage_teaching_types";	
		$this->data['pagetitle'] = get_languageword('Manage Teaching Types');
		$this->data['content'] 		= 'manage_teaching_types';
		$this->_render_page('template/site/seller-template', $this->data);
	}
	
	/**
	 * Fecilitates to display packages for seller.
	 *
	 * @access	public
	 * @param	string (Optional)
	 * @return	string
	 */	
	function list_packages($param1 = '')
	{
		if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_seller() || is_inst_seller($this->ion_auth->get_user_id())) {
			$this->prepare_flashmessage(get_languageword('You dont have permission to access this page'), 1);
			redirect('auth/login', 'refresh');
		}
		
		$this->data['message'] = $this->session->flashdata('message');
				
		$this->data['pagetitle'] = get_languageword('packages');
		$this->data['package_data'] = $this->seller_model->list_seller_packages();
		
		$this->data['payment_gateways'] = $this->base_model->get_payment_gateways('', 'Active');

		$this->data['activemenu'] 	= "Packages";
		$this->data['activesubmenu'] = "list_packages";	
		$this->data['pagetitle'] = get_languageword('Packages');
		$this->data['content'] 		= 'list_packages';
		$this->_render_page('template/site/seller-template', $this->data);
	}
	
	/**
	 * [mysubscriptions description]
	 * @return [type] [description]
	 */
	function mysubscriptions()
	{
		if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_seller()) {
			$this->prepare_flashmessage(get_languageword('You dont have permission to access this page'), 1);
			redirect('auth/login', 'refresh');
		}
		
		$this->data['message'] = $this->session->flashdata('message');

		$user_id = $this->ion_auth->get_user_id();
		$this->load->library(array('grocery_CRUD'));
		$crud = new grocery_CRUD();
		$crud_state = $crud->getState();
		$crud->set_table($this->db->dbprefix('subscriptions'));
		$crud->where('user_id', $user_id);
		$crud->set_subject( get_languageword('subscriptions') );


		$crud->unset_add();
		$crud->unset_delete();
		$crud->unset_edit();
		$crud->unset_read();

		$crud->columns('subscribe_date','package_name','transaction_no', 'payment_type','credits','amount_paid');
		$crud->callback_column('subscribe_date',array($this,'callback_subscribe_date'));
		$output = $crud->render();
		
		$this->data['pagetitle'] = get_languageword('packages');
		$this->data['activemenu'] 	= "Packages";
		$this->data['activesubmenu'] 	= "mysubscriptions";
		$this->data['content'] 		= 'mysubscriptions';
		$this->data['grocery_output'] = $output;
		$this->data['grocery'] = TRUE;
		$this->grocery_output($this->data);
	}
	
	function callback_subscribe_date($value, $row)
	{
		return date('d/m/Y', strtotime($value));
	}
	
	/**
	 * Fecilitates to set privacy
	 *
	 * @access	public
	 * @return	string
	 */
	function manage_privacy()
	{
		if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_seller()) {
			$this->prepare_flashmessage(get_languageword('You dont have permission to access this page'), 1);
			redirect('auth/login', 'refresh');
		}
		
		$this->data['message'] = $this->session->flashdata('message');
		$this->data['profile'] = getUserRec();		
		if(isset($_POST['submitbutt']))
		{
			$inputdata['free_demo'] = $this->input->post('free_demo');
			$inputdata['visibility_in_search'] = $this->input->post('visibility_in_search');
			$inputdata['show_contact'] = $this->input->post('show_contact');
			$inputdata['availability_status'] = $this->input->post('availability_status');
			$this->base_model->update_operation($inputdata, 'users', array('id' => $this->ion_auth->get_user_id()));
			$this->prepare_flashmessage(get_languageword('privacy updated successfully'), 0);								
			redirect('seller/manage-privacy');			
		}
		
		$this->data['pagetitle'] = get_languageword('Manage Privacy');
		$this->data['activemenu'] 	= "manage";
		$this->data['activesubmenu'] = "manage_privacy";	
		$this->data['content'] 		= 'manage_privacy';
		$this->_render_page('template/site/seller-template', $this->data);
	}

	/**
	 * Institute Seller Batches List
	 *
	 * @access	public
	 * @return	string
	 */
	function my_batches($book_id = "")
	{
		if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_seller()) {
			$this->prepare_flashmessage(get_languageword('You dont have permission to access this page'), 1);
			redirect('auth/login', 'refresh');
		}

		$user_id = $this->ion_auth->get_user_id(); 
		$inst_id = is_inst_seller($user_id);

		if(!$inst_id) {

			$this->prepare_flashmessage(get_languageword('You dont have permission to access this page'), 1);
			redirect('auth/login', 'refresh');
		}

		$page_title = get_languageword('my_batches');

		$book_id = ($this->input->post('book_id')) ? $this->input->post('book_id') : $book_id;


		if(!empty($book_id))
		{

			$this->data['message'] = $this->session->flashdata('message');

			$this->load->library(array('grocery_CRUD'));
			$crud = new grocery_CRUD();
			$crud_state = $crud->getState();
			$crud->set_table($this->db->dbprefix('inst_batches'));
			$crud->where('seller_id', $user_id);
			$crud->where('inst_id', $inst_id);
			$crud->where('book_id', $book_id);
			$crud->set_relation('inst_id','users','username');

			$crud->set_subject( get_languageword('seller_batches_list') );

			$crud->unset_add();
			$crud->unset_delete();
			$crud->unset_read();
			$crud->unset_edit();

			$crud->columns('inst_id','batch_code','batch_name','book_offering_location', 'batch_start_date', 'batch_end_date', 'time_slot','duration_value','duration_type', 'total_enrolled_buyers', 'status', 'initiate_session');

			$crud->callback_column('total_enrolled_buyers',array($this,'_callback_batch_enrolled_buyers_cnt'));
			$crud->callback_column('status',array($this,'_callback_batch_status'));
			$crud->callback_column('initiate_session',array($this,'callback_batch_action_initiated'));

			$crud->display_as('inst_id',get_languageword('Institute_Name'));
			$crud->display_as('status',get_languageword('batch_status'));

			//custom actions
			$crud->add_action(get_languageword('View Enrolled Buyers'), URL_FRONT_IMAGES.'magnifier-grocery.png',  URL_SELLER_VIEW_STUDETNS.'/');
			$crud->add_action(get_languageword('Update as Book Completed For Batch'), URL_FRONT_IMAGES.'approve.png', '','',array($this,'callback_batch_action_completed'));

			$output = $crud->render();

			$this->data['book_id'] = $book_id;

			$this->data['grocery_output'] = $output;
			$this->data['grocery'] = TRUE;

		 }

		$this->data['message'] = $this->session->flashdata('message');
		$this->data['pagetitle'] = $page_title;
		$this->data['seller_books'] = $this->seller_model->get_seller_assigned_book($user_id,$inst_id);
		$this->data['activemenu'] 	= "my_batches";
		$this->grocery_output($this->data);
		
	}


	function _callback_batch_enrolled_buyers_cnt($primary_key, $row)
	{
		$batch_id = $row->batch_id;
		$this->load->model('institute/institute_model');
		return $this->institute_model->get_batch_enrolled_buyers_cnt($batch_id);
	}


	function _callback_batch_status($val, $row)
	{
		$batch_id = $row->batch_id;

		$this->load->model('institute/institute_model');
		$batch_status = $this->institute_model->get_batch_status($batch_id);

		return get_languageword($batch_status);

	}

	function callback_batch_action_initiated($val, $row)
	{
		$batch_status = $row->status;

		$today = date('Y-m-d');
		$batch_start_date = str_replace('/', '-', $row->batch_start_date);
		$batch_start_date = date('Y-m-d', strtotime($batch_start_date));

		$batch_end_date = str_replace('/', '-', $row->batch_end_date);
		$batch_end_date = date('Y-m-d', strtotime($batch_end_date));

		if($batch_status == "approved" && (strtotime($batch_start_date) <= strtotime($today)) && (strtotime($today) <= strtotime($batch_end_date))) {

			$cur_time 	= (float)date('H.i');
			$time_slot 	= str_replace(':', '.', $row->time_slot);
			$time 	  	= explode('-', str_replace(' ', '', $time_slot));
			$start_time = date('H:i', strtotime(number_format($time[0],2)));
			$end_time   = date('H:i', strtotime(number_format($time[1],2)));

			$certain_mins_before_start_time = (float)date('H.i', strtotime($start_time.' -'.$this->config->item('site_settings')->enable_initiate_session_option_before_mins.' minutes'));
			$certain_mins_before_end_time 	= (float)date('H.i', strtotime($end_time.' -'.$this->config->item('site_settings')->enable_book_completed_option_before_mins.' minutes'));

			if($cur_time >= $certain_mins_before_start_time && $cur_time <= $certain_mins_before_end_time) {

				$initiate_actn = "<a title='".get_languageword('Initiate Session For Batch Buyers')."' href='".URL_SELLER_INITIATE_BATCH_SESSION.'/'.$row->book_id.'/'.$row->batch_id."'><img src='".URL_FRONT_IMAGES.'initiate-session.png'."' alt='".get_languageword('Initiate Session For Batch Buyers')."'/></a>";				

				return $initiate_actn;

			} else return '-';
		} else return '-';
	}


	function callback_batch_action_completed($val, $row)
	{
		$batch_status = $row->status;

		if($batch_status == "running" || $batch_status == "Running") {

			$today = date('Y-m-d');
			$batch_start_date = str_replace('/', '-', $row->batch_start_date);
			$batch_start_date = date('Y-m-d', strtotime($batch_start_date));

			if(strtotime($today) >= strtotime($batch_start_date)) {

				return URL_SELLER_COMPLETE_BATCH_SESSION.'/'.$row->book_id.'/'.$row->batch_id;
			}

		}
	}


	/**
	 * [initiate_batch_session description]
	 * @param  string $book_id [description]
	 * @param  string $batch_id  [description]
	 * @return [type]            [description]
	 */
	function initiate_batch_session($book_id = "", $batch_id = "")
	{
		if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_seller()) {
			$this->prepare_flashmessage(get_languageword('You dont have permission to access this page'), 1);
			redirect('auth/login', 'refresh');
		}

		$user_id   = $this->ion_auth->get_user_id();
		$inst_id   = is_inst_seller($user_id);

		if(!$inst_id) {

			$this->prepare_flashmessage(get_languageword('You dont have permission to access this page'), 1);
			redirect('auth/login', 'refresh');
		}

		$book_id = ($this->input->post('book_id')) ? $this->input->post('book_id') : $book_id;
		$batch_id  = ($this->input->post('batch_id')) ? $this->input->post('batch_id') : $batch_id;

		if(empty($book_id) || empty($batch_id)) {

			$this->prepare_flashmessage(get_languageword('No Details Found'), 1);
			redirect(URL_SELLER_MY_BATCHES);
		}

		$batch_det_recs = $this->base_model->fetch_records_from('inst_enrolled_buyers', array('batch_id' => $batch_id, 'status =' => 'approved'));

		if(empty($batch_det_recs)) {

			$this->prepare_flashmessage(get_languageword('No Buyer enrolled in this batch.'), 2);
			redirect(URL_SELLER_MY_BATCHES.'/'.$book_id);
		}


		$batch_det = $batch_det_recs[0];

		//Check Whether Seller updating their record only
		if($user_id != $batch_det->seller_id) {

			$this->prepare_flashmessage(get_languageword('You dont have permission to perform this action'), 1);
			redirect(URL_SELLER_MY_BATCHES.'/'.$book_id);
		}


		if($this->input->post()) {

			$this->load->model('institute/institute_model');
			$batch_status = $this->institute_model->get_batch_status($batch_id);

			//If batch not already initiated, status as approve and status_desc, 
			//else update status desc only.
			if($batch_status == "approved") {

				$up_data['prev_status'] = 'approved';
				$up_data['status'] 		= 'running';
			}

			$up_data['updated_at'] 		= date('Y-m-d H:i:s');
			$up_data['updated_by'] 		= $user_id;
			$up_data['status_desc'] 	= $this->input->post('status_desc');

			if($batch_status == "approved" || ($batch_status != "pending" && $batch_status != "approved" && ($up_data['status_desc'] != $batch_det->status_desc))) {

				if($this->base_model->update_operation($up_data, 'inst_enrolled_buyers', array('batch_id' => $batch_id, 'status =' => 'approved'))) {

					//Email Alert to Batch Buyers - Start

					//Get Batch Session Initiated Alert To Buyer Email Template
					$email_tpl = $this->base_model->fetch_records_from('email_templates', array('template_status' => 'Active', 'email_template_id' => '14'));

					if(!empty($email_tpl)) {

						$email_tpl = $email_tpl[0];

						$seller_rec 	 = getUserRec($batch_det->seller_id);

						foreach($batch_det_recs as $row) {

							$buyer_rec = getUserRec($row->buyer_id);

							if(!empty($email_tpl->from_email)) {

								$from = $email_tpl->from_email;

							} else {

								$from 	= $seller_rec->email;
							}

							$to 	= $buyer_rec->email;

							if(!empty($email_tpl->template_subject)) {

								$sub = $email_tpl->template_subject;

							} else {

								$sub = get_languageword("Batch Session Initiated");
							}

							if(!empty($email_tpl->template_content)) {


								$logo_img='<img src="'.get_site_logo().'" class="img-responsive" width="120px" height="50px">';

								$site_title = $this->config->item('site_settings')->site_title;


								$original_vars  = array($logo_img, $site_title, $buyer_rec->username, $seller_rec->username, $batch_det->batch_name." - ".$batch_det->batch_code, '<a href="'.URL_AUTH_LOGIN.'">'.get_languageword('Login Here').'</a>');


								$temp_vars		= array('__SITE_LOGO__', '__SITE_TITLE__', '__BUYER__NAME__', '__SELLER_NAME__', '__BATCH_NAME__', '__LOGINLINK__');


								$msg = str_replace($temp_vars, $original_vars, $email_tpl->template_content);

							} else {

								$msg = get_languageword('please')." <a href='".URL_AUTH_LOGIN."'>".get_languageword('Login Here')."</a> ".get_languageword('to view the details.');
								$msg .= "<p>".get_languageword('Thank you')."</p>";
							}

							sendEmail($from, $to, $sub, $msg);
						}
					}
					//Email Alert to Batch Buyers - End

					if($batch_status == "approved")
						$this->prepare_flashmessage(get_languageword('Batch Session Initiated successfully'), 0);
					else
						$this->prepare_flashmessage(get_languageword('Information updated successfully'), 0);

					redirect(URL_SELLER_MY_BATCHES.'/'.$book_id);

				} else {

					$this->prepare_flashmessage(get_languageword('Batch Session not initiated due to some technical issue'), 2);
					redirect(URL_SELLER_MY_BATCHES.'/'.$book_id);
				}

			} else {

				$this->prepare_flashmessage(get_languageword('Batch Session already initiated'), 2);
				redirect(URL_SELLER_MY_BATCHES.'/'.$book_id);
			}

		}

		$this->data['book_id'] 	= $book_id;
		$this->data['status_desc'] 	= $batch_det->status_desc;
		$this->data['batch_id'] 	= $batch_id;
		$this->data['message'] 		= $this->session->flashdata('message');
		$this->data['pagetitle'] 	= get_languageword('Initiate Session for the Batch')." (".$batch_det->batch_name." - ".$batch_det->batch_code.") ";
		$this->data['content']	 	= 'initiate_session_for_batch_buyers';
		$this->data['activemenu']	= "enrolled_buyers";
		$this->_render_page('template/site/seller-template', $this->data);
	}

	/**
	 * [complete_batch_session description]
	 * @param  string $book_id [description]
	 * @param  string $batch_id  [description]
	 * @return [type]            [description]
	 */
	function complete_batch_session($book_id = "", $batch_id = "")
	{
		if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_seller()) {
			$this->prepare_flashmessage(get_languageword('You dont have permission to access this page'), 1);
			redirect('auth/login', 'refresh');
		}

		$user_id   = $this->ion_auth->get_user_id();
		$inst_id   = is_inst_seller($user_id);

		if(!$inst_id) {

			$this->prepare_flashmessage(get_languageword('You dont have permission to access this page'), 1);
			redirect('auth/login', 'refresh');
		}

		$book_id = ($this->input->post('book_id')) ? $this->input->post('book_id') : $book_id;
		$batch_id  = ($this->input->post('batch_id')) ? $this->input->post('batch_id') : $batch_id;

		if(empty($book_id) || empty($batch_id)) {

			$this->prepare_flashmessage(get_languageword('No Details Found'), 1);
			redirect(URL_SELLER_MY_BATCHES);
		}

		$batch_det_recs = $this->base_model->fetch_records_from('inst_enrolled_buyers', array('batch_id' => $batch_id, 'status =' => 'running'));

		if(empty($batch_det_recs)) {

			$this->prepare_flashmessage(get_languageword('No Buyer enrolled in this batch'), 2);
			redirect(URL_SELLER_MY_BATCHES.'/'.$book_id);
		}


		$batch_det = $batch_det_recs[0];

		//Check Whether Seller updating their record only
		if($user_id != $batch_det->seller_id) {

			$this->prepare_flashmessage(get_languageword('You dont have permission to perform this action'), 1);
			redirect(URL_SELLER_MY_BATCHES.'/'.$book_id);
		}


		if($this->input->post()) {

			$this->load->model('institute/institute_model');
			$batch_status = $this->institute_model->get_batch_status($batch_id);

			if($batch_status == "running") {

				$up_data['prev_status'] = 'running';
				$up_data['status'] 		= 'closed';

				$up_data['updated_at'] 		= date('Y-m-d H:i:s');
				$up_data['updated_by'] 		= $user_id;
				$up_data['status_desc'] 	= $this->input->post('status_desc');


				if($this->base_model->update_operation($up_data, 'inst_enrolled_buyers', array('batch_id' => $batch_id, 'status =' => 'running'))) {

					//Log Credits transaction data & update user net credits - Start
					$this->load->model('institute_model');
					$total_credits_of_batch_closed = $this->institute_model->get_credits_of_batch_closed($batch_id);

					$log_data = array(
									'user_id' => $inst_id,
									'credits' => $total_credits_of_batch_closed,
									'per_credit_value' => $batch_det->per_credit_value,
									'action'  => 'credited',
									'purpose' => 'Credits added for the batch "'.$batch_id.'" ',
									'date_of_action	' => date('Y-m-d H:i:s'),
									'reference_table' => 'inst_enrolled_buyers',
									'reference_id' => $batch_id,
								);

					log_user_credits_transaction($log_data);

					update_user_credits($inst_id, $total_credits_of_batch_closed, 'credit');
					//Log Credits transaction data & update user net credits - End

					//Email Alert to Batch Buyers & Institute - Start
					//Get Batch Session Completed Alert To Buyers Email Template
					$email_tpl = $this->base_model->fetch_records_from('email_templates', array('template_status' => 'Active', 'email_template_id' => '15'));

					if(!empty($email_tpl)) {

						$email_tpl = $email_tpl[0];

						$seller_rec 	 = getUserRec($batch_det->seller_id);
						$inst_rec 	 = getUserRec($batch_det->inst_id);

						$cnt = 1;
						foreach($batch_det_recs as $row) {

							$buyer_rec = getUserRec($row->buyer_id);

							if(!empty($email_tpl->from_email)) {

								$from = $email_tpl->from_email;

							} else {

								$from 	= $seller_rec->email;
							}

							$to 	= $buyer_rec->email;
							if($cnt++ == count($batch_det_recs))
								$to .= $inst_rec->email;

							if(!empty($email_tpl->template_subject)) {

								$sub = $email_tpl->template_subject;

							} else {

								$sub = get_languageword("Book Completed for the Batch");
							}

							if(!empty($email_tpl->template_content)) {


								$logo_img='<img src="'.get_site_logo().'" class="img-responsive" width="120px" height="50px">';

								$site_title = $this->config->item('site_settings')->site_title;


								$original_vars  = array($logo_img, $site_title, $buyer_rec->username, $seller_rec->username, $batch_det->batch_name." - ".$batch_det->batch_code, '<a href="'.URL_AUTH_LOGIN.'">'.get_languageword('Login Here').'</a>');

								$temp_vars		= array('__SITE_LOGO__', '__SITE_TITLE__', '__BUYER__NAME__', '__SELLER_NAME__', '__BATCH_NAME__', '__LOGINLINK__');


								$msg = str_replace($temp_vars, $original_vars, $email_tpl->template_content);

							} else {

								$msg = get_languageword('please')." <a href='".URL_AUTH_LOGIN."'>".get_languageword('Login Here')."</a> ".get_languageword('to view the details');
								$msg .= "<p>".get_languageword('Thank you')."</p>";
							}

							sendEmail($from, $to, $sub, $msg);
						}
					}
					//Email Alert to Batch Buyers - End

					$this->prepare_flashmessage(get_languageword('Book completed for the Batch successfully'), 0);
					redirect(URL_SELLER_MY_BATCHES.'/'.$book_id);

				} else {

					$this->prepare_flashmessage(get_languageword('Book not completed for the Batch due to some technical issue'), 2);
					redirect(URL_SELLER_MY_BATCHES.'/'.$book_id);
				}

			} else {

				$this->prepare_flashmessage(get_languageword('Batch Session not completed'), 2);
				redirect(URL_SELLER_MY_BATCHES.'/'.$book_id);
			}

		}

		$this->data['book_id'] 	= $book_id;
		$this->data['status_desc'] 	= $batch_det->status_desc;
		$this->data['batch_id'] 	= $batch_id;
		$this->data['message'] 		= $this->session->flashdata('message');
		$this->data['pagetitle'] 	= get_languageword('Update as Book Completed for the Batch')." (".$batch_det->batch_name." - ".$batch_det->batch_code.") ";
		$this->data['content']	 	= 'book_completed_for_batch_buyers';
		$this->data['activemenu']	= "enrolled_buyers";
		$this->_render_page('template/site/seller-template', $this->data);

	}


	/**
	 * [view_buyers description]
	 * @param  string $batch_id [description]
	 * @return [type]           [description]
	 */
	function view_buyers($batch_id = "")
	{
		if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_seller()) {
			$this->prepare_flashmessage(get_languageword('You dont have permission to access this page'), 1);
			redirect('auth/login', 'refresh');
		}

		if(!empty($batch_id))
		{
			$this->data['message'] = $this->session->flashdata('message');

			$user_id = $this->ion_auth->get_user_id();
			$inst_id = is_inst_seller($user_id);

			if(!$inst_id) {

				$this->prepare_flashmessage(get_languageword('You dont have permission to access this page'), 1);
				redirect('auth/login', 'refresh');
			}

			$this->load->library(array('grocery_CRUD'));
			$crud = new grocery_CRUD();
			$crud_state = $crud->getState();
			$crud->set_table($this->db->dbprefix('inst_enrolled_buyers'));
			$crud->where('batch_id', $batch_id);
			$crud->set_relation('buyer_id','users','username');
			$crud->set_relation('inst_id','users','username');

			$crud->set_subject( get_languageword('enrolled_buyer') );

			//unset actions
			$crud->unset_add();
			$crud->unset_edit();
			$crud->unset_delete();

			//display columns
			$crud->columns('buyer_id','batch_code','batch_name','batch_start_date','batch_end_date','duration_value','duration_type','book_offering_location', 'time_slot','status');

			//display names as
			$crud->display_as('buyer_id',get_languageword('buyer_name'));
			$crud->display_as('inst_id',get_languageword('Institute_Name'));
			$crud->display_as('enroll_id',get_languageword('book_duration')); 

			if($crud_state == "read") {

				$p_key = $this->uri->segment(5);

				$enroll_det = $this->base_model->fetch_records_from('inst_enrolled_buyers', array('enroll_id' => $p_key));

				if(!empty($enroll_det)) {

					$enroll_det = $enroll_det[0];

					if($enroll_det->seller_id != $user_id) {

						$this->prepare_flashmessage(get_languageword('not_authorized'), 1);
		    			redirect(URL_SELLER_VIEW_STUDETNS.'/'.$batch_id);
					}

				} else {

					$this->prepare_flashmessage(get_languageword('invalid_request'), 1);
		    		redirect(URL_SELLER_VIEW_STUDETNS.'/'.$batch_id);
				}

				$crud->unset_read_fields('fee', 'per_credit_value', 'admin_commission', 'admin_commission_val');
			}

			$output = $crud->render();


			$this->data['grocery_output'] = $output;
			$this->data['grocery'] = TRUE;

			$this->data['message'] = $this->session->flashdata('message');
			$this->data['pagetitle'] = get_languageword('enrolled_buyers');
			$this->data['activemenu'] 	= "my_batches";
			$this->grocery_output($this->data);
		}
		else
		{
			redirect( URL_SELLER_MY_BATCHES);
		}
		
	}


		
	/**
	 * Fecilitates to upload certificates
	 *
	 * @access	public
	 * @return	string
	 */
	function certificates()
	{
		if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_seller()) {
			$this->prepare_flashmessage(get_languageword('You dont have permission to access this page'), 1);
			redirect('auth/login', 'refresh');
		}
		
		$this->data['message'] = $this->session->flashdata('message');
		$user_id = $this->ion_auth->get_user_id();
		
		if(isset($_POST['submitbutt']))
		{
			//neatPrint($_FILES);
			
			if(count($_FILES['certificate']['name']) > 0)
			{
				foreach ($_FILES['certificate']['name'] as $i => $value)
				{
					if($_FILES['certificate']['name'][$i] != '')
					{
					
					$tmpFilePath = $_FILES['certificate']['tmp_name'][$i];
					$ext = pathinfo($_FILES['certificate']['name'][$i], PATHINFO_EXTENSION);
					$new_name = $user_id.'_'.$i.'.'.$ext;
					$filePath = './assets/uploads/certificates/'.$new_name;
						if(move_uploaded_file($tmpFilePath, $filePath))
						{
							if(in_array(strtolower($ext), array('jpg', 'png', 'gif', 'jpeg')))
							{
							$this->create_thumbnail($filePath,'./assets/uploads/certificates/thumbs/','40','40');
							}
							$user_image['admin_certificate_id'] = $i;
							$user_image['user_id']				= $user_id;
							if(isset($this->config->item('site_settings')->need_admin_for_seller) && $this->config->item('site_settings')->need_admin_for_seller == 'yes')
							$user_image['admin_status']			= 'Pending';
							else
							$user_image['admin_status']			= 'Approved';
							$user_image['certificate_type']		= 'admin';
							$user_image['certificate_name']		= $new_name;
							$user_image['file_type']		= $ext;
							
							$existed = $this->base_model->fetch_records_from('users_certificates',
														array('admin_certificate_id'=>$i,
														'user_id'=>$user_id,'certificate_type'=>'admin'));
							if(count($existed)>0)
							{
								$whr['user_certificate_id']			= $existed[0]->user_certificate_id;
								$this->base_model->update_operation($user_image,'users_certificates',$whr);
							}
							else
							{
							$this->base_model->insert_operation($user_image,'users_certificates');	
							}
						}
					}
				}
				
				if(count($_FILES['other']['name']) > 0)
				{
					$n=0;
					if(count($_FILES['other']['name']) > 0)
					{
						$n=0;
						for($i=0; $i<count($_FILES['other']['name']); $i++) 
						{					
							$n++;
							 //Get the temp file path
							$tmpFilePath = $_FILES['other']['tmp_name'][$i];			
							
							 //Make sure we have a filepath
							if($tmpFilePath != "")
							{
								//save the filename
								$shortname = $user_id.'_'.str_replace(' ','_',rand().'_'.$_FILES['other']['name'][$i]);
								$ext = pathinfo($_FILES['other']['name'][$i], PATHINFO_EXTENSION);
								//$filename = 'other_'.$n.'.'.$ext;
								//save the url and the file
								$filePath = './assets/uploads/certificates/'.$shortname;
								//Upload the file into the temp dir
								if(move_uploaded_file($tmpFilePath, $filePath)) 
								{								
									$user_image['user_id']				= $user_id;
									$user_image['admin_certificate_id'] = 0;
									if(isset($this->config->item('site_settings')->need_admin_for_seller) && $this->config->item('site_settings')->need_admin_for_seller == 'yes')
									$user_image['admin_status']			= 'Pending';
									else
									$user_image['admin_status']			= 'Approved';
									$user_image['certificate_type']		= 'other';
									$user_image['certificate_name']		= $shortname;
									$user_image['file_type']		= $ext;									
									$this->base_model->insert_operation($user_image,'users_certificates');
								}
							}
						}
					}
				}
			}
			$this->prepare_flashmessage(get_languageword('Certificates uploaded successfully'), 0);
			redirect('seller/certificates');
		}

		$certificates = $this->base_model->fetch_records_from('certificates', array('certificate_for' => 'sellers', 'status' => 'Active'));
		$this->data['certificates'] 	= $certificates;
		
		$user_uploads = $this->base_model->fetch_records_from('users_certificates', array('user_id' => $user_id));
		$user_uploads_arr = array();
		if(!empty($user_uploads))
		{
			foreach($user_uploads as $up)
			{
				$user_uploads_arr[$up->admin_certificate_id] = $up->certificate_name;
			}
		}
		$this->data['user_uploads_arr'] 	= $user_uploads_arr;
		
		$this->data['activemenu'] 	= "manage";
		$this->data['activesubmenu'] = 'certificates';
		$this->data['content'] 		= 'certificates';
		$this->data['pagetitle']	= get_languageword('manage_certificates');
		$this->_render_page('template/site/seller-template', $this->data);
	}
		
		
	//Need to implement
	function membership()
	{
		$this->data['activemenu'] 	= "home";		
		$this->data['content'] 		= 'membership';
		$this->_render_page('template/site/seller-template', $this->data);
	}
	
	/**
	 * [preferences description]
	 * @return [type] [description]
	 */
	function preferences()
	{
		$this->data['activemenu'] 	= "home";		
		$this->data['content'] 		= 'preferences';
		$this->_render_page('template/site/seller-template', $this->data);
	}
	
	/**
	 * [privacy_settings description]
	 * @return [type] [description]
	 */
	function privacy_settings()
	{
		$this->data['activemenu'] 	= "home";		
		$this->data['content'] 		= 'privacy_settings';
		$this->_render_page('template/site/seller-template', $this->data);
	}
		


	/**
	 * Fecilitates to update buyer leads
	 * @access	public
	 * @return	string
	 */
	function user_reviews()
	{
		if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_seller()) {
			$this->prepare_flashmessage(get_languageword('You dont have permission to access this page'), 1);
			redirect('auth/login', 'refresh');
		}

		$seller_id = $this->ion_auth->get_user_id();

		if(is_inst_seller($seller_id)) {

			$this->prepare_flashmessage(get_languageword('Invalid Request'), 1);
			redirect(URL_SELLER_INDEX);
		}

		$this->load->library(array('grocery_CRUD'));
		$crud = new grocery_CRUD();
		$crud_state = $crud->getState();
		$crud->set_table($this->db->dbprefix('seller_reviews'));
		$crud->where('seller_id', $seller_id);
		$crud->set_relation('buyer_id','users','username');
		$crud->set_relation('book_id','categories','name');
		$crud->set_subject( get_languageword('buyer_reviews') );

		$crud->unset_add();

		$crud->columns('buyer_id','book_id','comments','rating', 'created_at', 'updated_at','status');

		//########Edit fields only#######
		$crud->edit_fields('status');

		//####### Changing column names #######
		$crud->display_as('created_at','Posted Date');
		$crud->display_as('updated_at','Last Updated');
		$crud->display_as('book_id','Book Name');
		$crud->display_as('buyer_id','Buyer Name');
		$crud->display_as('rating', get_languageword('rating').' ('.get_languageword('out_of').' 5)');

		#### Invisible fileds in reading ####
		if ($crud->getState() == 'read') {
		    $crud->field_type('seller_id', 'hidden');
		}


		$output = $crud->render();

		$this->data['activemenu'] 	= "reviews";
		$this->data['pagetitle'] = get_languageword('reviews');
		$this->data['grocery_output'] = $output;
		$this->data['grocery'] = TRUE;
		$this->_render_page('template/site/seller-template-grocery', $this->data);
	}

	/**
	 * [buyer_enquiries description]
	 * @param  string $param [description]
	 * @return [type]        [description]
	 */
	function buyer_enquiries($param = "")
	{
		if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_seller()) {
			$this->prepare_flashmessage(get_languageword('You dont have permission to access this page'), 1);
			redirect('auth/login', 'refresh');
		}

		$this->data['message'] = $this->session->flashdata('message');

		$user_id = $this->ion_auth->get_user_id();
		$this->load->library(array('grocery_CRUD'));
		$crud = new grocery_CRUD();
		$crud_state = $crud->getState();
		$crud->set_table(TBL_BOOKINGS);
		$crud->set_relation('buyer_id',TBL_USERS, 'username');
		$crud->set_relation('updated_by',TBL_USERS, 'username');
		$crud->where(TBL_BOOKINGS.'.seller_id', $user_id);

		$status_arr = array('pending', 'approved', 'cancelled_before_book_started', 'cancelled_when_book_running', 'cancelled_after_book_completed', 'session_initiated', 'running', 'completed', 'called_for_admin_intervention', 'closed');
		if(in_array($param, $status_arr)) {

			$crud->where(TBL_BOOKINGS.'.status', $param);
		}

		$crud->set_subject( get_languageword('booking_status') );

		//Unset Actions
		$crud->unset_add();
		$crud->unset_delete();

		//List Table Columns
		$crud->columns('buyer_id', 'book_id', 'book_duration', 'fee', 'admin_commission_val','content', 'start_date', 'time_slot', 'preferred_location', 'status', 'payment_status');

		/*****05-12-2018*********/
		if( $param == 'session_initiated' || $param == 'running' ) {
			$crud->add_action(get_languageword('join'), '', '', 'fa fa-mixcloud', array($this, 'join_link') );
			
		}
		/*****05-12-2018*********/


		$crud->callback_column('book_duration',array($this,'_callback_book_duration'));
		$crud->callback_column('book_id',array($this,'_callback_book_id'));

		if($crud_state =="read")
			$crud->set_relation('book_id','categories','name');
		//Display Alias Names
		$crud->display_as('status',get_languageword('booking_status'));
		$crud->display_as('buyer_id',get_languageword('buyer_name'));
		$crud->display_as('book_id',get_languageword('book_seeking'));
		$crud->display_as('fee',get_languageword('fee').' ('.get_languageword('in_credits').')');
		$crud->display_as('admin_commission_val',get_languageword('admin_commission_val').' ('.get_languageword('in_credits').')');
		$crud->display_as('admin_commission',get_languageword('admin_commission_percentage').' ('.get_languageword('with_credits').')');
		$crud->display_as('per_credit_value',get_languageword('per_credit_value')." (".get_system_settings('currency_symbol').")");
		$crud->display_as('start_date',get_languageword('preferred_commence_date'));



		if($param == "closed") {

			$crud->callback_column('payment_status', array($this, 'callback_payment_status'));

			$crud->add_action(get_languageword('send_credits_conversion_request'), URL_FRONT_IMAGES.'/money.png', URL_SELLER_SEND_CREDITS_CONVERSION_REQUEST.'/');

			$crud->add_action(get_languageword('issue_certificate'), '', URL_SELLER_ISSUE_CERTIFICATE.'/', 'fa fa-certificate');

		} else {
			$crud->unset_columns('payment_status');
		}

		//Form fields for Edit Record
		$crud->edit_fields('status', 'status_desc', 'updated_at', 'prev_status');

		//Hidden Fields
		$crud->field_type('updated_at', 'hidden', date('Y-m-d H:i:s'));

		//Unset Fields
		$crud->unset_fields('seller_id', 'admin_commission_val');


		//Authenticate whether Seller editing/viewing his records only
		if($crud_state == "edit" || $crud_state == "read") {

			if($param != "" && $param != "add" && $param != "edit" && $param != "read" && $param != "success")
				$p_key = $this->uri->segment(4);
			else
				$p_key = $this->uri->segment(3);

			$booking_det = $this->base_model->fetch_records_from('bookings', array('booking_id' => $p_key));

			if(!empty($booking_det)) {

				$booking_det = $booking_det[0];

				if($booking_det->seller_id != $user_id) {

					$this->prepare_flashmessage(get_languageword('not_authorized'), 1);
	    			redirect(URL_SELLER_BUYER_ENQUIRIES);
				}

				if($crud_state == "edit") {

					$booking_status = $booking_det->status;
					$updated_by = getUserType($booking_det->updated_by);

					$crud->field_type('prev_status', 'hidden', $booking_status);

					$crud->display_as('status', get_languageword('change_status'));

					if(in_array($booking_status, array('pending', 'approved', 'session_initiated', 'running', 'completed'))) {
						$crud->required_fields(array('status'));
					}

					if($booking_status == "pending") {
						$crud->field_type('status', 'dropdown', array('approved' => get_languageword('approve'), 'cancelled_before_book_started' => get_languageword('cancel')));
					}

					if($booking_status == "approved") {

						$status = array('cancelled_before_book_started' => get_languageword('cancel'));

						$today = date('Y-m-d');

						if((strtotime($booking_det->start_date) <= strtotime($today)) && (strtotime($today) <= strtotime($booking_det->end_date))) {

							$cur_time 	= (float)date('H.i');
							$time_slot 	= str_replace(':', '.', $booking_det->time_slot);
							$time 	  	= explode('-', str_replace(' ', '', $time_slot));
							$start_time = date('H:i', strtotime(number_format($time[0],2)));
							$end_time   = date('H:i', strtotime(number_format($time[1],2)));

							$certain_mins_before_start_time = (float)date('H.i', strtotime($start_time.' -'.$this->config->item('site_settings')->enable_initiate_session_option_before_mins.' minutes'));
							$certain_mins_before_end_time 	= (float)date('H.i', strtotime($end_time.' -'.$this->config->item('site_settings')->enable_book_completed_option_before_mins.' minutes'));

							if ($cur_time >= $certain_mins_before_start_time) {
								$status = array('session_initiated' => get_languageword('initiate_session'), 'cancelled_before_book_started' => get_languageword('cancel'));
							}
						}

						$crud->field_type('status', 'dropdown', $status);

					}

					if($booking_status == "session_initiated") {

						$status = array('cancelled_before_book_started' => get_languageword('cancel'));
						$crud->field_type('status', 'dropdown', $status);
					}

					if($booking_status == "running") {

						$status = array('cancelled_when_book_running' => get_languageword('cancel'));

						$today = date('Y-m-d');

						if(strtotime($today) >= strtotime($booking_det->start_date)) {

							$status = array('completed' => get_languageword('book_completed'), 'cancelled_when_book_running' => get_languageword('cancel'));
						}

						$crud->field_type('status', 'dropdown', $status);

					}

					if($booking_status == "completed") {

						$status = array('called_for_admin_intervention' => get_languageword('claim_for_admin_intervention'));

						$crud->field_type('status', 'dropdown', $status);

					}

					if($booking_status == "called_for_admin_intervention" && $updated_by == "buyer") {

						if($booking_det->prev_status == "pending")
							$status['approved'] = get_languageword('approve');
						else if($booking_det->prev_status == "approved")
							$status['cancelled_before_book_started'] = get_languageword('cancel');
						else if($booking_det->prev_status == "running") {
							$status['running'] = get_languageword('continue_book');
							$status['cancelled_when_book_running'] = get_languageword('cancel');
						}
						else if($booking_det->prev_status == "cancelled_when_book_running") {
							$status['running'] = get_languageword('continue_book');
						}
						else if($booking_det->prev_status == "completed") {
							$status['running'] = get_languageword('continue_book');
							$status['cancelled_when_book_running'] = get_languageword('cancel');
						}

						$crud->required_fields(array('status'));
						$crud->field_type('status', 'dropdown', $status);

					} else if($booking_status == "called_for_admin_intervention" && ($updated_by == "seller"  || $updated_by == "admin")) {

						$crud->edit_fields('status_desc', 'updated_at');
					}


					if($booking_status == "cancelled_when_book_running" && $updated_by == "buyer") {

						$crud->required_fields(array('status'));

						$status = array('called_for_admin_intervention' => get_languageword('claim_for_admin_intervention'));

						$crud->field_type('status', 'dropdown', $status);
					}


					if($booking_status == "cancelled_after_book_completed" && $updated_by == "buyer") {

						$crud->required_fields(array('status'));

						$status = array('called_for_admin_intervention' => get_languageword('claim_for_admin_intervention'));

						$crud->field_type('status', 'dropdown', $status);
					}

					if($booking_status == "closed" || $booking_status == "cancelled_before_book_started" || ($booking_status == "cancelled_when_book_running" && $updated_by == "seller") || ($booking_status == "cancelled_after_book_completed" && $updated_by == "seller")) {

						$crud->edit_fields('status_desc', 'updated_at');

					}

				}

			} else {

				$this->prepare_flashmessage(get_languageword('invalid_request'), 1);
	    		redirect(URL_SELLER_BUYER_ENQUIRIES);
			}

		}


		if($crud_state == "read") {

			$crud->field_type('updated_at', 'visibile');
		}


		//05-12-2018 admin notification start
		$methd = $this->uri->segment(3);
		
		if ($this->input->post('status')!="" && $this->input->post('prev_status')!="" && $methd=="update_validation") {
			 
			 $stus = ucwords(str_replace("_", " ", $this->input->post('status')));

			 $p_key = $this->uri->segment(4);
			 
			 $booking_details = $this->base_model->fetch_records_from('bookings', array('booking_id' => $p_key));
			 
			if(!empty($booking_details)) {

				$booking_details = $booking_details[0];

				$data = array();
				$data['user_id'] 	= $user_id;
				$data['title'] 		= get_languageword('seller_changed_book_status').' to '. $stus;
				$data['content'] 	= "Seller has changed book status to "." ".$stus;
				$data['datetime']   = date('Y-m-d H:i:s');
				$data['admin_read'] = 0;
				$data['page_link']  = SITEURL.'admin/buyer-bookings/read/'.$booking_details->booking_id;
				$data['table_name'] = "bookings";
				$data['primary_key_column'] = "booking_id";
				$data['primary_key_value']  = $booking_details->booking_id;


				
				$this->base_model->insert_operation($data,'notifications');	
				unset($data);
			}
		}
		//05-12-2018 admin notification end

		$crud->callback_column('preferred_location', array($this, 'callback_column_preferred_location'));
		$crud->callback_column('status', array($this, 'callback_column_booking_status'));

		$crud->callback_update(array($this,'callback_send_email'));

		$output = $crud->render();

		$param = get_languageword($param);
		$this->data['pagetitle'] = get_languageword('bookings');
		$this->data['activemenu'] 	= "enquiries";
		$this->data['activesubmenu'] = $param;

		$this->data['grocery_output'] = $output;
		$this->data['grocery'] = TRUE;
		$this->grocery_output($this->data);
	}

	/**
	 * [join_link description]
	 * @param  [type] $primary_key [description]
	 * @param  [type] $row         [description]
	 * @return [type]              [description]
	 */
	function join_link( $primary_key , $row ) {
		$user_id = $this->ion_auth->get_user_id();
		$now = date('Y-m-d');
		
		$query = 'SELECT b.*, book.name book_name FROM ' . TBL_BOOKINGS . ' b 
			INNER JOIN ' . TBL_USERS . ' st ON b.buyer_id = st.id ' . 
			'INNER JOIN ' . TBL_USERS . ' tu ON b.seller_id = tu.id ' . 
			'INNER JOIN ' . TBL_CATEGORIES . ' book ON b.book_id = book.id ' .
			'WHERE (b.status = "session_initiated" OR b.status="running") AND "'.$now.'" BETWEEN start_date AND end_date AND b.seller_id = ' . $user_id .' AND booking_id = ' . $primary_key;
		// echo $query;
		$booking_details = $this->db->query( $query )->result();
		//print_r($booking_details);
		$link = '#';
		if( count( $booking_details ) > 0  && $booking_details[0]->preferred_location == 'online-bbb' ) {
			$link = URL_VIRTUAL_CLASS . '/init/'.$primary_key;
		}
		return $link;
	}
	

	function _callback_book_id($primary_key , $row)
	{

	  $book_name = $this->base_model->fetch_value('categories', 'name', array('id' => $row->book_id));
	   return $book_name;
	}

	function callback_column_booking_status($primary_key , $row)
	{

	    return humanize($row->status);
	}

	function callback_column_preferred_location($primary_key , $row)
	{

	    return humanize($row->preferred_location);
	}

	function callback_payment_status($val, $row)
	{
		$user_id = $this->ion_auth->get_user_id();
		$payment_status = $this->base_model->fetch_records_from('admin_money_transactions', array('booking_id' => $row->booking_id, 'user_id' => $user_id, 'user_type' => 'seller'));
		if(!empty($payment_status))
			return $payment_status[0]->status_of_payment;
	}


	function callback_send_email($post_array, $primary_key)
	{
		$post_array['updated_by'] = $this->ion_auth->get_user_id();

		if($this->base_model->update_operation($post_array, 'bookings', array('booking_id' => $primary_key))) {

			$booking_det = $this->base_model->fetch_records_from('bookings', array('booking_id' => $primary_key));

			if(!empty($booking_det)) {

				$booking_det = $booking_det[0];

				$buyer_rec = getUserRec($booking_det->buyer_id);
				$seller_rec 	 = getUserRec($booking_det->seller_id);

				//If Seller Cancelled booking before session gets started, refund Buyer's Credits
				if($post_array['status'] == "cancelled_before_book_started") {

					//Log Credits transaction data & update user net credits - Start
					$log_data = array(
									'user_id' => $booking_det->buyer_id,
									'credits' => $booking_det->fee,
									'per_credit_value' => $booking_det->per_credit_value,
									'action'  => 'credited',
									'purpose' => 'Slot booked with the Seller "'.$seller_rec->username.'" has cancelled before book started',
									'date_of_action	' => date('Y-m-d H:i:s'),
									'reference_table' => 'bookings',
									'reference_id' => $primary_key,
								);

					log_user_credits_transaction($log_data);

					update_user_credits($booking_det->buyer_id, $booking_det->fee, 'credit');
					//Log Credits transaction data & update user net credits - End
				}


				//If Seller approves the booking send Buyer's address to Seller
				//Email Alert to Seller - Start
				//Get Send Buyer's Address Email Template
				if($post_array['status'] == "approved" && $booking_det->preferred_location == "home") {

					$email_tpl = $this->base_model->fetch_records_from('email_templates', array('template_status' => 'Active', 'email_template_id' => '6'));

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

							$sub = get_languageword("Buyer Address");
						}

						$buyer_addr = $buyer_rec->city.", <br />".$buyer_rec->land_mark.", <br />".$buyer_rec->country.", <br/>Phone: ".$buyer_rec->phone;

						$book_name = $this->base_model->fetch_value('categories', 'name', array('id' => $booking_det->book_id));

						if(!empty($email_tpl->template_content)) {

							$logo_img='<img src="'.get_site_logo().'" class="img-responsive" width="80px" height="50px">';

							$site_title = $this->config->item('site_settings')->site_title;



							$original_vars  = array($logo_img, $site_title, $seller_rec->username, $buyer_rec->username, $book_name, $booking_det->start_date." & ".$booking_det->time_slot, $booking_det->preferred_location, $buyer_addr, $site_title);


							$temp_vars		= array('__SITE_LOGO__', '__SITE_TITLE__', '__SELLER__NAME__', '__BUYER_NAME__', '__BOOK_NAME__', '__DATE_TIME__', '__LOCATION__', '__BUYER_ADDRESS__', '__SITE_TITLE__');

							$msg = str_replace($temp_vars, $original_vars, $email_tpl->template_content);

						} else {

							$msg = "<p>
										".get_languageword('hello')." ".$seller_rec->username.",</p>
									<p>
										".get_languageword('You approved Buyer')." &quot;".$buyer_rec->username."&quot; ".get_languageword('booking for the book')." &quot;".$book_name."&quot;</p>
									<p>
										".get_languageword('for the timeslot')." &quot;".$booking_det->start_date." & ".$booking_det->time_slot."&quot; and &quot; ".$booking_det->preferred_location."&quot; ".get_languageword('as preferred location for sessions').".</p>
									<p>
										".get_languageword('Below is the address of the Buyer')."</p>
									<p>
										".$buyer_addr."</p>";

							$msg .= "<p>".get_languageword('Thank you')."</p>";
						}

						sendEmail($from, $to, $sub, $msg);
					}
					//Email Alert to Seller - End
				}


				//If Seller initiates the session send email alert to Buyer
				//Email Alert to Buyer - Start
				//Get SEssion Initiated Email Template
				if($post_array['status'] == "session_initiated") {

					$email_tpl = $this->base_model->fetch_records_from('email_templates', array('template_status' => 'Active', 'email_template_id' => '11'));

					if(!empty($email_tpl)) {

						$email_tpl = $email_tpl[0];

						if(!empty($email_tpl->from_email)) {

							$from = $email_tpl->from_email;

						} else {

							$from 	= get_system_settings('Portal_Email');
						}

						$to 	= $buyer_rec->email;

						if(!empty($email_tpl->template_subject)) {

							$sub = $email_tpl->template_subject;

						} else {

							$sub = get_languageword("Session Initiated By Seller");
						}

						$book_name = $this->base_model->fetch_value('categories', 'name', array('id' => $booking_det->book_id));

						if(!empty($email_tpl->template_content)) {


							$logo_img='<img src="'.get_site_logo().'" class="img-responsive" width="120px" height="50px">';

							$site_title = $this->config->item('site_settings')->site_title;


							$original_vars  = array($logo_img, $site_title, $buyer_rec->username, $seller_rec->username, $book_name, $booking_det->start_date." & ".$booking_det->time_slot, $booking_det->preferred_location, '<a href="'.URL_AUTH_LOGIN.'">'.get_languageword('Login Here').'</a>', $site_title);

							$temp_vars		= array('__SITE_LOGO__', '__SITE_TITLE__', '__BUYER_NAME__', '__SELLER_NAME__', '__BOOK_NAME__', '__DATE_TIME__', '__LOCATION__', '__LOGIN_LINK__', '__SITE_TITLE__');


							$msg = str_replace($temp_vars, $original_vars, $email_tpl->template_content);

						} else {

							$msg = "<p>
										".get_languageword('hello')." ".$buyer_rec->username.",</p>
									<p>
										".get_languageword('Seller initiated the session Please start the session by logging in here')."<a href='".URL_AUTH_LOGIN."'>".get_languageword('Login Here')."</a></p>";

							$msg .= "<p>".get_languageword('Thank you')."</p>";
						}

						sendEmail($from, $to, $sub, $msg);
					}
					//Email Alert to Buyer - End
				}
			}

			return TRUE;

		} else return FALSE;
	}


	/**
	 * [send_credits_conversion_request description]
	 * @param  string $booking_id [description]
	 * @return [type]             [description]
	 */
	function send_credits_conversion_request($booking_id = "")
	{
		if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_seller()) {
			$this->prepare_flashmessage(get_languageword('You dont have permission to access this page'), 1);
			redirect('auth/login', 'refresh');
		}

		$booking_id = ($this->input->post('booking_id')) ? $this->input->post('booking_id') : $booking_id;

		if(empty($booking_id)) {

			$this->prepare_flashmessage(get_languageword('Please complete your book to send credit conversion request'), 2);
			redirect(URL_SELLER_BUYER_ENQUIRIES);
		}

		$user_id = $this->ion_auth->get_user_id();

		//Check whether booking exists
		$booking_det = $this->base_model->fetch_records_from('bookings', array('booking_id' => $booking_id, 'seller_id' => $user_id, 'status' => 'closed'));

		if(empty($booking_det)) {

			$this->prepare_flashmessage(get_languageword('Invalid request'), 1);
			redirect(URL_SELLER_BUYER_ENQUIRIES);
		}

		//Check whether already sent request
		$payment_status = $this->base_model->fetch_records_from('admin_money_transactions', array('booking_id' => $booking_id, 'user_id' => $user_id, 'user_type' => 'seller'));

		if(!empty($payment_status)) {

			$this->prepare_flashmessage(get_languageword('Already sent the request And status of the payment is ').$payment_status[0]->status_of_payment, 1);
			redirect(URL_SELLER_BUYER_ENQUIRIES.'/closed');
		}


		$booking_det = $booking_det[0];
		$user_rec 	 = getUserRec($user_id);

		$inputdata['user_id'] 						= $user_id;
		$inputdata['booking_id'] 					= $booking_id;
		$inputdata['user_type'] 					= 'seller';
		$inputdata['user_name'] 					= $user_rec->username;
		$inputdata['user_paypal_email'] 			= $user_rec->paypal_email;
		$inputdata['user_bank_ac_details'] 			= $user_rec->bank_ac_details;
		$inputdata['no_of_credits_to_be_converted'] = $booking_det->fee-$booking_det->admin_commission_val;
		$inputdata['admin_commission_val'] 			= $booking_det->admin_commission_val;
		$inputdata['per_credit_cost'] 				= $booking_det->per_credit_value;
		$inputdata['total_amount'] 					= $inputdata['no_of_credits_to_be_converted'] * $inputdata['per_credit_cost'];
		$inputdata['created_at'] 					= date('Y-m-d H:i:s');
		$inputdata['updated_at'] 					= $inputdata['created_at'];
		$inputdata['updated_by'] 					= $user_id;


		//admin notification
		$request_id = $this->base_model->insert_operation_id($inputdata, 'admin_money_transactions');
		if ($request_id) {

			//05-12-2018 admin notification start 
			$data = array();
			$data['user_id'] 	= $user_id;
			$data['title'] 		= get_languageword('seller_sent_money_request');
			$data['content'] 	= "Seller has been sent money request of credits "." ".$inputdata['no_of_credits_to_be_converted'];
			$data['datetime']   = date('Y-m-d H:i:s');
			$data['admin_read'] = 0;
			$data['page_link']  = SITEURL."admin/seller-money-conversion-requests/Pending/read/".$request_id;
			$data['table_name'] = "admin_money_transactions";
			$data['primary_key_column'] = "id";
			$data['primary_key_value']  = $request_id;

			$this->base_model->insert_operation($data,'notifications');	
			unset($data);
			//admin notification end
			
			$this->prepare_flashmessage(get_languageword('Credits to Money conversion request sent successfully'), 0);
			redirect(URL_SELLER_CREDIT_CONVERSION_REQUESTS);

		} else {

			$this->prepare_flashmessage(get_languageword('Somthing went wrong Your request not sent'), 2);
			redirect(URL_SELLER_BUYER_ENQUIRIES);
		}

	}


	// purchased book credit cpnversion request
	function send_book_credits_conversion_request($booking_id="") {

		if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_seller()) {
			$this->prepare_flashmessage(get_languageword('You dont have permission to access this page'), 1);
			redirect('auth/login', 'refresh');
		}

		$booking_id = ($this->input->post('booking_id')) ? $this->input->post('booking_id') : $booking_id;

		if(empty($booking_id)) {

			$this->prepare_flashmessage(get_languageword('Please complete your book to send credit conversion request'), 2);
			redirect(URL_SELLER_PURCHASED_BOOKS);
		}

		$user_id = $this->ion_auth->get_user_id();

		
		$booking_det = $this->base_model->fetch_records_from('book_purchases', array('purchase_id' => $booking_id, 'seller_id' => $user_id, 'status_of_payment_to_seller' => 'Pending'));
		

		//Check whether booking exists
		
		
		if(empty($booking_det)) {

			$this->prepare_flashmessage(get_languageword('Invalid request'), 1);
			redirect(URL_SELLER_PURCHASED_BOOKS);
		}

		//Check whether already sent request
		$payment_status = $this->base_model->fetch_records_from('admin_money_transactions', array('booking_id' => $booking_id, 'user_id' => $user_id, 'user_type' => 'seller','booking_type'=>'book_purchase'));

		if(!empty($payment_status)) {

			$this->prepare_flashmessage(get_languageword('Already sent the request And status of the payment is ').$payment_status[0]->status_of_payment, 1);
			redirect(URL_SELLER_PURCHASED_BOOKS);
		}


		$booking_det = $booking_det[0];
		$user_rec 	 = getUserRec($user_id);

		$inputdata['user_id'] 						= $user_id;
		$inputdata['booking_id'] 					= $booking_id;
		$inputdata['booking_type'] 					= 'book_purchase';
		$inputdata['user_type'] 					= 'seller';
		$inputdata['user_name'] 					= $user_rec->username;
		$inputdata['user_paypal_email'] 			= $user_rec->paypal_email;
		$inputdata['user_bank_ac_details'] 			= $user_rec->bank_ac_details;
		$inputdata['no_of_credits_to_be_converted'] = $booking_det->book_credits-$booking_det->admin_commission_val;
		$inputdata['admin_commission_val'] 			= $booking_det->admin_commission_val;
		$inputdata['per_credit_cost'] 				= $booking_det->per_credit_value;
		$inputdata['total_amount'] 					= $inputdata['no_of_credits_to_be_converted'] * $inputdata['per_credit_cost'];
		$inputdata['created_at'] 					= date('Y-m-d H:i:s');
		$inputdata['updated_at'] 					= $inputdata['created_at'];
		$inputdata['updated_by'] 					= $user_id;


		$request_id = $this->base_model->insert_operation_id($inputdata, 'admin_money_transactions');
		if ($request_id) {


			//admin notification start
			$data = array();
			$data['user_id'] 	= $user_id;
			$data['title'] 		= get_languageword('seller_money_request');
			$data['content'] 	= "Seller has been sent money request of credits "." ".$inputdata['no_of_credits_to_be_converted'];
			$data['datetime']   = date('Y-m-d H:i:s');
			$data['admin_read'] = 0;
			$data['page_link']  = SITEURL."admin/seller-money-conversion-requests/Pending";
			$data['table_name'] = "admin_money_transactions";
			$data['primary_key_column'] = "id";
			$data['primary_key_value']  = $request_id;

			$this->base_model->insert_operation($data,'notifications');	
			unset($data);
			//admin notification end

			$this->prepare_flashmessage(get_languageword('Credits to Money conversion request sent successfully'), 0);
			redirect(URL_SELLER_CREDIT_CONVERSION_REQUESTS);

		} else {

			$this->prepare_flashmessage(get_languageword('Somthing went wrong Your request not sent'), 2);
			redirect(URL_SELLER_PURCHASED_BOOKS);
		}
	}

	/**
	 * [credits_transactions_history description]
	 * @return [type] [description]
	 */
	function credits_transactions_history()
	{
		if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_seller()) {
			$this->prepare_flashmessage(get_languageword('You dont have permission to access this page'), 1);
			redirect('auth/login', 'refresh');
		}

		$seller_id = $this->ion_auth->get_user_id();
		$this->load->library(array('grocery_CRUD'));
		$crud = new grocery_CRUD();
		$crud_state = $crud->getState();
		$crud->set_table($this->db->dbprefix('user_credit_transactions'));
		$crud->where('user_id', $seller_id);
		$crud->order_by('id','desc');
		
		$crud->set_subject( get_languageword('user_credit_transactions') );

		$crud->unset_add();
		$crud->unset_delete();
		$crud->unset_edit();



		$crud->columns('id','trans_id','credits','action','purpose','date_of_action');

		$crud->unset_read_fields('user_id', 'reference_table', 'reference_id', 'per_credit_value');

		$output = $crud->render();

		$this->data['activemenu'] 	= "user_credit_transactions";
		$this->data['pagetitle'] = get_languageword('user_credit_transactions');
		$this->data['grocery_output'] = $output;
		$this->data['grocery'] = TRUE;
		$this->data['withdraw_credits_btn'] = TRUE;
		$this->grocery_output($this->data);
	}

	/**
	 * [credit_conversion_requests description]
	 * @param  string $param [description]
	 * @return [type]        [description]
	 */
	function credit_conversion_requests($param = "Pending")
	{

		if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_seller()) {
		$this->prepare_flashmessage(get_languageword('You dont have permission to access this page'), 1);
		redirect('auth/login', 'refresh');
		}

		$user_id = $this->ion_auth->get_user_id();

		$this->load->library(array('grocery_CRUD_extended'));
		$crud = new grocery_CRUD_extended();
		$crud_state = $crud->getState();

		$crud->set_table($this->db->dbprefix('admin_money_transactions'));
		$crud->where('user_id',$user_id);
		$crud->where('status_of_payment', $param);

		//unset actions
		$crud->unset_add();
		$crud->unset_edit();
		$crud->unset_delete();


		//display columns    			
		$crud->columns('user_paypal_email', 'no_of_credits_to_be_converted', 'per_credit_cost', 'total_amount', 'status_of_payment', 'updated_at');

		$crud->set_read_fields('user_paypal_email', 'total_amount', 'status_of_payment', 'transaction_details', 'updated_at');

		$currency_symbol = $this->config->item('site_settings')->currency_symbol;
		$crud->display_as('no_of_credits_to_be_converted', get_languageword('credits_acquired'));
		$crud->display_as('per_credit_cost', get_languageword('per_credit_cost')." (".$currency_symbol.")");
		$crud->display_as('total_amount', get_languageword('total_amount')." (".$currency_symbol.")");


		// $crud->callback_column('booking_id',array($this,'callback_booking_id'));

		$output = $crud->render();

		$this->data['activemenu'] 	= "credit_conversion_requests";
		$this->data['activesubmenu'] 	= $param;
		$this->data['pagetitle'] = get_languageword('credit_conversion_requests');
		$this->data['grocery_output'] = $output;
		$this->data['grocery'] = TRUE;
		$this->grocery_output($this->data);	

	}


	function callback_booking_id($value, $row)
	{
		return '<a href="'.URL_SELLER_BUYER_ENQUIRIES.'/read/'.$row->booking_id.'">'.$row->booking_id.'</a>';
	}





	/**
	 * Facilitates to set selling books information
	 *
	 * @access	public
	 * @return	string
	 */
	function sell_books_online($sc_id = "")
	{
		//echo phpinfo(); die;

		/*$file_name = 'test.zip';
		$response = common_s3_function('get_and_save', 'zip', $file_name);
		echo '<pre> $response :: '; print_r($response); die;*/

		/*$file_path = 'assets/uploads/book_curriculum_files/';
		//$file_path = base_url() . 'assets/uploads/book_curriculum_files/';
		//$file_path = URL_PUBLIC_UPLOADS. 'book_curriculum_files/';
		//$file_name = '60_1_20221203085411362963832_test.mp3';
		$file_name = '1_17.zip';
		$response = common_s3_function('set', 'zip', $file_name, $file_path);
		echo '<pre> $response :: '; print_r($response); die;*/

		if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_seller()) {
			$this->prepare_flashmessage(get_languageword('You dont have permission to access this page'), 1);
			redirect('auth/login', 'refresh');
		}

		$this->data['message'] = $this->session->flashdata('message');

		$seller_id = $this->ion_auth->get_user_id();

		if(is_inst_seller($seller_id)) {

			$this->prepare_flashmessage(get_languageword('Invalid Request'), 1);
			redirect(URL_SELLER_INDEX);
		}

		$book_temp_upload_path = 'assets/uploads/book_curriculum_files/';

		//Edit Operation
		if(!empty($sc_id)) {
			$book_image_arr = [];

			$record = get_seller_sellingbook_info($sc_id);

			//echo '<pre> $record :: '; print_r($record); die;

			if(empty($record)) {
				$this->prepare_flashmessage(get_languageword('No Details Found'), 2);
				redirect(URL_SELLER_LIST_SELLING_BOOKS);
			}

			$bookImageBody = '';

			$bookPreviewImageBody = '';

			$bookPreviewFileBody = '';
			$bookPreviewFileName = '';

			$bookPreviewFileMimetype = '';
			$bookPreviewFilepresignedUrl = '';

			/*try {
				if(isset($record->image) && $record->image != '') {
					$getBookImage = $this->s3Client->getObject([
									'Bucket' => $this->bucket_name,
									'Key' => $this->book_s3_upload_path.$record->image,
								]);

					$bookImageBody = $getBookImage->get('Body');
				}
			} catch (Aws\S3\Exception\S3Exception $e) {
				//echo $e->getMessage();
				$this->prepare_flashmessage(get_languageword("There is an error in fetching Book Image from S3 server"), 2);
			}

			try {
				if(isset($record->preview_image) && $record->preview_image != '') {
					$getBookPreviewImage = $this->s3Client->getObject([
									'Bucket' => $this->bucket_name,
									'Key' => $this->book_s3_upload_path.$record->preview_image,
								]);

					$bookPreviewImageBody = $getBookPreviewImage->get('Body');
				}
			} catch (Aws\S3\Exception\S3Exception $e) {
				//echo $e->getMessage();
				$this->prepare_flashmessage(get_languageword("There is an error in fetching Book Preview Image from S3 server"), 2);
			}

			try {
				if(isset($record->preview_file) && $record->preview_file != '') {
					$bookPreviewFile = $this->s3Client->getObject([
									'Bucket' => $this->bucket_name,
									'Key' => $this->book_s3_upload_path.$record->preview_file,
								]);

					$bookPreviewFileBody = $bookPreviewFile->get('Body');
				}
			} catch (Aws\S3\Exception\S3Exception $e) {
				//echo $e->getMessage();
				$this->prepare_flashmessage(get_languageword("There is an error in fetching Book Preview File from S3 server"), 2);
			}*/

			if(isset($record->image) && $record->image != '') {
				$response = common_s3_function('get', 'file', $record->image);

				if(!empty($response) && $response['status'] == 'success') {
					$bookImageBody = $response['message'];
				} else {
					$this->prepare_flashmessage(get_languageword("There is an error in fetching Book Image from S3 server"), 2);
				}
			}

			if(isset($record->preview_image) && $record->preview_image != '') {
				$response = common_s3_function('get', 'file', $record->preview_image);

				if(!empty($response) && $response['status'] == 'success') {
					$bookPreviewImageBody = $response['message'];
				} else {
					$this->prepare_flashmessage(get_languageword("There is an error in fetching Book Preview Image from S3 server"), 2);
				}
			}

			if(isset($record->preview_file) && $record->preview_file != '') {
				$response = common_s3_function('get', 'file', $record->preview_file);

				if(!empty($response) && $response['status'] == 'success') {
					$bookPreviewFileBodyMimetype = base64_decode($response['message']);
					$bookPreviewFileName = $record->preview_file;

					$f = finfo_open();
					$mime_type = finfo_buffer($f, $bookPreviewFileBodyMimetype, FILEINFO_MIME_TYPE);

					$bookPreviewFileMimetype = $mime_type;
					$bookPreviewFileBody = $response['message'];
					$bookPreviewFilepresignedUrl = $response['presignedUrl'];
				} else {
					$this->prepare_flashmessage(get_languageword("There is an error in fetching Book Preview File from S3 server"), 2);
				}
			}

			$this->data['record'] = $record;

			$this->data['record']->book_image_arr['image'] = $bookImageBody;

			$this->data['record']->book_image_arr['preview_image'] = $bookPreviewImageBody;

			$this->data['record']->book_image_arr['preview_file'] = $bookPreviewFileBody;
			$this->data['record']->book_image_arr['preview_file_name'] = $bookPreviewFileName;

			$this->data['record']->book_image_arr['preview_file_mimetype'] = $bookPreviewFileMimetype;
			$this->data['record']->book_image_arr['preview_file_presignedUrl'] = $bookPreviewFilepresignedUrl;
		}

		//echo '<pre> $this->input->post() :: '; print_r($this->input->post());

		$error = false;

		if($this->input->post()) {
			$total_curriculum_titles 	= count(array_filter($this->input->post('lesson_title')));
			$total_curriculum_files 	= (!empty($_FILES['lesson_file']['name'])) ? count(array_filter($_FILES['lesson_file']['name'])) : 0;
			$total_curriculum_urls 		= ($this->input->post('lesson_url')) ? count(array_filter($this->input->post('lesson_url'))) : 0;

			$remove_curriculums         = (!empty($this->input->post('removed_curriculum'))) ? explode(',',$this->input->post('removed_curriculum')) : [];

			$total_exam_questions	 	= count(array_filter($this->input->post('question')));
			$total_exam_answers         = count(array_filter($this->input->post('answer')));

			$remove_exams	 	        = (!empty($this->input->post('removed_exam'))) ? explode(',', $this->input->post('removed_exam')) : [];

			$this->form_validation->set_rules('category_id', get_languageword('Category'), 'trim|required|xss_clean');
			$this->form_validation->set_rules('book_name', get_languageword('Book_Name'), 'trim|required|xss_clean');
			$this->form_validation->set_rules('book_title', get_languageword('Book_Title'), 'trim|required|xss_clean');
			$this->form_validation->set_rules('description', get_languageword('Description'), 'trim|required|xss_clean');
			$this->form_validation->set_rules('skill_level', get_languageword('Skill_Level'), 'trim|xss_clean');

			$this->form_validation->set_rules('lesson_url[]', get_languageword('Curriculum_Source'), 'trim|xss_clean|valid_url|prep_url');

			$this->form_validation->set_rules('actual_price', get_languageword('Actual_Price'), 'trim|required|numeric|xss_clean');

			$this->form_validation->set_rules('book_price', get_languageword('Discount_Price'), 'trim|required|numeric|xss_clean');
			$this->form_validation->set_rules('max_downloads', get_languageword('Maximum_number_of_Downloads'), 'trim|required|integer|xss_clean');
			$this->form_validation->set_rules('status', get_languageword('status'), 'trim|required|xss_clean');

			$this->form_validation->set_error_delimiters('<div class="error">', '</div>');

			if ($this->form_validation->run() == TRUE) {
				$valid = 1;

				if($total_curriculum_titles != 0 && ($total_curriculum_files != 0 && $total_curriculum_urls != 0)) {
					// $valid = 0;

					if($total_curriculum_titles == 0) {
						if($this->input->post('sc_id'))
							$this->form_validation->set_rules('lesson_title[]', get_languageword('Curriculum_Titles'), 'trim|xss_clean');
						else
							$this->form_validation->set_rules('lesson_title[]', get_languageword('Curriculum_Titles'), 'trim|required|xss_clean');
					}
					if($total_curriculum_files == 0 || $total_curriculum_urls == 0) {
						if($this->input->post('sc_id'))
							$this->form_validation->set_rules('lesson_file[]', get_languageword('Curriculum_Source'), 'trim|xss_clean');
						else
							$this->form_validation->set_rules('lesson_file[]', get_languageword('Curriculum_Source'), 'trim|required|xss_clean');
					}
				} else if($total_curriculum_titles > 0 && $total_curriculum_files > 0) {
					//Check for atleast one valid file from uploaded
					$allowed_types = array('mp2', 'mp3', 'mp4', '3gp', 'pdf', 'ppt', 'pptx', 'doc', 'docx', 'rtf', 'rtx', 'txt', 'text', 'webm', 'aac', 'wav', 'wmv', 'flv', 'avi', 'ogg', 'jpg', 'jpeg', 'png', 'gif', 'svg', 'bmp');

					$uploaded_types = array();

					foreach ($_FILES['lesson_file']['name'] as $key => $value) {
						if(!empty($value))
							$uploaded_types[] = pathinfo($value, PATHINFO_EXTENSION);
					}

					if (!(count(array_intersect($allowed_types, $uploaded_types)) > 0)) {
						$valid = 0;
						$this->form_validation->set_rules('lesson_file[]', get_languageword('Curriculum_Source'), 'callback__msg_allowed_formats');
					}
				}

				if($total_exam_questions != 0) {
					if($total_exam_questions == 0) {
						if($this->input->post('sc_id'))
							$this->form_validation->set_rules('question[]', get_languageword('question'), 'trim|xss_clean');
						else
							$this->form_validation->set_rules('question[]', get_languageword('question'), 'trim|required|xss_clean');
					}
				}

				if($valid == 0) {
					$this->form_validation->run();
					$this->data['message'] = $this->prepare_message(validation_errors(), 1);
				} else {
					$inputdata['seller_id']		= $seller_id;
					$inputdata['category_id']	= $this->input->post('category_id');
					$inputdata['book_name']	= $this->input->post('book_name');
					$inputdata['book_title']	= $this->input->post('book_title');
					$inputdata['description']	= $this->input->post('description');
					$inputdata['skill_level']	= $this->input->post('skill_level');
					$inputdata['languages']		= implode(',', $this->input->post('languages'));
					$inputdata['actual_price']	= $this->input->post('actual_price');
					$inputdata['book_price']	= $this->input->post('book_price');
					$inputdata['max_downloads']	= $this->input->post('max_downloads');
					$inputdata['status']		= $this->input->post('status');
					$inputdata['admin_approved']= 'No';
					$inputdata['admin_commission_percentage']= $this->config->item('site_settings')->admin_commission_on_book_purchase;

					$book_image 	= $_FILES['book_image']['name'];
					$preview_image 	= $_FILES['preview_image']['name'];
					$preview_file  	= $_FILES['preview_file']['name'];

					$update_rec_id = $this->input->post('sc_id');

					if($update_rec_id > 0) { //Update Operation
						$prev_bookname = $this->base_model->fetch_value('seller_selling_books', 'book_name', array('sc_id' => $update_rec_id));

						//If user updates the username
						if($prev_bookname != $inputdata['book_name']) {
							$slug = prepare_slug($inputdata['book_name'], 'book_name', 'seller_selling_books');
							$inputdata['slug'] = $slug;
						}

						$inputdata['updated_at'] = date('Y-m-d H:i:s');

						$rec_det = $this->base_model->fetch_records_from('seller_selling_books', array('sc_id' => $update_rec_id));
						if(!empty($rec_det)) $rec_det = $rec_det[0];

					    /*if(!empty($book_image)) {
							$book_image_parts = explode('.', $book_image);
							$book_image_name = str_replace('.', '_', str_replace($book_image_parts[count($book_image_parts)-1], '', $book_image));

							$ext = pathinfo($book_image, PATHINFO_EXTENSION);
							$file_name = $book_image_name.date('Ymdhis').rand().'.'.$ext;
							$config['upload_path'] 		= 'assets/uploads/book_curriculum_files/';
							$config['allowed_types'] 	= 'jpg|jpeg|png|svg|bmp';
							$config['overwrite'] 		= true;
							$config['max_size']     	= '10240';//10MB
							$config['file_name']        = $file_name;

							$this->load->library('upload', $config);
							$this->upload->initialize($config);

							if($this->upload->do_upload('book_image'))
							{
								//Unlink Old File
								if(!empty($rec_det->book_image) && file_exists(URL_PUBLIC_UPLOADS.'book_curriculum_files/'.$rec_det->book_image))
									unlink(URL_PUBLIC_UPLOADS.'book_curriculum_files/'.$rec_det->book_image);

								$inputdata['image']	= $file_name;
							} else {neatPrint($this->upload->display_errors());}
						}

						if(!empty($preview_image)) {
							$preview_image_parts = explode('.', $preview_image);
							$preview_image_name = str_replace('.', '_', str_replace($preview_image_parts[count($preview_image_parts)-1], '', $preview_image));

							$ext = pathinfo($preview_image, PATHINFO_EXTENSION);
							$file_name = $preview_image_name.date('Ymdhis').rand().'.'.$ext;
							$config['upload_path'] 		= 'assets/uploads/book_curriculum_files/';
							$config['allowed_types'] 	= 'jpg|jpeg|png|svg|bmp';
							$config['overwrite'] 		= true;
							$config['max_size']     	= '10240';//10MB
							$config['file_name']        = $file_name;

							$this->load->library('upload', $config);
							$this->upload->initialize($config);

							if($this->upload->do_upload('preview_image'))
							{
								//Unlink Old File
								if(!empty($rec_det->preview_image) && file_exists(URL_PUBLIC_UPLOADS.'book_curriculum_files/'.$rec_det->preview_image))
									unlink(URL_PUBLIC_UPLOADS.'book_curriculum_files/'.$rec_det->preview_image);

								$inputdata['preview_image']	= $file_name;
							}
						}*/

						$book_imagecanvas = $this->input->post('book_imagecanvas');

						if($book_imagecanvas) {
							$book_imagecanvas = str_replace('data:image/png;base64,', '', $book_imagecanvas);
							$book_imagecanvas = str_replace(' ', '+', $book_imagecanvas);
							$book_imagecanvasData = base64_decode($book_imagecanvas);

							$book_imagecanvasName = date('Ymdhis').rand().'_main'.'.png';

							$book_imageFullName = $book_temp_upload_path.$book_imagecanvasName;

							if(isset($record) && $record->image != '') {
								$book_imageDeleteFullName = $this->book_s3_upload_path.$record->image;

								$response = common_s3_function('del', 'file', $record->image);
							}

							file_put_contents($book_imageFullName, $book_imagecanvasData);

							$inputdata['image']	= $book_imagecanvasName;

							//$file_path = FCPATH . $book_imageFullName;

							$file_path = $book_imageFullName;
							$response = common_s3_function('set', 'file', $book_imagecanvasName, $file_path);

							if(!empty($response) && $response['status'] == 'success') {
								unlink($file_path);
							}
						}

						$book_previewcanvas = $this->input->post('preview_imagecanvas');

						if($book_previewcanvas) {
							$book_previewcanvas = str_replace('data:image/png;base64,', '', $book_previewcanvas);
							$book_previewcanvas = str_replace(' ', '+', $book_previewcanvas);
							$book_previewcanvas = base64_decode($book_previewcanvas);

							$book_previewcanvasName = date('Ymdhis').rand().'_preivew'.'.png';

							$book_previewcanvasFullName = $book_temp_upload_path.$book_previewcanvasName;

							if(isset($record) && $record->preview_image != '') {
								$book_previewImageDeleteFullName = $this->book_s3_upload_path.$record->preview_image;

								$response = common_s3_function('del', 'file', $record->preview_image);
							}

							file_put_contents($book_previewcanvasFullName, $book_previewcanvas);

							$inputdata['preview_image']	= $book_previewcanvasName;

							$file_path = $book_previewcanvasFullName;
							$response = common_s3_function('set', 'file', $book_previewcanvasName, $file_path);

							if(!empty($response) && $response['status'] == 'success') {
								unlink($file_path);
							}
						}

						if(!empty($preview_file)) {
							$preview_file_parts = explode('.', $preview_file);
							$preview_file_name = str_replace('.', '_', str_replace($preview_file_parts[count($preview_file_parts)-1], '', $preview_file));

							$ext = pathinfo($preview_file, PATHINFO_EXTENSION);
							$file_name = $preview_file_name.date('Ymdhis').rand().'.'.$ext;
							$config['upload_path'] 		= 'assets/uploads/book_curriculum_files/';
							$config['allowed_types'] 	= 'mp2|mp3|mp4|3gp|pdf|webm|aac|wav|wmv|flv|avi|ogg|jpg|jpeg|png|svg|bmp';
							$config['overwrite'] 		= true;
							$config['max_size']     	= '10240';//10MB
							$config['file_name']        = $file_name;

							$this->load->library('upload', $config);
							$this->upload->initialize($config);

							if($this->upload->do_upload('preview_file'))
							{
								//Unlink Old File
								//if(!empty($rec_det->preview_file) && file_exists(URL_PUBLIC_UPLOADS.'book_curriculum_files/'.$rec_det->preview_file))
									//unlink(URL_PUBLIC_UPLOADS.'book_curriculum_files/'.$rec_det->preview_file);

								if(isset($record) && $record->preview_file != '') {
									$book_previewFileDeleteFullName = $this->book_s3_upload_path.$record->preview_file;

									$response = common_s3_function('del', 'file', $record->preview_file);

									/*try {
										$this->s3Client->deleteObject([
											'Bucket' => $bucket_name,
											'Key' => $book_previewFileDeleteFullName,
										]);
									} catch (Aws\S3\Exception\S3Exception $e) {
										//echo $e->getMessage();
									}*/
								}

								$book_previewFileFullName = $book_temp_upload_path.$file_name;

								$file_path = $book_previewFileFullName;
								$response = common_s3_function('set', 'file', $file_name, $file_path);

								if(!empty($response) && $response['status'] == 'success') {
									unlink($file_path);
								}

								/*$file_path = FCPATH . $book_previewFileFullName;
								$size = filesize($file_path);

								$sha256 = hash_file("sha256", $file_path);

								try {
									$result = $this->s3Client->putObject([
										'Bucket' => $this->bucket_name,
										'Key' => $this->book_s3_upload_path.$file_name,
										'SourceFile' => $file_path,
										//'Body' => fopen($file_path, 'r'),
										'ContentType' => 'image/jpeg/jpg/png',
										'ContentLength' => $size,
										//'ACL'    => 'public-read-write',
										'ContentSHA256' => $sha256
									]);

									unlink($file_path);
								} catch (Aws\S3\Exception\S3Exception $e) {
									//echo $e->getMessage();
								}*/

								$inputdata['preview_file']	= $file_name;
							} else {
								//echo $this->upload->display_errors();

								$error = true;

								$this->prepare_flashmessage(get_languageword($this->upload->display_errors()), 2);

								$this->data['message'] = $this->session->flashdata('message');
							}
						}

						if($this->base_model->update_operation($inputdata, 'seller_selling_books', array('sc_id' => $update_rec_id))) {
							$this->prepare_flashmessage(get_languageword("Your book has been updated successfully"), 0);

							$lesson_files			= $_FILES['lesson_file'];
							$curriculum_data_final 	= array();

							$curriculum_titles 	= $this->input->post('lesson_title');
							$source_type 		= $this->input->post('source_type');
							$is_free_items      = $this->input->post('is_free');
							$curriculum_urls 	= $this->input->post('lesson_url');
							$curriculum_ids     = $this->input->post('curriculum_id');
							$curriculum_file_names     = $this->input->post('book_preview_file_s3_mimetype');
							$total_curriculum_recs = count($curriculum_titles);

							$exam_data_final = array();
							$exam_questions	= $this->input->post('question');
							$answers = $this->input->post('answer');
							$exam_ids = $this->input->post('exam_id');
							$total_exam_recs = count($exam_questions);

							/*echo '$total_curriculum_recs :: '.$total_curriculum_recs.' :: ';
							echo '<pre> $curriculum_titles :: '; print_r($curriculum_titles);
							echo '<pre> $curriculum_ids :: '; print_r($curriculum_ids);
							echo '<pre> $lesson_files :: '; print_r($lesson_files);
							echo '<pre> $curriculum_urls :: '; print_r($curriculum_urls);
							die;*/

							// Loop through each file
							for($i=0; $i<$total_curriculum_recs; $i++) {
							  	if(!empty($curriculum_titles[$i])) {
									if((empty($curriculum_ids[$i]) && (!empty($lesson_files['size'][$i]) || !empty($curriculum_urls[$i]))) || !empty($curriculum_ids[$i])) {
										$curriculum_data 		= array();

										$curriculum_data['file_id']	    = $curriculum_ids[$i];
										$curriculum_data['sc_id']	    = $update_rec_id;
										$curriculum_data['title']	    = $curriculum_titles[$i];
										$curriculum_data['source_type']	= $source_type[$i];
										$curriculum_data['is_free']	    = (!empty($is_free_items) && isset($is_free_items[$i+1]) && $is_free_items[$i+1] == 'on') ? '1' : '0';

										if($source_type[$i] == "file") {
											if(empty($curriculum_ids[$i])) {
												$_FILES['lessonfile']['name'] = $lesson_files['name'][$i];
												$_FILES['lessonfile']['type'] = $lesson_files['type'][$i];
												$_FILES['lessonfile']['tmp_name'] = $lesson_files['tmp_name'][$i];
												$_FILES['lessonfile']['error'] = $lesson_files['error'][$i];
												$_FILES['lessonfile']['size'] = $lesson_files['size'][$i];

												$ext = pathinfo($_FILES['lessonfile']['name'], PATHINFO_EXTENSION);
												$file_name = $update_rec_id.'_'.($i+1).'_'.date('Ymdhis').rand().'.'.$ext;
												$config['upload_path'] 		= 'assets/uploads/book_curriculum_files/';
												$config['allowed_types'] 	= 'mp2|mp3|mp4|3gp|pdf|ppt|pptx|doc|docx|rtf|rtx|txt|text|webm|aac|wav|wmv|flv|avi|ogg|jpg|jpeg|png|gif|svg|bmp';
												$config['overwrite'] 		= true;
												$config['max_size']     	= '20480';//20MB
												$config['file_name']        = $file_name;

												$this->load->library('upload', $config);
												$this->upload->initialize($config);

												if($this->upload->do_upload('lessonfile'))
												{
													$curriculum_data['file_name']	= $file_name;
													$curriculum_data['file_ext']	= $ext;
													$curriculum_data['file_size']	= $_FILES['lessonfile']['size'];

													if(isset($curriculum_file_names[$i]) && $curriculum_file_names[$i] != '') {
														$response = common_s3_function('del', 'file', $curriculum_file_names[$i]);

														//echo '<pre> $response :: '; print_r($response); die;
													}

													$book_curriculumFullName = $book_temp_upload_path.$file_name;

													$file_path = $book_curriculumFullName;
													$response = common_s3_function('set', 'file', $file_name, $file_path);

													if(!empty($response) && $response['status'] == 'success') {
														unlink($file_path);
													}
												} else {
													//echo $this->upload->display_errors();

													$error = true;

													$this->prepare_flashmessage(get_languageword($this->upload->display_errors()), 2);

													$this->data['message'] = $this->session->flashdata('message');
												}
											} else if ((!empty($curriculum_ids[$i]) && !empty($lesson_files['name'][$i])) || empty($curriculum_ids[$i])) { echo 'in !empty curriculum else if :: ';
												$_FILES['lessonfile']['name'] = $lesson_files['name'][$i];
												$_FILES['lessonfile']['type'] = $lesson_files['type'][$i];
												$_FILES['lessonfile']['tmp_name'] = $lesson_files['tmp_name'][$i];
												$_FILES['lessonfile']['error'] = $lesson_files['error'][$i];
												$_FILES['lessonfile']['size'] = $lesson_files['size'][$i];

												$ext = pathinfo($_FILES['lessonfile']['name'], PATHINFO_EXTENSION);
												$file_name = $update_rec_id.'_'.($i+1).'_'.date('Ymdhis').rand().'.'.$ext;
												$config['upload_path'] 		= 'assets/uploads/book_curriculum_files/';
												$config['allowed_types'] 	= 'mp2|mp3|mp4|3gp|pdf|ppt|pptx|doc|docx|rtf|rtx|txt|text|webm|aac|wav|wmv|flv|avi|ogg|jpg|jpeg|png|gif|svg|bmp';
												$config['overwrite'] 		= true;
												$config['max_size']     	= '20480';//20MB
												$config['file_name']        = $file_name;

												$this->load->library('upload', $config);
												$this->upload->initialize($config);

												if($this->upload->do_upload('lessonfile'))
												{
													$curriculum_data['file_name']	= $file_name;
													$curriculum_data['file_ext']	= $ext;
													$curriculum_data['file_size']	= $_FILES['lessonfile']['size'];

													if(isset($curriculum_file_names[$i]) && $curriculum_file_names[$i] != '') {
														$response = common_s3_function('del', 'file', $curriculum_file_names[$i]);

														//echo '<pre> $response :: '; print_r($response); die;
													}

													$book_curriculumFullName = $book_temp_upload_path.$file_name;

													$file_path = $book_curriculumFullName;
													$response = common_s3_function('set', 'file', $file_name, $file_path);

													if(!empty($response) && $response['status'] == 'success') {
														unlink($file_path);
													}
												} else {
													//echo $this->upload->display_errors();

													$error = true;

													$this->prepare_flashmessage(get_languageword($this->upload->display_errors()), 2);

													$this->data['message'] = $this->session->flashdata('message');
												}
											}
										} else {
											$curriculum_data['file_name']	= $curriculum_urls[$i];
											$curriculum_data['file_ext']	= null;
											$curriculum_data['file_size']	= null;

											// $curriculum_data_final[] = $curriculum_data;
											// $k++;
										}

										$curriculum_data_final[] = $curriculum_data;
									}
							  	}
							}

							//loop through each exam
							for($i=0; $i<$total_exam_recs; $i++) {
							  	if(!empty($exam_questions[$i])) {
									if(empty($exam_ids[$i] || !empty($exam_ids[$i]))) {
										$exam_data = array();

										$exam_data['exam_id'] = $exam_ids[$i];
										$exam_data['sc_id'] = $update_rec_id;
										$exam_data['question'] = $exam_questions[$i];
										$exam_data['answer'] = $answers[$i];

										$exam_data_final[] = $exam_data;
									}
							  	}
							}

							//if(!empty($curriculum_data_final)) {
								// echo "<pre>";print_r($curriculum_data_final);exit;

							foreach($curriculum_data_final as $data_final) {
								$file_id = $data_final['file_id'];
								unset($data_final['file_id']);
								if(!empty($file_id)) {
									$this->db->update('seller_selling_books_curriculum', $data_final, array('file_id' => $file_id));
								} else {
									$this->db->insert('seller_selling_books_curriculum', $data_final);
								}
							}

							foreach($exam_data_final as $data_final) {
								$exam_id = $data_final['exam_id'];
								unset($data_final['exam_id']);
								if(!empty($exam_id)) {
									$this->db->update('seller_selling_books_exam', $data_final, array('exam_id' => $exam_id));
								} else {
									$this->db->insert('seller_selling_books_exam', $data_final);
								}
							}

							foreach($remove_curriculums as $rem_cur_id) {
								$book_curriculum_file_data = $this->base_model->fetch_records_from('seller_selling_books_curriculum', array('file_id' => $rem_cur_id));

								if((!empty($book_curriculum_file_data)) && ($book_curriculum_file_data[0]->source_type == 'file' && $book_curriculum_file_data[0]->file_name != '')) {
									//$book_curriculamRemoveFullName = $this->book_s3_upload_path.$book_curriculum_file_data[0]->file_name;

									$response = common_s3_function('del', 'file', $book_curriculum_file_data[0]->file_name);

									/*try {
										$this->s3Client->deleteObject([
											'Bucket' => $bucket_name,
											'Key' => $book_curriculamRemoveFullName,
										]);
									} catch (Aws\S3\Exception\S3Exception $e) {
										//echo $e->getMessage();
									}*/
								}

								$this->base_model->delete_record_new('seller_selling_books_curriculum', array('file_id' => $rem_cur_id));
							}

							foreach($remove_exams as $rem_exam_id) {
								$book_exam_data = $this->base_model->fetch_records_from('seller_selling_books_exam', array('exam_id' => $rem_exam_id));

								$this->base_model->delete_record_new('seller_selling_books_exam', array('exam_id' => $rem_exam_id));
							}

							//05-12-2018 admin notification start when seller updated his selling book
							$data = array();
							$data['user_id'] 	= $seller_id;
							$data['title'] 		= get_languageword('Seller_updated_his_selling_book');
							$data['content'] 	= "Seller has updated his selling book "." ".$this->input->post('book_name');
							$data['datetime']   = date('Y-m-d H:i:s');
							$data['admin_read'] = 0;
							$data['page_link']  = SITEURL."seller/view-selling-book-curriculum/".$update_rec_id;
							$data['table_name'] = "seller_selling_books";
							$data['primary_key_column'] = "sc_id";
							$data['primary_key_value']  = $update_rec_id;
							//admin notificaiton end

							$this->base_model->insert_operation($data,'notifications');	
							unset($data);

							$this->prepare_flashmessage(get_languageword("Your book has been published successfully"), 0);

							// } else {

							// 	$this->base_model->delete_record_new('seller_selling_books', array('sc_id' => $update_rec_id));

							// 	$this->prepare_flashmessage(get_languageword("Your book not published due to invalid input data"), 2);
							// }
						} else {
							$this->prepare_flashmessage(get_languageword("Your book not published due to invalid input data"), 2);
						}

						redirect(URL_SELLER_LIST_SELLING_BOOKS);
					} else { //Insert Operation
						$slug = prepare_slug($inputdata['book_name'], 'book_name', 'seller_selling_books');

						$inputdata['slug'] 		 = $slug;
						$inputdata['created_at'] = date('Y-m-d H:i:s');
						$inputdata['updated_at'] = $inputdata['created_at'];

						if(!empty($book_image)) {
							$book_image_parts = explode('.', $book_image);
							$book_image_name = str_replace('.', '_', str_replace($book_image_parts[count($book_image_parts)-1], '', $book_image));

							$ext = pathinfo($book_image, PATHINFO_EXTENSION);
							$file_name = $book_image_name.date('Ymdhis').rand().'.'.$ext;
							$config['upload_path'] 		= 'assets/uploads/book_curriculum_files/';
							$config['allowed_types'] 	= 'jpg|jpeg|png|svg|bmp';
							$config['overwrite'] 		= true;
							$config['max_size']     	= '10240';//10MB
							$config['file_name']        = $file_name;

							$this->load->library('upload', $config);
							$this->upload->initialize($config);

							if($this->upload->do_upload('book_image'))
							{
								$inputdata['image']	= $file_name;

								$book_imageFullName = $book_temp_upload_path.$file_name;

								$file_path = $book_imageFullName;
								$response = common_s3_function('set', 'file', $file_name, $file_path);

								if(!empty($response) && $response['status'] == 'success') {
									unlink($file_path.$file);
								}

								/*$file_path = FCPATH . $book_imageFullName;
								$size = filesize($file_path);

								$sha256 = hash_file("sha256", $file_path);

								try {
									$result = $this->s3Client->putObject([
										'Bucket' => $this->bucket_name,
										'Key' => $this->book_s3_upload_path.$file_name,
										'SourceFile' => $file_path,
										//'Body' => fopen($file_path, 'r'),
										'ContentType' => 'image/jpeg/jpg/png',
										'ContentLength' => $size,
										//'ACL'    => 'public-read-write',
										'ContentSHA256' => $sha256
									]);

									unlink($file_path);
								} catch (Aws\S3\Exception\S3Exception $e) {
									//echo $e->getMessage();
								}*/
							} else {
								$error = true;

								$this->prepare_flashmessage(get_languageword($this->upload->display_errors()), 2);

								$this->data['message'] = $this->session->flashdata('message');
							}
						}

						if(!empty($preview_image)) {
							$preview_image_parts = explode('.', $preview_image);
							$preview_image_name = str_replace('.', '_', str_replace($preview_image_parts[count($preview_image_parts)-1], '', $preview_image));

							$ext = pathinfo($preview_image, PATHINFO_EXTENSION);
							$file_name = $preview_image_name.date('Ymdhis').rand().'.'.$ext;
							$config['upload_path'] 		= 'assets/uploads/book_curriculum_files/';
							$config['allowed_types'] 	= 'jpg|jpeg|png|svg|bmp';
							$config['overwrite'] 		= true;
							$config['max_size']     	= '10240';//10MB
							$config['file_name']        = $file_name;

							$this->load->library('upload', $config);
							$this->upload->initialize($config);

							if($this->upload->do_upload('preview_image'))
							{
								$book_previewImageFullName = $book_temp_upload_path.$file_name;

								$file_path = $book_previewImageFullName;
								$response = common_s3_function('set', 'file', $file_name, $file_path);

								if(!empty($response) && $response['status'] == 'success') {
									unlink($file_path);
								}

								/*$file_path = FCPATH . $book_previewImageFullName;
								$size = filesize($file_path);
								$sha256 = hash_file("sha256", $file_path);

								try {
									$result = $this->s3Client->putObject([
												'Bucket' => $this->bucket_name,
												'Key' => $this->book_s3_upload_path.$file_name,
												'SourceFile' => $file_path,
												//'Body' => fopen($file_path, 'r'),
												'ContentType' => 'image/jpeg/jpg/png',
												'ContentLength' => $size,
												//'ACL'    => 'public-read-write',
												'ContentSHA256' => $sha256
											]);

									unlink($file_path);
								}  catch (Aws\S3\Exception\S3Exception $e)  {
									//echo $e->getMessage();
								}*/

								$inputdata['preview_image']	= $file_name;
							} else {
								$error = true;

								$this->prepare_flashmessage(get_languageword($this->upload->display_errors()), 2);

								$this->data['message'] = $this->session->flashdata('message');
							}
						}

						if(!empty($preview_file)) {
							$preview_file_parts = explode('.', $preview_file);
							$preview_file_name = str_replace('.', '_', str_replace($preview_file_parts[count($preview_file_parts)-1], '', $preview_file));

							$ext = pathinfo($preview_file, PATHINFO_EXTENSION);
							$file_name = $preview_file_name.date('Ymdhis').rand().'.'.$ext;
							$config['upload_path'] 		= 'assets/uploads/book_curriculum_files/';
							$config['allowed_types'] 	= 'mp2|mp3|mp4|3gp|pdf|webm|aac|wav|wmv|flv|avi|ogg|jpg|jpeg|png|svg|bmp';
							$config['overwrite'] 		= true;
							$config['max_size']     	= '10240';//10MB
							$config['file_name']        = $file_name;

							$this->load->library('upload', $config);
							$this->upload->initialize($config);

							if($this->upload->do_upload('preview_file'))
							{
								$book_previewFileFullName = $book_temp_upload_path.$file_name;

								$file_path = $book_previewFileFullName;
								$response = common_s3_function('set', 'file', $file_name, $file_path);

								if(!empty($response) && $response['status'] == 'success') {
									unlink($file_path);
								}

								/*$file_path = FCPATH . $book_previewFileFullName;
								$size = filesize($file_path);
								$sha256 = hash_file("sha256", $file_path);

								try {
									$result = $this->s3Client->putObject([
												'Bucket' => $this->bucket_name,
												'Key' => $this->book_s3_upload_path.$file_name,
												'SourceFile' => $file_path,
												//'Body' => fopen($file_path, 'r'),
												'ContentType' => 'image/jpeg/jpg/png',
												'ContentLength' => $size,
												//'ACL'    => 'public-read-write',
												'ContentSHA256' => $sha256
											]);

									unlink($file_path);
								}  catch (Aws\S3\Exception\S3Exception $e)  {
									//echo $e->getMessage();
								}*/

								$inputdata['preview_file']	= $file_name;
							} else {
								$error = true;

								$this->prepare_flashmessage(get_languageword($this->upload->display_errors()), 2);

								$this->data['message'] = $this->session->flashdata('message');
							}
						}

						if(!$error) {
							$insert_id = $this->base_model->insert_operation($inputdata, 'seller_selling_books');

							if($insert_id > 0) {
								//05-12-2018 admin notification start
								//notifications when seller posted his selling book
								$data = array();
								$data['user_id'] 	= $seller_id;
								$data['title'] 		= get_languageword('Seller_posted_his_selling_book');
								$data['content'] 	= "Seller has created his selling book "." ".$this->input->post('book_name');
								$data['datetime']   = date('Y-m-d H:i:s');
								$data['admin_read'] = 0;
								$data['page_link']  = SITEURL."seller/view-selling-book-curriculum/".$insert_id;
								$data['table_name'] = "seller_selling_books";
								$data['primary_key_column'] = "sc_id";
								$data['primary_key_value']  = $insert_id;

								$this->base_model->insert_operation($data,'notifications');	
								unset($data);
								//admin notification end

								$lesson_files			= $_FILES['lesson_file'];

								$curriculum_data_final 	= array();

								$curriculum_titles 	= $this->input->post('lesson_title');
								$source_type 		= $this->input->post('source_type');
								$curriculum_urls 	= $this->input->post('lesson_url');
								$is_free_items      = $this->input->post('is_free');
								$curriculum_ids     = $this->input->post('curriculum_id');
								$total_curriculum_recs = count($curriculum_titles);

								$exam_data_final 	= array();
								$exam_questions		= $this->input->post('question');
								$answers  			= $this->input->post('answer');
								$exam_ids		    = $this->input->post('exam_id');
								$total_exam_recs = count($exam_questions);

								// Loop through each file
								for($i=0; $i<$total_curriculum_recs; $i++) {
									if(!empty($curriculum_titles[$i])) {
									  if(!empty($lesson_files['size'][$i]) || !empty($curriculum_urls[$i])) {
											$curriculum_data 		= array();

											$curriculum_data['file_id']	    = $curriculum_ids[$i];
											$curriculum_data['sc_id']	    = $insert_id;
											$curriculum_data['title']	    = $curriculum_titles[$i];
											$curriculum_data['source_type']	= $source_type[$i];
											$curriculum_data['is_free']	    = (!empty($is_free_items) && isset($is_free_items[$i+1]) && $is_free_items[$i+1] == 'on') ? '1' : '0';

											if($source_type[$i] == "file") {
												if((!empty($curriculum_ids[$i]) && !empty($lesson_files['name'][$i])) || empty($curriculum_ids[$i])) {
													$_FILES['lessonfile']['name'] = $lesson_files['name'][$i];
													$_FILES['lessonfile']['type'] = $lesson_files['type'][$i];
													$_FILES['lessonfile']['tmp_name'] = $lesson_files['tmp_name'][$i];
													$_FILES['lessonfile']['error'] = $lesson_files['error'][$i];
													$_FILES['lessonfile']['size'] = $lesson_files['size'][$i];

													$ext = pathinfo($_FILES['lessonfile']['name'], PATHINFO_EXTENSION);
													$file_name = $insert_id.'_'.($i+1).'_'.date('Ymdhis').rand().'.'.$ext;
													$config['upload_path'] 		= 'assets/uploads/book_curriculum_files/';
													$config['allowed_types'] 	= 'mp2|mp3|mp4|3gp|pdf|ppt|pptx|doc|docx|rtf|rtx|txt|text|webm|aac|wav|wmv|flv|avi|ogg|jpg|jpeg|png|gif|svg|bmp';
													$config['overwrite'] 		= true;
													$config['max_size']     	= '20480';//20MB
													$config['file_name']        = $file_name;

													$this->load->library('upload', $config);
													$this->upload->initialize($config);

													if($this->upload->do_upload('lessonfile')) {
														$curriculum_data['file_name']	= $file_name;
														$curriculum_data['file_ext']	= $ext;
														$curriculum_data['file_size']	= $_FILES['lessonfile']['size'];

														$book_curriculamFileFullName = $book_temp_upload_path.$file_name;

														$file_path = $book_curriculamFileFullName;
														$response = common_s3_function('set', 'file', $file_name, $file_path);

														if(!empty($response) && $response['status'] == 'success') {
															unlink($file_path);
														}

														/*$file_path = FCPATH . $book_curriculamFileFullName;
														$size = filesize($file_path);
														$sha256 = hash_file("sha256", $file_path);

														try {
															$result = $this->s3Client->putObject([
																		'Bucket' => $this->bucket_name,
																		'Key' => $this->book_s3_upload_path.$file_name,
																		'SourceFile' => $file_path,
																		//'Body' => fopen($file_path, 'r'),
																		'ContentType' => 'image/jpeg/jpg/png',
																		'ContentLength' => $size,
																		//'ACL'    => 'public-read-write',
																		'ContentSHA256' => $sha256
																	]);

															unlink($file_path);
														}  catch (Aws\S3\Exception\S3Exception $e)  {
															//echo $e->getMessage();
														}*/

														// $curriculum_data_final[] = $curriculum_data;
														// $j++;
													} else {
														//echo $this->upload->display_errors();

														$error = true;

														$this->prepare_flashmessage(get_languageword($this->upload->display_errors()), 2);

														$this->data['message'] = $this->session->flashdata('message');
													}
												}
											} else {
												$curriculum_data['file_name']	= $curriculum_urls[$i];
												$curriculum_data['file_ext']	= null;
												$curriculum_data['file_size']	= null;

												// $curriculum_data_final[] = $curriculum_data;
												// $k++;
											}
										  $curriculum_data_final[] = $curriculum_data;
										}
									}
								}

								for($i=0; $i<$total_exam_recs; $i++) {
									if(!empty($exam_questions[$i])) {
										$exam_data = array();

										$exam_data['exam_id'] = $exam_ids[$i];
										$exam_data['sc_id'] = $insert_id;
										$exam_data['answer'] = $answers[$i];
										$exam_data['question'] = $exam_questions[$i];

										$exam_data_final[] = $exam_data;
									}
								}

								//echo "<pre>";print_r($curriculum_urls);exit;

								if(!empty($curriculum_data_final)) {
									foreach($curriculum_data_final as $data_final) {
										$file_id = $data_final['file_id'];
										unset($data_final['file_id']);
										if(!empty($file_id)) {
											$this->db->update('seller_selling_books_curriculum', $data_final, array('file_id' => $file_id));
										} else {
											$this->db->insert('seller_selling_books_curriculum', $data_final);
										}
									}

									$this->prepare_flashmessage(get_languageword("Your book has been published successfully"), 0);

								} else {
									// $this->base_model->delete_record_new('seller_selling_books', array('sc_id' => $insert_id));

									//$this->prepare_flashmessage(get_languageword("Your book not published due to invalid input data"), 2);
								}

								if(!empty($exam_data_final)) {
									foreach($exam_data_final as $data_final) {
										$exam_id = $data_final['exam_id'];
										unset($data_final['exam_id']);
										if(!empty($exam_id)) {
											$this->db->update('seller_selling_books_exam', $data_final, array('exam_id' => $exam_id));
										} else {
											$this->db->insert('seller_selling_books_exam', $data_final);
										}
									}

									$this->prepare_flashmessage(get_languageword("Your book has been published successfully"), 0);

								} else {
									// $this->base_model->delete_record_new('seller_selling_books', array('sc_id' => $insert_id));

									//$this->prepare_flashmessage(get_languageword("Your book not published due to invalid input data"), 2);
								}

								$this->prepare_flashmessage(get_languageword("Your book has been published successfully"), 0);
							} else {
								$this->prepare_flashmessage(get_languageword("Your book not published due to invalid input data"), 2);
							}

							redirect(URL_SELLER_LIST_SELLING_BOOKS);
						}
					} // Insert Operation - End
				}
			}
			else {
				$this->data['message'] = $this->prepare_message(validation_errors(), 1);
			}
		}

		//Preparing Language options
		$lng_opts = $this->base_model->fetch_records_from('languages',array('status' => 'Active'));
		$options = array();
		if(!empty($lng_opts))
		{
			foreach($lng_opts as $row):
				$options[$row->name] = $row->name;
			endforeach;
		}

		//Preparing Categories options
		$cat_recs = $this->base_model->fetch_records_from('categories',array('is_parent' => 1, 'status' => 1), '*', 'name ASC');
		$cat_opts = array('' => get_languageword("no_categories_available"));

		if(!empty($cat_recs))
		{
			$cat_opts = array('' => get_languageword("select"));
			foreach($cat_recs as $rec):
				$cat_opts[$rec->id] = $rec->name;
			endforeach;
		}

		$this->data['cat_opts'] 		= $cat_opts;

		$this->data['language_options'] = $options;
		$this->data['activemenu'] 		= "sell_books_online";
		$this->data['activesubmenu'] 	= "publish";
		$this->data['pagetitle'] 		= get_languageword('Sell_Books_Online');
		$this->data['content'] 			= 'sell_books_online';
		$this->data['profile_info']     = getUserRec();
		$this->data['texteditor'] 		= TRUE;
		//$this->data['grocery'] 		= TRUE;

		$this->data['css_files'] 		= [
			'/assets/cropperjs/cropper.min.css',
			'/assets/cropperjs/cropper.css',
		];

		$this->data['js_files'] 		= [
			'/assets/cropperjs/cropper.min.js',
			'/assets/cropperjs/cropper.js',
		];

		//$this->grocery_output($this->data);

		//$this->data['grocery_output'] = $this->_render_page('sell_books_online', $this->data,true);

		$grocery_output = new stdClass();
		$grocery_output->output = $this->load->view('sell_books_online', $this->data,TRUE); 

		$this->data['grocery'] = TRUE;

		$this->data['grocery_output'] =  $grocery_output;

		$this->load->view('template/site/seller-template',$this->data);
	}

	function _msg_allowed_formats()
	{
		$this->form_validation->set_message('_msg_allowed_formats', get_languageword('Please upload files only with allowed formats')." ".get_languageword('Allowed File Foramts are')." 'mp2', 'mp3', 'mp4', '3gp', 'pdf', 'ppt', 'pptx', 'doc', 'docx', 'rtf', 'rtx', 'txt', 'text', 'webm', 'aac', 'wav', 'wmv', 'flv', 'avi', 'ogg', 'jpg', 'jpeg', 'png', 'gif', 'svg', 'bmp'.");

		return false;
	}


	/**
	 * [list_selling_books description]
	 * @param  string $operation [description]
	 * @param  string $sc_id     [description]
	 * @return [type]            [description]
	 */
	function list_selling_books($operation = "", $sc_id = "")
	{
		if (!$this->ion_auth->logged_in() || !($this->ion_auth->is_seller() || $this->ion_auth->is_admin())) {
			$this->prepare_flashmessage(get_languageword('You dont have permission to access this page'), 1);
			redirect('auth/login', 'refresh');
		}

		if($this->ion_auth->is_seller()) {
			$seller_id = $this->ion_auth->get_user_id();

			if(is_inst_seller($seller_id)) {
				$this->prepare_flashmessage(get_languageword('Invalid Request'), 1);
				redirect(URL_SELLER_INDEX);
			}
		}

		//Delete Operation - Start
		if($operation == "delete" && $sc_id > 0) {
			$query_book_files = "(SELECT image, preview_image, preview_file FROM ".TBL_PREFIX."seller_selling_books WHERE sc_id=".$sc_id.")";

			$book_files = $this->base_model->get_query_result($query_book_files);

			$query_book_curriculum_files = "(SELECT preview_file AS file_name FROM ".TBL_PREFIX."seller_selling_books WHERE sc_id=".$sc_id.") UNION (SELECT file_name FROM ".TBL_PREFIX."seller_selling_books_curriculum WHERE sc_id=".$sc_id.") ";

			$book_curriculum_files = $this->base_model->get_query_result($query_book_curriculum_files);

			if($this->base_model->delete_record_new('seller_selling_books', array('sc_id' => $sc_id))) {
				$this->base_model->delete_record_new('seller_selling_books_curriculum', array('sc_id' => $sc_id));

				if(!empty($book_files[0])) {
					foreach ($book_files[0] as $key => $value) {
						$response = common_s3_function('del', 'file', $value);

						/*try {
							$this->s3Client->deleteObject([
								'Bucket' => $this->bucket_name,
								'Key' => $this->book_s3_upload_path.$value
							]);
						}  catch (Aws\S3\Exception\S3Exception $e)  {
							//echo $e->getMessage();
						}*/
					}
				}

				if(!empty($book_curriculum_files)) {
					foreach ($book_curriculum_files as $key => $value) {
						$response = common_s3_function('del', 'file', $value->file_name);

						/*try {
							$this->s3Client->deleteObject([
								'Bucket' => $this->bucket_name,
								'Key' => $this->book_s3_upload_path.$value->file_name
							]);
						}  catch (Aws\S3\Exception\S3Exception $e)  {
							//echo $e->getMessage();
						}*/

						//if(file_exists(URL_PUBLIC_UPLOADS.'book_curriculum_files/'.$value->file_name))
							//unlink(URL_PUBLIC_UPLOADS.'book_curriculum_files/'.$value->file_name);
					}
				}

				$this->prepare_flashmessage(get_languageword('Record_Deleted_Successfully'), 0);
			} else {
				$this->prepare_flashmessage(get_languageword('Record_Not_Deleted'), 2);
			}

			redirect(URL_SELLER_LIST_SELLING_BOOKS);
		}
		//Delete Operation - End

		$this->load->library(array('grocery_CRUD'));
		$crud = new grocery_CRUD();
		$crud_state = $crud->getState();
		$crud->set_table($this->db->dbprefix('seller_selling_books'));

		if($this->ion_auth->is_admin()) {
			$crud->set_relation('seller_id', TBL_PREFIX.'users', 'username');
		}

		$crud->set_subject( get_languageword('Published_Books'));

		if($this->ion_auth->is_admin()) {
			$crud->unset_jquery();

			$crud->unset_add();
			$crud->unset_delete();
			$crud->unset_read();

			// $crud->unset_operations();

			$crud->columns('seller_id', 'book_name', 'book_title', 'actual_price', 'book_price', 'admin_commission_percentage', 'max_downloads', 'status', 'admin_approved','show_on_main_site');

			$crud->edit_fields('admin_approved','show_on_main_site');

			$crud->display_as('seller_id', get_languageword('Seller_Name'));

			$activemenu = 'seller_selling_books';
			$title 		= get_languageword('Seller_Selling_Books');
		} else {
			$crud->where('seller_id', $seller_id);

			$crud->unset_operations();

			$crud->columns('book_name', 'book_title', 'actual_price', 'book_price', 'admin_commission_percentage', 'max_downloads', 'status', 'admin_approved','show_on_main_site');

			$activemenu = 'sell_books_online';
			$title 		= get_languageword('My_Selling_Books');
			$this->data['activesubmenu'] 	= "list";
		}

		$crud->display_as('book_title', get_languageword('title'));
		$crud->display_as('actual_price', get_languageword('Actual_Price'));
		$crud->display_as('book_price', get_languageword('Discounted_Price'));
		$crud->display_as('admin_commission_percentage', 'Admin + Transaction Fee');

		$crud->add_action(get_languageword('visit'), URL_FRONT_IMAGES.'view-grocery.png', '', 'view-icon-grocery', array($this,'callback_visit_selling_book'));

		if($this->ion_auth->is_seller()){
			$crud->add_action(get_languageword('edit'), URL_FRONT_IMAGES.'edit-grocery.png', URL_SELLER_SELL_BOOKS_ONLINE.'/');

			$crud->add_action(get_languageword('delete'), URL_FRONT_IMAGES.'close-grocery.png', '', 'delete-icon-grocery', array($this,'callback_delete_selling_book'));
		}

		$output = $crud->render();

		$this->data['activemenu'] 		= $activemenu;
		$this->data['pagetitle'] 		= $title;
		$this->data['grocery_output'] 	= $output;
		$this->data['grocery'] = TRUE;
		$this->grocery_output($this->data);
	}

	function callback_visit_selling_book($primary_key , $row)
	{
		return SITEURL2.'/buy-book/'.$row->slug;
	}

	function callback_delete_selling_book($primary_key , $row)
	{
		return URL_SELLER_LIST_SELLING_BOOKS.'/delete/'.$row->sc_id;
	}

	/**
	 * [delete_book_curriculum_record description]
	 * @param  string $file_id [description]
	 * @return [type]          [description]
	 */
	function delete_book_curriculum_record($file_id = "")
	{
		if (!$this->ion_auth->logged_in() || !($this->ion_auth->is_seller())) {
			$this->prepare_flashmessage(get_languageword('You dont have permission to access this page'), 1);
			redirect('auth/login', 'refresh');
		}


		if($this->ion_auth->is_seller()) {

			$seller_id = $this->ion_auth->get_user_id();

			if(is_inst_seller($seller_id)) {

				$this->prepare_flashmessage(get_languageword('Invalid Request'), 1);
				redirect(URL_SELLER_INDEX);
			}
		}


		if(!($file_id > 0)) {

			$this->prepare_flashmessage(get_languageword('No_Details_Found'), 2);
			redirect(URL_SELLER_LIST_SELLING_BOOKS);
		}


		$record = $this->base_model->fetch_records_from('seller_selling_books_curriculum', array('file_id' => $file_id));


		if(empty($record)) {

			$this->prepare_flashmessage(get_languageword('No Details Found'), 2);
			redirect(URL_SELLER_LIST_SELLING_BOOKS);

		} else {

			$record = $record[0];

			$sc_id = $record->sc_id;

			if($record->source_type == "file" && $record->file_name != "" && file_exists(URL_PUBLIC_UPLOADS.'book_curriculum_files/'.$record->file_name)) {

				//Unlink file first
				unlink(URL_PUBLIC_UPLOADS.'book_curriculum_files/'.$record->file_name);

			}

			if($this->base_model->delete_record_new('seller_selling_books_curriculum', array('file_id' => $file_id))) {

				$this->prepare_flashmessage(get_languageword('Record_Deleted_Successfully'), 0);

			} else {

				$this->prepare_flashmessage(get_languageword('Record_Not_Deleted'), 2);
			}

			redirect(URL_SELLER_VIEW_SELLING_BOOK_CURRICULUM.'/'.$sc_id);
		}

	}


	/**
	 * [view_selling_book_curriculum description]
	 * @param  string $sc_id [description]
	 * @return [type]        [description]
	 */
	function view_selling_book_curriculum($sc_id = "")
	{

		if (!$this->ion_auth->logged_in() || !($this->ion_auth->is_seller() || $this->ion_auth->is_admin())) {
			$this->prepare_flashmessage(get_languageword('You dont have permission to access this page'), 1);
			redirect('auth/login', 'refresh');
		}


		if($this->ion_auth->is_seller()) {

			$seller_id = $this->ion_auth->get_user_id();

			if(is_inst_seller($seller_id)) {

				$this->prepare_flashmessage(get_languageword('Invalid Request'), 1);
				redirect(URL_SELLER_INDEX);
			}
		}


		if(!($sc_id > 0)) {

			$this->prepare_flashmessage(get_languageword('No_Details_Found'), 2);
			redirect(URL_SELLER_LIST_SELLING_BOOKS);
		}


		$record = get_seller_sellingbook_info($sc_id);

		if(empty($record)) {

			$this->prepare_flashmessage(get_languageword('No Details Found'), 2);
			redirect(URL_SELLER_LIST_SELLING_BOOKS);

		}


		$this->data['record'] = $record;

		$template = 'template/site/seller-template';
		$activemenu = 'sell_books_online';

		if($this->ion_auth->is_admin()) {
			
			//update notification
			$view_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http")."://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
				
			update_notification($view_link);


			$template = 'template/admin/admin-template';
			$activemenu = 'seller_selling_books';
		}

		$this->data['activemenu'] 		= $activemenu;
		$this->data['activesubmenu'] 	= "list";
		$this->data['pagetitle'] 		= get_languageword('Selling_Book_Curriculum');
		$this->data['content'] 			= 'selling_book_curriculum';

		$this->_render_page($template, $this->data);

	}



	/**
	 * [purchased_books description]
	 * @return [type] [description]
	 */
	function purchased_books()
	{
		if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_seller()) {
			$this->prepare_flashmessage(get_languageword('You dont have permission to access this page'), 1);
			redirect('auth/login', 'refresh');
		}

		$seller_id = $this->ion_auth->get_user_id();

		$this->load->library(array('grocery_CRUD'));
		$crud = new grocery_CRUD();
		$crud_state = $crud->getState();
		$crud->set_table(TBL_PREFIX.'book_purchases');
		$crud->set_relation('sc_id',TBL_PREFIX.'seller_selling_books','book_title');
		$crud->set_relation('user_id',TBL_PREFIX.'users','username');
		$crud->where(TBL_PREFIX.'book_purchases.seller_id', $seller_id);
		// $crud->where('payment_status', 'Completed');

		$where = "total_amount > 0  AND payment_status = 'Completed' ";
		$crud->where($where);
		$crud->order_by('purchase_id','desc');

		$crud->set_subject( get_languageword('Purchased_Books') );

		$crud->unset_add();
		$crud->unset_delete();
		$crud->unset_edit();
		$crud->unset_read();

		$crud->columns('purchase_id','transaction_id','sc_id','user_id','item_price','admin_commission_val','payable','paid_date');

		$crud->callback_column('payable', function ($value, $row) {
			$payable = (float)$row->item_price - (float)$row->admin_commission_val - (float)$row->fee;
			return round($payable, 2);
		});

		// $crud->callback_column('due', function ($value, $row) {
		// 	$payable = (float)$row->item_price - (float)$row->admin_commission_val - (float)$row->fee - (float)$row->paid_to_seller;
		// 	return round($payable, 2);
		// });

		$crud->display_as('sc_id', get_languageword('Book_Title'));
		$crud->display_as('user_id', get_languageword('buyer_name'));
		$crud->display_as('paid_date', get_languageword('Purchased_On'));
		$crud->display_as('payable', 'Credited');
		// $crud->display_as('status_of_payment_to_seller', get_languageword('Payment_from_Admin'));
		// $crud->display_as('paid_to_seller', get_languageword('paid_to_seller'));
		// $crud->display_as('fee', get_languageword('trans._fee'));
		$crud->display_as('admin_commission_val', 'Admin + Transaction Fee');
		$crud->display_as('purchase_id', get_languageword('no.'));
		$output = $crud->render();

		$this->data['activemenu'] 		= "purchased_books";
		$this->data['pagetitle'] 		= get_languageword('Purchased_Books');
		$this->data['grocery_output'] 	= $output;
		$this->data['grocery'] 			= TRUE;
		$this->grocery_output($this->data);

	}
	/**
	 * Seller Blogs
	 * @access	public
	 * @return	array
	 */
	function blogs()
	{
		if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_seller()) {
			$this->prepare_flashmessage(get_languageword('You dont have permission to access this page'), 1);
			redirect('auth/login', 'refresh');
		}

		$this->data['message'] = $this->session->flashdata('message');

		$user_id = $this->ion_auth->get_user_id();
		
		$this->load->library(array('grocery_CRUD'));
		$crud = new grocery_CRUD();
		$crud_state = $crud->getState();
		$crud->set_table(TBL_SELLER_BLOGS);
		$crud->where('seller_id', $user_id);
		$crud->set_subject( get_languageword('blogs') );


		//List Table Columns
		
		$crud->columns('title','related_to','created','blog_status','admin_approved');

		//Form fields for Add Record
		$crud->add_fields('seller_id', 'title','related_to','image_url','description','created', 'updated', 'blog_status');

		//Form fields for Edit Record
		$crud->edit_fields('seller_id', 'title','related_to','image_url','description', 'updated', 'blog_status');
	
		//Display Alias Names
		$crud->display_as('created',get_languageword('created_on'));
		$crud->display_as('updated',get_languageword('last_updated'));
		$crud->display_as('approved_datetime',get_languageword('approved_on'));

		
		//From Validations
		$crud->required_fields(array('title','related_to','description'));
		
		

		//Unset Read Fields
		$crud->unset_read_fields('seller_id');

		//Set Custom Filed Types
		$crud->field_type('blog_status','dropdown', array('Active' => 'Active', 'Inactive' => 'Inactive'));
		
		$crud->field_type('seller_id', 'hidden', $user_id);
	
		$crud->field_type('created', 'hidden', date('Y-m-d H:i:s'));
		$crud->field_type('updated', 'hidden', date('Y-m-d H:i:s'));

		
		//Authenticate whether Seller editing/viewing his records only
		if($crud_state == "edit" || $crud_state == "read") {

			$p_key = $this->uri->segment(4);
			$seller_id = $this->base_model->fetch_value('seller_blogs', 'seller_id', array('blog_id' => $p_key));
			if($seller_id != $user_id) {

				$this->prepare_flashmessage(get_languageword('not_authorized'), 1);
    			redirect(URL_SELLER_BLOGS);
			}

		}

		if($crud_state == "read") {

			$crud->field_type('created', 'visibile');
			$crud->field_type('updated', 'visibile');
			// $crud->set_relation('blog_status','user_status_texts','text');
		}


		//05-12-2018 admin notification start when seller edit his blog
		$mthd = $this->uri->segment(3);
		$p_key = $this->uri->segment(4);

		if ($this->input->post('title')!="" && $p_key>0 && $mthd=="update_validation") {

			$blog = $this->base_model->fetch_records_from('seller_blogs', array('blog_id' => $p_key));

			if(!empty($blog)) {

				$blog = $blog[0];

				$data = array();
				$data['user_id'] 	= $user_id;
				$data['title'] 		= get_languageword("seller_updated_his_blog");
				$data['content'] 	= "Seller has been updated his blog ". $blog->title;
				$data['datetime']   = date('Y-m-d H:i:s');
				$data['admin_read'] = 0;
				$data['page_link']  = SITEURL.'admin/view-sellers-blogs/read/'.$blog->blog_id;
				$data['table_name'] = "seller_blogs";
				$data['primary_key_column'] = "blog_id";
				$data['primary_key_value']  = $blog->blog_id;

				// echo "<pre>";print_R($data);die();
				$this->base_model->insert_operation($data,'notifications');	
				unset($data);
			}
		}
		//admin notification end


		$crud->callback_after_insert(array($this, 'callback_is_profile_updated1'));

		$output = $crud->render();

		
		$this->data['activemenu'] 	= 'blogs';	
		$this->data['activesubmenu'] = 'view_blogs';	
		$this->data['pagetitle'] = get_languageword('Blogs');
		$this->data['grocery_output'] = $output;
		$this->data['grocery'] = TRUE;
		
		$this->grocery_output($this->data);
	}


	/**
	 * SELLER SENDS MONEY CONVERSTION REQUEST
	 */
	function send_seller_credits_conversion_request()
	{
		if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_seller()) {
			$this->prepare_flashmessage(get_languageword('You dont have permission to access this page'), 1);
			redirect('auth/login', 'refresh');
		}

		$redirect_url = URL_SELLER_BUYER_BOOKINGS;
		$booking_id = $this->input->post('booking_id');
		$user_id = $this->ion_auth->get_user_id();
		if(empty($booking_id)) {

			$this->prepare_flashmessage(get_languageword('Please complete your book to send credit conversion request'), 2);
			redirect($redirect_url);
		}

		$booking_det = $this->base_model->fetch_records_from('bookings', array('booking_id' => $booking_id, 'seller_id' => $user_id, 'status' => 'closed'));
		//Check whether booking exists
		if(empty($booking_det)) {

			$this->prepare_flashmessage(get_languageword('Invalid request'), 1);
			redirect($redirect_url);
		}

		$payment_status = $this->base_model->fetch_records_from('admin_money_transactions', array('booking_id' => $booking_id, 'user_id' => $user_id, 'user_type' => 'seller','booking_type'=>'booking'));
		if(!empty($payment_status)) {

			$this->prepare_flashmessage(get_languageword('Already sent the request And status of the payment is ').$payment_status[0]->status_of_payment, 1);
			redirect($redirect_url);
		}

		$booking_det = $booking_det[0];
		$user_rec 	 = getUserRec($user_id);

		$inputdata['user_id'] 						= $user_id;
		$inputdata['booking_id'] 					= $booking_id;
		$inputdata['booking_type'] 					= 'booking';
		$inputdata['user_type'] 					= 'seller';
		$inputdata['user_name'] 					= $user_rec->username;
		$inputdata['user_paypal_email'] 			= $user_rec->paypal_email;
		$inputdata['user_bank_ac_details'] 			= $user_rec->bank_ac_details;
		// $no_of_credits_to_be_converted = parseInt($booking_det->fee)-parseInt($booking_det->admin_commission_val);
		$no_of_credits_to_be_converted = $booking_det->fee-$booking_det->admin_commission_val;
		$inputdata['no_of_credits_to_be_converted'] = $no_of_credits_to_be_converted;
		$inputdata['admin_commission_val'] 			= $booking_det->admin_commission_val;
		$inputdata['per_credit_cost'] 				= $booking_det->per_credit_value;


		// $total_amount = parseInt($no_of_credits_to_be_converted)*parseInt($booking_det->per_credit_value);
		$total_amount = $no_of_credits_to_be_converted*$booking_det->per_credit_value;
		$inputdata['total_amount'] 					= $total_amount;
		
		$inputdata['created_at'] 					= date('Y-m-d H:i:s');
		$inputdata['updated_at'] 					= $inputdata['created_at'];
		$inputdata['updated_by'] 					= $user_id;
		
		$request_id = $this->base_model->insert_operation_id($inputdata, 'admin_money_transactions');
		if($request_id) {

			//05-12-2018 admin notification start
			$data = array();
			$data['user_id'] 	= $user_id;
			$data['title'] 		= get_languageword('seller_money_request');
			$data['content'] 	= "Seller has been sent money request of credits "." ".$inputdata['no_of_credits_to_be_converted'];
			$data['datetime']   = date('Y-m-d H:i:s');
			$data['admin_read'] = 0;
			$data['page_link']  = SITEURL."admin/seller-money-conversion-requests/Pending";
			$data['table_name'] = "admin_money_transactions";
			$data['primary_key_column'] = "id";
			$data['primary_key_value']  = $request_id;

			$this->base_model->insert_operation($data,'notifications');	
			unset($data);
			//admin notification end

			$this->prepare_flashmessage(get_languageword('Credits to Money conversion request sent successfully'), 0);
			redirect(URL_SELLER_CREDIT_CONVERSION_REQUESTS);
		}else{
			$error  = $this->db->error();
			$this->prepare_flashmessage(get_languageword('Something went wrong Your request not sent'.$error['message']), 2);
			redirect($redirect_url);
		}

	}

	/*********************
	06-12-2018
	**********************/
	/**
	 * [issue_certificate description]
	 * @param  string $booking_id [description]
	 * @return [type]             [description]
	 */
	function issue_certificate($booking_id = "")
	{
		if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_seller()) {
			$this->prepare_flashmessage(get_languageword('You dont have permission to access this page'), 1);
			redirect('auth/login', 'refresh');
		}

		$booking_id = ($this->input->post('booking_id')) ? $this->input->post('booking_id') : $booking_id;

		if(empty($booking_id)) {

			$this->prepare_flashmessage(get_languageword('Please complete your book to send credit conversion request'), 2);
			redirect(URL_SELLER_BUYER_ENQUIRIES.'/closed');
		}

		$user_id = $this->ion_auth->get_user_id();

		//Check whether booking exists
		$booking_det = $this->base_model->fetch_records_from('bookings', array('booking_id' => $booking_id, 'seller_id' => $user_id, 'status' => 'closed'));

		if(empty($booking_det)) {

			$this->prepare_flashmessage(get_languageword('Invalid request'), 1);
			redirect(URL_SELLER_BUYER_ENQUIRIES.'/closed');
		}

		$booking_det = $booking_det[0];

		if($booking_det->is_certificate_issued == "Yes") {

			$this->prepare_flashmessage(get_languageword('Certificate Already Issued'), 2);
			redirect(URL_SELLER_BUYER_ENQUIRIES.'/closed');
		}


		if($this->base_model->update_operation(array('is_certificate_issued' => 'Yes', 'certificate_issue_date' => date("Y-m-d")), 'bookings', array('booking_id' => $booking_id))) {

			$this->prepare_flashmessage(get_languageword('Certificate Issued Successfully'), 0);
			redirect(URL_SELLER_BUYER_ENQUIRIES.'/closed');
		}
	}




	/**
	 * [locations description]
	 * @return [type] [description]
	 */
	function locations()
	{
		if ($this->config->item('site_settings')->seller_locations_enable!=="yes") {
			redirect(URL_SELLER_INDEX, 'refresh');
		}

		if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_seller()) {
			$this->prepare_flashmessage(get_languageword('You dont have permission to access this page'), 1);
			redirect('auth/login', 'refresh');
		}


		$crud = new grocery_CRUD();
		$crud_state = $crud->getState();

		$user_id = $this->ion_auth->get_user_id();

		$crud->set_table($this->db->dbprefix('locations'));
		$crud->where('parent_location_id', 0);
		$crud->where('created_by', $user_id);
		$crud->set_subject( get_languageword('location') );
		$crud->columns('id','location_name','code','status');

		$crud->add_fields(array('location_name', 'slug', 'code', 'parent_location_id', 'status', 'created_at', 'created_by'));
		$crud->edit_fields(array('location_name', 'slug', 'code', 'parent_location_id', 'status', 'updated_at', 'updated_by'));

		//Add Hidden fields
		$crud->field_type('created_at', 'hidden', date('Y-m-d H:i:s')); //Add hidden field
		$crud->field_type('updated_at', 'hidden', date('Y-m-d H:i:s')); //Add hidden field
		$crud->field_type('parent_location_id', 'hidden', 0); //Add hidden field
		$crud->field_type('status', 'hidden', 'Inactive'); //Add hidden field
		$crud->field_type('created_by', 'hidden', $user_id); //Add hidden field
		$crud->field_type('updated_by', 'hidden', $user_id); //Add hidden field

		$crud->required_fields(array('location_name', 'slug', 'code'));
		$crud->display_as('location_name',get_languageword('location_Name'));

		$crud->unique_fields('location_name', 'code');

		$crud->unset_delete();
		$crud->unset_read();

		$crud->add_action('view sub locations', URL_ADMIN_IMAGES.'icon-location.png', 'seller/view_locations','ui-icon-plus'); //TO add custom action link4

		$crud->callback_before_insert(array($this,'callback_loc_before_insert'));
		$crud->callback_before_update(array($this,'callback_loc_before_update'));
		$crud->callback_after_insert(array($this, 'email_admin_after_insert'));
		$crud->callback_after_update(array($this, 'email_admin_after_update'));


		//Allow only seller added records to edit or delete
		if($crud_state == "edit" || $crud_state == "delete") {
			$rec_id = $this->uri->segment(4);
			$is_valid = $this->base_model->fetch_records_from('locations', array('id' => $rec_id, 'created_by' => $user_id));
			if(empty($is_valid)) {

				$this->prepare_flashmessage(get_languageword("invalid_request"), 1);
				redirect(URL_SELLER_LOCATIONS);
			}

		}



		$output = $crud->render();

		$this->data['activemenu'] = 'locations';
		$this->data['pagetitle'] = get_languageword('locations');
		if($crud_state == 'read')
			$crud_state ='View';
		if($crud_state != 'list')
		{
			if($crud_state == 'add')
			$this->data['activesubmenu'] = 'locations-add';
			else if($crud_state == 'edit')
			$this->data['activesubmenu'] = 'locations-edit';
			$this->data['pagetitle'] = get_languageword($crud_state).' '.get_languageword('location');
			$this->data['maintitle'] = get_languageword('locaitons');
			$this->data['maintitle_link'] = URL_SELLER_LOCATIONS;
		}
		else
		{
			$this->data['activemenu'] = 'locations';
			$this->data['pagetitle'] = get_languageword('locations');
		}
		$this->data['grocery_output'] = $output;
		$this->data['grocery'] = TRUE;
		$this->grocery_output($this->data);
	}


	function callback_loc_before_insert($post_array) {

		$post_array['slug'] = prepare_slug($post_array['slug'], 'slug', 'locations');

		return $post_array;
	}


	function callback_loc_before_update($post_array, $primary_key) {

		$prev_name = $this->base_model->fetch_value('locations', 'slug', array('id' => $primary_key));

		//If updates the name
		if($prev_name != $post_array['slug']) {
			$post_array['slug'] = prepare_slug($post_array['slug'], 'slug', 'locations');
		}

		return $post_array;
	}

	/**
	 * [email_admin_after_insert description]
	 * @param  [type] $post_array  [description]
	 * @param  [type] $primary_key [description]
	 * @return [type]              [description]
	 */
	function email_admin_after_insert($post_array,$primary_key)
	{

		//admin notification
		$data = array();
		$data['user_id'] 	= $this->ion_auth->get_user_id();
		$data['title'] 		= get_languageword('seller_added_new_location').' '.$post_array['location_name'];
		$data['content'] 	= "Seller has been added a new location "." ".$post_array['location_name'];
		$data['datetime']   = date('Y-m-d H:i:s');
		$data['admin_read'] = 0;
		$data['page_link']  = SITEURL."locations/index";
		$data['table_name'] = "locations";
		$data['primary_key_column'] = "id";
		$data['primary_key_value']  = $primary_key;

		$this->base_model->insert_operation($data,'notifications');	
		unset($data);
		//admin notification

		return true;
	}

	function email_admin_after_update($post_array,$primary_key)
	{

		//admin notification
		$data = array();
		$data['user_id'] 	= $this->ion_auth->get_user_id();
		$data['title'] 		= get_languageword('seller_updated_a_location').' '.$post_array['location_name'];
		$data['content'] 	= "Seller has been updated a location "." ".$post_array['location_name'];
		$data['datetime']   = date('Y-m-d H:i:s');
		$data['admin_read'] = 0;
		$data['page_link']  = SITEURL."locations/index";
		$data['table_name'] = "locations";
		$data['primary_key_column'] = "id";
		$data['primary_key_value']  = $primary_key;

		$this->base_model->insert_operation($data,'notifications');	
		unset($data);
		//admin notification
		
		return true;
	}

	/**
	 * [view_locations description]
	 * @param  [type] $param [description]
	 * @return [type]        [description]
	 */
	function view_locations( $param )
	{
		if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_seller()) {
			$this->prepare_flashmessage(get_languageword('Please login to access this area'));
			redirect('auth/login');
		}

		if(empty($param))
		{
			$this->prepare_flashmessage(get_languageword('Please select a location'));
			redirect(URL_SELLER_LOCATIONS);
		}

		$user_id = $this->ion_auth->get_user_id();

		$is_valid = $this->base_model->fetch_records_from('locations', array('id' => $param, 'created_by' => $user_id));
		if(empty($is_valid)) {

			$this->prepare_flashmessage(get_languageword("invalid_request"), 1);
			redirect(URL_SELLER_LOCATIONS);
		}

		$locaiton_name = $this->base_model->fetch_records_from('locations', array('id' => $param));
		if(empty($locaiton_name))
		{
			$this->prepare_flashmessage(get_languageword('invalid location'));
			redirect(URL_SELLER_LOCATIONS);
		}
		$locaiton_name = $locaiton_name[0]->location_name;

		$crud = new grocery_CRUD();
		$crud_state = $crud->getState();
		$crud->set_table($this->db->dbprefix('locations'));
		$crud->where('parent_location_id', $param);
		$crud->where('created_by', $user_id);
		$crud->set_subject(get_languageword('Sub Location for ').$locaiton_name);
		$crud->columns('id','location_name','code','status');

		
		$crud->add_fields(array('location_name', 'slug', 'code', 'status', 'parent_location_id', 'created_at', 'created_by'));
		$crud->edit_fields(array('location_name', 'slug', 'code', 'status', 'parent_location_id', 'updated_at', 'updated_by'));
		
		$crud->required_fields(array('location_name', 'code', 'slug', 'status'));
		$crud->unique_fields('location_name', 'code');

		$crud->display_as('location_name',get_languageword('location_Name'));
				
		
		$crud->field_type('parent_location_id', 'hidden', $param); //Add hidden field
		$crud->field_type('created_at', 'hidden', date('Y-m-d H:i:s')); //Add hidden field
		$crud->field_type('updated_at', 'hidden', date('Y-m-d H:i:s')); //Add hidden field
		$crud->field_type('status', 'hidden', 'Inactive'); //Add hidden field
		$crud->field_type('created_by', 'hidden', $user_id); //Add hidden field
		$crud->field_type('updated_by', 'hidden', $user_id); //Add hidden field


		$crud->unset_read();

		$this->data['activemenu'] = 'locations';
		$this->data['activesubmenu'] = 'subLocations';
		$this->data['maintitle'] = get_languageword('locations');
		$this->data['pagetitle'] = get_languageword('sub_Locations');
		$this->data['maintitle_link'] = URL_SELLER_LOCATIONS;

		if($crud_state == 'read')
			$crud_state = 'View';

		if($crud_state != 'list')
		{
			if($crud_state == 'add')
			$this->data['activesubmenu'] = 'add';
			else if($crud_state == 'edit')
			$this->data['activesubmenu'] = 'edit';
			$this->data['pagetitle'] = get_languageword($crud_state).' '.get_languageword('location');
			$this->data['maintitle'] = get_languageword('sub_Locations');
			$this->data['maintitle_link'] = base_url().'seller/view_locations/'.$param;
		}
		else
		{
			$this->data['activesubmenu'] = get_languageword('sub_Locations');
			$this->data['pagetitle'] = get_languageword('sub_Locations');
		}

		$crud->callback_before_insert(array($this,'callback_loc_before_insert'));
		$crud->callback_before_update(array($this,'callback_loc_before_update'));
		$crud->callback_after_insert(array($this, 'email_admin_after_insert'));
		$crud->callback_after_update(array($this, 'email_admin_after_update'));


		//Allow only seller added records to edit or delete
		if($crud_state == "edit" || $crud_state == "delete") {
			$rec_id = $this->uri->segment(5);
			$is_valid = $this->base_model->fetch_records_from('locations', array('id' => $rec_id, 'created_by' => $user_id));
			if(empty($is_valid)) {

				$this->prepare_flashmessage(get_languageword("invalid_request"), 1);
				redirect(URL_SELLER_LOCATIONS);
			}

		}


		$output = $crud->render();		
		$this->data['activemenu'] = 'locations';
		$this->data['grocery_output'] = $output;
		$this->data['grocery'] = TRUE;
		$this->grocery_output($this->data);
	}

	/**
	 * [books description]
	 * @return [type] [description]
	 */
	function books()
	{
		if ($this->config->item('site_settings')->seller_books_enable!=="yes") {
			redirect(URL_SELLER_INDEX, 'refresh');
		}
		
		if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_seller()) {
			$this->prepare_flashmessage(get_languageword('Please login to access this area'));
			redirect('auth/login');
		}

		$crud = new grocery_CRUD();
		$crud_state = $crud->getState();

		$user_id = $this->ion_auth->get_user_id();

		$crud->unset_jquery(); //As we are using admin lte we need to unset default jQuery
		$crud->set_table($this->db->dbprefix('categories'));
		$crud->where('is_parent',0);
		$crud->where('created_by',$user_id);
		$crud->set_subject(get_languageword('book'));
		$crud->columns('name','code', 'slug', 'status');
		$crud->add_fields(array('categories','name', 'slug', 'description', 'code', 'image', 'is_parent'));
		$crud->edit_fields(array('categories','name', 'slug', 'description', 'code', 'image', 'is_parent'));

		$crud->required_fields(array('name', 'slug', 'code', 'status'));
		$crud->unique_fields('name', 'code');
		$crud->set_field_upload('image','assets/uploads/books');
		
		//Field Types
		$crud->field_type('is_parent', 'hidden', '0'); //1-category, 0-book

		$categories = $this->base_model->fetch_records_from('categories', array('is_parent' => 1, 'status' => 1));
		$categories_arr = array('' => get_languageword('no_categories_available'));
		if(!empty($categories))
		{
			foreach($categories as $cat)
			{
				$categories_arr[$cat->id] = $cat->name;
			}
		}
		$crud->field_type('categories', 'multiselect', $categories_arr);

		$crud->unset_read();

		$crud->order_by('id','desc');
		$crud->callback_insert(array($this,'book_insert_callback'));
		$crud->callback_update(array($this,'book_update_callback'));

		//Allow only seller added records to edit or delete
		if($crud_state == "edit" || $crud_state == "delete") {
			$rec_id = $this->uri->segment(4);
			$is_valid = $this->base_model->fetch_records_from('categories', array('is_parent' => 0, 'id' => $rec_id, 'created_by' => $user_id));
			if(empty($is_valid)) {

				$this->prepare_flashmessage(get_languageword("invalid_request"), 1);
				redirect(URL_SELLER_BOOKS);
			}
		}

		$output = $crud->render();
		$this->data['activemenu'] = 'books';

		if($crud_state != 'list')
		{
			if($crud_state == 'add')
			$this->data['activesubmenu'] = 'add';
			if($crud_state == 'edit')
			$this->data['activesubmenu'] = 'edit';
			$this->data['pagetitle'] = get_languageword($crud_state).' '.get_languageword('book');
			$this->data['maintitle'] = get_languageword('books');
			$this->data['maintitle_link'] = URL_SELLER_BOOKS;
		}
		else
		{
			$this->data['pagetitle'] = get_languageword('books');
		}
		
		$this->data['grocery_output'] = $output;
		$this->data['grocery'] = TRUE;
		$this->grocery_output($this->data);
	}

	function book_insert_callback( $post_array )
	{
		$data = array(
			'is_parent' => 0,
			'name' => $post_array['name'],
			'description' => $post_array['description'],
			'code'	=> $post_array['code'],
			'image' => $post_array['image'],
			'slug' => prepare_slug($post_array['slug'], 'slug', 'categories'),
			'status' => 0,
			'created_at' => date('Y-m-d H:i:s'),
			'created_by' => $this->ion_auth->get_user_id(),
			'categories' => implode(',', $post_array['categories']),
		);
		$this->db->insert('categories', $data);
		$insert_id = $this->db->insert_id();
		$this->base_model->delete_record_new($this->db->dbprefix('book_categories'), array('book_id' => $insert_id));
		$categories = $post_array['categories'];
		if(!empty($categories))
		{
			$cats_books = array();
			foreach($categories as $cat)
			{
				$cats_books[] = array('book_id' => $insert_id, 'category_id' => $cat);
			}
			if(!empty($cats_books))
			{
				$this->db->insert_batch('book_categories', $cats_books);
			}
		}


		unset($data);
		//admin notification
		$data = array();
		$data['user_id'] 	= $this->ion_auth->get_user_id();
		$data['title'] 		= get_languageword('seller_added_new_book');
		$data['content'] 	= "Seller has been added a new book "." ".$post_array['name'];
		$data['datetime']   = date('Y-m-d H:i:s');
		$data['admin_read'] = 0;
		$data['page_link']  = SITEURL."categories/books/read/".$insert_id;
		$data['table_name'] = "categories";
		$data['primary_key_column'] = "id";
		$data['primary_key_value']  = $insert_id;

		$this->base_model->insert_operation($data,'notifications');	
		unset($data);
		//admin notification

		return TRUE;
	}

	function book_update_callback( $post_array, $primary_key )
	{

		$data = array(
			'is_parent' => 0,
			'name' => $post_array['name'],
			'description' => $post_array['description'],
			'code'	=> $post_array['code'],
			'image' => $post_array['image'],
			'status' => 0,
			'updated_at' => date('Y-m-d H:i:s'),
			'updated_by' => $this->ion_auth->get_user_id(),
			'categories' => implode(',', $post_array['categories']),
		);

		$prev_name = $this->base_model->fetch_value('categories', 'slug', array('id' => $primary_key));

		//If updates the name
		if($prev_name != $post_array['slug']) {
			$data['slug'] = prepare_slug($post_array['slug'], 'slug', 'categories');
		}


		$this->db->update('categories',$data,array('id' => $primary_key));
		
		$this->base_model->delete_record_new($this->db->dbprefix('book_categories'), array('book_id' => $primary_key));
		$categories = $post_array['categories'];
		if(!empty($categories))
		{
			$cats_books = array();
			foreach($categories as $cat)
			{
				$cats_books[] = array('book_id' => $primary_key, 'category_id' => $cat);
			}
			if(!empty($cats_books))
			{
				$this->db->insert_batch('book_categories', $cats_books);
			}
		}


		unset($data);
		//admin notification
		$data = array();
		$data['user_id'] 	= $this->ion_auth->get_user_id();
		$data['title'] 		= get_languageword('seller_updated_a_book');
		$data['content'] 	= "Seller has been updated a book "." ".$post_array['name'];
		$data['datetime']   = date('Y-m-d H:i:s');
		$data['admin_read'] = 0;
		$data['page_link']  = SITEURL."categories/books/read/".$primary_key;
		$data['table_name'] = "categories";
		$data['primary_key_column'] = "id";
		$data['primary_key_value']  = $primary_key;

		$this->base_model->insert_operation($data,'notifications');	
		unset($data);
		//admin notification
		return TRUE;
	}

	function admin_money_request()
	{
		if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_seller()) {
			$this->prepare_flashmessage(get_languageword('You dont have permission to access this page'), 1);
			redirect('auth/login', 'refresh');
		}

		$seller_id = $this->ion_auth->get_user_id();

		$user_paypal_email = $this->base_model->fetch_value('users', 'paypal_email', array('id' => $seller_id));
		
		$this->data['activemenu'] 	= "user_credit_transactions";
		$this->data['pagetitle'] = get_languageword('user_credit_transactions');
		
		$this->data['message'] = $this->session->flashdata('message');
		
		$user_id = $this->ion_auth->get_user_id();

		if(isset($_POST['submitbutt']))
		{
			$user_net_credits = round(get_user_credits($user_id));

			$this->form_validation->set_rules('credits', get_languageword('credits'), 'numeric|required|less_than_equal_to['.$user_net_credits.']');
			$this->form_validation->set_error_delimiters('<div class="error">', '</div>');

			if ($this->form_validation->run() == TRUE )
			{
				if ($user_paypal_email != '') {
					$inputdata['user_id'] = $this->input->post('user_id');
					$inputdata['user_type'] = $this->input->post('user_type');
					$inputdata['user_name'] = $this->input->post('user_name');
					$inputdata['user_paypal_email'] = $this->input->post('user_paypal_email');
					$inputdata['user_bank_ac_details'] = $inputdata['user_bank_ac_details'];
					$inputdata['per_credit_cost'] = $this->input->post('user_per_credit_cost');
					$inputdata['no_of_credits_to_be_converted'] = $this->input->post('credits');
					$inputdata['total_amount'] = $inputdata['no_of_credits_to_be_converted'] * $inputdata['per_credit_cost'];

					//echo '<pre> $inputdata :: '; print_r($inputdata); die;

					$this->base_model->insert_operation($inputdata, 'admin_money_transactions');

					$this->base_model->addupdate_pointsystem($inputdata['user_id'], '', "Credit Withdrawal Request", $inputdata['no_of_credits_to_be_converted'], 'debited');

					$this->prepare_flashmessage(get_languageword('Credits Updated'), 0);

					$user_rec = getUserRec($seller_id);

					//send email to seller start
					$from = get_system_settings('Portal_Email');
					$to = $user_rec->email;

					// echo "<pre>";print_r($to);
					// die();

					$sub = get_languageword("Credit Withdrawal Request");

					$logo_img = '<img src="' . get_site_logo() . '" class="img-responsive" width="120px" height="50px">';
					$site_title = $this->config->item('site_settings')->site_title;

					$msg = '<p>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp;<strong>'.$logo_img.'</strong></p>
					<p>&nbsp;</p>
					<p>&nbsp;</p>
					<p> Dear '.$inputdata['user_name'].',</p>
					<p>You have successfully requested credits conversation to money</p>
					<p>User Paypal Email <strong>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp;<strong>'.$inputdata['user_paypal_email'].'</strong></p>
					 <p>Per Credit Cost <strong>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp;<strong>'.$inputdata['per_credit_cost'].'</strong></p>
					 <p>No. of Credits to be Converted <strong>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp;<strong>'.$inputdata['no_of_credits_to_be_converted'].'</strong></p>
					 <p>Total Amount <strong>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp;<strong>'.$inputdata['total_amount'].'</strong></p>
					<p>&nbsp;</p>
					<p>&nbsp;</p>
					<p>&nbsp;</p>
					<p>
					 <strong>Regards,</strong>&nbsp;<br />
					 <br />
					  <strong>__SITE_TITLE__</strong></p>
					<p>&nbsp;</p>';

					sendEmail($from, $to, $sub, $msg);
					//send email to seller end


					//send email to admin start
					$from = get_system_settings('Portal_Email');
					$to = get_system_settings('Portal_Email');

					// echo "<pre>";print_r($to);
					// die();

					$sub = get_languageword("Credit Withdrawal Request");

					$logo_img = '<img src="' . get_site_logo() . '" class="img-responsive" width="120px" height="50px">';

					$site_title = $this->config->item('site_settings')->site_title;

					$msg = '<p>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp;<strong>'.$logo_img.'</strong></p>
					<p>&nbsp;</p>
					<p>&nbsp;</p>
					<p> Dear '.$inputdata['user_name'].',</p>
					<p>You have successfully requested credits conversation to money</p>
					<p>User Paypal Email <strong>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp;<strong>'.$inputdata['user_paypal_email'].'</strong></p>
					 <p>Per Credit Cost <strong>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp;<strong>'.$inputdata['per_credit_cost'].'</strong></p>
					 <p>No. of Credits to be Converted <strong>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp;<strong>'.$inputdata['no_of_credits_to_be_converted'].'</strong></p>
					 <p>Total Amount <strong>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp;<strong>'.$inputdata['total_amount'].'</strong></p>
					<p>&nbsp;</p>
					<p>&nbsp;</p>
					<p>&nbsp;</p>
					<p>
					 <strong>Regards,</strong>&nbsp;<br />
					 <br />
					  <strong>__SITE_TITLE__</strong></p>
					<p>&nbsp;</p>';

					sendEmail($from, $to, $sub, $msg);
					//send email to admin end

					redirect('seller/credits-transactions-history');
				} else {
					$url = base_url('seller/personal_info');

					$this->data['message'] = $this->prepare_message('To withdraw you must need to input your paypal account from <a href="'.$url.'">here</a>', 1);
				}
			}
			else
			{
				$this->data['message'] = $this->prepare_message(validation_errors(), 1);
			}
		}

		$this->data['profile'] = getUserRec();
		$this->data['content'] = 'admin_money_request';
		$this->_render_page('template/site/seller-template', $this->data);
	}

}
