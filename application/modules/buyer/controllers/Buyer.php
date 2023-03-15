<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// phpinfo();
// die();

class Buyer extends MY_Controller 
{
	function __construct()
	{
		parent::__construct();
		
		$this->load->library(array('session'));
		$this->load->library(array('ion_auth','form_validation'));
		$this->load->model('home_model');
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
		if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_buyer()) {
			$this->prepare_flashmessage(get_languageword('You dont have permission to access this page'), 1);
			redirect('auth/login', 'refresh');
		}

		$this->data['message'] = $this->session->flashdata('message');
		$user_id = $this->ion_auth->get_user_id();
		$this->load->model('buyer_model');
		$buyer_dashboard_data = $this->buyer_model->get_buyer_dashboard_data($user_id);
		$this->data['buyer_dashboard_data']	= $buyer_dashboard_data;	
		
		$profile = getUserRec();

		$this->data['pagetitle'] 	= get_languageword('dashboard');
		$this->data['activemenu'] 	= "dashboard";
		$this->data['content'] 		= 'index';
		$this->_render_page('template/site/buyer-template', $this->data);
	}
	
	/**
	 * Fecilitates to upload gallery pictures
	 *
	 * @access	public
	 * @return	string
	 */
	function my_gallery()
	{
			
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
		$this->data['activesubmenu'] 	= "gallery";
		$this->grocery_output($this->data);

	}

	/**
	 * Fecilitates to update buyer leads
	 * @access	public
	 * @return	string
	 */
	function buyer_leads()
	{
		if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_buyer()) {
			$this->prepare_flashmessage(get_languageword('You dont have permission to access this page'), 1);
			redirect('auth/login', 'refresh');
		}
		
		$user_id = $this->ion_auth->get_user_id();
		$this->data['message'] = $this->session->flashdata('message');
		$this->data['profile'] = getUserRec();
		$this->load->library(array('grocery_CRUD'));
		$crud = new grocery_CRUD();
		$crud_state = $crud->getState();
		$crud->set_table($this->db->dbprefix('buyer_leads'));
		$crud->where('user_id', $user_id);
		$crud->set_relation('teaching_type_id','teaching_types','teaching_type');
		$crud->set_relation('location_id','locations','location_name');
		$crud->set_relation('book_id','categories','name');
		$crud->set_subject( get_languageword('buyer_leads') );
		
		$crud->unset_add();
		$crud->unset_delete();
		 
    			
		$crud->columns('book_id','teaching_type_id','location_id','title_of_requirement','updated_at','duration_needed', 'no_of_views','status');

		

		//########Eidt fields only#######
		$crud->edit_fields('status', 'updated_at');

		$crud->field_type('updated_at', 'hidden', date('Y-m-d H:i:s'));

		//####### Changing column names #######
		$crud->display_as('updated_at','Last Updated');
		$crud->display_as('book_id','Book Name');
		$crud->display_as('teaching_type_id','Teaching Type');
		$crud->display_as('location_id','Location');
		$crud->display_as('duration_needed','Duration');


		//#### Invisible fileds in reading ####
		if ($crud->getState() == 'read') {
		    $crud->field_type('user_id', 'hidden');
		    $crud->field_type('updated_at', 'visible');
		}


		$output = $crud->render();
		
		$this->data['activemenu'] 	= "myleads";
		$this->data['activesubmenu'] 	= "buyer_leads";
		$this->data['content'] 		= 'buyer_leads';
		$this->data['pagetitle'] = get_languageword('Buyer Leads');
		$this->data['grocery_output'] = $output;
		$this->data['grocery'] = TRUE;
		$this->grocery_output($this->data);
	}

	
	/**
	 * Fecilitates to update personal information
	 *
	 * @access	public
	 * @return	string
	 */
	function post_requirement()
	{
		if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_buyer()) {
			$this->prepare_flashmessage(get_languageword('You dont have permission to access this page'), 1);
			redirect('auth/login', 'refresh');
		}
		
		$this->data['message'] = $this->session->flashdata('message');

		$buyer_id = $this->ion_auth->get_user_id();

		if(isset($_POST['submitbutt']))
		{
			$this->form_validation->set_rules('location_id', get_languageword('location'), 'trim|required|xss_clean');
			$this->form_validation->set_rules('teaching_type_id', get_languageword('teaching_type'), 'trim|required|xss_clean');
			$this->form_validation->set_rules('priority_of_requirement', get_languageword('priority_of_requirement'), 'trim|required|xss_clean');
			$this->form_validation->set_rules('duration_needed', get_languageword('duration_needed'), 'trim|required|xss_clean');
			$this->form_validation->set_rules('title_of_requirement', get_languageword('title_of_requirement'),'trim|required|xss_clean');
			$this->form_validation->set_rules('book_id', get_languageword('book'), 'trim|required|xss_clean');
						
			$this->form_validation->set_error_delimiters('<div class="error">', '</div>');
			
			if ($this->form_validation->run() == TRUE)
			{	
				

				$inputdata['user_id']=$buyer_id;
				$inputdata['location_id'] = $this->input->post('location_id');
				$inputdata['book_id'] = $this->input->post('book_id');
				$inputdata['teaching_type_id'] = $this->input->post('teaching_type_id');
				$inputdata['present_status']=$this->input->post('present_status');
				$inputdata['priority_of_requirement']=$this->input->post('priority_of_requirement');
				$inputdata['duration_needed']=$this->input->post('duration_needed');
				$inputdata['budget']=$this->input->post('budget');
				$inputdata['budget_type']=$this->input->post('budget_type');
				$inputdata['requirement_details']=$this->input->post('requirement_details');
				$inputdata['title_of_requirement']=$this->input->post('title_of_requirement' );
				$inputdata['created_at']=date("Y-m-d H:i:s");
				$inputdata['updated_at']=$inputdata['created_at'];

				$is_duplicate = $this->base_model->fetch_records_from('buyer_leads', array('location_id' => $inputdata['location_id'], 'book_id' => $inputdata['book_id'], 'teaching_type_id' => $inputdata['teaching_type_id'], 'budget' => $inputdata['budget'], 'budget_type' => $inputdata['budget_type'], 'priority_of_requirement' => $inputdata['priority_of_requirement'], 'status' => 'Opened'));

				if(count($is_duplicate) > 0) {

					$this->prepare_flashmessage(get_languageword('you_have_already_posted_the_same_requirement'), 2);
					redirect(URL_BUYER_LEADS);
				}


				//05-12-2018 admin notification start
				$lead_id = $this->base_model->insert_operation_id($inputdata, 'buyer_leads');

				$data = array();
				$data['user_id'] 	= $buyer_id;
				$data['title'] 		= get_languageword('buyer_posted_new_lead');
				$data['content'] 	= "Buyer has been Posted a Lead "." ".$this->input->post('title_of_requirement');
				$data['datetime']   = date('Y-m-d H:i:s');
				$data['admin_read'] = 0;
				$data['page_link']  = SITEURL."admin/all_leads";
				$data['table_name'] = "buyer_leads";
				$data['primary_key_column'] = "id";
				$data['primary_key_value']  = $lead_id;

				$this->base_model->insert_operation($data,'notifications');	
				unset($data);
				//admin notification end



				$this->prepare_flashmessage(get_languageword('your requirement posted successfully'), 0);
				redirect('buyer/buyer-leads');	
			}
			else
			{
				$this->data['message'] = $this->prepare_message(validation_errors(), 1);
			}			
		
		}	
		$this->data['profile'] = getUserRec();

		//location options
		$locations = $this->home_model->get_locations($params = array('child' => 1));
		$location_opts[''] = get_languageword('select_location');
		foreach ($locations as $key => $value) {
			$location_opts[$value->id] = $value->location_name;
		}
		$this->data['location_opts'] = $location_opts;
		
		//Book Options
		$books = $this->home_model->get_books();
		$book_opts[''] = get_languageword('type_of_book');
		foreach ($books as $key => $value) {
			$book_opts[$value->id] = $value->name;
		}
		$this->data['book_opts'] = $book_opts;
		//Teaching type Options
		$teaching_types = $this->base_model->fetch_records_from('teaching_types');
		$teaching_types_options = array();
		foreach ($teaching_types as $key => $value) {
			$teaching_types_options[$value->id] = $value->teaching_type;
		}
		$this->data['teaching_types_options'] = $teaching_types_options;

		$this->data['activemenu'] 	= "myleads";
		$this->data['activesubmenu'] = "post_requirement";
		$this->data['content'] 		= 'post_requirement';
		$this->data['pagetitle'] = get_languageword('Post Requirement');
		$this->_render_page('template/site/buyer-template', $this->data);


	}



	/**
	 * Fecilitates to update personal information
	 *
	 * @access	public
	 * @return	string
	 */
	function personal_info()
	{
		if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_buyer()) {
			$this->prepare_flashmessage(get_languageword('You dont have permission to access this page'), 1);
			redirect('auth/login', 'refresh');
		}

		$this->data['message'] = $this->session->flashdata('message');

		$user_id = $this->ion_auth->get_user_id();


		if(isset($_POST['submitbutt']))
		{
			$this->form_validation->set_rules('first_name', get_languageword('first_name'), 'trim|required|xss_clean');
			$this->form_validation->set_rules('dob', get_languageword('date_of_birth'), 'trim|required|xss_clean');
			$this->form_validation->set_error_delimiters('<div class="error">', '</div>');
			
			if ($this->form_validation->run() == TRUE)
			{
				$first_name = ucfirst(strtolower($this->input->post('first_name')));
				$last_name = ucfirst(strtolower($this->input->post('last_name')));
				$username =  $first_name.' '.$last_name;

				$prev_username = $this->base_model->fetch_value('users', 'username', array('id' => $user_id));

				//If user updates the username
				if($prev_username != $username) {
					$slug = prepare_slug($username, 'slug', 'users');
					$inputdata['slug'] =  $slug;
				}

				$inputdata['first_name'] = $first_name;
				$inputdata['last_name'] = $last_name;
				$inputdata['username'] = $username;
				$inputdata['gender'] = $this->input->post('gender');
				$inputdata['dob'] = $this->input->post('dob');
				$inputdata['website'] = $this->input->post('website');
				$inputdata['facebook'] = $this->input->post('facebook');
				$inputdata['twitter'] = $this->input->post('twitter');
				$inputdata['linkedin'] = $this->input->post('linkedin');
				$inputdata['skype'] = $this->input->post('skype');
				
				$language_of_teaching = $this->input->post('language_of_teaching');
				if(!empty($language_of_teaching))
				$inputdata['language_of_teaching'] = implode(',', $language_of_teaching);

				
				$this->base_model->update_operation($inputdata, 'users', array('id' => $user_id));
				
				$this->prepare_flashmessage(get_languageword('profile updated successfully'), 0);
				redirect('buyer/personal-info');				
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
		$this->data['content'] 		= 'personal_info';
		$this->data['pagetitle'] = get_languageword('Personal Information');
		$this->_render_page('template/site/buyer-template', $this->data);
	}
	
	/**
	 * Fecilitates to update profile information includes profile picture
	 *
	 * @access	public
	 * @return	string
	 */
	function profile_information()
	{
		if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_buyer()) {
			$this->prepare_flashmessage(get_languageword('You dont have permission to access this page'), 1);
			redirect('auth/login', 'refresh');
		}
		
		$this->data['message'] = $this->session->flashdata('message');
		
		if(isset($_POST['submitbutt']))
		{

			$this->form_validation->set_rules('profile', get_languageword('profile_description'), 'trim|required|max_length[500]|xss_clean');
			$this->form_validation->set_rules('seo_keywords',get_languageword('seo_keywords'), 'trim|max_length[100]|xss_clean');
			$this->form_validation->set_rules('meta_desc',get_languageword('meta_description'),'trim|max_length[100]|xss_clean');
			$this->form_validation->set_rules('profile_page_title', get_languageword('profile_page_title'), 'trim|required|xss_clean');
			
			if($_FILES['photo']['name'] != '')
			{
				$this->form_validation->set_rules('photo', get_languageword('Profile Image'), 'trim|callback__image_check');
			}

			$this->form_validation->set_error_delimiters('<div class="error">', '</div>');
			
			if ($this->form_validation->run() == TRUE)
			{
				$user_id = $this->ion_auth->get_user_id();
				$inputdata['profile'] = $this->input->post('profile');
				$inputdata['profile_page_title'] = $this->input->post('profile_page_title');
				$inputdata['seo_keywords'] = $this->input->post('seo_keywords');
				$inputdata['meta_desc'] = $this->input->post('meta_desc');
				$inputdata['qualification'] = $this->input->post('qualification');
				
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
				redirect('buyer/profile-information');				
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
		$this->data['content'] 		= 'profile_information';
		$this->data['pagetitle'] = get_languageword('Profile Information');
		$this->_render_page('template/site/buyer-template', $this->data);
	}
	
	public function _image_check()
	{
		$image = $_FILES['photo']['name'];
		$name = explode('.',$image);
		
		if(count($name)>2 || count($name)<= 0) {
           $this->form_validation->set_message('_image_check', 'Only jpg / jpeg / png images are accepted');
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
	 * [manage_books description]
	 * @return [type] [description]
	 */
	function manage_books()
	{
		if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_buyer()) {
			$this->prepare_flashmessage(get_languageword('You dont have permission to access this page'), 1);
			redirect('auth/login', 'refresh');
		}
		
		$this->data['message'] = $this->session->flashdata('message');
		$this->load->model('buyer_model');
		$studnentPrefferedBookIds	= $this->buyer_model->get_buyer_preffered_book_ids($this->ion_auth->get_user_id()); //Getting locaiton ids
		

		if ($this->input->post()) 
		{		
			if ($this->input->post('buyer_books')) {
				$user_id = $this->ion_auth->get_user_id();
				if ($this->input->post('buyer_books') != $studnentPrefferedBookIds) {
					$buyer_books 	= $this->input->post('buyer_books');
					if ($this->base_model->delete_record_new('buyer_preffered_books', array('buyer_id'=> $user_id))) {
						$data['buyer_id'] 	= $this->ion_auth->get_user_id();
						$data['created_at'] = date('Y-m-d H:i:s');
						foreach($buyer_books as $books) {
							if (is_numeric($books)) {
								$data['book_id'] = $books;
								$this->base_model->insert_operation($data, 'buyer_preffered_books');
							}
						}

						$is_profile_updated = $this->ion_auth->user($user_id)->row()->is_profile_update;

						if($is_profile_updated != 1) {

							$stu_pref_teaching_types = $this->base_model->fetch_records_from('buyer_prefferd_teaching_types', array('buyer_id' => $user_id, 'status' => 1));
							if(count($stu_pref_teaching_types) > 0)
								$this->base_model->update_operation(array('is_profile_update' => 1), 'users', array('id' => $user_id));
						}

						$this->prepare_flashmessage(get_languageword('books') . " " . get_languageword('updated successfully'), 0);
					}
					else
					{
						$this->prepare_flashmessage(get_languageword('books') . " " . get_languageword('failed to updated'), 1);
					}						
				}
				else {
					$this->prepare_flashmessage(get_languageword('You have not done any changes'), 2);
				}
			}
			else {
				$this->prepare_flashmessage(
				get_languageword('please_select_atleast_one_preferred_book'), 1);
			}
			redirect('buyer/manage-books');
		}
		
		$this->data['books'] 				  =   $this->home_model->get_popular_books('','',false);
		$this->data['studnentPrefferedBookIds'] =   $studnentPrefferedBookIds;
		$this->data['activemenu'] 	= "manage";
		$this->data['activesubmenu'] = "manage_books";
		$this->data['pagetitle'] = get_languageword('books');		
		$this->data['content'] 		= 'manage_books';

		$this->_render_page('template/site/buyer-template', $this->data);
	}
	
	
	/**
	 * Fecilitates to update contact information
	 *
	 * @access	public
	 * @return	string
	 */
	function update_contact_information()
	{
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
				redirect('buyer/update-contact-information');				
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
		$this->data['activemenu'] 	 = "account";
		$this->data['activesubmenu'] = "update_contact_info";
		$this->data['content'] 		 = 'update_contact_information';
		$this->data['pagetitle'] 	 = get_languageword('update_contact_information');
		$this->_render_page('template/site/buyer-template', $this->data);
	}
	
	/**
	 * Fecilitates to view contact information
	 *
	 * @access	public
	 * @return	string
	 */
	function contact_information()
	{
		$this->data['profile'] = getUserRec();
		$this->data['activemenu'] 	= "account";
		$this->data['activesubmenu'] = "update_contact_info";	
		$this->data['content'] 		= 'contact_information';
		$this->data['pagetitle'] = get_languageword('My Address');
		$this->_render_page('template/site/buyer-template', $this->data);
	}
	
	/**
	 * Fecilitates to add or update buyer buyering subjects
	 *
	 * @access	public
	 * @return	string
	 */
	function manage_subjects()
	{
		$this->data['message'] = $this->session->flashdata('message');
		$this->load->model('buyer_model');
		$buyerSubjectIds 	= $this->buyer_model->get_buyer_subject_ids(
		$this->ion_auth->get_user_id()); //Getting buyer selected subject ids
		
		if ($this->input->post()) {	
		
			if ($this->input->post('buyer_subjects')) {
				if ($this->input->post('buyer_subjects') != $buyerSubjectIds) {
					$buyer_subjects 	= $this->input->post('buyer_subjects');
					if ($this->base_model->delete_record_new('buyer_subjects', array(
						'user_id' 	=> $this->ion_auth->get_user_id()))) {
						$data['user_id'] 		= $this->ion_auth->get_user_id();
						foreach($buyer_subjects as $subject) {
							if (is_numeric($subject)) {
								$data['subject_id'] = $subject;
								$this->base_model->insert_operation($data, 'buyer_subjects');
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
			redirect('buyer/manage-subjects', 'refresh');
		}
		
		$this->data['subjects'] 	= $this->buyer_model->get_subjects();
		$this->data['buyerSubjectIds'] 	= $buyerSubjectIds;
		
		$this->data['activemenu'] 	= "manage";
		$this->data['activesubmenu'] = "manage_subjects";	
		$this->data['content'] 		= 'manage_subjects';
		$this->_render_page('template/site/buyer-template', $this->data);
	}
	
	/**
	 * Fecilitates to add or update buyer locations, where he is buyering
	 *
	 * @access	public
	 * @return	string
	 */
	function manage_locations()
	{
		if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_buyer()) {
			$this->prepare_flashmessage(get_languageword('You dont have permission to access this page'), 1);
			redirect('auth/login', 'refresh');
		}
		
		$this->data['message'] = $this->session->flashdata('message');
		$this->load->model('buyer_model');
		$buyerLocationIds 	= $this->buyer_model->get_buyer_location_ids(
		$this->ion_auth->get_user_id()); //Getting locaiton ids
		
		if ($this->input->post()) 
		{		
			$user_id = $this->ion_auth->get_user_id();
			if ($this->input->post('buyer_locations')) {
				if ($this->input->post('buyer_locations') != $buyerLocationIds) {
					$buyer_locations 	= $this->input->post('buyer_locations');
					if ($this->base_model->delete_record_new('buyer_locations', array('buyer_id'=> $user_id))) {
						$data['buyer_id'] 	= $this->ion_auth->get_user_id();
						$data['created_at'] = date('Y-m-d H:i:s');
						foreach($buyer_locations as $location) {
							if (is_numeric($location)) {
								$data['location_id'] = $location;
								$this->base_model->insert_operation($data, 'buyer_locations');
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
			redirect('buyer/manage-locations');
		}
		
		$this->data['locations'] 				= $this->buyer_model->get_locations();
		$this->data['buyerLocationIds'] 		= $buyerLocationIds;
		
		$this->data['activemenu'] 	= "manage";
		$this->data['activesubmenu'] = "manage_locations";
		$this->data['pagetitle'] = get_languageword('Locations');		
		$this->data['content'] 		= 'manage_locations';
		$this->_render_page('template/site/buyer-template', $this->data);
	}
	
	/**
	 * Fecilitates to add or update buyer teaching types
	 *
	 * @access	public
	 * @return	string
	 */
	function manage_teaching_types()
	{

		if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_buyer()) {
			$this->prepare_flashmessage(get_languageword('You dont have permission to access this page'), 1);
			redirect('auth/login', 'refresh');
		}

		$this->data['message'] = $this->session->flashdata('message');
		$this->load->model('buyer_model');
		$buyerSelectedTypeIds 	= $this->buyer_model->get_buyer_selected_teachingtype_ids(
		$this->ion_auth->get_user_id());
		
		if ($this->input->post()) 
		{
		
			if ($this->input->post('buyer_selected_types')) {

				$user_id = $this->ion_auth->get_user_id();
				if ($this->input->post('buyer_selected_types') != $buyerSelectedTypeIds) {
					$buyer_selected_types 	= $this->input->post('buyer_selected_types');
					 
					if ($this->base_model->delete_record_new('buyer_prefferd_teaching_types', array('buyer_id' => $user_id))) {
						$data['buyer_id'] 		= $this->ion_auth->get_user_id();
						$data['created_at'] 	= date('Y-m-d H:i:s');
						foreach($buyer_selected_types as $buyer_type) {
							if (is_numeric($buyer_type)) {
								$data['teaching_type_id'] = $buyer_type;
								$this->base_model->insert_operation($data, 'buyer_prefferd_teaching_types');
							}
						}

						$is_profile_updated = $this->ion_auth->user($user_id)->row()->is_profile_update;

						if($is_profile_updated != 1) {

							$stu_pref_books = $this->base_model->fetch_records_from('buyer_preffered_books', array('buyer_id' => $user_id, 'status' => 1));
							if(count($stu_pref_books) > 0)
								$this->base_model->update_operation(array('is_profile_update' => 1), 'users', array('id' => $user_id));
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
			redirect('buyer/manage-teaching-types', 'refresh');
		}
		
		$this->data['buyer_types'] 				= $this->buyer_model->get_teachingtypes();
		$this->data['buyerSelectedTypeIds']	 	= $buyerSelectedTypeIds;
		$this->data['activemenu'] 	= "manage";
		$this->data['activesubmenu'] = "manage_teaching_types";	
		$this->data['pagetitle']	= get_languageword('preffered_teaching_types');
		$this->data['content'] 		= 'manage_teaching_types';
		$this->_render_page('template/site/buyer-template', $this->data);

	}


	/**
	 * Fecilitates to upload certificates
	 *
	 * @access	public
	 * @return	string
	 */
	function certificates()
	{
		if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_buyer()) {
			$this->prepare_flashmessage(get_languageword('You dont have permission to access this page'), 1);
			redirect('auth/login', 'refresh');
		}
		
		$this->data['message'] = $this->session->flashdata('message');
		$user_id = $this->ion_auth->get_user_id();
		
		if(isset($_POST['submitbutt']))
		{

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
							if(isset($this->config->item('site_settings')->need_admin_for_buyer) && $this->config->item('site_settings')->need_admin_for_buyer == 'yes')
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

								//save the url and the file
								$filePath = './assets/uploads/certificates/'.$shortname;
								//Upload the file into the temp dir
								if(move_uploaded_file($tmpFilePath, $filePath)) 
								{								
									$user_image['user_id']				= $user_id;
									$user_image['admin_certificate_id'] = 0;
									if(isset($this->config->item('site_settings')->need_admin_for_buyer) && $this->config->item('site_settings')->need_admin_for_buyer == 'yes')
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
			redirect('buyer/certificates');
		}
		
		$certificates = $this->base_model->fetch_records_from('certificates', array('certificate_for' => 'buyers', 'status' => 'Active'));		
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
		$this->data['activesubmenu'] = "certificates";
		$this->data['pagetitle']	= get_languageword('certificates');
		$this->data['content'] 		= 'certificates';
		$this->_render_page('template/site/buyer-template', $this->data);
	}
		
	/**
	 * Fecilitates to set privacy
	 *
	 * @access	public
	 * @return	string
	 */
	function manage_privacy()
	{
		if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_buyer()) {
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
			redirect('buyer/manage-privacy');			
		}
		
		$this->data['pagetitle'] = get_languageword('Manage Privacy');
		$this->data['activemenu'] 	= "manage";
		$this->data['activesubmenu'] = "manage_privacy";	
		$this->data['content'] 		= 'manage_privacy';

		$this->_render_page('template/site/buyer-template', $this->data);
	}

	/**
	 * Facilitates to display packages for buyer.
	 *
	 * @access	public
	 * @param	string (Optional)
	 * @return	string
	 */	
	function list_packages($param1 = '')
	{
		if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_buyer()) {
			$this->prepare_flashmessage(get_languageword('You dont have permission to access this page'), 1);
			redirect('auth/login', 'refresh');
		}
		
		$this->data['message'] = $this->session->flashdata('message');
		
		$this->data['pagetitle'] = get_languageword('packages');
		$this->load->model('buyer_model');
		$this->data['package_data'] = $this->buyer_model->list_buyer_packages();
		$this->data['payment_gateways'] = $this->base_model->get_payment_gateways('', 'Active');

		$this->data['activemenu'] 	= "packages";
		$this->data['activesubmenu'] = "list_packages";	
		$this->data['content'] 		= 'list_packages';
		$this->_render_page('template/site/buyer-template', $this->data);
	}

	/**
	 * [mysubscriptions description]
	 * @return [type] [description]
	 */
	function mysubscriptions()
	{
		if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_buyer()) {
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
		$crud->callback_column('amount_paid',array($this,'callback_amount_paid'));
		$output = $crud->render();
		
		$this->data['pagetitle'] = get_languageword('My Subscriptions');
		$this->data['activemenu'] 	= "packages";
		$this->data['activesubmenu'] 	= "mysubscriptions";
		$this->data['pagetitle'] = get_languageword('Subscriptions');		
		$this->data['content'] 		= 'mysubscriptions';
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
				$value .= '&nbsp;<a href="'.URL_BUYER_MANUAL.'/'.$row->id.'"><img src="'.URL_FRONT_IMAGES . 'error.png"></a>';
			} else {
				$value .= '&nbsp;<img src="'.URL_FRONT_IMAGES . 'error.png">';
			}
		}
		return $value;
	}
	
	/**
	 * [book_seller description]
	 * @return [type] [description]
	 */
	function book_seller()
	{
		if (!$this->ion_auth->logged_in()) {
			$this->prepare_flashmessage(get_languageword('please_login_to_book_seller'), 2);
			redirect('auth/login', 'refresh');
		}

		if(!$this->ion_auth->is_buyer()) {
			$this->prepare_flashmessage(get_languageword('You dont have permission to access this page'), 1);
			redirect('auth', 'refresh');
		}


		if(!$this->input->post()) {

			$this->prepare_flashmessage(get_languageword('invalid_request'), 1);
			redirect(URL_HOME_SEARCH_SELLER, 'refresh');
		}


		$buyer_id 		= $this->ion_auth->get_user_id();
		$seller_id   		= $this->input->post('seller_id');
		$seller_slug   		= $this->input->post('seller_slug');
		$book_slug		= $this->input->post('book_slug');


		//Check Whether buyer is premium user or not
		if(!is_premium($buyer_id)) {

			$this->prepare_flashmessage(get_languageword('please_become_premium_member_to_book_seller'), 2);
			redirect(URL_BUYER_LIST_PACKAGES, 'refresh');
		}

		$book_details = $this->home_model->get_seller_book_details($book_slug, $seller_id);

		//Check whether Seller teaches the book or not
		if(empty($book_details)) {

			$this->prepare_flashmessage(get_languageword('no_book_details_found'), 2);
			redirect(URL_HOME_SELLER_PROFILE.'/'.$seller_slug, 'refresh');
		}

		$book_id 				= $book_details->book_id;
		$fee 					= $book_details->fee;

		//Check If buyer has sufficient credits to book seller
		if(!is_eligible_to_make_booking($buyer_id, $fee)) {

			$this->prepare_flashmessage(get_languageword("you_do_not_have_enough_credits_to_book_the_seller_Please_get_required_credits_here"), 2);
			redirect(URL_BUYER_LIST_PACKAGES, 'refresh');
		}

		$start_date  			= date('Y-m-d', strtotime($this->input->post('start_date')));
		$time_slot   			= $this->input->post('time_slot');

		//Check If buyer already booked the seller on the same slot and it is not yet approved by seller
		if($this->home_model->is_already_booked_the_seller($buyer_id, $seller_id, $book_id, $start_date, $time_slot)) {

			$this->prepare_flashmessage(get_languageword('you_already_booked_this_seller_and_your_book_not_yet_completed'), 2);
			redirect(URL_HOME_SELLER_PROFILE.'/'.$seller_slug, 'refresh');
		}

		//Check If selected time-slot is available
		if(empty($book_details->time_slots) || !$this->home_model->is_time_slot_avail($seller_id, $book_id, $start_date, $time_slot)) {

			$this->prepare_flashmessage(get_languageword('time_slot_not_available'), 2);
			redirect(URL_HOME_SELLER_PROFILE.'/'.$seller_slug, 'refresh');
		}


		$content 				= $book_details->content;
		$duration_value 		= $book_details->duration_value;
		$duration_type 			= $book_details->duration_type;
		$per_credit_value 		= $book_details->per_credit_value;
		$days_off 				= $book_details->days_off;

		$preferred_location 	= ($this->input->post('teaching_type') == "willing-to-travel") ? $this->input->post('location_slug') : $this->input->post('teaching_type');
		$message   				= $this->input->post('message');

		if($duration_type == "hours") {

			$formatted  = str_replace(':', '.', $time_slot);
			$time 	    = explode('-', str_replace(' ', '', $formatted));

			$start_time = number_format($time[0],2);
			$end_time   = number_format($time[1],2);

			$total_time = $end_time - $start_time;

			if($total_time >= 1) {

				$days = round($duration_value / $total_time);

			} else {

				$total_time = (int)(explode('.', number_format($total_time,2))[1]);
				$days = round($duration_value / ($total_time/60));
			}

			$end_date = date("Y-m-d", strtotime($start_date.'+'.$days.' days'));

		} else {

			$end_date = date("Y-m-d", strtotime($start_date.'+'.$duration_value.' '.$duration_type));
		}

		$end_date = date("Y-m-d", strtotime($end_date.'-1 days'));

		$admin_commission   	= get_system_settings('admin_commission_for_a_booking');
		$admin_commission_val   = round($fee * ($admin_commission / 100));

		$created_at   		= date('Y-m-d H:i:s');
		$updated_at   		= $created_at;
		$updated_by   		= $buyer_id;


		$inputdata	=	array(
								'buyer_id'			=> $buyer_id,
								'seller_id'				=> $seller_id,
								'book_id'				=> $book_id,
								'content'				=> $content,
								'duration_value'		=> $duration_value,
								'duration_type'			=> $duration_type,
								'fee'					=> $fee,
								'per_credit_value'		=> $per_credit_value,
								'start_date'			=> $start_date,
								'end_date'				=> $end_date,
								'time_slot'				=> $time_slot,
								'days_off'				=> $days_off,
								'preferred_location'	=> $preferred_location,
								'message'				=> $message,
								'admin_commission'		=> $admin_commission,
								'admin_commission_val'	=> $admin_commission_val,
								'created_at'			=> $created_at,
								'updated_at'			=> $updated_at,
								'updated_by'			=> $updated_by
							);

		$ref = $this->base_model->insert_operation($inputdata, 'bookings');

		if($ref > 0) {

			//Log Credits transaction data & update user net credits - Start
			$log_data = array(
							'user_id' => $buyer_id,
							'credits' => $fee,
							'per_credit_value' => $per_credit_value,
							'action'  => 'debited',
							'purpose' => 'Slot booked with the Seller "'.$seller_slug.'" and Booking Id is '.$ref,
							'date_of_action	' => date('Y-m-d H:i:s'),
							'reference_table' => 'bookings',
							'reference_id' => $ref,
						);

			log_user_credits_transaction($log_data);

			update_user_credits($buyer_id, $fee, 'debit');
			//Log Credits transaction data & update user net credits - End


			//Email Alert to Seller - Start
				//Get Seller Booking Success Email Template
				$email_tpl = $this->base_model->fetch_records_from('email_templates', array('template_status' => 'Active', 'email_template_id' => '5'));

				if(!empty($email_tpl)) {

					$email_tpl = $email_tpl[0];

					$buyer_rec = getUserRec($buyer_id);
					$seller_rec 	 = getUserRec($seller_id);


					if(!empty($email_tpl->from_email)) {

						$from = $email_tpl->from_email;

					} else {

						$from 	= get_system_settings('Portal_Email');
					}

					$to 	= $seller_rec->email;

					if(!empty($email_tpl->template_subject)) {

						$sub = $email_tpl->template_subject;

					} else {

						$sub = get_languageword("Booking Request From Buyer");
					}

					if(!empty($email_tpl->template_content)) {

						$logo_img='<img src="'.get_site_logo().'" class="img-responsive" width="120px" height="50px">';

						$site_title = $this->config->item('site_settings')->site_title;


						$original_vars  = array($logo_img, $site_title, $seller_rec->username, $buyer_rec->username, $book_slug, $start_date." & ".$time_slot, $preferred_location, '<a href="'.URL_AUTH_LOGIN.'">'.get_languageword('Login Here').'</a>', $site_title);


						$temp_vars		= array('__SITE_LOGO__', '__SITE_TITLE__', '__SELLER_NAME__', '__BUYER_NAME__', '__BOOK_NAME__', '__DATE_TIME__', '__LOCATION__', '__LOGIN_LINK__', '__SITE_TITLE__');
						$msg = str_replace($temp_vars, $original_vars, $email_tpl->template_content);

					} else {

						$msg = get_languageword('please')." <a href='".URL_AUTH_LOGIN."'> ".get_languageword('Login Here')."</a> ".get_languageword('to view the booking details');
						$msg .= "<p>".get_languageword('Thank you')."</p>";
					}

					sendEmail($from, $to, $sub, $msg);
				}
			//Email Alert to Seller - End
			


			//05-12-2018 admin notification start
			$data = array();
			$data['user_id'] 	= $buyer_id;
			$data['title'] 		= get_languageword('new_book_booking');
			$data['content'] 	= "Buyer sent Booking Request to Seller";
			$data['datetime']   = date('Y-m-d H:i:s');
			$data['admin_read'] = 0;
			$data['page_link']  = SITEURL.'admin/buyer-bookings/read/'.$ref;
			$data['table_name'] = "bookings";
			$data['primary_key_column'] = "booking_id";
			$data['primary_key_value']  = $ref;

			$this->base_model->insert_operation($data,'notifications');	
			unset($data);
			//05-12-2018 admin notification start
			

			$this->prepare_flashmessage(get_languageword('your_slot_with_the_seller_booked_successfully_Once_seller_approved_your_booking and_initiated_the_session_you_can_start_the_book_on_the_booked_date'), 0);
			redirect(URL_HOME_SELLER_PROFILE.'/'.$seller_slug);

		} else {

			$this->prepare_flashmessage(get_languageword('your_slot_with_the_seller_not_booked_you_can_send_message_to_the_seller'), 2);
			redirect(URL_HOME_SELLER_PROFILE.'/'.$seller_slug);
		}


	}


	/**
	 * [enroll_in_institute description]
	 * @return [type] [description]
	 */
	function enroll_in_institute()
	{
		if (!$this->ion_auth->logged_in()) {
			$this->prepare_flashmessage(get_languageword('please_login_to_enroll_in_institute'), 2);
			redirect('auth/login', 'refresh');
		}

		if(!$this->ion_auth->is_buyer()) {
			$this->prepare_flashmessage(get_languageword('You dont have permission to access this page'), 1);
			redirect('auth', 'refresh');
		}


		if(!$this->input->post()) {

			$this->prepare_flashmessage(get_languageword('invalid_request'), 1);
			redirect(URL_HOME_SEARCH_INSTITUTE, 'refresh');
		}


		$buyer_id 		= $this->ion_auth->get_user_id();
		$inst_id	   		= $this->input->post('inst_id');
		$inst_slug   		= $this->input->post('inst_slug');
		$batch_id			= $this->input->post('batch_id');


		//Check Whether buyer is premium user or not
		if(!is_premium($buyer_id)) {

			$this->prepare_flashmessage(get_languageword('please_become_premium_member_to_enroll_in_institute'), 2);
			redirect(URL_BUYER_LIST_PACKAGES, 'refresh');
		}

		$batch_details = $this->home_model->get_inst_batch_details($batch_id);

		//Check whether Institute offering the book-batch or not
		if(empty($batch_details)) {

			$this->prepare_flashmessage(get_languageword('no_batch_details_found'), 2);
			redirect(URL_HOME_INSTITUTE_PROFILE.'/'.$inst_slug, 'refresh');
		}

		$fee 					= $batch_details['fee'];
		$batch_max_strength 	= $batch_details['batch_max_strength'];

		//Check If buyer has sufficient credits to book seller
		if(!is_eligible_to_make_booking($buyer_id, $fee)) {

			$this->prepare_flashmessage(get_languageword("you_do_not_have_enough_credits_to_enroll_in_the_institute_Please_get_required_credits_here"), 2);
			redirect(URL_BUYER_LIST_PACKAGES, 'refresh');
		}


		//Check If buyer already booked the seller on the same slot and it is not yet approved by seller
		if($this->home_model->is_already_enrolled_in_the_batch($buyer_id, $batch_id)) {

			$this->prepare_flashmessage(get_languageword('you_already_enrolled_in_this_batch_and_your_book_not_yet_completed'), 2);
			redirect(URL_HOME_INSTITUTE_PROFILE.'/'.$inst_slug, 'refresh');
		}

		//Check If slot is available in the selected batch
		if(!($batch_max_strength > $this->home_model->total_enrolled_buyers_in_batch($batch_id))) {

			$this->prepare_flashmessage(get_languageword('slot_not_available_in_the_batch_Please_select_other_batch'), 2);
			redirect(URL_HOME_INSTITUTE_PROFILE.'/'.$inst_slug, 'refresh');
		}


		$inputdata = $batch_details;


		$message   				= $this->input->post('message');

		$admin_commission   	= get_system_settings('admin_commission_for_a_booking');
		$admin_commission_val   = round($fee * ($admin_commission / 100));

		$created_at   		= date('Y-m-d H:i:s');
		$updated_at   		= $created_at;
		$updated_by   		= $buyer_id;


		unset($inputdata['batch_max_strength']);
		unset($inputdata['status']);
		unset($inputdata['sort_order']);
		unset($inputdata['created_at']);
		unset($inputdata['updated_at']);

		$inputdata['buyer_id']			=	$buyer_id;
		$inputdata['message']				=	$message;
		$inputdata['admin_commission']		=	$admin_commission;
		$inputdata['admin_commission_val']	=	$admin_commission_val;
		$inputdata['created_at']			=	$created_at;
		$inputdata['updated_at']			=	$updated_at;
		$inputdata['updated_by']			=	$updated_by;

		$ref = $this->base_model->insert_operation($inputdata, 'inst_enrolled_buyers');

		if($ref > 0) {

			$book_name = $this->base_model->fetch_value('categories', 'name', array('id' => $inputdata['book_id']));

			//Log Credits transaction data & update user net credits - Start
			$log_data = array(
							'user_id' => $buyer_id,
							'credits' => $fee,
							'per_credit_value' => $inputdata['per_credit_value'],
							'action'  => 'debited',
							'purpose' => 'Enrolled in the batch "'.$inputdata['batch_name'].' - '.$inputdata['batch_code'].'" for the book "'.$book_name.'" offered by the isntitute "'.$inst_slug.'"',
							'date_of_action	' => date('Y-m-d H:i:s'),
							'reference_table' => 'inst_enrolled_buyers',
							'reference_id' => $ref,
						);

			log_user_credits_transaction($log_data);

			update_user_credits($buyer_id, $fee, 'debit');
			//Log Credits transaction data & update user net credits - End


			//admin notification
			$not_data = array();
			$not_data['user_id'] 	= $buyer_id;
			$not_data['title'] 		= get_languageword('buyer_enrolled_in_batch').' '.$batch_details['batch_name'];
			$not_data['content'] 	= "Buyer has enrolled in batch ".' '.$batch_details['batch_name'];
			$not_data['datetime']   = date('Y-m-d H:i:s');
			$not_data['admin_read'] = 0;
			$not_data['page_link']  = SITEURL.'admin/inst-batche-enrolled-buyers/'.$batch_details['batch_id'].'/read/'.$ref;
			$not_data['table_name'] = "inst_enrolled_buyers";
			$not_data['primary_key_column'] = "enroll_id";
			$not_data['primary_key_value']  = $ref;

			$this->base_model->insert_operation($not_data,'notifications');	
			unset($not_data);



			//Email Alert to Institute & Batch Seller - Start
				//Get Institute Enroll Success Email Template
				$email_tpl = $this->base_model->fetch_records_from('email_templates', array('template_status' => 'Active', 'email_template_id' => '8'));

				if(!empty($email_tpl)) {

					$email_tpl = $email_tpl[0];

					$buyer_rec = getUserRec($buyer_id);
					$seller_rec 	 = getUserRec($inputdata['seller_id']);
					$inst_rec 	 = getUserRec($inputdata['inst_id']);


					if(!empty($email_tpl->from_email)) {

						$from = $email_tpl->from_email;

					} else {

						$from 	= get_system_settings('Portal_Email');
					}

					$to 	= $inst_rec->email.",".$seller_rec->email;

					if(!empty($email_tpl->template_subject)) {

						$sub = $email_tpl->template_subject;

					} else {

						$sub = get_languageword("Enrollment Request From Buyer");
					}

					if(!empty($email_tpl->template_content)) {

						$logo_img='<img src="'.get_site_logo().'" class="img-responsive" width="120px" height="50px">';

						$site_title = $this->config->item('site_settings')->site_title;


						$original_vars  = array($logo_img, $site_title, $buyer_rec->username, $inputdata['batch_name'].' - '.$inputdata['batch_code'], $book_name, $inst_slug, '<a href="'.URL_AUTH_LOGIN.'">'.get_languageword('Login Here').'</a>', $site_title);


						$temp_vars		= array('__SITE_LOGO__', '__SITE_TITLE__', '__BUYER_NAME__', '__BATCH_NAME__', '__BOOK_NAME__', '__INSTITUTE_NAME__', '__LOGIN_LINK__', '__SITE_TITLE__');

						$msg = str_replace($temp_vars, $original_vars, $email_tpl->template_content);

					} else {

						$msg = get_languageword('please')." <a href='".URL_AUTH_LOGIN."'>".get_languageword('Login Here')."</a> ".get_languageword('to view the enrollment details');
						$msg .= "<p>".get_languageword('Thank you')."</p>";
					}

					sendEmail($from, $to, $sub, $msg);
				}
			//Email Alert to Institute & Batch Seller - End

			$this->prepare_flashmessage(get_languageword('your_slot_booked_successfully_Once_isntitute_approved_your_booking and_seller_initiated_the_session_you_can_start_the_book_on_book_starting_date'), 0);
			redirect(URL_HOME_INSTITUTE_PROFILE.'/'.$inst_slug);

		} else {

			$this->prepare_flashmessage(get_languageword('your_slot_with_the_seller_not_booked_You_can_send_message_to_the_seller'), 2);
			redirect(URL_HOME_INSTITUTE_PROFILE.'/'.$inst_slug);
		}


	}


	function _callback_book_duration($primary_key, $row)
	{
		return $row->duration_value.' '. $row->duration_type;
	}

	function _callback_book_id($primary_key , $row)
	{

	  $book_name = $this->base_model->fetch_value('categories', 'name', array('id' => $row->book_id));
	   return $book_name;
	}

	/**
	 * [enquiries description]
	 * @param  string $param [description]
	 * @return [type]        [description]
	 */
	function enquiries($param = "")
	{
		if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_buyer()) {
			$this->prepare_flashmessage(get_languageword('You dont have permission to access this page'), 1);
			redirect('auth/login', 'refresh');
		}

		$this->data['message'] = $this->session->flashdata('message');

		$user_id = $this->ion_auth->get_user_id();
		$this->load->library(array('grocery_CRUD'));
		$crud = new grocery_CRUD();
		$crud_state = $crud->getState();
		$crud->set_table(TBL_BOOKINGS);
		$crud->set_relation('seller_id',TBL_USERS, 'username');
		$crud->set_relation('updated_by',TBL_USERS, 'username');
		$crud->where(TBL_BOOKINGS.'.buyer_id', $user_id);


		$status_arr = array('pending', 'approved', 'cancelled_before_book_started', 'cancelled_when_book_running', 'cancelled_after_book_completed', 'session_initiated', 'running', 'completed', 'called_for_admin_intervention', 'closed');
		if(in_array($param, $status_arr)) {

			$crud->where(TBL_BOOKINGS.'.status', $param);
		}


		$crud->set_subject( get_languageword('booking_status') );

		//Unset Actions
		$crud->unset_add();
		$crud->unset_delete();

		//List Table Columns
		$crud->columns('book_id', 'seller_id', 'book_duration', 'fee','content', 'start_date', 'time_slot', 'preferred_location', 'status');

		/*******05-12-2018*************/
		if( $param == 'session_initiated' || $param == 'running' ) {
			$crud->add_action(get_languageword('join'), '', '', 'fa fa-mixcloud', array($this, 'join_link') );
		}
		/*******05-12-2018*************/


		$crud->callback_column('book_duration',array($this,'_callback_book_duration'));
		$crud->callback_column('book_id',array($this,'_callback_book_id'));

		//Display Alias Names
		$crud->display_as('book_id',get_languageword('book_booked'));
		$crud->display_as('seller_id',get_languageword('seller_name'));
		$crud->display_as('fee',get_languageword('fee').' ('.get_languageword('in_credits').')');
		$crud->display_as('admin_commission',get_languageword('admin_commission_percentage_in_credits'));
		$crud->display_as('per_credit_value',get_languageword('per_credit_value')." (".get_system_settings('currency_symbol').")");
		$crud->display_as('start_date',get_languageword('preferred_commence_date'));


		if($param == "closed") {

			$crud->add_action(get_languageword('rate_this_seller'), URL_FRONT_IMAGES.'/star.png', URL_BUYER_RATE_SELLER.'/');

			$crud->add_action(get_languageword('get_certificate'), URL_FRONT_IMAGES.'print.png', '', 'stu-certificate', array($this,'callback_buyer_certificate'));


		}

		//Form fields for Edit Record
		$crud->edit_fields('status', 'status_desc', 'updated_at', 'prev_status');

		//Hidden Fields
		$crud->field_type('updated_at', 'hidden', date('Y-m-d H:i:s'));

		//Unset Fields
		$crud->unset_fields('buyer_id', 'admin_commission_val');


		//Authenticate whether Seller editing/viewing his records only
		if($crud_state == "edit" || $crud_state == "read") {

			if($param != "" && $param != "add" && $param != "edit" && $param != "read" && $param != "success")
				$p_key = $this->uri->segment(4);
			else
				$p_key = $this->uri->segment(3);

			$booking_det = $this->base_model->fetch_records_from('bookings', array('booking_id' => $p_key));

			if(!empty($booking_det)) {

				$booking_det = $booking_det[0];

				if($booking_det->buyer_id != $user_id) {

					$this->prepare_flashmessage(get_languageword('not_authorized'), 1);
	    			redirect(URL_BUYER_ENQUIRIES);
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

						$crud->field_type('status', 'dropdown', array('cancelled_before_book_started' => get_languageword('cancel'), 'called_for_admin_intervention' => get_languageword('claim_for_admin_intervention')));
					}

					if($booking_status == "approved") {

						$status = array('cancelled_before_book_started' => get_languageword('cancel'), 'called_for_admin_intervention' => get_languageword('claim_for_admin_intervention'));

						$crud->field_type('status', 'dropdown', $status);

					}

					if($booking_status == "session_initiated") {

						$status = array('running' => get_languageword('start_book'), 'cancelled_before_book_started' => get_languageword('cancel'), 'called_for_admin_intervention' => get_languageword('claim_for_admin_intervention'));
						$crud->field_type('status', 'dropdown', $status);
					}

					if($booking_status == "running") {

						$status = array('cancelled_when_book_running' => get_languageword('cancel'), 'called_for_admin_intervention' => get_languageword('claim_for_admin_intervention'));

						$crud->field_type('status', 'dropdown', $status);

					}

					if($booking_status == "completed") {

						$status = array('closed' => get_languageword('close'), 'called_for_admin_intervention' => get_languageword('claim_for_admin_intervention'));

						$crud->field_type('status', 'dropdown', $status);

					}

					if($booking_status == "called_for_admin_intervention" && $updated_by == "seller") {

						$crud->required_fields(array('status'));

						$status = array('closed' => get_languageword('close'));

						$crud->field_type('status', 'dropdown', $status);

					} else if($booking_status == "called_for_admin_intervention" && ($updated_by == "buyer" || $updated_by == "admin")) {

						$crud->edit_fields('status_desc', 'updated_at');
					}


					if($booking_status == "cancelled_when_book_running" && $updated_by == "seller") {

						$crud->required_fields(array('status'));

						$status = array('called_for_admin_intervention' => get_languageword('claim_for_admin_intervention'));

						$crud->field_type('status', 'dropdown', $status);
					}

					if($booking_status == "closed" || $booking_status == "cancelled_before_book_started" || ($booking_status == "cancelled_when_book_running" && $updated_by == "buyer") || $booking_status == "cancelled_after_book_completed") {

						$crud->edit_fields('status_desc', 'updated_at');

					}

				}

			} else {

				$this->prepare_flashmessage(get_languageword('invalid_request'), 1);
	    		redirect(URL_BUYER_ENQUIRIES);
			}

		}


		if($crud_state == "read") {

			$crud->field_type('updated_at', 'visibile');
			$crud->set_relation('book_id', 'categories', 'name');
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
				$data['title'] 		= get_languageword('buyer_changed_book_status').' to '. $stus;
				$data['content'] 	= "Buyer has changed book status to "." ".$stus;
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

	function callback_buyer_certificate($primary_key , $row)
	{
		if ($row->is_certificate_issued=="Yes") {
			return URL_BUYER_GET_CERTIFICATE.'/'.$row->booking_id;
		} 
		return '#';
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
			'WHERE (b.status = "session_initiated" OR b.status="running") AND "'.$now.'" BETWEEN start_date AND end_date AND b.buyer_id = ' . $user_id .' AND booking_id = ' . $primary_key;
		$booking_details = $this->db->query( $query )->result();
		// echo $query;
		$link = '#';
		if( count( $booking_details ) > 0 && $booking_details[0]->preferred_location == 'online-bbb' ) {
			$link = URL_VIRTUAL_CLASS . '/init/'.$primary_key;
		}
		return $link;
	}
	

	function callback_column_booking_status($primary_key , $row)
	{

	    return humanize($row->status);
	}

	function callback_column_preferred_location($primary_key , $row)
	{

	    return humanize($row->preferred_location);
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

				//If Buyer Cancelled booking before session gets started, refund Buyer's Credits
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


				//If Buyer Cancelled booking before session gets started, refund Buyer's Credits
				if($post_array['status'] == "closed") {

					$seller_acquired_credits = $booking_det->fee - $booking_det->admin_commission_val;

					//Log Credits transaction data & update user net credits - Start
					$log_data = array(
									'user_id' => $booking_det->seller_id,
									'credits' => $seller_acquired_credits,
									'per_credit_value' => $booking_det->per_credit_value,
									'action'  => 'credited',
									'purpose' => 'Credits added for the booking "'.$primary_key.'" ',
									'date_of_action	' => date('Y-m-d H:i:s'),
									'reference_table' => 'bookings',
									'reference_id' => $primary_key,
								);

					log_user_credits_transaction($log_data);

					update_user_credits($booking_det->seller_id, $seller_acquired_credits, 'credit');
					//Log Credits transaction data & update user net credits - End
				}


				//If Buyer updates the status as "start book", and if preferred teaching type is online, Email buyer's skype id to Seller
				//Email Alert to Seller - Start
				//Get Send Buyer's Skype Email Template
				if($post_array['status'] == "running" && $booking_det->preferred_location == "online") {

					$email_tpl = $this->base_model->fetch_records_from('email_templates', array('template_status' => 'Active', 'email_template_id' => '7'));

					if(!empty($email_tpl)) {

						$email_tpl = $email_tpl[0];

						if(!empty($email_tpl->from_email)) {

							$from = $email_tpl->from_email;

						} else {

							$from 	= $buyer_rec->email;
						}

						$to 	= $seller_rec->email;

						if(!empty($email_tpl->template_subject)) {

							$sub = $email_tpl->template_subject;

						} else {

							$sub = get_languageword("Buyer Skype Id");
						}

						$buyer_addr = $buyer_rec->skype.", <br/>Phone: ".$buyer_rec->phone;

						$book_name = $this->base_model->fetch_value('categories', 'name', array('id' => $booking_det->book_id));

						if(!empty($email_tpl->template_content)) {


							$logo_img='<img src="'.get_site_logo().'" class="img-responsive" width="120px" height="50px">';

							$site_title = $this->config->item('site_settings')->site_title;



							$original_vars  = array($logo_img, $site_title, $seller_rec->username, $buyer_rec->username, $book_name, $booking_det->start_date." & ".$booking_det->time_slot, $booking_det->preferred_location, $buyer_addr, $site_title);


							$temp_vars		= array('__SITE_LOGO__', '__SITE_TITLE__', '__SELLER__NAME__', '__BUYER_NAME__', '__BOOK_NAME__', '__DATE_TIME__', '__LOCATION__', '__BUYER_ADDRESS__', '__SITE_TITLE__');

							$msg = str_replace($temp_vars, $original_vars, $email_tpl->template_content);

						} else {

							$msg = "<p>
										".get_languageword('hello')." ".$seller_rec->username.",</p>
									<p>
										".get_languageword('Buyer ')." &quot;".$buyer_rec->username."&quot; ".get_languageword('started the book')."  &quot;".$book_name."&quot;</p>
									<p>
										".get_languageword('for the timeslot')." &quot;".$booking_det->start_date." & ".$booking_det->time_slot."&quot; and &quot; ".$booking_det->preferred_location."&quot; ".get_languageword('as preferred location for sessions')."</p>
									<p>
										".get_languageword('Below is the Skype id of the Buyer')."</p>
									<p>
										".$buyer_addr."</p>";

							$msg .= "<p>".get_languageword('Thank you')."</p>";
						}

						sendEmail($from, $to, $sub, $msg);
					}
					//Email Alert to Seller - End
				}


				//If Buyer updates the status as "called_for_admin_intervention", Send email alert to Admin
				//Email Alert to Admin - Start
				//Get Claim By Buyer Email Template
				if($post_array['status'] == "called_for_admin_intervention") {

					$email_tpl = $this->base_model->fetch_records_from('email_templates', array('template_status' => 'Active', 'email_template_id' => '9'));

					if(!empty($email_tpl)) {

						$email_tpl = $email_tpl[0];

						if(!empty($email_tpl->from_email)) {

							$from = $email_tpl->from_email;

						} else {

							$from 	= $buyer_rec->email;
						}

						$to 	= get_system_settings('Portal_Email');

						if(!empty($email_tpl->template_subject)) {

							$sub = $email_tpl->template_subject;

						} else {

							$sub = get_languageword("Claim By Buyer");
						}


						if(!empty($email_tpl->template_content)) {

							$logo_img='<img src="'.get_site_logo().'" class="img-responsive" width="120px" height="50px">';

							$site_title = $this->config->item('site_settings')->site_title;


							$original_vars  = array($logo_img, $site_title, $buyer_rec->username, get_languageword('booking').' "'.$booking_det->booking_id.'"', '<a href="'.URL_AUTH_LOGIN.'">'.get_languageword('Login Here').'</a>', $site_title);

							$temp_vars		= array('__SITE_LOGO__', '__SITE_TITLE__', '__BUYER_NAME__', '__BOOKING_ID__', '__LOGIN_LINK__', '__SITE_TITLE__');

							$msg = str_replace($temp_vars, $original_vars, $email_tpl->template_content);

						} else {

							$msg = "<p>
										".get_languageword('Hi').",</p>
									<p>
										".get_languageword('Buyer ')." &quot;".$buyer_rec->username."&quot; ".get_languageword('claimed for your intervention for the booking')." &quot;".$booking_det->booking_id."&quot;</p>
									<p>
										".get_languageword('please')." <a href='".URL_AUTH_LOGIN."'>".get_languageword('Login Here')."</a> ".get_languageword('to view the details').".</p>";

							$msg .= "<p>".get_languageword('Thank you')."</p>";
						}

						sendEmail($from, $to, $sub, $msg);
					}
					//Email Alert to Admin - End
				}

			}

			return TRUE;

		} else return FALSE;
	}


	//Give Rating to Seller
	function rate_seller($booking_id = "")
	{
		if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_buyer()) {
			$this->prepare_flashmessage(get_languageword('You dont have permission to access this page'), 1);
			redirect('auth/login', 'refresh');
		}

		$user_id = $this->ion_auth->get_user_id();
		$booking_id = ($this->input->post('booking_id')) ? $this->input->post('booking_id') : $booking_id;

		if(empty($booking_id)) {

			$this->prepare_flashmessage(get_languageword('Invalid Request'), 1);
			redirect(URL_BUYER_ENQUIRIES);
		}

		$booking_det = $this->base_model->fetch_records_from('bookings', array('booking_id' => $booking_id, 'status' => 'closed'));

		if(empty($booking_det)) {

			$this->prepare_flashmessage(get_languageword('No Booking Deatils Found'), 2);
			redirect(URL_BUYER_ENQUIRIES);
		}

		$booking_det = $booking_det[0];

		//Check whether related buyer is rating the seller
		if($booking_det->buyer_id != $user_id) {

			$this->prepare_flashmessage(get_languageword('Not Authorized'), 1);
			redirect(URL_BUYER_INDEX);
		}

		if($this->input->post()) {

			$inputdata['booking_id']	= $booking_id;
			$inputdata['buyer_id']	= $user_id;
			$inputdata['seller_id']		= $booking_det->seller_id;
			$inputdata['book_id']		= $booking_det->book_id;
			$inputdata['rating']		= $this->input->post('score');
			$inputdata['comments']		= $this->input->post('comments');


			if($this->input->post('review_id')) {

				$inputdata['updated_at']	= date('Y-m-d H:i:s');

				if($this->base_model->update_operation($inputdata, 'seller_reviews', array('id' => $this->input->post('review_id')))) {


					
					$this->prepare_flashmessage(get_languageword('Thanks for rating the seller Your review successfully updated to the Seller'), 0);

				} else {

					$this->prepare_flashmessage(get_languageword('Review not updated due to some technical issue Please retry again Thank you'), 2);
				}

			} else {

				$inputdata['created_at']	= date('Y-m-d H:i:s');
				$inputdata['updated_at']	= $inputdata['created_at'];

				//05-12-2018 admin notification 
				$review_id = $this->base_model->insert_operation_id($inputdata, 'seller_reviews');

				if ($review_id) {

					$this->prepare_flashmessage(get_languageword('Thanks for rating the seller Your review successfully sent to the Seller'), 0);

				} else {

					$this->prepare_flashmessage(get_languageword('Review not sent due to some technical issue Please retry again Thank you'), 2);
				}
			}

			redirect(URL_BUYER_ENQUIRIES.'/closed');

		}

		$review_det = $this->base_model->fetch_records_from('seller_reviews', array('booking_id' => $booking_id));

		$this->data['review_det'] 	= (!empty($review_det)) ? $review_det[0] : array();
		$this->data['booking_id'] 	= $booking_id;
		$this->data['pagetitle'] 	= get_languageword('Rate Seller')." (".getUserRec($booking_det->seller_id)->username.")";
		$this->data['activemenu'] 	= "enquiries";
		$this->data['content'] 		= 'rate_seller';
		$this->_render_page('template/site/buyer-template', $this->data);

	}



	/**
	 * [enrolled_books description]
	 * @return [type] [description]
	 */
	function enrolled_books()
	{
		if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_buyer()) {
			$this->prepare_flashmessage(get_languageword('You dont have permission to access this page'), 1);
			redirect('auth/login', 'refresh');
		}

		$this->data['message'] = $this->session->flashdata('message');

		$user_id = $this->ion_auth->get_user_id();
		$this->load->library(array('grocery_CRUD'));
		$crud = new grocery_CRUD();
		$crud_state = $crud->getState();
		$crud->set_table($this->db->dbprefix('inst_enrolled_buyers'));
		$crud->where('buyer_id', $user_id);
		$crud->set_relation('book_id','categories','name');
		$crud->set_relation('inst_id','users','username');
		$crud->set_relation('seller_id','users','username');
		$crud->set_subject( get_languageword('buyer_enrolled_books') );

		$crud->columns('book_id','inst_id','batch_code','batch_name', 'time_slot','book_offering_location','duration_type', 'duration_value','batch_start_date', 'status');

		$crud->unset_add();
		$crud->unset_delete();

		$crud->edit_fields('status', 'status_desc', 'updated_at', 'prev_status', 'updated_by');

		//Hidden Fields
		$crud->field_type('updated_at', 'hidden', date('Y-m-d H:i:s'));
		$crud->field_type('updated_by', 'hidden', $user_id);

		$crud->display_as('book_id',get_languageword('book_Name'));
		$crud->display_as('inst_id',get_languageword('institute_Name'));
		$crud->display_as('seller_id',get_languageword('Seller Name'));

		if($crud_state == "edit") {

			$p_key = $this->uri->segment(4);

			$enroll_det = $this->base_model->fetch_records_from('inst_enrolled_buyers', array('enroll_id' => $p_key));

			if(!empty($enroll_det)) {

				$enroll_det = $enroll_det[0];

				if($enroll_det->buyer_id != $user_id) {

					$this->prepare_flashmessage(get_languageword('not_authorized'), 1);
	    			redirect(URL_BUYER_ENROLLED_BOOKS);
				}

				$booking_status = $enroll_det->status;

				$crud->field_type('prev_status', 'hidden', $booking_status);

				$crud->display_as('status', get_languageword('change_status'));

				if($booking_status == "pending")
					$crud->field_type('status', 'dropdown', array('cancelled_before_book_started' => get_languageword('cancel')));
				else if($booking_status == "closed" || $booking_status == "approved")
					$crud->field_type('status', 'dropdown', array('called_for_admin_intervention' => get_languageword('claim_for_admin_intervention')));
				else {
					$crud->field_type('prev_status', 'hidden', $enroll_det->prev_status);
					$crud->field_type('status', 'hidden', $booking_status);
				}

			} else {

				$this->prepare_flashmessage(get_languageword('invalid_request'), 1);
	    		redirect(URL_BUYER_ENROLLED_BOOKS);
			}
		}

		if($crud_state == "read") {
			$crud->field_type('updated_at', 'visible');
		}


		$crud->callback_after_update(array($this, 'callback_after_enroll_status_update'));

		$output = $crud->render();

		$this->data['pagetitle'] = get_languageword('enrolled_books');
		$this->data['grocery_output'] = $output;
		$this->data['activemenu'] = 'enrolled_books';
		$this->data['grocery'] = TRUE;
		$this->grocery_output($this->data);

	}


	function callback_after_enroll_status_update($post_array,$primary_key)
	{

		$rec_det = $this->base_model->fetch_records_from('inst_enrolled_buyers', array('enroll_id' => $primary_key));

		if(empty($rec_det))
			return FALSE;

		$rec_det = $rec_det[0];

		//If Buyer Cancelled booking before session gets started, refund Buyer's Credits
		if($post_array['status'] == "cancelled_before_book_started") {

			$inst_rec = getUserRec($rec_det->inst_id);

			//Log Credits transaction data & update user net credits - Start
			$log_data = array(
							'user_id' => $rec_det->buyer_id,
							'credits' => $rec_det->fee,
							'per_credit_value' => $rec_det->per_credit_value,
							'action'  => 'credited',
							'purpose' => 'Slot booked with the Institute "'.$inst_rec->username.'" has cancelled by you before book started',
							'date_of_action	' => date('Y-m-d H:i:s'),
							'reference_table' => 'inst_enrolled_buyers',
							'reference_id' => $primary_key,
						);

			log_user_credits_transaction($log_data);

			update_user_credits($rec_det->buyer_id, $rec_det->fee, 'credit');
			//Log Credits transaction data & update user net credits - End
		}


		//If Buyer updates the status as "called_for_admin_intervention", Send email alert to Admin
		//Email Alert to Admin - Start
		//Get Claim By Buyer Email Template
		if($post_array['status'] == "called_for_admin_intervention") {

			$email_tpl = $this->base_model->fetch_records_from('email_templates', array('template_status' => 'Active', 'email_template_id' => '9'));

			if(!empty($email_tpl)) {

				$email_tpl = $email_tpl[0];


				if(!empty($email_tpl->from_email)) {

					$from = $email_tpl->from_email;

				} else {

					$from 	= $this->session->userdata('email');
				}

				$to 	= get_system_settings('Portal_Email');

				if(!empty($email_tpl->template_subject)) {

					$sub = $email_tpl->template_subject;

				} else {

					$sub = get_languageword("Claim By Buyer About Batch");
				}


				if(!empty($email_tpl->template_content)) {

					$original_vars  = array($this->session->userdata('username'), get_languageword('batch').' "'.$rec_det->batch_id.'"', '<a href="'.URL_AUTH_LOGIN.'">'.get_languageword('Login Here').'</a>');
					$temp_vars		= array('___BUYER_NAME___', '___BOOKING_ID___', '___LOGINLINK___');
					$msg = str_replace($temp_vars, $original_vars, $email_tpl->template_content);

				} else {

					$msg = "<p>
								".get_languageword('Hi').",</p>
							<p>
								".get_languageword('Buyer ')." &quot;".$this->session->userdata('username')."&quot; ".get_languageword('claimed for your intervention for the batch')." &quot;".$rec_det->batch_id."&quot;</p>
							<p>
								".get_languageword('please')." <a href='".URL_AUTH_LOGIN."'>".get_languageword('Login Here')."</a> ".get_languageword('to view the details')."</p>";

					$msg .= "<p>".get_languageword('Thank you')."</p>";
				}

				sendEmail($from, $to, $sub, $msg);
			}
			//Email Alert to Admin - End
		}
 
		return true;
	}


	/**
	 * [credits_transactions_history description]
	 * @return [type] [description]
	 */
	function credits_transactions_history()
	{
		if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_buyer()) {
			$this->prepare_flashmessage(get_languageword('You dont have permission to access this page'), 1);
			redirect('auth/login', 'refresh');
		}

		$buyer_id = $this->ion_auth->get_user_id();
		$this->load->library(array('grocery_CRUD'));
		$crud = new grocery_CRUD();
		$crud_state = $crud->getState();
		$crud->set_table($this->db->dbprefix('user_credit_transactions'));
		$crud->where('user_id', $buyer_id);
		$crud->order_by('id','desc');
		
		$crud->set_subject( get_languageword('user_credit_transactions') );

		$crud->unset_add();
		$crud->unset_delete();
		$crud->unset_edit();

		$crud->columns('id','trans_id','credits','action','purpose','date_of_action');

		$crud->unset_read_fields('user_id', 'reference_table', 'reference_id', 'per_credit_value');

		$output = $crud->render();

		$this->data['activemenu'] 	= "user_credit_transactions";
		$this->data['pagetitle'] = get_languageword('credits_transactions_history');
		$this->data['grocery_output'] = $output;
		$this->data['grocery'] = TRUE;
		$this->grocery_output($this->data);
	}	
		
	/**
	 * [manual_payment_status description]
	 * @param  [type] $payment_id [description]
	 * @return [type]             [description]
	 */
	function manual_payment_status( $payment_id ) {
		$this->data['message'] = $this->session->flashdata('message');
		if ( ! empty( $payment_id ) ) {
			$check = $this->db->query( 'SELECT * FROM `'.$this->db->dbprefix('subscriptions').'` s INNER JOIN `'.$this->db->dbprefix('users').'` u ON s.user_id = u.id AND u.active = 1 AND s.payment_received = 0 WHERE s.id = ' . $payment_id .' AND u.id = ' . $this->ion_auth->get_user_id())->result();
			if ( empty( $check ) ) {
				safe_redirect( $this->ion_auth->get_user_id() );
			} else {
				if(isset($_POST['submitbutt']))
				{
					$this->form_validation->set_rules('payment_updated_user_message', get_languageword('Enter you comments'), 'trim|required|xss_clean');
					$this->form_validation->set_error_delimiters('<div class="error">', '</div>');			
					if ($this->form_validation->run() == TRUE)
					{
						$inputdata = array();						
						$inputdata['payment_updated_user'] = 'yes';
						$inputdata['payment_updated_user_date'] = date('Y-m-d H:i:s');
						$inputdata['payment_updated_user_message'] = $this->input->post('payment_updated_user_message');
						$this->base_model->update_operation($inputdata, 'subscriptions', array('id' => $payment_id));
						$this->prepare_flashmessage(get_languageword('record updated successfully'), 0);
						redirect('buyer/mysubscriptions');	
					}
				}
				$this->data['activemenu'] 	 = "packages";
				$this->data['activesubmenu'] = "mysubscriptions";
				$this->data['content'] 		 = 'manual_payment_status';
				$this->data['pagetitle'] 	 = get_languageword('manual_payment_status');
				$this->data['profile'] = $check[0];
				$this->_render_page('template/site/buyer-template', $this->data);
			}
		} else {
			$this->safe_redirect( site_url( 'buyer/mysubscriptions', 'Wrong operation' ) );
		}
	}

	/**
	 * [paywith_razorpay description]
	 * @return [type] [description]
	 */
	function paywith_razorpay() {
		$params = $this->uri->uri_to_assoc();
		print_r($params);
		if ( ! isset( $params['package'] ) || ! isset( $params['gateway'] ) ) {
			$this->safe_redirect();
		}
		$package_id = $params['package'];
		$gateway_id = $params['gateway'];
		
		$package_info 	= $this->db->get_where('packages',array('id' => $package_id))->result();
		
		$gateway_details = $this->base_model->get_payment_gateways(' AND st2.type_id = '.$gateway_id);
		
		if ( empty( $package_info ) || empty( $gateway_details ) ) {
			$this->safe_redirect();
		}
		
		$this->load->model('buyer_model');
		$this->data['package_data'] = $package_info;
		$this->data['payment_gateways'] = $gateway_details;
		$user_info = $this->base_model->get_user_details( $this->ion_auth->get_user_id() );
		$this->data['user_info'] = $user_info[0];
		$user_info = $user_info[0];
		$field_values = $this->db->get_where('system_settings_fields',array('type_id' => $gateway_id))->result();
		$razorpay_key_id = 'rzp_test_tjwMzd8bqhZkMr';
		$razorpay_key_secret = 'EWI9VQiMH43p6LDCbpsgvvHZ';
		$razorpay_payment_action = 'capture';
		$razorpay_mode = 'sandbox';
		
		foreach($field_values as $value) {
			if( $value->field_key == 'razorpay_key_id' ) {
				$razorpay_key_id = $value->field_output_value;
			}
			if( $value->field_key == 'razorpay_key_secret' ) {
				$razorpay_key_secret = $value->field_output_value;
			}
			if( $value->field_key == 'razorpay_payment_action' ) {
				$razorpay_payment_action = $value->field_output_value;
			}
			if( $value->field_key == 'razorpay_mode' ) {
				$razorpay_mode = $value->field_output_value;
			}
		}
		$package_info 	= $this->db->get_where('packages',array('id' => $package_id))->result();
		
		$total_amount 	= $package_info[0]->package_cost;
		if(isset($package_info[0]->discount) && ($package_info[0]->discount != 0))
		{
			if($package_info[0]->discount_type == 'Value')
			{
				$total_amount = $package_info[0]->package_cost - $package_info[0]->discount;				
			}
			else
			{
				$discount = ($package_info[0]->discount/100)*$package_info[0]->package_cost;						
				$total_amount = $package_info[0]->package_cost - $discount;
			}
		}
		$config = array(
			'razorpay_key_id' => $razorpay_key_id,
			'razorpay_key_secret' => $razorpay_key_secret,
			'razorpay_payment_action' => $razorpay_payment_action,
			'razorpay_mode' => $razorpay_mode,
			'total_amount' => $total_amount * 100,
			
			'product_name' => $package_info[0]->package_name,
			
			'firstname' => $user_info->first_name,
			'lastname' => $user_info->last_name,
			'email' => $user_info->email,
			'phone' => $user_info->phone,
			
			'success_url' => base_url() . 'payment/razorpay_success',
			'cancel_url' => base_url() . 'payment/razorpay_failed',
			'failed_url' => base_url() . 'payment/razorpay_success',
		);
		$this->data['razorpay'] = $config;
		
		$this->data['activemenu'] 	= "packages";
		$this->data['activesubmenu'] = "paywith_razorpay";	
		$this->data['content'] 		= 'paywith_razorpay';
		if ( $this->ion_auth->is_buyer() ) {
			$this->_render_page('template/site/buyer-template', $this->data);
		} elseif( $this->ion_auth->is_seller() ) {
			$this->_render_page('template/site/seller-template', $this->data);
		} elseif( $this->ion_auth->is_institute() ) {
			$this->_render_page('template/site/institute-template', $this->data);
		} else {
			$this->_render_page('template/site/admin-template', $this->data);
		}
	}



	/**
	 * [book_purchases description]
	 * @return [type] [description]
	 */
	function book_purchases()
	{
		if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_buyer()) {
			$this->prepare_flashmessage(get_languageword('You dont have permission to access this page'), 1);
			redirect('auth/login', 'refresh');
		}

		$buyer_id = $this->ion_auth->get_user_id();

		$this->load->library(array('grocery_CRUD'));
		$crud = new grocery_CRUD();
		$crud_state = $crud->getState();

		// echo "<pre>";
		// print_r($crud_state);
		// die();

		$crud->set_table(TBL_PREFIX.'book_purchases');
		$crud->set_relation('sc_id',TBL_PREFIX.'seller_selling_books','book_title');
		$crud->set_relation('seller_id',TBL_PREFIX.'users','username');
		$crud->where('user_id', $buyer_id);
		$crud->where('payment_status', 'Completed');

		$crud->set_subject( get_languageword('My_Book_Purchases') );

		$crud->unset_add();
		$crud->unset_delete();
		$crud->unset_edit();
		$crud->unset_read();

		$crud->columns('purchase_id','transaction_id','sc_id','seller_id','total_amount','max_downloads','total_downloads','paid_date');

		$crud->display_as('sc_id', get_languageword('Book_Title'));
		$crud->display_as('seller_id', get_languageword('seller_name'));
		// $crud->display_as('total_amount', get_languageword('total_amount'));
		$crud->display_as('paid_date', get_languageword('Purchased_On'));
		$crud->display_as('purchase_id', get_languageword('no.'));

		$crud->add_action(get_languageword('View_Planted_Tree'), URL_FRONT_IMAGES.'tree-icon.png', '', 'view-icon-grocery view-plant-tree-icon', array($this,'callback_view_planted_tree'));

		$crud->add_action(get_languageword('visit'), URL_FRONT_IMAGES.'view-grocery.png', '', 'view-icon-grocery', array($this,'callback_visit_selling_book'));

		$crud->add_action(get_languageword('View_Download_History'), URL_FRONT_IMAGES.'magnifier-grocery.png', URL_BUYER_BOOK_DOWNLOAD_HISTORY.'/');

		$crud->add_action(get_languageword('Download_Book_Curriculum'), URL_FRONT_IMAGES.'download.png', URL_BUYER_DOWNLOAD_BOOK.'/');

		$purchase_det = $this->base_model->fetch_records_from('book_purchases', array('user_id' => $buyer_id, 'payment_status' => 'Completed'));

		if(!empty($purchase_det)) {
			$sc_id = $purchase_det[0]->sc_id;

			$record = get_seller_sellingbook_info($sc_id);
			$this->data['record'] = $record;

			$book_exam_quiz_data = $this->base_model->fetch_records_from('seller_selling_books_exam_result', array('sc_id' => $sc_id, 'user_id' => $buyer_id));

			//echo '<pre> $book_exam_quiz_data :: '; print_r($book_exam_quiz_data);

			if(!empty($book_exam_quiz_data))
				$crud->add_action(get_languageword('Download_Book_Quiz_Result_Cretificate'), URL_FRONT_IMAGES.'download.png', URL_BUYER_BOOK_DOWNLOAD_EXAM_QUIZ_RESULT_CERTIFICATE.'/');
			else
				$crud->add_action(get_languageword('Show_Book_Quiz'), URL_FRONT_IMAGES.'certificate.png', '', 'show_book_quiz_modal');
		}

		$output = $crud->render();

		$this->data['activemenu'] 		= "my_book_purchases";
		$this->data['pagetitle'] 		= get_languageword('My_Book_Purchases');
		$this->data['grocery_output'] 	= $output;
		$this->data['grocery'] 			= TRUE;
		$this->grocery_output($this->data);

	}

	function callback_view_planted_tree($primary_key, $row)
	{
		$api_val = $row->api_val;

		if((float)$api_val > 0) {
			return $row->moretrees_cert_url;
		}
		return 'javascript:void(0)';
	}

	function callback_visit_selling_book($primary_key, $row)
	{
		$sc_id = $row->sc_id;
		$q = $this->db->select('slug')->from('pre_seller_selling_books')->where('sc_id',$sc_id)->get();
		$result = $q->result();

		foreach ($result as $row ) {
			$slug = $row->slug;
		}

		return SITEURL2.'/buy-book/'.$slug;
	}

	/**
	 * [download_book description]
	 * @param  string $purchase_id [description]
	 * @return [type]              [description]
	 */
	function download_book($purchase_id = '')
	{
		if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_buyer()) {
			$this->prepare_flashmessage(get_languageword('You dont have permission to access this page'), 1);
			redirect('auth/login', 'refresh');
		}

		$buyer_id = $this->ion_auth->get_user_id();

		if(empty($purchase_id)) {
			$this->prepare_flashmessage(get_languageword('invalid_request'), 1);
			redirect(URL_BUYER_BOOK_PURCHASES);
		}

		$purchase_det = $this->base_model->fetch_records_from('book_purchases', array('user_id' => $buyer_id, 'payment_status' => 'Completed', 'purchase_id' => $purchase_id));

		if(empty($purchase_det)) {
			$this->prepare_flashmessage(get_languageword('You_have_not_purchased_the_book'), 1);
			redirect(URL_BUYER_BOOK_PURCHASES);
		} else {

			$purchase_det = $purchase_det[0];
		}

		if(!empty($purchase_det)) {

			if(!($purchase_det->total_downloads < $purchase_det->max_downloads)) {

				$this->prepare_flashmessage(get_languageword('You_have_reached_maximum_limit_of_downloads_Please_purchase_the_book_again_to_download_again_Thank_you'), 2);
				redirect(URL_BUYER_BOOK_PURCHASES);
			}
		}

		$dir = $purchase_id.'_'.$buyer_id;

		//$zip_file_ph = URL_PUBLIC_UPLOADS.'book_curriculum_files/'.$dir.'.zip';

		$this->load->library('user_agent');

		$inputdata = array(
			'sc_id' 		=> $purchase_det->sc_id,
			'purchase_id' 	=> $purchase_id,
			'seller_id' 		=> $purchase_det->seller_id,
			'user_id' 		=> $purchase_det->user_id,
			'ip_address' 	=> $this->input->ip_address(),
			'browser' 		=> $this->agent->browser(),
			'browser_version' => $this->agent->version(),
			'platform' 		=> $this->agent->platform(),
			'mobile_device' => $this->agent->mobile(),
			'robot' 		=> $this->agent->robot(),
			'is_download_success' => 'No', 
			'downloaded_date' => date('Y-m-d H:i:s')
		);

		//$zip = new ZipArchive;

		$file_name = $purchase_id.'_'.$buyer_id.'.zip';
		$response = common_s3_function('get_and_save', 'zip', $file_name);

		//echo '<pre> $response :: '; print_r($response); die;

		if(!empty($response) && $response['status'] == 'success') {
			$inputdata['is_download_success'] = 'Yes';

			if($this->base_model->insert_operation($inputdata, 'book_downloads')) {
		    	$this->load->model('buyer_model');
		    	$this->buyer_model->update_buyer_book_downloads($buyer_id, $purchase_id);
		    	$this->buyer_model->update_seller_book_downloads($purchase_det->seller_id, $purchase_det->sc_id);
			}

			$book_title = $this->base_model->fetch_value('seller_selling_books', 'book_title', array('sc_id' => $purchase_det->sc_id));

			header('Content-Description: File Transfer');
			//this assumes content type is set when uploading the file.
			header('Content-Type: ' . $response['ContentType']);
			header('Content-Disposition: attachment; filename='.$book_title.'.zip');
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');

			//send file to browser for download. 
			echo $response['message'];
		} else {
			$this->prepare_flashmessage(get_languageword('Book_Curriculum_could_not_be_downloaded_due_to_some_technical_issue_Please_download_after_some_time'), 2);
			redirect(URL_BUYER_BOOK_PURCHASES);
		}

		/*if ($zip->open($zip_file_ph) === TRUE) {
		    $inputdata['is_download_success'] = 'Yes';
		    $zip->close();

		    if($this->base_model->insert_operation($inputdata, 'book_downloads')) {

		    	$this->load->model('buyer_model');
		    	$this->buyer_model->update_buyer_book_downloads($buyer_id, $purchase_id);
		    	$this->buyer_model->update_seller_book_downloads($purchase_det->seller_id, $purchase_det->sc_id);
			}

			$book_title = $this->base_model->fetch_value('seller_selling_books', 'book_title', array('sc_id' => $purchase_det->sc_id));

			$this->load->library('zip');
			$this->zip->read_file($zip_file_ph);
			$this->zip->download($book_title);
		} else {
			$this->prepare_flashmessage(get_languageword('Book_Curriculum_could_not_be_downloaded_due_to_some_technical_issue_Please_download_after_some_time'), 2);
			redirect(URL_BUYER_BOOK_PURCHASES);
		}*/
	}

	function book_download_exam_quiz_result_certificate($purchase_id = '')
	{
		/*if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_buyer()) {
			$this->prepare_flashmessage(get_languageword('You dont have permission to access this page'), 1);
			redirect('auth/login', 'refresh');
		}

		$buyer_id = $this->ion_auth->get_user_id();

		if(empty($purchase_id)) {
			$this->prepare_flashmessage(get_languageword('invalid_request'), 1);
			redirect(URL_BUYER_BOOK_PURCHASES);
		}

		$purchase_det = $this->base_model->fetch_records_from('book_purchases', array('user_id' => $buyer_id, 'payment_status' => 'Completed', 'purchase_id' => $purchase_id));

		if(empty($purchase_det)) {
			$this->prepare_flashmessage(get_languageword('You_have_not_purchased_the_book'), 1);
			redirect(URL_BUYER_BOOK_PURCHASES);
		} else {
			$purchase_det = $purchase_det[0];

			$book_curriculum_data = $this->base_model->fetch_records_from('seller_selling_books_curriculum', array('sc_id' => $purchase_det->sc_id));

			$book_exam_quiz_result_data = $this->base_model->fetch_records_from('seller_selling_books_exam_result', array('sc_id' => $purchase_det->sc_id, 'user_id' => $buyer_id));
		}

		if(!empty($purchase_det)) {
			if(!($purchase_det->total_downloads < $purchase_det->max_downloads)) {

				$this->prepare_flashmessage(get_languageword('You_have_reached_maximum_limit_of_downloads_Please_purchase_the_book_again_to_download_again_Thank_you'), 2);

				redirect(URL_BUYER_BOOK_PURCHASES);
			}
		}

		$this->load->library('user_agent');

		$inputdata = array(
			'Is_certificate_issued' => 'Yes', 
			'certificate_issue_date' => date('Y-m-d H:i:s'),
			'updated_at' => date('Y-m-d H:i:s')
		);

		$this->base_model->update_operation($inputdata, 'seller_selling_books_exam_result', array('sc_id' => $purchase_det->sc_id, 'user_id' => $buyer_id));*/

		$user_rec 	 = getUserRec($buyer_id);

		//$book_title = $this->base_model->fetch_value('seller_selling_books', 'book_title', array('sc_id' => $purchase_det->sc_id));

		$book_title = 'Test';

		$this->data['book_title'] = $book_title;
		$this->data['user_data'] = $user_rec;
		/*$this->data['book_exam_quiz_result_data'] = $book_exam_quiz_result_data[0];
		$this->data['book_curriculum_data'] = $book_curriculum_data;*/

		//echo '<pre> $html_data :: '; print_r($html_data);

		$path = base_url('/assets/uploads/images/download.png');
		//$path = base_url(URL_PUBLIC_UPLOADS.'images/download.png');

		//$path = base_url(FCPATH.'assets/uploads/images/download.png');

		$type = pathinfo($path, PATHINFO_EXTENSION);
		$data = file_get_contents($path);
		$base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);

		//$this->data['base64'] = $base64;

		$this->data['image_signature'] = '<img src="'.$path.'" />';
		$this->data['image_signature2'] = '<img src="'.URL_PUBLIC_UPLOADS.'images/NicePng_dan-howell-png_1667011.png" />';

		//echo ; die;

		//echo FCPATH.'assets/uploads/images/download.png'; die;

		$html = $this->load->view('book_quiz_completion_certificate', $this->data, TRUE);

		$this->load->library('pdf');

		$this->pdf->createPDF($html, $book_title, true);

		//Load the library
		/*$this->load->library('html2pdf');

		//Load html view
		$this->html2pdf->html($html);

		//Set folder to save PDF to
	    $this->html2pdf->folder('assets/uploads/');

		//Set the paper defaults
	    $this->html2pdf->paper('letter', 'portrait');

		//Create the PDF
		$this->html2pdf->create();

		//Set the filename to save/download as
		$this->html2pdf->filename($book_title.'.pdf');*/

		/*header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename='.$book_title.'.pdf');
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');*/

		// Load all views as normal
		//$this->load->view('book_quiz_completion_certificate', array('purchase_det' => $purchase_det, 'html_data', $html_data));

		// Get output html
		//$html = $this->output->get_output();

		//echo $html;

		die();
	}

	/**
	 * [book_download_history description]
	 * @param  string $purchase_id [description]
	 * @return [type]              [description]
	 */
	function book_download_history($purchase_id = "")
	{

		if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_buyer()) {
			$this->prepare_flashmessage(get_languageword('You dont have permission to access this page'), 1);
			redirect('auth/login', 'refresh');
		}

		$buyer_id = $this->ion_auth->get_user_id();

		if(empty($purchase_id)) {

			$this->prepare_flashmessage(get_languageword('invalid_request'), 1);
			redirect(URL_BUYER_BOOK_PURCHASES);
		}


		$this->load->library(array('grocery_CRUD'));
		$crud = new grocery_CRUD();
		$crud_state = $crud->getState();
		$crud->set_table(TBL_PREFIX.'book_downloads');
		$crud->set_relation('sc_id',TBL_PREFIX.'seller_selling_books','book_title');
		$crud->set_relation('seller_id',TBL_PREFIX.'users','username');
		$crud->where('user_id', $buyer_id);
		$crud->where('purchase_id', $purchase_id);

		$crud->set_subject( get_languageword('Book_Download_History') );

		$crud->unset_add();
		$crud->unset_delete();
		$crud->unset_edit();
		$crud->unset_read();

		$crud->columns('sc_id','seller_id','ip_address','browser','browser_version', 'platform', 'downloaded_date');

		$crud->display_as('sc_id', get_languageword('Book_Title'));
		$crud->display_as('seller_id', get_languageword('seller_name'));

		$output = $crud->render();

		$this->data['activemenu'] 		= "my_book_purchases";
		$this->data['pagetitle'] 		= get_languageword('Book_Download_History');
		$this->data['grocery_output'] 	= $output;
		$this->data['grocery'] 			= TRUE;
		$this->grocery_output($this->data);

	}
	

	/******************************
	06-12-2018
	*******************************/
	/**
	 * [get_certificate description]
	 * @param  string $booking_id [description]
	 * @return [type]             [description]
	 */
	function get_certificate($booking_id = "")
	{

		if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_buyer()) {
			$this->prepare_flashmessage(get_languageword('You dont have permission to access this page'), 1);
			redirect('auth/login', 'refresh');
		}

		$booking_id = ($this->input->post('booking_id')) ? $this->input->post('booking_id') : $booking_id;

		if(empty($booking_id)) {

			$this->prepare_flashmessage(get_languageword('Please complete your book to get certificate'), 2);
			redirect(URL_BUYER_ENQUIRIES.'/closed');
		}

		$user_id = $this->ion_auth->get_user_id();

		//Check whether booking exists
		$booking_det = $this->base_model->get_query_result("SELECT b.*, c.name AS book_name FROM ".$this->db->dbprefix('bookings')." b INNER JOIN ".$this->db->dbprefix('categories')." c ON c.id=b.book_id WHERE b.booking_id=".$booking_id." AND b.buyer_id=".$user_id." AND b.status='closed' ");

		if(empty($booking_det)) {

			$this->prepare_flashmessage(get_languageword('Invalid request'), 1);
			redirect(URL_BUYER_ENQUIRIES.'/closed');
		}

		$booking_det = $booking_det[0];

		if($booking_det->is_certificate_issued != "Yes") {

			$this->prepare_flashmessage(get_languageword('Certificate_not_issued_by_Seller'), 2);
			redirect(URL_BUYER_ENQUIRIES.'/closed');
		}

		$user_rec 	 = getUserRec($user_id);
		$seller_rec 	 = getUserRec($booking_det->seller_id);

		// Load all views as normal
		$this->load->view('book_completion_certificate', array('booking_det' => $booking_det, 'user_rec' => $user_rec, '$seller_rec' => $seller_rec));
		// Get output html
		$html = $this->output->get_output();
		echo $html;die();
	}

	function save_book_exam_quiz_result()
	{
		$response = [];

		if($this->input->is_ajax_request()) {
			//echo '<pre> $posts :: '; print_r($this->input->post());

			$exam_ids = $this->input->post('exam_id');
			$correct_answers = $this->input->post('correct_answer');
			$questions = $this->input->post('question');

			$total_correct_answers = 0;

			$input_data = array();
			$input_data['sc_id'] = $this->input->post('sc_id');
			$input_data['user_id'] = $this->ion_auth->get_user_id();

			$answers = array();

			if(!empty($exam_ids)) {
				foreach($exam_ids as $key => $val) {
					$answers[$key] = $questions[$val['exam_id']];

					if($correct_answers[$key] == $questions[$val['exam_id']])
						$total_correct_answers++;
				}
			}

			$input_data['exam_id'] = json_encode($exam_ids);
			$input_data['correct_answer'] = json_encode($correct_answers);
			$input_data['answer'] = json_encode($answers);
			$input_data['created_at'] = date('Y-m-d H:i:s');
			$input_data['updated_at'] = date('Y-m-d H:i:s');

			$input_data['total_correct_answers'] = $total_correct_answers;

			//echo '<pre> $answers :: '; print_r($answers);
			//echo '<pre> $input_data :: '; print_r($input_data);

			$this->base_model->insert_operation($input_data, 'seller_selling_books_exam_result');

			$response["response"] = "success";
			$response["message"] = "Result Saved Successfully.";

			header('Content-Type: application/json');
			echo json_encode($response);
			die;
		}
	}
}
?>