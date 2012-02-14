<?
	/*
	|Name: Set Value for Setting|
	|Description: Updates an existing BigTree setting's value.|
	|Readonly: NO|
	|Level: 1|
	|Parameters: 
		id: Setting ID,
		value: Value|
	|Returns:
		setting: Setting Object|
	*/
	
	$admin->requireAPIWrite();
	$admin->requireAPILevel(1);
	
	$setting = $admin->getSetting($_POST["id"]);
	if ($setting["locked"] && $admin->Level < 2) {
		echo bigtree_api_encode(array("success" => false, "error" => "You do not have permission to modify that setting."));	
	} else {
		$admin->updateSettingValue($_POST["id"],$_POST["value"]);
		echo bigtree_api_encode(array("success" => true));
	}
	
	$success = $admin->updateSetting($_POST["setting"],$_POST);
	if ($success) {
		echo bigtree_api_encode(array("success" => true,"setting" => $admin->getSetting($_POST["id"])));
	} else {
		echo bigtree_api_encode(array("success" => true,"error" => "A setting already exists with that id."));
	}
?>