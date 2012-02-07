<?
	/*
	|Name: Delete User|
	|Description: Deletes an existing BigTree user.|
	|Readonly: NO|
	|Level: 1|
	|Parameters: 
		token: API Token,
		id: User's Database ID|
	|Returns:|
	*/
	
	$admin->requireAPIWrite();
	$admin->requireAPILevel(1);
	
	$success = $admin->deleteUser($_POST["id"]);
	
	if ($success)
		echo bigtree_api_encode(array("success" => true));
	else
		echo bigtree_api_encode(array("success" => false,"error" => "You may not delete a user with a higher permission level."));
?>