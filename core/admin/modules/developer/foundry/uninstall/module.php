<?
	$id = mysql_real_escape_string(end($commands));
	$module = sqlfetch(sqlquery("SELECT * FROM bigtree_module_packages WHERE id = '$id'"));
	
	$details = unserialize($module["details"]);
	
	foreach ($details["tables"] as $table) {
		list($table,$type) = explode("#",$table);
		sqlquery("DROP TABLE `$table`");
	}
	
	foreach ($details["class_files"] as $file) {
		unlink($server_root.$file);
	}
	
	foreach ($details["files"] as $file) {
		unlink($server_root.$file);
	}
	
	foreach ($details["required_files"] as $file) {
		unlink($server_root.$file);
	}

	foreach ($details["templates"] as $template) {
		if (substr($template,0,7) == "module-") {
			deleteFileDirectory($server_root."templates/modules/".substr($template,7)."/");
		} else {
			unlink($server_root."templates/".$template.".php");
		}
		sqlquery("DELETE FROM bigtree_templates WHERE id = '".mysql_real_escape_string($template)."'");
	}

	foreach ($details["sidelets"] as $sidelet) {
		sqlquery("DELETE FROM bigtree_sidelets WHERE id = '".mysql_real_escape_string($sidelet)."'");
	}
	
	foreach ($details["feeds"] as $feed) {
		sqlquery("DELETE FROM bigtree_feeds WHERE id = '".mysql_real_escape_string($feed)."'");
	}
	
	foreach ($details["settings"] as $setting) {
		sqlquery("DELETE FROM bigtree_settings WHERE id = '".mysql_real_escape_string($setting)."'");
	}
	
	if ($module["group_id"]) {
		$g = mysql_real_escape_string($module["group_id"]);
		$q = sqlquery("SELECT * FROM bigtree_modules WHERE `group` = '$g'");
		while ($f = sqlfetch($q)) {
			removeModuleFromDB($f["id"]);
		}
		sqlquery("DELETE FROM bigtree_module_groups WHERE id = '$g'");
	}
	
	if ($module["module_id"]) {
		removeModuleFromDB($module["module_id"]);
	}
	
	function removeModuleFromDB($id) {
		$id = mysql_real_escape_string($id);
		// Go through all actions, remove forms and views
		$q = sqlquery("SELECT * FROM bigtree_module_actions WHERE module = '$id'");
		while ($f = sqlfetch($q)) {
			sqlquery("DELETE FROM bigtree_module_forms WHERE id = '".$f["form"]."'");
			sqlquery("DELETE FROM bigtree_module_views WHERE id = '".$f["view"]."'");
		}
		sqlquery("DELETE FROM bigtree_module_actions WHERE module = '$id'");
		sqlquery("DELETE FROM bigtree_modules WHERE id = '$id'");
	}
	
	sqlquery("DELETE FROM bigtree_module_packages WHERE id = '$id'");
	
	$admin->growl("Developer","Uninstalled Module");
	header("Location: ../../../view/");
	die();
	
	function deleteFileDirectory($directory) {
		global $x,$index,$server_root,$tname,$dir;
		$o = opendir($directory);
		while ($r = readdir($o)) {
			if ($r != "." && $r != "..") {
				if (is_dir($directory.$r)) {
					deleteFileDirectory($directory.$r."/");
				} else {
					$x++;
					unlink($directory.$r);
				}
			}
		}
		rmdir($directory);
	}
?>