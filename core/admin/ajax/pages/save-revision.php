<?
	print_r($_POST);
	// Get the version, check if the user has access to the page the version refers to.
	$version = $admin->getPageVersion($_POST["id"]);
	$access = $admin->getPageAccessLevelByUserId($version["page"],$admin->ID);
	if ($access != "p") {
		$admin->stop("You must be a publisher to manage revisions.");
	}
	
	// Save the version's description and saved status
	$description = mysql_real_escape_string(htmlspecialchars($_POST["description"]));
	sqlquery("UPDATE bigtree_page_versions SET saved = 'on', saved_description = '$description' WHERE id = '".$version["id"]."'");
?>