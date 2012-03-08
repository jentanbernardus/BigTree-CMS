<?
	$id = end($path);
	
	// Get info and delete the class.
	$f = $admin->getModule($id);
	unlink($server_root."custom/inc/modules/".$f["route"].".php");
	
	// Delete all the related auto module actions
	sqlquery("DELETE FROM bigtree_modules WHERE id = '$id'");
	$actions = $admin->getModuleActions($id);
	foreach ($actions as $action) {
		if ($action["form"]) {
			sqlquery("DELETE FROM bigtree_module_forms WHERE id = '".$action["form"]."'");
		}
		if ($action["view"]) {
			sqlquery("DELETE FROM bigtree_module_views WHERE id = '".$action["view"]."'");
		}
	}
	
	// Delete actions
	sqlquery("DELETE FROM bigtree_module_actions WHERE module = '$id'");
	
	$admin->growl("Developer","Deleted Module");
	header("Location: ".$developer_root."modules/view/");
	die();
?>