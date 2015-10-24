<?php
include_once("autoload.php");
include_once("common/header.php");
$adminDetails = new AdminController();


$fileObj = new File();
$fileSystemObj = new FileSystem();


$dirList = $fileSystemObj->dirList('filesystem');




if(isset($_POST['submit']) && isset($_POST['filename']))
{	
	$fileObj->setName($_POST['filename']);
	performWalk($fileSystemObj, $fileObj);
	header('location:files');
	
}

if(isset($_GET['del']) && !empty($_GET['del']))
{	
	$file = $_GET['del'];
	
	if(file_exists($file)){
		
		$fileObj->setName( basename($file) );	
		$fileSystemObj->deleteFile($fileObj);
		header('location:files');
	
	}
	
}


if(isset($_POST['rename']) && !empty($_POST['filename']))
{	
	$newName = $_POST['filename'];
	$path = $_POST['filepath'];
	
	if(file_exists($path)){		
		$fileObj->setName( basename($path) );	
		$fileSystemObj->renameFile($fileObj,$newName);
		
		header('location:files');
	
	}
	
}

$adminData = $adminDetails->viewAdminDetail();

$id = !empty($adminData->id) ? "" : $adminData->id;
$firstname = empty($adminData->firstname) ? "" : $adminData->firstname;
$lastname = empty($adminData->lastname) ? "" : $adminData->lastname;
$email = empty($adminData->id) ? "" : $adminData->email;


?>
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">Files</h1>
            </div>
            <!-- /.col-lg-12 -->
        </div>
        <div class="row">
                
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-heading clearfix">
                            Files Listing
                            <button type="button" class="btn btn-primary pull-right" data-toggle="modal" data-target="#myModal">Add New File</button>
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <div class="dataTable_wrapper">
                                <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                                    <thead>
                                        <tr>
                                            <th>S. No</th>
                                            <th>Name</th>
                                            <th>Size</th>
                                            <th>Path</th>
                                           
                                            <th>Time</th>
                                            <th>Action</th>
                                            
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php 
									if(count( $dirList)>0):
									$i=1;
									foreach($dirList as $file) : ?>                                   
                                    
                                        <tr class="odd gradeX">
                                            <td><?php echo $i; ?></td>
                                            <td><?php echo $file->getName(); ?></td>
                                            <td><?php echo $file->getSize(); ?></td>
                                            <td ><?php echo $file->getPath(); ?></td>                                            
                                            <td><?php echo $file->getTime(); ?></td> 
                                            
                                             <td><button class="btn btn-primary pull-left rname" data-toggle="modal" data-path="<?php echo $file->getPath(); ?>" data-target="#rename" data-name="<?php echo $file->getName(); ?>">Rename</button>
                                             	 <a class="btn pull-right btn-danger" href="?del=<?php echo $file->getPath(); ?>">Delete</a>
                                             </td>                                            
                                        </tr> 
                                    <?php $i++; ?>
                                    <?php endforeach; ?>  
                                    <?php endif; ?>                                    
                                    </tbody>
                                </table>
                            </div>
                            <!-- /.table-responsive -->
                            
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                </div>
            
            </div>
            
<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="top:200px;">
    <div class="modal-content">
      <div class="modal-header">     
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Upload CSV</h4>
      </div>
       <form method="post" enctype="multipart/form-data" >
          <div class="modal-body">
         
         	<input type="text" name="filename" />
            <input type="hidden" name="filepath" />
         
          </div>
          <div class="modal-footer">            
           <button type="submit" class="btn btn-primary" name="submit">Save changes</button>
          </div>
      </form>
    </div>
  </div>
</div>


<!-- Modal -->
<div class="modal fade" id="rename" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="top:200px;">
    <div class="modal-content">
      <div class="modal-header">     
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Upload CSV</h4>
      </div>
       <form method="post" enctype="multipart/form-data" >
          <div class="modal-body">
         
         	<input type="text" id="renameMe" name="filename" />
         	<input type="hidden" id="fpath" name="filepath" />
          </div>
          <div class="modal-footer">            
          <button type="submit" name="rename" class="btn btn-primary" name="submit">Save changes</button>
          </div>
      </form>
    </div>
  </div>
</div>

<script type="text/javascript">
$(document).ready(function(e) {
    $('.rname').click(function(e) {
        $('#renameMe').val($(this).attr('data-name'));
		$('#fpath').val($(this).attr('data-path'));
    });
});
</script>

<?php




include_once("common/footer.php");
?>