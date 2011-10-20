<?php
class App_FacultyService {
	protected $db;
   
   
 	function __construct(){
	     	$options = array(
   	 	    'host' => 'localhost', 
   		    'username' => 'cstclair', 
   		    'password' => 'cstclair',
   		    'dbname' => 'matrix_stclair'
	   	);
   	
		$this->db = Zend_Db::factory('PDO_MYSQL', $options);
		Zend_Db_Table_Abstract::setDefaultAdapter($this->db);
 		$this->user = new App_UserTable();
		$this->artifact = new App_ArtifactTable();
   		$this->artifact_indicator_rating = new App_ArtifactIndicatorRatingTable();
   		$this->cover_sheet = new App_CoverSheetTable();
   		$this->role = new App_RoleTable();
   		$this->standard = new App_StandardTable();
   		$this->indicator = new App_IndicatorTable();
	}

        // Used by:GetArtifactsForFacultyIDWithOrderAndStatus
 	private function PrivateGetAllArtifactsForFacultyID($id)
   	{   	$select = $this->user->select()    							
   			->from(array('u' => 'user'),'user_id')
   			->from(array('a' => 'artifact'))
   			->join(array('su' => 'user'), 'a.student_id = su.user_id', array('su.last_name as student_last_name', 'su.first_name as student_first_name', '*'))
			->join(array('air'=>'artifact_indicator_rating'), 'a.artifact_id = air.artifact_id',
			       array('DATE_FORMAT(air.timestamp, \'%m/%d/%Y %h:%i %p\')as submitted_timestamp', '*'))
   			->join(array('c'=>'course'), 'a.course_id = c.course_id', array('course_number'))
   			->join(array('i'=> 'indicator'), 'i.indicator_id = air.indicator_id')
   			->join(array('s'=> 'standard'), 'i.standard_id = s.standard_id')
   			->join(array('cs' => 'cover_sheet'), 'cs.artifact_id = air.artifact_id')
			->join(array('csr' => 'cover_sheet_rating'), 'air.artifact_indicator_rating_id =
			       csr.artifact_indicator_rating_id')
			->where('u.user_id = ?', $id)
   			->setIntegrityCheck(false); 
   	
   		return $select;				   			
   	}
   	
   	/**
   	 * MD and PV
   	 * Public function to get all pending artifacts associated with a specific faculty member, allows you to 
   	 * specify sort order and artifact status
	 * @param int $id - faculty id number
   	 * @param string $order - column in database to sort by
   	 * @param int $status - the status column from artifact indicator status
   	 */
	// Modified C St.Clair
	// Used in: LandingController
	public function GetPendingArtifacts($id, $order, $status)
   	{   	 
   		$select = $this->PrivateGetAllArtifactsForFacultyID($id)
   						->where('air.status_code = ?', $status)				
   						->order($order)  	
   						->setIntegrityCheck(false); 
   						   						
		return $this->user->fetchAll($select); 
   	}
	
   	// Used in: LandingController
	public function GetReviewedArtifacts($id, $order, $status)
   	{   	 
   		$select = $this->PrivateGetAllArtifactsForFacultyID($id)
   						->where('air.status_code = ?', $status)				
   						->order($order)  	
   						->setIntegrityCheck(false); 
   						   						
		return $this->user->fetchAll($select); 
   	}
	
	// C StClair
	// Used in: FacultyevaluateController
	public function GetRatings(){
		$select = $this->db->select()
   			->from(array('r' => 'rating'));
			
   		return $this->db->fetchAll($select);
	}


   	/**
   	 * MD and PV
	 * Updates the rating and comments for a specific artifact and reflective/cover letter, based
	 * on what was in the approve artifact form. Updates both individually.
	 * modified C St.Clair
	 * used in FacultyevaluateController
   	 */
   	public function UpdateRatingForArtifactAndCover(
			$artifactRating, $artifactComment,
			$coverRating, $coverComment, $airid)
   	{	
  
		//Update the artifact's rating   		
   		$params = array('artifact_rating' => $artifactRating,
				'artifact_comment' => $artifactComment,
				'status_code' => 3);
   		$where = "artifact_indicator_rating_id = " . $airid;
		$this->db->update('artifact_indicator_rating', $params, $where);   	 	
  	 	   	 	
		//Update the cover sheet's rating   	
   	 	$params = array('cover_rating' => $coverRating,
				'cover_comment' => $coverComment);
   		$where = "artifact_indicator_rating_id = " . $airid;
		$this->db->update('cover_sheet_rating', $params, $where);
   	 	
   	 	return true;
   	}
   	
   	// Used in: SubmitartifactController
	public function GetAllFaculty(){
		$select = $this->db->select()
			       ->from('user', array(
				'name' => new Zend_Db_Expr("CONCAT(first_name, ' ', last_name)")
				))
				->where('role = ?', 'F');
		$result = $this->db->fetchAll($select);
		return $result;
	}
	
	// C StClair
	// Used in: SubmitartifactController
	public function UpdateArtifactRating($air, $evaluator)
	{
		// update artifact indicator rating to reflect submitted
		$params = array('status_code' => 2,
				'artifact_evaluator' => $evaluator);
		$where = "artifact_indicator_rating_id = " . $air;
		$this->db->update('artifact_indicator_rating', $params, $where);   	 	
  	 	
		
	}
	// C StClair
	// Used in: IndicatorsController
	public function GetFacultyByName($name){
		$fname = strtok($name, " ");
		$lname = strtok(" ");
		$select = $this->db->select()
			       ->from('user')
			       ->where('last_name = ?', $lname)
			       ->where('first_name = ?', $fname);
		$result = $this->db->fetchAll($select);
		return $result;
	}
   
}


		