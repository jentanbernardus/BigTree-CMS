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
	
	$pages = array();
	$q = sqlquery("SELECT * FROM bigtree_pages ORDER BY id ASC");
	while ($f = sqlfetch($q))
		$pages[] = $f;
	
	echo bigtree_api_encode(array("success" => true,"pages" => $pages));
?>