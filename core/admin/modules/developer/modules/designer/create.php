<?
	bigtree_process_post_vars();
	
	$errors = array();
	
	// Check if the table exists
	$table_exists = false;
	$q = sqlquery("SHOW TABLES");
	while ($f = sqlfetch($q)) {
		$tname = $f["Tables_in_".$GLOBALS["config"]["db"]["name"]];
		if ($tname == $table)
			$table_exists = true;
	}
	if ($table_exists)
		$errors["table"] = "The table you chose already exists.";
	
	// Check if the class name exists
	if (class_exists($class))
		$errors["class"] = "The class name you chose already exists.";
	
	if (count($errors)) {
		$_SESSION["developer"]["designer_errors"] = $errors;
		$_SESSION["developer"]["saved_module"] = $_POST;
		header("Location: ../");
		die();
	}
		
	if ($group_new) {
		$f = $admin->getModuleGroupByName($group_new);
		if ($f) {
			$group = $f["id"];
		} else {
			sqlquery("INSERT INTO bigtree_module_groups (`name`) VALUES ('".mysql_real_escape_string($group_new)."')");
			$group = sqlid();
		}
	} else {
		$group = $group_existing;
	}
	
	$route = $cms->urlify($name);
	$route = $admin->getAvailableModuleRoute($route);
	
	$name = mysql_real_escape_string(htmlspecialchars($name));
	
	sqlquery("INSERT INTO bigtree_modules (`name`,`route`,`class`,`group`) VALUES ('$name','$route','".mysql_real_escape_string($class)."','$group')");
	$id = sqlid();	
	
	if (!file_exists($GLOBALS["server_root"]."custom/inc/modules/$route.php")) {
		// Create class module.
		$f = fopen($GLOBALS["server_root"]."custom/inc/modules/$route.php","w");
		fwrite($f,"<?\n");
		fwrite($f,"	class $class extends BigTreeModule {\n");
		fwrite($f,"\n");
		fwrite($f,'		var $Table = "'.$table.'";'."\n");
		fwrite($f,'		var $Module = "'.$id.'";'."\n");
		fwrite($f,"	}\n");
		fwrite($f,"?>\n");
		fclose($f);
		chmod($GLOBALS["server_root"]."custom/inc/modules/$route.php",0777);
	}
	
	// Create the table.
	sqlquery("CREATE TABLE `$table` (`id` int(11) NOT NULL auto_increment, PRIMARY KEY  (`id`)) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin");
	
	header("Location: ../form/$id/$table/");
?>