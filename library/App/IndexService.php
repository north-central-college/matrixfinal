<?php

class App_IndexService {
   
	protected $db;
    protected $user;
    protected $role;
    
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
		$this->role = new App_RoleTable();
	}
	//Database function to retrieve the full name of the user with the given id number
	public function GetUserInfo($student_name)
	{
		$select = $this->user->select()->where('username = ?', $student_name);
		return $this->user->fetchRow($select);
	}
	
	
}


