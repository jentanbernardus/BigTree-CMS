<?
	$id = end($path);
	sqlquery("DELETE FROM bigtree_module_groups WHERE id = '$id'");
	sqlquery("UPDATE bigtree_modules SET `group` = '0' WHERE `group` = '$id'");
	
	$admin->growl("Developer","Deleted Module Group");
	header("Location: ".$saroot."modules/groups/view/");
	die();
?>