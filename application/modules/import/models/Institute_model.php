<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

class Institute_model extends Base_Model  
{
	var $numrows;

	function __construct()
	{
		parent::__construct();
	}
	
		
	/****** GET LOCATIONS	
	* Author @
	* Adi
	******/
	function get_locations()
	{
		
		$locations = array();
		
		$parentLocationDetails = $this->db->select('id AS parentLocation_id, location_name AS parentLocation_name')->get_where($this->db->dbprefix( 'locations' ), array('parent_location_id' => 0, 'status' => 'Active'))->result();
		
		foreach($parentLocationDetails as $p) {
		
			$query = "SELECT l . * , (

					SELECT count( * )
					FROM ".$this->db->dbprefix( 'inst_locations' )." tl,
					 ".$this->db->dbprefix( 'users' )." u,
					 ".$this->db->dbprefix( 'users_groups' )." ug
					WHERE (tl.location_id = l.id OR 
					u.location_id = l.id) 
					AND ug.group_id = 3
					AND ug.user_id = u.id
					AND u.id = tl.inst_id
					AND u.active = 1
					AND tl.status = '1'
					) AS no_of_institutes
					FROM ".$this->db->dbprefix( 'locations' )." l
					WHERE l.parent_location_id = ".$p->parentLocation_id."
					AND l.status = 'active'";
			
			$childLocations = $this->db->query($query)->result();
			
			$locations[$p->parentLocation_name] = $childLocations;		
		}

		return $locations;
	
	}
	
	/****** GET SELLER LOCATION IDs
	* Author @
	*Raghu
	******/
	function get_institute_location_ids($inst_id = null)
	{
	
		$instituteLocationIds = array();
		
		if($inst_id != null && is_numeric($inst_id)) {
		
			$instituteLocationsRec = $this->db->select('location_id')->get_where($this->db->dbprefix( 'inst_locations' ), array('inst_id' => $inst_id, 'status' => '1'))->result();
				
			foreach($instituteLocationsRec as $l)
				array_push($instituteLocationIds, $l->location_id);
		}
		
		return $instituteLocationIds;
	
	}
	
	/****** GET SELLER LOCATIONS	
	* Author @
	* Adi
	******/
	function get_seller_locations($seller_id = null)
	{		
		$sellerLocations = array();
		$sellerLocationsArr = array();
		
		if($seller_id != null && is_numeric($seller_id)) {
		
			$sellerLocationsRec = $this->db->select('location_id')->get_where($this->db->dbprefix( 'seller_locations' ), array('seller_id' => $seller_id, 'status' => '1'))->result();
			
			foreach($sellerLocationsRec as $l)
				array_push($sellerLocationsArr, $l->location_id);
		
			$parentLocationDetails = $this->db->select('id AS parentLocation_id, location_name AS parentLocation_name')->get_where($this->db->dbprefix( 'locations' ), array('parent_location_id' => 0, 'status' => 'Active'))->result();
			
			foreach($parentLocationDetails as $p) {
			
				$childLocations = $this->db->query("SELECT * FROM ".$this->db->dbprefix( 'locations' )." WHERE parent_location_id = ".$p->parentLocation_id." AND id IN (".implode(',', $sellerLocationsArr).") AND status='Active'")->result();
				
				if(count($childLocations) > 0)
					$sellerLocations[$p->parentLocation_name] = $childLocations;		
			}
		}

		return $sellerLocations;
	
	}
	
	/****** GET INSTITUTE Teaching type IDs
	* Author @
	* Adi
	******/
	function get_institute_selected_teachingtype_ids($inst_id = null)
	{
	
		$instituteSelectedTypeIds = array();
		
		if($inst_id != null && is_numeric($inst_id)) {
		
			$instituteSelectedTypesRec = $this->db->select('teaching_type_id')->get_where($this->db->dbprefix( 'inst_teaching_types' ), array('inst_id' => $inst_id, 'status' => '1'))->result();
				
			foreach($instituteSelectedTypesRec as $t)
				array_push($instituteSelectedTypeIds, $t->teaching_type_id);
		}
		
		return $instituteSelectedTypeIds;
	
	}
	
	/**
	 * Get seller types
	 *
	 * @access	public
	 * @param	void
	 * @return	mixed
	 */
	function get_institute_teachingtypes()
	{		
		$instituteTypes = $this->db->get_where($this->db->dbprefix( 'teaching_types' ), array('status'=>'1','teaching_type!='=>'home'))->result();		
		return $instituteTypes;	
	}

	/**
	 * Get Institute offered books
	 *
	 * @access	public
	 * @param	void
	 * @return	mixed
	 */


	function get_institute_offered_book_ids($inst_id = null)
	{
	
		$instituteOfferedBookIds = array();
		
		if($inst_id != null && is_numeric($inst_id)) {
		
			$instituteOfferedBookRec = $this->db->select('book_id')->get_where($this->db->dbprefix( 'inst_offered_books' ), array('inst_id' => $inst_id, 'status' => '1'))->result();
				
			foreach($instituteOfferedBookRec as $l)
				array_push($instituteOfferedBookIds, $l->book_id);
		}
		
		return $instituteOfferedBookIds;
	
	}

	function get_institute_offered_book($inst_id = null)
	{

		$inst_id = (!empty($inst_id)) ? $inst_id : $this->ion_auth->get_user_id();

		if(empty($inst_id))
			return array();

		$inst_offered_book_opts = array('' => get_languageword('no_books_available'));

		$query = "SELECT ic.book_id, c.name FROM ".$this->db->dbprefix('inst_offered_books')." ic INNER JOIN ".$this->db->dbprefix('categories')." c ON c.id=ic.book_id WHERE ic.inst_id=".$inst_id." GROUP BY ic.book_id ORDER BY c.name";

		$inst_offered_books = $this->db->query($query)->result();

		if(!empty($inst_offered_books)) {

			$inst_offered_book_opts = array('' => get_languageword('select_book'));

			foreach ($inst_offered_books as $key => $value)
				$inst_offered_book_opts[$value->book_id] = $value->name;
		}

		return $inst_offered_book_opts;
	}
		
	

	
	/**
	 * Get Institute Packages
	 *
	 * @access	public
	 * @param	void
	 * @return	mixed
	 */
	function list_institute_packages()
	{		
		$query = "select * from " . $this->db->dbprefix('packages') . " 
		where status = 'Active' AND (package_for='All' OR package_for='Institute')";
		$packages = $this->db->query($query)->result();
		return $packages;	
	}

	function get_sellers($inst_id)
	{

		$sellers = array();

		if($inst_id != null && is_numeric($inst_id)) {

			$where = array('active' => 1, 'parent_id' => $inst_id, 'availability_status' => '1');

			$sellers = $this->db->select('*')->get_where($this->db->dbprefix( 'users' ), $where)->result();
		}

		return $sellers;
	}

	
	function get_batches_by_book($book_id = "", $inst_id = "")
	{
		$inst_id = (!empty($inst_id)) ? $inst_id : $this->ion_auth->get_user_id();

		if(empty($book_id) || empty($inst_id))
			return array();

		$today = date('Y-m-d');

		$batches = $this->db->select('*')->get_where($this->db->dbprefix( 'inst_batches' ), array('book_id' => $book_id, 'inst_id' => $inst_id, 'batch_end_date >' => $today))->result();

		return $batches;
	}



	function get_inst_locations($inst_id = "")
	{
		if(empty($inst_id))
			return array();

		$query = "SELECT l.location_name FROM ".$this->db->dbprefix('inst_locations')." il INNER JOIN ".TBL_LOCATIONS." l ON l.id=il.location_id WHERE l.status='Active' AND il.status=1 AND il.inst_id=".$inst_id." ";
		$rs = $this->db->query($query);

		return ($rs->num_rows() > 0) ? $rs->result() : array();
	}

	function get_inst_dashboard_data($inst_id = "")
	{

		 $inst_dashboard_data = array();

		$query = "select count(*) num_of_sellers from ".$this->db->dbprefix('users')." where parent_id = ".$inst_id;
		$inst_dashboard_data['num_of_sellers'] = $this->db->query($query)->row()->num_of_sellers;

		$query1= "select count(*) batches from ".$this->db->dbprefix('inst_batches')." where inst_id = ".$inst_id; 
		$inst_dashboard_data['batches'] =  $this->db->query($query1)->row()->batches;
	
		$query2 = "SELECT count(Distinct book_id) books FROM ". $this->db->dbprefix('inst_offered_books') ." ic INNER JOIN ".$this->db->dbprefix('categories')." c ON c.id=ic.book_id WHERE ic.inst_id=".$inst_id;
		
		$inst_dashboard_data['books'] =  $this->db->query($query2)->row()->books;
	
		return $inst_dashboard_data;
	}

	function get_batch_enrolled_buyers_cnt($batch_id = '')
	{
		if(empty($batch_id))
			return 0;

		$enrolled_buyers = $this->base_model->fetch_records_from('inst_enrolled_buyers', array('batch_id' => $batch_id, 'status !=' => 'cancelled_before_book_started'));

		return count($enrolled_buyers);

	}


	function get_batch_status($batch_id = "")
	{
		if(empty($batch_id))
			return NULL;

		$is_pending_rec_exist = $this->db->select('status')
										 ->get_where(
										 				$this->db->dbprefix('inst_enrolled_buyers'), 
										 				array(
										 						'batch_id' => $batch_id, 
										 						'status' => 'pending'
										 					 )
										 			)
										 ->result();

		if(count($is_pending_rec_exist)) {

			return 'pending';

		} else {

			$query = "SELECT status , COUNT( 
				 status ) AS max_occurred
				 FROM  ".TBL_INST_ENROLLED_BUYERS." 
				 WHERE batch_id =".$batch_id." 
				 AND status != 'cancelled_before_book_started' 
				 AND status != 'called_for_admin_intervention' 
				 AND status != 'pending'
				 GROUP BY status ORDER BY max_occurred DESC 
				 LIMIT 1";
			$batch_status = $this->db->query($query)->row();

			if(!empty($batch_status))
				return $batch_status->status;
			else
				return 'pending';
		}

	}


	function get_credits_of_batch_closed($batch_id = "")
	{
		if(empty($batch_id))
			return 0;

		$query = "SELECT (
					SUM( fee ) - SUM( admin_commission_val )
					) AS total_credits_of_batch_closed
					FROM  ".TBL_INST_ENROLLED_BUYERS." 
					WHERE batch_id =".$batch_id."

					AND STATUS =  'closed'";

		$row = $this->db->query($query)->row();

		if(!empty($row))
			return $row->total_credits_of_batch_closed;
		else
			return 0;
	}



	function get_admin_commission_credits_of_batch_closed($batch_id = "")
	{
		if(empty($batch_id))
			return 0;

		$query = "SELECT (
					SUM( admin_commission_val )
					) AS total_admin_commission_credits_of_batch_closed
					FROM  ".TBL_INST_ENROLLED_BUYERS." 
					WHERE batch_id =".$batch_id."

					AND STATUS =  'closed'";

		$row = $this->db->query($query)->row();

		if(!empty($row))
			return $row->total_admin_commission_credits_of_batch_closed;
		else
			return 0;
	}




}

?>