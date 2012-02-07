<?
	$id = end($path);
	$f = $admin->getModuleAction($id);
	if ($f["form"]) {
		// Only delete the auto-ness if it's the only one using it.
		if (sqlrows(sqlquery("SELECT * FROM bigtree_module_actions WHERE form = '".$f["form"]."'")) == 1) {
			sqlquery("DELETE FROM bigtree_module_forms WHERE id = '".$f["form"]."'");
		}
	}
	if ($f["view"]) {
		// Only delete the auto-ness if it's the only one using it.
		if (sqlrows(sqlquery("SELECT * FROM bigtree_module_actions WHERE view = '".$f["view"]."'")) == 1) {
			sqlquery("DELETE FROM bigtree_module_views WHERE id = '".$f["view"]."'");
		}
	}
	sqlquery("DELETE FROM bigtree_module_actions WHERE id = '$id'");
	
	$admin->growl("Developer","Deleted Action");
	header("Location: ".$saroot."modules/edit/".$f["module"]."/");
	die();
?>