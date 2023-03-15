<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Home extends MY_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->model(array('base_model', 'home_model'));
		$this->load->library('Ajax_pagination');
		$this->load->library(array('session'));
		$this->load->library(array('ion_auth', 'form_validation'));
	}
	/*** Displays the Index Page**/
	function index()
	{
		$show_records_count_in_search_filters = strip_tags($this->config->item('site_settings')->show_records_count_in_search_filters);
		$avail_records_cnt = "";
		//Location Options
		$locations = $this->home_model->get_locations(array('child' => true));
		$location_opts[''] = get_languageword('select_location');
		if (!empty($locations)) {
			foreach ($locations as $key => $value) {
				if ($show_records_count_in_search_filters == "Yes") {
					$avail_records_cnt = " (" . count($this->home_model->get_sellers(array('location_slug' => $value->slug))) . ")";
				}
				$location_opts[$value->slug] = $value->location_name . $avail_records_cnt;
			}
		}
		$this->data['location_opts'] = $location_opts;

		//Book Options
		$books = $this->home_model->get_books();
		$book_opts[''] = get_languageword('type_of_book');
		if (!empty($books)) {
			foreach ($books as $key => $value) {
				if ($show_records_count_in_search_filters == "Yes") {
					$avail_records_cnt = " (" . count($this->home_model->get_sellers(array('book_slug' => $value->slug))) . ")";
				}
				$book_opts[$value->slug] = $value->name . $avail_records_cnt;
			}
		}
		$this->data['book_opts'] = $book_opts;

		//Recent Added Books
		$this->data['recent_books'] = $this->home_model->get_books(array('order_by' => 'books.id DESC', 'limit' => 6));

		/* Category-wise Popular Books - Start */
		$category_limit = 8;
		$book_limit   = 4;
		$this->data['popular_books'] = $this->home_model->get_popular_books($category_limit, $book_limit);
		/* Category-wise  Popular Books - End */
		//Site Testimonials
		$site_testimonials = $this->home_model->get_site_testimonials();
		$this->data['site_testimonials']	= $site_testimonials;
		// Tuotor ratings
		$home_seller_ratings = $this->home_model->get_home_seller_ratings();
		$this->data['home_seller_ratings'] = $home_seller_ratings;

		/*****05-12-2018-start********/
		$latest_blogs = $this->home_model->get_latest_blogs();
		$this->data['latest_blogs']	= $latest_blogs;
		$this->data['jquery_min']	= TRUE;
		/*****05-12-2018-end***********/

		//Send App Link Email - Start
		if ($this->input->post()) {
			//Form Validations
			$this->form_validation->set_rules('mailid', get_languageword('Email'), 'trim|required|xss_clean|valid_email');
			$this->form_validation->set_error_delimiters('<div class="error">', '</div>');
			if ($this->form_validation->run() == TRUE) {
				$to = $this->input->post('mailid');
				//Email Alert to User - Start
				//Get Send App Download Link Email Template
				$email_tpl = $this->base_model->fetch_records_from('email_templates', array('template_status' => 'Active', 'email_template_id' => '18'));

				if (!empty($email_tpl)) {
					$email_tpl = $email_tpl[0];

					if (!empty($email_tpl->from_email)) {
						$from = $email_tpl->from_email;
					} else {
						$from = get_system_settings('Portal_Email');
					}
					if (!empty($email_tpl->template_subject)) {
						$sub = $email_tpl->template_subject;
					} else {
						$sub = get_languageword('Seller_App_Download_Link');
					}
					if (!empty($email_tpl->template_content)) {
						$logo_img = '<img src="' . get_site_logo() . '" class="img-responsive" width="120px" height="50px">';

						$content 	= $email_tpl->template_content;
						$content 	= str_replace("__SITE_LOGO__", $logo_img, $content);
						$content 	= str_replace("__SITE_TITLE__", $this->config->item('site_settings')->site_title, $content);
						$content 	= str_replace("__SITE_TITLE__", $this->config->item('site_settings')->site_title, $content);
						$msg = $content;
					} else {
						$msg = "";
					}
					if (sendEmail($from, $to, $sub, $msg)) {
						$this->prepare_flashmessage(get_languageword('Seller_App_Download_Link_sent_to_your_email_successfully'), 0);
					} else {
						$this->prepare_flashmessage(get_languageword('App not sent due to some technical issue Please enter valid email Thankyou'), 2);
					}
					redirect('/#footer_sec');
					//Email Alert to User - End
				} else {
					$this->prepare_flashmessage(get_languageword('App not available Please contact Admin for any details Thankyou'), 2);
					redirect(URL_HOME_CONTACT_US);
				}
			} else {
				$this->prepare_flashmessage(validation_errors(), 1);
				redirect('/#footer_sec');
			}
		}
		//Send App Link Email - End
		$this->data['categories'] = get_categories();
		$this->data['activemenu'] 	= "home";
		$this->data['content'] 		= 'index';
		$this->_render_page('template/site/site-template', $this->data);
	}

	function contact_us()
	{
		if ($this->input->post()) {
			//Form Validations
			$this->form_validation->set_rules('fname', get_languageword('first_name'), 'trim|required|xss_clean');
			$this->form_validation->set_rules('lname', get_languageword('last_name'), 'trim|required|xss_clean');
			$this->form_validation->set_rules('email', get_languageword('email'), 'trim|required|xss_clean|valid_email');
			$this->form_validation->set_rules('sub', get_languageword('subject'), 'trim|required|xss_clean');
			$this->form_validation->set_rules('msg', get_languageword('message'), 'trim');
			$this->form_validation->set_error_delimiters('<div class="error">', '</div>');
			if ($this->form_validation->run() == TRUE) {
				$first_name = $this->input->post('fname');
				$last_name  = $this->input->post('lname');
				$email 		= $this->input->post('email');
				$subjct 	= $this->input->post('sub');
				$msgg 		= $this->input->post('msg');
				//Send conatct query details to Admin Email
				//Email Alert to Admin - Start
				//Get Contact Query Email Template
				$email_tpl = $this->base_model->fetch_records_from('email_templates', array('template_status' => 'Active', 'email_template_id' => '16'));
				$from 	= $email;
				$to 	= get_system_settings('Portal_Email');
				$sub 	= get_languageword("Contact Query Received");
				$msg 	= '<p>
									' . get_languageword('Hello Admin, ') . ',</p>
							<p>
								' . get_languageword('You got contact query Below are the details') . '</p>
							<p>
								<strong>' . get_languageword('first_name') . ':</strong> ' . $first_name . '</p>
							<p>
								<strong>' . get_languageword('last_name') . ':</strong> ' . $last_name . '</p>
							<p>
								<strong>' . get_languageword('email') . ':</strong> ' . $email . '</p>
							<p>
								<strong>' . get_languageword('subject') . ':</strong> ' . $subjct . '</p>
							<p>
								<strong>' . get_languageword('message') . ':</strong> ' . $msgg . '</p>
							<p>
								&nbsp;</p>
							';
				$msg 	.= "<p>" . get_languageword('Thank you') . "</p>";
				if (!empty($email_tpl)) {
					$email_tpl = $email_tpl[0];

					if (!empty($email_tpl->from_email)) {
						$from = $email_tpl->from_email;
					}
					if (!empty($email_tpl->template_subject)) {
						$sub = $email_tpl->template_subject;
					}
					if (!empty($email_tpl->template_content)) {
						$msg = "";
						$logo_img = '<img src="' . get_site_logo() . '" class="img-responsive" width="120px" height="50px">';
						$site_title = $this->config->item('site_settings')->site_title;

						$original_vars  = array($logo_img, $site_title, $first_name, $last_name, $email, $subjct, $msgg,);
						$temp_vars		= array('__SITE_LOGO__', '__SITE_TITLE__', '__FIRST_NAME__', '__LAST_NAME__', '__EMAIL__', '__SUBJECT__', '__MESSAGE__');
						$msg = str_replace($temp_vars, $original_vars, $email_tpl->template_content);
					}
				}
				if (sendEmail($from, $to, $sub, $msg)) {
					$this->prepare_flashmessage(get_languageword('Your contact request sent successfully'), 0);
				} else {
					$this->prepare_flashmessage(get_languageword('Your contact request not sent due to some technical issue Please contact us after some time Thankyou.'), 2);
				}
				redirect(URL_HOME_CONTACT_US);
				//Email Alert to Admin - End
			}
		}

		$this->data['activemenu'] 	= "contact_us";
		$this->data['content'] 		= 'contact_us';
		$this->data['pagetitle']	= get_languageword('contact_us');
		$this->_render_page('template/site/site-template', $this->data);
	}
	function cookies_policy()
	{
		$this->load->model('base_model');
		$cookies_policy = $this->base_model->get_page_cookies_policy();
		$this->data['cookies_policy'] 	= $cookies_policy;
		$this->data['activemenu'] 	= "blog";
		$this->data['content'] 		= 'cookies_policy';
		$this->_render_page('template/site/site-template', $this->data);
	}

	function terms_and_conditions()
	{
		$this->load->model('base_model');
		$terms_and_conditions = $this->base_model->get_page_terms_and_conditions();
		$this->data['pageTermsAndCondtions'] 	= $terms_and_conditions;
		$this->data['activemenu'] 	= "terms_conditions";
		$this->data['content'] 		= 'terms_conditions';
		$this->_render_page('template/site/site-template', $this->data);
	}
	function privacy_policy()
	{
		$this->load->model('base_model');
		$privacy_policy = $this->base_model->get_page_privacy_policy();
		$this->data['privacy_policy'] 	= $privacy_policy;
		$this->data['activemenu'] 	= "blog";
		$this->data['content'] 		= 'privacy_policy';
		$this->_render_page('template/site/site-template', $this->data);
	}
	function refund_policy()
	{
		$this->load->model('base_model');
		$refund_policy = $this->base_model->get_page_refund_policy();
		$this->data['refund_policy'] 	= $refund_policy;
		$this->data['activemenu'] 	= "blog";
		$this->data['content'] 		= 'refund_policy';
		$this->_render_page('template/site/site-template', $this->data);
	}
	function disclaimer()
	{
		$this->load->model('base_model');
		$disclaimer = $this->base_model->get_page_disclaimer();
		$this->data['disclaimer'] 	= $disclaimer;
		$this->data['activemenu'] 	= "blog";
		$this->data['content'] 		= 'disclaimer';
		$this->_render_page('template/site/site-template', $this->data);
	}

	/*** Displays All Books **/
	function all_books($category_slug = '')
	{
		$category_slug = str_replace('_', '-', $category_slug);
		$this->data['categories'] = get_categories();
		$params = array(
			'limit' 		=> LIMIT_BOOK_LIST,
			'category_slug' => $category_slug
		);
		$this->data['books'] 	  = $this->home_model->get_books($params);

		//total rows count
		unset($params['limit']);
		$total_records = count($this->home_model->get_books($params));

		$active_cat = 'all_books';
		$heading1   = get_languageword('all_books') . ' (' . $total_records . ')';
		if (!empty($category_slug)) {
			$active_cat = $category_slug;
			$heading1	= get_languageword('books_in') . ' ' . $this->home_model->get_categoryname_by_slug($category_slug) . ' (' . $total_records . ')';
		}

		$this->data['total_records'] = $total_records;
		$this->data['active_cat']	 = (!empty($category_slug)) ? $category_slug : "all_books";
		$this->data['category_slug'] = $category_slug;
		$this->data['activemenu'] 	 = "books";
		$this->data['heading1'] 	 = $heading1;
		$this->data['content'] 		 = 'all_books';
		$this->_render_page('template/site/site-template', $this->data);
	}

	function load_more_books()
	{
		$limit   		= $this->input->post('limit');
		$offset  		= $this->input->post('offset');
		$category_slug = str_replace('_', '-', $this->input->post('category_slug'));
		$params = array(
			'start'			=> $offset,
			'limit' 		=> $limit,
			'category_slug' => $category_slug
		);
		$books  		= $this->home_model->get_books($params);
		$result 		= $this->load->view('sections/book_section', array('books' => $books), true);
		$data['result'] = $result;
		$data['offset'] = $offset + $limit;
		$data['limit']  = $limit;
		echo json_encode($data);
	}

	/* SEARCH SELLER */
	function search_seller($book_slug = '', $location_slug = '', $teaching_type_slug = '')
	{
		$book_slug = (!empty($book_slug)) ? array($book_slug) : $this->input->post('book_slug');
		$location_slug = (!empty($location_slug)) ? array($location_slug) : $this->input->post('location_slug');
		$teaching_type_slug = (!empty($teaching_type_slug)) ? array($teaching_type_slug) : $this->input->post('teaching_type_slug');

		if (!empty($book_slug[0]) && $book_slug[0] == "by_location")
			$book_slug = '';
		if (!empty($book_slug[0]) && $book_slug[0] == "by_teaching_type") {
			$teaching_type_slug = $location_slug;
			$book_slug   = '';
			$location_slug = '';
		}

		$book_slug = str_replace('_', '-', $book_slug);
		$location_slug = str_replace('_', '-', $location_slug);
		$teaching_type_slug = str_replace('_', '-', $teaching_type_slug);

		$params = array(
			'limit' 	  	=> LIMIT_PROFILES_LIST,
			'book_slug' 	=> $book_slug,
			'location_slug' => $location_slug,
			'teaching_type_slug' => $teaching_type_slug
		);
		$seller_list = $this->home_model->get_sellers($params);
		$this->data['seller_list'] = $seller_list;

		//total rows count
		unset($params['limit']);
		$total_records = count($this->home_model->get_sellers($params));

		$this->data['total_records'] = $total_records;
		$this->data['book_slug'] 	 = $book_slug;
		$this->data['location_slug'] = $location_slug;
		$this->data['teaching_type_slug'] = $teaching_type_slug;

		/*** Drop-down Options - Start ***/
		$show_records_count_in_search_filters = strip_tags($this->config->item('site_settings')->show_records_count_in_search_filters);
		$avail_records_cnt = "";
		//Book Options
		$books = $this->home_model->get_books();
		$book_opts[''] = get_languageword('select');
		if (!empty($books)) {
			foreach ($books as $key => $value) {
				if ($show_records_count_in_search_filters == "Yes") {
					$avail_records_cnt = " (" . count($this->home_model->get_sellers(array('book_slug' => $value->slug, 'location_slug' => $location_slug, 'teaching_type_slug' => $teaching_type_slug))) . ")";
				}
				$book_opts[$value->slug] = $value->name . $avail_records_cnt;
			}
		}
		$this->data['book_opts'] = $book_opts;

		//Location Options
		$locations = $this->home_model->get_locations(array('child' => true));
		$location_opts[''] = get_languageword('select');
		if (!empty($locations)) {
			foreach ($locations as $key => $value) {
				if ($show_records_count_in_search_filters == "Yes") {
					$avail_records_cnt = " (" . count($this->home_model->get_sellers(array('location_slug' => $value->slug, 'book_slug' => $book_slug, 'teaching_type_slug' => $teaching_type_slug))) . ")";
				}
				$location_opts[$value->slug] = $value->location_name . $avail_records_cnt;
			}
		}
		$this->data['location_opts'] = $location_opts;

		//Teaching type Options
		$teaching_types = $this->home_model->get_teaching_types();
		$teaching_type_opts[''] = get_languageword('select');
		foreach ($teaching_types as $key => $value) {
			if ($show_records_count_in_search_filters == "Yes") {
				$avail_records_cnt = " (" . count($this->home_model->get_sellers(array('teaching_type_slug' => $value->slug, 'book_slug' => $book_slug, 'location_slug' => $location_slug))) . ")";
			}
			$teaching_type_opts[$value->slug] = $value->teaching_type . $avail_records_cnt;
		}
		$this->data['teaching_type_opts'] = $teaching_type_opts;
		/*** Drop-down Options - End ***/
		$this->data['activemenu'] 	= "search_seller";
		$this->data['content'] 		= 'search_seller';
		$this->_render_page('template/site/site-template', $this->data);
	}

	function load_more_sellers()
	{
		$limit   		= $this->input->post('limit');
		$offset  		= $this->input->post('offset');
		$book_slug  	= ($this->input->post('book_slug')) ? explode(',', $this->input->post('book_slug')) : '';
		$location_slug  = ($this->input->post('location_slug')) ? explode(',', $this->input->post('location_slug')) : '';
		$teaching_type_slug  = ($this->input->post('teaching_type_slug')) ? explode(',', $this->input->post('teaching_type_slug')) : '';

		$book_slug = str_replace('_', '-', $book_slug);
		$location_slug = str_replace('_', '-', $location_slug);
		$teaching_type_slug = str_replace('_', '-', $teaching_type_slug);
		$params = array(
			'start'			=> $offset,
			'limit' 		=> $limit,
			'book_slug' 	=> $book_slug,
			'location_slug' => $location_slug,
			'teaching_type_slug' => $teaching_type_slug
		);
		$seller_list  	= $this->home_model->get_sellers($params);
		$result 		= $this->load->view('sections/seller_list_section', array('seller_list' => $seller_list), true);
		$data['result'] = $result;
		$data['offset'] = $offset + $limit;
		$data['limit']  = $limit;
		echo json_encode($data);
	}

	/* SEARCH INSTITUTE */
	function search_institute($book_slug = '', $location_slug = '', $teaching_type_slug = '', $inst_slug = '')
	{
		$book_slug = (!empty($book_slug)) ? array($book_slug) : $this->input->post('book_slug');
		$location_slug = (!empty($location_slug)) ? array($location_slug) : $this->input->post('location_slug');
		$teaching_type_slug = (!empty($teaching_type_slug)) ? array($teaching_type_slug) : $this->input->post('teaching_type_slug');
		$inst_slug = (!empty($inst_slug)) ? array($inst_slug) : $this->input->post('inst_slug');

		if (!empty($book_slug[0]) && $book_slug[0] == "by_location")
			$book_slug = '';
		if (!empty($book_slug[0]) && $book_slug[0] == "by_teaching_type") {
			$teaching_type_slug = $location_slug;
			$book_slug   = '';
			$location_slug = '';
		}

		$book_slug = str_replace('_', '-', $book_slug);
		$location_slug = str_replace('_', '-', $location_slug);
		$teaching_type_slug = str_replace('_', '-', $teaching_type_slug);
		$inst_slug = str_replace('_', '-', $inst_slug);

		$params = array(
			'limit' 	  	=> LIMIT_PROFILES_LIST,
			'book_slug' 	=> $book_slug,
			'location_slug' => $location_slug,
			'teaching_type_slug' => $teaching_type_slug,
			'inst_slug' 	=> $inst_slug
		);
		$this->data['institute_list'] = $this->home_model->get_institutes($params);

		//total rows count
		unset($params['limit']);
		$total_records = count($this->home_model->get_institutes($params));

		$this->data['total_records'] = $total_records;
		$this->data['book_slug'] 	 = $book_slug;
		$this->data['location_slug'] = $location_slug;
		$this->data['teaching_type_slug'] = $teaching_type_slug;
		$this->data['inst_slug'] = $inst_slug;

		/*** Drop-down Options - Start ***/
		$show_records_count_in_search_filters = strip_tags($this->config->item('site_settings')->show_records_count_in_search_filters);
		$avail_records_cnt = "";
		//Book Options
		$books = $this->home_model->get_books();
		$book_opts[''] = get_languageword('select');
		if (!empty($books)) {
			foreach ($books as $key => $value) {
				if ($show_records_count_in_search_filters == "Yes") {
					$avail_records_cnt = " (" . count($this->home_model->get_institutes(array('book_slug' => $value->slug, 'location_slug' => $location_slug, 'inst_slug' => $inst_slug))) . ")";
				}
				$book_opts[$value->slug] = $value->name . $avail_records_cnt;
			}
		}
		$this->data['book_opts'] = $book_opts;

		//Location Options
		$locations = $this->home_model->get_locations(array('child' => true));
		$location_opts[''] = get_languageword('select');
		if (!empty($locations)) {
			foreach ($locations as $key => $value) {
				if ($show_records_count_in_search_filters == "Yes") {
					$avail_records_cnt = " (" . count($this->home_model->get_institutes(array('location_slug' => $value->slug, 'book_slug' => $book_slug, 'inst_slug' => $inst_slug))) . ")";
				}
				$location_opts[$value->slug] = $value->location_name . $avail_records_cnt;
			}
		}
		$this->data['location_opts'] = $location_opts;

		//Institute Options
		$insts = $this->home_model->get_institutes();
		$inst_opts[''] = get_languageword('select');
		if (!empty($insts)) {
			foreach ($insts as $key => $value) {
				$inst_opts[$value->slug] = $value->username;
			}
		}
		$this->data['inst_opts'] = $inst_opts;
		/*** Drop-down Options - End ***/

		$this->data['activemenu'] 	= "search_institute";
		$this->data['content'] 		= 'search_institute';
		$this->_render_page('template/site/site-template', $this->data);
	}

	function load_more_institutes()
	{
		$limit   		= $this->input->post('limit');
		$offset  		= $this->input->post('offset');
		$book_slug  	= ($this->input->post('book_slug')) ? explode(',', $this->input->post('book_slug')) : '';
		$location_slug  = ($this->input->post('location_slug')) ? explode(',', $this->input->post('location_slug')) : '';
		$teaching_type_slug  = ($this->input->post('teaching_type_slug')) ? explode(',', $this->input->post('teaching_type_slug')) : '';

		$book_slug = str_replace('_', '-', $book_slug);
		$location_slug = str_replace('_', '-', $location_slug);
		$teaching_type_slug = str_replace('_', '-', $teaching_type_slug);
		$params = array(
			'start'			=> $offset,
			'limit' 		=> $limit,
			'book_slug' 	=> $book_slug,
			'location_slug' => $location_slug,
			'teaching_type_slug' => $teaching_type_slug
		);
		$institute_list  = $this->home_model->get_institutes($params);
		$result 		= $this->load->view('sections/institute_list_section', array('institute_list' => $institute_list), true);
		$data['result'] = $result;
		$data['offset'] = $offset + $limit;
		$data['limit']  = $limit;
		echo json_encode($data);
	}

	/* SEARCH BUYER LEADS */
	function search_buyer_leads($book_slug = '', $location_slug = '', $teaching_type_slug = '')
	{
		$book_slug = (!empty($book_slug)) ? array($book_slug) : $this->input->post('book_slug');
		$location_slug = (!empty($location_slug)) ? array($location_slug) : $this->input->post('location_slug');
		$teaching_type_slug = (!empty($teaching_type_slug)) ? array($teaching_type_slug) : $this->input->post('teaching_type_slug');

		if (!empty($book_slug[0]) && $book_slug[0] == "by_location")
			$book_slug = '';
		if (!empty($book_slug[0]) && $book_slug[0] == "by_teaching_type") {
			$teaching_type_slug = $location_slug;
			$book_slug   = '';
			$location_slug = '';
		}
		$book_slug = str_replace('_', '-', $book_slug);
		$location_slug = str_replace('_', '-', $location_slug);
		$teaching_type_slug = str_replace('_', '-', $teaching_type_slug);
		$params = array(
			'limit' 	  	=> LIMIT_PROFILES_LIST,
			'book_slug' 	=> $book_slug,
			'location_slug' => $location_slug,
			'teaching_type_slug' => $teaching_type_slug
		);
		$this->data['buyer_leads_list'] = $this->home_model->get_buyer_leads($params);

		//total rows count
		unset($params['limit']);
		$total_records = count($this->home_model->get_buyer_leads($params));

		$this->data['total_records'] = $total_records;
		$this->data['book_slug'] 	 = $book_slug;
		$this->data['location_slug'] = $location_slug;
		$this->data['teaching_type_slug'] = $teaching_type_slug;

		$show_records_count_in_search_filters = strip_tags($this->config->item('site_settings')->show_records_count_in_search_filters);
		$avail_records_cnt = "";
		//Book Options
		$books = $this->home_model->get_books();
		$book_opts[''] = get_languageword('select');
		if (!empty($books)) {
			foreach ($books as $key => $value) {
				if ($show_records_count_in_search_filters == "Yes") {
					$avail_records_cnt = " (" . count($this->home_model->get_buyer_leads(array('book_slug' => $value->slug, 'location_slug' => $location_slug, 'teaching_type_slug' => $teaching_type_slug))) . ")";
				}
				$book_opts[$value->slug] = $value->name . $avail_records_cnt;
			}
		}
		// echo "<pre>";
		// print_r($book_opts); die();
		$this->data['book_opts'] = $book_opts;

		//Location Options
		$locations = $this->home_model->get_locations(array('child' => true));
		$location_opts[''] = get_languageword('select');
		if (!empty($locations)) {
			foreach ($locations as $key => $value) {
				if ($show_records_count_in_search_filters == "Yes") {
					$avail_records_cnt = " (" . count($this->home_model->get_buyer_leads(array('location_slug' => $value->slug, 'book_slug' => $book_slug, 'teaching_type_slug' => $teaching_type_slug))) . ")";
				}
				$location_opts[$value->slug] = $value->location_name . $avail_records_cnt;
			}
		}
		$this->data['location_opts'] = $location_opts;

		//Teaching type Options
		$teaching_types = $this->home_model->get_teaching_types();
		$teaching_type_opts[''] = get_languageword('select');
		foreach ($teaching_types as $key => $value) {
			if ($show_records_count_in_search_filters == "Yes") {
				$avail_records_cnt = " (" . count($this->home_model->get_buyer_leads(array('teaching_type_slug' => $value->slug, 'book_slug' => $book_slug, 'location_slug' => $location_slug))) . ")";
			}
			$teaching_type_opts[$value->slug] = $value->teaching_type . $avail_records_cnt;
		}
		$this->data['teaching_type_opts'] = $teaching_type_opts;

		$this->data['activemenu'] 	= "search_buyer_leads";
		$this->data['content'] 		= 'search_buyer_leads';
		$this->_render_page('template/site/site-template', $this->data);
	}

	function load_more_buyer_leads()
	{
		$limit   		= $this->input->post('limit');
		$offset  		= $this->input->post('offset');
		$book_slug  	= ($this->input->post('book_slug')) ? explode(',', $this->input->post('book_slug')) : '';
		$location_slug  = ($this->input->post('location_slug')) ? explode(',', $this->input->post('location_slug')) : '';
		$teaching_type_slug  = ($this->input->post('teaching_type_slug')) ? explode(',', $this->input->post('teaching_type_slug')) : '';

		$book_slug = str_replace('_', '-', $book_slug);
		$location_slug = str_replace('_', '-', $location_slug);
		$teaching_type_slug = str_replace('_', '-', $teaching_type_slug);
		$params = array(
			'start'			=> $offset,
			'limit' 		=> $limit,
			'book_slug' 	=> $book_slug,
			'location_slug' => $location_slug,
			'teaching_type_slug' => $teaching_type_slug
		);
		$buyer_leads_list  = $this->home_model->get_buyer_leads($params);
		$result 		= $this->load->view('sections/buyer_leads_list_section', array('buyer_leads_list' => $buyer_leads_list), true);
		$data['result'] = $result;
		$data['offset'] = $offset + $limit;
		$data['limit']  = $limit;
		echo json_encode($data);
	}


	//SELLER PROFILE
	function seller_profile($seller_slug = '')
	{
		$seller_slug = ($this->input->post('seller_slug')) ? $this->input->post('seller_slug') : $seller_slug;
		if (empty($seller_slug)) {
			$this->prepare_flashmessage(get_languageword('invalid_request'), 1);
			redirect(URL_HOME_SEARCH_SELLER);
		}
		$seller_slug = str_replace('_', '-', $seller_slug);
		$seller_details = $this->home_model->get_seller_profile($seller_slug);
		if (empty($seller_details)) {
			$this->prepare_flashmessage(get_languageword('no_details_available'), 2);
			redirect(URL_HOME_SEARCH_SELLER);
		}
		$this->data['seller_details'] = $seller_details;

		//Send Message to Seller
		if ($this->input->post()) {
			if (!$this->ion_auth->logged_in()) {
				$this->prepare_flashmessage(get_languageword('please_login_to_send_message'), 2);
				redirect(URL_AUTH_LOGIN, 'refresh');
			}
			$inputdata['from_user_id'] 	= $this->ion_auth->get_user_id();
			$credits_for_sending_message = $this->config->item('site_settings')->credits_for_sending_message;
			//Check Whether buyer is premium user or not
			if (!is_premium($inputdata['from_user_id'])) {
				$this->prepare_flashmessage(get_languageword('please_become_premium_member_to_send_message_to_seller'), 2);
				redirect(URL_BUYER_LIST_PACKAGES, 'refresh');
			}
			//Check If buyer has sufficient credits to send message to seller
			if (!is_eligible_to_make_booking($inputdata['from_user_id'], $credits_for_sending_message)) {
				$this->prepare_flashmessage(get_languageword("you_do_not_have_enough_credits_to_send_message_to_the_seller_Please_get_required_credits_here"), 2);
				redirect(URL_BUYER_LIST_PACKAGES, 'refresh');
			}
			//Form Validations
			$this->form_validation->set_rules('name', get_languageword('name'), 'trim|required|xss_clean');
			$this->form_validation->set_rules('email', get_languageword('email'), 'trim|required|xss_clean|valid_email');
			$this->form_validation->set_rules('phone', get_languageword('phone'), 'trim|required|xss_clean');
			$this->form_validation->set_rules('msg', get_languageword('message'), 'trim|required');
			$this->form_validation->set_error_delimiters('<div class="error">', '</div>');
			if ($this->form_validation->run() == TRUE) {

				$book_name = $this->base_model->fetch_value('categories', 'name', array('slug' => $this->input->post('book_slug1')));
				$inputdata['name'] 			= $this->input->post('name');
				$inputdata['book_slug']	= $book_name;
				$inputdata['email'] 		= $this->input->post('email');
				$inputdata['phone'] 		= $this->input->post('phone');
				$inputdata['message'] 		= $this->input->post('msg');
				$to_user_type   = $this->input->post('to_user_type');
				$inputdata['to_user_id']   = $this->input->post('to_user_id');
				$inputdata['created_at']	= date('Y-m-d H:i:s');
				$inputdata['updated_at']	= $inputdata['created_at'];
				$ref = $this->base_model->insert_operation($inputdata, 'messages');
				if ($ref) {
					//Send message details to Seller Email
					//Email Alert to Seller - Start
					//Get Send Message Email Template
					$email_tpl = $this->base_model->fetch_records_from('email_templates', array('template_status' => 'Active', 'email_template_id' => '17'));
					$seller_rec = getUserRec($inputdata['to_user_id']);
					$from 	= $inputdata['email'];
					$to 	= $seller_rec->email;
					$sub 	= get_languageword("Message Received From Buyer");
					$msg 	= '<p>
										' . get_languageword('Hi ') . $seller_rec->username . ',</p>
									<p>
										' . get_languageword('You got a message from Buyer Below are the details') . '</p>
									<p>
										<strong>' . get_languageword('name') . ':</strong> ' . $inputdata['name'] . '</p>
									<p>
										<strong>' . get_languageword('email') . ':</strong> ' . $inputdata['email'] . '</p>
									<p>
										<strong>' . get_languageword('phone') . ':</strong> ' . $inputdata['phone'] . '</p>
									<p>
										<strong>' . get_languageword('book_seeking') . ':</strong> ' . $inputdata['book_slug'] . '</p>
									<p>
										<strong>' . get_languageword('message') . ':</strong> ' . $inputdata['message'] . '</p>
									<p>
										&nbsp;</p>
									';
					$msg 	.= "<p>" . get_languageword('Thank you') . "</p>";
					if (!empty($email_tpl)) {
						$email_tpl = $email_tpl[0];

						if (!empty($email_tpl->from_email)) {
							$from = $email_tpl->from_email;
						}
						if (!empty($email_tpl->template_subject)) {
							$sub = $email_tpl->template_subject . get_languageword(' Buyer');
						}
						if (!empty($email_tpl->template_content)) {
							$msg = "";
							$logo_img = '<img src="' . get_site_logo() . '" class="img-responsive" width="120px" height="50px">';
							$site_title = $this->config->item('site_settings')->site_title;

							$original_vars  = array($logo_img, $site_title, $seller_rec->username, get_languageword('Buyer'), $inputdata['name'], $inputdata['email'], $inputdata['phone'], $inputdata['book_slug'], $inputdata['message']);

							$temp_vars		= array('__SITE_LOGO__', '__SITE_TITLE__', '__TO_NAME__', '__USER_TYPE__', '__NAME__', '__EMAIL__', '__PHONE__', '__BOOK__', '__MESSAGE__');

							$msg = str_replace($temp_vars, $original_vars, $email_tpl->template_content);
						}
					}
					if (sendEmail($from, $to, $sub, $msg)) {
						//Log Credits transaction data & update user net credits - Start
						$per_credit_value = $this->config->item('site_settings')->per_credit_value;
						$log_data = array(
							'user_id' => $inputdata['from_user_id'],
							'credits' => $credits_for_sending_message,
							'per_credit_value' => $per_credit_value,
							'action'  => 'debited',
							'purpose' => 'For Sending Message To Seller "' . $seller_slug . '" ',
							'date_of_action	' => date('Y-m-d H:i:s'),
							'reference_table' => 'messages',
							'reference_id' => $ref,
						);
						log_user_credits_transaction($log_data);
						update_user_credits($inputdata['from_user_id'], $credits_for_sending_message, 'debit');
						//Log Credits transaction data & update user net credits - End

						$this->prepare_flashmessage(get_languageword('Your message sent to Seller successfully'), 0);
					} else {
						$this->prepare_flashmessage(get_languageword('Your message not sent due to some technical issue Please send message after some time Thankyou'), 2);
					}
					redirect(URL_HOME_SELLER_PROFILE . '/' . $seller_slug);
				}
				//Email Alert to Seller - End
			}
		}

		//Seller Book Options
		$seller_books = $this->home_model->get_seller_books($seller_slug);
		if (!empty($seller_books)) {
			$seller_book_opts[''] = get_languageword('select');
			foreach ($seller_books as $key => $value) {
				$seller_book_opts[$value->slug] = $value->name;
			}
		} else {
			$seller_book_opts = "";
		}
		$this->data['seller_book_opts'] = $seller_book_opts;

		//Seller Location Options
		$seller_locations = $this->home_model->get_seller_locations($seller_slug);
		if (!empty($seller_locations)) {
			$seller_location_opts[''] = get_languageword('select_location');
			foreach ($seller_locations as $key => $value) {
				$seller_location_opts[$value->slug] = $value->location_name;
			}
		} else {
			$seller_location_opts = "";
		}
		$this->data['seller_location_opts'] = $seller_location_opts;
		//User Meta Data
		$this->data['meta_description'] = $seller_details[0]->meta_desc;
		$this->data['meta_keywords'] = $seller_details[0]->seo_keywords;
		//Seller Teaching types
		$seller_teaching_types = $this->home_model->get_seller_teaching_types($seller_slug);
		$this->data['seller_teaching_types'] = $seller_teaching_types;
		//Seller Reviews
		$seller_reviews = $this->home_model->get_seller_reviews($seller_slug);
		$this->data['seller_reviews'] = $seller_reviews;
		//Seller ratings
		$seller_rating	= $this->home_model->get_seller_rating($seller_slug);
		$this->data['seller_raing'] = $seller_rating;
		$this->data['activemenu'] 	= "search_seller";
		$this->data['content'] 		= 'seller_profile';
		$this->_render_page('template/site/site-template', $this->data);
	}

	function ajax_get_seller_book_details()
	{
		$avail_time_slots = array();
		$book_slug = $this->input->post('book_slug');
		$seller_id = $this->input->post('seller_id');
		$selected_date = $this->input->post('selected_date');
		if (empty($book_slug) || empty($seller_id) || empty($selected_date)) {
			echo '';
			die();
		}
		$row =  $this->home_model->get_seller_book_details($book_slug, $seller_id);
		if (empty($row)) {
			echo NULL;
			die();
		}
		$seller_time_slots = explode(',', $row->time_slots);
		$booked_slots = $this->home_model->get_booked_slots($seller_id, $row->book_id, $selected_date);
		if (!empty($booked_slots)) {
			foreach ($seller_time_slots as $slot) {
				if (!in_array($slot, $booked_slots))
					$avail_time_slots[] = $slot;
			}
		} else {
			$avail_time_slots = $seller_time_slots;
		}
		if (!empty($row))
			echo $row->fee . "~" . $row->duration_value . " " . $row->duration_type . "~" . $row->content . "~" . implode(',', $avail_time_slots) . "~" . $row->days_off;
	}

	//INSTITUTE PROFILE
	function institute_profile($inst_slug = '')
	{
		$inst_slug = ($this->input->post('inst_slug')) ? $this->input->post('inst_slug') : $inst_slug;
		if (empty($inst_slug)) {
			$this->prepare_flashmessage(get_languageword('invalid_request'), 1);
			redirect(URL_HOME_SEARCH_INSTITUTE);
		}
		$inst_slug = str_replace('_', '-', $inst_slug);

		$inst_details = $this->home_model->get_inst_profile($inst_slug);
		if (empty($inst_details)) {
			$this->prepare_flashmessage(get_languageword('no_details_available'), 2);
			redirect(URL_HOME_SEARCH_INSTITUTE);
		}

		//Send Message to Institute
		if ($this->input->post()) {
			$inputdata['from_user_id'] 	= $this->ion_auth->get_user_id();
			$credits_for_sending_message = $this->config->item('site_settings')->credits_for_sending_message;
			//Check Whether buyer is premium user or not
			if (!is_premium($inputdata['from_user_id'])) {
				$this->prepare_flashmessage(get_languageword('please_become_premium_member_to_send_message_to_institute'), 2);
				redirect(URL_BUYER_LIST_PACKAGES, 'refresh');
			}
			//Check If buyer has sufficient credits to send message to institute
			if (!is_eligible_to_make_booking($inputdata['from_user_id'], $credits_for_sending_message)) {
				$this->prepare_flashmessage(get_languageword("you_do_not_have_enough_credits_to_send_message_to_the_institute_Please_get_required_credits_here"), 2);
				redirect(URL_BUYER_LIST_PACKAGES, 'refresh');
			}
			//Form Validations
			$this->form_validation->set_rules('name', get_languageword('name'), 'trim|required|xss_clean');
			$this->form_validation->set_rules('email', get_languageword('email'), 'trim|required|xss_clean|valid_email');
			$this->form_validation->set_rules('phone', get_languageword('phone'), 'trim|required|xss_clean');
			$this->form_validation->set_rules('msg', get_languageword('message'), 'trim|required');
			$this->form_validation->set_error_delimiters('<div class="error">', '</div>');
			if ($this->form_validation->run() == TRUE) {
				$book_name = $this->base_model->fetch_value('categories', 'name', array('slug' => $this->input->post('book_slug1')));
				$inputdata['name'] 			= $this->input->post('name');
				$inputdata['book_slug']	= $book_name;
				$inputdata['email'] 		= $this->input->post('email');
				$inputdata['phone'] 		= $this->input->post('phone');
				$inputdata['message'] 		= $this->input->post('msg');
				$to_user_type   = $this->input->post('to_user_type');
				$inputdata['to_user_id']   = $this->input->post('to_user_id');
				$inputdata['created_at']	= date('Y-m-d H:i:s');
				$inputdata['updated_at']	= $inputdata['created_at'];
				$ref = $this->base_model->insert_operation($inputdata, 'messages');
				if ($ref) {
					//Email Alert to Institute - Start
					//Get Send Message Email Template
					$email_tpl = $this->base_model->fetch_records_from('email_templates', array('template_status' => 'Active', 'email_template_id' => '17'));
					$inst_rec = getUserRec($inputdata['to_user_id']);
					$from 	= $inputdata['email'];
					$to 	= $inst_rec->email;
					$sub 	= get_languageword("Message Received From Buyer");
					$msg 	= '<p>
										' . get_languageword('Hi ') . $inst_rec->username . ',</p>
									<p>
										' . get_languageword('You got a message from Buyer Below are the details') . '</p>
									<p>
										<strong>' . get_languageword('name') . ':</strong> ' . $inputdata['name'] . '</p>
									<p>
										<strong>' . get_languageword('email') . ':</strong> ' . $inputdata['email'] . '</p>
									<p>
										<strong>' . get_languageword('phone') . ':</strong> ' . $inputdata['phone'] . '</p>
									<p>
										<strong>' . get_languageword('book_seeking') . ':</strong> ' . $inputdata['book_slug'] . '</p>
									<p>
										<strong>' . get_languageword('message') . ':</strong> ' . $inputdata['message'] . '</p>
									<p>
										&nbsp;</p>
									';
					$msg 	.= "<p>" . get_languageword('Thank you') . "</p>";
					if (!empty($email_tpl)) {
						$email_tpl = $email_tpl[0];

						if (!empty($email_tpl->from_email)) {
							$from = $email_tpl->from_email;
						}
						if (!empty($email_tpl->template_subject)) {
							$sub = $email_tpl->template_subject . get_languageword(' Buyer');
						}
						if (!empty($email_tpl->template_content)) {
							$msg = "";
							$logo_img = '<img src="' . get_site_logo() . '" class="img-responsive" width="120px" height="50px">';
							$site_title = $this->config->item('site_settings')->site_title;

							$original_vars  = array($logo_img, $site_title, $inst_rec->username, get_languageword('Buyer'), $inputdata['name'], $inputdata['email'], $inputdata['phone'], $inputdata['book_slug'], $inputdata['message']);
							$temp_vars		= array('__SITE_LOGO__', '__SITE_TITLE__', '__TO_NAME__', '__USER_TYPE__', '__NAME__', '__EMAIL__', '__PHONE__', '__BOOK__', '__MESSAGE__');

							$msg = str_replace($temp_vars, $original_vars, $email_tpl->template_content);
						}
					}
					if (sendEmail($from, $to, $sub, $msg)) {
						//Log Credits transaction data & update user net credits - Start
						$per_credit_value = $this->config->item('site_settings')->per_credit_value;
						$log_data = array(
							'user_id' => $inputdata['from_user_id'],
							'credits' => $credits_for_sending_message,
							'per_credit_value' => $per_credit_value,
							'action'  => 'debited',
							'purpose' => 'For Sending Message To Institute "' . $inst_slug . '" ',
							'date_of_action	' => date('Y-m-d H:i:s'),
							'reference_table' => 'messages',
							'reference_id' => $ref,
						);
						log_user_credits_transaction($log_data);
						update_user_credits($inputdata['from_user_id'], $credits_for_sending_message, 'debit');
						//Log Credits transaction data & update user net credits - End

						$this->prepare_flashmessage(get_languageword('Your message sent to Institute successfully'), 0);
					} else {
						$this->prepare_flashmessage(get_languageword('Your message not sent due to some technical issue Please send message after some time Thankyou'), 2);
					}
					redirect(URL_HOME_INSTITUTE_PROFILE . '/' . $inst_slug);
				}
				//Email Alert to Institute - End
			}
		}

		$this->data['inst_details'] = $inst_details;
		//Inst meta data
		$this->data['meta_description'] = $inst_details[0]->meta_desc;
		$this->data['meta_keywords'] = $inst_details[0]->seo_keywords;
		$this->data['activemenu'] 	= "search_institute";
		$this->data['content'] 		= 'institute_profile';
		$this->_render_page('template/site/site-template', $this->data);
	}

	//BUYER PROFILE
	function buyer_profile($buyer_slug = '', $buyer_lead_id = '')
	{
		if (!$this->ion_auth->logged_in()) {
			$this->prepare_flashmessage(get_languageword('please_login_to_continue'), 2);
			redirect(URL_AUTH_LOGIN);
		}
		if ($this->ion_auth->is_buyer() || $this->ion_auth->is_admin()) {
			$this->prepare_flashmessage(get_languageword('You dont have permission to access this page'), 1);
			redirect(URL_AUTH_LOGIN);
		}
		$buyer_slug = ($this->input->post('buyer_slug')) ? $this->input->post('buyer_slug') : $buyer_slug;
		if (empty($buyer_slug)) {
			$this->prepare_flashmessage(get_languageword('invalid_request'), 1);
			redirect(URL_HOME_SEARCH_BUYER_LEADS);
		}
		$buyer_slug = str_replace('_', '-', $buyer_slug);
		$buyer_lead_id = ($this->input->post('lead_id')) ? $this->input->post('lead_id') : $buyer_lead_id;
		$stduent_details = $this->home_model->get_buyer_profile($buyer_slug, $buyer_lead_id);

		if (empty($stduent_details)) {
			$this->prepare_flashmessage(get_languageword('no_details_available'), 2);
			redirect(URL_HOME_SEARCH_BUYER_LEADS);
		}

		//Send Message to Buyer
		if ($this->input->post()) {
			$from_user_type = "";
			if ($this->ion_auth->is_seller())
				$from_user_type = 'seller';
			else if ($this->ion_auth->is_institute())
				$from_user_type = 'institute';
			$inputdata['from_user_id'] 	= $this->ion_auth->get_user_id();
			$credits_for_sending_message = $this->config->item('site_settings')->credits_for_sending_message;
			//Check Whether buyer is premium user or not
			if (!is_premium($inputdata['from_user_id'])) {
				$this->prepare_flashmessage(get_languageword('please_become_premium_member_to_send_message_to_buyer'), 2);
				if ($from_user_type == "seller")
					redirect(URL_SELLER_LIST_PACKAGES, 'refresh');
				else if ($from_user_type == "institute")
					redirect(URL_SELLER_LIST_PACKAGES, 'refresh');
				else
					redirect(URL_AUTH_INDEX);
			}
			//Check If buyer has sufficient credits to send message to institute
			if (!is_eligible_to_make_booking($inputdata['from_user_id'], $credits_for_sending_message)) {
				$this->prepare_flashmessage(get_languageword("you_do_not_have_enough_credits_to_send_message_to_the_buyer_Please_get_required_credits_here"), 2);
				if ($from_user_type == "seller")
					redirect(URL_SELLER_LIST_PACKAGES, 'refresh');
				else if ($from_user_type == "institute")
					redirect(URL_SELLER_LIST_PACKAGES, 'refresh');
				else
					redirect(URL_AUTH_INDEX);
			}
			//Form Validations
			$this->form_validation->set_rules('name', get_languageword('name'), 'trim|required|xss_clean');
			$this->form_validation->set_rules('email', get_languageword('email'), 'trim|required|xss_clean|valid_email');
			$this->form_validation->set_rules('phone', get_languageword('phone'), 'trim|required|xss_clean');
			$this->form_validation->set_rules('msg', get_languageword('message'), 'trim|required');
			$this->form_validation->set_error_delimiters('<div class="error">', '</div>');
			if ($this->form_validation->run() == TRUE) {
				$inputdata['name'] 			= $this->input->post('name');
				$inputdata['book_slug']	= $this->input->post('book_slug1');
				$inputdata['email'] 		= $this->input->post('email');
				$inputdata['phone'] 		= $this->input->post('phone');
				$inputdata['message'] 		= $this->input->post('msg');
				$to_user_type   = $this->input->post('to_user_type');
				$inputdata['to_user_id']   = $this->input->post('to_user_id');
				$inputdata['created_at']	= date('Y-m-d H:i:s');
				$inputdata['updated_at']	= $inputdata['created_at'];
				$ref = $this->base_model->insert_operation($inputdata, 'messages');
				if ($ref) {
					//Email Alert to Buyer - Start
					//Get Send Message Email Template
					$email_tpl = $this->base_model->fetch_records_from('email_templates', array('template_status' => 'Active', 'email_template_id' => '17'));
					$buyer_rec = getUserRec($inputdata['to_user_id']);
					$from 	= $inputdata['email'];
					$to 	= $buyer_rec->email;
					$sub 	= get_languageword("Message Received From ") . " " . get_languageword(ucfirst($from_user_type));
					$msg 	= '<p>
										' . get_languageword('Hi ') . $buyer_rec->username . ',</p>
									<p>
										' . get_languageword('You got a message from ' . ucfirst($from_user_type) . ' Below are the details') . '</p>
									<p>
										<strong>' . get_languageword('name') . ':</strong> ' . $inputdata['name'] . '</p>
									<p>
										<strong>' . get_languageword('email') . ':</strong> ' . $inputdata['email'] . '</p>
									<p>
										<strong>' . get_languageword('phone') . ':</strong> ' . $inputdata['phone'] . '</p>
									<p>
										<strong>' . get_languageword('message') . ':</strong> ' . $inputdata['message'] . '</p>
									<p>
										&nbsp;</p>
									';
					$msg 	.= "<p>" . get_languageword('Thank you') . "</p>";
					if (!empty($email_tpl)) {
						$email_tpl = $email_tpl[0];

						if (!empty($email_tpl->from_email)) {
							$from = $email_tpl->from_email;
						}
						if (!empty($email_tpl->template_subject)) {
							$sub = $email_tpl->template_subject . " " . get_languageword(ucfirst($from_user_type));
						}
						if (!empty($email_tpl->template_content)) {
							$msg = "";
							$logo_img = '<img src="' . get_site_logo() . '" class="img-responsive" width="120px" height="50px">';
							$site_title = $this->config->item('site_settings')->site_title;

							$original_vars  = array($logo_img, $site_title, $buyer_rec->username, get_languageword(ucfirst($from_user_type)), $inputdata['name'], $inputdata['email'], $inputdata['phone'], $inputdata['book_slug'], $inputdata['message']);
							$temp_vars		= array('__SITE_LOGO__', '__SITE_TITLE__', '__TO_NAME__', '__USER_TYPE__', '__NAME__', '__EMAIL__', '__PHONE__', '__BOOK__', '__MESSAGE__');

							$msg = str_replace($temp_vars, $original_vars, $email_tpl->template_content);
						}
					}
					if (sendEmail($from, $to, $sub, $msg)) {
						//Log Credits transaction data & update user net credits - Start
						$per_credit_value = $this->config->item('site_settings')->per_credit_value;
						$log_data = array(
							'user_id' => $inputdata['from_user_id'],
							'credits' => $credits_for_sending_message,
							'per_credit_value' => $per_credit_value,
							'action'  => 'debited',
							'purpose' => 'For Sending Message To Buyer "' . $buyer_slug . '" ',
							'date_of_action	' => date('Y-m-d H:i:s'),
							'reference_table' => 'messages',
							'reference_id' => $ref,
						);
						log_user_credits_transaction($log_data);
						update_user_credits($inputdata['from_user_id'], $credits_for_sending_message, 'debit');
						//Log Credits transaction data & update user net credits - End

						$this->prepare_flashmessage(get_languageword('Your message sent to Buyer successfully'), 0);
					} else {
						$this->prepare_flashmessage(get_languageword('Your message not sent due to some technical issue Please send message after some time Thankyou'), 2);
					}
					redirect(URL_VIEW_BUYER_PROFILE . '/' . $buyer_slug . '/' . $buyer_lead_id);
				}
				//Email Alert to Buyer - End
			}
		}

		$this->data['stduent_details'] = $stduent_details;
		//Buyer Meta Data
		$this->data['meta_description'] = $stduent_details[0]->meta_desc;
		$this->data['meta_keywords'] = $stduent_details[0]->seo_keywords;

		$this->data['activemenu'] 	= "search_buyer_leads";
		$this->data['content'] 		= 'buyer_profile';
		$this->_render_page('template/site/site-template', $this->data);
	}
	function ajax_get_institute_batches()
	{
		$book_id = $this->input->post('book_id');
		$inst_id = $this->input->post('inst_id');
		$this->load->model('institute/institute_model');
		$batches = $this->institute_model->get_batches_by_book($book_id, $inst_id);
		$batch_opts = '';
		if (!empty($batches)) {
			$batch_opts .= '<option value="">' . get_languageword('select_batch') . '</option>';
			foreach ($batches as $key => $value) {
				$batch_opts .= '<option value="' . $value->batch_id . '">' . $value->batch_name . '</option>';
			}
		} else {
			$batch_opts = '<option value="">' . get_languageword('no_batches_available') . '</option>';
		}
		echo $batch_opts;
	}
	function ajax_get_institute_batches_info()
	{
		$book_id = $this->input->post('book_id');
		$inst_id = $this->input->post('inst_id');
		$batch_id = $this->input->post('batch_id');

		$batch_status = "";
		$batche_info = $this->home_model->get_institute_batches_info_by_book($book_id, $inst_id, $batch_id);
		$total_enrolled = $this->home_model->total_enrolled_buyers_in_batch($batch_id);
		$available_slots = "";
		$html = "";
		foreach ($batche_info as  $row) {
			$available_slots = $row->batch_max_strength - $total_enrolled;
			$today = date('Y-m-d');
			if ($row->batch_start_date >= $today)
				$batch_status = get_languageword('not_yet_started');
			else
				$batch_status = get_languageword('running');
			$html .= '<div class="dashboard-panel">
					<h2>Batch Details</h2>
						<div class="table-responsive">
                           	<table class="report-table row-border">
                            	<thead>
		                            <tr>
		                              	<th>' . get_languageword('batch_code') . '</th>
			                            <th>' . get_languageword('seller_name') . '</th>
			                            <th>' . get_languageword('book_content') . '</th>
			                            <th>' . get_languageword('time_slot') . '</th>
			                            <th>' . get_languageword('book_offering_location') . '</th>
			                            <th>' . get_languageword('batch_start_date') . '</th>
			                            <th>' . get_languageword('batch_end_date') . '</th>
			                            <th>' . get_languageword('days_off') . '</th>
			                            <th>' . get_languageword('fee') . ' (' . get_languageword('in_credits') . ')</th>
			                            <th>' . get_languageword('batch_max_strength') . '</th>
			                            <th>' . get_languageword('slots_available') . '</th>
			                            <th>' . get_languageword('batch_status') . '</th>
			                        </tr>
                            	</thead>
                           		<tbody>
		                            <tr>
		                                <td>' . $row->batch_code . '</td>
		                                <td>' . $row->sellername . '</td>
		                                <td><div class="message more">' . strip_tags($row->book_content) . '</div></td>
		                                <td>' . $row->time_slot . '</td>
		                                <td>' . $row->book_offering_location . '</td>
		                                <td>' . $row->batch_start_date . '</td>
		                                <td>' . $row->batch_end_date . '</td>
		                                 <td>' . $row->days_off . '</td>
		                                <td>' . $row->fee . '</td>
		                                <td>' . $row->batch_max_strength . '</td>
		                                <td>' . $available_slots . '</td>
		                                <td>' . $batch_status . '</td>
		                            </tr>
	                        	</tbody>
                        	</table>
                		</div>
                    </div>';
		}
		echo $html;
	}

	/*** Displays All Selling Books **/
	function buy_books($category_slug = '')
	{
		$category_slug = str_replace('_', '-', $category_slug);
		$this->data['categories'] = get_categories();
		$params = array(
			'limit' 		=> LIMIT_BOOK_LIST,
			'category_slug' => $category_slug
		);

		$this->data['selling_books'] 	  = $this->home_model->get_selling_books($params);

		//total rows count
		unset($params['limit']);

		$total_records = count($this->home_model->get_selling_books($params));
		$total_records = ($total_records > 0) ? $total_records : 0;
		$heading1   = get_languageword('selling_books') . ' (' . $total_records . ')';

		if (!empty($category_slug)) {
			$active_cat = $category_slug;
			$heading1	= get_languageword('selling_books_in') . ' ' . $this->home_model->get_categoryname_by_slug($category_slug) . ' (' . $total_records . ')';
		}

		$this->data['total_records'] = $total_records;
		$this->data['active_cat']	 = (!empty($category_slug)) ? $category_slug : "all_books";
		$this->data['category_slug'] = $category_slug;
		$this->data['activemenu'] 	 = "buy_books";
		$this->data['heading1'] 	 = $heading1;
		$this->data['content'] 		 = 'selling_books';

		$this->_render_page('template/site/site-template', $this->data);
	}

	/*** Displays All Selling Books **/
	function api($category_slug = '')
	{
		//var_dump($this->input->post('email'));die("working here");

		$category_slug = str_replace('_', '-', $category_slug);
		$this->data['categories'] = get_categories();

		$params = array(
			'limit' 		=> LIMIT_BOOK_LIST,
			'category_slug' => $category_slug
		);

		$data = $this->home_model->get_selling_books_via_api($params);

		//echo '<pre> $data :: '; print_r($data); die;

		if(!empty($data)) {
			foreach($data as $key => $val) {
				/*$book_image_arr = [];

				$bookImageBody = '';
				$bookPreviewImageBody = '';
				$bookPreviewFileBody = '';*/

				if(isset($val->image) && $val->image != '') {
					$response = common_s3_function('get', 'file', $val->image);

					if(!empty($response) && $response['status'] == 'success')
						$data[$key]->image_s3_file = $response['message'];
				}

				if(isset($val->preview_image) && $val->preview_image != '') {
					$response = common_s3_function('get', 'file', $val->preview_image);

					if(!empty($response) && $response['status'] == 'success')
						$data[$key]->preview_image_s3_file = $response['message'];
				}

				if(isset($val->preview_file) && $val->preview_file != '') {
					$response = common_s3_function('get', 'file', $val->preview_image);

					if(!empty($response) && $response['status'] == 'success')
						$data[$key]->preview_file_s3_file = $response['message'];
				}

				/*try {
					if(isset($val->image) && $val->image != '') {
						$getBookImage = $this->s3Client->getObject([
										'Bucket' => $this->bucket_name,
										'Key' => $this->book_s3_upload_path.$val->image,
									]);

						$bookImageBody = $getBookImage->get('Body');

						$data[$key]->image_s3_file = base64_encode($bookImageBody);
					}
				} catch (Aws\S3\Exception\S3Exception $e) {
					//echo $e->getMessage();
					$this->prepare_flashmessage(get_languageword("There is an error in fetching Book Image from S3 server"), 2);
				}

				try {
					if(isset($val->preview_image) && $val->preview_image != '') {
						$getBookPreviewImage = $this->s3Client->getObject([
										'Bucket' => $this->bucket_name,
										'Key' => $this->book_s3_upload_path.$val->preview_image,
									]);

						$bookPreviewImageBody = $getBookPreviewImage->get('Body');

						$data[$key]->preview_image_s3_file = base64_encode($bookPreviewImageBody);
					}
				} catch (Aws\S3\Exception\S3Exception $e) {
					//echo $e->getMessage();
					$this->prepare_flashmessage(get_languageword("There is an error in fetching Book Preview Image from S3 server"), 2);
				}

				try {
					if(isset($val->preview_file) && $val->preview_file != '') {
						$bookPreviewFile = $this->s3Client->getObject([
										'Bucket' => $this->bucket_name,
										'Key' => $this->book_s3_upload_path.$val->preview_file,
									]);

						$bookPreviewFileBody = $bookPreviewFile->get('Body');

						$data[$key]->preview_file_s3_file = base64_encode($bookPreviewFileBody);
					}
				} catch (Aws\S3\Exception\S3Exception $e) {
					//echo $e->getMessage();
					$this->prepare_flashmessage(get_languageword("There is an error in fetching Book Preview File from S3 server"), 2);
				}*/
			}
		}

		//echo '<pre> $data :: '; print_r($data); die;

		$data = json_encode($data, true);

		echo $data;
	}

	function load_more_selling_books()
	{
		$limit   		= $this->input->post('limit');
		$offset  		= $this->input->post('offset');
		$category_slug  = str_replace('_', '-', $this->input->post('category_slug'));
		$params = array(
			'start'			=> $offset,
			'limit' 		=> $limit,
			'category_slug'	=> $category_slug
		);
		$selling_books = $this->home_model->get_selling_books($params);
		$result 		= $this->load->view('sections/selling_book_section', array('selling_books' => $selling_books), true);
		$data['result'] = $result;
		$data['offset'] = $offset + $limit;
		$data['limit']  = $limit;
		echo json_encode($data);
	}

	function buy_book($selling_book_slug = "", $empty_paramurl = "")
	{
		//die($empty_paramurl);
		
		if (empty($selling_book_slug)) {
			$this->prepare_flashmessage(get_languageword('Invalid_Request'), 1);
			redirect(URL_HOME_BUY_BOOKS);
		}
		$selling_book_slug = str_replace('_', '-', $selling_book_slug);
		$sc_id = $this->base_model->fetch_value('seller_selling_books', 'sc_id', array('slug' => $selling_book_slug));


		if (!($sc_id > 0)) {
			$this->prepare_flashmessage(get_languageword('Invalid_Request'), 1);
			redirect(URL_HOME_BUY_BOOKS . "/" . $empty_paramurl);
		}

		$record = get_seller_sellingbook_info($sc_id);

		//echo '<pre> $record :: '; print_r($record); die;

		if (empty($record)) {
			$this->prepare_flashmessage(get_languageword('No Details Found'), 2);
			redirect(URL_HOME_BUY_BOOKS);
		}

		$book_image_arr = [];

		$bookImageBody = '';
		$bookPreviewImageBody = '';
		$bookPreviewFileBody = '';
		$presignedUrl = '';
		$mime_type = '';

		if(isset($record->image) && $record->image != '') {
			$response = common_s3_function('get', 'file', $record->image);

			if(!empty($response) && $response['status'] == 'success')
				$bookImageBody = $response['message'];
			else
				$this->prepare_flashmessage(get_languageword("There is an error in fetching Book Image from S3 server"), 2);
		}

		if(isset($record->preview_image) && $record->preview_image != '') {
			$response = common_s3_function('get', 'file', $record->preview_image);

			if(!empty($response) && $response['status'] == 'success')
				$bookPreviewImageBody = $response['message'];
			else
				$this->prepare_flashmessage(get_languageword("There is an error in fetching Book Preview Image from S3 server"), 2);
		}

		if(isset($record->preview_file) && $record->preview_file != '') {
			$response = common_s3_function('get', 'file', $record->preview_file);

			if(!empty($response) && $response['status'] == 'success') {
				$bookPreviewFileBody = $response['message'];

				$f = finfo_open();
				$mime_type = finfo_buffer($f, base64_decode($response['message']), FILEINFO_MIME_TYPE);
			} else {
				$this->prepare_flashmessage(get_languageword("There is an error in fetching Book Preview File from S3 server"), 2);
			}
		}

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

				$cmd = $this->s3Client->getCommand('GetObject', [
						'Bucket' => $this->bucket_name,
						'Key' => $this->book_s3_upload_path.$record->image,
					]);

				$request = $this->s3Client->createPresignedRequest($cmd, '+40 minutes');

				// Get the actual presigned-url
				$presignedUrl = (string)$request->getUri();

				$f = finfo_open();
				$mime_type = finfo_buffer($f, $bookPreviewFileBody, FILEINFO_MIME_TYPE);
			}
		} catch (Aws\S3\Exception\S3Exception $e) {
			//echo $e->getMessage();
			$this->prepare_flashmessage(get_languageword("There is an error in fetching Book Preview File from S3 server"), 2);
		}*/

		$this->data['record'] = $record;

		$this->data['record']->book_image_arr['image'] = $bookImageBody;
		$this->data['record']->book_image_arr['preview_image'] = $bookPreviewImageBody;
		$this->data['record']->book_image_arr['preview_file'] = $bookPreviewFileBody;
		$this->data['record']->book_image_arr['preview_file_mimetype'] = $mime_type;

		$this->data['uid'] = $this->ion_auth->get_user_id();

		// echo "<pre>";print_r($this->data);
		// die();

		//$this->data['record']->book_image_arr['preview_file_presignedUrl'] = $presignedUrl;

		//echo '<pre> $record :: '; print_r($this->data['record']); die;

		//var_dump($this->session->userdata); die;

		if ($this->ion_auth->logged_in()) {
			$user_id = $this->ion_auth->get_user_id();
			$this->data['is_purchased'] = $this->base_model->get_query_row("SELECT max_downloads FROM " . TBL_PREFIX . "book_purchases WHERE sc_id=" . $sc_id . " AND user_id=" . $user_id . " ORDER BY max_downloads DESC LIMIT 1 ");

			if ($this->config->item('site_settings')->like_comment_setting == "Yes") {
				$SELECTCOLUMN = "  CASE WHEN " . TBL_PREFIX . "selling_book_likes.user_id = " . $user_id . "  THEN 'yes'  ELSE 'no' END AS userliked ,(SELECT count(" . TBL_PREFIX . "selling_book_likes.like_id) FROM " . TBL_PREFIX . "selling_book_likes WHERE " . TBL_PREFIX . "selling_book_likes.item_id=" . $sc_id . ") as likescount";
				$SELECTCOLUMN .= "  ,   CASE WHEN " . TBL_PREFIX . "selling_book_rating.user_id = " . $user_id . "  THEN 'yes'  ELSE 'no' END AS userratings ,(SELECT count(" . TBL_PREFIX . "selling_book_rating.rating_id) FROM " . TBL_PREFIX . "selling_book_rating WHERE " . TBL_PREFIX . "selling_book_rating.item_id=" . $sc_id . ") as ratingscount,(SELECT COALESCE(SUM(" . TBL_PREFIX . "selling_book_rating.rating_number),0) FROM " . TBL_PREFIX . "selling_book_rating WHERE " . TBL_PREFIX . "selling_book_rating.item_id=" . $sc_id . ") as totalratingscount";
				$SELECTCOLUMN .= "  ,   CASE WHEN " . TBL_PREFIX . "selling_book_rating.user_id = " . $user_id . "  AND  " . TBL_PREFIX . "selling_book_rating.review IS NOT NULL THEN COUNT(" . TBL_PREFIX . "selling_book_rating.rating_id)  ELSE '0' END AS usercomment";
				$DB_LEFTJOIN_QUERY = "LEFT JOIN " . TBL_PREFIX . "selling_book_rating ON " . TBL_PREFIX . "selling_book_rating.item_id = " . TBL_PREFIX . "selling_book_likes.item_id";
				$DB_QUERY = "SELECT *," . $SELECTCOLUMN . " FROM " . TBL_PREFIX . "selling_book_likes " . $DB_LEFTJOIN_QUERY . " WHERE " . TBL_PREFIX . "selling_book_likes.item_id=" . $sc_id . " AND " . TBL_PREFIX . "selling_book_likes.user_id=" . $user_id . "; ";

				//echo $DB_QUERY;	die;
				$likesystem = $this->base_model->get_query_row($DB_QUERY);
				$this->data['likecommentsystem'] = $likesystem;
			}
		} else {

			if ($this->config->item('site_settings')->like_comment_setting == "Yes") {
				$SELECTCOLUMN = "  CASE WHEN " . TBL_PREFIX . "selling_book_likes.user_id   THEN 'yes'  ELSE 'no' END AS userliked ,(SELECT count(" . TBL_PREFIX . "selling_book_likes.like_id) FROM " . TBL_PREFIX . "selling_book_likes WHERE " . TBL_PREFIX . "selling_book_likes.item_id=" . $sc_id . ") as likescount";
				$SELECTCOLUMN .= "  ,   CASE WHEN " . TBL_PREFIX . "selling_book_rating.user_id IS NOT NULL THEN 'yes'  ELSE 'no' END AS userratings ,(SELECT count(" . TBL_PREFIX . "selling_book_rating.rating_id) FROM " . TBL_PREFIX . "selling_book_rating WHERE " . TBL_PREFIX . "selling_book_rating.item_id=" . $sc_id . ") as ratingscount,(SELECT COALESCE(SUM(" . TBL_PREFIX . "selling_book_rating.rating_number),0) FROM " . TBL_PREFIX . "selling_book_rating WHERE " . TBL_PREFIX . "selling_book_rating.item_id=" . $sc_id . ") as totalratingscount";
				$SELECTCOLUMN .= "  ,   CASE WHEN " . TBL_PREFIX . "selling_book_rating.user_id IS NOT NULL  AND  " . TBL_PREFIX . "selling_book_rating.review IS NOT NULL THEN COUNT(" . TBL_PREFIX . "selling_book_rating.rating_id)  ELSE '0' END AS usercomment";
				$DB_LEFTJOIN_QUERY = "LEFT JOIN " . TBL_PREFIX . "selling_book_rating ON " . TBL_PREFIX . "selling_book_rating.item_id = " . TBL_PREFIX . "selling_book_likes.item_id";
				$DB_QUERY = "SELECT *," . $SELECTCOLUMN . " FROM " . TBL_PREFIX . "selling_book_likes " . $DB_LEFTJOIN_QUERY . " WHERE " . TBL_PREFIX . "selling_book_likes.item_id=" . $sc_id . " AND " . TBL_PREFIX . "selling_book_likes.user_id IS NOT NULL; ";

				//echo $DB_QUERY;	die;
				$likesystem = $this->base_model->get_query_row($DB_QUERY);
				$this->data['likecommentsystem'] = $likesystem;
			}
		}

		//More From this Seller
		$params = array(
			'limit' 		=> 4,
			'seller_slug'	=> $record->seller_id
		);
		$this->data['more_selling_books'] = $this->home_model->get_selling_books($params);

		$this->data['activemenu'] 	= "buy_books";
		$this->data['content'] 		= 'buy_book';


		//var_dump($record);die;

		$actual_price = $record->actual_price;
		$discount_price = $record->book_price;
		$total_discounted_price = "";

		if ($discount_price && $actual_price) {
			if ($discount_price > 0 && $actual_price > 0) {
				$total_discounted_price = (($actual_price - $discount_price) / $actual_price) * 100;

				$total_discounted_price =  round($total_discounted_price) . "% Discount";
			} else {
				$total_discounted_price =  "100% off";
			}
		}




		$this->data['pagetitle'] 		 = $record->book_name . " " . $total_discounted_price;
		$this->data['pageogtype'] 		 = '';
		$this->data['pageogimage'] 		 = "https://" . $_SERVER['SERVER_NAME'] . "/assets/uploads/book_curriculum_files/" . $record->preview_image;
		//	$this->data['pagetitle'] 	= get_languageword('buy_book');
		$this->_render_page('template/site/site-template', $this->data);
	}
	function get_free($selling_book_slug = "")
	{
		if (empty($selling_book_slug)) {
			$this->prepare_flashmessage(get_languageword('Invalid_Request'), 1);
			redirect(URL_HOME_BUY_BOOKS);
		}
		$selling_book_slug = str_replace('_', '-', $selling_book_slug);
		$sc_id = $this->base_model->fetch_value('seller_selling_books', 'sc_id', array('slug' => $selling_book_slug));
		if (!($sc_id > 0)) {
			$this->prepare_flashmessage(get_languageword('Invalid_Request'), 1);
			redirect(URL_HOME_BUY_BOOKS);
		}
		$record = get_seller_sellingbook_info($sc_id);
		if (empty($record)) {
			$this->prepare_flashmessage(get_languageword('No Details Found'), 2);
			redirect(URL_HOME_BUY_BOOKS);
		}
		// 	echo "string";
		// die();
		$total_amount 					= $record->book_price;
		$admin_commission 				= 0;

		$input_data['sc_id']    	    = $sc_id;
		$input_data['seller_id']    	    = $record->seller_id;
		$input_data['total_amount']     = $total_amount;
		$input_data['admin_commission_percentage']  = $admin_commission;
		$input_data['admin_commission_val']     	= $admin_commission;
		$input_data['max_downloads']    = $record->max_downloads;
		$input_data['payment_gateway_id']   = 'free';
		$input_data['paid_date']     		= date('Y-m-d H:i:s');
		$input_data['last_modified']     	= date('Y-m-d H:i:s');

		$book_title = $record->book_title;
		$this->session->set_userdata('is_valid_request', 1);
		$this->session->set_userdata('book_purchase_data', $input_data);
		$this->session->set_userdata('selling_book_slug', $selling_book_slug);
		$this->session->set_userdata('selling_book_det', $record);
		$this->add_book('auto', $input_data);


		$this->data['record'] = $record;

		if ($this->ion_auth->logged_in()) {
			$user_id = $this->ion_auth->get_user_id();
			$this->data['is_purchased'] = $this->base_model->get_query_row("SELECT max_downloads FROM " . TBL_PREFIX . "book_purchases WHERE sc_id=" . $sc_id . " AND user_id=" . $user_id . " ORDER BY max_downloads DESC LIMIT 1 ");
		}
	}

	function checkout($selling_book_slug = "", $payment_gateway = "")
	{

		$selling_book_slug = str_replace('_', '-', $selling_book_slug);
		$sc_id = $this->base_model->fetch_value('seller_selling_books', 'sc_id', array('slug' => $selling_book_slug));

		if ($this->ion_auth->logged_in()) {
			$user_id = $this->ion_auth->get_user_id();
			$this->data['is_purchased'] = $this->base_model->get_query_row("SELECT max_downloads FROM " . TBL_PREFIX . "book_purchases WHERE sc_id=" . $sc_id . " AND user_id=" . $user_id . " ORDER BY max_downloads DESC LIMIT 1 ");
		}

		if (!empty($this->data['is_purchased']) && $this->data['is_purchased']->max_downloads > 0) {
			redirect(URL_BUYER_BOOK_PURCHASES);
		}

		// echo "<pre>";
		// print_r($this->data['is_purchased']);
		// die();


		if (empty($selling_book_slug)) {
			$this->prepare_flashmessage(get_languageword('Invalid_Request'), 1);
			redirect(URL_HOME_BUY_BOOKS);
		}

		if (!($sc_id > 0)) {
			$this->prepare_flashmessage(get_languageword('Invalid_Request'), 1);
			redirect(URL_HOME_BUY_BOOKS);
		}

		if (!$this->ion_auth->logged_in()) {
			$this->session->set_userdata('req_from', 'buy_book');
			$this->session->set_userdata('selling_book_slug', $selling_book_slug);
			$this->prepare_flashmessage(get_languageword('please_login_to_continue'), 2);
			redirect(URL_AUTH_LOGIN);
		}

		$record = get_seller_sellingbook_info($sc_id);
		if (empty($record)) {
			$this->prepare_flashmessage(get_languageword('No Details Found'), 2);
			redirect(URL_HOME_BUY_BOOKS);
		}

		/* echo "<pre>";
print_r($this->session->userdata()); die("workinbg here"); */

		if (!empty($payment_gateway)) {
			$gateway_details = $this->session->userdata('gateway_details');
			$user_info = $this->base_model->get_user_details($this->ion_auth->get_user_id());
			$user_info = $user_info[0];
			$this->data['user_info'] = $user_info;
			$field_values = $this->db->get_where('system_settings_fields', array('type_id' => $payment_gateway))->result();
			$razorpay_key_id 			= 'rzp_test_tjwMzd8bqhZkMr';
			$razorpay_key_secret 		= 'EWI9VQiMH43p6LDCbpsgvvHZ';
			$razorpay_payment_action 	= 'capture';
			$razorpay_mode 				= 'sandbox';
			foreach ($field_values as $value) {
				if ($value->field_key == 'razorpay_key_id') {
					$razorpay_key_id = $value->field_output_value;
				}
				if ($value->field_key == 'razorpay_key_secret') {
					$razorpay_key_secret = $value->field_output_value;
				}
				if ($value->field_key == 'razorpay_payment_action') {
					$razorpay_payment_action = $value->field_output_value;
				}
				if ($value->field_key == 'razorpay_mode') {
					$razorpay_mode = $value->field_output_value;
				}
			}
			$book_name  = $record->book_name;
			$book_title = $record->book_title;
			$total_amount = $record->book_price;

			$config = array(
				'razorpay_key_id' 			=> $razorpay_key_id,
				'razorpay_key_secret' 		=> $razorpay_key_secret,
				'razorpay_payment_action' 	=> $razorpay_payment_action,
				'razorpay_mode' 			=> $razorpay_mode,
				'total_amount' 				=> $total_amount * 100, //As Razorpay accepts amount in paise
				'product_name' 				=> $book_name,
				'product_desc' 				=> $book_title,
				'firstname' 				=> $user_info->first_name,
				'lastname' 					=> $user_info->last_name,
				'email' 					=> $user_info->email,
				'phone' 					=> $user_info->phone,
				'success_url' 	=> base_url() . 'pay/payment_success',
				'cancel_url' 	=> base_url() . 'pay/payment_cancel',
				'failed_url' 	=> base_url() . 'pay/payment_success',
			);
			$site_logo = get_system_settings('Logo');
			if ($site_logo != '' && file_exists(URL_PUBLIC_UPLOADS . 'settings/thumbs/' . $site_logo)) {
				$config['image'] = URL_PUBLIC_UPLOADS2 . 'settings/thumbs/' . $site_logo;
			}
			$this->data['razorpay'] = $config;
			$content 	= 'checkout_razorpay';
			$pagetitle 	= get_languageword('checkout_with_Razorpay');
		} else {
			$gateway_details = $this->base_model->get_payment_gateways('', 'Active');
			$content 	= 'checkout';
			$pagetitle 	= get_languageword('checkout');
		}
		$this->data['record'] = $record;
		$this->data['payment_gateways'] = $gateway_details;

		$this->data['activemenu'] 	= "buy_books";
		$this->data['content'] 		= $content;
		$this->data['pagetitle'] 	= $pagetitle;
		$this->_render_page('template/site/site-template', $this->data);
	}

	/***************************
	05-12-2018
	 ****************************/
	function blogs($sellers = '')
	{
		$sellers = (!empty($sellers)) ? array($sellers) : $this->input->post('sellers');
		$params = array(
			'limit' => LIMIT_BLOG_LIST,
			'sellers' => $sellers
		);
		$this->data['blogs'] 	  = $this->home_model->get_seller_blogs($params);
		//total rows count
		unset($params['limit']);
		$total_records = count($this->home_model->get_seller_blogs($params));
		$this->data['total_records'] = $total_records;

		//sellers options
		$this->data['sellers_options'] = $this->home_model->get_blogs_sellers_options();
		$this->data['sellers'] 	 = $sellers;
		$this->data['activemenu'] 	= "blogs";
		$this->data['content'] 		= "blogs";
		$this->data['pagetitle'] 	= get_languageword('blogs');
		$this->_render_page('template/site/site-template', $this->data);
	}

	function load_more_blogs()
	{
		$limit   = $this->input->post('limit');
		$offset  = $this->input->post('offset');
		$sellers  = $this->input->post('sellers');

		$params = array(
			'start'	=> $offset,
			'limit' => $limit,
			'sellers' => $sellers
		);
		$blogs  		= $this->home_model->get_seller_blogs($params);
		$result 		= $this->load->view('sections/list_blogs', array('blogs' => $blogs), true);
		$data['result'] = $result;
		$data['offset'] = $offset + $limit;
		$data['limit']  = $limit;
		echo json_encode($data);
	}

	function view_blog($blog_id = '')
	{
		if (!$blog_id)
			redirect(URL_HOME_LIST_BLOGS);
		$blog_id = ($this->input->post('blog_id')) ? $this->input->post('blog_id') : $blog_id;
		if (empty($blog_id)) {
			$this->prepare_flashmessage(get_languageword('invalid_request'), 1);
			redirect(URL_HOME_LIST_BLOGS);
		}

		$blog_details = $this->home_model->get_blog_details($blog_id);
		if (empty($blog_details)) {
			$this->prepare_flashmessage(get_languageword('no_details_available'), 2);
			redirect(URL_HOME_LIST_BLOGS);
		}
		$this->data['blog_details'] = $blog_details;
		//user record
		$seller_record = getUserRec($blog_details->seller_id);
		$this->data['seller_record'] = $seller_record;
		$this->output->set_header("Cache-Control: post-check=0, pre-check=0");
		$this->output->set_header("Pragma: no-cache");
		$this->data['activemenu'] 	= "blogs";
		$this->data['content'] 		= 'blog_details';
		$this->data['pagetitle']	= get_languageword("view_blog") . ' ' . $blog_details->title;
		$this->_render_page('template/site/site-template', $this->data);
	}
	//add book
	function add_book($auto = false, $input)
	{
		$selling_book_slug = $this->session->userdata('selling_book_slug');
		//       if($this->base_model->fetch_records_from('book_purchases',['sc_id'=>$input['sc_id'],'user_id'=>$this->session->userdata('user_id')]))
		//       {
		//       	$this->prepare_flashmessage("You already have this book.", 1);
		// redirect(URL_HOME_BUY_BOOK.'/'.$selling_book_slug);
		//       }
		$success = 0;
		if ($this->session->userdata('book_purchase_data') && $this->session->userdata('is_valid_request')) {
			$input_data 		= $this->session->userdata('book_purchase_data');
			$record				= $this->session->userdata('selling_book_det');
			//$gateway_details 	= $this->session->userdata('gateway_details');
			if ($auto == 'auto') {
				$input['user_id'] = $this->session->userdata('user_id');
				$input_data['paid_date']      	= $input['paid_date'];
				$input_data['transaction_id']   = $input['user_id'] . rand(9999, 4);
				$input_data['paid_amount']   	= $input['total_amount'];
				$input_data['payer_id']      	= $input['user_id'];
				$input_data['user_id'] = $input['user_id'];
				$input_data['payer_email']      = $this->session->userdata('email');
				$input_data['payer_name']      	= $this->session->userdata('first_name') . " " . $this->session->userdata('last_name');
				$input_data['payment_status']   = "Completed";

				$input_data['admin_commission_percentage']  = $input['admin_commission_percentage'];
				$input_data['admin_commission_val']     	= $input['admin_commission_val'];
				//$this->input->post('payment_status'); Uncomment this for live
				//print_r($input_data);die;
				if ($input_data['payment_status'] == "Completed")
					$success = 1;
			} else {
				$this->prepare_flashmessage("Invalid Operation", 1);
				redirect(URL_HOME_BUY_BOOK . '/' . $selling_book_slug);
			}

			if ($success == 1) {
				$purchase_id = $this->base_model->insert_operation($input_data, 'book_purchases');
				if ($purchase_id > 0) {
					//05-12-2018 start
					//admin notification
					$purchased_rcrd = $this->base_model->get_query_result("SELECT c.*,s.book_name,t.username FROM pre_book_purchases c INNER JOIN pre_seller_selling_books s ON c.sc_id=s.sc_id INNER JOIN pre_users t ON c.seller_id=t.id WHERE c.purchase_id=" . $purchase_id . " ");
					if (!empty($purchased_rcrd)) {
						$purchased_rcrd = $purchased_rcrd[0];
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
								$original_vars  = array($logo_img, $site_title, $user_rec->username, $purchased_rcrd->book_name, date('Y-m-d'), $purchased_rcrd->book_name, $seller_rec->username, $currency . $purchased_rcrd->paid_amount, $purchased_rcrd->transaction_id, $purchased_rcrd->payer_id, $purchased_rcrd->payer_email, $purchased_rcrd->payer_name, $purchased_rcrd->payment_status);
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
								$original_vars  = array($logo_img, $site_title, $seller_rec->username, $user_rec->username, $purchased_rcrd->book_name, date('Y-m-d'), $purchased_rcrd->book_name, $currency . $purchased_rcrd->total_amount, $purchased_rcrd->admin_commission_percentage, $purchased_rcrd->admin_commission_val, $currency . $purchased_rcrd->paid_amount, $purchased_rcrd->transaction_id, $purchased_rcrd->payer_id, $purchased_rcrd->payer_email, $purchased_rcrd->payer_name, $purchased_rcrd->payment_status, $purchased_rcrd->status_of_payment_to_seller);
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
								$original_vars  = array($logo_img, $site_title, $user_rec->username, $purchased_rcrd->book_name, date('Y-m-d'), $purchased_rcrd->book_name, $seller_rec->username, $currency . $purchased_rcrd->total_amount, $purchased_rcrd->admin_commission_percentage, $purchased_rcrd->admin_commission_val, $currency . $purchased_rcrd->paid_amount, $purchased_rcrd->transaction_id, $purchased_rcrd->payer_id, $purchased_rcrd->payer_email, $purchased_rcrd->payer_name, $purchased_rcrd->payment_status, $purchased_rcrd->status_of_payment_to_seller);
								$temp_vars		= array('__SITE_LOGO__', '__SITE_TITLE__', '__BUYER_NAME__', '__BOOK_NAME__', '__SELLER_NAME__', '__PURCHASED_DATE__', '__BOOK_NAME__', '__TOTAL_AMOUNT__', '__PERCENT__', '__VALUE__', '__PAID_AMOUNT__', '__TRANSACTION_ID__', '__PAYER ID__', '__PAYER_EMAIL__', '__PAYER_NAME__', '__PAYMENT_STATUS__', '__PAYMENT_SELLER__');
								$msg = str_replace($temp_vars, $original_vars, $email_tpl->template_content);
								sendEmail($from, $to, $sub, $msg);
							}
						}
						//send email to admin end
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
								$zip_file_name = URL_PUBLIC_UPLOADS . 'book_curriculum_files/' . $dir.'.zip';

								$name = $sno . '.' . $value->title . '.' . $value->file_ext;

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

					$this->session->unset_userdata('is_valid_request');
					$this->session->unset_userdata('book_purchase_data');
					$this->session->unset_userdata('selling_book_slug');
					$this->session->unset_userdata('selling_book_det');
					//$this->session->unset_userdata('gateway_details');
					$this->prepare_flashmessage("You purchased Book Successfully", 0);
					redirect(URL_BUYER_BOOK_PURCHASES);
					//redirect(URL_HOME_BUY_BOOK.'/'.$selling_book_slug);
				} else {
					$this->prepare_flashmessage("Purchase Data Not Saved", 2);
					redirect(URL_HOME_BUY_BOOK . '/' . $selling_book_slug);
				}
			} else {
				$this->prepare_flashmessage("Purchase Data not saved due to some technical issue. Please contact Admin", 2);
				redirect(URL_HOME_BUY_BOOK . '/' . $selling_book_slug);
			}
		} else {
			$this->prepare_flashmessage("Invalid Operation", 1);
			redirect(URL_HOME_BUY_BOOKS);
		}
	}
	function do_likecomment()
	{
		$user_id = $this->input->post('user_id');
		$book_id = $this->input->post('book_id');
		$response = [];
		if ($user_id && $book_id) {
			$response['response'] = "success";
			$where_array = array('user_id' => $user_id, 'item_id' => $book_id);
			$this->db->where($where_array);
			$query = $this->db->get('pre_selling_book_likes');

			$count_row = $query->num_rows();
			if ($count_row > 0) {
				$ret = $query->row();
				$this->db->where('like_id', $ret->like_id);
				$this->db->delete('pre_selling_book_likes');
				$response['action'] = "remove";
				$response['message'] = "Unlike succesfully done";

				$count_row -= 1;
			} else {
				$data = array(
					'user_id' => $user_id,
					'item_id' => $book_id
				);
				$this->db->insert('pre_selling_book_likes', $data);
				//echo $this->db->last_query();
				$response['message'] = "Like succesfully placed";
				$response['action'] = "add";
				$count_row += 1;
			}


			$where_array = array('item_id' => $book_id);
			$this->db->where($where_array);
			$query = $this->db->get('pre_selling_book_likes');

			$count_row = $query->num_rows();

			$response['totalcount'] = $count_row;
			$response['response'] = "success";


			$pre_user_credit_transactionswhere_array = array('user_id' => $user_id, 'reference_id' => $book_id);
			$this->db->where($pre_user_credit_transactionswhere_array);
			$pre_user_credit_transactionsquery = $this->db->get('pre_user_credit_transactions');

			if ($pre_user_credit_transactionsquery->num_rows() > 0) {
				$response['message'] .= ", Points already added and cant earn again.";
			} else {
				$points = $this->config->item('site_settings')->point_system_likepoints;
				$this->home_model->addupdate_pointsystem($user_id, $book_id, "likes", $points);
			}
		} else {
			$response['response'] = "redirect";
		}
		header('Content-Type: application/json');
		echo json_encode($response);
	}
	function do_review()
	{
		$rating = $this->input->post("rating");
		$title = "review"; //$this->input->post("title")
		$review = $this->input->post("review");
		$user_id = $this->input->post("user_id");
		$item_id = $this->input->post("item_id");
		$response = [];
		if ($user_id && $item_id) {
			if ($rating == FALSE) {
				$response["response"] = "error";
				$response["message"] = "Please select stars";
			} else {
				$where_array = array('user_id' => $user_id, 'item_id' => $item_id);
				$this->db->where($where_array);
				$query = $this->db->get('pre_selling_book_rating');
				$count_row = $query->num_rows();
				$response["response"] = "success";
				$data = array(
					'user_id' => $user_id,
					'item_id' => $item_id,
					'rating_number' => $rating,
					'review' => $review,
					'title' => $title,
				);
				if ($count_row > 0) {
					$ret = $query->row();
					$this->db->where('rating_id', $ret->rating_id);
					$this->db->update('pre_selling_book_rating', $data);
					//echo $this->db->last_query();
					$response["message"] = "Your review successfully updated.";
				} else {

					$this->db->insert('pre_selling_book_rating', $data);
					$response["message"] = "Thankyou!! Your review successfully submitted.";
				}


				$pre_user_credit_transactionswhere_array = array('user_id' => $user_id, 'reference_id' => $book_id);
				$this->db->where($pre_user_credit_transactionswhere_array);
				$pre_user_credit_transactionsquery = $this->db->get('pre_user_credit_transactions');
				if ($pre_user_credit_transactionsquery->num_rows() > 0) {
					$response['message'] .= ", Points already added and cant earn again.";
				} else {
					$points = $this->config->item('site_settings')->point_system_likepoints;
					$this->home_model->addupdate_pointsystem($user_id, $item_id, "reviews", $points);
				}
			}
		} else {
			$response["response"] = "redirect";
			$response["message"] = "Please login to submit review.";
		}

		header('Content-Type: application/json');
		echo json_encode($response);
		die;
	}
	function view_list_reviewrating()
	{
		$book_id = $this->input->post("book_id");
		$average_rating = $this->input->post("average_rating");



		$where_array = array('item_id' => $book_id);



		$this->db->select('*');

		if ($average_rating) {

			//$this->db->select_sum('rating_number','total_rating');
			$this->db->select('(SELECT  SUM(`rating_number`) FROM `pre_selling_book_rating` WHERE `item_id` = ' . $book_id . ' LIMIT 1) AS `total_rating` ');
		}

		$this->db->from('pre_selling_book_rating');
		$this->db->where($where_array);
		$this->db->join('pre_users', 'pre_selling_book_rating.user_id = pre_users.id');
		$this->db->group_by('rating_id');
		$this->db->order_by('username', 'asc');

		$rowlistquery = $this->db->get();

		//echo $this->db->last_query();die;
		$generate_html = "";

		$response = [];

		$response["response"] = "success";
		if ($rowlistquery->num_rows() > 0) {
			$generate_html = '';
			foreach ($rowlistquery->result_array() as $result) {

				//var_dump($result);die("working here");

				$generate_html .= '<div class="rating-wraper d-flex flex-row ">';

				if ($result["first_name"]) {

					$generate_html .= '<div class="username">' . $result["first_name"] . ' ' . $result["last_name"] . '</div>';
				} else {
					$generate_html .= '<div class="username">' . $result["username"] . '</div>';
				}

				$generate_html .= '<div class="rating">';
				for ($i = 5; $i >= 1; $i--) {
					$generate_html .= '<label class="' . ($i <= $result["rating_number"] ? "checked" : "") . '" ></label>';
				}

				$generate_html .= '</div>';
				$generate_html .= '<div class="rating_number">' . number_format($result["rating_number"], 1) . '</div>';
				$generate_html .= '<div class="review">	' . $result["review"] . '</div></div>';
			}
		}
		$response["average_rating"] = 0;
		if ($average_rating) {
			if ($rowlistquery->row()->total_rating > 0 && $rowlistquery->num_rows() > 0) {
				$response["average_rating"] = number_format(($rowlistquery->row()->total_rating / $rowlistquery->num_rows()), 1, '.', '');
				$average_rating_html = '<div class="rating"><div class="pt-2"><span class="ml-1">(' . $rowlistquery->num_rows() . ')</span></div>';
				for ($i = 5; $i >= 1; $i--) {
					$average_rating_html .= '<label class="' . ($i <= $response["average_rating"] ? "checked" : "") . '" ></label>';
				}

				$average_rating_html .= '<div class="pt-2"><span class="ml-1">' . $response["average_rating"] . '</span></div></div>';
				$response["average_rating_html"] = $average_rating_html;
				$response["total_comments"] = $rowlistquery->num_rows();
			}
		}

		//total_comments
		/*echo "total_rating".$rowlistquery->row()->total_rating;
		echo "total_rating count".$rowlistquery->num_rows();
		var_dump($response);die;
		*/



		$response["response_list"] = $generate_html;
		header('Content-Type: application/json');
		echo json_encode($response);
		die;

		die($book_id . "working here");
	}

	function do_sharerpointcredit()
	{


		$user_id = $this->input->post("user_id");
		$item_id = $this->input->post("item_id");
		$datanetwork = $this->input->post("datanetwork");

		$response["success"] = 0;

		$pre_user_credit_transactionswhere_array = array('user_id' => $user_id, 'purpose' => 'share', 'reference_id' => $item_id);
		$this->db->where($pre_user_credit_transactionswhere_array);
		$pre_user_credit_transactionsquery = $this->db->get('pre_user_credit_transactions');

		if ($pre_user_credit_transactionsquery->num_rows() > 0) {
			//$response['message'] .= ", Points already added and cant earn again.";
		} else {
			$points = $this->config->item('site_settings')->point_system_sharepoints;
			$this->home_model->addupdate_pointsystem($user_id, $item_id, "share_" . $datanetwork, $points);
			$response["success"] = 1;
		}


		header('Content-Type: application/json');
		return json_encode($response);
	}
}
