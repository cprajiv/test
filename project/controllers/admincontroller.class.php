<?php
class AdminController extends AdminModel{
	
	public function __construct(){
		parent::__construct();
	}
	
	public function logInAdmin($credentials){
	   $result = $this->verifyAdminCredentials($credentials);
	   
	   if($result){
		   session_regenerate_id(true);
		   $_SESSION['TR_IS_ADMIN']  = 	true;
		   $_SESSION['TR_ADMIN_ID']	 =	$result->id;
		   $_SESSION['TR_ADMIN_NAME']=	$result->firstname;
		   return true;
	   }
	   return false;
	}
	
	public function viewAdminDetail(){		
		return $this->getAdminDetails();
	}
	
	public function updateAdminDetails($adminDetails){		
		if(empty($adminDetails)) return false;
		
		
		$result = $this->editAdminDetails($adminDetails);
		if($result!=false){
			header('Location:myaccount');
			return true;
		}
		return false;
	}
	
	public function uploadAgents($csvData){		
		if(empty($csvData)) return false;		
		
		$result = $this->saveAgents($csvData);
		if($result!=false){
			header('Location:agents');
			return true;
		}
		return false;
	}
	public function viewAgentDetails(){		
		return $this->getAgentDetails();
	}
	public function viewQouteDetails(){		
		return $this->getQouteDetails();
	}
	
}
?>