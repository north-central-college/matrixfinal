<?php
class App_FacultyService {
	protected $db;
   
   	/** 
    * faculty Zend table
    * Enter description here ...
    * @var unknown_type
    */
   	protected $user;
   
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
   		$this->artifact_rating = new App_ArtifactRatingTable();
   		$this->artifact_indicator_status = new App_ArtifactIndicatorStatusTable();
   		$this->reflective_statement = new App_ReflectiveStatementTable();
   		$this->reflective_statement_rating = new App_ReflectiveStatementRatingTable();
   		$this->reflective_statement_status = new App_ReflectiveStatementStatusTable();
   		$this->role = new App_RoleTable();
   		$this->standard = new App_StandardTable();
   		$this->indicator = new App_IndicatorTable();
	}

   
 	private function PrivateGetAllArtifactsForFacultyID($id)
   	{   	 
   		$select = $this->user->select()    							
   						->from(array('u' => 'user'),'user_id')
   						->from(array('s' => 'user'), array('s.last_name as student_last_name', 's.first_name as student_first_name', '*'))  
   						->from(array('a' => 'artifact'), array('a.artifact_title as artifact_filename', 'a.artifact_id as this_artifact_id', '*'))
   						->from(array('ar' => 'artifact_rating'), array('ar.rating_code as artifact_rating_code', 'ar.comments as artifact_comments', '*'))
   						->from(array('r' => 'reflective_statement'), array('r.filename as reflective_filename', '*'))
						->from(array('rr' => 'reflective_statement_rating'), array('rr.rating_code as reflective_rating_code', 'rr.comment as reflective_comment', '*'))
   						->from(array('ais' => 'artifact_indicator_status'), array('DATE_FORMAT(ais.timestamp, \'%m/%d/%Y %h:%i %p\')as submitted_timestamp', '*'))
   						->join(array('c' => 'course'),
   							       'ar.rating_user_id = u.user_id && 
   							   	ar.artifact_id = a.artifact_id && 
   							   	ais.artifact_id = a.artifact_id &&
   							   	ar.indicator_id = ais.indicator_id &&  							   	
   							   	a.artifact_id = r.artifact_id &&
   							   	a.course_id = c.course_id &&  
   							   	ais.indicator_id = r.indicator_id && 							
  							        a.student_id = s.user_id')
   						->where('u.user_id = ?', $id)
   						->setIntegrityCheck(false); 
   	
   		return $select;				   			
   	}
   	
   	/**
   	 * MD and PV
   	 * Public function to get detailed information about a specific artifact
	 * NOTE: should use a registry for the $id, but for now we are just passing the integer id variable
	 * Usage: FacultyapproveartifactsController indexAction
   	 * @param int $id - faculty id number
   	 * @param int $artifactid - artifact id number
   	 */
   	public function GetArtifactByID($id, $artifactid)
   	{ 	 
   		$select = $this->PrivateGetAllArtifactsForFacultyID($id)
   						->where('a.artifact_id = ?', $artifactid)	
   						->setIntegrityCheck(false); 				   						
		return $this->user->fetchAll($select); 
   	}
   	
   	/**
   	 * MD and PV
   	 * Public function to get all artifacts associated with a specific faculty member
	 * NOTE: should use a registry for the $id, but for now we are just passing the integer id variable
	 * Usage: Not used
   	 * @param int $id - faculty id number
   	 */
	public function GetAllArtifactsForFacultyID($id)
   	{   	 
   		$select = $this->PrivateGetAllArtifactsForFacultyID($id)
   						->setIntegrityCheck(false); 				   						
		return $this->user->fetchAll($select); 
   	}
   	
   	/**
   	 * MD and PV
   	 * Public function to get all artifacts associated with a specific faculty member, allows you to 
   	 * specify sort order and artifact status
	 * NOTE: should use a registry for the $id, but for now we are just passing the integer id variable
	 * Usage: FacultylandingController indexAction
   	 * @param int $id - faculty id number
   	 * @param string $order - column in database to sort by
   	 * @param int $status - the status column from artifact indicator status
   	 */
	public function GetArtifactsForFacultyIDWithOrderAndStatus($id, $order, $status)
   	{   	 
   		$select = $this->PrivateGetAllArtifactsForFacultyID($id)
   						->where('ais.status_code = ?', $status)				
   						->order($order)  	
   						->setIntegrityCheck(false); 
   						   						
		return $this->user->fetchAll($select); 
   	}
   	

   	/**
   	 * MD and PV
   	 * Returns an associative array of descriptions from the rating table
	 * Usage: ApproveArtifactForm
   	 */
   	public function GetRatingDescriptions()
   	{
   		$select = $this->user->select()    							
   						->from('rating', 'description')   						
   						->setIntegrityCheck(false); 
   		$rowset = $this->user->fetchAll($select)->toArray();
   		return $rowset;
   	}
   	
   	/**
   	 * MD and PV
   	 * Returns an associative array of rating_codes from the rating table
	 * Usage: ApproveArtifactForm
   	 */
   	public function GetRatingCodes()
   	{
   		$select = $this->user->select()    							
   						->from('rating', 'rating_code')   						
   						->setIntegrityCheck(false); 
   		$rowset = $this->user->fetchAll($select)->toArray();
   		return $rowset;
   	}
   	
   	/**
   	 * MD and PV
	 * Updates the rating and comments for a specific artifact and reflective/cover letter, based
	 * on what was in the approve artifact form. Updates both individually.
	 * Usage: FacultyapproveartifactsController processAction
   	 * @param int $artifactratingid - artifact_rating_id from artifact_rating table
   	 * @param int $reflectiveratingid - reflective_statement_id from reflective_statement table
   	 * @param string $artifactComment - artifacts comments from the approve artifact form
   	 * @param string $artifactRating - artifact rating from the approve artifact form
   	 * @param string $coverLetterComment - reflective comments from the approve artifact form
   	 * @param string $coverLetterRating - reflective rating from the approve artifact form
   	 * @param bool $shouldSubmit - if the comments and rating should be submitted or just saved
   	 */
   	public function SaveArtifactApproveFormWithStatus($artifactratingid, $reflectiveratingid, 
   													$artifactComment, $artifactRating, 
   													$coverLetterComment, $coverLetterRating, 
   													$shouldSubmit)
   	{	
   		//Update the artifact comments and rating   		
   		$artifactparams = array('comments' => $artifactComment,
   						'rating_code' => $artifactRating);
   		$artifactwhere = "artifact_rating_id = " . $artifactratingid;
   		
   		//if should submit is set change the status of this artifact indicator status to evaluated
   	 	if($shouldSubmit)
        {
        	$artifactparams[0]['ais.status_code'] = 3;
        }
        
   	 	$this->db->update('artifact_rating', $artifactparams, $artifactwhere);   	 	
  	 	   	 	
   		//Update the reflective statement comments and rating   	
   	 	$reflectiveparams = array('comments' => $coverLetterComment,
   						'rating_code' => $coverLetterRating);
   		$reflectivewhere = "reflective_statement_id = " . $reflectiveratingid;
   		
   	 	$this->db->update('reflective_statement', $reflectiveparams, $reflectivewhere);
   	 	
   	 	return true;
   	}
   	
   	
   	
   	
	public function SelectUser($username, $password){
   		$select = $this->db->select()->from('user', 'username')->where("username = '$username'");
   		$stmt = $select->query();
   		
   		
   		$select2 = $this->db->select()->from('user', 'password')->where("username = '$username'");
   		$stmt2 = $select2->query();
   		
   		
   		/*if (count($stmt2->fetchAll()) == 0)
   		{
   			return array('0' => 'invalid');
   		}*/
   		
   		return $stmt2->fetchAll();
	}
   
	
   
	/**
	 * KG, PA
	 * This function takes a username, then queries the database and returns the role
	 * of that user.
	 * Call this function only after confirming that the user exists in the database.
	 * This is done by calling the ValidUser function above before calling this function.
	 */
	public function GetUserRole($username){
   		$select = $this->db->select()->from('user', 'role')->where("username = '$username'");
   		$stmt = $select->query();
   		
   		$roleEntry = $stmt->fetchAll();
   		
   		$roleArray = $roleEntry[0];
   		
   		return $roleArray['role'];		
	}
	
	public function GetAllFaculty(){
		$select = $this->db->select()
						->from('user')
						->where('role = ?', 'F');
		$result = $this->db->fetchAll($select);
		return $result;
	}
}


		