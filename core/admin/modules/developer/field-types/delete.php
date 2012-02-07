<?
	$type = $admin->getFieldType(end($commands));
	
	unlink($server_root."custom/admin/form-field-types/draw/".$type["file"]);
	unlink($server_root."custom/admin/form-field-types/process/".$type["file"]);
	sqlquery("DELETE FROM bigtree_field_types WHERE id = '".mysql_real_escape_string(end($commands))."'");
	
	$admin->growl("Developer","Deleted Field Type");
	header("Location: ../../view/");
	die();
?>