<?
	/*
	|Name: Get List of Settings|
	|Description: Gets an array of all BigTree settings.|
	|Readonly: YES|
	|Level: 1|
	|Parameters:|
	|Returns:
		settings: Array of Setting Objects|
	*/
	$admin->requireAPILevel(1);
	$s = $admin->getSettings();
	echo bigtree_api_encode(array("success" => true,"settings" => $s));
?>