<?php
/*

UserFrosting Version: 0.1
By Alex Weissman
Copyright (c) 2014

Based on the UserCake user management system, v2.0.2.
Copyright (c) 2009-2012

UserFrosting, like UserCake, is 100% free and open-source.

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the 'Software'), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:
The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED 'AS IS', WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.

*/

// Request method: POST

require_once("../models/db-settings.php");
require_once("../models/funcs.php");
require_once("../models/languages/en.php");
require_once("../models/class.mail.php");
require_once("../models/class.user.php");
require_once("../models/class.newuser.php");

session_start();

if (!($root_account_config_token = fetchConfigParameter('root_account_config_token'))){
	addAlert("danger", lang("INSTALLER_INCOMPLETE"));
	header('Location: index.php');
	exit();
}

if (fetchUserDetails(NULL, NULL, '1')){
	addAlert("danger", lang("MASTER_ACCOUNT_EXISTS"));
	header('Location: index.php');
	exit();
}

//Forms posted
if(!empty($_POST))
{
	$new_user_id = "";
	$errors = array();
	$email = trim($_POST["email"]);
	$username = trim($_POST["username"]);
	$displayname = trim($_POST["displayname"]);
	$password = trim($_POST["password"]);
	$confirm_pass = trim($_POST["passwordc"]);
	$token = trim($_POST["token"]);
	
	if ($token != $root_account_config_token['value'])
	{
		$errors[] = lang("CONFIG_TOKEN_MISMATCH");
	}
	if(minMaxRange(1,25,$username))
	{
		$errors[] = lang("ACCOUNT_USER_CHAR_LIMIT",array(1,25));
	}
	if(!ctype_alnum($username)){
		$errors[] = lang("ACCOUNT_USER_INVALID_CHARACTERS");
	}
	if(minMaxRange(1,50,$displayname))
	{
		$errors[] = lang("ACCOUNT_DISPLAY_CHAR_LIMIT",array(1,50));
	}
	if(!isValidName($displayname)){
		$errors[] = lang("ACCOUNT_DISPLAY_INVALID_CHARACTERS");
	}
	if(minMaxRange(8,50,$password) && minMaxRange(8,50,$confirm_pass))
	{
		$errors[] = lang("ACCOUNT_PASS_CHAR_LIMIT",array(8,50));
	}
	else if($password != $confirm_pass)
	{
		$errors[] = lang("ACCOUNT_PASS_MISMATCH");
	}
	if(!isValidEmail($email))
	{
		$errors[] = lang("ACCOUNT_INVALID_EMAIL");
	}
	//End data validation
	if(count($errors) == 0)
	{	
		//Construct a user object
		$user = new User($username, $displayname, 'Master Account', $password, $email);
		
		//Checking this flag tells us whether there were any errors such as possible data duplication occured
		if(!$user->status)
		{
			if($user->username_taken) $errors[] = lang("ACCOUNT_USERNAME_IN_USE",array($username));
			if($user->displayname_taken) $errors[] = lang("ACCOUNT_DISPLAYNAME_IN_USE",array($displayname));
			if($user->email_taken) 	  $errors[] = lang("ACCOUNT_EMAIL_IN_USE",array($email));		
		}
		else
		{
			//Attempt to add the user to the database, carry out finishing  tasks like emailing the user (if required)
			//Attempt to add the user to the database, carry out finishing  tasks like emailing the user (if required)
			$new_user_id = $user->userCakeAddUser();
			if($new_user_id == -1)
			{
				if($user->mail_failure) $errors[] = lang("MAIL_ERROR");
				if($user->sql_failure)  $errors[] = lang("SQL_ERROR");
			}
		}
	}
	
	// If everything went well, add default permissions for the new user
	if(count($errors) == 0) {
		// Get default permissions
		$permissions = fetchAllPermissions();
		$add = array();
		foreach ($permissions as $permission){
			if ($permission['is_default'] == 1) {
				$permission_id = $permission['id'];
				$add[$permission_id] = $permission_id;
			}
		}
		if ($addition_count = addPermission($add, $new_user_id)){
			// Uncomment this if you want self-registered users to know about permission groups
			//$successes[] = lang("ACCOUNT_PERMISSION_ADDED", array ($addition_count));
		}
		else {
			$errors[] = lang("SQL_ERROR");
		}
	}

	if(count($errors) == 0) {
		// On success, create the success message and delete the activation token
		deleteConfigParameter('root_account_config_token');
		$successes[] = "You have successfully created the root account.  Please delete this installation folder and log in via login.php.";
	}	
}

foreach ($errors as $error){
  addAlert("danger", $error);
}
foreach ($successes as $success){
  addAlert("success", $success);
}

// Send successfully registered users to the completion page, while errors should return them to the registration page.
if (isset($_POST['ajaxMode']) and $_POST['ajaxMode'] == "true" ){
  echo json_encode(array(
	"errors" => count($errors),
	"successes" => count($successes)));
} else {
  if(count($errors) == 0) {
	header('Location: complete.php');
	exit();
  } else {
	header('Location: register_root.php');
	exit();	
  }
}

?>
