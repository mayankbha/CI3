<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

class Seller_model extends Base_Model  
{
	var $numrows;
	function __construct()
	{
		parent::__construct();
	}
	
	/****** GET SUBJECTS	
	* Author @
	* Adi
	******/
	function get_subjects()
	{		
		$subjects = array();		
		$parentSubjectDetails = $this->db->select('id AS parentSubject_id, subject_name AS parentSubject_name')->get_where($this->db->dbprefix( 'subjects' ), array('subject_parent_id' => 0, 'status' => 'Active'))->result();
		
		foreach($parentSubjectDetails as $p) {		
			$query = "SELECT s.*, (SELECT count(*) FROM ".$this->db->dbprefix( 'seller_subjects' )." ts WHERE ts.subject_id = s.id AND ts.status = 'Active') AS no_of_sellers FROM ".$this->db->dbprefix( 'subjects' )." s 	WHERE s.subject_parent_id = ".$p->parentSubject_id." AND s.status = 'active'";			
			$childSubjects = $this->db->query($query)->result();			
			$subjects[$p->parentSubject_name] = $childSubjects;		
		}
		return $subjects;	
	}
	
	/****** GET SELLER SUBJECT IDs
	* Author @
	* Adi
	******/
	function get_seller_subject_ids($seller_id = null)
	{
	
		$sellerSubjectIds = array();
		
		if($seller_id != null && is_numeric($seller_id)) {
		
			$sellerSubjectsRec = $this->db->select('subject_id')->get_where($this->db->dbprefix( 'seller_subjects' ), array('user_id' => $seller_id, 'status' => 'Active'))->result();
				
			foreach($sellerSubjectsRec as $t)
				array_push($sellerSubjectIds, $t->subject_id);
		}
		
		return $sellerSubjectIds;
	
	}
	
	/****** GET SELLER SUBJECTS	
	* Author @
	* Adi
	******/
	function getSellerSubjects($seller_id = null)
	{		
		$sellerSubjects = array();
		$sellerSubjectsArr = array();
		
		if($seller_id != null && is_numeric($seller_id)) {
		
			$sellerSubjectsRec = $this->db->select('subject_id')->get_where($this->db->dbprefix( 'seller_subjects' ), array('user_id' => $seller_id, 'status' => 'Active'))->result();
			
			foreach($sellerSubjectsRec as $t)
				array_push($sellerSubjectsArr, $t->subject_id);
		
			$parentSubjectDetails = $this->db->select('id AS parentSubject_id, subject_name AS parentSubject_name')->get_where($this->db->dbprefix( 'subjects' ), array('subject_parent_id' => 0, 'status' => 'Active'))->result();
			
			foreach($parentSubjectDetails as $p) {
			
				$childSubjects = $this->db->query("SELECT * FROM ".$this->db->dbprefix( 'subjects' )." WHERE subject_parent_id = ".$p->parentSubject_id." AND id IN (".implode(',', $sellerSubjectsArr).") AND status='Active'")->result();
				
				if(count($childSubjects) > 0)
					$sellerSubjects[$p->parentSubject_name] = $childSubjects;		
			}
		}

		return $sellerSubjects;
	
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
					FROM ".$this->db->dbprefix( 'seller_locations' )." tl,
					 ".$this->db->dbprefix( 'users' )." u,
					 ".$this->db->dbprefix( 'users_groups' )." ug
					WHERE (tl.location_id = l.id OR 
					u.location_id = l.id) 
					AND ug.group_id = 3
					AND ug.user_id = u.id
					AND u.id = tl.seller_id
					AND u.active = 1
					AND tl.status = '1'
					) AS no_of_sellers
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
	function get_seller_location_ids($seller_id = null)
	{
	
		$sellerLocationIds = array();
		
		if($seller_id != null && is_numeric($seller_id)) {
		
			$sellerLocationsRec = $this->db->select('location_id')->get_where($this->db->dbprefix( 'seller_locations' ), array('seller_id' => $seller_id, 'status' => '1'))->result();
				
			foreach($sellerLocationsRec as $l)
				array_push($sellerLocationIds, $l->location_id);
		}
		
		return $sellerLocationIds;
	
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
	
	/****** GET SELLER Teaching type IDs
	* Author @
	* Adi
	******/
	function get_seller_selected_teachingtype_ids($seller_id = null)
	{
	
		$sellerSelectedTypeIds = array();
		
		if($seller_id != null && is_numeric($seller_id)) {
		
			$sellerSelectedTypesRec = $this->db->select('teaching_type_id')->get_where($this->db->dbprefix( 'seller_teaching_types' ), array('seller_id' => $seller_id, 'status' => '1'))->result();
				
			foreach($sellerSelectedTypesRec as $t)
				array_push($sellerSelectedTypeIds, $t->teaching_type_id);
		}
		
		return $sellerSelectedTypeIds;
	
	}
	
	/**
	 * Get seller types
	 *
	 * @access	public
	 * @param	void
	 * @return	mixed
	 */
	function get_seller_teachingtypes()
	{		
		$sellerTypes = $this->db->get_where($this->db->dbprefix( 'teaching_types' ), array('status' => '1'))->result();		
		return $sellerTypes;	
	}
	
	/**
	 * Get seller types
	 *
	 * @access	public
	 * @param	void
	 * @return	mixed
	 */
	function list_seller_packages()
	{		
		$query = "select * from " . $this->db->dbprefix('packages') . " 
		where status = 'Active' AND (package_for='All' OR package_for='Seller')";
		$packages = $this->db->query($query)->result();
		return $packages;	
	}

	

	function get_seller_assigned_book($user_id = null, $inst_id=null)
	{
		
		$user_id = (!empty($user_id)) ? $user_id : $this->ion_auth->get_user_id();
		$inst_id = (!empty($inst_id)) ? $inst_id : is_inst_seller();

		if(empty($user_id) || empty($inst_id))
			return array();

		$seller_assigned_books_opts = array('' => get_languageword('no_books_available'));

		$query = "SELECT ib.book_id, c.name FROM ".$this->db->dbprefix('inst_batches')." ib INNER JOIN ".$this->db->dbprefix('categories')." c ON c.id=ib.book_id WHERE ib.inst_id=".$inst_id." AND ib.seller_id=".$user_id." GROUP BY ib.book_id ORDER BY c.name";

		$seller_assigned_books = $this->db->query($query)->result();

		if(!empty($seller_assigned_books)) {

			$seller_assigned_books_opts = array('' => get_languageword('select_book'));

			foreach ($seller_assigned_books as $key => $value)
				$seller_assigned_books_opts[$value->book_id] = $value->name;
		}

		return $seller_assigned_books_opts;
	}


	function get_seller_dashboard_data($seller_id = "")
	{

		$seller_dashboard_data = array();

		$query = "select count(*) books from ".$this->db->dbprefix('seller_selling_books')." where seller_id=".$seller_id;
		$seller_dashboard_data['books'] = $this->db->query($query)->row()->books;		

		return $seller_dashboard_data;
	}
	
	function get_inst_seller_dashboard($seller_id=" ")
	{
		$query = "SELECT u.username as inst_name, c.name books ,count(ib.book_id) batches FROM ".$this->db->dbprefix('inst_batches')." ib join ".$this->db->dbprefix('users')." u on ib.inst_id=id join ".$this->db->dbprefix('categories')." c on ib.book_id= c.id  where seller_id=".$seller_id." group by ib.book_id,ib.inst_id";
		
		$get_inst_seller_dashboard = $this->db->query($query)->result();

		return $get_inst_seller_dashboard;
	}
	
	function get_blogs($seller_id='')
	{
		$cond="";
		if(!empty($seller_id)) {
			$cond .= " WHERE b.seller_id=".$seller_id." ";
		} else {
			$cond .= " WHERE u.active=1 ";
		}
		
		$blogs = array();
		
		$query = "SELECT b.*,u.username FROM ".$this->db->dbprefix('seller_blogs')." b INNER JOIN ".$this->db->dbprefix('users')." u ON b.seller_id=u.id  ";

		$blogs = $this->base_model->get_query_result($query);
		
		return $blogs;
	}
}

?>