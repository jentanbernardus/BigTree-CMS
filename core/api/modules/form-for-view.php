<?
	/*
	|Name: Get Edit Form for View|
	|Description: Retrieves the associated edit form for the specified view and optionally retrieves information on the provided item ID.|
	|Readonly: YES|
	|Level: 0|
	|Parameters: 
		view: View ID,
		item: Item ID|
	|Returns:
		form: Form Array,
		item: Item Array|
	*/
	
	$edit = "edit";
	$view = $autoModule->getView($_POST["view"]);
	$module = $autoModule->getModuleForView($view);
	if ($view["suffix"])
		$edit .= "-".$view["suffix"];
	
	$e = sqlfetch(sqlquery("SELECT * FROM bigtree_module_actions WHERE module = '$module' AND route = '$edit'"));
	if ($e["form"]) {
		$form = $autoModule->getForm($e["form"]);
		
		if ($_POST["item"]) {
			$item = BigTreeModule::get($_POST["item"],$form["table"]);
		} else {
			$item = false;
		}
		
		echo bigtree_api_encode(array("success" => true,"form" => $form,"item" => $item));
	} else {
		echo bigtree_api_encode(array("success" => false,"error" => "Could not find edit form for given view."));
	}
?>