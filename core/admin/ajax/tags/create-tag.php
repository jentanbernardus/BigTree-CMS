<?
	BigTree::globalizePOSTVars();
	
	$tag = strtolower(html_entity_decode($tag));
	// Check if the tag exists already.
	$f = sqlfetch(sqlquery("SELECT * FROM bigtree_tags WHERE tag = '".mysql_real_escape_string($tag)."'"));
	
	if (!$f) {
		$meta = metaphone($tag);
		$route = $cms->urlify($tag);
		$oroute = $route;
		$x = 2;
		while ($f = sqlfetch(sqlquery("SELECT * FROM bigtree_tags WHERE route = '$route'"))) {
			$route = $oroute."-".$x;
			$x++;
		}
		sqlquery("INSERT INTO bigtree_tags (`tag`,`metaphone`,`route`) VALUES ('".mysql_real_escape_string($tag)."','$meta','$route')");
		$id = sqlid();
	} else {
		$id = $f["id"];
	}
	
	echo $id;
?>