<?php

class File implements FileInterface
{
	/**
	* @return array
	*/	
	private $fileName = "";
	
	private $time = "";
	private $size = 0;
	
	public function getFileList() {		
						
					$dir    = getcwd().'/filesystem/';
					$files = scandir($dir);
					                  
					return $files;
				
				}
	
	
	/**
	* @return string
	*/			
	public function getName() {		
              
              return $this->fileName;
				
				}
				
	/**
    * @param string $name
    *
	* @return $this
	*/
	
	public function setName($name) {
					 $this->fileName = $name;
				 }
				 
	 
	/**
   * @return int
   */
   
	public function getSize() {
		
		$size = array('B','kB','MB','GB','TB','PB','EB','ZB','YB');
		$factor = floor((strlen($this->size) - 1) / 3);
		return sprintf("%1.2f", $this->size / pow(1024, $factor)) . @$size[$factor];
				  
	}
  
  /**
   * @param int $size
   *
   * @return $this
   */
   
   
  public function setSize($size) {
	  
	  $this->size = $size;
	  return $this;
			  
  }


  /**
   * @return string
   */
   
  public function getPath(){

	  return getcwd().'/filesystem/'.$this->getName();
	 
  }
 

  /**
   * @param String
   *
   * @return $this
  */
  public function setTime($time){
  	$this->time = $time;
	return $this;
  }
  
  /**
   * @return String
  */
  public function getTime(){
  	return $this->time;
  }

}
?>