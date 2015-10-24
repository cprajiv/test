<?php

/**
 * File System Management
 */
class FileSystem implements FileSystemInterface
{
  /**
   * @param FileInterface   $file
   *
   * @return FileInterface
   */
  public function createFile(FileInterface $file ) {	  
	    $path=getcwd().'/filesystem/'.$file->getName();        
        fopen($path, "w");
	  
  }

  /**
   * @param FileInterface $file
   * @param               $newName
   *
   * @return FileInterface
   */
  public function renameFile(FileInterface $file, $newName) {	  
	  rename($file->getPath(), getcwd().'/filesystem/'.$newName);	  
  }

  /**
   * @param FileInterface $file
   *
   * @return bool
   */
  public function deleteFile(FileInterface $file) {	  
	  unlink( $file->getPath() );	  
  }
  
  /**
   * @param FileInterface $file
   *
   * @return int
   */
  public function getFileSize(FileInterface $file) {	  
	  $size=filesize($file);	  
	  return $size;	  
  }
  
  /**
   *
   * @return int
   */
  public function getFileCount(){	  
	  $i = 0; 
	  $dir = getcwd()."/filesystem";
	  if ($handle = opendir($dir)) {
        while (($file = readdir($handle)) !== false){
            if (!in_array($file, array('.', '..')) && !is_dir($dir.$file)) 
                $i++;
        }
    }	
	return $i ;
  }
  
  

 		public function dirList($dir)
        {
			$files  = array();
			
            if ($dir[strlen($dir)-1] != '/') $dir .= '/';

            if (!is_dir($dir)) return array();

            $dir_handle  = opendir($dir);
            $dir_objects = array();
            while ($object = readdir($dir_handle))
                if (!in_array($object, array('.','..')))
                {
					$file = new File();
                    $filename    = $dir . $object;                   
					$file->setName($object);
					$file->setSize(filesize($filename));
					
					$file->setTime( date("d F Y H:i:s", filemtime($filename)) );
					$files[] = $file;
                }

            return $files;
        }

}


?>