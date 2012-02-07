<?
	$id = mysql_real_escape_string(end($commands));
	$type = $admin->getFieldType($id);
	
	$files = json_decode($type["files"],true);
	foreach ($files as $file) {
		unlink($server_root.$file);
	}
	
	sqlquery("DELETE FROM bigtree_field_types WHERE id = '$id'");
	$admin->growl("Developer","Uninstalled Field Type");
	header("Location: ../../../view/");
	die();
?>