<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

class Buyer_model extends CI_Model  
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
					FROM ".$this->db->dbprefix( 'buyer_locations' )." tl,
					 ".$this->db->dbprefix( 'users' )." u,
					 ".$this->db->dbprefix( 'users_groups' )." ug
					WHERE (tl.location_id = l.id OR 
					u.location_id = l.id) 
					AND ug.group_id = 3
					AND ug.user_id = u.id
					AND u.id = tl.buyer_id
					AND u.active = 1
					AND tl.status = '1'
					) AS no_of_buyer
					FROM ".$this->db->dbprefix( 'locations' )." l
					WHERE l.parent_location_id = ".$p->parentLocation_id."
					AND l.status = 'active'";
			
			$childLocations = $this->db->query($query)->result();
			
			$locations[$p->parentLocation_name] = $childLocations;		
		}

		return $locations;
	
	}

	function get_books()
	{
		
		$books = array();
		
		$parentBookDetails = $this->db->select('id AS parentBook_id, name AS parentBook_name')->get_where($this->db->dbprefix( 'categories' ), array('is_parent' => 1))->result();
		
		foreach($parentBookDetails as $p) {
		
			
			
			$books[$p->parentBook_name] = $p->parentBook_id;		
		}

		return $books;
	
	}
	
	/****** GET SELLER LOCATION IDs
	* Author @
	*Raghu
	******/
	function get_buyer_location_ids($buyer_id = null)
	{
	
		$studnentLocationIds = array();
		
		if($buyer_id != null && is_numeric($buyer_id)) {
		
			$buyerLocationsRec = $this->db->select('location_id')->get_where($this->db->dbprefix( 'buyer_locations' ), array('buyer_id' => $buyer_id, 'status' => '1'))->result();
				
			foreach($buyerLocationsRec as $l)
				array_push($studnentLocationIds, $l->location_id);
		}
		
		return $studnentLocationIds;
	
	}



	function get_buyer_preffered_book_ids($buyer_id = null)
	{
	
		$studnentPrefferedBookIds = array();
		
		if($buyer_id != null && is_numeric($buyer_id)) {
		
			$studnentPrefferedBookRec = $this->db->select('book_id')->get_where($this->db->dbprefix( 'buyer_preffered_books' ), array('buyer_id' => $buyer_id, 'status' => '1'))->result();
				
			foreach($studnentPrefferedBookRec as $l)
				array_push($studnentPrefferedBookIds, $l->book_id);
		}
		
		return $studnentPrefferedBookIds;
	
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
	function get_buyer_selected_teachingtype_ids($buyer_id = null)
	{
	
		$buyerSelectedTypeIds = array();
		
		if($buyer_id != null && is_numeric($buyer_id)) {
		
			$buyerSelectedTypesRec = $this->db->select('teaching_type_id')->get_where($this->db->dbprefix('buyer_prefferd_teaching_types' ), array('buyer_id' => $buyer_id, 'status' => '1'))->result();
				
			foreach($buyerSelectedTypesRec as $t)
				array_push($buyerSelectedTypeIds, $t->teaching_type_id);
		}
		
		return $buyerSelectedTypeIds;
	
	}
	
	/**
	 * Get seller types
	 *
	 * @access	public
	 * @param	void
	 * @return	mixed
	 */
	function get_teachingtypes()
	{		
		$TeachingTypes = $this->db->get_where($this->db->dbprefix( 'teaching_types' ), array('status' => '1'))->result();		
		return $TeachingTypes;	
	}
	
	/**
	 * Get seller types
	 *
	 * @access	public
	 * @param	void
	 * @return	mixed
	 */
	function list_buyer_packages()
	{		
		$query = "select * from " . $this->db->dbprefix('packages') . " 
		where status = 'Active' AND (package_for='All' OR package_for='buyer')";
		$packages = $this->db->query($query)->result();
		return $packages;	
	}

	function get_buyer_dashboard_data($buyer_id = "")
	{

		 $buyer_dashboard_data = array();

		$query = "select count(*) books from ".$this->db->dbprefix('book_purchases')." where user_id=".$buyer_id;
		$buyer_dashboard_data['books'] = $this->db->query($query)->row()->books;		

		return $buyer_dashboard_data;
	}


	function update_buyer_book_downloads($buyer_id = "", $purchase_id = "")
	{
		if(!($buyer_id > 0) || !($purchase_id > 0))
			return false;

		$query = "UPDATE ".TBL_PREFIX."book_purchases SET total_downloads= total_downloads+1 WHERE user_id=".$buyer_id." AND purchase_id=".$purchase_id." AND payment_status='Completed' ";

		$this->db->query($query);

		return true;
	}


	function update_seller_book_downloads($seller_id = "", $sc_id = "")
	{
		if(!($seller_id > 0) || !($sc_id > 0))
			return false;

		$query = "UPDATE ".TBL_PREFIX."seller_selling_books SET total_downloads= total_downloads+1 WHERE seller_id=".$seller_id." AND sc_id=".$sc_id." ";

		$this->db->query($query);

		return true;
	}


	function update_seller_book_purchases($seller_id = "", $sc_id = "")
	{
		if(!($seller_id > 0) || !($sc_id > 0))
			return false;

		$query = "UPDATE ".TBL_PREFIX."seller_selling_books SET total_purchases= total_purchases+1 WHERE seller_id=".$seller_id." AND sc_id=".$sc_id." ";

		$this->db->query($query);

		return true;
	}





}

?>