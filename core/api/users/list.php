<?
	/*
	|Name: Get List of Users|
	|Description: Gets an array of all existing BigTree users.|
	|Readonly: NO|
	|Level: 1|
	|Parameters:|
	|Returns:
		users: Array of User Objects|
	*/
	$admin->requireAPIWrite();
	$admin->requireAPILevel(1);
	$u = $admin->getUsers();
	echo BigTree::apiEncode(array("success" => true,"users" => $u));
?>