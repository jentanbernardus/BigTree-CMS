<?
	/*
	|Name: Get Pages List|
	|Description: Returns all pages from the database (unmodified).|
	|Readonly: NO|
	|Level: 0|
	|Parameters: |
	|Returns:
		pages: Array of Entries from the bigtree_pages table.|
	*/
	
	$pages = $admin->getPages();
	
	echo BigTree::apiEncode(array("success" => true,"pages" => $pages));
?>