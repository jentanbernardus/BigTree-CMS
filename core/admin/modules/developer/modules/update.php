<h3>Edit Module</h3>
<?
	bigtree_process_post_vars();

	$id = end($path);

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
	
	$gbp = mysql_real_escape_string(json_encode($_POST["gbp"]));
	
	$name = mysql_real_escape_string(htmlspecialchars($name));
	sqlquery("UPDATE bigtree_modules SET name = '$name', `group` = '$group', class = '$class', `gbp` = '$gbp' WHERE id = '$id'");
	
	// Remove cached class list.
	unlink($GLOBALS["server_root"]."cache/module-class-list.btc");

	$admin->growl("Developer","Updated Module");
	header("Location: ".$saroot."modules/view/");
	die();	
?>