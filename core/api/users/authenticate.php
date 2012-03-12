<?
	/*
	|Name: Authenticate|
	|Description: Returns a temporary API Token for the authenticated user.|
	|Readonly: NO|
	|Level: 0|
	|Parameters: 
		email: Email Address,
		password: Password|
	|Returns:
		token: Temporary API Token|
	*/
	
	$token = $admin->getAPIToken($_POST["email"],$_POST["password"]);
	if ($token)
		echo BigTree::apiEncode(array("success" => true,"token" => $token));
	else
		echo BigTree::apiEncode(array("success" => false));
?>