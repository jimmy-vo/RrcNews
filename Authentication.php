<?php
 /**
 * Edit.php
 *
 *	Edit, create
 *
 * @author     Jimmy Vo
 * @project    Assignment 4
 */
    session_start();
	if  (!defined("Include_Sql"))	include("Sql.php");	
	if  (!defined("Include_HeaderScript"))	include("HeaderScript.php");		
	HeaderScript::getInstance()->script_reset();	
	HeaderScript::getInstance()->script_PageTitle("Login");

	if (($getLogout = isset($_GET['logout'])? TRUE:FALSE) === FALSE)
	{
		unset($getLogout);
	}
	if (($getRegister = isset($_GET['register'])? TRUE:FALSE) === FALSE)
	{
		unset($getRegister);
	}

	if (isset($getRegister))
	{
		HeaderScript::getInstance()->script_BannerText("Log in");
		HeaderScript::getInstance()->script_BannerLink("Authentication.php");
	}
	else if (isset($getLogout))
	{
		Sql::getInstance()->LoginAnonymous();
		HeaderScript::getInstance()->script_alert("Logged in as Anonymous");
		HeaderScript::getInstance()->script_navigate("index.php");
	}
	else
	{	
		HeaderScript::getInstance()->script_BannerText("Register");
		HeaderScript::getInstance()->script_BannerLink("Authentication.php?register");
		if (($buttonLogin = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_STRING)) !== "Login")
		{
			unset($buttonLogin);
		}
		if (($buttonRegister = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_STRING)) !== "Register")
		{
			unset($buttonRegister);
		}
		if ((($username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING))===FALSE) || (strlen($username)===0))
		{
			unset($username);
		}	
		if ((($password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING))===FALSE) || (strlen($password)===0))
		{
			unset($password);
		}

		if (isset($buttonLogin))
		{	
			if (isset($password) && isset($username) )
			{
				if (Sql::getInstance()->LoginAccount($username, $password) === TRUE)
				{ 		
					HeaderScript::getInstance()->script_alert("Logged in sucessfully");
					HeaderScript::getInstance()->script_navigate("index.php");	
				}
				else
				{ 		
					HeaderScript::getInstance()->script_alert("Wrong username or password");		
				}
			}
		}
		else if (isset($buttonRegister))
		{
			if ((Sql::getInstance()->register($username, $password) === TRUE) &&
			 	(Sql::getInstance()->LoginAccount($username, $password) === TRUE))
			{				
				HeaderScript::getInstance()->script_alert("Register sucessfully");
				HeaderScript::getInstance()->script_navigate("index.php");
			}
			else
			{		
				HeaderScript::getInstance()->script_alert("Register failed");	
			}
		}
	}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<?php include("Header.php");?>	
	<?php if (isset($getRegister)): ?>
		<script  src="Authentication.js"></script>	
	<?php endif  ?>
</head>
<body>
	<?php include("Banner.php");?>
    <div id="wrapper">
		<?php include("menu.php");?>
		<div id="all_blogs" >
			<form id="login" action="Authentication.php" method="post">
					<label for="username">Username</label>
					<p id="username-valid" class="valid">Valid username</p>
					<p id="username-invalid" class="invalid">Username is not available</p>
					<input name="username" id="username" value="<?= isset($_POST["username"])?$_POST["username"]:"" ?>" />
					<label for="password">Password</label>
					<input type="password" name="password" id="password" value="<?= isset($_POST["password"])?$_POST["password"]:"" ?>" />
					<?php if (isset($getRegister)): ?>
						<input class="button" type="submit" name="action" value="Register" />
					<?php else:  ?>
						<input class="button" type="submit" name="action" value="Login"  />
					<?php endif  ?>
			</form>
		</div>
		<div style="clear: both;"></div>
    </div>
</body>
</html>
