<?
	$tags = array();
	$meta = metaphone($_POST["tag"]);
	$close_tags = array();
	$dist = array();
	$q = sqlquery("SELECT * FROM bigtree_tags");
	while ($f = sqlfetch($q)) {
		$distance = levenshtein($f["metaphone"],$meta);
		if ($distance < 2) {
			$tags[] = $f["tag"];
			$dist[] = $distance;
		}
	}
	
	array_multisort($dist,SORT_ASC,$tags);
	if (count($tags) > 8)
		$tags = array_slice($tags,0,8);
	foreach ($tags as $tag) {
?>
<li><a href="#"><? if ($tag == $_POST["tag"]) { ?><span><?=htmlspecialchars($tag)?></span><? } else { ?><?=htmlspecialchars($tag)?><? } ?></a></li>
<?
	}
?>