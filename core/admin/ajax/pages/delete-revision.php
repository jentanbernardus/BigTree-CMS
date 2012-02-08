<?
	// Get the version, check if the user has access to the page the version refers to.
	$version = $admin->getPageVersion($_GET["id"]);
	$access = $admin->getPageAccessLevelByUserId($version["page"],$admin->ID);
	if ($access != "p") {
		$admin->stop("You must be a publisher to manage revisions.");
	}
	
	// Delete the revision
	sqlquery("DELETE FROM bigtree_page_versions WHERE id = '".$version["id"]."'");
?>