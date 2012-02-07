<?
	/*
	|Name: Get Module List|
	|Description: Retrieves an alphabetic list of available modules (that a user has access to).|
	|Readonly: YES|
	|Level: 0|
	|Parameters: |
	|Returns:
		modules: Array of Modules|
	*/
	
	$modules = $admin->getModules("name asc");
	foreach ($modules as &$module) {
		$module["actions"] = $admin->getAutoModuleActions($module["id"]);
	}

	echo bigtree_api_encode(array("success" => true,"modules" => $modules));
?>