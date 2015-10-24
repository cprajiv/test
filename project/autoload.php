<?php
 //error_reporting(0);
 ob_start();
 if(!session_id()) session_start();
 include 'common/constants.php';
 
 define ('__SITE_ADMIN_PATH',realpath(dirname(__FILE__)));
 include __SITE_ADMIN_PATH . '/class/db/ez_sql_mysql.php';
 
 $admin_directory = DIRECTORY_SEPARATOR.'admin';
 define('__SITE_PATH',str_replace($admin_directory,'',__SITE_ADMIN_PATH)); 
 
 function __autoload($class_name) {
    $filename 		= strtolower($class_name).'.class.php';
    $modelfile 		= __SITE_ADMIN_PATH . '/models/'.$filename;
	$controllerfile = __SITE_ADMIN_PATH . '/controllers/'.$filename;
	$helperfile 	= __SITE_ADMIN_PATH . '/helpers/'.$filename;
	
	
	
    if (file_exists($modelfile) != false){
        include ($modelfile);
    }
	
	if (file_exists($controllerfile) != false){
        include ($controllerfile);
    }
	
	if (file_exists($helperfile) != false){
        include ($helperfile);
    }
	

	return true;
 }
 
if(empty($_SESSION['TR_IS_ADMIN']) && (strpos($_SERVER['PHP_SELF'],'/login')==false)){
	header("Location:login"); exit;
}

	$FileInterface 	= __SITE_ADMIN_PATH.'/inc/FileInterface.php';
	$FileSystemInterface 	= __SITE_ADMIN_PATH.'/inc/FileSystemInterface.php';
	
	$ClassFile 	= __SITE_ADMIN_PATH.'/inc/ClassFile.php';
	$ClassFileSystem 	= __SITE_ADMIN_PATH.'/inc/ClassFileSystem.php';	
	
	
	if (file_exists($FileInterface) != false){
        include ($FileInterface);
    }
	if (file_exists($FileSystemInterface) != false){
        include ($FileSystemInterface);
    }
	if (file_exists($ClassFile) != false){
        include ($ClassFile);
    }
	if (file_exists($ClassFileSystem) != false){
        include ($ClassFileSystem);
    }

$FormHelper = new FormHelper(); //mandatory


function performWalk(FileSystemInterface $FileInterface, FileInterface $fileObj){
   return $FileInterface->createFile($fileObj);
}