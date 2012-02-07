<?
	header("Content-type: text/javascript");

	$id = $_GET["id"];
	
	// Grab View Data
	$view = BigTreeAutoModule::getView($_GET["view"]);
	$table = $view["table"];
	$module = $admin->getModuleById(BigTreeAutoModule::getModuleForView($_GET["view"]));
	$item = BigTreeModule::get($id,$table);
	$perm = $admin->getAccessLevel($module,$item,$table);
	
	if ($item["archived"]) {
		if ($perm != "p") {
			echo 'BigTree.growl("'.$module["name"].'","You don\'t have permission to perform this action.");';
		} else {
			echo 'BigTree.growl("'.$module["name"].'","Item is now unarchived.");';
			BigTreeModule::unarchive($id,$table);
		}
	} else {
		if ($perm != "p") {
			echo 'BigTree.growl("'.$mod_data["name"].'","You don\'t have permission to perform this action.");';
		} else {
			echo 'BigTree.growl("'.$mod_data["name"].'","Item is now archived.");';
			BigTreeModule::archive($id,$table);
		}
	}
	
	BigTreeAutoModule::recacheItem($id,$table);
?>