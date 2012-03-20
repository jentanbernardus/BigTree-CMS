<?
	/*
	|Name: Create a Module Item|
	|Description: Creates an entry in the database for a given module form.|
	|Readonly: NO|
	|Level: 0|
	|Parameters: 
		form: Form ID,
		item: Item Object|
	|Returns:
		id: Page ID or Change ID,
		status: "APPROVED" for immediate change or "PENDING"|
	*/
	
	$admin->requireAPIWrite();
	
	$form = $autoModule->getForm($_POST["form"]);
	$module = $autoModule->getModuleForForm($form);
	$parser = new BigTreeForms($form["table"]);
	$permission = $admin->getAccessLevel($module,$_POST["item"],$form["table"]);
	
	$data = $parser->sanitizeFormDataForDB($_POST["item"]);
	
	if (!$permission || $permission == "n") {
		echo BigTree::apiEncode(array("success" => false,"error" => "Permission denied."));
		die();
	}
	
	if ($permission == "e") {
		$id = $autoModule->createPendingItem($module,$form["table"],$data);
		$status = "PENDING";
	}
	
	if ($permission == "p") {
		$id = $autoModule->createItem($form["table"],$data);
		$status = "APPROVED";
	}
	
	echo BigTree::apiEncode(array("success" => true,"id" => $id,"status" => $status));
?>