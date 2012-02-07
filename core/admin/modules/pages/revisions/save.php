<?
	$version = $admin->getPageVersion(end($path));
	$access = $admin->getPageAccessLevelByUserId($version["page"],$admin->ID);
	if ($access != "p") {
		$admin->stop("You must be a publisher to manage revisions.");
	}
	
	$description = mysql_real_escape_string(htmlspecialchars($_POST["description"]));
	
	sqlquery("UPDATE bigtree_page_versions SET saved = 'on', saved_description = '$description' WHERE id = '".$version["id"]."'");
	
	print_r($sqlerrors);
?>