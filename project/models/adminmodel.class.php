<?php
class AdminModel {
	
	private $db_table = 'admin';
	
	public function __construct(){
		
	}
		
	public function verifyAdminCredentials($credentials){
		global $mysqldb;
		
		extract($credentials);
		if(empty($email)) return false;
		
		$email    = empty($email) ? '' : strip_tags($email);
		$password = empty($password) ? '' : md5($password);
		
		$result = $mysqldb->get_row($mysqldb->prepare("SELECT * FROM $this->db_table WHERE email=%s AND password=%s",$email,$password));
		return empty($result) ? false : $result;
		
	}	
	public function getAdminDetails(){		
		global $mysqldb;
		return $mysqldb->get_row($mysqldb->prepare("SELECT * FROM $this->db_table WHERE id=%d",1));	
	}
	
	public function editAdminDetails($adminDetails){
		global $mysqldb;
			
		$data = array();
		extract($adminDetails);
		$data	= empty($firstname) ?  array_merge($data,array()) : array_merge($data, array('firstname'=>$firstname));
		$data	= empty($lastname) ? array_merge($data,array()) : array_merge($data, array('lastname'=>$lastname));
		$data	= empty($email) ? array_merge($data,array()) : array_merge($data, array('email'=>$email));
		$data	= empty($password) ? array_merge($data,array()) : array_merge($data, array('password'=>md5($password)));	
		
		$dataString = array();
		for($i=0; $i<count($data); $i++)
		{
			$dataString = array_merge($dataString, array('%s'));
		}
		
		
		return $mysqldb->update( 
			$this->db_table, 
			$data,
			array('id'=> 1), 
			$dataString,
			array('%d') 
		);
		
	}
	
	public function agentsTableTruncate()
	{
		global $mysqldb;
		
		$data = array();
		$mysqldb->query("TRUNCATE TABLE agents");
	}
	
	public function saveAgents($csvData)
	{
		global $mysqldb;		
		$data = array();		
		
		$data = array('OrganizationID' => $csvData[0],
					  'OrganizationCode' => $csvData[1],
					  'OrganizationName' => $csvData[2],
					  'CounselorID' => $csvData[3],
					  'CounselorCode' => $csvData[4],
				      'FirstName' => $csvData[5],
					  'LastName' => $csvData[6],
					  'ContactType' => $csvData[7],
					  'Street1' => $csvData[8],
					  'Street2' => $csvData[9],
					  'City' => $csvData[10],
					  'Prov' => $csvData[11],
					  'PostalCode' => $csvData[12],
					  'EmailAddress' => $csvData[13],
					  'Phone' => $csvData[14],
					  'Fax' => $csvData[15]);		
		
		$dataString = array();
		for($i=0; $i<count($data); $i++)
		{
			$dataString = array_merge($dataString, array('%s'));
		}
		
		return $mysqldb->insert( 
			'agents', 
			$data,			
			$dataString
		);	
	}	
	
	
	public function getAgentDetails(){		
		global $mysqldb;
		return $mysqldb->get_results("SELECT * FROM agents");	
	}
	
	public function getQouteDetails(){		
		global $mysqldb;
		return $mysqldb->get_results("SELECT * FROM qoutes");	
	}
}

?>