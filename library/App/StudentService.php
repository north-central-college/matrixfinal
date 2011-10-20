<?php 

class App_StudentService {
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
		$this->cover_sheet_rating = new App_CoverSheetRatingTable();
   		$this->role = new App_RoleTable();
   		$this->standard = new App_StandardTable();
   		$this->indicator = new App_IndicatorTable();
   		$this->course = new App_CourseTable();
   		
	}
  
    // C StClair
    // Used in: LandingController
    // Used in: StandardsController
    public function GetRoleInfo($role_id)
	{
		$select = $this->role->select()->where('role_code = ?', $role_id);
		return $this->role->fetchRow($select);
	}
    
    // Used in: StudentartifactController
    // Used in: FacultyreviewController
    public function GetArtifact($id)
    {
   		$select = $this->artifact->select()->where('artifact_id = ?', $id);
   		return $this->artifact->fetchRow($select);
    }
   
    // Used in: FacultyreviewController
    public function GetReflective($id)
    {
   		$select = $this->reflective_statement->select()->where('reflective_statement_id = ?', $id);
   		return $this->reflective_statement->fetchRow($select);
    }
   
    // C StClair
    // Used in: StandardsController
    // Used in: StudentartifactController
    public function GetAllArtifacts($id){
		$select = $this->db->select()
   			->from(array('a' => 'artifact'))
			->join(array('u'=>'user'), 'a.student_id = u.user_id')
   			->where('u.user_id = ?', $id);
			
   		return $this->db->fetchAll($select);
    }
    
    // C StClair
    // Used in: ArtifactlinkController
    public function GetAllArtifactsNotLinkedtoIndicator($id, $standard, $indicator)
    {		
	
		$notinSelect = $this->db->select()
				->from(array('air' => 'artifact_indicator_rating'), array('artifact_id'))
				->where('air.indicator_id = ?', $indicator);
		
		$notinResult = $this->db->fetchCol($notinSelect);
		
   		$select = $this->db->select()
   			->from(array('a' => 'artifact'))
			->join(array('cs' => 'cover_sheet'), 'a.artifact_id = cs.artifact_id')
			->join(array('u'=>'user'), 'a.student_id = u.user_id')
   			//->where('a.artifact_id NOT IN ?', $notinResult)
   			->where('u.user_id = ?', $id);
			
   		return $this->db->fetchAll($select);
    }

    /**
    * JD
    * A public funtion returning information about a single artifact, including
    * all the tables about its status and rating.
    * Should rely on session variables for the $id. Hard-coded to user 1 for now.
    * @param int $id - student id number
    * @param int $aid - artifact id number
    */
    // Used in: StudentartifactController
    public function GetAllArtifactDetails($id, $aid)
    {
   		$select = $this->db->select()
   			->from(array('a' => 'artifact'), array('artifact_id', 'artifact_title', 'description', 'a.timestamp as upload_timestamp'))
   			->from(array('ais' => 'artifact_indicator_rating'), array('artifact_id', 'indicator_id', 'status_code', 'ais.timestamp as link_timestamp'))
   			->from(array('ar' => 'artifact_rating'), array('artifact_rating_id', 'artifact_id', 'indicator_id', 'rating_user_id', 'rating_code', 'ar.timestamp as eval_timestamp'))
   			->join(array('u'=>'user'), 'a.student_id = u.user_id &&
   					ais.artifact_id = a.artifact_id &&
   					ar.artifact_id = ais.artifact_id &&
   					ar.indicator_id = ais.indicator_id')
   			->where('u.user_id = ?', $id)
   			->where('a.artifact_id = ?', $aid);
   		return $this->db->fetchAll($select);
    }
   
    //Database function to retrieve the uploaded artifacts for the user with the given id number
    //will return only those records that are in the artifact table but NOT in the
    //artifact_indicator_table, meaning those that are not yet linked
    //will return only the five most recent uploads
    // Modified C StClair
    // Used in: LandingController
    public function GetUploads($student_id)
	{
   		$selectAIR = $this->artifact_indicator_rating->select()
   			->from(array('air' => 'artifact_indicator_rating'),array('air.artifact_id'));

   		$select = $this->db->select()
			->from(array('a' => 'artifact'))
			->joinLeft(array('c'=>'course'), 'a.course_id = c.course_id')
			->where('student_id = ?', $student_id)
   			->where('artifact_id NOT IN (?)', $selectAIR)
   			->order('artifact_id DESC')
   			->limit(5);
   		return $this->db->fetchAll($select);
	}
   
     //Database function to retrieve the linked artifacts for the user with the given id number
     //will return only those records that are in the artifact_indicator_rating table 
     //and have a status of '1' (linked) but not submitted
     //will return only the five most recent links
     // Modified C StClair
     // Used in: LandingController
     public function GetLinkedArtifacts($student_id)
	{
   		$select = $this->db->select()
   			->from(array('a' => 'artifact'), array('artifact_id', 'artifact_title'))
   			->join(array('air'=>'artifact_indicator_rating'), 'a.artifact_id = air.artifact_id')
   			->join(array('c'=>'course'), 'a.course_id = c.course_id')
   			->join(array('i'=> 'indicator'), 'i.indicator_id = air.indicator_id')
   			->join(array('s'=> 'standard'), 'i.standard_id = s.standard_id')
   			->where('air.status_code = ?', '1')
   			->where('a.student_id = ?', $student_id)
   			->order('artifact_indicator_rating_id DESC')
   			->limit(5);
   			
		$result = $this->db->fetchAll($select);
		return $result;
	}
   
     //Database function to retrieve the submitted artifacts for the user with the given id number
     //will return only those records that are in the artifact_indicator_rating table 
     //and have a status of 'S' (submitted)
     //will return only the five most recent submissions
     // modified C StClair
     // Used in: LandingController
     public function GetSubmittedArtifacts($student_id)
	{
   		$select = $this->db->select()
   			->from(array('a' => 'artifact'), array('artifact_id', 'artifact_title'))
   			->join(array('air'=>'artifact_indicator_rating'), 'a.artifact_id = air.artifact_id')
   			->join(array('c'=>'course'), 'a.course_id = c.course_id')
   			->join(array('i'=> 'indicator'), 'i.indicator_id = air.indicator_id')
   			->join(array('s'=> 'standard'), 'i.standard_id = s.standard_id')
   			->join(array('u'=>'user'), 'air.artifact_evaluator = u.user_id')
   			->where('air.status_code = ?', '2')
   			->where('a.student_id = ?', $student_id)
   			->order('artifact_indicator_rating_id DESC')
   			->limit(5);
   			
   		$result = $this->db->fetchAll($select);
		return $result;
     }
	
      //Database function to retrieve the evaluated artifacts for the user with the given id number
      //will return only those records that are in the artifact_indicator_rating table 
      //and have a status of '3' (evaluated)
      //will return only the five most recent evaluations
      // Used in: LandingController
      public function GetEvaluatedArtifacts($student_id)
	{
		$select = $this->db->select()
   			->from(array('a' => 'artifact'), array('artifact_id', 'artifact_title'))
   			->join(array('air'=>'artifact_indicator_rating'), 'a.artifact_id = air.artifact_id')
			->join(array('csr' =>'cover_sheet_rating'),
				     'air.artifact_indicator_rating_id = csr.artifact_indicator_rating_id')
   			->join(array('c'=>'course'), 'a.course_id = c.course_id')
   			->join(array('i'=> 'indicator'), 'i.indicator_id = air.indicator_id')
   			->join(array('s'=> 'standard'), 'i.standard_id = s.standard_id')
   			->join(array('u'=>'user'), 'air.artifact_evaluator = u.user_id')
   			->where('air.status_code = ?', '3')
   			->where('a.student_id = ?', $student_id)
   			->order('air.artifact_indicator_rating_id DESC')
   			->limit(5);
   			
   		$result = $this->db->fetchAll($select);
		return $result;
	}
	
       // Used in: StandardsController
       public function GetStandardsbyProgram($program)
	{
		$select = $this->standard->select()
		      ->where('program = ?', $program);
		
		return $this->standard->fetchAll($select);
	}
	
	// C StClair
	// Used in: IndicatorsController
	public function GetIndicatorsbyStandard($standard_id)
	{
		$select = $this->db->select()
		->from(array('s' => 'standard'))
		->join(array('i' => 'indicator'), 's.standard_id = i.standard_id')
		->where('s.standard_id = ?', $standard_id)
		;
		
		return $this->db->fetchAll($select);
	}
	
	// C StClair
	// Used in: ArtifactsbyindicatorController
	public function GetArtifactsbyIndicator($student_id, $indicator_id)
	{
		$select = $this->db->select()
   			->from(array('a' => 'artifact'), array('artifact_id', 'artifact_title'))
   			->join(array('ais'=>'artifact_indicator_rating'), 'a.artifact_id = ais.artifact_id')
   			->where('ais.indicator_id = ?', $indicator_id)
   			->where('a.student_id = ?', $student_id);
   			
   		$result = $this->db->fetchAll($select);
		return $result;
	}
	
	// C StClair
	// Used in: IndicatorsController
	public function GetAllIndicatorsArtifactsbyStandard($student_id, $standard_id)
	{
		
		$select = $this->db->select()
   			->from(array('i' => 'indicator'))
			->join(array('air'=>'artifact_indicator_rating'), 'i.indicator_id = air.indicator_id')
			->join(array('a' => 'artifact'), 'air.artifact_id = a.artifact_id')
   			->join(array('cs'=>'cover_sheet'), 'cs.artifact_id = a.artifact_id')
   			->joinLeft(array('csr'=>'cover_sheet_rating'), 'csr.cover_id = cs.cover_id
				   && air.artifact_indicator_rating_id = csr.artifact_indicator_rating_id')
   			->joinLeft(array('u'=>'user'), 'air.artifact_evaluator = u.user_id',
				array('artifact_evaluator_first'=>'u.first_name', 'artifact_evaluator_last'=>'u.last_name'))
   			->join(array('c'=>'course'), 'a.course_id = c.course_id')
   			->where('i.standard_id = ?', $standard_id)
		        ->where('a.student_id = ?', $student_id)
		;
		
 			
   		$result = $this->db->fetchAll($select);
		return $result;
	}
		
	// C StClair
	// Used in: ArtifactsbyindicatorController
	public function GetIndicatorNumbyIndicatorId($indicator_id)
	{
		$select = $this->indicator->select()
		->where('indicator_id = ?', $indicator_id);
		return $this->indicator->fetchRow($select);
	}
	
	// C StClair
	// Used in: StudentartifactController
	public function NewArtifactAndCover($atitle, $adescription, $afilename, $amediaextension, $course,
				$ctitle, $cdescription, $cfilename, $cmediaextension, $userid)

	{
   		$params = array('artifact_title' => $atitle,
   				'artifact_description' => $adescription,
   				'artifact_filename' => $afilename,
				'artifact_media_extension' => $amediaextension,
				'course_id' => $course,
   				'student_id' => $userid);
   					
   		$newart = $this->artifact->insert($params);
		
		$params = array('cover_title' => $ctitle,
				'artifact_id' => $newart,
   				'cover_description' => $cdescription,
   				'cover_filename' => $cfilename,
   				'cover_media_extension' => $cmediaextension,
				);
   					
   		$this->cover_sheet->insert($params);
	}
	// C StClair
	// Used in: IndicatorsController
	public function NewArtifactIndicator($artifact_id, $indicator_id, $cover_id){
		$params = array('artifact_id' => $artifact_id,
				'indicator_id' => $indicator_id,
				'status_code' => '1');
		$newair = $this->artifact_indicator_rating->insert($params);
		
		$params = array('cover_id' => $cover_id,
				'artifact_indicator_rating_id' => $newair);
		$this->cover_sheet_rating->insert($params);	
	}
   
	// C StClair
	// Used in: IndicatorsController
	public function RemoveArtifactIndicatorLink($air){
		// delete artifact indicator rating record
		$where = array();
		$where[] = $this->db->quoteInto('artifact_indicator_rating_id = ?', $air);
		$this->artifact_indicator_rating->delete($where);
		
		// delete cover sheet rating record also
		$this->cover_sheet_rating->delete($where);
	}	
	// C StClair
	// Used in: StudentartifactController
	public function GetCourse($course_number){
		$select = $this->db->select()
			       ->from('course', array('course_id'))
			       ->where('course_number = ?', $course_number);
		$result = $this->db->fetchRow($select);
		return $result;
	}
	
	// C StClair
	// Used in: StudentartifactController
	public function GetAllCourses(){
		$select = $this->db->select()
			       ->from('course', array('course_id', 'course_number'));
		$result = $this->db->fetchAll($select);
		return $result;
	}
}   
  
