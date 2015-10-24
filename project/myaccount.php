<?php
include_once("autoload.php");
include_once("common/header.php");
$adminDetails = new AdminController();
$message="";
if(isset($_POST['submit']))
{
	$_POST = $FormHelper->stripAllTagsFromArray($_POST,true);
		
	$result = $adminDetails->updateAdminDetails($_POST);
	if($result==false){
		$message .= "Unable to update the information, Please try after sometime";
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
                <h1 class="page-header">My Account</h1>
            </div>
            <!-- /.col-lg-12 -->
        </div>
        <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Admin Profile
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-lg-6">
                                    <form role="form" method="post">
                                    	<div class="form-group">
                                        	<div class="errorDiv"><?php echo $message; ?></div>
                                           
                                        </div>
                                        <div class="form-group">
                                            <label>First Name</label>
                                            <input type="text" class="form-control" name="firstname" id="firstname" value="<?php echo $firstname; ?>" autocomplete="off">                                             
                                        </div>
                                        
                                        <div class="form-group">
                                            <label>Last Name</label>
                                            <input type="text" class="form-control" name="lastname" id="lastname" value="<?php echo $lastname; ?>" autocomplete="off">                                            
                                        </div>
                                        
                                        <div class="form-group">
                                            <label>Email Address</label>
                                            <input type="email" class="form-control" name="email" id="email" value="<?php echo $email; ?>" autocomplete="off">
                                            <p class="help-block">Example : john@emample.com</p>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label>Password</label>
                                            <input type="password" class="form-control" name="password" id="password" autocomplete="off">
                                        </div>
                                        
                                        <div class="form-group">
                                            <label>Re-Password</label>
                                            <input type="password" class="form-control" id="cpassword">                                           
                                        </div>                                        
                                        <button type="submit" name="submit" class="btn btn-default submit">Save</button>                                     
                                    </form>
                                </div>                               
                            </div>
                            <!-- /.row (nested) -->
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                </div>
                <!-- /.col-lg-12 -->
            </div>
<script type="text/javascript">
		$(document).ready(function(e) {
            $(".submit").click(function(event){
				$("div.login-error").html('').hide(); 
				var error  = [];
				var firstname   = $.trim($("#firstname").val());
				var lastname   = $.trim($("#lastname").val());
				var email   = $.trim($("#email").val());				
				var pattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
				var password   = $.trim($("#password").val());
				var cpassword   = $.trim($("#cpassword").val());
				
				
				if(firstname=="")
				{
					error.push('<p>Please enter your firstname! </p>');
				}
				if(lastname=="")
				{
					error.push('<p>Please enter your lastname! </p>');
				}
				if(firstname=="")
				{
					error.push('<p>Please enter your firstname! </p>');
				}
				
				if(pattern.test(email)==false)	
				{
					error.push('<p>Please enter a valid email address!</p>');
				}
					
				
				if(password!=cpassword)
				{
					 error.push('<p>Password did not match!</p>');
				}
				if(error.length==0){
				  return true;
				}else{
				  var msg = '';
				  $.each(error,function(index,value){
					 msg = msg+error[index];
				  })
				  $("div.errorDiv").html(msg).show(); 
				  return false;
				}
				event.preventDefault();
			 });
        });
	</script>           
<?php
include_once("common/footer.php");
?>