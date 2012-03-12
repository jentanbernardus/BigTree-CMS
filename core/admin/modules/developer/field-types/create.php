<?
	BigTree::globalizePOSTVars(array("mysql_real_escape_string"));
	
	if (file_exists("../core/admin/form-field-types/draw/$id.php") || file_exists("../core/admin/form-field-types/process/$id.php")) {
		$_SESSION["bigtree"]["admin_error"] = "The ID you have chosen is reserved for a core field type.";
		$_SESSION["bigtree"]["admin_saved"] = $_POST;
		header("Location: ../add/");
		die();
	}
	
	if ($admin->getFieldType($id)) {
		$_SESSION["bigtree"]["admin_error"] = "The ID you have chosen is already used by a custom field type.";
		$_SESSION["bigtree"]["admin_saved"] = $_POST;
		header("Location: ../add/");
		die();
	}
	
	$author = mysql_real_escape_string($admin->Name);
	$file = "$id.php";
	
	sqlquery("INSERT INTO bigtree_field_types (`id`,`author`,`name`,`primary_version`,`pages`,`modules`,`callouts`,`last_updated`) VALUES ('$id','$author','$name','1','$pages','$modules','$callouts',NOW())");
	
	if (!file_exists($server_root."custom/admin/form-field-types/draw/$file")) {
		BigTree::touchFile($server_root."custom/admin/form-field-types/draw/$file");
		file_put_contents($server_root."custom/admin/form-field-types/draw/$file",'<? include bigtree_path("admin/form-field-types/draw/text.php"); ?>');
		chmod($server_root."custom/admin/form-field-types/draw/$file",0777);
	}
	if (!file_exists($server_root."custom/admin/form-field-types/process/$file")) {
		BigTree::touchFile($server_root."custom/admin/form-field-types/process/$file");
		file_put_contents($server_root."custom/admin/form-field-types/process/$file",'<? $value = $data[$key]; ?>');
		chmod($server_root."custom/admin/form-field-types/process/$file",0777);
	}
		
	unlink($server_root."cache/form-field-types.btc");
	
	$admin->growl("Developer","Created Custom Field Type");
	header("Location: ../new/$id/");
?>