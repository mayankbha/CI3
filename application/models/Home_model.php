<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Home_Model extends CI_Model
{
    var $numrows;

    function __construct()
    {
        parent::__construct();
    }


    //General database operations	
    function run_query($query)
    {
        $rs = $this->db->query($query);
        return $rs or die('Error:' . mysql_error());
    }

    function count_records($table, $condition = '')
    {
        if (!(empty($condition)))
            $this->db->where($condition);
        $this->db->from($this->db->dbprefix($table));
        $reocrds = $this->db->count_all_results();
        //echo $this->db->last_query();
        return $reocrds;
    }


    function fetch_records_from($table, $condition = '', $select = '*', $order_by = '', $like = '', $offset = '', $perpage = '')
    {
        $this->db->start_cache();
        $this->db->select($select, FALSE);
        $this->db->from($this->db->dbprefix($table));
        if (!empty($condition))
            $this->db->where($condition);
        if (!empty($like))
            $this->db->like($like);
        if (!empty($order_by))
            $this->db->order_by($order_by);
        $this->db->stop_cache();
        $result = $this->db->get();
        $this->numrows = $this->db->affected_rows();
        //echo $this->numrows.'<br>';
        if ($perpage != '')
            $this->db->limit($perpage, $offset);
        $result = $this->db->get();
        //print_r($result);die();
        $this->db->flush_cache();
        return $result->result();
    }



    function get_categoryid_by_slug($category_slug)
    {
        if (empty($category_slug))
            return 0;

        $column_to_select = 'id';
        if (is_array($category_slug) || $category_slug instanceof Traversable) {

            $column_to_select = 'GROUP_CONCAT(id) AS id';
        }
        $result_set = $this->db->select($column_to_select)
            ->where_in('slug', $category_slug)
            ->get(TBL_CATEGORIES);

        return ($result_set->num_rows() > 0) ? $result_set->row()->id : 0;
    }



    function get_locationid_by_slug($location_slug)
    {
        if (empty($location_slug))
            return 0;

        $column_to_select = 'id';
        if (is_array($location_slug) || $location_slug instanceof Traversable) {

            $column_to_select = 'GROUP_CONCAT(id) AS id';
        }
        $result_set = $this->db->select($column_to_select)
            ->where_in('slug', $location_slug)
            ->get(TBL_LOCATIONS);

        return ($result_set->num_rows() > 0) ? $result_set->row()->id : 0;
    }



    function get_teachingtypeid_by_slug($teaching_type_slug)
    {
        if (empty($teaching_type_slug))
            return 0;

        $column_to_select = 'id';
        if (is_array($teaching_type_slug) || $teaching_type_slug instanceof Traversable) {

            $column_to_select = 'GROUP_CONCAT(id) AS id';
        }
        $result_set = $this->db->select($column_to_select)
            ->where_in('slug', $teaching_type_slug)
            ->get(TBL_TEACHING_TYPES);

        return ($result_set->num_rows() > 0) ? $result_set->row()->id : 0;
    }


    function get_categoryname_by_slug($category_slug)
    {
        if (empty($category_slug))
            return NULL;

        $result_set = $this->db->select('name')
            ->get_where(
                TBL_CATEGORIES,
                array('slug' => $category_slug)
            );

        return ($result_set->num_rows() > 0) ? $result_set->row()->name : 0;
    }



    //GET CATEGORY-WISE POPULAR BOOKS
    function get_popular_books($category_limit = '', $book_limit = '', $is_popular = true)
    {
        $records             = array();
        $categories         = array();
        $books             = array();
        $book_limit_cond     = "";
        $cnt                 = 0;
        $is_books_found    = 0;
        $is_popular_cond    = "";

        if ($is_popular)
            $is_popular_cond = ' AND books.is_popular=1 ';


        if ($category_limit > 0)
            $this->db->limit($category_limit);

        $categories = $this->db->order_by('sort_order', 'ASC')
            ->get_where(
                TBL_CATEGORIES,
                array(
                    'is_parent' => 1,
                    'status' => 1
                )
            )
            ->result();

        if (empty($categories))
            return $records;

        if ($book_limit > 0)
            $book_limit_cond = ' LIMIT ' . $book_limit;

        foreach ($categories as $record) {

            $query   = "SELECT books.* FROM " . TBL_BOOK_CATEGORIES . " cc 
						INNER JOIN " . TBL_CATEGORIES . " books ON books.id=cc.book_id 
						WHERE cc.category_id=" . $record->id . " AND books.is_parent=0 
						AND books.status=1 " . $is_popular_cond . " 
						ORDER BY books.sort_order ASC " . $book_limit_cond . " ";

            $books = $this->db->query($query)->result();

            if (!empty($books)) {

                $is_books_found = 1;

                foreach ($books as $record1) {

                    $records[$record->id . "_" . $record->slug . "_" . $record->name][] = $record1->id . "_" . $record1->slug . "_" . $record1->name;
                }
            }
        }

        if ($is_books_found == 1)
            return $records;
        else
            return array();
    }



    /* GET ONLY CATEGORIES WHICH HAS BOOKS */
    function get_categories($params = array())
    {
        $limit_cond = "";
       
        if (!empty($params['start']) && !empty($params['limit']) && $params['start'] >= 0 && $params['limit'] >= 0) {
           
            $limit_cond = ' LIMIT ' . $params['start'] . ', ' . $params['limit'];
        } elseif (empty($params['start']) && !empty($params['limit']) && $params['limit'] >= 0) {

            $limit_cond = ' LIMIT ' . $params['limit'];
        }

        // $query = "SELECT cat.* FROM ".TBL_CATEGORIES." cat 
        // 		  WHERE is_parent=1 AND status=1 AND 
        // 		  EXISTS (SELECT * FROM ".TBL_BOOK_CATEGORIES." cc WHERE cc.category_id=cat.id) 
        // 		  ORDER BY cat.sort_order ASC ".$limit_cond." ";

        // $query = "SELECT cat.* FROM ".TBL_CATEGORIES." cat 
        //           WHERE is_parent=1 AND status=1 AND 
        //           EXISTS (SELECT * FROM ".TBL_BOOK_CATEGORIES." cc WHERE cc.category_id=cat.id) 
        //           ORDER BY cat.sort_order ASC ";

        // $query = "SELECT cat.* FROM ".TBL_CATEGORIES." cat 
        //           WHERE is_parent=1 AND status=1 AND 
        //           EXISTS (SELECT * FROM ".TBL_BOOK_CATEGORIES." cc WHERE cc.category_id=cat.id) 
        //           ORDER BY cat.sort_order ASC ";

        // $query = "SELECT cat.* FROM ".TBL_CATEGORIES." cat 
        //           WHERE is_parent=1 AND status=1 
        //           ORDER BY cat.sort_order ASC ";

       
if($limit_cond){
    $categories = $this->db->select('cat.*,count(bs.sc_id)as book_count')->from('pre_categories as cat')->join('pre_seller_selling_books as bs', 'bs.category_id=cat.id')->where('bs.admin_approved', 'Yes')->where('cat.is_parent', 1)->where('cat.status', 1)->group_by('cat.id')->order_by('cat.sort_order', 'ASC')->limit($limit_cond)->get()->result();
}else {
    $categories = $this->db->select('cat.*,count(bs.sc_id)as book_count')->from('pre_categories as cat')->join('pre_seller_selling_books as bs', 'bs.category_id=cat.id')->where('bs.admin_approved', 'Yes')->where('cat.is_parent', 1)->where('cat.status', 1)->group_by('cat.id')->order_by('cat.sort_order', 'ASC')->get()->result();
}
        

        return $categories;
    }



    /* GET BOOKS */
    function get_books($params = array())
    {

        $query      = "";
        $limit_cond = "";
        $order_cond = " ORDER BY books.sort_order ASC";


        if (!empty($params['start']) && !empty($params['limit']) && $params['start'] >= 0 && $params['limit'] >= 0) {

            $limit_cond = ' LIMIT ' . $params['start'] . ', ' . $params['limit'];
        } elseif (empty($params['start']) && !empty($params['limit']) && $params['limit'] >= 0) {

            $limit_cond = ' LIMIT ' . $params['limit'];
        }


        if (!empty($params['order_by'])) {

            $order_cond = " ORDER BY " . $params['order_by'];
        }



        if (empty($params['category_slug'])) {

            $query = "SELECT books.* FROM " . TBL_CATEGORIES . " books WHERE books.is_parent=0 AND books.status=1 " . $order_cond . " " . $limit_cond . " ";
        } else if (!empty($params['category_slug'])) {

            $category_id = $this->get_categoryid_by_slug($params['category_slug']);

            if (!($category_id > 0))
                return FALSE;

            $query = "SELECT books.* FROM " . TBL_BOOK_CATEGORIES . " cc 
                    INNER JOIN " . TBL_CATEGORIES . " books ON books.id=cc.book_id 
                    WHERE cc.category_id=" . $category_id . " 
                    AND books.is_parent=0 AND books.status=1 
                    " . $order_cond . " " . $limit_cond . " ";
        }


        $result_set = $this->db->query($query);

        return ($result_set->num_rows() > 0) ? $result_set->result() : FALSE;
    }



    /* GET SELLING BOOKS */
    function get_selling_books($params = array())
    {

        $query      = "";
        $limit_cond = "";
        $seller_cond = "";
        $order_cond = " ORDER BY book_name ASC";


        if (!empty($params['start']) && !empty($params['limit']) && $params['start'] >= 0 && $params['limit'] >= 0) {

            $limit_cond = ' LIMIT ' . $params['start'] . ', ' . $params['limit'];
        } elseif (empty($params['start']) && !empty($params['limit']) && $params['limit'] >= 0) {

            $limit_cond = ' LIMIT ' . $params['limit'];
        }


        if (!empty($params['seller_id'])) {

            $seller_cond = " AND seller_id= " . $params['seller_id'];
        }

        if (!empty($params['order_by'])) {

            $order_cond = " ORDER BY " . $params['order_by'];
        }
        if (!empty($params['category_slug'])) {

            $category_id = $this->get_categoryid_by_slug($params['category_slug']);

            if ($category_id > 0) {
                $seller_cond = " AND category_id= " . $category_id;
            }
        }


        /* like, review, comments data to api */
        // echo $this->session->userdata['user_id'];die;
        $SELECTCOLUMN = "";
        $INNERJOINTABLE = "";
        if ($this->config->item('site_settings')->like_comment_setting == "Yes") {
            $SELECTCOLUMN .= ",(SELECT GROUP_CONCAT(DISTINCT " . TBL_PREFIX . "selling_book_likes.`user_id` SEPARATOR ', ') FROM " . TBL_PREFIX . "selling_book_likes WHERE " . TBL_PREFIX . "selling_book_likes.item_id = pre_seller_selling_books.sc_id ) AS userliked,(SELECT count( DISTINCT " . TBL_PREFIX . "selling_book_likes.like_id) FROM " . TBL_PREFIX . "selling_book_likes WHERE " . TBL_PREFIX . "selling_book_likes.item_id = pre_seller_selling_books.sc_id) as likescount";
            $SELECTCOLUMN .= "  , (SELECT COUNT(DISTINCT " . TBL_PREFIX . "selling_book_rating.rating_id) FROM " . TBL_PREFIX . "selling_book_rating WHERE " . TBL_PREFIX . "selling_book_rating.item_id = pre_seller_selling_books.sc_id ) as ratingscount,(SELECT  COALESCE(SUM(" . TBL_PREFIX . "selling_book_rating.rating_number),0) FROM " . TBL_PREFIX . "selling_book_rating WHERE " . TBL_PREFIX . "selling_book_rating.item_id = pre_seller_selling_books.sc_id) as totalratingscount";
            $SELECTCOLUMN .= "  ,   CASE WHEN " . TBL_PREFIX . "selling_book_rating.review IS NOT NULL THEN COUNT(" . TBL_PREFIX . "selling_book_rating.rating_id)  ELSE '0' END AS usercomment";


            $INNERJOINTABLE .= " LEFT  JOIN " . TBL_PREFIX . "selling_book_likes ON " . TBL_PREFIX . "selling_book_likes.item_id = " . TBL_PREFIX . "seller_selling_books.sc_id ";
            $INNERJOINTABLE .= " LEFT  JOIN " . TBL_PREFIX . "selling_book_rating ON " . TBL_PREFIX . "selling_book_rating.item_id = " . TBL_PREFIX . "seller_selling_books.sc_id ";
           
        }
         /* End like, review, comments data to api */
        $query = "SELECT * " . $SELECTCOLUMN . " FROM " . TBL_PREFIX . "seller_selling_books " . $INNERJOINTABLE . " WHERE " . TBL_PREFIX . "seller_selling_books.status='Active' AND admin_approved='Yes' " . $seller_cond . " GROUP BY pre_seller_selling_books.sc_id " . $order_cond . " " . $limit_cond . " ";

        // echo $query ;die;
        $result_set = $this->db->query($query);

        return $result_set->result();
    }

    /* GET SELLING BOOKS */
    function get_selling_books_via_api($params = array())
    {

        $query      = "";
        $limit_cond = "";
        $seller_cond = "";
        $order_cond = " ORDER BY book_name ASC";


        if (!empty($params['start']) && !empty($params['limit']) && $params['start'] >= 0 && $params['limit'] >= 0) {

            $limit_cond = ' LIMIT ' . $params['start'] . ', ' . $params['limit'];
        } elseif (empty($params['start']) && !empty($params['limit']) && $params['limit'] >= 0) {

            $limit_cond = ' LIMIT ' . $params['limit'];
        }


        if (!empty($params['seller_id'])) {

            $seller_cond = " AND seller_id= " . $params['seller_id'];
        }

        if (!empty($params['order_by'])) {

            $order_cond = " ORDER BY " . $params['order_by'];
        }
        if (!empty($params['category_slug'])) {

            $category_id = $this->get_categoryid_by_slug($params['category_slug']);

            if ($category_id > 0) {
                $seller_cond = " AND category_id= " . $category_id;
            }
        }





 /* like, review, comments data to api */
        // echo $this->session->userdata['user_id'];die;
        $SELECTCOLUMN = "";
        $INNERJOINTABLE = "";
        if ($this->config->item('site_settings')->like_comment_setting == "Yes") {

            $AdminDiscountSelect = ", 0 AS admin_discount";

            if ($postEmail = $this->input->post('email')) {
                $this->db->where('email', $postEmail);
                $query = $this->db->get(TBL_PREFIX . "users");
                $result_array = $query->row();
                //echo $this->db->last_query();
                //var_dump($result_array->admin_discount);die("under email");
                if ($result_array->admin_discount) {
                    $AdminDiscountSelect = ", $result_array->admin_discount AS admin_discount";
                } else {
                    $AdminDiscountSelect = ", 0 AS admin_discount";
                }

                //$SELECTCOLUMN .= ", ".$this->session->userdata['admin_discount']." AS admin_discount";
            }

            $SELECTCOLUMN .= $AdminDiscountSelect;

            $SELECTCOLUMN .= ",(SELECT GROUP_CONCAT(DISTINCT " . TBL_PREFIX . "selling_book_likes.`user_id` SEPARATOR ', ') FROM " . TBL_PREFIX . "selling_book_likes WHERE " . TBL_PREFIX . "selling_book_likes.item_id = pre_seller_selling_books.sc_id ) AS userliked,(SELECT count( DISTINCT " . TBL_PREFIX . "selling_book_likes.like_id) FROM " . TBL_PREFIX . "selling_book_likes WHERE " . TBL_PREFIX . "selling_book_likes.item_id = pre_seller_selling_books.sc_id) as likescount";
            $SELECTCOLUMN .= "  , (SELECT COUNT(DISTINCT " . TBL_PREFIX . "selling_book_rating.rating_id) FROM " . TBL_PREFIX . "selling_book_rating WHERE " . TBL_PREFIX . "selling_book_rating.item_id = pre_seller_selling_books.sc_id ) as ratingscount,(SELECT  COALESCE(SUM(" . TBL_PREFIX . "selling_book_rating.rating_number),0) FROM " . TBL_PREFIX . "selling_book_rating WHERE " . TBL_PREFIX . "selling_book_rating.item_id = pre_seller_selling_books.sc_id) as totalratingscount";
            $SELECTCOLUMN .= "  ,   CASE WHEN " . TBL_PREFIX . "selling_book_rating.review IS NOT NULL THEN COUNT(" . TBL_PREFIX . "selling_book_rating.rating_id)  ELSE '0' END AS usercomment";


            $INNERJOINTABLE .= " LEFT  JOIN " . TBL_PREFIX . "selling_book_likes ON " . TBL_PREFIX . "selling_book_likes.item_id = " . TBL_PREFIX . "seller_selling_books.sc_id ";
            $INNERJOINTABLE .= " LEFT  JOIN " . TBL_PREFIX . "selling_book_rating ON " . TBL_PREFIX . "selling_book_rating.item_id = " . TBL_PREFIX . "seller_selling_books.sc_id ";
        }
         /* End like, review, comments data to api */
         

        $query = "SELECT * " . $SELECTCOLUMN . " FROM " . TBL_PREFIX . "seller_selling_books " . $INNERJOINTABLE . " WHERE " . TBL_PREFIX . "seller_selling_books.status='Active' AND admin_approved='Yes' AND show_on_main_site='Yes' " . $seller_cond . "GROUP BY pre_seller_selling_books.sc_id "  . $order_cond . " " . $limit_cond . " ";

        //echo $query; die("workikng here");

        $result_set = $this->db->query($query);

        return $result_set->result();
    }



    /* GET LOCATIONS */
    function get_locations($params = array())
    {

        $query         = "";
        $limit_cond = "";
        $extra_cond = "";


        if (!empty($params['start']) && !empty($params['limit']) && $params['start'] >= 0 && $params['limit'] >= 0) {

            $limit_cond = ' LIMIT ' . $params['start'] . ', ' . $params['limit'];
        } elseif (empty($params['start']) && !empty($params['limit']) && $params['limit'] >= 0) {

            $limit_cond = ' LIMIT ' . $params['limit'];
        }

        if (!empty($params['child'])) {

            $extra_cond = ' AND parent_location_id != 0';
        }

        $query = "SELECT * FROM " . TBL_LOCATIONS . " WHERE (status='Active' OR status=1) " . $extra_cond . " ORDER BY sort_order ASC " . $limit_cond . " ";

        $result_set = $this->db->query($query);

        return ($result_set->num_rows() > 0) ? $result_set->result() : FALSE;
    }



    /* GET TEACHING TYPES */
    function get_teaching_types($params = array())
    {

        $query         = "";
        $limit_cond = "";


        if (!empty($params['start']) && !empty($params['limit']) && $params['start'] >= 0 && $params['limit'] >= 0) {

            $limit_cond = ' LIMIT ' . $params['start'] . ', ' . $params['limit'];
        } elseif (empty($params['start']) && !empty($params['limit']) && $params['limit'] >= 0) {

            $limit_cond = ' LIMIT ' . $params['limit'];
        }


        $query = "SELECT * FROM " . TBL_TEACHING_TYPES . " WHERE status=1 ORDER BY sort_order ASC " . $limit_cond . " ";

        $result_set = $this->db->query($query);

        return ($result_set->num_rows() > 0) ? $result_set->result() : FALSE;
    }



    /* GET SELLERS */
    function get_sellers($params = array())
    {

        $query                     = "";
        $limit_cond             = "";

        $adm_approval_cond         = "";

        $book_tbl_join        = "";
        $location_tbl_join         = "";
        $teaching_type_tbl_join = "";

        $book_cond             = "";
        $location_cond             = "";
        $teaching_type_cond     = "";


        if (!empty($params['start']) && !empty($params['limit']) && $params['start'] >= 0 && $params['limit'] >= 0) {

            $limit_cond = ' LIMIT ' . $params['start'] . ', ' . $params['limit'];
        } elseif (empty($params['start']) && !empty($params['limit']) && $params['limit'] >= 0) {

            $limit_cond = ' LIMIT ' . $params['limit'];
        }


        if (strcasecmp(get_system_settings('need_admin_for_seller'), 'yes') == 0) {

            $adm_approval_cond = ' AND u.admin_approved = "Yes" ';
        }



        if (!empty($params['book_slug'])) {

            $book_id      = $this->get_categoryid_by_slug($params['book_slug']);

            if (empty($book_id))
                return FALSE;

            $book_tbl_join = " INNER JOIN " . TBL_SELLER_BOOKS . " tc ON tc.seller_id=u.id ";
            $book_cond = " AND tc.book_id IN (" . $book_id . ") AND tc.status=1 ";
        }

        if (!empty($params['location_slug'])) {

            $location_id = $this->get_locationid_by_slug($params['location_slug']);

            if (empty($location_id))
                return FALSE;

            $location_tbl_join = " INNER JOIN " . TBL_SELLER_LOCATIONS . " tl ON tl.seller_id=u.id ";
            $location_cond = " AND tl.location_id IN (" . $location_id . ") ";
        }

        if (!empty($params['teaching_type_slug'])) {

            $teaching_type_id = $this->get_teachingtypeid_by_slug($params['teaching_type_slug']);

            if (empty($teaching_type_id))
                return FALSE;

            $teaching_type_tbl_join = " INNER JOIN " . TBL_SELLER_TEACHING_TYPES . " tt ON tt.seller_id=u.id ";
            $teaching_type_cond = " AND tt.teaching_type_id IN (" . $teaching_type_id . ") ";
        }


        $query = "SELECT u.* FROM " . TBL_USERS . " u 
	    			INNER JOIN " . TBL_USERS_GROUPS . " ug ON ug.user_id=u.id 
	    			" . $book_tbl_join . " 
	    			" . $location_tbl_join . " 
	    			" . $teaching_type_tbl_join . " 
					WHERE u.active=1 AND u.visibility_in_search='1' 
                    AND u.is_profile_update=1 AND (u.parent_id=0 OR u.parent_id='') AND ug.group_id=3 
					" . $adm_approval_cond . " 
					" . $book_cond . " 
					" . $location_cond . " 
					" . $teaching_type_cond . " 
					GROUP BY u.id ORDER BY u.net_credits DESC " . $limit_cond . " ";


        $result_set = $this->db->query($query);

        return ($result_set->num_rows() > 0) ? $result_set->result() : array();
    }



    /* GET SELLERS */
    function get_institutes($params = array())
    {

        $query                  = "";
        $limit_cond             = "";

        $adm_approval_cond      = "";

        $book_tbl_join        = "";
        $location_tbl_join      = "";
        $teaching_type_tbl_join = "";

        $inst_cond              = "";
        $book_cond            = "";
        $location_cond          = "";
        $teaching_type_cond     = "";
        $visibility_in_search   = "";

        if (!empty($params['start']) && !empty($params['limit']) && $params['start'] >= 0 && $params['limit'] >= 0) {

            $limit_cond = ' LIMIT ' . $params['start'] . ', ' . $params['limit'];
        } elseif (empty($params['start']) && !empty($params['limit']) && $params['limit'] >= 0) {

            $limit_cond = ' LIMIT ' . $params['limit'];
        }


        if (strcasecmp(get_system_settings('need_admin_for_inst'), 'yes') == 0) {

            $adm_approval_cond = ' AND u.admin_approved = "Yes" ';
        }


        if (!empty($params['book_slug'])) {

            $book_id   = $this->get_categoryid_by_slug($params['book_slug']);

            if (empty($book_id))
                return FALSE;

            $book_tbl_join = " INNER JOIN " . TBL_INST_OFFERED_BOOKS . " ic ON ic.inst_id=u.id ";
            $book_cond = " AND ic.book_id IN (" . $book_id . ") AND ic.status=1 ";
        }

        if (!empty($params['location_slug'])) {

            $location_id = $this->get_locationid_by_slug($params['location_slug']);

            if (empty($location_id))
                return FALSE;

            $location_tbl_join = " INNER JOIN " . TBL_INST_LOCATIONS . " il ON il.inst_id=u.id ";
            $location_cond = " AND il.location_id IN (" . $location_id . ") ";
        }

        if (!empty($params['teaching_type_slug'])) {

            $teaching_type_id = $this->get_teachingtypeid_by_slug($params['teaching_type_slug']);

            if (empty($teaching_type_id))
                return FALSE;

            $teaching_type_tbl_join = " INNER JOIN " . TBL_INST_TEACHING_TYPES . " tt ON tt.inst_id=u.id ";
            $teaching_type_cond = " AND tt.teaching_type_id IN (" . $teaching_type_id . ") ";
        }

        if (!empty($params['inst_slug'])) {

            $inst_slugs = "'" . implode("','", $params['inst_slug']) . "'";
            $inst_cond = ' AND u.slug IN (' . $inst_slugs . ')';
        }


        $query = "SELECT u.* FROM " . TBL_USERS . " u 
                    INNER JOIN " . TBL_USERS_GROUPS . " ug ON ug.user_id=u.id 
                    " . $book_tbl_join . " 
                    " . $location_tbl_join . " 
                    " . $teaching_type_tbl_join . " 
                    WHERE u.active=1 AND u.visibility_in_search='1' AND u.is_profile_update=1 AND ug.group_id=4 
                    " . $inst_cond . " 
                    " . $adm_approval_cond . " 
                    " . $book_cond . " 
                    " . $location_cond . " 
                    " . $teaching_type_cond . " 
                    GROUP BY u.id ORDER BY u.net_credits DESC " . $limit_cond . " ";


        $result_set = $this->db->query($query);

        return ($result_set->num_rows() > 0) ? $result_set->result() : array();
    }



    /* GET BUYER LEADS */
    function get_buyer_leads($params = array())
    {

        $query                 = "";
        $limit_cond         = "";
        $book_cond         = "";
        $location_cond         = "";
        $teaching_type_cond = "";


        if (!empty($params['start']) && !empty($params['limit']) && $params['start'] >= 0 && $params['limit'] >= 0) {

            $limit_cond = ' LIMIT ' . $params['start'] . ', ' . $params['limit'];
        } elseif (empty($params['start']) && !empty($params['limit']) && $params['limit'] >= 0) {

            $limit_cond = ' LIMIT ' . $params['limit'];
        }



        if (!empty($params['book_slug'])) {

            $book_id      = $this->get_categoryid_by_slug($params['book_slug']);

            if (empty($book_id))
                return FALSE;

            $book_cond = " AND sl.book_id IN (" . $book_id . ") ";
        }

        if (!empty($params['location_slug'])) {

            $location_id = $this->get_locationid_by_slug($params['location_slug']);

            if (empty($location_id))
                return FALSE;

            $location_cond = " AND sl.location_id IN (" . $location_id . ") ";
        }

        if (!empty($params['teaching_type_slug'])) {

            $teaching_type_id = $this->get_teachingtypeid_by_slug($params['teaching_type_slug']);

            if (empty($teaching_type_id))
                return FALSE;

            $teaching_type_cond = " AND sl.teaching_type_id IN (" . $teaching_type_id . ") ";
        }


        $query = "SELECT u.*, sl.*, sl.id AS lead_id FROM " . TBL_USERS . " u 
    			  INNER JOIN " . TBL_USERS_GROUPS . " ug ON ug.user_id=u.id 
    			  INNER JOIN " . TBL_BUYER_LEADS . " sl ON sl.user_id=u.id 
    			  WHERE u.active=1 AND u.visibility_in_search='1' AND u.availability_status='1' 
                  AND u.is_profile_update=1 AND ug.group_id=2 AND sl.status='opened' 
    			  " . $book_cond . " 
    			  " . $location_cond . " 
    			  " . $teaching_type_cond . " 
    			  ORDER BY sl.id DESC " . $limit_cond . " ";


        $result_set = $this->db->query($query);

        return ($result_set->num_rows() > 0) ? $result_set->result() : array();
    }


    function get_uid_by_slug($uslug = "")
    {
        if (empty($uslug))
            return NULL;

        $row = $this->db->select('id')->get_where($this->db->dbprefix('users'), array('slug' => $uslug))->row();

        return (!empty($row)) ? $row->id : '';
    }


    function get_bookid_by_slug($cslug = "")
    {
        if (empty($cslug))
            return NULL;

        return $this->db->select('id')->get_where($this->db->dbprefix('categories'), array('slug' => $cslug))->row()->id;
    }


    function get_seller_profile($seller_slug = "")
    {
        if (empty($seller_slug))
            return NULL;

        $adm_approval_cond     = "";

        if (strcasecmp(get_system_settings('need_admin_for_seller'), 'yes') == 0) {

            $adm_approval_cond = ' AND u.admin_approved = "Yes" ';
        }

        $seller_id = $this->get_uid_by_slug($seller_slug);


        $seller_info_query = "SELECT u.* FROM " . $this->db->dbprefix('users') . " u WHERE u.active=1 AND u.visibility_in_search='1' AND u.availability_status='1' AND u.is_profile_update=1 AND (u.parent_id=0 OR u.parent_id='') AND u.slug='" . $seller_slug . "' " . $adm_approval_cond . " ";

        $seller_details = $this->db->query($seller_info_query)->result();

        if (empty($seller_details)) {
            return array();
        }

        //Sellering Books
        $seller_books_query = "SELECT GROUP_CONCAT(' ', books.name) AS sellering_books FROM " . $this->db->dbprefix('seller_books') . " tc INNER JOIN " . $this->db->dbprefix('categories') . " books ON books.id=tc.book_id WHERE tc.seller_id=" . $seller_id . " AND tc.status=1 AND books.status=1 ORDER BY tc.sort_order ASC";
        $seller_details[0]->sellering_books = $this->db->query($seller_books_query)->row()->sellering_books;

        //Sellering Locations
        $seller_locations_query = "SELECT GROUP_CONCAT(' ', l.location_name) AS sellering_locations FROM " . $this->db->dbprefix('seller_locations') . " tl INNER JOIN " . $this->db->dbprefix('locations') . " l ON l.id=tl.location_id WHERE tl.seller_id=" . $seller_id . " AND tl.status=1 AND l.status=1 ORDER BY tl.sort_order ASC";
        $seller_details[0]->sellering_locations = $this->db->query($seller_locations_query)->row()->sellering_locations;

        //Seller's Gallery
        $seller_gallery_query = "SELECT image_title, image_name FROM " . $this->db->dbprefix('gallery') . " WHERE user_id=" . $seller_id . " AND image_status='Active' ORDER BY image_order ASC";
        $seller_details[0]->seller_gallery = $this->db->query($seller_gallery_query)->result();

        //Seller Experience
        $seller_experience_query = "SELECT company, role, description, from_date, to_date FROM " . $this->db->dbprefix('users_experience') . " WHERE user_id=" . $seller_id . " ";
        $seller_details[0]->seller_experience = $this->db->query($seller_experience_query)->result();


        return $seller_details;
    }



    function get_full_location_name($location_id = "")
    {
        if (!($location_id > 0))
            return NULL;

        $query = "SELECT CONCAT(l.location_name, ', ', pl.location_name) AS full_location_name FROM " . $this->db->dbprefix('locations') . " l INNER JOIN " . $this->db->dbprefix('locations') . " pl ON pl.id=l.parent_location_id WHERE l.id=" . $location_id;
        $result_set = $this->db->query($query);

        return ($result_set->num_rows() > 0) ? $result_set->row()->full_location_name : FALSE;
    }



    /* GET SELLER BOOKS BY SELLER SLUG */
    function get_seller_books($seller_slug = "", $result_type = "")
    {
        if (empty($seller_slug))
            return NULL;

        $seller_id = $this->get_uid_by_slug($seller_slug);

        $query = "SELECT books.slug, books.name FROM " . $this->db->dbprefix('categories') . " books INNER JOIN " . $this->db->dbprefix('seller_books') . " tc ON tc.book_id=books.id WHERE tc.seller_id=" . $seller_id . " AND tc.status=1 AND books.status=1 ORDER BY tc.sort_order ASC ";

        if ($result_type == "grouped") {

            $query = "SELECT GROUP_CONCAT(' ', books.name) AS sellering_books FROM " . $this->db->dbprefix('seller_books') . " tc INNER JOIN " . $this->db->dbprefix('categories') . " books ON books.id=tc.book_id WHERE tc.seller_id=" . $seller_id . " AND tc.status=1 AND books.status=1 ORDER BY tc.sort_order ASC";

            $rs = $this->db->query($query);

            return ($rs->num_rows() > 0) ? $rs->row()->sellering_books : NULL;
        }

        $rs = $this->db->query($query);

        return ($rs->num_rows() > 0) ? $rs->result() : NULL;
    }


    /* GET SELLER LOCATIONS BY SELLER SLUG */
    function get_seller_locations($seller_slug = "")
    {
        if (empty($seller_slug))
            return NULL;

        $seller_id = $this->get_uid_by_slug($seller_slug);

        $query = "SELECT l.slug, l.location_name FROM " . $this->db->dbprefix('locations') . " l INNER JOIN " . $this->db->dbprefix('seller_locations') . " tl ON tl.location_id=l.id WHERE tl.seller_id=" . $seller_id . " AND tl.status=1 AND (l.status=1 OR l.status='Active') ORDER BY tl.sort_order ASC ";

        $rs = $this->db->query($query);

        return ($rs->num_rows() > 0) ? $rs->result() : NULL;
    }


    /* GET SELLER TEACHING TYPES BY SELLER SLUG */
    function get_seller_teaching_types($seller_slug = "")
    {
        if (empty($seller_slug))
            return NULL;

        $seller_id = ($seller_slug > 0) ? $seller_slug : $this->get_uid_by_slug($seller_slug);

        $query = "SELECT tt.slug, tt.teaching_type FROM " . $this->db->dbprefix('teaching_types') . " tt INNER JOIN " . $this->db->dbprefix('seller_teaching_types') . " ttt ON ttt.teaching_type_id=tt.id WHERE ttt.seller_id=" . $seller_id . " AND ttt.status=1 AND tt.status=1 ORDER BY ttt.sort_order ASC ";

        $rs = $this->db->query($query);

        return ($rs->num_rows() > 0) ? $rs->result() : NULL;
    }


    function get_seller_book_details($book_slug = "", $seller_id = "")
    {
        if (empty($book_slug) || empty($seller_id))
            return NULL;

        $book_id = ($book_slug > 0) ? $book_slug : $this->get_bookid_by_slug($book_slug);

        $rs = $this->db->select('book_id, duration_value, duration_type, fee, per_credit_value, content, time_slots, days_off')
            ->get_where(
                $this->db->dbprefix('seller_books'),
                array(
                    'book_id' => $book_id,
                    'seller_id' => $seller_id,
                    'status' => 1
                )
            );

        return ($rs->num_rows() > 0) ? $rs->row() : NULL;
    }


    function get_booked_slots($seller_id = "", $book_id = "", $selected_date = "")
    {
        if (empty($seller_id) || empty($book_id) || empty($selected_date))
            return NULL;

        $query = "SELECT time_slot FROM " . TBL_BOOKINGS . " WHERE seller_id=" . $seller_id . " AND book_id=" . $book_id . " AND ('" . $selected_date . "' BETWEEN start_date AND end_date) AND status!='pending' AND status!='cancelled_before_book_started' AND status!='cancelled_when_book_running' AND status!='cancelled_after_book_completed' AND status!='completed' AND status!='closed' ";
        $rs = $this->db->query($query);

        if ($rs->num_rows() > 0) {

            $slots = array();

            foreach ($rs->result() as $key => $value) {
                $slots[] = $value->time_slot;
            }

            return $slots;
        } else return NULL;
    }


    function is_already_booked_the_seller($buyer_id = "", $seller_id = "", $book_id = "", $selected_date = "", $time_slot = "")
    {
        if (empty($buyer_id) || empty($seller_id) || empty($book_id) || empty($selected_date) || empty($time_slot))
            return FALSE;

        $query = "SELECT booking_id FROM " . TBL_BOOKINGS . " WHERE buyer_id=" . $buyer_id . " AND seller_id=" . $seller_id . " AND book_id=" . $book_id . " AND ('" . $selected_date . "' BETWEEN start_date AND end_date) AND time_slot='" . $time_slot . "' AND (status='pending' OR status='approved' OR status='session_initiated' OR status='running' OR status='called_for_admin_intervention') ";
        $rs = $this->db->query($query);

        return ($rs->num_rows() > 0) ? TRUE : FALSE;
    }


    function is_already_enrolled_in_the_batch($buyer_id = "", $batch_id = "")
    {
        if (empty($buyer_id) || empty($batch_id))
            return FALSE;

        $query = "SELECT enroll_id FROM " . TBL_INST_ENROLLED_BUYERS . " WHERE buyer_id=" . $buyer_id . " AND batch_id=" . $batch_id . " AND (status='pending' OR status='approved' OR status='session_initiated' OR status='running' OR status='called_for_admin_intervention') ";
        $rs = $this->db->query($query);

        return ($rs->num_rows() > 0) ? TRUE : FALSE;
    }


    function is_time_slot_avail($seller_id = "", $book_id = "", $selected_date = "", $time_slot = "")
    {
        if (empty($seller_id) || empty($book_id) || empty($selected_date) || empty($time_slot))
            return FALSE;

        $query = "SELECT booking_id FROM " . TBL_BOOKINGS . " WHERE seller_id=" . $seller_id . " AND book_id=" . $book_id . " AND ('" . $selected_date . "' BETWEEN start_date AND end_date) AND time_slot='" . $time_slot . "' AND (status='approved' OR status='session_initiated' OR status='running' OR status='called_for_admin_intervention') ";
        $rs = $this->db->query($query);

        return ($rs->num_rows() > 0) ? FALSE : TRUE;
    }


    //To check for new enrollment availability
    function total_enrolled_buyers_in_batch($batch_id = "")
    {
        if (empty($batch_id))
            return 0;

        $query = "SELECT enroll_id FROM " . TBL_INST_ENROLLED_BUYERS . " WHERE batch_id=" . $batch_id . " AND (status='pending' OR status='approved' OR status='session_initiated' OR status='running' OR status='called_for_admin_intervention') ";
        $rs = $this->db->query($query);

        return $rs->num_rows();
    }


    function log_user_credits_transaction($log_data = array())
    {
        if (empty($log_data))
            return NULL;

        if ($this->db->insert(TBL_USER_CREDIT_TRANSACTIONS, $log_data))
            return TRUE;
        else
            return FALSE;
    }


    function update_user_credits($user_id = "", $credits = 0, $action = "")
    {
        if (!($user_id > 0) || !($credits > 0) || !in_array($action, array('credit', 'debit')))
            return NULL;

        $operation = '-';
        if ($action == "credit")
            $operation = '+';

        $date = date('Y-m-d H:i:s');
        $query = "UPDATE " . TBL_USERS . " SET net_credits=net_credits" . $operation . $credits . ", last_updated='" . $date . "' WHERE id=" . $user_id . " ";

        $this->db->query($query);

        return $this->db->affected_rows();
    }


    function view_lead_app($buyer_slug = "", $buyer_lead_id = "", $seller_id = "")
    {
        if (empty($buyer_slug))
            return false;

        $CI = &get_instance();

        $buyer_id = $this->get_uid_by_slug($buyer_slug);

        if (empty($buyer_id))
            return false;

        $buyer_info_query = "SELECT * FROM " . TBL_USERS . " WHERE id=" . $buyer_id . " AND active=1 AND visibility_in_search='1' AND availability_status='1' AND is_profile_update=1 ";

        $buyer_details = $this->db->query($buyer_info_query)->result();
        if (!empty($buyer_details)) {
            if ($buyer_lead_id > 0) {
                $lead_info_query = "SELECT sl.*, l.location_name, pl.location_name AS parent_location_name, c.name AS book_name, t.teaching_type FROM " . TBL_BUYER_LEADS . " sl INNER JOIN " . TBL_LOCATIONS . " l ON l.id=sl.location_id INNER JOIN " . TBL_LOCATIONS . " pl ON pl.id=l.parent_location_id INNER JOIN " . TBL_CATEGORIES . " c ON c.id=sl.book_id INNER JOIN " . TBL_TEACHING_TYPES . " t ON t.id=sl.teaching_type_id WHERE sl.id=" . $buyer_lead_id . " AND sl.status='Opened' ";

                $lead_details = $this->db->query($lead_info_query)->result();

                if (!empty($lead_details)) {
                    $credits_required_for_viewing_lead = get_system_settings('credits_for_viewing_lead');

                    if ($credits_required_for_viewing_lead > 0) {
                        $viewer_id = $seller_id;

                        if (!$this->is_already_viewed_the_lead($viewer_id, 'buyer_leads', $buyer_lead_id)) {
                            $viewer_credits = get_user_credits($viewer_id);

                            if ($viewer_credits >= $credits_required_for_viewing_lead) {
                                //Log Credits transaction data & update user net credits - Start
                                $log_data = array(
                                    'user_id' => $viewer_id,
                                    'credits' => $credits_required_for_viewing_lead,
                                    'per_credit_value' => get_system_settings('per_credit_value'),
                                    'action'  => 'debited',
                                    'purpose' => get_languageword('For viewing lead ') . ' "' . $lead_details[0]->title_of_requirement . '" ' . get_languageword('of Buyer') . ' "' . $buyer_details[0]->username . '"',
                                    'date_of_action ' => date('Y-m-d H:i:s'),
                                    'reference_table' => 'buyer_leads',
                                    'reference_id' => $buyer_lead_id,
                                );

                                log_user_credits_transaction($log_data);

                                update_user_credits($viewer_id, $credits_required_for_viewing_lead, 'debit');
                                //Log Credits transaction data & update user net credits - End

                                //Update Lead View Count
                                $this->update_lead_view_count($buyer_lead_id);


                                //Successfully viewed
                                //$msg='Seller viewed lead';
                                $result = array('Message' => 'Seller viewed lead', 'status' => true);
                                // return true;

                            } else {
                                //Seller does not have enough credits
                                //$msg='Seller does not have enough credits';
                                $result = array('Message' => 'Seller does not have enough credits', 'status' => false);
                                // return false;
                            }
                        }
                    }
                } else {
                    //Lead details not found
                    //$msg='Lead Details not Found';
                    $result = array('Message' => 'Lead Details not Found', 'status' => false);
                    // return false;
                }
            } else {
                //
                //$msg='Invalid Operation';
                $result = array('Message' => 'Invalid Operation', 'status' => false);
                //return false;
            }
        } else {
            //Lead details not found
            //$msg='Buyer Details not Found';
            $result = array('Message' => 'Buyer Details not Found', 'status' => false);

            // return false;
        }

        return $result;
    }

    function get_buyer_profile_app($buyer_slug = "", $buyer_lead_id = "", $seller_id = "")
    {
        if (empty($buyer_slug))
            return NULL;

        $CI = &get_instance();

        $buyer_id = $this->get_uid_by_slug($buyer_slug);

        if (empty($buyer_id))
            return NULL;

        $buyer_info_query = "SELECT * FROM " . TBL_USERS . " WHERE id=" . $buyer_id . " AND active=1 AND visibility_in_search='1' AND availability_status='1' AND is_profile_update=1 ";

        $buyer_details = $this->db->query($buyer_info_query)->result();

        if (!empty($buyer_details)) {
            if ($buyer_lead_id > 0) {
                $lead_info_query = "SELECT sl.*, l.location_name, pl.location_name AS parent_location_name, c.name AS book_name, t.teaching_type FROM " . TBL_BUYER_LEADS . " sl INNER JOIN " . TBL_LOCATIONS . " l ON l.id=sl.location_id INNER JOIN " . TBL_LOCATIONS . " pl ON pl.id=l.parent_location_id INNER JOIN " . TBL_CATEGORIES . " c ON c.id=sl.book_id INNER JOIN " . TBL_TEACHING_TYPES . " t ON t.id=sl.teaching_type_id WHERE sl.id=" . $buyer_lead_id . " AND sl.status='Opened' ";

                $lead_details = $this->db->query($lead_info_query)->result();

                if (!empty($lead_details)) {
                    $credits_required_for_viewing_lead = get_system_settings('credits_for_viewing_lead');

                    if ($credits_required_for_viewing_lead > 0) {
                        $viewer_id = $seller_id;

                        if (!$this->is_already_viewed_the_lead($viewer_id, 'buyer_leads', $buyer_lead_id)) {
                            $viewer_credits = get_user_credits($viewer_id);

                            if ($viewer_credits >= $credits_required_for_viewing_lead) {
                                //Log Credits transaction data & update user net credits - Start
                                $log_data = array(
                                    'user_id' => $viewer_id,
                                    'credits' => $credits_required_for_viewing_lead,
                                    'per_credit_value' => get_system_settings('per_credit_value'),
                                    'action'  => 'debited',
                                    'purpose' => get_languageword('For viewing lead ') . ' "' . $lead_details[0]->title_of_requirement . '" ' . get_languageword('of Buyer') . ' "' . $buyer_details[0]->username . '"',
                                    'date_of_action ' => date('Y-m-d H:i:s'),
                                    'reference_table' => 'buyer_leads',
                                    'reference_id' => $buyer_lead_id,
                                );

                                log_user_credits_transaction($log_data);

                                update_user_credits($viewer_id, $credits_required_for_viewing_lead, 'debit');
                                //Log Credits transaction data & update user net credits - End

                                //Update Lead View Count
                                $this->update_lead_view_count($buyer_lead_id);
                            } else {
                                //Seller does not have enough credits
                                $msg = 'Seller does not have enough credits';
                                return $msg;
                            }
                        }
                    }
                    /* else
					   {
						   //Seller does not have enough credits
							$msg='Seller does not have enough credits';
							return $msg;
					   } */
                    $buyer_details[0]->lead_details = $lead_details;
                } else {
                    //Lead details not found
                    $msg = 'Lead Details not Found';
                    return $msg;
                }
            }

            //Buyer's Gallery
            $buyer_gallery_query = "SELECT image_title, image_name FROM " . $this->db->dbprefix('gallery') . " WHERE user_id=" . $buyer_id . " AND image_status='Active' ORDER BY image_order ASC";
            $buyer_details[0]->buyer_gallery = $this->db->query($buyer_gallery_query)->result();

            return $buyer_details;
        } else
            return array();
    }


    function get_buyer_profile($buyer_slug = "", $buyer_lead_id = "")
    {
        if (empty($buyer_slug))
            return NULL;

        $CI = &get_instance();


        if (!$this->ion_auth->logged_in()) {
            $CI->prepare_flashmessage(get_languageword('please_login_to_continue.'), 2);
            return redirect(URL_AUTH_LOGIN);
        }


        $buyer_id = $this->get_uid_by_slug($buyer_slug);

        if (empty($buyer_id))
            return NULL;

        $buyer_info_query = "SELECT * FROM " . TBL_USERS . " WHERE id=" . $buyer_id . " AND active=1 AND visibility_in_search='1' AND availability_status='1' 
                  AND is_profile_update=1 ";

        $buyer_details = $this->db->query($buyer_info_query)->result();

        if (!empty($buyer_details)) {

            if ($buyer_lead_id > 0) {

                $lead_info_query = "SELECT sl.*, l.location_name, pl.location_name AS parent_location_name, c.name AS book_name, t.teaching_type FROM " . TBL_BUYER_LEADS . " sl INNER JOIN " . TBL_LOCATIONS . " l ON l.id=sl.location_id INNER JOIN " . TBL_LOCATIONS . " pl ON pl.id=l.parent_location_id INNER JOIN " . TBL_CATEGORIES . " c ON c.id=sl.book_id INNER JOIN " . TBL_TEACHING_TYPES . " t ON t.id=sl.teaching_type_id WHERE sl.id=" . $buyer_lead_id . " AND sl.status='Opened' ";

                $lead_details = $this->db->query($lead_info_query)->result();

                if (!empty($lead_details) && !$this->ion_auth->is_admin()) {

                    $credits_required_for_viewing_lead = get_system_settings('credits_for_viewing_lead');

                    if ($credits_required_for_viewing_lead > 0) {

                        $viewer_id = $this->ion_auth->get_user_id();

                        if (!$this->is_already_viewed_the_lead($viewer_id, 'buyer_leads', $buyer_lead_id)) {

                            $viewer_credits = get_user_credits($viewer_id);

                            if ($viewer_credits >= $credits_required_for_viewing_lead) {

                                //Log Credits transaction data & update user net credits - Start
                                $log_data = array(
                                    'user_id' => $viewer_id,
                                    'credits' => $credits_required_for_viewing_lead,
                                    'per_credit_value' => get_system_settings('per_credit_value'),
                                    'action'  => 'debited',
                                    'purpose' => get_languageword('For viewing lead ') . ' "' . $lead_details[0]->title_of_requirement . '" ' . get_languageword('of Buyer') . ' "' . $buyer_details[0]->username . '"',
                                    'date_of_action ' => date('Y-m-d H:i:s'),
                                    'reference_table' => 'buyer_leads',
                                    'reference_id' => $buyer_lead_id,
                                );

                                log_user_credits_transaction($log_data);

                                update_user_credits($viewer_id, $credits_required_for_viewing_lead, 'debit');
                                //Log Credits transaction data & update user net credits - End

                                //Update Lead View Count
                                $this->update_lead_view_count($buyer_lead_id);
                            } else {


                                $hlink = '#';
                                if ($this->ion_auth->is_seller())
                                    $hlink = URL_SELLER_LIST_PACKAGES;
                                else if ($this->ion_auth->is_institute())
                                    $hlink = URL_INSTITUTE_LIST_PACKAGES;

                                $CI->prepare_flashmessage(get_languageword('you_don\'t_have_enough_credits_to_view_the_lead_details. Please') . " <a href='" . $hlink . "'><strong>" . get_languageword('_get_credits_here.') . "</strong></a> ", 2);
                                return redirect(URL_HOME_SEARCH_BUYER_LEADS);
                            }
                        }
                    }
                }


                $buyer_details[0]->lead_details = $lead_details;
            }

            //Buyer's Gallery
            $buyer_gallery_query = "SELECT image_title, image_name FROM " . $this->db->dbprefix('gallery') . " WHERE user_id=" . $buyer_id . " AND image_status='Active' ORDER BY image_order ASC";
            $buyer_details[0]->buyer_gallery = $this->db->query($buyer_gallery_query)->result();

            return $buyer_details;
        } else return array();
    }


    function is_already_viewed_the_lead($user_id = "", $reference_table = "", $reference_id = "")
    {
        if (empty($user_id) || empty($reference_table) || empty($reference_id))
            return FALSE;

        $is_exist = $this->db->select('id')->get_where(TBL_USER_CREDIT_TRANSACTIONS, array('user_id' => $user_id, 'reference_table' => $reference_table, 'reference_id' => $reference_id))->row();
        if (count($is_exist) > 0)
            return TRUE;
        else
            return FALSE;
    }


    function update_lead_view_count($lead_id = "")
    {
        if (empty($lead_id))
            return NULL;

        $query = "UPDATE " . TBL_BUYER_LEADS . " SET no_of_views=no_of_views+1 WHERE id=" . $lead_id . " ";

        $this->db->query($query);

        return $this->db->affected_rows();
    }



    function is_uploaded_certificates($user_id = "")
    {
        if (empty($user_id))
            return FALSE;

        $user_type = getUserType($user_id);

        $sub_query = "SELECT certificate_id
                        FROM " . $this->db->dbprefix('certificates') . " c
                        WHERE c.certificate_for =  '" . $user_type . "'
                        AND c.status =  'Active'";

        $admin_req_cert_cnt = $this->db->query($sub_query)->num_rows();


        $query = "SELECT uc.admin_certificate_id
                    FROM " . $this->db->dbprefix('users_certificates') . " uc
                    WHERE uc.admin_certificate_id !=0
                    AND uc.user_id =" . $user_id . "
                    AND EXISTS (

                    " . $sub_query . "
                    )";

        $user_uploaded_cert_cnt = $this->db->query($query)->num_rows();


        return ($user_uploaded_cert_cnt >= $admin_req_cert_cnt) ? TRUE : FALSE;
    }


    function get_faqs()
    {
        $faqData = $this->db->get_where($this->db->dbprefix('faqs'), array('status' => 'Active'))->result();
        return $faqData;
    }

    function get_inst_profile($inst_slug = " ")
    {
        if (empty($inst_slug))
            return NULL;

        $adm_approval_cond  = "";

        if (strcasecmp(get_system_settings('need_admin_for_institute'), 'yes') == 0) {

            $adm_approval_cond = ' AND u.admin_approved = "Yes" ';
        }

        $inst_id = $this->get_uid_by_slug($inst_slug);


        $inst_info_query = "SELECT u.* FROM " . $this->db->dbprefix('users') . " u WHERE u.active=1 AND u.is_profile_update=1 AND u.slug='" . $inst_slug . "' " . $adm_approval_cond . " ";

        $inst_details = $this->db->query($inst_info_query)->result();

        if (empty($inst_details)) {
            return array();
        }

        //Institute Offered Books
        $inst_offered_books_query = " SELECT ic.book_id, c.slug, c.name FROM " . $this->db->dbprefix('inst_offered_books') . " ic INNER JOIN  " . $this->db->dbprefix('categories') . "  c ON c.id=ic.book_id WHERE ic.inst_id= " . $inst_id . " GROUP BY ic.book_id";
        $inst_details[0]->institute_offered_books = $this->db->query($inst_offered_books_query)->result();


        //Institute Sellering Locations
        $inst_sellering_locations_query = "SELECT l.location_name FROM " . $this->db->dbprefix('inst_locations') . " il INNER JOIN " . TBL_LOCATIONS . " l ON l.id=il.location_id WHERE l.status='Active' AND il.status=1 AND il.inst_id=" . $inst_id . " ";
        $inst_details[0]->institute_sellering_locations = $this->db->query($inst_sellering_locations_query)->result();


        //Institute's Gallery
        $inst_gallery_query = "SELECT image_title, image_name FROM " . $this->db->dbprefix('gallery') . " WHERE user_id=" . $inst_id . " AND image_status='Active' ORDER BY image_order ASC";
        $inst_details[0]->inst_gallery = $this->db->query($inst_gallery_query)->result();


        return $inst_details;
    }

    function get_institute_batches_info_by_book($book_id = "", $inst_id = "", $batch_id = "")
    {
        $query = "select *, (select username from " . $this->db->dbprefix('users') . " where p.seller_id = id) as sellername from " . $this->db->dbprefix('inst_batches') . " p where book_id = " . $book_id . " AND inst_id =" . $inst_id . " AND batch_id = " . $batch_id;
        $institute_batches_info_by_book = $this->db->query($query)->result();
        return $institute_batches_info_by_book;
    }


    function get_inst_batch_details($batch_id = "")
    {
        if (empty($batch_id))
            return NULL;

        $rs = $this->db->select('*')
            ->get_where(
                $this->db->dbprefix('inst_batches'),
                array(
                    'batch_id' => $batch_id,
                    'status' => 1
                )
            );

        return ($rs->num_rows() > 0) ? $rs->row_array() : NULL;
    }

    // get testimonials
    function get_site_testimonials()
    {
        $site_testimonials_query = "select * from " . $this->db->dbprefix('sitetestimonials') . " where status='Active'";
        $site_testimonials = $this->db->query($site_testimonials_query)->result();
        return $site_testimonials;
    }

    function get_scroll_news()
    {
        $scroll_news = $this->db->get_where($this->db->dbprefix('scroll_news'), array('status' => 'Active'))->result();
        return $scroll_news;
    }


    function get_seller_reviews($seller_slug = "")
    {
        $seller_id = $this->get_uid_by_slug($seller_slug);
        $query = "select  u.username as buyer_name,u.gender,u.photo,c.name as book,tr.rating,tr.comments,tr.created_at as posted_on from " . $this->db->dbprefix('seller_reviews') . " tr inner join " . $this->db->dbprefix('users') . " u on tr.buyer_id = u.id inner join " . $this->db->dbprefix('categories') . " c on tr.book_id=c.id where tr.seller_id=" . $seller_id . " and tr.status='Approved' ORDER by rating DESC limit 0,5";

        $seller_reviews = $this->db->query($query)->result();
        return $seller_reviews;
    }

    function get_seller_rating($seller_slug = "")
    {
        $seller_id = $this->get_uid_by_slug($seller_slug);
        if (empty($seller_id))
            return NULL;

        $query = "select count(*) as no_of_ratings, (sum(rating)/count(*)) as avg_rating from " . $this->db->dbprefix('seller_reviews') . " where seller_id=" . $seller_id . " AND status='Approved'";
        $seller_ratings = $this->db->query($query)->row();
        return $seller_ratings;
    }

    function get_home_seller_ratings()
    {
        $query = "select u.username,u.photo,u.qualification,u.gender,u.slug,u.facebook,u.twitter,u.linkedin,avg(tr.rating)as rating  from " . $this->db->dbprefix('seller_reviews') . " tr join " . $this->db->dbprefix('users') . " u on tr.seller_id = u.id group by tr.seller_id having avg(tr.rating)>=3";
        $seller_home_rating = $this->db->query($query)->result();
        return $seller_home_rating;
    }



    /*******************************
   05-12-2018
     ********************************/
    function get_latest_blogs()
    {
        $query = "SELECT tb.*,u.username,u.photo,u.gender,u.slug FROM " . TBL_SELLER_BLOGS . " tb INNER JOIN " . TBL_USERS . " u ON tb.seller_id=u.id WHERE tb.blog_status='Active' AND tb.admin_approved='Yes' AND u.active=1 AND u.user_belongs_group=3 ORDER BY tb.blog_id DESC LIMIT 5";

        //$this->db->get_where($this->db->dbprefix('seller_blogs'),array('blog_status' => 'Active','admin_approved'=>'Yes'))->result();


        $latest_blogs = $this->db->query($query)->result();
        return $latest_blogs;
    }


    /* GET SELLERS BLOGS */
    function get_seller_blogs($params = array())
    {

        $query      = "";
        $limit_cond = "";
        $order_cond = " ORDER BY tb.blog_id DESC";

        $tuotr_cond = "";

        if (!empty($params['start']) && !empty($params['limit']) && $params['start'] >= 0 && $params['limit'] >= 0) {

            $limit_cond = ' LIMIT ' . $params['start'] . ', ' . $params['limit'];
        } elseif (empty($params['start']) && !empty($params['limit']) && $params['limit'] >= 0) {

            $limit_cond = ' LIMIT ' . $params['limit'];
        }


        if (!empty($params['order_by'])) {

            $order_cond = " ORDER BY " . $params['order_by'];
        }


        if (!empty($params['sellers'])) {

            $sellers = $params['sellers'];

            //get seller ids

            $seller_id   = $this->get_sellerid_by_slug($params['sellers']);
            // echo "<pre>";print_R($seller_id);die();

            if (empty($seller_id))
                return FALSE;


            // $book_tbl_join = " INNER JOIN ".TBL_SELLER_BOOKS." tc ON tc.seller_id=u.id ";
            $tuotr_cond = " AND tb.seller_id IN (" . $seller_id . ")  ";
        }


        $query = "SELECT tb.*,u.username,u.photo,u.gender,u.slug FROM " . TBL_SELLER_BLOGS . " tb 
                    INNER JOIN " . TBL_USERS . " u ON tb.seller_id=u.id WHERE tb.blog_status='Active' AND tb.admin_approved='Yes' AND u.active=1 AND u.user_belongs_group=3 " . $tuotr_cond . "
                    " . $order_cond . " " . $limit_cond . " ";

        $result_set = $this->db->query($query);

        return ($result_set->num_rows() > 0) ? $result_set->result() : FALSE;
    }


    function get_blog_details($blog_id = "")
    {
        if (empty($blog_id))
            return NULL;


        $query = "SELECT tb.*,u.username,u.photo,u.gender,u.slug FROM " . TBL_SELLER_BLOGS . " tb INNER JOIN " . TBL_USERS . " u ON tb.seller_id=u.id WHERE tb.blog_id=" . $blog_id . " AND tb.blog_status='Active' AND tb.admin_approved='Yes' AND  u.active=1 AND u.user_belongs_group=3";


        $result_set = $this->db->query($query);

        if ($result_set->num_rows() > 0) {

            $result = $result_set->result();
            if (!empty($result)) {
                //selected blog
                $result = $result[0];

                //seller all blogs
                /*$blogs_query = "SELECT b.* FROM ".TBL_SELLER_BLOGS." b WHERE b.blog_id!=".$blog_id." AND b.seller_id=".$result->seller_id." ";*/
                $blogs_query = "SELECT b.*,u.username,u.photo,u.gender,u.slug FROM " . TBL_SELLER_BLOGS . " b INNER JOIN " . TBL_USERS . " u ON b.seller_id=u.id WHERE b.blog_id!=" . $blog_id . " AND b.seller_id=" . $result->seller_id . " AND b.blog_status='Active' AND b.admin_approved='Yes' ";
                $blogs = $this->db->query($blogs_query)->result();
                $result->blogs = $blogs;


                //Seller Reviews
                // $seller_reviews = $this->get_seller_reviews($result->slug);
                // $result->seller_reviews = $seller_reviews;


                return $result;
            } else
                return NULL;
        } else
            return NULL;
    }


    /**get blogs sellers**/
    function get_blogs_sellers_options()
    {

        $options = array();

        $query = "SELECT DISTINCT(b.seller_id) as seller_id,u.username,u.slug FROM " . TBL_SELLER_BLOGS . " b INNER JOIN " . TBL_USERS . " u ON b.seller_id=u.id WHERE u.active=1 AND u.user_belongs_group=3";

        $result_set = $this->db->query($query);

        if ($result_set->num_rows() > 0) {

            $result = $result_set->result();

            $options[''] = get_languageword('select_seller');
            // $options = array(''=>'Select Seller');
            foreach ($result as $r) {
                $options[$r->slug] = $r->username;
            }
        } else
            $options = array('' => 'Not Available');


        return $options;
    }




    function get_sellerid_by_slug($seller_slug)
    {
        if (empty($seller_slug))
            return 0;

        $column_to_select = 'id';
        if (is_array($seller_slug) || $seller_slug instanceof Traversable) {

            $column_to_select = 'GROUP_CONCAT(id) AS id';
        }
        $result_set = $this->db->select($column_to_select)
            ->where_in('slug', $seller_slug)
            ->get(TBL_USERS);

        return ($result_set->num_rows() > 0) ? $result_set->row()->id : 0;
    }
    
    /* Point system */
    function addupdate_pointsystem($user_id, $item_id, $point_type, $points, $action = "credited", $trans_id = "")
    {

        if ($this->config->item('site_settings')->enable_point_system == "Yes") {
            //  if (
            $insert_data = ['user_id' => $user_id, 'reference_id' => $item_id, 'reference_table' => 'seller_books', 'credits' => $points, 'purpose' => $point_type, 'action' => $action, 'trans_id' => $trans_id];
            $this->db->insert(TBL_POINTSYSTEM, $insert_data);

            // echo $this->db->last_query();die("wprlomg jere");
            // }

        }
        // return false;

    }
    
}
?>
