<?
	// Grab View Data
	$view = BigTreeAutoModule::getView($_POST["view"]);
	$module = $admin->getModule(BigTreeAutoModule::getModuleForView($_POST));
	$perm = $admin->getAccessLevel($module);
	$table = $view["table"];
	
	if ($perm == "p") {
		parse_str($_POST["sort"]);
	
		foreach ($row as $position => $id) {
			if (is_numeric($id)) {
				sqlquery("UPDATE `$table` SET position = '".(count($row)-$position)."' WHERE id = '".mysql_real_escape_string($id)."'");
				BigTreeAutoModule::recacheItem($id,$table);
			} else {
				BigTreeAutoModule::updatePendingItemField(substr($id,1),"position",(count($row)-$position));
				BigTreeAutoModule::recacheItem(substr($id,1),$table,true);
			}
		}
	}
?>