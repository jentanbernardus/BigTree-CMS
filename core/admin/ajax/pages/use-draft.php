<?
	// Get the version, check if the user has access to the page the version refers to.
	$version = $admin->getPageVersion($_GET["id"]);
	$access = $admin->getPageAccessLevelByUser($version["page"],$admin->ID);
	if ($access != "p") {
		$admin->stop("You must be a publisher to manage revisions.");
	}
	
	foreach ($version as $key => $val) {
		$$key = $val;
	}
	
	// See if we have an existing draft, if so load its changes.  Otherwise start a new list.
	$existing = sqlfetch(sqlquery("SELECT * FROM bigtree_pending_changes WHERE `table` = 'bigtree_pages' AND item_id = '".$version["page"]."'"));
	if ($existing) {
		$changes = json_decode($existing["changes"],true);
	} else {
		$changes = array();
	}

	$changes["title"] = $title;
	$changes["meta_keywords"] = $meta_keywords;
	$changes["meta_description"] = $meta_description;
	$changes["template"] = $template;
	$changes["external"] = $external;
	$changes["new_window"] = $new_window;
	// These two already are json encoded.  We don't want it encoded twice so we decode it here first.
	$changes["resources"] = json_decode($resources,true);
	$changes["callouts"] = json_decode($callouts,true);
	
	$changes = mysql_real_escape_string(json_encode($changes));
	
	if ($existing) {
		// Update an existing draft with our changes and new author
		sqlquery("UPDATE bigtree_pending_changes SET changes = '$changes', user = '".$admin->ID."', date = NOW() WHERE id = '".$existing["id"]."'");
	} else {
		// If we don't have an existing copy, make a new draft.
		sqlquery("INSERT INTO bigtree_pending_changes (`user`,`date`,`title`,`table`,`changes`,`item_id`) VALUES ('".$admin->ID."',NOW(),'Page Change Pending','bigtree_pages','$changes','".$version["page"]."')");		
	}
	
	$admin->growl("Pages","Loaded Saved Revision");
	header("Location: ".$aroot."pages/edit/".$version["page"]."/");
	die();
?>