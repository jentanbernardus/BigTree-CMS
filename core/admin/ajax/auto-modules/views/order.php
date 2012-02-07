<?
	// Grab View Data
	$view = BigTreeAutoModule::getView($_GET["view"]);
	$module = $admin->getModuleById(BigTreeAutoModule::getModuleForView($_GET["view"]));
	$perm = $admin->getAccessLevel($module);
	$table = $view["table"];
	
	if ($perm == "p") {
		parse_str($_GET["sort"]);
	
		foreach ($row as $position => $id) {
			BigTreeModule::setPosition($id,count($sort_table)-$position,$table);
			BigTreeAutoModule::recacheItem($id,$table);
		}
	}
?>