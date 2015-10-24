<?php
include_once("autoload.php");

if(!empty($_SESSION['TR_IS_ADMIN'])){ header("Location:index");}
include_once("common/header.php");


if(isset($_POST['email']) && isset($_POST['password'])){
	$_POST = $FormHelper->stripAllTagsFromArray($_POST,true);
	$admin_controller = new AdminController();
	$login_result = $admin_controller->logInAdmin($_POST);
	if($login_result===true){
	 	header("Location:index");
	}else{
		$error = 'Invalid email or Password';
	}
	
//	$error = $admin_controller->logInAdmin($_POST)==false ? 'Invalid email or Password' : '';
}

?>
<div class="container">
        <div class="row">
            <div class="col-md-4 col-md-offset-4">
                <div class="login-panel panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Please Sign In</h3>
                    </div>
                    <div class="panel-body">
                        <form role="form" method="post" name="login-form" id="login-form">
                            <fieldset>
                                <div class="form-group">
                                    <input class="form-control" placeholder="E-mail" name="email" type="email" id="email" autofocus required>
                                </div>
                                <div class="form-group">
                                    <input class="form-control" placeholder="Password" name="password" type="password" id="password" value="" required>
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input name="remember" type="checkbox" value="1">Remember Me
                                    </label>
                                </div>
                                <!-- Change this to a button or input when using this as a form -->
                                <button type="submit" class="btn btn-lg btn-success btn-block submit-login">Login</button>
                                <?php if(!empty($error)){
									echo '<div class="checkbox alert alert-danger login-error text-center">
									'.$error.'</div>';
								}else{
									echo '<div class="checkbox alert alert-danger login-error text-center" style="display:none"></div>';
								}?>
                            </fieldset>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
		$(document).ready(function(e) {
            $("button.submit-login").click(function(event){
				$("div.login-error").html('').hide(); 
				var error  = Array();
				var mail   = $.trim($("#email").val());
				var pass   = $.trim($("#password").val());
				var pattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
				
				if(pattern.test(mail)==false)	
					error.push('<p>Please provide a valid email address </p>');
				if(pass=='')
				   error.push('<p>Please enter the password</p>');
				
				if(error.length==0){
				  $("form#login-form").submit();
				}else{
				  var msg = '';
				  $.each(error,function(index,value){
					 msg = msg+error[index];
				  })
				  $("div.login-error").html(msg).show(); 
				}
				event.preventDefault();
			 });
        });
	</script>
<?php
include_once("common/footer.php");
?>